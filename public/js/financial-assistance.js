/**
 * ==========================================================================
 * MSWDO Silang - Financial Assistance Client Intake Scripts
 * ==========================================================================
 */

// Helper to get CSRF token
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// =====================================
// AGE COMPUTATION
// =====================================
function calculateAge(birthdayStr) {
    if (!birthdayStr) return '';
    const birthDate = new Date(birthdayStr);
    if (isNaN(birthDate.getTime())) return '';
    
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age >= 0 ? age : 0;
}

function handleBirthdayChange(inputEl, targetAgeId) {
    const ageEl = document.getElementById(targetAgeId);
    if (!ageEl) return;
    const age = calculateAge(inputEl.value);
    ageEl.value = age !== '' ? age : '';
}

// =====================================
// CONTACT NUMBER COUNTER & VALIDATION
// =====================================
function setupContactInput(inputId, counterId, errorId) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const error = document.getElementById(errorId);
    if (!input) return;

    function update() {
        // Strip non-digits
        input.value = input.value.replace(/[^0-9]/g, '').slice(0, 11);
        const len = input.value.length;
        
        if (counter) {
            counter.textContent = `${len}/11 digits`;
            if (len === 11) {
                counter.className = 'digit-counter valid';
                if (error) error.classList.add('hidden');
            } else if (len > 0) {
                counter.className = 'digit-counter invalid';
                if (error) error.classList.remove('hidden');
            } else {
                counter.className = 'digit-counter text-slate-400';
                if (error) error.classList.add('hidden');
            }
        }
    }

    input.addEventListener('input', update);
    update();
}

// =====================================
// REPRESENTATIVE TOGGLE
// =====================================
function toggleRepresentative(enable) {
    const repCard = document.getElementById('representativeCard');
    const switchContainer = document.getElementById('repSwitchContainer');
    const switchInput = document.getElementById('has_representative');
    if (!repCard || !switchInput) return;

    switchInput.checked = enable;
    if (switchContainer) {
        if (enable) {
            switchContainer.classList.add('active');
        } else {
            switchContainer.classList.remove('active');
        }
    }

    const repFields = repCard.querySelectorAll('.rep-field');
    const repStars = repCard.querySelectorAll('.rep-star');

    if (enable) {
        repCard.classList.remove('hidden');
        repFields.forEach(f => {
            if (f.dataset.requiredIfRep === 'true') {
                f.required = true;
            }
        });
        repStars.forEach(s => s.classList.remove('hidden'));
    } else {
        repCard.classList.add('hidden');
        repFields.forEach(f => {
            f.required = false;
        });
        repStars.forEach(s => s.classList.add('hidden'));
    }
}

// =====================================
// DYNAMIC FAMILY COMPOSITION (FORM BLOCKS)
// =====================================
let familyMemberIndex = 1;

