/**
 * Beneficiary Intake Form Handlers (Create & Edit)
 * Includes real-time 6-Month Validity & Automatic Duplicate Checking (Strict Policy Enforcement)
 * Instant Pop-up on Duplicate Match & Instant Removal when clear/non-duplicate.
 */

let duplicateCheckTimer = null;
let hasActiveDuplicate = false;
let activeDuplicateDetails = null;

function formatAndCalculateAge(inputEl, ageInputId) {
    let val = inputEl.value.replace(/\D/g, '');
    if (val.length >= 2 && val.length < 4) {
        val = val.slice(0, 2) + '/' + val.slice(2);
    } else if (val.length >= 4) {
        val = val.slice(0, 2) + '/' + val.slice(2, 4) + '/' + val.slice(4, 8);
    }
    inputEl.value = val;

    const ageEl = document.getElementById(ageInputId);
    if (ageEl && val.length === 10) {
        const parts = val.split('/');
        const month = parseInt(parts[0], 10) - 1;
        const day = parseInt(parts[1], 10);
        const year = parseInt(parts[2], 10);

        if (!isNaN(month) && !isNaN(day) && !isNaN(year) && year > 1900 && month >= 0 && month <= 11 && day >= 1 && day <= 31) {
            const birthDate = new Date(year, month, day);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageEl.value = age >= 0 ? age : '';
            return;
        }
    }
    if (ageEl) {
        ageEl.value = '';
    }
}

function toggleCategoryOtherText() {
    const catOthers = document.getElementById('cat_others');
    const otherInput = document.getElementById('beneficiary_category_other_input');
    if (catOthers && catOthers.checked) {
        otherInput?.classList.remove('d-none');
    } else if (otherInput) {
        otherInput.classList.add('d-none');
        otherInput.value = '';
    }
}

function togglePurposeOtherInput() {
    const select = document.getElementById('assistance_purpose_select');
    const otherInput = document.getElementById('purpose_other_input');
    if (select && (select.value === 'Other Medical Conditions' || select.value === 'Others')) {
        otherInput?.classList.remove('d-none');
    } else if (otherInput) {
        otherInput.classList.add('d-none');
        otherInput.value = '';
    }
}

function toggleRepresentativeSection() {
    const hasRepCheckbox = document.getElementById('has_representative');
    const repSection = document.getElementById('representative_section');
    if (!hasRepCheckbox || !repSection) return;

    const hasRep = hasRepCheckbox.checked;
    const repFields = repSection.querySelectorAll('.rep-field');

    if (hasRep) {
        repSection.classList.remove('rep-card-disabled');
        repFields.forEach(el => el.disabled = false);
    } else {
        repSection.classList.add('rep-card-disabled');
        repFields.forEach(el => {
            el.disabled = true;
            if (el.tagName === 'INPUT' && el.type !== 'hidden') el.value = '';
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
        });
    }

    triggerDuplicateCheck();
}

// Dynamic Family Composition Table
function addFamilyRow() {
    const tbody = document.getElementById('familyTableBody');
    if (!tbody) return;

    const familyIndex = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="family_composition[${familyIndex}][name]" class="form-control form-control-sm" placeholder="Full Name"></td>
        <td><input type="text" name="family_composition[${familyIndex}][relationship]" class="form-control form-control-sm" placeholder="e.g. Spouse, Son"></td>
        <td><input type="number" min="0" name="family_composition[${familyIndex}][age]" class="form-control form-control-sm" placeholder="Edad"></td>
        <td><input type="text" name="family_composition[${familyIndex}][occupation]" class="form-control form-control-sm" placeholder="Trabaho"></td>
        <td><input type="number" step="0.01" min="0" name="family_composition[${familyIndex}][salary]" class="form-control form-control-sm" placeholder="0.00"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFamilyRow(this)"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function removeFamilyRow(btn) {
    const row = btn.closest('tr');
    const tbody = document.getElementById('familyTableBody');
    if (tbody && tbody.children.length > 1) {
        row.remove();
    } else if (row) {
        row.querySelectorAll('input').forEach(i => i.value = '');
    }
}

/**
 * Instantly hide duplicate alert card
 */
function hideDuplicateCard() {
    hasActiveDuplicate = false;
    activeDuplicateDetails = null;
    const card = document.getElementById('duplicateAlertCard');
    const tbody = document.getElementById('duplicateMatchesTableBody');
    if (card) {
        card.classList.add('d-none');
    }
    if (tbody) {
        tbody.innerHTML = '';
    }
}

