/**
 * Financial Assistance Step 2 Payroll Records JavaScript
 */

/**
 * Expand all collapsible payroll record cards on the Date View.
 */
function expandAllRecords() {
    document.querySelectorAll('.record-collapse').forEach(function (el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
            const collapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
            collapse.show();
        }
    });
}

/**
 * Collapse all collapsible payroll record cards on the Date View.
 */
function collapseAllRecords() {
    document.querySelectorAll('.record-collapse').forEach(function (el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
            const collapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
            collapse.hide();
        }
    });
}

// Expose functions globally for backward compatibility
window.expandAllRecords = expandAllRecords;
window.collapseAllRecords = collapseAllRecords;

document.addEventListener('DOMContentLoaded', function () {
    // Delegated / bound triggers for Expand All and Collapse All
    const btnExpandAll = document.getElementById('btnExpandAll');
    if (btnExpandAll) {
        btnExpandAll.addEventListener('click', function (e) {
            e.preventDefault();
            expandAllRecords();
        });
    }

    const btnCollapseAll = document.getElementById('btnCollapseAll');
    if (btnCollapseAll) {
        btnCollapseAll.addEventListener('click', function (e) {
            e.preventDefault();
            collapseAllRecords();
        });
    }

    document.addEventListener('click', function (e) {
        const expandTrigger = e.target.closest('.btn-expand-all');
        if (expandTrigger) {
            e.preventDefault();
            expandAllRecords();
        }

        const collapseTrigger = e.target.closest('.btn-collapse-all');
        if (collapseTrigger) {
            e.preventDefault();
            collapseAllRecords();
        }
    });

    // Automatic Search Debounce & Cursor Position Management
    const filterForm = document.getElementById('payrollRecordsFilterForm') || document.getElementById('datePayrollFilterForm');
    const searchInput = document.getElementById('recordsSearchInput');

    if (filterForm && searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                filterForm.submit();
            }, 550);
        });

        // Maintain cursor position at end of text after auto-submit
        if (searchInput.value.trim().length > 0 && document.activeElement !== searchInput) {
            const val = searchInput.value;
            searchInput.focus();
            searchInput.setSelectionRange(val.length, val.length);
        }
    }

    // Automatic form submission on filter change for selects and date inputs
    const filterForms = document.querySelectorAll('#payrollRecordsFilterForm, #datePayrollFilterForm');
    filterForms.forEach(function (form) {
        const inputs = form.querySelectorAll('select[name="barangay"], select[name="payroll_id"], select[name="claim_status"], select[name="sort"], input[name="date_from"], input[name="date_to"]');
        inputs.forEach(function (input) {
            input.addEventListener('change', function () {
                form.submit();
            });
        });
    });

    // Delegated Claim Status Update Action
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-mark-claim');
        if (!btn) return;

        e.preventDefault();

        const intakeId = btn.getAttribute('data-intake-id');
        const targetStatus = btn.getAttribute('data-status');
        const beneficiaryName = btn.getAttribute('data-beneficiary-name') || 'the beneficiary';
        const recordId = btn.getAttribute('data-record-id');

        if (!intakeId || !targetStatus) return;

        if (targetStatus === 'Claimed') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Mark as Claimed?',
                    html: `Confirm that financial assistance for <strong>${beneficiaryName}</strong> has been successfully released and claimed?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Yes, Mark Claimed',
                    cancelButtonText: 'Cancel',
                    customClass: { popup: 'rounded-4 shadow-lg' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeClaimStatusUpdate(btn, intakeId, targetStatus, beneficiaryName, recordId);
                    }
                });
            } else if (confirm(`Confirm that financial assistance for ${beneficiaryName} has been claimed?`)) {
                executeClaimStatusUpdate(btn, intakeId, targetStatus, beneficiaryName, recordId);
            }
        } else {
            // Revert / Undo to Unclaimed
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Revert to Unclaimed?',
                    html: `Revert claim status for <strong>${beneficiaryName}</strong> back to <strong>Unclaimed</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D97706',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-rotate-left me-1"></i> Yes, Revert to Unclaimed',
                    cancelButtonText: 'Cancel',
                    customClass: { popup: 'rounded-4 shadow-lg' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeClaimStatusUpdate(btn, intakeId, targetStatus, beneficiaryName, recordId);
                    }
                });
            } else if (confirm(`Revert status for ${beneficiaryName} back to Unclaimed?`)) {
                executeClaimStatusUpdate(btn, intakeId, targetStatus, beneficiaryName, recordId);
            }
        }
    });

    /**
     * Send AJAX request to update beneficiary claim status.
     */
    function executeClaimStatusUpdate(btn, intakeId, targetStatus, beneficiaryName, recordId) {
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const rowAmount = parseFloat(btn.getAttribute('data-amount')) || 0;

        const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenEl ? csrfTokenEl.content : '';

        const url = `/admin/financial/financialstep2/payroll/intake/${intakeId}/claim-status`;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: targetStatus
            })
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, status: response.status, data: data };
            });
        })
        .then(function (res) {
            btn.disabled = false;
            if (!res.ok) {
                btn.innerHTML = originalHtml;
                const errorMsg = (res.data && res.data.message) ? res.data.message : 'Failed to update claim status.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Error',
                        text: errorMsg,
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                } else {
                    alert(errorMsg);
                }
                return;
            }

            const statusCol = document.getElementById(`claim-status-col-${intakeId}`);
            const actionCol = document.getElementById(`claim-action-col-${intakeId}`);
            const intakeAmount = (res.data && res.data.recommended_amount !== undefined) 
                ? parseFloat(res.data.recommended_amount) 
                : rowAmount;

            // Preserve metadata for SMS action buttons from existing elements
            const existingSmsBtn = actionCol ? actionCol.querySelector('.btn-send-sms, .btn-message-beneficiary') : null;
            const repName = existingSmsBtn ? (existingSmsBtn.getAttribute('data-representative-name') || '') : '';
            const isSeparateRep = existingSmsBtn ? (existingSmsBtn.getAttribute('data-is-separate-rep') || '0') : '0';
            const contactNo = existingSmsBtn ? (existingSmsBtn.getAttribute('data-contact-number') || '') : '';
            const purpose = existingSmsBtn ? (existingSmsBtn.getAttribute('data-purpose') || 'Financial Assistance') : 'Financial Assistance';
            const amountFormatted = existingSmsBtn ? (existingSmsBtn.getAttribute('data-amount-formatted') || '') : '';
            const claimingDate = existingSmsBtn ? (existingSmsBtn.getAttribute('data-claiming-date') || '') : '';
            const rawClaimingDate = existingSmsBtn ? (existingSmsBtn.getAttribute('data-raw-claiming-date') || '') : '';
            const lastMsgDate = existingSmsBtn ? (existingSmsBtn.getAttribute('data-last-message-date') || '') : '';

            // Check existing last-sent-badge
            const existingBadge = document.getElementById(`last-sent-badge-${intakeId}`);
            const lastSentDate = lastMsgDate || (existingBadge && !existingBadge.classList.contains('d-none') ? existingBadge.textContent.replace('Last sent:', '').trim() : '');
            const badgeHtml = lastSentDate 
                ? `<div class="last-sent-badge text-muted text-2xs" id="last-sent-badge-${intakeId}"><i class="fas fa-paper-plane text-primary me-1"></i> Last sent: ${lastSentDate}</div>`
                : `<div class="last-sent-badge text-muted text-2xs d-none" id="last-sent-badge-${intakeId}"></div>`;

            if (targetStatus === 'Claimed') {
                if (statusCol) {
                    statusCol.innerHTML = `
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 text-xs fw-semibold claim-status-badge">
                            <i class="fas fa-check-circle me-1"></i> Claimed
                        </span>
                        <div class="text-muted text-2xs mt-0.5">${res.data.claimed_at || 'Just now'}</div>
                    `;
                }
                if (actionCol) {
                    actionCol.innerHTML = `
                        <div class="table-action-wrapper">
                            <div class="table-action-group">
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-pill btn-mark-claim"
                                    data-intake-id="${intakeId}"
                                    data-beneficiary-name="${beneficiaryName}"
                                    data-status="Unclaimed"
                                    data-record-id="${recordId || ''}"
                                    data-amount="${intakeAmount}"
                                    title="Click to revert status to Unclaimed">
                                    <i class="fas fa-rotate-left"></i> Undo
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-pill btn-message-beneficiary"
                                    data-intake-id="${intakeId}"
                                    data-beneficiary-name="${beneficiaryName}"
                                    data-representative-name="${repName}"
                                    data-is-separate-rep="${isSeparateRep}"
                                    data-contact-number="${contactNo}"
                                    data-purpose="${purpose}"
                                    data-amount-formatted="${amountFormatted}"
                                    data-claiming-date="${claimingDate}"
                                    data-raw-claiming-date="${rawClaimingDate}"
                                    data-last-message-date="${lastSentDate}"
                                    data-default-template="Follow-up"
                                    title="Send SMS message to beneficiary">
                                    <i class="fas fa-comment-sms"></i> Message
                                </button>
                            </div>
                            ${badgeHtml}
                        </div>
                    `;
                }

                // Adjust batch & date counters: Claimed +1, Unclaimed -1
                adjustBatchCounters(recordId, 1, -1, intakeAmount, 'Claimed', res.data);
            } else {
                if (statusCol) {
                    statusCol.innerHTML = `
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 text-xs fw-semibold claim-status-badge">
                            <i class="fas fa-clock me-1"></i> Unclaimed
                        </span>
                    `;
                }
                if (actionCol) {
                    actionCol.innerHTML = `
                        <div class="table-action-wrapper">
                            <div class="table-action-group">
                                <button type="button"
                                    class="btn btn-sm btn-primary rounded-pill btn-send-sms fw-semibold"
                                    data-intake-id="${intakeId}"
                                    data-beneficiary-name="${beneficiaryName}"
                                    data-representative-name="${repName}"
                                    data-is-separate-rep="${isSeparateRep}"
                                    data-contact-number="${contactNo}"
                                    data-purpose="${purpose}"
                                    data-amount-formatted="${amountFormatted}"
                                    data-claiming-date="${claimingDate}"
                                    data-raw-claiming-date="${rawClaimingDate}"
                                    data-last-message-date="${lastSentDate}"
                                    data-default-template="Unclaimed Assistance"
                                    title="Send unclaimed notification via SMS">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-success rounded-pill btn-mark-claim"
                                    data-intake-id="${intakeId}"
                                    data-beneficiary-name="${beneficiaryName}"
                                    data-status="Claimed"
                                    data-record-id="${recordId || ''}"
                                    data-amount="${intakeAmount}"
                                    title="Mark financial assistance as Claimed">
                                    <i class="fas fa-check"></i> Claimed
                                </button>
                            </div>
                            ${badgeHtml}
                        </div>
                    `;
                }

                // Adjust batch & date counters: Claimed -1, Unclaimed +1
                adjustBatchCounters(recordId, -1, 1, intakeAmount, 'Unclaimed', res.data);
            }

            // Toast notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.data.message || 'Claim status updated.',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            }
        })
        .catch(function (err) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            console.error('Error updating claim status:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to update claim status. Please try again.',
                    customClass: { popup: 'rounded-4 shadow-lg' }
                });
            } else {
                alert('Unable to update claim status. Please try again.');
            }
        });
    }

    /**
     * Format number as currency (₱X,XXX.XX)
     */
    function formatCurrency(amount) {
        return '₱' + parseFloat(amount || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * Adjust live UI counters and total amounts for claimed vs unclaimed.
     */
    function adjustBatchCounters(recordId, claimedDelta, unclaimedDelta, rowAmount, status, resData) {
        rowAmount = parseFloat(rowAmount) || 0;

        // 1. Batch-specific counters
        if (recordId) {
            const batchClaimedBadge = document.getElementById(`batch-claimed-count-${recordId}`);
            const batchUnclaimedBadge = document.getElementById(`batch-unclaimed-count-${recordId}`);
            const tfootClaimed = document.getElementById(`tfoot-claimed-${recordId}`);
            const tfootUnclaimed = document.getElementById(`tfoot-unclaimed-${recordId}`);

            if (batchClaimedBadge) {
                const currentClaimed = parseInt(batchClaimedBadge.textContent.replace(/[^0-9]/g, ''), 10) || 0;
                const newClaimed = Math.max(0, currentClaimed + claimedDelta);
                batchClaimedBadge.innerHTML = `<i class="fas fa-check-circle me-1"></i>${newClaimed}`;
            }
            if (batchUnclaimedBadge) {
                const currentUnclaimed = parseInt(batchUnclaimedBadge.textContent.replace(/[^0-9]/g, ''), 10) || 0;
                const newUnclaimed = Math.max(0, currentUnclaimed + unclaimedDelta);
                batchUnclaimedBadge.innerHTML = `<i class="fas fa-clock me-1"></i>${newUnclaimed}`;
            }

            if (tfootClaimed) {
                const currentTfootClaimed = parseInt(tfootClaimed.textContent.replace(/[^0-9]/g, ''), 10) || 0;
                tfootClaimed.textContent = Math.max(0, currentTfootClaimed + claimedDelta);
            }
            if (tfootUnclaimed) {
                const currentTfootUnclaimed = parseInt(tfootUnclaimed.textContent.replace(/[^0-9]/g, ''), 10) || 0;
                tfootUnclaimed.textContent = Math.max(0, currentTfootUnclaimed + unclaimedDelta);
            }

            // Batch-level Unclaimed Amount
            const batchUnclaimedAmountEl = document.getElementById(`batch-unclaimed-amount-${recordId}`);
            if (batchUnclaimedAmountEl) {
                let currentRaw = parseFloat(batchUnclaimedAmountEl.getAttribute('data-raw-amount')) || 0;
                let newBatchUnclaimed = status === 'Claimed' 
                    ? Math.max(0, currentRaw - rowAmount) 
                    : (currentRaw + rowAmount);
                batchUnclaimedAmountEl.setAttribute('data-raw-amount', newBatchUnclaimed);
                batchUnclaimedAmountEl.textContent = formatCurrency(newBatchUnclaimed);

                const tfootUnclaimedAmountEl = document.getElementById(`tfoot-unclaimed-amount-${recordId}`);
                if (tfootUnclaimedAmountEl) {
                    tfootUnclaimedAmountEl.textContent = formatCurrency(newBatchUnclaimed);
                }
            }
        }

        // 2. Date View Metric Cards (Overall for this Date)
        // Total Unclaimed Financial Assistance Amount
        const statUnclaimedEl = document.getElementById('stat-unclaimed-amount');
        let finalUnclaimedAmount = null;

        if (statUnclaimedEl) {
            let currentRaw = parseFloat(statUnclaimedEl.getAttribute('data-raw-amount')) || 0;
            if (resData && resData.date_unclaimed_amount !== null && resData.date_unclaimed_amount !== undefined) {
                finalUnclaimedAmount = parseFloat(resData.date_unclaimed_amount);
            } else {
                finalUnclaimedAmount = status === 'Claimed' 
                    ? Math.max(0, currentRaw - rowAmount) 
                    : (currentRaw + rowAmount);
            }
            statUnclaimedEl.setAttribute('data-raw-amount', finalUnclaimedAmount);
            statUnclaimedEl.textContent = formatCurrency(finalUnclaimedAmount);
        }

        // Controls bar & Banner Unclaimed Amount
        const barUnclaimedEl = document.getElementById('bar-unclaimed-amount');
        if (barUnclaimedEl && finalUnclaimedAmount !== null) {
            barUnclaimedEl.textContent = formatCurrency(finalUnclaimedAmount);
        }
        const bannerUnclaimedEl = document.getElementById('banner-unclaimed-amount');
        if (bannerUnclaimedEl && finalUnclaimedAmount !== null) {
            bannerUnclaimedEl.textContent = formatCurrency(finalUnclaimedAmount);
        }

        // Unclaimed Beneficiaries Count
        const statUnclaimedCountEl = document.getElementById('stat-unclaimed-count');
        if (statUnclaimedCountEl) {
            let currentCount = parseInt(statUnclaimedCountEl.textContent.replace(/[^0-9]/g, ''), 10) || 0;
            let newCount = (resData && resData.date_unclaimed_count !== null && resData.date_unclaimed_count !== undefined)
                ? resData.date_unclaimed_count
                : Math.max(0, currentCount + unclaimedDelta);
            statUnclaimedCountEl.textContent = newCount;
        }

        // Total Claimed Financial Assistance Amount & Count
        const statClaimedEl = document.getElementById('stat-claimed-amount');
        if (statClaimedEl) {
            let currentRaw = parseFloat(statClaimedEl.getAttribute('data-raw-amount')) || 0;
            let finalClaimedAmount = (resData && resData.date_claimed_amount !== null && resData.date_claimed_amount !== undefined)
                ? parseFloat(resData.date_claimed_amount)
                : (status === 'Claimed' ? (currentRaw + rowAmount) : Math.max(0, currentRaw - rowAmount));
            statClaimedEl.setAttribute('data-raw-amount', finalClaimedAmount);
            statClaimedEl.textContent = formatCurrency(finalClaimedAmount);
        }

        const statClaimedCountEl = document.getElementById('stat-claimed-count');
        if (statClaimedCountEl) {
            let currentCount = parseInt(statClaimedCountEl.textContent.replace(/[^0-9]/g, ''), 10) || 0;
            let newCount = (resData && resData.date_claimed_count !== null && resData.date_claimed_count !== undefined)
                ? resData.date_claimed_count
                : Math.max(0, currentCount + claimedDelta);
            statClaimedCountEl.textContent = newCount;
        }
    }
});
