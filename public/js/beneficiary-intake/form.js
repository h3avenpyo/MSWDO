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

document.addEventListener('DOMContentLoaded', function() {
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
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();

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
