/**
 * Financial Assistance Step 2 Printable Payroll JavaScript
 */

/**
 * Triggers the system print dialog for the Legal Landscape payroll document.
 */
function printPayroll() {
    window.print();
}

// Expose globally for backward compatibility
window.printPayroll = printPayroll;

document.addEventListener('DOMContentLoaded', function () {
    // Bind click event to print button
    const printBtn = document.getElementById('btnPrintPayroll');
    if (printBtn) {
        printBtn.addEventListener('click', function (e) {
            e.preventDefault();
            printPayroll();
        });
    }

    // Delegated click listener for any print triggers
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.btn-print-payroll, [data-action="print"]');
        if (trigger) {
            e.preventDefault();
            printPayroll();
        }
    });

    // Keyboard shortcut: Ctrl+P or Cmd+P
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            // Allow default browser print flow, but ensure any custom handling executes
            printPayroll();
        }
    });
});
