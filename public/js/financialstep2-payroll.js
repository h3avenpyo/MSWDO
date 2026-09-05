/**
 * Financial Assistance Step 2 Payroll Generation Module JavaScript
 */

// Retrieve configuration and initial metrics from workspace element or DOM
function getWorkspaceData() {
    const el = document.getElementById('payrollWorkspace');
    return {
        csrfToken: el?.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        updateAmountUrl: el?.dataset.updateAmountUrl || '',
        generatePayrollUrl: el?.dataset.generatePayrollUrl || '',
        printPayrollUrl: el?.dataset.printPayrollUrl || '',
        payrollRecordsUrl: el?.dataset.payrollRecordsUrl || '',
        payrollDate: el?.dataset.payrollDate || '',
        totalIntakes: parseInt(el?.dataset.totalIntakes || '0', 10),
        encodedIntakes: parseInt(el?.dataset.encodedIntakes || '0', 10),
        pendingIntakes: parseInt(el?.dataset.pendingIntakes || '0', 10),
        allEncoded: el?.dataset.allEncoded === 'true'
    };
}

let config = getWorkspaceData();

let totalIntakesCount = config.totalIntakes;
let encodedIntakesCount = config.encodedIntakes;
let pendingIntakesCount = config.pendingIntakes;
let isAllEncoded = config.allEncoded;

/**
 * Applies a preset amount into the input field and saves it.
 * @param {number|string} intakeId
 * @param {number|string} amount
 */
function applyPresetAmount(intakeId, amount) {
    const input = document.getElementById('input-amount-' + intakeId);
    if (!input) return;
    input.value = parseFloat(amount).toFixed(2);
    saveSingleAmount(intakeId);
}

/**
 * Handles input change styling for an amount field.
 * @param {number|string} intakeId
 */
function handleAmountInputChanged(intakeId) {
    const input = document.getElementById('input-amount-' + intakeId);
    if (!input) return;
    input.classList.remove('is-saved');
    if (parseFloat(input.value) > 0) {
        input.classList.remove('is-unencoded');
    } else {
        input.classList.add('is-unencoded');
    }
}

/**
 * Saves an encoded assistance amount via AJAX.
 * @param {number|string} intakeId
 */
function saveSingleAmount(intakeId) {
    config = getWorkspaceData();
    const input = document.getElementById('input-amount-' + intakeId);
    const btn = document.getElementById('btn-save-' + intakeId);
    if (!input) return;

    const amountVal = parseFloat(input.value);
    if (isNaN(amountVal) || amountVal < 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid non-negative financial assistance amount.',
                confirmButtonColor: '#1A237E'
            });
        }
        return;
    }

    const origBtnHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }

    fetch(config.updateAmountUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': config.csrfToken
        },
        body: JSON.stringify({
            intake_id: intakeId,
            recommended_amount: amountVal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                btn.innerHTML = origBtnHtml;
                btn.disabled = false;
            }, 1200);
        }

        if (data.success) {
            input.value = amountVal.toFixed(2);
            input.classList.add('is-saved');
            input.classList.remove('is-unencoded');

            // Update row status badge
            const statusBadgeEl = document.getElementById('status-badge-' + intakeId);
            const rowEl = document.getElementById('row-intake-' + intakeId);
            if (amountVal > 0) {
                if (statusBadgeEl) {
                    statusBadgeEl.innerHTML = '<span class="status-pill-encoded"><i class="fas fa-check-circle"></i> Encoded</span>';
                }
                if (rowEl) rowEl.classList.remove('table-warning-subtle');
            } else {
                if (statusBadgeEl) {
                    statusBadgeEl.innerHTML = '<span class="status-pill-pending"><i class="fas fa-exclamation-circle"></i> Required</span>';
                }
                if (rowEl) rowEl.classList.add('table-warning-subtle');
            }

            // Update stats
            updateDashboardMetrics(data);

            // Toast notification
            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Saving Amount',
                    text: data.message || 'Could not save financial assistance amount.',
                    confirmButtonColor: '#1A237E'
                });
            }
        }
    })
    .catch(err => {
        console.error(err);
        if (btn) {
            btn.innerHTML = origBtnHtml;
            btn.disabled = false;
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'An error occurred while saving the amount. Please try again.',
                confirmButtonColor: '#1A237E'
            });
        }
    });
}

