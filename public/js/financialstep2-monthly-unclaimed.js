/**
 * Financial Assistance Step 2 - Monthly Unclaimed Bulk Messaging Module
 */
(function () {
    'use strict';

    const DEFAULT_BULK_MESSAGE = "Magandang araw, [Beneficiary Name]. Ito po ay mula sa Municipal Social Welfare and Development Office (MSWDO). Ang inyong tulong pinansyal ay maaari nang kunin sa [Claiming Date]. Mangyaring magtungo sa aming tanggapan sa naturang petsa at dalhin ang inyong valid ID at mga kinakailangang dokumento. Maraming salamat po.";

    let modalEl = null;
    let bsModal = null;
    let monthSelectEl = null;
    let searchInputEl = null;
    let tableBodyEl = null;
    let masterCheckboxEl = null;
    let sendSelectedBtnEl = null;
    let sendAllBtnEl = null;
    let sendAllSpinnerEl = null;
    let sendAllIconEl = null;
    let sendAllTextEl = null;

    // Bulk Message Customization & Claiming Date Elements
    let claimingDateInputEl = null;
    let messageBodyInputEl = null;
    let charCountEl = null;
    let btnInsertNameTagEl = null;
    let btnInsertDateTagEl = null;
    let btnResetTemplateEl = null;

    // State
    const state = {
        currentMonth: '',
        monthLabel: '',
        availableMonths: [],
        records: [],
        filteredRecords: [],
        selectedIds: new Set(),
        isLoading: false,
    };

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Format a raw YYYY-MM-DD date string into human-readable Month Day, Year.
     */
    function formatDateToHuman(dateStr) {
        if (!dateStr) return '';
        try {
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10) - 1;
                const day = parseInt(parts[2], 10);
                const d = new Date(year, month, day);
                return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }
            return new Date(dateStr).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        } catch (e) {
            return dateStr;
        }
    }

    /**
     * Update character and SMS segment counter.
     */
    function updateCharCounter() {
        if (!messageBodyInputEl || !charCountEl) return;
        const text = messageBodyInputEl.value || '';
        const len = text.length;
        const segments = Math.ceil(len / 160) || 1;
        charCountEl.textContent = `${len} characters (${segments} SMS)`;
    }

    const updateLivePreview = updateCharCounter;

    /**
     * Insert tag/placeholder into the message textarea at current cursor location.
     */
    function insertTagAtCursor(tag) {
        if (!messageBodyInputEl) return;
        const start = messageBodyInputEl.selectionStart || 0;
        const end = messageBodyInputEl.selectionEnd || 0;
        const text = messageBodyInputEl.value;
        messageBodyInputEl.value = text.substring(0, start) + tag + text.substring(end);
        messageBodyInputEl.selectionStart = messageBodyInputEl.selectionEnd = start + tag.length;
        messageBodyInputEl.focus();
        updateLivePreview();
    }

    /**
     * Set quick claiming date shortcut.
     */
    function setQuickDate(daysToAdd) {
        const d = new Date();
        d.setDate(d.getDate() + parseInt(daysToAdd, 10));
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const dateStr = `${year}-${month}-${day}`;
        if (claimingDateInputEl) {
            claimingDateInputEl.value = dateStr;
            updateLivePreview();
        }
    }

    /**
     * Fetch unclaimed records for the selected month from the server.
     */
    function fetchUnclaimedRecords(month) {
        state.isLoading = true;
        tableBodyEl.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading unclaimed financial assistance records...
                </td>
            </tr>`;

        const url = `/admin/financial/financialstep2/messages/unclaimed-by-month${month ? `?month=${month}` : ''}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(res => res.json())
            .then(data => {
                state.isLoading = false;
                if (data.success) {
                    state.currentMonth = data.month;
                    state.monthLabel = data.month_label;
                    state.availableMonths = data.available_months || [];
                    state.records = data.records || [];
                    state.filteredRecords = [...state.records];
                    state.selectedIds.clear();

                    // Update Month Badge & Header
                    const badgeEl = document.getElementById('monthlyModalMonthBadge');
                    if (badgeEl) badgeEl.textContent = state.monthLabel;

                    // Populate Month Dropdown if needed
                    populateMonthSelect();

                    // Update Metric Counters
                    updateCounters(data.total_unclaimed, data.valid_contact_count, data.missing_contact_count);

                    // Render Table
                    renderTable();

                    // Update live preview with first record name
                    updateLivePreview();
                } else {
                    tableBodyEl.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load records: ${data.message || 'Unknown error'}</td></tr>`;
                }
            })
            .catch(err => {
                state.isLoading = false;
                console.error('Error fetching monthly unclaimed:', err);
                tableBodyEl.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">A network error occurred while loading records.</td></tr>`;
            });
    }

    /**
     * Populate the Month selection dropdown.
     */
    function populateMonthSelect() {
        if (!monthSelectEl) return;

        let html = '';
        if (state.availableMonths.length > 0) {
            state.availableMonths.forEach(m => {
                const selected = m.month_key === state.currentMonth ? 'selected' : '';
                html += `<option value="${m.month_key}" ${selected}>${m.month_label} (${m.unclaimed_count} Unclaimed)</option>`;
            });
        } else {
            html = `<option value="${state.currentMonth}" selected>${state.monthLabel} (0 Unclaimed)</option>`;
        }
        monthSelectEl.innerHTML = html;
    }

    /**
     * Update metric chips at the top of the modal.
     */
    function updateCounters(total, valid, missing) {
        const totalEl = document.getElementById('monthlyTotalUnclaimedCount');
        const validEl = document.getElementById('monthlyValidContactCount');
        const missingEl = document.getElementById('monthlyMissingContactCount');
        const showingEl = document.getElementById('monthlyShowingCount');

        if (totalEl) totalEl.textContent = total;
        if (validEl) validEl.textContent = valid;
        if (missingEl) missingEl.textContent = missing;
        if (showingEl) showingEl.textContent = total;

        updateSelectionUI();
    }

    /**
     * Update Selected checkboxes count and button state.
     */
    function updateSelectionUI() {
        const count = state.selectedIds.size;
        const selectedCountSpan = document.getElementById('monthlySelectedCount');
        const btnCountSpan = document.getElementById('btnSelectedCountSpan');

        if (selectedCountSpan) selectedCountSpan.textContent = count;
        if (btnCountSpan) btnCountSpan.textContent = count;

        if (sendSelectedBtnEl) {
            sendSelectedBtnEl.disabled = count === 0;
        }

        if (masterCheckboxEl) {
            const visibleCount = state.filteredRecords.length;
            masterCheckboxEl.checked = visibleCount > 0 && count === visibleCount;
            masterCheckboxEl.indeterminate = count > 0 && count < visibleCount;
        }

        // Enable / disable send all button
        if (sendAllBtnEl) {
            sendAllBtnEl.disabled = state.records.length === 0;
        }

        // Update live preview to reflect selected recipient sample
        updateLivePreview();
    }

    /**
     * Render the beneficiaries table body without any individual Action / Send SMS buttons.
     */
    function renderTable() {
        if (!tableBodyEl) return;

        if (state.filteredRecords.length === 0) {
            tableBodyEl.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-search fa-2x mb-2 text-muted opacity-50 d-block"></i>
                        No unclaimed financial assistance records found for <strong>${state.monthLabel}</strong>.
                    </td>
                </tr>`;
            updateSelectionUI();
            return;
        }

        let html = '';
        state.filteredRecords.forEach((row, idx) => {
            const isChecked = state.selectedIds.has(row.id) ? 'checked' : '';

            // Contact Number
            let contactHtml = '';
            if (row.has_valid_contact) {
                contactHtml = `<span class="fw-semibold font-monospace">${row.contact_number}</span>`;
            } else {
                contactHtml = `<span class="text-danger fst-italic">${row.contact_number || 'No contact'}</span>`;
            }

            // Notification Status
            let notifHtml = '';
            if (row.has_sent_message) {
                notifHtml = `<span class="text-success small" id="row-notif-${row.id}"><i class="fas fa-check me-1"></i>Sent (${row.last_message_date})</span>`;
            } else {
                notifHtml = `<span class="text-muted small" id="row-notif-${row.id}">Not sent</span>`;
            }

            // Claiming Date
            let claimingHtml = row.claiming_date
                ? `<span>${row.claiming_date}</span>`
                : `<span class="text-muted fst-italic">Not set</span>`;

            html += `
                <tr id="monthly-row-${row.id}" class="${isChecked ? 'table-active' : ''}">
                    <td class="text-center">
                        <input class="form-check-input row-select-checkbox" type="checkbox" data-id="${row.id}" ${isChecked}>
                    </td>
                    <td class="text-center text-muted">${idx + 1}</td>
                    <td>
                        <div class="fw-semibold text-dark">${row.beneficiary_name}</div>
                        ${row.is_separate_rep ? `<div class="text-muted small">Rep: ${row.representative_name}</div>` : ''}
                        <div class="text-muted small">${row.control_number}</div>
                    </td>
                    <td>
                        <div class="text-dark small">${row.barangay}</div>
                    </td>
                    <td>${contactHtml}</td>
                    <td class="text-end">
                        <span class="fw-semibold">${row.formatted_amount}</span>
                    </td>
                    <td>${claimingHtml}</td>
                    <td>${notifHtml}</td>
                </tr>`;
        });

        tableBodyEl.innerHTML = html;
        updateSelectionUI();
    }

    /**
     * Filter table records by search query.
     */
    function filterRecords(query) {
        const q = query.trim().toLowerCase();
        if (!q) {
            state.filteredRecords = [...state.records];
        } else {
            state.filteredRecords = state.records.filter(r => {
                return (r.beneficiary_name && r.beneficiary_name.toLowerCase().includes(q)) ||
                    (r.representative_name && r.representative_name.toLowerCase().includes(q)) ||
                    (r.barangay && r.barangay.toLowerCase().includes(q)) ||
                    (r.control_number && r.control_number.toLowerCase().includes(q)) ||
                    (r.contact_number && r.contact_number.includes(q));
            });
        }
        renderTable();
    }

    /**
     * Perform Bulk Send to either All Unclaimed in month or Selected IDs.
     */
    function performBulkSend(targetMode) {
        const isAll = targetMode === 'all';
        const intakeIds = isAll ? [] : Array.from(state.selectedIds);
        const count = isAll ? state.records.length : intakeIds.length;

        if (count === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Beneficiaries Selected',
                    text: 'Please select at least one beneficiary to send SMS messages.',
                    confirmButtonColor: '#1A237E'
                });
            } else {
                alert('Please select at least one beneficiary.');
            }
            return;
        }

        const claimingDate = claimingDateInputEl ? claimingDateInputEl.value : '';
        if (!claimingDate) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Claiming Date Required',
                    text: 'Please select a Claiming Date that will be included in the message for the beneficiaries.',
                    confirmButtonColor: '#1A237E'
                });
            } else {
                alert('Please select a Claiming Date.');
            }
            if (claimingDateInputEl) claimingDateInputEl.focus();
            return;
        }

        const messageText = messageBodyInputEl ? messageBodyInputEl.value.trim() : '';
        if (!messageText) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Message Body Required',
                    text: 'Please enter or customize the SMS message before sending.',
                    confirmButtonColor: '#1A237E'
                });
            } else {
                alert('Please enter an SMS message body.');
            }
            if (messageBodyInputEl) messageBodyInputEl.focus();
            return;
        }

        // Count how many have valid contact numbers
        const targetRecords = isAll
            ? state.records
            : state.records.filter(r => state.selectedIds.has(r.id));

        const validCount = targetRecords.filter(r => r.has_valid_contact).length;
        const missingCount = targetRecords.filter(r => !r.has_valid_contact).length;
        const humanClaimingDate = formatDateToHuman(claimingDate);

        const titleText = isAll
            ? 'Send Message to All Unclaimed?'
            : `Send Message to ${count} Selected Beneficiaries?`;

        const confirmButtonText = isAll ? 'Send to All' : 'Send to Selected';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `<span style="font-size: 1.15rem; font-weight: 600;">${titleText}</span>`,
                html: `
                    <div class="text-start p-2.5 bg-light rounded border mb-2 small">
                        <div class="mb-1"><strong>Payroll Month:</strong> ${state.monthLabel}</div>
                        <div class="mb-1"><strong>Recipients:</strong> ${count} (${validCount} with valid mobile number)</div>
                        ${missingCount > 0 ? `<div class="mb-1 text-danger">Notice: ${missingCount} record(s) have no contact number and will be skipped.</div>` : ''}
                        <div><strong>Claiming Date:</strong> ${humanClaimingDate}</div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    dispatchBulkSend(intakeIds, claimingDate, messageText);
                }
            });
        } else {
            if (confirm(`${titleText}\n\nMonth: ${state.monthLabel}\nBeneficiaries: ${count}\nClaiming Date: ${humanClaimingDate}\n\nContinue?`)) {
                dispatchBulkSend(intakeIds, claimingDate, messageText);
            }
        }
    }

    /**
     * Dispatch the bulk send POST request to the backend.
     */
    function dispatchBulkSend(intakeIds, claimingDate, messageText) {
        // Show loading state
        sendAllBtnEl.disabled = true;
        sendSelectedBtnEl.disabled = true;
        sendAllSpinnerEl.classList.remove('d-none');
        sendAllIconEl.classList.add('d-none');
        sendAllTextEl.textContent = 'Sending Bulk SMS...';

        const payload = {
            month: state.currentMonth,
            intake_ids: intakeIds.length > 0 ? intakeIds : null,
            claiming_date: claimingDate,
            message: messageText,
        };

        fetch('/admin/financial/financialstep2/messages/send-bulk-unclaimed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                // Reset loading state
                sendAllSpinnerEl.classList.add('d-none');
                sendAllIconEl.classList.remove('d-none');
                sendAllTextEl.textContent = 'Send Message to All Unclaimed';
                sendAllBtnEl.disabled = false;
                sendSelectedBtnEl.disabled = state.selectedIds.size === 0;

                if (status === 200 && body.success) {
                    // Show result summary alert in modal
                    const resultAlert = document.getElementById('bulkSendResultAlert');
                    const titleEl = document.getElementById('bulkResultTitle');
                    const subtitleEl = document.getElementById('bulkResultSubtitle');
                    const sentBadge = document.getElementById('bulkResultSentBadge');
                    const failedBadge = document.getElementById('bulkResultFailedBadge');
                    const noContactBadge = document.getElementById('bulkResultNoContactBadge');

                    if (resultAlert) {
                        resultAlert.classList.remove('d-none');
                        titleEl.textContent = `Bulk messaging completed for ${body.month_label}!`;
                        subtitleEl.textContent = `Processed ${body.total_attempted} beneficiaries.`;
                        sentBadge.textContent = `${body.sent_count} Successfully Sent`;
                        failedBadge.textContent = `${body.failed_count} Failed`;
                        noContactBadge.textContent = `${body.no_contact_count} No Contact Number`;
                    }

                    // Show SweetAlert confirmation summary
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: body.failed_count === 0 ? 'success' : 'info',
                            title: 'Bulk Messaging Results',
                            html: `
                            <div class="text-start p-2.5 bg-light rounded border mb-2 small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Payroll Month:</span>
                                    <strong>${body.month_label}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 text-success">
                                    <span>Successfully Sent:</span>
                                    <strong>${body.sent_count}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 text-danger">
                                    <span>Failed:</span>
                                    <strong>${body.failed_count}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-0 text-secondary">
                                    <span>No Contact Number:</span>
                                    <strong>${body.no_contact_count}</strong>
                                </div>
                            </div>
                        `,
                            confirmButtonColor: '#1A237E',
                            confirmButtonText: 'OK'
                        });
                    }

                    // Re-fetch records to update rows and display updated claiming dates & last-sent statuses
                    fetchUnclaimedRecords(state.currentMonth);

                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Bulk Messaging Failed',
                            text: body.message || 'An error occurred during bulk messaging.',
                            confirmButtonColor: '#1A237E'
                        });
                    } else {
                        alert('Bulk messaging failed: ' + (body.message || 'Unknown error'));
                    }
                }
            })
            .catch(err => {
                console.error('Bulk Send Error:', err);
                sendAllSpinnerEl.classList.add('d-none');
                sendAllIconEl.classList.remove('d-none');
                sendAllTextEl.textContent = 'Send Message to All Unclaimed';
                sendAllBtnEl.disabled = false;
                sendSelectedBtnEl.disabled = state.selectedIds.size === 0;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'A network error occurred while sending messages. Please try again.',
                        confirmButtonColor: '#1A237E'
                    });
                } else {
                    alert('A network error occurred.');
                }
            });
    }

    /**
     * Open the Monthly Unclaimed Modal.
     */
    function openMonthlyUnclaimedModal(initialMonth) {
        if (!modalEl) {
            modalEl = document.getElementById('monthlyUnclaimedModal');
            if (modalEl) bsModal = new bootstrap.Modal(modalEl);
        }

        // Hide result alert if previously shown
        const resultAlert = document.getElementById('bulkSendResultAlert');
        if (resultAlert) resultAlert.classList.add('d-none');

        // Clear search input
        if (searchInputEl) searchInputEl.value = '';

        // Reset message template to default if blank
        if (messageBodyInputEl && !messageBodyInputEl.value.trim()) {
            messageBodyInputEl.value = DEFAULT_BULK_MESSAGE;
        }

        // Default date to today if blank
        if (claimingDateInputEl && !claimingDateInputEl.value) {
            const today = new Date();
            const y = today.getFullYear();
            const m = String(today.getMonth() + 1).padStart(2, '0');
            const d = String(today.getDate()).padStart(2, '0');
            claimingDateInputEl.value = `${y}-${m}-${d}`;
        }

        updateLivePreview();

        // Show modal
        if (bsModal) {
            bsModal.show();
        }

        // Fetch data
        fetchUnclaimedRecords(initialMonth || '');
    }

    // Initialize Event Listeners
    document.addEventListener('DOMContentLoaded', function () {
        modalEl = document.getElementById('monthlyUnclaimedModal');
        if (!modalEl) return;

        bsModal = new bootstrap.Modal(modalEl);
        monthSelectEl = document.getElementById('monthlyUnclaimedSelect');
        searchInputEl = document.getElementById('monthlySearchInput');
        tableBodyEl = document.getElementById('monthlyUnclaimedTableBody');
        masterCheckboxEl = document.getElementById('masterSelectAllCheckbox');
        sendSelectedBtnEl = document.getElementById('btnSendSelectedUnclaimed');
        sendAllBtnEl = document.getElementById('btnSendAllUnclaimed');
        sendAllSpinnerEl = document.getElementById('bulkSendSpinner');
        sendAllIconEl = document.getElementById('bulkSendIcon');
        sendAllTextEl = document.getElementById('btnSendAllText');

        // Bulk SMS customization elements
        claimingDateInputEl = document.getElementById('monthlyBulkClaimingDate');
        messageBodyInputEl = document.getElementById('monthlyBulkMessageBody');
        charCountEl = document.getElementById('monthlyBulkCharCount');
        btnInsertNameTagEl = document.getElementById('btnInsertNameTag');
        btnInsertDateTagEl = document.getElementById('btnInsertDateTag');
        btnResetTemplateEl = document.getElementById('btnResetBulkTemplate');

        // Initialize default message in textarea if empty
        if (messageBodyInputEl && !messageBodyInputEl.value.trim()) {
            messageBodyInputEl.value = DEFAULT_BULK_MESSAGE;
        }

        // Message input listener for live counter & preview
        if (messageBodyInputEl) {
            messageBodyInputEl.addEventListener('input', updateLivePreview);
        }

        // Claiming Date change listener
        if (claimingDateInputEl) {
            claimingDateInputEl.addEventListener('change', updateLivePreview);
        }

        // Variable insertion buttons
        if (btnInsertNameTagEl) {
            btnInsertNameTagEl.addEventListener('click', function (e) {
                e.preventDefault();
                insertTagAtCursor('[Beneficiary Name]');
            });
        }

        if (btnInsertDateTagEl) {
            btnInsertDateTagEl.addEventListener('click', function (e) {
                e.preventDefault();
                insertTagAtCursor('[Claiming Date]');
            });
        }

        // Reset template button
        if (btnResetTemplateEl) {
            btnResetTemplateEl.addEventListener('click', function (e) {
                e.preventDefault();
                if (messageBodyInputEl) {
                    messageBodyInputEl.value = DEFAULT_BULK_MESSAGE;
                    updateLivePreview();
                }
            });
        }

        // Quick date buttons
        document.querySelectorAll('.btn-quick-date').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const days = this.getAttribute('data-days') || 0;
                setQuickDate(days);
            });
        });

        // Initial preview render
        updateLivePreview();

        // Month Selector change
        if (monthSelectEl) {
            monthSelectEl.addEventListener('change', function () {
                fetchUnclaimedRecords(this.value);
            });
        }

        // Refresh button
        const btnRefresh = document.getElementById('btnRefreshMonthlyUnclaimed');
        if (btnRefresh) {
            btnRefresh.addEventListener('click', function () {
                fetchUnclaimedRecords(monthSelectEl ? monthSelectEl.value : state.currentMonth);
            });
        }

        // Search Input filter
        if (searchInputEl) {
            searchInputEl.addEventListener('input', function () {
                filterRecords(this.value);
            });
        }

        // Master "Select All" Checkbox
        if (masterCheckboxEl) {
            masterCheckboxEl.addEventListener('change', function () {
                const checked = this.checked;
                state.filteredRecords.forEach(r => {
                    if (checked) {
                        state.selectedIds.add(r.id);
                    } else {
                        state.selectedIds.delete(r.id);
                    }
                });
                renderTable();
            });
        }

        // Deselect All Button
        const btnDeselectAll = document.getElementById('btnDeselectAll');
        if (btnDeselectAll) {
            btnDeselectAll.addEventListener('click', function (e) {
                e.preventDefault();
                state.selectedIds.clear();
                if (masterCheckboxEl) masterCheckboxEl.checked = false;
                renderTable();
            });
        }

        // Row Checkbox change (Event Delegation)
        tableBodyEl.addEventListener('change', function (e) {
            const cb = e.target.closest('.row-select-checkbox');
            if (!cb) return;

            const id = parseInt(cb.getAttribute('data-id'), 10);
            if (cb.checked) {
                state.selectedIds.add(id);
            } else {
                state.selectedIds.delete(id);
            }
            updateSelectionUI();

            const rowEl = document.getElementById(`monthly-row-${id}`);
            if (rowEl) {
                if (cb.checked) rowEl.classList.add('table-active');
                else rowEl.classList.remove('table-active');
            }
        });

        // Send to Selected Button
        if (sendSelectedBtnEl) {
            sendSelectedBtnEl.addEventListener('click', function (e) {
                e.preventDefault();
                performBulkSend('selected');
            });
        }

        // Send to All Unclaimed Button
        if (sendAllBtnEl) {
            sendAllBtnEl.addEventListener('click', function (e) {
                e.preventDefault();
                performBulkSend('all');
            });
        }

        // Global trigger for .btn-open-monthly-unclaimed
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('.btn-open-monthly-unclaimed');
            if (!trigger) return;
            e.preventDefault();

            const month = trigger.getAttribute('data-month') || '';
            openMonthlyUnclaimedModal(month);
        });
    });

    // Expose globally
    window.openMonthlyUnclaimedModal = openMonthlyUnclaimedModal;

})();
