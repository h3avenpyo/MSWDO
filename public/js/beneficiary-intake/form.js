/**
 * Beneficiary Intake Form Handlers (Create & Edit)
 */

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

    const formEl = document.getElementById('intakeForm') || document.getElementById('editIntakeForm');
    if (formEl) {
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!formEl.checkValidity()) {
                formEl.reportValidity();
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
});