/**
 * Updates UI counter badges and readiness banner dynamically.
 * @param {Object} data
 */
function updateDashboardMetrics(data) {
    if (!data) return;

    totalIntakesCount = data.total_today_count;
    encodedIntakesCount = data.encoded_count;
    pendingIntakesCount = data.pending_count;
    isAllEncoded = data.all_amounts_encoded;

    const elTotal = document.getElementById('statTotalIntakes');
    if (elTotal) elTotal.textContent = data.total_today_count;

    const elEncoded = document.getElementById('statEncodedCount');
    if (elEncoded) elEncoded.textContent = data.encoded_count;

    const elPending = document.getElementById('statPendingCount');
    if (elPending) elPending.textContent = data.pending_count;

    const elTotalAmt = document.getElementById('statTotalPayrollAmount');
    if (elTotalAmt) elTotalAmt.textContent = data.formatted_total_payroll_amount;
    
    const footerEnc = document.getElementById('footerEncodedCount');
    if (footerEnc) footerEnc.textContent = data.encoded_count;

    const footerPen = document.getElementById('footerPendingCount');
    if (footerPen) footerPen.textContent = data.pending_count;

    const readinessBanner = document.getElementById('readinessBanner');
    const readinessIcon = document.getElementById('readinessIcon');
    const readinessTitle = document.getElementById('readinessTitle');
    const readinessSubtitle = document.getElementById('readinessSubtitle');
    const btnGenerate = document.getElementById('btnGeneratePayroll');
    const btnPrint = document.getElementById('btnPrintPayroll');

    if (btnPrint) {
        if (totalIntakesCount > 0 && (isAllEncoded || encodedIntakesCount > 0)) {
            btnPrint.classList.remove('disabled');
            btnPrint.style.pointerEvents = 'auto';
            btnPrint.style.opacity = '1';
        } else {
            btnPrint.classList.add('disabled');
            btnPrint.style.pointerEvents = 'none';
            btnPrint.style.opacity = '0.5';
        }
    }

    if (readinessBanner) {
        if (totalIntakesCount === 0) {
            readinessBanner.className = 'payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3';
            if (readinessIcon) readinessIcon.className = 'fas fa-info-circle text-muted';
            if (readinessTitle) readinessTitle.textContent = "No Intake Records Recorded For This Date";
            if (readinessSubtitle) readinessSubtitle.textContent = "Clients who complete the Step 1 General Intake for this date will automatically appear here for amount encoding.";
            if (btnGenerate) btnGenerate.disabled = true;
        } else if (isAllEncoded) {
            readinessBanner.className = 'payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3';
            if (readinessIcon) readinessIcon.className = 'fas fa-check-circle text-success';
            if (readinessTitle) readinessTitle.textContent = "All Intakes Verified & Encoded! Ready for Payroll Generation";
            if (readinessSubtitle) readinessSubtitle.textContent = "Every client in the list has an assigned grant amount. You can now print or generate the official signed payroll record.";
            if (btnGenerate) btnGenerate.disabled = false;
        } else {
            readinessBanner.className = 'payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3';
            if (readinessIcon) readinessIcon.className = 'fas fa-exclamation-triangle text-warning';
            if (readinessTitle) readinessTitle.textContent = pendingIntakesCount + " of " + totalIntakesCount + " Intakes Pending Financial Assistance Amount";
            if (readinessSubtitle) readinessSubtitle.textContent = "Please encode the financial assistance amount for all remaining intakes below. Once verified, the Generate Payroll button will be enabled.";
            if (btnGenerate) btnGenerate.disabled = true;
        }
    }
}

