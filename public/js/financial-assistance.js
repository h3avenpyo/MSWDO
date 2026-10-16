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
    // Reject future birthdates
    if (birthDate > today) return '';

    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return (age >= 0 && age <= 125) ? age : '';
}

function handleBirthdayChange(inputEl, targetAgeId) {
    const ageEl = document.getElementById(targetAgeId);
    if (!ageEl) return;

    if (inputEl.value) {
        const inputDate = new Date(inputEl.value);
        const today = new Date();
        today.setHours(23, 59, 59, 999);
        if (inputDate > today) {
            inputEl.value = '';
            ageEl.value = '';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Bawal ang Petsa sa Hinaharap',
                    text: 'Hindi maaaring pumili ng petsa ng kapanganakan sa hinaharap (future date).',
                    icon: 'warning',
                    confirmButtonColor: '#b11116'
                });
            }
            return;
        }
    }

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
        let val = input.value.trim();
        // Allow user to paste +639XXXXXXXXX or 639XXXXXXXXX and auto-normalize to 09XXXXXXXXX
        if (val.startsWith('+639')) {
            val = '09' + val.substring(4);
        } else if (val.startsWith('+63')) {
            val = '0' + val.substring(3);
        } else if (val.startsWith('639') && val.length >= 12) {
            val = '09' + val.substring(3);
        }
        val = val.replace(/\D/g, '').slice(0, 11);
        input.value = val;
        const len = input.value.length;
        const startsWith09 = input.value.startsWith('09');

        if (counter) {
            counter.textContent = `${len}/11 digits`;
            if (len === 11 && startsWith09) {
                counter.className = 'digit-counter valid';
                if (error) error.classList.add('hidden');
            } else if (len > 0) {
                counter.className = 'digit-counter invalid';
                if (error) {
                    error.classList.remove('hidden');
                    if (len === 11 && !startsWith09) {
                        error.textContent = 'Dapat magsimula sa 09 ang numero ng telepono.';
                    } else {
                        error.textContent = 'Kinakailangan ang 11-digit na numero (09XXXXXXXXX).';
                    }
                }
            } else {
                counter.className = 'digit-counter text-slate-400';
                if (error) error.classList.add('hidden');
            }
        }
    }

    input.addEventListener('input', update);
    input.addEventListener('paste', () => setTimeout(update, 50));
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
                    <input type="text" maxlength="150" name="family_composition[${familyMemberIndex}][name]" placeholder="Hal. Juan Dela Cruz Jr." class="form-input-custom uppercase text-only-input" data-text-only="true">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-6">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Relasyon sa Benepisyaryo <span class="label-sub">(Relationship)</span>
                        </label>
                    </div>
                    <input type="text" maxlength="100" name="family_composition[${familyMemberIndex}][relationship]" placeholder="Hal. Asawa, Anak, Magulang" class="form-input-custom text-only-input" data-text-only="true">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-3">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Edad <span class="label-sub">(Age)</span>
                        </label>
                    </div>
                    <input type="number" min="0" max="125" step="1" inputmode="numeric" name="family_composition[${familyMemberIndex}][age]" placeholder="Edad" class="form-input-custom text-center">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-5">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Trabaho <span class="label-sub">(Occupation)</span>
                        </label>
                    </div>
                    <input type="text" maxlength="150" name="family_composition[${familyMemberIndex}][occupation]" placeholder="Hal. Magsasaka, Vendor, Wala" class="form-input-custom text-only-input" data-text-only="true">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-4">
                <div class="form-field-group">
                    <div class="form-label-wrapper-compact">
                        <label class="form-label-custom">
                            Buwanang Kita <span class="label-sub">(Monthly Income)</span>
                        </label>
                    </div>
                    <input type="text" inputmode="decimal" maxlength="15" name="family_composition[${familyMemberIndex}][salary]" placeholder="0.00" class="form-input-custom salary-comma-input" oninput="handleSalaryInput(this)">
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

