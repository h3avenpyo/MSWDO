/**
 * Financial Assistance Step 2 - SMS Messaging Module
 */
(function () {
    'use strict';

    // State object for active modal
    const smsState = {
        intakeId: null,
        beneficiaryName: '',
        representativeName: '',
        isSeparateRep: false,
        contactNumber: '',
        purpose: '',
        amount: '',
        claimingDateFormatted: '',
        claimingDateRaw: '',
        lastMessageDate: '',
        activeTemplate: 'Unclaimed Assistance',
        history: [],
    };

    // DOM Elements
    let modalEl = null;
    let bsModal = null;
    let formEl = null;
    let messageBodyEl = null;
    let charCountEl = null;
    let segmentCountEl = null;
    let sendBtnEl = null;
    let sendSpinnerEl = null;
    let sendIconEl = null;
    let sendBtnTextEl = null;

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Format a raw YYYY-MM-DD date into readable format e.g. September 15, 2026.
     */
    function formatReadableDate(dateStr) {
        if (!dateStr || dateStr === '--' || dateStr === 'N/A') return '';
        try {
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                const date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }
            const date = new Date(dateStr);
            if (!isNaN(date.getTime())) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }
        } catch (e) {
            console.error('Date formatting error:', e);
        }
        return dateStr;
    }

    /**
     * Generate template message based on current state.
     */
    function generateTemplate(type) {
        const name = smsState.beneficiaryName || 'Beneficiary';
        const date = smsState.claimingDateFormatted || '[Claiming Date]';

        if (type === 'Unclaimed Assistance') {
            return `Magandang araw, ${name}. Ito po ay mula sa Municipal Social Welfare and Development Office (MSWDO). Ang inyong tulong pinansyal ay maaari nang kunin sa ${date}. Mangyaring magtungo sa aming tanggapan sa naturang petsa at dalhin ang inyong valid ID at mga kinakailangang dokumento. Maraming salamat po.`;
        } else if (type === 'Follow-up') {
            return `Magandang araw, ${name}. Ito po ay paalala mula sa Municipal Social Welfare and Development Office (MSWDO) ukol sa inyong tulong pinansyal na nakatakdang kunin sa ${date}. Mangyaring dalhin ang inyong valid ID at mga kinakailangang dokumento. Maraming salamat po.`;
        }
        return '';
    }

    /**
     * Update character and SMS segments counter.
     */
    function updateCharCounter() {
        if (!messageBodyEl || !charCountEl || !segmentCountEl) return;
        const text = messageBodyEl.value;
        const len = text.length;
        charCountEl.textContent = len;

        // Basic SMS segmentation (160 characters for single GSM SMS, 153 for multi-part)
        let segments = 1;
        if (len > 160) {
            segments = Math.ceil(len / 153);
        } else if (len === 0) {
            segments = 1;
        }
        segmentCountEl.textContent = segments;
    }

    /**
     * Validate current modal form and enable/disable send button accordingly.
     */
    function validateForm() {
        if (!sendBtnEl) return;

        const hasContact = Boolean(smsState.contactNumber && smsState.contactNumber !== 'N/A' && smsState.contactNumber !== 'No contact');
        const hasMessage = Boolean(messageBodyEl && messageBodyEl.value.trim().length >= 3);
        const hasClaimingDate = Boolean(smsState.claimingDateFormatted && smsState.claimingDateFormatted !== '[Claiming Date]');

        const missingClaimingDateAlert = document.getElementById('smsMissingClaimingDateAlert');
        if (missingClaimingDateAlert) {
            if (smsState.activeTemplate === 'Unclaimed Assistance' && !hasClaimingDate) {
                missingClaimingDateAlert.classList.remove('d-none');
            } else {
                missingClaimingDateAlert.classList.add('d-none');
            }
        }

        // For Unclaimed Assistance, claiming date is required
        let isValid = hasContact && hasMessage;
        if (smsState.activeTemplate === 'Unclaimed Assistance' && !hasClaimingDate) {
            isValid = false;
        }

        sendBtnEl.disabled = !isValid;
    }

    /**
     * Load messaging history from backend via AJAX.
     */
    function loadHistory(intakeId) {
        const tbody = document.getElementById('smsHistoryTableBody');
        const countBadge = document.getElementById('smsHistoryCountBadge');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-2"><span class="spinner-border spinner-border-sm me-1"></span> Loading history...</td></tr>';

        fetch(`/admin/financial/financialstep2/messages/history/${intakeId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.messages)) {
                smsState.history = data.messages;
                if (countBadge) countBadge.textContent = data.total || 0;

                if (data.messages.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No messages sent yet.</td></tr>';
                    return;
                }

                let html = '';
                data.messages.forEach(msg => {
                    const statusBadge = msg.status === 'Sent'
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 text-2xs">Sent</span>'
                        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 text-2xs">Failed</span>';

                    html += `<tr>
                        <td class="text-nowrap fw-semibold">${msg.date_short || msg.date}</td>
                        <td class="text-truncate" style="max-width: 250px;" title="${msg.message.replace(/"/g, '&quot;')}">${msg.message}</td>
                        <td><span class="badge bg-light text-dark border text-2xs">${msg.type}</span></td>
                        <td class="text-center">${statusBadge}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            }
        })
        .catch(err => {
            console.error('Failed to load history:', err);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-2">Failed to load message history.</td></tr>';
        });
    }

    /**
     * Populate and open the SMS Messaging Modal.
     */
    function openSmsModal(data) {
        smsState.intakeId = data.intakeId;
        smsState.beneficiaryName = data.beneficiaryName || 'Beneficiary';
        smsState.representativeName = data.representativeName || '';
        smsState.isSeparateRep = Boolean(data.isSeparateRep && data.representativeName && data.representativeName !== data.beneficiaryName);
        smsState.contactNumber = data.contactNumber || '';
        smsState.purpose = data.purpose || 'Financial Assistance';
        smsState.amount = data.amount || '₱0.00';
        smsState.claimingDateFormatted = data.claimingDate || '';
        smsState.claimingDateRaw = data.rawClaimingDate || '';
        smsState.lastMessageDate = data.lastMessageDate || '';
        smsState.activeTemplate = data.defaultTemplate || 'Unclaimed Assistance';

        // Populate hidden form inputs
        document.getElementById('smsIntakeId').value = smsState.intakeId;
        document.getElementById('smsRecipientNumber').value = smsState.contactNumber;
        document.getElementById('smsClaimingDateHidden').value = smsState.claimingDateRaw;

        // Populate Beneficiary Info
        document.getElementById('smsModalBeneficiaryName').textContent = smsState.beneficiaryName;

        const repContainer = document.getElementById('smsModalRepContainer');
        const repNameEl = document.getElementById('smsModalRepName');
        if (smsState.isSeparateRep) {
            repContainer.classList.remove('d-none');
            repNameEl.textContent = smsState.representativeName;
        } else {
            repContainer.classList.add('d-none');
        }

        // Contact Number & Missing Contact Alert
        const contactEl = document.getElementById('smsModalContactNumber');
        const noContactAlert = document.getElementById('smsNoContactAlert');
        const contactBadge = document.getElementById('smsContactStatusBadge');

        const hasValidContact = Boolean(smsState.contactNumber && smsState.contactNumber !== 'N/A' && smsState.contactNumber !== 'No contact');
        if (hasValidContact) {
            contactEl.textContent = smsState.contactNumber;
            contactBadge.className = 'badge bg-success-subtle text-success border border-success-subtle text-2xs rounded-pill';
            contactBadge.innerHTML = '<i class="fas fa-check-circle me-0.5"></i> Verified';
            noContactAlert.classList.add('d-none');
        } else {
            contactEl.textContent = 'No contact number';
            contactBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle text-2xs rounded-pill';
            contactBadge.innerHTML = '<i class="fas fa-times-circle me-0.5"></i> Missing';
            noContactAlert.classList.remove('d-none');
        }

        // Purpose and Amount
        document.getElementById('smsModalPurpose').textContent = smsState.purpose;
        document.getElementById('smsModalAmount').textContent = smsState.amount;

        // Claiming Date
        const claimingDateEl = document.getElementById('smsModalClaimingDate');
        if (smsState.claimingDateFormatted) {
            claimingDateEl.textContent = smsState.claimingDateFormatted;
            claimingDateEl.className = 'fw-bold text-dark';
        } else {
            claimingDateEl.textContent = 'No claiming date assigned';
            claimingDateEl.className = 'fw-bold text-danger';
        }

        // Previous Notification Alert (Duplicate check)
        const duplicateAlert = document.getElementById('smsDuplicateAlert');
        const lastSentText = document.getElementById('smsLastSentText');
        if (smsState.lastMessageDate) {
            duplicateAlert.classList.remove('d-none');
            lastSentText.textContent = `Last message sent: ${smsState.lastMessageDate}`;
        } else {
            duplicateAlert.classList.add('d-none');
        }

        // Set Template Buttons state
        document.querySelectorAll('.btn-template-select').forEach(btn => {
            if (btn.getAttribute('data-type') === smsState.activeTemplate) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Populate Textarea with default template
        const defaultText = generateTemplate(smsState.activeTemplate);
        messageBodyEl.value = defaultText;
        updateCharCounter();

        // Close inline date picker if open
        const inlineGroup = document.getElementById('smsInlineDatePickerGroup');
        if (inlineGroup) inlineGroup.classList.add('d-none');

        // Close history collapse if open
        const historyCollapse = document.getElementById('smsHistoryCollapse');
        if (historyCollapse && typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
            const c = bootstrap.Collapse.getInstance(historyCollapse);
            if (c) c.hide();
        }

        // Load History
        loadHistory(smsState.intakeId);

        // Validate
        validateForm();

        // Show Modal
        if (!bsModal) {
            bsModal = new bootstrap.Modal(modalEl);
        }
        bsModal.show();
    }

    /**
     * Submit SMS message with pre-send SweetAlert confirmation dialog.
     */
    function submitSms() {
        if (!smsState.contactNumber || smsState.contactNumber === 'N/A' || smsState.contactNumber === 'No contact') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Contact Number',
                    text: 'No contact number is available for this beneficiary. Message cannot be sent.',
                    confirmButtonColor: '#1A237E'
                });
            } else {
                alert('No contact number is available for this beneficiary.');
            }
            return;
        }

        const message = messageBodyEl.value.trim();
        if (!message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Message',
                    text: 'Please write a message before sending.',
                    confirmButtonColor: '#1A237E'
                });
            }
            return;
        }

        const claimingDateDisplay = smsState.claimingDateFormatted || 'Not Specified';

        // Confirmation dialog before sending
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `<span style="font-size: 1.15rem; font-weight: 700;">Are you sure you want to send this message to ${smsState.beneficiaryName}?</span>`,
                html: `
                    <div class="text-start p-3 bg-light rounded-3 small border mb-2">
                        <div class="mb-1.5"><strong>Recipient Number:</strong> <span class="font-monospace text-primary">${smsState.contactNumber}</span></div>
                        <div class="mb-1.5"><strong>Claiming Date:</strong> <span class="fw-semibold text-dark">${claimingDateDisplay}</span></div>
                        <div class="mt-2 pt-2 border-top">
                            <strong class="d-block mb-1 text-muted">Message Preview:</strong>
                            <div class="p-2 bg-white rounded border fst-italic text-dark">${message}</div>
                        </div>
                    </div>
                    ${smsState.lastMessageDate ? '<div class="alert alert-warning py-1.5 px-2 text-start small mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Notice: This beneficiary was already contacted previously.</div>' : ''}
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Send Message',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#6B7280',
                reverseButtons: true,
                focusConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    dispatchSendRequest(message);
                }
            });
        } else {
            if (confirm(`Are you sure you want to send this SMS to ${smsState.beneficiaryName}?\nClaiming Date: ${claimingDateDisplay}\n\nMessage:\n"${message}"`)) {
                dispatchSendRequest(message);
            }
        }
    }

    /**
     * Dispatch the POST request to backend with spinner and anti-duplicate lock.
     */
    function dispatchSendRequest(message) {
        // Set loading state
        sendBtnEl.disabled = true;
        sendSpinnerEl.classList.remove('d-none');
        sendIconEl.classList.add('d-none');
        sendBtnTextEl.textContent = 'Sending...';

        const payload = {
            intake_id: smsState.intakeId,
            message: message,
            message_type: smsState.activeTemplate,
            claiming_date: smsState.claimingDateRaw || null,
            recipient_contact_number: smsState.contactNumber,
        };

        fetch('/admin/financial/financialstep2/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            // Reset loading state
            sendSpinnerEl.classList.add('d-none');
            sendIconEl.classList.remove('d-none');
            sendBtnTextEl.textContent = 'Send Message';
            sendBtnEl.disabled = false;

            if (status === 200 && body.success) {
                // Success
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: 'Message sent successfully.',
                        timer: 2500,
                        showConfirmButton: false,
                    });
                } else {
                    alert('Message sent successfully.');
                }

                // Update UI badges on the table row
                updateRowAfterSend(smsState.intakeId, body.data.last_sent_date);

                // Update local modal state
                smsState.lastMessageDate = body.data.last_sent_date;
                const duplicateAlert = document.getElementById('smsDuplicateAlert');
                const lastSentText = document.getElementById('smsLastSentText');
                if (duplicateAlert && lastSentText) {
                    duplicateAlert.classList.remove('d-none');
                    lastSentText.textContent = `Last message sent: ${smsState.lastMessageDate}`;
                }

                // Refresh history in modal
                loadHistory(smsState.intakeId);

                // Hide modal after short delay
                setTimeout(() => {
                    if (bsModal) bsModal.hide();
                }, 1200);

            } else {
                // Failed
                const errorMsg = body.message || 'Failed to send message.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to send message.',
                        text: errorMsg,
                        confirmButtonColor: '#1A237E'
                    });
                } else {
                    alert('Failed to send message: ' + errorMsg);
                }
                loadHistory(smsState.intakeId);
            }
        })
        .catch(err => {
            console.error('SMS Send Error:', err);
            sendSpinnerEl.classList.add('d-none');
            sendIconEl.classList.remove('d-none');
            sendBtnTextEl.textContent = 'Send Message';
            sendBtnEl.disabled = false;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to send message.',
                    text: 'A network error occurred while connecting to the server. Please try again.',
                    confirmButtonColor: '#1A237E'
                });
            } else {
                alert('Failed to send message. Please check your network connection.');
            }
        });
    }

    /**
     * Dynamically update the row in the table after sending.
     */
    function updateRowAfterSend(intakeId, sentDate) {
        // Update send message button to show last sent badge
        const lastSentSpan = document.getElementById(`last-sent-badge-${intakeId}`);
        if (lastSentSpan) {
            lastSentSpan.textContent = `Sent: ${sentDate}`;
            lastSentSpan.classList.remove('d-none');
        }

        // Update data attribute on row buttons
        const btns = document.querySelectorAll(`[data-intake-id="${intakeId}"]`);
        btns.forEach(btn => {
            btn.setAttribute('data-last-message-date', sentDate);
        });
    }

    /**
     * Save inline claiming date change.
     */
    function saveInlineClaimingDate() {
        const input = document.getElementById('smsInlineClaimingDateInput');
        if (!input || !input.value) return;

        const newRawDate = input.value;
        const newFormattedDate = formatReadableDate(newRawDate);

        // Update state
        smsState.claimingDateRaw = newRawDate;
        smsState.claimingDateFormatted = newFormattedDate;

        // Update display
        const claimingDateEl = document.getElementById('smsModalClaimingDate');
        claimingDateEl.textContent = newFormattedDate;
        claimingDateEl.className = 'fw-bold text-dark';

        document.getElementById('smsClaimingDateHidden').value = newRawDate;

        // Auto-refresh message template if user hasn't heavily customized it
        messageBodyEl.value = generateTemplate(smsState.activeTemplate);
        updateCharCounter();

        // Hide inline picker
        document.getElementById('smsInlineDatePickerGroup').classList.add('d-none');

        // Persist to backend
        fetch(`/admin/financial/financialstep2/intakes/${smsState.intakeId}/claiming-date`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ claiming_date: newRawDate })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update table row attribute
                const btns = document.querySelectorAll(`[data-intake-id="${smsState.intakeId}"]`);
                btns.forEach(btn => {
                    btn.setAttribute('data-claiming-date', newFormattedDate);
                    btn.setAttribute('data-raw-claiming-date', newRawDate);
                });
            }
        })
        .catch(err => console.error('Error updating claiming date:', err));

        validateForm();
    }

    // Initialize Event Listeners
    document.addEventListener('DOMContentLoaded', function () {
        modalEl = document.getElementById('smsMessagingModal');
        if (!modalEl) return;

        formEl = document.getElementById('smsComposeForm');
        messageBodyEl = document.getElementById('smsMessageBody');
        charCountEl = document.getElementById('smsCharCount');
        segmentCountEl = document.getElementById('smsSegmentCount');
        sendBtnEl = document.getElementById('btnSendSmsSubmit');
        sendSpinnerEl = document.getElementById('smsSendSpinner');
        sendIconEl = document.getElementById('smsSendIcon');
        sendBtnTextEl = document.getElementById('smsSendBtnText');

        // Live typing counter
        if (messageBodyEl) {
            messageBodyEl.addEventListener('input', function () {
                updateCharCounter();
                validateForm();
            });
        }

        // Template buttons
        document.querySelectorAll('.btn-template-select').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.btn-template-select').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                smsState.activeTemplate = this.getAttribute('data-type');
                if (smsState.activeTemplate !== 'Custom Message') {
                    messageBodyEl.value = generateTemplate(smsState.activeTemplate);
                }
                updateCharCounter();
                validateForm();
            });
        });

        // Reset to default button
        const btnResetDefault = document.getElementById('btnResetDefaultMessage');
        if (btnResetDefault) {
            btnResetDefault.addEventListener('click', function () {
                messageBodyEl.value = generateTemplate(smsState.activeTemplate);
                updateCharCounter();
                validateForm();
            });
        }

        // Send Message button click
        if (sendBtnEl) {
            sendBtnEl.addEventListener('click', function (e) {
                e.preventDefault();
                submitSms();
            });
        }

        // Inline Claiming Date Edit Triggers
        const btnEditClaimingDate = document.getElementById('btnEditClaimingDate');
        const inlineGroup = document.getElementById('smsInlineDatePickerGroup');
        const inlineInput = document.getElementById('smsInlineClaimingDateInput');
        const btnSaveInline = document.getElementById('btnSaveInlineClaimingDate');
        const btnCancelInline = document.getElementById('btnCancelInlineClaimingDate');

        if (btnEditClaimingDate && inlineGroup && inlineInput) {
            btnEditClaimingDate.addEventListener('click', function () {
                inlineGroup.classList.remove('d-none');
                inlineInput.value = smsState.claimingDateRaw || '';
                inlineInput.focus();
            });
        }

        if (btnCancelInline && inlineGroup) {
            btnCancelInline.addEventListener('click', function () {
                inlineGroup.classList.add('d-none');
            });
        }

        if (btnSaveInline) {
            btnSaveInline.addEventListener('click', function () {
                saveInlineClaimingDate();
            });
        }

        // Delegated trigger for all Send Message / Message buttons
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-send-sms, .btn-message-beneficiary');
            if (!btn) return;
            e.preventDefault();

            // Extract row / item data
            const intakeId = btn.getAttribute('data-intake-id');
            if (!intakeId) return;

            const beneficiaryName = btn.getAttribute('data-beneficiary-name') || '';
            const representativeName = btn.getAttribute('data-representative-name') || '';
            const isSeparateRep = btn.getAttribute('data-is-separate-rep') === '1' || btn.getAttribute('data-is-separate-rep') === 'true';
            const contactNumber = btn.getAttribute('data-contact-number') || '';
            const purpose = btn.getAttribute('data-purpose') || 'Financial Assistance';
            const amount = btn.getAttribute('data-amount-formatted') || '₱0.00';
            const claimingDate = btn.getAttribute('data-claiming-date') || '';
            const rawClaimingDate = btn.getAttribute('data-raw-claiming-date') || '';
            const lastMessageDate = btn.getAttribute('data-last-message-date') || '';
            const defaultTemplate = btn.getAttribute('data-default-template') || 'Unclaimed Assistance';

            // Check if already notified and show gentle prompt
            if (lastMessageDate && btn.classList.contains('btn-send-sms')) {
                // If clicked from unclaimed row with existing message, show notice or open modal directly
                openSmsModal({
                    intakeId,
                    beneficiaryName,
                    representativeName,
                    isSeparateRep,
                    contactNumber,
                    purpose,
                    amount,
                    claimingDate,
                    rawClaimingDate,
                    lastMessageDate,
                    defaultTemplate,
                });
            } else {
                openSmsModal({
                    intakeId,
                    beneficiaryName,
                    representativeName,
                    isSeparateRep,
                    contactNumber,
                    purpose,
                    amount,
                    claimingDate,
                    rawClaimingDate,
                    lastMessageDate,
                    defaultTemplate,
                });
            }
        });
    });

    // Expose to window
    window.openSmsModal = openSmsModal;

})();
