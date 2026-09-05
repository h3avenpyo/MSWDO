/**
 * Financial Assistance Step 2 All General Intakes JavaScript
 */

/**
 * Populate and display the Quick Preview modal with intake details.
 * @param {Object} intake - The intake record object
 */
function viewIntakeDetails(intake) {
    if (!intake) return;

    // Header info
    const controlNumberHeader = document.getElementById('modalControlNumberHeader');
    if (controlNumberHeader) {
        controlNumberHeader.textContent = 'Control No: ' + (intake.control_number || 'N/A') + ' • Processed: ' + (intake.date_processed ? intake.date_processed.split('T')[0] : 'N/A');
    }

    // Beneficiary details
    const benFullName = [intake.beneficiary_first_name, intake.beneficiary_middle_name, intake.beneficiary_last_name, intake.beneficiary_extension_name].filter(Boolean).join(' ') || 'N/A';
    const benNameEl = document.getElementById('modalBeneficiaryName');
    if (benNameEl) benNameEl.textContent = benFullName;

    const benSexAgeEl = document.getElementById('modalBeneficiarySexAge');
    if (benSexAgeEl) {
        benSexAgeEl.textContent = (intake.beneficiary_sex || '--') + ' / ' + (intake.beneficiary_age ? intake.beneficiary_age + ' yrs' : '--');
    }

    const benBdayEl = document.getElementById('modalBeneficiaryBirthday');
    if (benBdayEl) {
        benBdayEl.textContent = intake.beneficiary_birthday ? intake.beneficiary_birthday.split('T')[0] : '--';
    }

    const benContactEl = document.getElementById('modalBeneficiaryContact');
    if (benContactEl) {
        benContactEl.textContent = intake.beneficiary_contact_number || 'N/A';
    }

    const benAddress = [intake.beneficiary_street_address, intake.beneficiary_barangay, intake.beneficiary_city || 'Silang', intake.beneficiary_province || 'Cavite'].filter(Boolean).join(', ') || 'N/A';
    const benAddressEl = document.getElementById('modalBeneficiaryAddress');
    if (benAddressEl) benAddressEl.textContent = benAddress;

    const benBrgyEl = document.getElementById('modalBeneficiaryBarangay');
    if (benBrgyEl) benBrgyEl.textContent = intake.beneficiary_barangay || 'Silang';

    const benOccEl = document.getElementById('modalBeneficiaryOccupation');
    if (benOccEl) benOccEl.textContent = intake.beneficiary_occupation || 'N/A';

    const benSalEl = document.getElementById('modalBeneficiarySalary');
    if (benSalEl) {
        benSalEl.textContent = intake.beneficiary_monthly_salary ? '₱' + parseFloat(intake.beneficiary_monthly_salary).toLocaleString('en-US', { minimumFractionDigits: 2 }) : 'N/A';
    }

    let categoriesText = intake.beneficiary_category || 'N/A';
    if (Array.isArray(intake.beneficiary_categories) && intake.beneficiary_categories.length > 0) {
        categoriesText = intake.beneficiary_categories.join(', ');
    }
    const benCatEl = document.getElementById('modalBeneficiaryCategory');
    if (benCatEl) benCatEl.textContent = categoriesText;

    // Representative details
    const repCard = document.getElementById('modalRepresentativeCard');
    if (repCard) {
        if (intake.has_representative) {
            repCard.style.display = 'block';
            const repFullName = [intake.rep_first_name, intake.rep_middle_name, intake.rep_last_name, intake.rep_extension_name].filter(Boolean).join(' ') || 'N/A';
            const repNameEl = document.getElementById('modalRepName');
            if (repNameEl) repNameEl.textContent = repFullName;

            const repRelEl = document.getElementById('modalRepRelationship');
            if (repRelEl) repRelEl.textContent = intake.rep_relationship || 'Representative';

            const repSexAgeEl = document.getElementById('modalRepSexAge');
            if (repSexAgeEl) {
                repSexAgeEl.textContent = (intake.rep_sex || '--') + ' / ' + (intake.rep_age ? intake.rep_age + ' yrs' : '--');
            }

            const repContactEl = document.getElementById('modalRepContact');
            if (repContactEl) repContactEl.textContent = intake.rep_contact_number || 'N/A';

            const repAddress = [intake.rep_street_address, intake.rep_barangay, intake.rep_city || 'Silang', intake.rep_province || 'Cavite'].filter(Boolean).join(', ') || 'N/A';
            const repAddressEl = document.getElementById('modalRepAddress');
            if (repAddressEl) repAddressEl.textContent = repAddress;

            const repOccEl = document.getElementById('modalRepOccupation');
            if (repOccEl) repOccEl.textContent = intake.rep_occupation || 'N/A';

            const repSalEl = document.getElementById('modalRepSalary');
            if (repSalEl) {
                repSalEl.textContent = intake.rep_monthly_salary ? '₱' + parseFloat(intake.rep_monthly_salary).toLocaleString('en-US', { minimumFractionDigits: 2 }) : 'N/A';
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
    const medCondEl = document.getElementById('modalMedicalConditions');
    if (medCondEl) medCondEl.textContent = medCond;

    const assistPurposeEl = document.getElementById('modalAssistancePurpose');
    if (assistPurposeEl) {
        assistPurposeEl.textContent = intake.purpose_other || intake.assistance_purpose || intake.purpose || 'N/A';
    }

    const swAssessEl = document.getElementById('modalSocialWorkerAssessment');
    if (swAssessEl) {
        swAssessEl.textContent = intake.social_worker_assessment || 'Assessment completed in Step 1 intake.';
    }

    const recTypeEl = document.getElementById('modalRecommendedType');
    if (recTypeEl) {
        recTypeEl.textContent = intake.recommended_assistance_type || intake.service_provided || 'Financial Assistance';
    }

    const recAmountEl = document.getElementById('modalRecommendedAmount');
    if (recAmountEl) {
        if (intake.recommended_amount) {
            recAmountEl.textContent = '₱' + parseFloat(intake.recommended_amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
        } else {
            recAmountEl.textContent = 'To be assessed';
        }
    }

    // Show modal using Bootstrap Modal API
    const modalEl = document.getElementById('intakeQuickViewModal');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// Expose to window for global invocation and backwards-compatibility
window.viewIntakeDetails = viewIntakeDetails;

document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('allIntakesFilterForm');
    const searchInput = document.getElementById('searchInput');

    // Automatic Search Debounce
    if (filterForm && searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                filterForm.submit();
            }, 600);
        });

        // Focus search input at the end of value if user was actively searching
        if (searchInput.value.trim().length > 0 && document.activeElement !== searchInput) {
            const val = searchInput.value;
            searchInput.focus();
            searchInput.setSelectionRange(val.length, val.length);
        }
    }

    // Automatic filter submission on change for dropdowns and date input
    if (filterForm) {
        const autoSubmitElements = filterForm.querySelectorAll('select[name="barangay"], select[name="category"], input[name="date"], select[name="sort"]');
        autoSubmitElements.forEach(el => {
            el.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }

    // Delegated click handler for intake quick preview buttons
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-view-intake');
        if (btn) {
            const rawData = btn.getAttribute('data-intake');
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
});