function toggleAssistanceTypeOther() {
    const typeSelect = document.getElementById('assistance_type');
    const typeOtherInput = document.getElementById('assistance_type_other_input');
    if (!typeSelect || !typeOtherInput) return;
    if (typeSelect.value === 'Other Assistance Purpose') {
        typeOtherInput.classList.remove('hidden');
        typeOtherInput.focus();
    } else {
        typeOtherInput.classList.add('hidden');
        typeOtherInput.value = '';
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
            title: '<div class="w-full text-white text-left font-extrabold text-base px-6 py-4 -mx-6 -mt-6 rounded-t-xl uppercase tracking-wide" style="background: linear-gradient(135deg, #b11116 0%, #8A0D11 100%);">PAUNAWA</div>',
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
            confirmButtonColor: '#b11116',
            buttonsStyling: true,
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'rounded-xl shadow-2xl p-6 border-0 max-w-md',
                confirmButton: 'bg-[#b11116] hover:bg-[#8A0D11] text-white px-6 py-2 rounded-lg font-semibold text-sm'
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
    togglePurposeOther();
    toggleAssistanceTypeOther();

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

    // Real-time restriction: prevent numeric digits in name fields while preserving Filipino characters, spaces, hyphens, and apostrophes
    const nameInputIds = [
        'beneficiary_first_name',
        'beneficiary_last_name',
        'beneficiary_middle_name',
        'beneficiary_extension_name',
        'rep_first_name',
        'rep_last_name',
        'rep_middle_name',
        'rep_extension_name'
    ];
    nameInputIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function () {
                if (/[0-9]/.test(this.value)) {
                    this.value = this.value.replace(/[0-9]/g, '');
                }
            });
        }
    });

    // Helper to identify text-only fields (Trabaho and Komposisyon ng Pamilya)
    function isTextOnlyField(el) {
        if (!el || !el.matches) return false;
        if (el.matches('.text-only-input, [data-text-only="true"]')) return true;
        const name = el.getAttribute('name') || '';
        if (name === 'beneficiary_occupation' || name === 'rep_occupation') return true;
        if (/^family_composition\[\d+\]\[(name|relationship|occupation)\]$/.test(name)) return true;
        return false;
    }

    // Prevent typing digits 0-9 into text-only fields
    document.addEventListener('keypress', function (e) {
        if (isTextOnlyField(e.target) && /[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });

    // Strip pasted numbers on paste for text-only fields
    document.addEventListener('paste', function (e) {
        const el = e.target;
        if (isTextOnlyField(el)) {
            setTimeout(() => {
                if (el && /[0-9]/.test(el.value)) {
                    el.value = el.value.replace(/[0-9]/g, '');
                }
            }, 10);
        }
    });

    // Strip numbers on input for text-only fields
    document.addEventListener('input', function (e) {
        const el = e.target;
        if (isTextOnlyField(el) && /[0-9]/.test(el.value)) {
            const s = el.selectionStart;
            const originalLen = el.value.length;
            el.value = el.value.replace(/[0-9]/g, '');
            const diff = originalLen - el.value.length;
            if (s !== null) {
                const newPos = Math.max(0, s - diff);
                el.setSelectionRange?.(newPos, newPos);
            }
        }
    });

    // Form submit handler
    const form = document.getElementById('financialIntakeForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate contact numbers
            const benContact = (document.getElementById('beneficiary_contact_number')?.value || '').trim();
            if (!/^09\d{9}$/.test(benContact)) {
                Swal.fire({
                    title: 'Invalid Contact Number',
                    text: 'Numero ng Telepono ng Benepisyaryo must be strictly 11 digits starting with 09 (e.g., 09XXXXXXXXX).',
                    icon: 'warning',
                    confirmButtonColor: '#b11116'
                });
                return;
            }

            const hasRep = document.getElementById('has_representative')?.checked;
            if (hasRep) {
                const repContact = (document.getElementById('rep_contact_number')?.value || '').trim();
                if (!/^09\d{9}$/.test(repContact)) {
                    Swal.fire({
                        title: 'Invalid Representative Contact',
                        text: 'Numero ng Telepono ng Kinatawan must be strictly 11 digits starting with 09 (e.g., 09XXXXXXXXX).',
                        icon: 'warning',
                        confirmButtonColor: '#b11116'
                    });
                    return;
                }
            }

            // Ensure name fields contain legitimate letters (reject numeric or punctuation-only entries)
            const nameChecks = [
                { id: 'beneficiary_first_name', label: 'Unang Pangalan ng Benepisyaryo' },
                { id: 'beneficiary_last_name', label: 'Apelyido ng Benepisyaryo' },
            ];
            if (hasRep) {
                nameChecks.push(
                    { id: 'rep_first_name', label: 'Unang Pangalan ng Kinatawan' },
                    { id: 'rep_last_name', label: 'Apelyido ng Kinatawan' }
                );
            }
            for (const chk of nameChecks) {
                const val = (document.getElementById(chk.id)?.value || '').trim();
                if (val && !/[\p{L}]/u.test(val)) {
                    Swal.fire({
                        title: 'Di-wastong Pangalan',
                        text: `Ang "${chk.label}" ay dapat maglaman ng wastong mga titik (letters).`,
                        icon: 'warning',
                        confirmButtonColor: '#b11116'
                    });
                    return;
                }
            }

            // Validation for text-only fields: Trabaho and Komposisyon ng Pamilya
            const textOnlyErrors = [];
            const checkFieldTextOnly = (inputEl, label) => {
                if (!inputEl) return;
                const val = (inputEl.value || '').trim();
                if (!val) return;
                if (/[0-9]/.test(val)) {
                    textOnlyErrors.push({
                        element: inputEl,
                        message: `Ang "${label}" ay dapat teksto lamang. Hindi pinapayagan ang mga numero.`
                    });
                    inputEl.classList.add('border-rose-500');
                } else if (!/[\p{L}]/u.test(val)) {
                    textOnlyErrors.push({
                        element: inputEl,
                        message: `Ang "${label}" ay dapat maglaman ng wastong mga titik (letters).`
                    });
                    inputEl.classList.add('border-rose-500');
                } else {
                    inputEl.classList.remove('border-rose-500');
                }
            };

            const benOcc = document.getElementById('beneficiary_occupation');
            checkFieldTextOnly(benOcc, 'Trabaho ng Benepisyaryo');

            if (hasRep) {
                const repOcc = document.getElementById('rep_occupation');
                checkFieldTextOnly(repOcc, 'Trabaho ng Kinatawan');
            }

            const familyCards = document.querySelectorAll('#familyMembersContainer .family-member-card');
            familyCards.forEach((card, idx) => {
                const nameInput = card.querySelector('input[name*="[name]"]');
                const relInput = card.querySelector('input[name*="[relationship]"]');
                const occInput = card.querySelector('input[name*="[occupation]"]');

                if (nameInput && nameInput.value.trim()) {
                    checkFieldTextOnly(nameInput, `Pangalan sa Miyembro #${idx + 1}`);
                }
                if (relInput && relInput.value.trim()) {
                    checkFieldTextOnly(relInput, `Relasyon sa Miyembro #${idx + 1}`);
                }
                if (occInput && occInput.value.trim()) {
                    checkFieldTextOnly(occInput, `Trabaho sa Miyembro #${idx + 1}`);
                }
            });

            if (textOnlyErrors.length > 0) {
                const firstErr = textOnlyErrors[0];
                firstErr.element.focus();
                Swal.fire({
                    title: 'Maling Uri ng Impormasyon',
                    text: firstErr.message,
                    icon: 'warning',
                    confirmButtonColor: '#b11116'
                });
                return;
            }

            // Confirm submission
            Swal.fire({
                title: 'Submit Intake Sheet?',
                text: 'Please confirm that all beneficiary identifying details are correct before submitting.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#b11116',
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
                                confirmButtonColor: '#b11116',
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
                                confirmButtonColor: '#b11116'
                            });
                        } else {
                            const intakeLabels = {
                                beneficiary_last_name: 'Apelyido ng Benepisyaryo',
                                beneficiary_first_name: 'Pangalan ng Benepisyaryo',
                                beneficiary_middle_name: 'Gitnang Pangalan',
                                beneficiary_street_address: 'Tirahan / Street Address',
                                beneficiary_birthday: 'Petsa ng Kapanganakan',
                                beneficiary_barangay: 'Barangay',
                                beneficiary_city: 'Munisipalidad',
                                beneficiary_province: 'Probinsya',
                                beneficiary_region: 'Rehiyon',
                                beneficiary_contact_number: 'Numero ng Telepono',
                                beneficiary_age: 'Edad',
                                beneficiary_sex: 'Kasarian',
                                beneficiary_civil_status: 'Katayuang Sibil',
                                beneficiary_occupation: 'Trabaho',
                                beneficiary_monthly_salary: 'Buwanang Sahod',
                                beneficiary_category: 'Kategorya',
                                rep_last_name: 'Apelyido ng Kinatawan',
                                rep_first_name: 'Pangalan ng Kinatawan',
                                rep_middle_name: 'Gitnang Pangalan ng Kinatawan',
                                rep_birthday: 'Petsa ng Kapanganakan ng Kinatawan',
                                rep_contact_number: 'Numero ng Telepono ng Kinatawan',
                                rep_relationship: 'Relasyon sa Benepisyaryo',
                                rep_sex: 'Kasarian ng Kinatawan',
                                rep_civil_status: 'Katayuang Sibil ng Kinatawan',
                                rep_age: 'Edad ng Kinatawan',
                                client_type: 'Uri ng Kliyente',
                                assistance_purpose: 'Layunin ng Tulong',
                                purpose: 'Layunin',
                                documents: 'Mga Dokumento'
                            };
                            const prettify = (key) => key
                                .replace(/^beneficiary_/, '')
                                .replace(/^rep_/, '')
                                .replace(/_/g, ' ')
                                .replace(/\b\w/g, ch => ch.toUpperCase());

                            let errorHtml = '';
                            if (err.errors && typeof err.errors === 'object') {
                                const items = Object.keys(err.errors).map(key => {
                                    const label = intakeLabels[key] || prettify(key);
                                    return `<li>${label}</li>`;
                                });
                                if (items.length) {
                                    errorHtml = `<p class="swal-err-intro">Pakipunan o itama ang mga sumusunod:</p><ul class="swal-err-list">${items.join('')}</ul>`;
                                }
                            }

                            const fallbackMsg = (err && err.message) ? err.message : 'Nagkaroon ng problema sa pagsumite ng inyong aplikasyon. Pakisubukang muli.';
                            Swal.fire({
                                title: 'Hindi Naisumite',
                                html: errorHtml || `<p class="swal-err-intro">${fallbackMsg}</p>`,
                                icon: 'error',
                                confirmButtonColor: '#DC2626',
                                confirmButtonText: 'Subukang Muli'
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
window.toggleAssistanceTypeOther = toggleAssistanceTypeOther;
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