/**
 * Prompts user confirmation and executes official payroll generation.
 */
function handleGeneratePayrollClick() {
    config = getWorkspaceData();

    if (totalIntakesCount === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'No Pending Intakes',
                text: 'There are no pending intake records to generate a payroll for this date. All intakes may have already been generated.',
                confirmButtonColor: '#1A237E'
            });
        }
        return;
    }

    const payrollDate = config.payrollDate || new Date().toISOString().split('T')[0];

    if (!isAllEncoded && pendingIntakesCount > 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Unencoded Amounts Detected',
                text: 'Please encode and save the financial assistance amount for all ' + pendingIntakesCount + ' remaining intake(s) before generating the official payroll.',
                confirmButtonColor: '#1A237E'
            });
        }
        return;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Generate Official Payroll?',
            html: '<div class="text-start small text-muted">' +
                  '<p>This action will generate an official payroll record for <strong>' + encodedIntakesCount + '</strong> beneficiaries on <strong>' + payrollDate + '</strong>.</p>' +
                  '<p class="mb-0"><i class="fas fa-shield-alt text-primary me-1"></i> <strong>Processed Isolation:</strong> Once generated, these intakes will be marked as processed and moved to Payroll Records. They will no longer appear in this generation list. Any intakes added later today can be generated as another separate payroll.</p>' +
                  '</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#64748B',
            confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Generate Payroll',
            cancelButtonText: 'Review First'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating Payroll...',
                    text: 'Finalizing payroll and archiving processed records...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(config.generatePayrollUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        date: payrollDate
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payroll Successfully Generated!',
                            text: data.message || 'Intakes have been processed and moved to Payroll Records.',
                            confirmButtonColor: '#1A237E',
                            confirmButtonText: '<i class="fas fa-arrow-right me-1"></i> View All Generated Payrolls'
                        }).then(() => {
                            window.location.href = data.redirect_url || config.payrollRecordsUrl;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Generation Error',
                            text: data.message || 'Failed to generate payroll. Please try again.',
                            confirmButtonColor: '#1A237E'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'System Error',
                        text: 'An unexpected error occurred while generating the payroll.',
                        confirmButtonColor: '#1A237E'
                    });
                });
            }
        });
    }
}

/**
 * Displays modal preview for an individual intake sheet record.
 * @param {Object} intake
 */
