/**
 * Financial Assistance Step 2 - Monthly Unclaimed Bulk Messaging Module
 */
(function () {
    'use strict';

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
     * Fetch unclaimed records for the selected month from the server.
     */
    function fetchUnclaimedRecords(month) {
        state.isLoading = true;
        tableBodyEl.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5 text-muted">
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
            } else {
                tableBodyEl.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load records: ${data.message || 'Unknown error'}</td></tr>`;
            }
        })
        .catch(err => {
            state.isLoading = false;
            console.error('Error fetching monthly unclaimed:', err);
            tableBodyEl.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">A network error occurred while loading records.</td></tr>`;
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
    }

    /**
     * Render the beneficiaries table body.
     */
    function renderTable() {
        if (!tableBodyEl) return;

        if (state.filteredRecords.length === 0) {
            tableBodyEl.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
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

            // Contact Number badge
            let contactHtml = '';
            if (row.has_valid_contact) {
                contactHtml = `
                    <div class="fw-semibold font-monospace text-dark">${row.contact_number}</div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-2xs px-2 py-0.5">
                        <i class="fas fa-check-circle me-0.5"></i> Valid
                    </span>`;
            } else {
                contactHtml = `
                    <div class="text-danger small fst-italic">${row.contact_number}</div>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill text-2xs px-2 py-0.5">
                        <i class="fas fa-times-circle me-0.5"></i> No Number
                    </span>`;
            }

            // Notification Status badge
            let notifHtml = '';
            if (row.has_sent_message) {
                notifHtml = `
                    <span class="badge bg-light text-primary border rounded-pill px-2 py-0.5 text-2xs" id="row-notif-${row.id}">
                        <i class="fas fa-paper-plane me-0.5"></i> Sent: ${row.last_message_date}
                    </span>`;
            } else {
                notifHtml = `
                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 text-2xs" id="row-notif-${row.id}">
                        <i class="fas fa-clock me-0.5"></i> Not yet notified
                    </span>`;
            }

            // Claiming Date
            let claimingHtml = '';
            if (row.claiming_date) {
                claimingHtml = `<span class="fw-semibold text-dark">${row.claiming_date}</span>`;
            } else {
                claimingHtml = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle text-2xs">Pending Date</span>`;
            }

            html += `
                <tr id="monthly-row-${row.id}" class="${isChecked ? 'table-active' : ''}">
                    <td class="text-center">
                        <input class="form-check-input row-select-checkbox" type="checkbox" data-id="${row.id}" ${isChecked}>
                    </td>
                    <td class="text-center text-muted fw-bold">${idx + 1}</td>
                    <td>
                        <div class="fw-bold text-dark">${row.beneficiary_name}</div>
                        ${row.is_separate_rep ? `<div class="text-muted text-2xs"><span class="badge bg-info-subtle text-info border text-2xs">Rep:</span> ${row.representative_name}</div>` : ''}
                        <div class="text-muted text-2xs font-monospace">${row.control_number}</div>
                    </td>
                    <td>
                        <div class="text-dark small"><i class="fas fa-map-marker-alt text-muted me-1"></i>${row.barangay}</div>
                    </td>
                    <td>${contactHtml}</td>
                    <td class="text-end">
                        <span class="badge bg-light text-dark border fw-bold font-monospace">${row.formatted_amount}</span>
                    </td>
                    <td>${claimingHtml}</td>
                    <td>${notifHtml}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 btn-individual-sms shadow-xs text-xs fw-semibold"
                            data-intake-id="${row.id}"
                            data-beneficiary-name="${row.beneficiary_name.replace(/"/g, '&quot;')}"
                            data-representative-name="${(row.representative_name || '').replace(/"/g, '&quot;')}"
                            data-is-separate-rep="${row.is_separate_rep ? '1' : '0'}"
                            data-contact-number="${row.contact_number}"
                            data-purpose="${(row.purpose || 'Financial Assistance').replace(/"/g, '&quot;')}"
                            data-amount-formatted="${row.formatted_amount}"
                            data-claiming-date="${row.claiming_date || ''}"
                            data-raw-claiming-date="${row.raw_claiming_date || ''}"
                            data-last-message-date="${row.last_message_date || ''}"
                            data-default-template="Unclaimed Assistance"
                            title="Send individual SMS to this beneficiary">
                            <i class="fas fa-paper-plane me-1"></i> Send SMS
                        </button>
                    </td>
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

        // Count how many have valid contact numbers
        const targetRecords = isAll 
            ? state.records 
            : state.records.filter(r => state.selectedIds.has(r.id));
        
        const validCount = targetRecords.filter(r => r.has_valid_contact).length;
        const missingCount = targetRecords.filter(r => !r.has_valid_contact).length;

        // Confirmation Prompt as specified by User:
        // Send Message to All Unclaimed?
        // Month: September 2026
        // Unclaimed Beneficiaries: 24
        // Cancel | Send to All
        const titleText = isAll 
            ? 'Send Message to All Unclaimed?' 
            : `Send Message to ${count} Selected Beneficiaries?`;

        const confirmButtonText = isAll ? 'Send to All' : 'Send to Selected';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `<span style="font-size: 1.25rem; font-weight: 700;">${titleText}</span>`,
                html: `
                    <div class="text-start p-3 bg-light rounded-3 small border mb-3">
                        <div class="mb-1.5"><strong>Month:</strong> <span class="fw-bold text-primary">${state.monthLabel}</span></div>
                        <div class="mb-1.5"><strong>Unclaimed Beneficiaries:</strong> <span class="fw-bold text-dark fs-6">${count}</span></div>
                        <div class="mb-1"><strong>With Valid Phone Numbers:</strong> <span class="text-success fw-semibold">${validCount}</span></div>
                        ${missingCount > 0 ? `<div class="mb-0 text-danger small"><i class="fas fa-exclamation-triangle me-1"></i> Notice: ${missingCount} record(s) have no contact number and will be skipped.</div>` : ''}
                    </div>
                    <div class="alert alert-info py-2 px-2.5 text-start text-2xs mb-0">
                        <i class="fas fa-info-circle me-1"></i> Messages will be sent individually in Tagalog with each beneficiary's own name and claiming date.
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `<i class="fas fa-paper-plane me-1"></i> ${confirmButtonText}`,
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#6B7280',
                reverseButtons: true,
                focusConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    dispatchBulkSend(intakeIds);
                }
            });
        } else {
            if (confirm(`${titleText}\n\nMonth: ${state.monthLabel}\nUnclaimed Beneficiaries: ${count}\n\nContinue?`)) {
                dispatchBulkSend(intakeIds);
            }
        }
    }

    /**
     * Dispatch the bulk send POST request to the backend.
     */
    function dispatchBulkSend(intakeIds) {
        // Show loading state
        sendAllBtnEl.disabled = true;
        sendSelectedBtnEl.disabled = true;
        sendAllSpinnerEl.classList.remove('d-none');
        sendAllIconEl.classList.add('d-none');
        sendAllTextEl.textContent = 'Sending Bulk SMS...';

        const payload = {
            month: state.currentMonth,
            intake_ids: intakeIds.length > 0 ? intakeIds : null,
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
                            <div class="text-start p-3 bg-light rounded-3 small border mb-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><strong>Month:</strong></span>
                                    <span class="text-primary fw-bold">${body.month_label}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 text-success">
                                    <span><i class="fas fa-check-circle me-1"></i> <strong>Successfully Sent:</strong></span>
                                    <span class="fw-bold">${body.sent_count}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 text-danger">
                                    <span><i class="fas fa-times-circle me-1"></i> <strong>Failed:</strong></span>
                                    <span class="fw-bold">${body.failed_count}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-0 text-warning-emphasis">
                                    <span><i class="fas fa-exclamation-circle me-1"></i> <strong>No Contact Number:</strong></span>
                                    <span class="fw-bold">${body.no_contact_count}</span>
                                </div>
                            </div>
                        `,
                        confirmButtonColor: '#1A237E',
                        confirmButtonText: 'Done'
                    });
                }

                // Re-fetch records to update rows and last-sent statuses
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

        // Individual "Send SMS" in row delegates to existing window.openSmsModal
        tableBodyEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-individual-sms');
            if (!btn) return;
            e.preventDefault();

            if (typeof window.openSmsModal === 'function') {
                window.openSmsModal({
                    intakeId: btn.getAttribute('data-intake-id'),
                    beneficiaryName: btn.getAttribute('data-beneficiary-name'),
                    representativeName: btn.getAttribute('data-representative-name'),
                    isSeparateRep: btn.getAttribute('data-is-separate-rep') === '1',
                    contactNumber: btn.getAttribute('data-contact-number'),
                    purpose: btn.getAttribute('data-purpose'),
                    amount: btn.getAttribute('data-amount-formatted'),
                    claimingDate: btn.getAttribute('data-claiming-date'),
                    rawClaimingDate: btn.getAttribute('data-raw-claiming-date'),
                    lastMessageDate: btn.getAttribute('data-last-message-date'),
                    defaultTemplate: 'Unclaimed Assistance',
                });
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
