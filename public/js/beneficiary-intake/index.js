/**
 * Beneficiary Intake Masterlist Scripts
 */

document.addEventListener('DOMContentLoaded', function() {
    // Delete Intake Dialog
    const deleteButtons = document.querySelectorAll('.btn-delete-intake');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('.delete-intake-form');
            if (!form) return;

            Swal.fire({
                title: 'Delete Intake Sheet?',
                text: "This action will permanently delete this intake record.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Yes, Delete It',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