function updateFamilyMemberNumbers() {
    const container = document.getElementById('familyMembersContainer');
    const emptyState = document.getElementById('familyEmptyState');
    if (!container) return;
    const cards = container.querySelectorAll('.family-member-card');
    
    if (emptyState) {
        if (cards.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    cards.forEach((card, idx) => {
        const badge = card.querySelector('.family-member-badge');
        if (badge) {
            badge.textContent = `Miyembro #${idx + 1}`;
        }
    });

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function addFamilyMember() {
    const container = document.getElementById('familyMembersContainer');
    if (!container) return;

    const card = document.createElement('div');
    card.className = 'family-member-card';
    card.dataset.index = familyMemberIndex;
    card.innerHTML = `
        <div class="family-member-header">
            <div class="flex items-center gap-2">
                <span class="family-member-badge">Miyembro</span>
                <span class="text-xs text-slate-500 font-medium hidden sm:inline">(Kasamahan sa Tahanan)</span>
            </div>
            <button type="button" class="family-remove-btn" onclick="removeFamilyMember(this)" title="Alisin ang miyembrong ito">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Alisin</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-3">
            <div class="col-span-12 sm:col-span-6">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Buong Pangalan <span class="label-sub">(Full Name)</span>
                        </label>
                    </div>
                    <input type="text" name="family_composition[${familyMemberIndex}][name]" placeholder="Hal. Juan Dela Cruz Jr." class="form-input-custom uppercase">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-6">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Relasyon sa Benepisyaryo <span class="label-sub">(Relationship)</span>
                        </label>
                    </div>
                    <input type="text" name="family_composition[${familyMemberIndex}][relationship]" placeholder="Hal. Asawa, Anak, Magulang" class="form-input-custom">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-3">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Edad <span class="label-sub">(Age)</span>
                        </label>
                    </div>
                    <input type="number" min="0" max="120" name="family_composition[${familyMemberIndex}][age]" placeholder="Edad" class="form-input-custom text-center">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-5">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Trabaho <span class="label-sub">(Occupation)</span>
                        </label>
                    </div>
                    <input type="text" name="family_composition[${familyMemberIndex}][occupation]" placeholder="Hal. Magsasaka, Vendor, Wala" class="form-input-custom">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-4">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Buwanang Kita <span class="label-sub">(Monthly Income)</span>
                        </label>
                    </div>
                    <input type="text" inputmode="decimal" name="family_composition[${familyMemberIndex}][salary]" placeholder="0.00" class="form-input-custom salary-comma-input" oninput="handleSalaryInput(this)">
                </div>
            </div>
        </div>
    `;

    container.appendChild(card);
    familyMemberIndex++;
    updateFamilyMemberNumbers();

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    const firstInput = card.querySelector('input');
    if (firstInput) {
        firstInput.focus();
    }
}

function removeFamilyMember(buttonEl) {
    const card = buttonEl.closest('.family-member-card');
    if (card) {
        card.remove();
        updateFamilyMemberNumbers();
    }
}

// Aliases for backwards compatibility
function addFamilyRow() {
    addFamilyMember();
}

function removeFamilyRow(buttonEl) {
    removeFamilyMember(buttonEl);
}

// =====================================
// OTHER SPECIFY TOGGLES
// =====================================
function toggleCategoryOther() {
    const catOtherCheckbox = document.getElementById('cat_others');
    const otherInput = document.getElementById('beneficiary_category_other_input');
    if (!catOtherCheckbox || !otherInput) return;
    if (catOtherCheckbox.checked) {
        otherInput.classList.remove('hidden');
        otherInput.focus();
    } else {
        otherInput.classList.add('hidden');
        otherInput.value = '';
    }
}

function togglePurposeOther() {
    const purposeSelect = document.getElementById('assistance_purpose');
    const purposeOtherInput = document.getElementById('purpose_other_input');
    if (!purposeSelect || !purposeOtherInput) return;
    if (purposeSelect.value === 'Other Medical Conditions') {
        purposeOtherInput.classList.remove('hidden');
        purposeOtherInput.focus();
    } else {
        purposeOtherInput.classList.add('hidden');
        purposeOtherInput.value = '';
    }
}

// =====================================
// REAL-TIME 6-MONTH DUPLICATE CHECK
// =====================================
let duplicateCheckTimeout = null;

function triggerDuplicateCheck() {
    clearTimeout(duplicateCheckTimeout);
    duplicateCheckTimeout = setTimeout(() => {
        const fName = document.getElementById('beneficiary_first_name')?.value?.trim() || '';
        const lName = document.getElementById('beneficiary_last_name')?.value?.trim() || '';
        const mName = document.getElementById('beneficiary_middle_name')?.value?.trim() || '';
        const bDay = document.getElementById('beneficiary_birthday')?.value || '';

        if (fName.length < 2 || lName.length < 2) {
            hideDuplicateAlert();
            return;
        }

        const payload = {
            beneficiary_first_name: fName,
            beneficiary_last_name: lName,
            beneficiary_middle_name: mName,
            beneficiary_birthday: bDay,
            has_representative: document.getElementById('has_representative')?.checked ? 1 : 0,
            rep_first_name: document.getElementById('rep_first_name')?.value?.trim() || '',
            rep_last_name: document.getElementById('rep_last_name')?.value?.trim() || '',
            rep_birthday: document.getElementById('rep_birthday')?.value || '',
        };

        fetch('/financial-assistance/check-duplicate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.is_duplicate) {
                showDuplicateAlert(data.warning_message, data.matches);
            } else {
                hideDuplicateAlert();
            }
        })
        .catch(err => {
            console.error('Duplicate check error:', err);
        });
    }, 500);
}

function showDuplicateAlert(message, matches) {
    const alertBox = document.getElementById('duplicateAlertBox');
    const messageEl = document.getElementById('duplicateAlertMessage');
    const dateEl = document.getElementById('duplicateEligibleDate');
    if (!alertBox) return;

    if (dateEl && Array.isArray(matches) && matches.length > 0) {
        dateEl.textContent = matches[0].eligible_again_date || 'N/A';
    }

    if (messageEl) {
        messageEl.textContent = message || 'Beneficiary has already received financial assistance within the 6-month validity policy.';
    }

    alertBox.classList.remove('hidden');
}

function hideDuplicateAlert() {
    const alertBox = document.getElementById('duplicateAlertBox');
    if (alertBox) alertBox.classList.add('hidden');
}

// =====================================
// ADVISORY MODAL (POPUP ON LOAD / REDIRECT)
// =====================================
function showAdvisoryModal() {
    const modal = document.getElementById('applicantAdvisoryModal');
    if (modal) {
        modal.classList.add('active');
        document.body.classList.add('modal-open');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        return;
    }

    // Fallback to SweetAlert2 if custom DOM modal is not present
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '<div class="w-full bg-[#192534] text-white text-left font-extrabold text-base px-6 py-4 -mx-6 -mt-6 rounded-t-xl uppercase tracking-wide">PAUNAWA</div>',
            html: `
                <div class="text-left text-slate-700 text-sm sm:text-[15px] leading-relaxed space-y-4 pt-4">
                    <p>
                        1. Punan nang wasto at kumpleto ang lahat ng kinakailangang impormasyon na may <span class="text-rose-600 font-bold">pulang asterisk (*)</span>.
                    </p>
                    <p>
                        2. <strong class="text-amber-600 font-bold">6-Month Policy Restriction:</strong> Ang tulong pinansyal ay may <strong class="text-amber-600 font-bold">6-month validity period</strong> bawat benepisyaryo alinsunod sa patakaran ng MSWDO Silang.
                    </p>
                    <p>
                        3. Ihanda ang mga <strong class="text-amber-600 font-bold">sumusuportang dokumento</strong> (Valid ID, Barangay Certificate of Indigency, Medical Abstract/Certificate o Hospital Bill) kapag pupunta sa tanggapan.
                    </p>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Close',
            confirmButtonColor: '#0c2340',
            buttonsStyling: true,
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'rounded-xl shadow-2xl p-6 border-0 max-w-md',
                confirmButton: 'bg-[#0c2340] text-white px-6 py-2 rounded-lg font-semibold text-sm'
            }
        });
    }
}

function closeAdvisoryModal() {
    const modal = document.getElementById('applicantAdvisoryModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.classList.remove('modal-open');
    }
}

function setupAdvisoryModalListeners() {
    const modal = document.getElementById('applicantAdvisoryModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeAdvisoryModal();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAdvisoryModal();
        }
    });
}

// =====================================
// SALARY COMMA FORMATTING HELPERS
// =====================================
function formatNumberWithCommas(value) {
    if (value === null || value === undefined) return '';
    let str = value.toString();

    // Keep only digits and decimal point (strip existing commas and non-numeric chars)
    str = str.replace(/[^\d.]/g, '');

    // Allow only one decimal point
    const parts = str.split('.');
    let integerPart = parts[0];
    let decimalPart = parts.length > 1 ? '.' + parts.slice(1).join('') : '';

    // If empty
    if (integerPart === '' && decimalPart === '') return '';

    // Remove leading zeros if more than 1 digit before decimal
    if (integerPart.length > 1) {
        integerPart = integerPart.replace(/^0+/, '') || '0';
    }

    // Limit decimal places to 2
    if (decimalPart.length > 3) {
        decimalPart = decimalPart.substring(0, 3);
    }

    // Format integer part with thousands separators
    const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return (integerPart === '' && decimalPart !== '' ? '0' : formattedInteger) + decimalPart;
}

function handleSalaryInput(input) {
    const originalValue = input.value;
    const cursorPosition = input.selectionStart || 0;
    
    // Count how many numeric/decimal characters were before the cursor
    const rawBeforeCursor = originalValue.slice(0, cursorPosition).replace(/[^\d.]/g, '');
    const numCharsBeforeCursor = rawBeforeCursor.length;

    const formatted = formatNumberWithCommas(originalValue);
    input.value = formatted;

    // Calculate new cursor position preserving user's edit point
    let newCursorPos = 0;
    let countedChars = 0;
    for (let i = 0; i < formatted.length; i++) {
        if (/[\d.]/.test(formatted[i])) {
            countedChars++;
        }
        if (countedChars <= numCharsBeforeCursor) {
            newCursorPos = i + 1;
        }
    }
    
    if (input.setSelectionRange) {
        input.setSelectionRange(newCursorPos, newCursorPos);
    }
}

// Delegation listener for salary inputs
document.addEventListener('input', function (e) {
    const el = e.target;
    if (el.matches?.('.salary-comma-input')) {
        handleSalaryInput(el);
    }
});

// =====================================
// FORM SUBMISSION & FEEDBACK
// =====================================
function initFinancialIntakeForm() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Format any initial/restored salary inputs
    document.querySelectorAll('.salary-comma-input').forEach(input => {
        if (input.value) {
            input.value = formatNumberWithCommas(input.value);
        }
    });

    // Set up modal listeners & automatically display popup upon entering / redirecting
    setupAdvisoryModalListeners();
    showAdvisoryModal();

    // Contact number watchers
    setupContactInput('beneficiary_contact_number', 'beneficiary_contact_counter', 'beneficiary_contact_error');
    setupContactInput('rep_contact_number', 'rep_contact_counter', 'rep_contact_error');

    // Birthday watchers
    const benBday = document.getElementById('beneficiary_birthday');
    if (benBday) {
        benBday.addEventListener('change', () => {
            handleBirthdayChange(benBday, 'beneficiary_age');
            triggerDuplicateCheck();
        });
    }

    const repBday = document.getElementById('rep_birthday');
    if (repBday) {
        repBday.addEventListener('change', () => {
            handleBirthdayChange(repBday, 'rep_age');
            triggerDuplicateCheck();
        });
    }

    // Name watchers for duplicate checker
    ['beneficiary_first_name', 'beneficiary_last_name', 'beneficiary_middle_name'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('blur', triggerDuplicateCheck);
            el.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') triggerDuplicateCheck();
            });
        }
    });

    // Form submit handler
    const form = document.getElementById('financialIntakeForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate contact numbers
            const benContact = document.getElementById('beneficiary_contact_number')?.value || '';
            if (benContact.length !== 11) {
                Swal.fire({
                    title: 'Invalid Contact Number',
                    text: 'Numero ng Telepono ng Benepisyaryo must be strictly 11 digits (e.g., 09XXXXXXXXX).',
                    icon: 'warning',
                    confirmButtonColor: '#1A237E'
                });
                return;
            }

            const hasRep = document.getElementById('has_representative')?.checked;
            if (hasRep) {
                const repContact = document.getElementById('rep_contact_number')?.value || '';
                if (repContact.length !== 11) {
                    Swal.fire({
                        title: 'Invalid Representative Contact',
                        text: 'Numero ng Telepono ng Kinatawan must be strictly 11 digits (e.g., 09XXXXXXXXX).',
                        icon: 'warning',
                        confirmButtonColor: '#1A237E'
                    });
                    return;
                }
            }

            // Confirm submission
            Swal.fire({
                title: 'Submit Intake Sheet?',
                text: 'Please confirm that all beneficiary identifying details are correct before submitting.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Yes, Submit Application',
                cancelButtonText: 'Review Form'
            }).then((result) => {
                if (!result.isConfirmed) return;

                // Disable submit button during request
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Submitting Intake...</span>
                    `;
                }

                // Strip commas from salary inputs before building FormData
                form.querySelectorAll('.salary-comma-input').forEach(input => {
                    if (input.value) {
                        input.value = input.value.replace(/,/g, '');
                    }
                });

                const formData = new FormData(form);

                fetch('/financial-assistance', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                })
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Aplikasyon Naisumite!',
                            html: `
                                <div class="text-left p-2">
                                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 text-center">
                                        <p class="text-xs uppercase tracking-wider text-amber-700 font-bold mb-1">Katayuan ng Aplikasyon</p>
                                        <p class="text-xl sm:text-2xl font-extrabold text-amber-800 tracking-tight">For Review (Pending)</p>
                                        <p class="text-xs font-semibold text-slate-600 mt-1">Reference No.: ${data.reference_number || ('#ONLINE-' + (data.data?.id || ''))}</p>
                                    </div>
                                    <div class="space-y-2 text-sm text-slate-700 border-t border-slate-100 pt-3">
                                        <p><strong>Benepisyaryo:</strong> ${data.beneficiary_name || 'N/A'}</p>
                                        <p><strong>Barangay:</strong> ${data.data?.barangay || 'Silang, Cavite'}</p>
                                        <p><strong>Layunin:</strong> ${data.data?.purpose || 'General Assistance'}</p>
                                        <p><strong>Petsa ng Pagsumite:</strong> ${data.data?.date_submitted || 'Ngayong Araw'}</p>
                                    </div>
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-slate-700 leading-relaxed">
                                        <strong class="text-primary">Mahalagang Paalala:</strong> Ang inyong aplikasyon ay dadaan muna sa pagsusuri ng MSWDO Staff. Ang <strong>Opisyal na Control Number</strong> ay ipagkakaloob kapag opisyal nang tinanggap (accepted) ang inyong aplikasyon.
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#1A237E',
                            confirmButtonText: 'Bumalik sa Home',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = '/';
                        });
                    }
                })
                .catch((err) => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = `
                            <i data-lucide="send" class="w-5 h-5 mr-2 inline-block"></i>
                            <span>I-sumite ang Intake Sheet (Submit Application)</span>
                        `;
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    }

                    // Re-apply comma formatting to salary inputs on submission failure
                    form.querySelectorAll('.salary-comma-input').forEach(input => {
                        if (input.value) {
                            input.value = formatNumberWithCommas(input.value);
                        }
                    });

                    if (err.is_duplicate) {
                        showDuplicateAlert(err.message, err.matches);
                        Swal.fire({
                            title: '6-Month Policy Restriction',
                            text: err.message || 'Beneficiary has already received financial assistance within the last 6 months.',
                            icon: 'warning',
                            confirmButtonColor: '#1A237E'
                        });
                    } else {
                        let errorMsg = err.message || 'There was an error submitting your intake. Please check your form.';
                        if (err.errors) {
                            errorMsg = Object.values(err.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            title: 'Submission Error',
                            html: `<div class="text-left text-sm text-rose-700">${errorMsg}</div>`,
                            icon: 'error',
                            confirmButtonColor: '#DC2626'
                        });
                    }
                });
            });
        });
    }
}

// Global functions for inline actions
window.calculateAge = calculateAge;
window.handleBirthdayChange = handleBirthdayChange;
window.toggleRepresentative = toggleRepresentative;
window.addFamilyRow = addFamilyRow;
window.removeFamilyRow = removeFamilyRow;
window.toggleCategoryOther = toggleCategoryOther;
window.togglePurposeOther = togglePurposeOther;
window.triggerDuplicateCheck = triggerDuplicateCheck;
window.closeAdvisoryModal = closeAdvisoryModal;
window.formatNumberWithCommas = formatNumberWithCommas;
window.handleSalaryInput = handleSalaryInput;

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFinancialIntakeForm);
} else {
    initFinancialIntakeForm();
}