function viewIntakeDetails(intake) {
    if (!intake) return;

    const controlHeader = document.getElementById('modalControlNumberHeader');
    if (controlHeader) {
        controlHeader.textContent = 'Control No: ' + (intake.control_number || 'N/A') + ' | Processed: ' + (intake.date_processed ? intake.date_processed.split('T')[0] : 'N/A');
    }

    const benFullName = [intake.beneficiary_first_name, intake.beneficiary_middle_name, intake.beneficiary_last_name, intake.beneficiary_extension_name].filter(Boolean).join(' ') || 'N/A';
    const modalBenName = document.getElementById('modalBeneficiaryName');
    if (modalBenName) modalBenName.textContent = benFullName;

    const modalBenSexAge = document.getElementById('modalBeneficiarySexAge');
    if (modalBenSexAge) {
        modalBenSexAge.textContent = (intake.beneficiary_sex || 'N/A') + ' / ' + (intake.beneficiary_age ? intake.beneficiary_age + ' yrs' : 'N/A');
    }

    const modalBenBday = document.getElementById('modalBeneficiaryBirthday');
    if (modalBenBday) {
        modalBenBday.textContent = intake.beneficiary_birthday ? intake.beneficiary_birthday.split('T')[0] : 'N/A';
    }

    const modalBenContact = document.getElementById('modalBeneficiaryContact');
    if (modalBenContact) modalBenContact.textContent = intake.beneficiary_contact_number || 'N/A';
    
    const benAddress = [intake.beneficiary_street_address, intake.beneficiary_barangay, intake.beneficiary_city || 'Silang', intake.beneficiary_province || 'Cavite'].filter(Boolean).join(', ') || 'N/A';
    const modalBenAddr = document.getElementById('modalBeneficiaryAddress');
    if (modalBenAddr) modalBenAddr.textContent = benAddress;

    const modalBenBrgy = document.getElementById('modalBeneficiaryBarangay');
    if (modalBenBrgy) modalBenBrgy.textContent = intake.beneficiary_barangay || 'Silang';

    const modalBenOcc = document.getElementById('modalBeneficiaryOccupation');
    if (modalBenOcc) modalBenOcc.textContent = intake.beneficiary_occupation || 'N/A';

    const modalBenSalary = document.getElementById('modalBeneficiarySalary');
    if (modalBenSalary) {
        modalBenSalary.textContent = intake.beneficiary_monthly_salary ? '₱' + parseFloat(intake.beneficiary_monthly_salary).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A';
    }
    
    let categoriesText = intake.beneficiary_category || 'N/A';
    if (Array.isArray(intake.beneficiary_categories) && intake.beneficiary_categories.length > 0) {
        categoriesText = intake.beneficiary_categories.join(', ');
    }
    const modalBenCat = document.getElementById('modalBeneficiaryCategory');
    if (modalBenCat) modalBenCat.textContent = categoriesText;

    // Representative details
    const repCard = document.getElementById('modalRepresentativeCard');
    if (repCard) {
        if (intake.has_representative) {
            repCard.style.display = 'block';
            const repFullName = [intake.rep_first_name, intake.rep_middle_name, intake.rep_last_name, intake.rep_extension_name].filter(Boolean).join(' ') || 'N/A';
            const modalRepName = document.getElementById('modalRepName');
            if (modalRepName) modalRepName.textContent = repFullName;

            const modalRepRel = document.getElementById('modalRepRelationship');
            if (modalRepRel) modalRepRel.textContent = intake.rep_relationship || 'Representative';

            const modalRepSexAge = document.getElementById('modalRepSexAge');
            if (modalRepSexAge) modalRepSexAge.textContent = (intake.rep_sex || 'N/A') + ' / ' + (intake.rep_age ? intake.rep_age + ' yrs' : 'N/A');

            const modalRepContact = document.getElementById('modalRepContact');
            if (modalRepContact) modalRepContact.textContent = intake.rep_contact_number || 'N/A';

            const repAddress = [intake.rep_street_address, intake.rep_barangay, intake.rep_city || 'Silang', intake.rep_province || 'Cavite'].filter(Boolean).join(', ') || 'N/A';
            const modalRepAddr = document.getElementById('modalRepAddress');
            if (modalRepAddr) modalRepAddr.textContent = repAddress;

            const modalRepOcc = document.getElementById('modalRepOccupation');
            if (modalRepOcc) modalRepOcc.textContent = intake.rep_occupation || 'N/A';

            const modalRepSalary = document.getElementById('modalRepSalary');
            if (modalRepSalary) {
                modalRepSalary.textContent = intake.rep_monthly_salary ? '₱' + parseFloat(intake.rep_monthly_salary).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A';
            }
        } else {
            repCard.style.display = 'none';
        }
    }

    // Assessment & Assistance
    let medCond = 'None';
    if (Array.isArray(intake.medical_conditions) && intake.medical_conditions.length > 0) {
        medCond = intake.medical_conditions.join(', ');
    } else if (intake.medical_condition_other) {
        medCond = intake.medical_condition_other;
    }
    const modalMed = document.getElementById('modalMedicalConditions');
    if (modalMed) modalMed.textContent = medCond;

    const modalPurpose = document.getElementById('modalAssistancePurpose');
    if (modalPurpose) {
        modalPurpose.textContent = intake.purpose_other || intake.assistance_purpose || intake.purpose || 'N/A';
    }

    const modalAssess = document.getElementById('modalSocialWorkerAssessment');
    if (modalAssess) {
        modalAssess.textContent = intake.social_worker_assessment || 'Assessment completed in Step 1 intake.';
    }

    const modalType = document.getElementById('modalRecommendedType');
    if (modalType) {
        modalType.textContent = intake.recommended_assistance_type || intake.service_provided || 'Financial Assistance';
    }
    
    const modalAmt = document.getElementById('modalRecommendedAmount');
    if (modalAmt) {
        if (intake.recommended_amount) {
            modalAmt.textContent = '₱' + parseFloat(intake.recommended_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
        } else {
            modalAmt.textContent = 'To be assessed';
        }
    }

    const modalEl = document.getElementById('intakeQuickViewModal');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// Expose globally for backward compatibility
window.applyPresetAmount = applyPresetAmount;
window.handleAmountInputChanged = handleAmountInputChanged;
window.saveSingleAmount = saveSingleAmount;
window.updateDashboardMetrics = updateDashboardMetrics;
window.handleGeneratePayrollClick = handleGeneratePayrollClick;
window.viewIntakeDetails = viewIntakeDetails;

document.addEventListener('DOMContentLoaded', function () {
    config = getWorkspaceData();

    // Event listener for Generate Payroll button
    const btnGeneratePayroll = document.getElementById('btnGeneratePayroll');
    if (btnGeneratePayroll) {
        btnGeneratePayroll.addEventListener('click', function (e) {
            e.preventDefault();
            handleGeneratePayrollClick();
        });
    }

    // Delegated event listener for preset buttons
    document.addEventListener('click', function (e) {
        const presetBtn = e.target.closest('.preset-btn');
        if (presetBtn) {
            const intakeId = presetBtn.getAttribute('data-intake-id');
            const amount = presetBtn.getAttribute('data-amount');
            if (intakeId && amount) {
                applyPresetAmount(intakeId, amount);
            }
        }

        const saveBtn = e.target.closest('.btn-save-amount');
        if (saveBtn) {
            const intakeId = saveBtn.getAttribute('data-intake-id');
            if (intakeId) {
                saveSingleAmount(intakeId);
            }
        }

        const viewBtn = e.target.closest('.btn-view-intake');
        if (viewBtn) {
            const rawData = viewBtn.getAttribute('data-intake');
            if (rawData) {
                try {
                    const intake = JSON.parse(rawData);
                    viewIntakeDetails(intake);
                } catch (err) {
                    console.error('Failed to parse intake JSON:', err);
                }
            }
        }
    });

    // Enter key handling on amount input fields
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const inputField = e.target.closest('.amount-input-field');
            if (inputField) {
                const intakeId = inputField.getAttribute('data-intake-id');
                if (intakeId) {
                    e.preventDefault();
                    saveSingleAmount(intakeId);
                }
            }
        }
    });

    // Delegated input listener for amount input styling changes
    document.addEventListener('input', function (e) {
        const inputField = e.target.closest('.amount-input-field');
        if (inputField) {
            const intakeId = inputField.getAttribute('data-intake-id');
            if (intakeId) {
                handleAmountInputChanged(intakeId);
            }
        }
    });

    // Bulk save button click listener
    const btnBulkSave = document.getElementById('btnBulkSave');
    if (btnBulkSave) {
        btnBulkSave.addEventListener('click', function () {
            const bulkForm = document.getElementById('bulkPayrollForm');
            if (bulkForm) {
                bulkForm.requestSubmit();
            }
        });
    }

    // Automatic Search Debounce & Filter Focus
    const filterForm = document.getElementById('payrollFilterForm');
    const searchInput = document.getElementById('searchInput');

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

    // Auto-submit filter form on select or date change
    if (filterForm) {
        const autoSubmitInputs = filterForm.querySelectorAll('input[name="date"], select[name="barangay"], select[name="category"], select[name="status"], select[name="sort"]');
        autoSubmitInputs.forEach(function (element) {
            element.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }
});

