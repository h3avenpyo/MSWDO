/**
 * Shared unsaved-changes protection for portal forms and injected edit forms.
 */
(() => {
    const baselines = new Map();
    const visibility = new Map();
    const pendingForms = new Map();
    let manualDirty = false;
    let isSubmitting = false;
    let bypassPrompt = false;
    let confirmation = null;

    // Several AJAX editors omit method; only explicit GET/filter forms are ignored.
    const isEditableForm = form => form && (form.method.toUpperCase() !== 'GET' || !form.hasAttribute('method'))
        && !form.matches('[data-unsaved-ignore], [id*="filter"], [class*="filter"]');
    const snapshot = form => JSON.stringify(Array.from(form.elements).filter(field => {
        if (!field.matches('input, select, textarea') || field.disabled || field.readOnly) return false;
        if (['hidden', 'submit', 'button', 'reset', 'image'].includes(field.type)) return false;
        return field.id !== 'selectAll' && !field.classList.contains('select-all');
    }).map((field, index) => {
        let value = field.value;
        if (field.type === 'checkbox' || field.type === 'radio') value = [field.value, field.checked];
        else if (field.type === 'file') value = Array.from(field.files || [], file => [file.name, file.size, file.lastModified]);
        else if (field.multiple) value = Array.from(field.selectedOptions, option => option.value);
        return [field.name || field.id || index, value];
    }));
    const isVisible = form => typeof form.getClientRects !== 'function' || form.getClientRects().length > 0;
    const registerForm = form => {
        if (!isEditableForm(form)) return;
        const visible = isVisible(form);
        if (!baselines.has(form) || (visible && !visibility.get(form))) baselines.set(form, snapshot(form));
        visibility.set(form, visible);
    };
    const registerForms = () => document.querySelectorAll('form').forEach(registerForm);
    const hasUnsavedChanges = () => {
        registerForms();
        let dirty = manualDirty;
        for (const [form, baseline] of baselines) {
            if (!form.isConnected) {
                baselines.delete(form);
                visibility.delete(form);
                pendingForms.delete(form);
            } else if (isVisible(form) && snapshot(form) !== baseline) dirty = true;
        }
        return dirty;
    };
    const reset = () => {
        registerForms();
        baselines.forEach((value, form) => baselines.set(form, snapshot(form)));
        manualDirty = false;
        isSubmitting = false;
        pendingForms.clear();
    };
    const markSaved = form => {
        if (isEditableForm(form)) baselines.set(form, pendingForms.get(form) ?? snapshot(form));
        pendingForms.delete(form);
        manualDirty = false;
        isSubmitting = false;
    };

    // Initialize after the page's own defaults, and register AJAX forms before editing.
    const initialize = () => {
        registerForms();
        if (typeof MutationObserver !== 'undefined') {
            new MutationObserver(registerForms).observe(document.documentElement, {
                childList: true, subtree: true, attributes: true,
                attributeFilter: ['class', 'style', 'open', 'hidden'],
            });
        }
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => queueMicrotask(initialize));
    else initialize();
    ['focusin', 'beforeinput', 'pointerdown'].forEach(event => {
        document.addEventListener(event, e => registerForm(e.target.closest('form')), true);
    });
    const onEdit = e => {
        if (!isEditableForm(e.target.closest('form'))) return;
        registerForm(e.target.closest('form'));
        isSubmitting = false;
        bypassPrompt = false;
    };
    document.addEventListener('input', onEdit, true);
    document.addEventListener('change', onEdit, true);
    document.addEventListener('reset', e => {
        if (!isEditableForm(e.target)) return;
        queueMicrotask(() => {
            baselines.set(e.target, snapshot(e.target));
            manualDirty = false;
        });
    }, true);

    // A Save attempt does not clear edits. Only an unhandled native submission
    // bypasses beforeunload; AJAX validation/failure keeps the original baseline.
    document.addEventListener('submit', e => {
        if (isEditableForm(e.target)) pendingForms.set(e.target, snapshot(e.target));
    }, true);
    document.addEventListener('submit', e => {
        if (isEditableForm(e.target) && !e.defaultPrevented) isSubmitting = true;
    });

    // Existing AJAX modules signal successful writes via reset(), markSaved(),
    // or an explicit success result following a form submission.
    const hookSwal = () => {
        if (!window.Swal || typeof window.Swal.fire !== 'function' || window.Swal._unsavedHooked) return;
        window.Swal._unsavedHooked = true;
        const original = window.Swal.fire;
        window.Swal.fire = function(...args) {
            const options = args[0];
            const icon = typeof options === 'object' ? options?.icon : args[2];
            if (icon === 'success') Array.from(pendingForms.keys()).forEach(markSaved);
            else if (icon === 'error') {
                pendingForms.clear();
                isSubmitting = false;
            }
            const result = original.apply(this, args);
            if (options?.showCancelButton) result.then(answer => {
                if (!answer.isConfirmed) pendingForms.clear();
            });
            return result;
        };
    };
    hookSwal();
    document.addEventListener('DOMContentLoaded', hookSwal);
    window.addEventListener('beforeunload', e => {
        if (isSubmitting || bypassPrompt || !hasUnsavedChanges()) return;
        e.preventDefault();
        e.returnValue = '';
    });
    window.addEventListener('pageshow', () => {
        isSubmitting = false;
        bypassPrompt = false;
    });

    // Keep an existing SweetAlert edit form mounted while confirming dismissal.
    const nativeConfirmation = () => {
        const dialog = document.createElement('dialog');
        if (typeof dialog.showModal !== 'function') {
            return Promise.resolve(window.confirm('Discard your unsaved changes and leave this page?'));
        }
        dialog.setAttribute('aria-labelledby', 'portal-unsaved-title');
        dialog.style.cssText = 'margin:auto;padding:1.5rem;border:1px solid #e5e7eb;border-radius:.75rem;width:min(30rem,calc(100vw - 2rem));color:#374151;background:white;z-index:2147483647;';
        dialog.innerHTML = '<h2 id="portal-unsaved-title" style="font-size:1.5rem;font-weight:600;margin-bottom:.75rem">Unsaved Changes</h2>'
            + '<p>You have unsaved changes. Discard them and leave?</p>'
            + '<div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.5rem">'
            + '<button type="button" data-stay style="padding:.625rem 1rem;border-radius:.5rem;background:#6b7280;color:white">Stay on Page</button>'
            + '<button type="button" data-discard style="padding:.625rem 1rem;border-radius:.5rem;background:#C41E3A;color:white">Discard Changes</button></div>';
        document.body.append(dialog);
        return new Promise(resolve => {
            const finish = discard => { dialog.close(); dialog.remove(); resolve(discard); };
            dialog.querySelector('[data-stay]').addEventListener('click', () => finish(false));
            dialog.querySelector('[data-discard]').addEventListener('click', () => finish(true));
            dialog.addEventListener('cancel', e => { e.preventDefault(); finish(false); });
            dialog.showModal();
            dialog.querySelector('[data-stay]').focus();
        });
    };
    const confirmExit = (onConfirm, onCancel) => {
        if (confirmation) return confirmation;
        if (bypassPrompt || isSubmitting || !hasUnsavedChanges()) {
            if (typeof onConfirm === 'function') onConfirm();
            return Promise.resolve(true);
        }
        const answer = window.Swal && !document.querySelector('.swal2-popup form, dialog[open]')
            ? window.Swal.fire({
                title: 'Unsaved Changes',
                text: 'You have unsaved changes. Discard them and leave?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Discard Changes',
                cancelButtonText: 'Stay on Page',
                customClass: { popup: 'portal-unsaved-changes-popup' },
                confirmButtonColor: '#C41E3A',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                focusCancel: true,
                allowOutsideClick: false,
            }).then(result => result.isConfirmed)
            : nativeConfirmation();
        confirmation = Promise.resolve(answer).then(discard => {
            confirmation = null;
            if (discard) {
                reset();
                bypassPrompt = true;
                try {
                    if (typeof onConfirm === 'function') onConfirm();
                } finally {
                    setTimeout(() => { bypassPrompt = false; }, 0);
                }
            } else if (typeof onCancel === 'function') onCancel();
            return discard;
        });
        return confirmation;
    };
    const closeEditModal = () => {
        window.dispatchEvent(new CustomEvent('close-edit-modal'));
        if (typeof window.closeEditModal === 'function') window.closeEditModal();
        const modal = document.getElementById('editCaseModal');
        if (modal?._x_dataStack?.[0]) modal._x_dataStack[0].open = false;
    };
    const stop = e => {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    };
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape' || confirmation || bypassPrompt || !hasUnsavedChanges()) return;
        const modal = document.getElementById('editCaseModal');
        const open = modal && (modal._x_dataStack?.[0]?.open || window.Alpine?.$data?.(modal)?.open);
        if (open) { stop(e); confirmExit(closeEditModal); }
    }, true);
    document.addEventListener('click', e => {
        if (bypassPrompt || isSubmitting || !hasUnsavedChanges()) return;
        const modal = document.getElementById('editCaseModal');
        if (modal && e.target === modal.querySelector('.fixed.inset-0')
            && (modal._x_dataStack?.[0]?.open || window.Alpine?.$data?.(modal)?.open)) {
            stop(e);
            confirmExit(closeEditModal);
            return;
        }
        const btn = e.target.closest('button, a');
        if (!btn) return;
        // Alert buttons stay usable; Cancel/Close on an editable popup is guarded.
        const popup = btn.closest('.swal2-popup');
        if (btn.closest('.swal2-container, .swal2-popup, [class*="swal2-"]')
            && !(popup?.querySelector('form') && btn.matches('.swal2-cancel, .swal2-close'))) return;
        if (btn.matches('[data-skip-unsaved-guard]')) return;
        if (btn.tagName === 'BUTTON' && btn.type === 'submit') return;
        if (btn.closest('form[method="GET"], form[method="get"]')) return;
        const text = (btn.textContent || '').trim().toLowerCase();
        if (text === 'clear' || btn.matches('.clear-btn, [data-clear]')) return;
        const back = btn.matches('#back-button, .back-btn, [data-back]')
            || !!btn.querySelector('.fa-arrow-left, .fa-chevron-left') || text.startsWith('back');
        const cancel = btn.id === 'cancel-edit-button' || btn.matches('.btn-cancel, .swal2-cancel') || text.includes('cancel');
        const close = btn.matches('[data-close-modal], .close-modal, .btn-close, [data-bs-dismiss="modal"], .swal2-close')
            || !!btn.querySelector('.fa-times, .fa-xmark') || (btn.getAttribute('@click') || '').includes('open = false');
        const link = btn.tagName === 'A' && btn.href && !btn.href.startsWith('javascript:') && !btn.href.endsWith('#');
        if (link && (btn.hasAttribute('download') || btn.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey)) return;
        if (!link && !back && !cancel && !close) return;
        stop(e);
        confirmExit(() => {
            if (link) window.location.assign(btn.href);
            else if (back) {
                if (document.referrer && document.referrer !== window.location.href) window.location.assign(document.referrer);
                else window.history.back();
            } else btn.click();
        });
    }, true);

    window.portalUnsavedChanges = {
        setDirty: (dirty = true) => {
            if (!dirty) reset();
            else { manualDirty = true; isSubmitting = false; bypassPrompt = false; }
        },
        isDirty: hasUnsavedChanges,
        reset,
        markSaved,
        confirmExit,
    };
})();
