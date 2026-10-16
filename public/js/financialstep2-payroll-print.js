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

    // Back button navigation (Back to Payroll Records / Generator)
    const backBtn = document.getElementById('btnBackPayroll');
    if (backBtn) {
        backBtn.addEventListener('click', function (e) {
            e.preventDefault();

            // 1. If opened from an existing opener tab (Payroll Records or Generator)
            if (window.opener && !window.opener.closed) {
                try {
                    window.opener.focus();
                } catch (err) {}
                window.close();

                // Fallback if browser restricted window.close()
                setTimeout(function () {
                    if (!window.closed) {
                        window.location.href = backBtn.getAttribute('href');
                    }
                }, 250);
                return;
            }

            // 2. Try closing this tab if it was opened as a separate window/tab
            window.close();

            // 3. If window is still open (e.g., opened directly or in same tab), return smoothly
            setTimeout(function () {
                if (!window.closed) {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.location.href = backBtn.getAttribute('href');
                    }
                }
            }, 250);
        });
    }
});
