document.addEventListener('DOMContentLoaded', function() {
    const barangaySelect = document.getElementById('case-designation');
    const caseNumberInput = document.getElementById('case-number');
    
    if (barangaySelect && caseNumberInput) {
        barangaySelect.addEventListener('change', function() {
            const barangayId = this.value;
            
            if (!barangayId) {
                caseNumberInput.value = '';
                return;
            }
            
            // Show loading state
            caseNumberInput.value = 'Generating...';
            
            // Call the API to generate the case number
            fetch(`/api/cases/generate-number/${barangayId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    caseNumberInput.value = data.case_number;
                    // Store the case count in a data attribute for reference if needed
                    caseNumberInput.dataset.caseCount = data.case_count;
                })
                .catch(error => {
                    console.error('Error generating case number:', error);
                    caseNumberInput.value = 'Error generating case number';
                });
        });
    }
});