/**
 * Trigger debounced automatic 6-month validity duplicate check
 */
function triggerDuplicateCheck() {
    if (duplicateCheckTimer) {
        clearTimeout(duplicateCheckTimer);
    }
    duplicateCheckTimer = setTimeout(performDuplicateCheck, 300);
}

function performDuplicateCheck() {
    const benFirstName = document.querySelector('input[name="beneficiary_first_name"]')?.value || '';
    const benLastName = document.querySelector('input[name="beneficiary_last_name"]')?.value || '';
    const benBirthday = document.querySelector('input[name="beneficiary_birthday"]')?.value || '';

    const hasRep = document.getElementById('has_representative')?.checked || false;
    const repFirstName = document.querySelector('input[name="rep_first_name"]')?.value || '';
    const repLastName = document.querySelector('input[name="rep_last_name"]')?.value || '';
    const repBirthday = document.querySelector('input[name="rep_birthday"]')?.value || '';

    const dateProcessed = document.querySelector('input[name="date_processed"]')?.value || '';
    const excludeId = document.getElementById('exclude_id')?.value || null;
    const csrfToken = document.querySelector('input[name="_token"]')?.value || '';

    // If inputs are cleared or insufficient, instantly hide validity card
    if ((benFirstName.trim().length < 2 || benLastName.trim().length < 2) && (!hasRep || repFirstName.trim().length < 2 || repLastName.trim().length < 2)) {
        hideDuplicateCard();
        return;
    }

    const payload = {
        beneficiary_first_name: benFirstName,
        beneficiary_last_name: benLastName,
        beneficiary_birthday: benBirthday,
        has_representative: hasRep ? 1 : 0,
        rep_first_name: repFirstName,
        rep_last_name: repLastName,
        rep_birthday: repBirthday,
        date_processed: dateProcessed,
        exclude_id: excludeId
    };

    fetch('/admin/beneficiary-intake/check-duplicate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(response => response.json())
        .then(data => {
            const card = document.getElementById('duplicateAlertCard');
            const textEl = document.getElementById('duplicateWarningText');
            const tbody = document.getElementById('duplicateMatchesTableBody');

            if (!card || !textEl || !tbody) return;

            if (data.is_duplicate) {
                hasActiveDuplicate = true;
                activeDuplicateDetails = data.matches && data.matches.length > 0 ? data.matches[0] : null;
                textEl.textContent = data.warning_message || 'Beneficiary has already received financial assistance within the last 6 months.';

                let html = '';
                if (data.matches && data.matches.length > 0) {
                    data.matches.forEach(match => {
                        html += `
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td class="ps-3 fw-bold" style="color: #1A237E;">${match.control_number}</td>
                            <td class="text-secondary">${match.date_processed}</td>
                            <td>
                                <span class="px-2.5 py-0.5 rounded-pill fw-semibold" style="background: #FEF2F2; border: 1px solid #F87171; color: #991B1B; font-size: 0.75rem;">
                                    ${match.matched_role}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">${match.beneficiary_name}</td>
                            <td class="text-secondary">${match.representative_name}</td>
                            <td class="text-secondary">${match.assistance_type}</td>
                            <td class="pe-3 text-end fw-bold" style="color: #DC2626;">${match.eligible_again_date}</td>
                        </tr>
                    `;
                    });
                }
                tbody.innerHTML = html;

                // POP-UP INSTANTLY
                card.classList.remove('d-none');
            } else {
                // INSTANTLY HIDE WHEN NO DUPLICATION HAPPENING
                hideDuplicateCard();
            }
        })
        .catch(err => {
            console.error('Error checking duplicates:', err);
        });
}

/**
 * Strict 11-digit Contact Number Input & Validation Handler
 * - Allows numbers only (0-9)
 * - Maximum and minimum length: exactly 11 digits
 * - Prevents letters, symbols, spaces, or >11 digits
 * - Realtime digit counter & clear feedback
 */
function initContactNumberValidation() {
    const contactInputs = document.querySelectorAll('.contact-number-input');

    contactInputs.forEach(input => {
        const inputId = input.id;
        const counterEl = document.getElementById(`${inputId}_counter`);
        const errorEl = document.getElementById(`${inputId}_error`);

        function validateInput(showErrors = false) {
            // Strip any non-digits and cap at 11 digits
            let rawVal = input.value;
            let cleanVal = rawVal.replace(/\D/g, '').slice(0, 11);
            if (rawVal !== cleanVal) {
                input.value = cleanVal;
            }

            const len = cleanVal.length;
            const isRep = input.classList.contains('rep-field');
            const hasRep = document.getElementById('has_representative')?.checked || false;
            const isRequired = input.required || (isRep && hasRep);

            // Update live digit counter
            if (counterEl) {
                if (len === 11) {
                    counterEl.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>11/11 digits</span>`;
                } else if (len > 0) {
                    const remaining = 11 - len;
                } else {
                    counterEl.textContent = '0/11 digits';
                    counterEl.className = 'text-muted small contact-digit-counter';
                }
            }

            // Not required and empty
            if (len === 0 && !isRequired) {
                input.classList.remove('is-invalid', 'is-valid');
                input.setCustomValidity('');
                if (errorEl) errorEl.style.setProperty('display', 'none', 'important');
                return true;
            }

            // Exactly 11 digits -> Valid
            if (len === 11) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                input.setCustomValidity('');
                if (errorEl) errorEl.style.setProperty('display', 'none', 'important');
                return true;
            } else {
                input.classList.remove('is-valid');
                if (showErrors || len > 0) {
                    input.classList.add('is-invalid');
                    if (errorEl) {
                        const fieldName = isRep ? 'Representative contact number' : 'Numero ng telepono (Contact number)';
                        errorEl.textContent = len === 0
                            ? `${fieldName} is required and must be exactly 11 digits.`
                            : `${fieldName} must be exactly 11 digits. Currently: ${len} digit${len > 1 ? 's' : ''}.`;
                        errorEl.style.removeProperty('display');
                    }
                    input.setCustomValidity('Contact number must be exactly 11 digits (e.g., 09XXXXXXXXX).');
                } else {
                    input.classList.remove('is-invalid');
                    input.setCustomValidity(isRequired ? 'Please fill out this field.' : '');
                }
                return false;
            }
        }

        // Prevent non-numeric key typing
        input.addEventListener('keydown', function (e) {
            // Allow control and navigation keys
            const allowedKeys = [
                'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                'Home', 'End'
            ];
            if (allowedKeys.includes(e.key)) return;

            // Allow Ctrl / Cmd combinations (A, C, V, X, Z)
            if (e.ctrlKey || e.metaKey) return;

            // Reject anything that is not a numeric digit 0-9
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
                return;
            }

            // Prevent typing more than 11 digits unless replacing selected text
            const selectedText = input.value.substring(input.selectionStart, input.selectionEnd);
            const digits = input.value.replace(/\D/g, '');
            if (digits.length >= 11 && selectedText.length === 0) {
                e.preventDefault();
            }
        });

        // Realtime input filtering & validation
        input.addEventListener('input', function () {
            validateInput(input.value.length > 0);
        });

        // Paste handler: clean non-digits, convert +639 if present, and enforce 11 digits
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text') || '';
            let digits = pastedText.replace(/\D/g, '');
            if (digits.startsWith('63') && digits.length === 12) {
                digits = '0' + digits.slice(2);
            }
            digits = digits.slice(0, 11);

            const start = input.selectionStart;
            const end = input.selectionEnd;
            const currentVal = input.value;
            const newVal = (currentVal.substring(0, start) + digits + currentVal.substring(end)).replace(/\D/g, '').slice(0, 11);
            input.value = newVal;
            validateInput(true);
        });

        input.addEventListener('blur', function () {
            validateInput(true);
        });

        // Run validation on initial load if pre-filled
        if (input.value) {
            validateInput(false);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const benBirth = document.getElementById('beneficiary_birthday');
    if (benBirth && benBirth.value) {
        formatAndCalculateAge(benBirth, 'beneficiary_age');
    }
    const repBirth = document.getElementById('rep_birthday');
    if (repBirth && repBirth.value) {
        formatAndCalculateAge(repBirth, 'rep_age');
    }
    toggleRepresentativeSection();
    togglePurposeOtherInput();
    initContactNumberValidation();

    // Check server-side duplicate error state
    const alertCard = document.getElementById('duplicateAlertCard');
    if (alertCard && !alertCard.classList.contains('d-none')) {
        hasActiveDuplicate = true;
    }

    // Attach listeners for real-time instant pop-up / removal
    const inputsToCheck = [
        'beneficiary_first_name',
        'beneficiary_last_name',
        'beneficiary_birthday',
        'rep_first_name',
        'rep_last_name',
        'rep_birthday',
        'date_processed'
    ];

    inputsToCheck.forEach(name => {
        const el = document.querySelector(`input[name="${name}"]`);
        if (el) {
            el.addEventListener('input', triggerDuplicateCheck);
            el.addEventListener('change', triggerDuplicateCheck);
            el.addEventListener('blur', triggerDuplicateCheck);
        }
    });

    const formEl = document.getElementById('intakeForm') || document.getElementById('editIntakeForm');
    if (formEl) {
        formEl.addEventListener('submit', function (e) {
            e.preventDefault();

            // Strict 11-digit Contact Number Verification before submit
            const benContact = document.getElementById('beneficiary_contact_number');
            if (benContact) {
                const benDigits = benContact.value.replace(/\D/g, '');
                if (benDigits.length !== 11) {
                    benContact.classList.add('is-invalid');
                    benContact.focus();
                    const benError = document.getElementById('beneficiary_contact_number_error');
                    if (benError) {
                        benError.textContent = `Numero ng telepono must be exactly 11 digits. Currently: ${benDigits.length} digits.`;
                        benError.style.removeProperty('display');
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Incomplete Contact Number',
                            text: 'Beneficiary contact number must be exactly 11 digits (0–9) without letters, spaces, or symbols (e.g., 09XXXXXXXXX).',
                            confirmButtonColor: '#1A237E'
                        });
                    } else {
                        alert('Beneficiary contact number must be exactly 11 digits.');
                    }
                    return;
                }
            }

            const hasRep = document.getElementById('has_representative')?.checked || false;
            const repContact = document.getElementById('rep_contact_number');
            if (hasRep && repContact) {
                const repDigits = repContact.value.replace(/\D/g, '');
                if (repDigits.length !== 11) {
                    repContact.classList.add('is-invalid');
                    repContact.focus();
                    const repError = document.getElementById('rep_contact_number_error');
                    if (repError) {
                        repError.textContent = `Representative contact number must be exactly 11 digits. Currently: ${repDigits.length} digits.`;
                        repError.style.removeProperty('display');
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Incomplete Representative Contact',
                            text: 'Representative contact number must be exactly 11 digits (0–9) without letters, spaces, or symbols (e.g., 09XXXXXXXXX).',
                            confirmButtonColor: '#1A237E'
                        });
                    } else {
                        alert('Representative contact number must be exactly 11 digits.');
                    }
                    return;
                }
            }

            if (!formEl.checkValidity()) {
                formEl.reportValidity();
                return;
            }

            // Strict 6-Month Policy Guard Check
            if (hasActiveDuplicate) {
                const eligibleDate = activeDuplicateDetails?.eligible_again_date || 'the end of the 6-month validity period';
                const controlNo = activeDuplicateDetails?.control_number || 'previous intake sheet';

                Swal.fire({
                    title: 'Application Restricted!',
                    html: `
                        <div class="text-start">
                            <p class="text-danger fw-bold mb-2"><i class="fas fa-ban me-1"></i> 6-Month Validity Policy Restriction</p>
                            <p class="small text-muted mb-2">The Beneficiary has a previous financial assistance record (<strong>${controlNo}</strong>) within the last 6 months.</p>
                            <div class="alert alert-danger small mb-0 py-2">
                                <i class="fas fa-calendar-times me-1"></i> Under MSWDO policy, a Beneficiary cannot receive financial assistance again until <strong>${eligibleDate}</strong>.
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonColor: '#1A237E',
                    confirmButtonText: 'I Understand',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
                return;
            }

            const isEdit = formEl.id === 'editIntakeForm';
            const actionText = isEdit ? 'Update' : 'Save';

            Swal.fire({
                title: `${actionText} General Intake Sheet?`,
                text: `Are you sure you want to ${actionText.toLowerCase()} this General Intake Sheet record?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#64748B',
                confirmButtonText: `<i class="fas fa-save me-1"></i> Yes, ${actionText} Record`,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formEl.submit();
                }
            });
        });
    }

    // Real-time uppercase conversion for editable user inputs
    document.addEventListener('input', function (e) {
        const el = e.target;
        if (el.matches?.(':is(input[type="text"], input:not([type]), textarea):not([readonly])')) {
            const { selectionStart: s, selectionEnd: end, value } = el;
            if (value !== value.toUpperCase()) {
                el.value = value.toUpperCase();
                el.setSelectionRange?.(s, end);
            }
        }
    });
});
