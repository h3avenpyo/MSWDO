/**
 * Unsaved changes guard for case creation, editing, referral, and monitoring forms.
 * Prompts user before exiting if they have unsaved work.
 */
(() => {
    let isDirty = false;
    let isSubmitting = false;
    let bypassPrompt = false;
    let historyGuarded = false;

    const pushHistoryGuard = () => {
        if (!historyGuarded && window.history && typeof window.history.pushState === 'function') {
            historyGuarded = true;
            window.history.pushState({ unsavedGuard: true }, '', window.location.href);
        }
    };

    // Track user input in forms
    const markDirty = (e) => {
        if (bypassPrompt || isSubmitting) return;
        const target = e.target;
        if (!target) return;
        
        // Only track inputs inside forms or editable elements
        const form = target.closest('form');
        if (!form) return;

        // Ignore GET forms (such as search and filter bars)
        if (form.method && form.method.toUpperCase() === 'GET') return;
        if (form.id === 'case-profiles-filter-form' || form.matches('[id*="filter"], [class*="filter"]')) return;

        // Ignore read-only, disabled, hidden, search, age, or selectAll inputs
        if (target.readOnly || target.disabled || target.type === 'hidden') return;
        if (target.id === 'search' || target.name === 'search' || target.name === 'age' || target.classList.contains('search-input')) return;
        if (target.type === 'checkbox' && (target.id === 'selectAll' || target.classList.contains('select-all'))) return;

        isDirty = true;
        pushHistoryGuard();
    };

    document.addEventListener('input', markDirty, true);
    document.addEventListener('change', markDirty, true);

    // When any non-GET form submits, clear dirty and mark submitting
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form && form.method && form.method.toUpperCase() === 'GET') return;
        isSubmitting = true;
        isDirty = false;
        bypassPrompt = true;
    }, true);

    // Automatically hook window.Swal to clear dirty state whenever a success alert is shown
    const hookSwal = () => {
        if (window.Swal && typeof window.Swal.fire === 'function' && !window.Swal._unsavedHooked) {
            window.Swal._unsavedHooked = true;
            const origFire = window.Swal.fire;
            window.Swal.fire = function(...args) {
                const opt = args[0];
                const isSuccess = opt && opt.title !== 'Unsaved Changes' && (
                    opt.icon === 'success' ||
                    opt === 'success' ||
                    (typeof opt.title === 'string' && /success|created|saved|updated|restored|deleted|submitted/i.test(opt.title)) ||
                    (typeof opt.text === 'string' && /success|created|saved|updated|restored|deleted|submitted/i.test(opt.text)) ||
                    (typeof opt.html === 'string' && /success|created|saved|updated|restored|deleted|submitted/i.test(opt.html))
                );
                if (isSuccess) {
                    isDirty = false;
                    isSubmitting = true;
                    bypassPrompt = true;
                }
                return origFire.apply(this, args);
            };
        }
    };
    hookSwal();
    document.addEventListener('DOMContentLoaded', hookSwal);

    // Browser navigation / close tab prompt
    window.addEventListener('beforeunload', (e) => {
        if (isSubmitting || bypassPrompt || !isDirty) return;
        if (document.querySelector('.swal2-icon-success, .swal2-success, .swal2-loading')) {
            isDirty = false;
            return;
        }
        e.preventDefault();
        e.returnValue = '';
        return '';
    });

    const confirmExit = (onConfirm, onCancel) => {
        if (!isDirty || isSubmitting || bypassPrompt) {
            if (typeof onConfirm === 'function') onConfirm();
            return;
        }

        const swalOpts = {
            title: 'Unsaved Changes',
            text: 'Are you sure you want to exit? You will lose your unsaved work.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C41E3A',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, exit',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: true,
            allowEscapeKey: true
        };

        if (window.Swal) {
            window.Swal.fire(swalOpts).then((result) => {
                if (result.isConfirmed) {
                    isDirty = false;
                    bypassPrompt = true;
                    if (typeof onConfirm === 'function') onConfirm();
                } else {
                    if (typeof onCancel === 'function') onCancel();
                }
            });
        } else {
            if (window.confirm('Are you sure you want to exit? You will lose your unsaved work.')) {
                isDirty = false;
                bypassPrompt = true;
                if (typeof onConfirm === 'function') onConfirm();
            } else {
                if (typeof onCancel === 'function') onCancel();
            }
        }
    };

    // Handle browser Back / Forward buttons
    window.addEventListener('popstate', () => {
        if (isDirty && !isSubmitting && !bypassPrompt) {
            historyGuarded = false;
            confirmExit(
                () => {
                    isDirty = false;
                    bypassPrompt = true;
                    window.history.back();
                },
                () => {
                    pushHistoryGuard();
                }
            );
        }
    });

    const closeAllOpenModals = () => {
        window.dispatchEvent(new CustomEvent('close-edit-modal'));
        if (typeof window.closeEditModal === 'function') {
            try { window.closeEditModal(); } catch (err) {}
        }
        const editModal = document.getElementById('editCaseModal');
        if (editModal) {
            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try { window.Alpine.$data(editModal).open = false; } catch (err) {}
            }
            if (editModal._x_dataStack && editModal._x_dataStack[0]) {
                try { editModal._x_dataStack[0].open = false; } catch (err) {}
            }
            const modalContent = editModal.querySelector('.fixed.inset-0') || editModal;
            if (modalContent) modalContent.style.display = 'none';
        }
    };

    // Keyboard ESC key to close modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isDirty && !isSubmitting && !bypassPrompt) {
            const editModal = document.getElementById('editCaseModal');
            const isModalOpen = editModal && (
                (editModal._x_dataStack && editModal._x_dataStack[0] && editModal._x_dataStack[0].open) ||
                (window.Alpine && window.Alpine.$data && window.Alpine.$data(editModal)?.open)
            );
            if (isModalOpen) {
                e.preventDefault();
                e.stopPropagation();
                confirmExit(() => {
                    closeAllOpenModals();
                });
            }
        }
    }, true);

    // Intercept clicks on links, back buttons, cancel buttons, modal close (X) buttons, and backdrops
    document.addEventListener('click', (e) => {
        // ALWAYS ignore clicks inside SweetAlert2 modals so Cancel, Confirm, and Close buttons work naturally!
        if (e.target.closest('.swal2-container, .swal2-popup, [class*="swal2-"]')) {
            if (document.querySelector('.swal2-icon-success, .swal2-success')) {
                isDirty = false;
                isSubmitting = true;
                bypassPrompt = true;
            }
            return;
        }

        if (!isDirty || isSubmitting || bypassPrompt) return;

        // Check if clicking modal backdrop on #editCaseModal
        const editModal = document.getElementById('editCaseModal');
        const isModalOpen = editModal && (
            (editModal._x_dataStack && editModal._x_dataStack[0] && editModal._x_dataStack[0].open) ||
            (window.Alpine && window.Alpine.$data && window.Alpine.$data(editModal)?.open)
        );
        if (isModalOpen) {
            const backdrop = editModal.querySelector('.fixed.inset-0');
            if (e.target === backdrop) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                confirmExit(() => {
                    closeAllOpenModals();
                });
                return;
            }
        }

        const btn = e.target.closest('button, a');
        if (!btn) return;

        // Specific modal cancel controls can opt out of this global guard when
        // their form is intentionally designed to close without confirmation.
        if (btn.matches('[data-skip-unsaved-guard]')) {
            isDirty = false;
            bypassPrompt = true;
            setTimeout(() => { bypassPrompt = false; }, 300);
            return;
        }

        // Skip submit buttons
        if (btn.type === 'submit' || btn.closest('button[type="submit"]')) return;

        // Skip clear buttons/links
        const btnText = (btn.textContent || '').trim().toLowerCase();
        if (btnText === 'clear' || btn.matches('.clear-btn, [data-clear]') || btn.closest('form[method="GET"], form[method="get"]')) {
            isDirty = false;
            return;
        }
        const hasTimesIcon = !!btn.querySelector('.fa-times, .fa-xmark');
        const hasArrowLeftIcon = !!btn.querySelector('.fa-arrow-left, .fa-chevron-left');
        const isBackBtn = btn.matches('a[href*="javascript:history.back"], a[href*="history.back"], #back-button, .back-btn, [data-back]')
            || hasArrowLeftIcon
            || btnText.startsWith('back')
            || btnText.includes('back to');
        const isCloseModalBtn = btn.matches('[data-close-modal], .close-modal, .btn-close, [data-bs-dismiss="modal"]')
            || hasTimesIcon
            || (btn.getAttribute('@click') && btn.getAttribute('@click').includes('open = false'));
        const isCancelBtn = btn.id === 'cancel-edit-button' || btn.classList.contains('btn-cancel') || btnText === 'cancel' || btnText.includes('cancel');
        const isRealLink = btn.tagName === 'A' && btn.href && !btn.href.startsWith('javascript:') && !btn.href.endsWith('#');

        if (isRealLink && (isCancelBtn || isBackBtn || isRealLink)) {
            // It's a link (such as Cancel link or Back link or navigation link) that navigates to a URL
            const targetHref = btn.href;
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            confirmExit(() => {
                isDirty = false;
                bypassPrompt = true;
                if (window.location.href === targetHref || window.location.href.split('#')[0] === targetHref.split('#')[0]) {
                    window.location.assign(targetHref);
                    window.location.reload();
                } else {
                    window.location.assign(targetHref);
                }
            });
            return;
        }

        if (isBackBtn) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            confirmExit(() => {
                isDirty = false;
                bypassPrompt = true;
                if (btn.href && !btn.href.includes('history.back') && !btn.href.startsWith('javascript:') && !btn.href.endsWith('#')) {
                    window.location.assign(btn.href);
                } else if (document.referrer && document.referrer !== window.location.href) {
                    window.location.assign(document.referrer);
                } else {
                    window.history.back();
                }
            });
            return;
        }

        if (isCloseModalBtn || isCancelBtn) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            confirmExit(() => {
                closeAllOpenModals();
                isDirty = false;
                bypassPrompt = true;
                if (btn.tagName === 'A' && btn.href) {
                    window.location.assign(btn.href);
                } else {
                    btn.click();
                    setTimeout(() => { bypassPrompt = false; }, 300);
                }
            });
            return;
        }
    }, true);

    // Provide manual reset / trigger API on window
    window.portalUnsavedChanges = {
        setDirty: (dirty = true) => {
            isDirty = dirty;
            if (dirty) pushHistoryGuard();
        },
        isDirty: () => isDirty,
        reset: (bypass = true) => { isDirty = false; isSubmitting = false; bypassPrompt = bypass; historyGuarded = false; },
        confirmExit: confirmExit
    };
})();
