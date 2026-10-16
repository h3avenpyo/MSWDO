(() => {
    if (window.contactNumberValidation) return;
    window.contactNumberValidation = true;

    const fields = new Set(['phone', 'phone_number', 'mobile', 'mobile_number', 'contact_number',
        'beneficiary_contact_number', 'rep_contact_number', 'emergency_contact_number',
        'victim_contact', 'guardian_contact', 'guardian_abuser_contact', 'witness_contact',
        'client_contact', 'recipient_contact', 'referred_by_contact', 'recipient_contact_number']);

    function isContact(input) {
        if (!(input instanceof HTMLInputElement) || ['hidden', 'search', 'checkbox', 'radio'].includes(input.type)) return false;
        const name = input.name.replace(/\]/g, '').split(/[.\[]/).pop();
        return fields.has(name) || input.id === 'contactNumber' || input.type === 'tel' || input.matches('input[oninput*="draftIntake.client.contact"]');
    }

    function limits(input) {
        const name = input.name;
        const monitoring = name.startsWith('bcpc_organizations[');
        let exact = /^(victim_contact|guardian_contact|guardian_abuser_contact|client_contact|recipient_contact|referred_by_contact|beneficiary_contact_number|rep_contact_number|witness_contact)$/.test(name) ? 11 : null;
        if (input.id === 'contactNumber') exact = 11;
        if (input.matches('input[oninput*="draftIntake.client.contact"]')) exact = 11;
        if (['contact_number', 'emergency_contact_number'].includes(name)
            && (location.pathname.includes('/senior') || location.pathname.includes('/referral-info') || location.pathname.includes('service-request'))) exact = 11;
        const patternLength = (input.getAttribute('pattern') || '').match(/(?:\[0-9\]|\\d)\{(\d+)\}/);
        if (patternLength) exact = Number(patternLength[1]);
        let maximum = input.maxLength > 0 ? input.maxLength : (monitoring ? null : 20);
        if (name === 'witness_contact') maximum = 11;
        if (exact) maximum = exact;
        return { exact, maximum, minimum: input.minLength > 0 ? input.minLength : null };
    }

    function validate(input, sanitize = false) {
        if (!isContact(input) || input.disabled) return true;
        input.inputMode = 'numeric';
        if (sanitize) {
            const start = input.selectionStart;
            const original = input.value;
            const clean = original.replace(/[^0-9]/g, '');
            if (clean !== original) {
                input.value = clean;
                if (start !== null && input.type !== 'number') {
                    const caret = original.slice(0, start).replace(/[^0-9]/g, '').length;
                    input.setSelectionRange(caret, caret);
                }
            }
        }
        const { exact, maximum, minimum } = limits(input);
        let message = '';
        if (input.value && !/^[0-9]+$/.test(input.value)) message = 'Enter digits only (0–9), without spaces or symbols.';
        else if (input.value && exact && input.value.length !== exact) message = `Enter exactly ${exact} digits for the contact number.`;
        else if (input.value && maximum && input.value.length > maximum) message = `Enter no more than ${maximum} digits for the contact number.`;
        else if (input.value && minimum && input.value.length < minimum) message = `Enter at least ${minimum} digits for the contact number.`;
        input.setCustomValidity(message);
        return !message;
    }

    const validateForm = form => {
        let valid = true;
        form.querySelectorAll('input').forEach(input => { if (!validate(input)) valid = false; });
        return valid;
    };
    document.addEventListener('beforeinput', event => {
        if (isContact(event.target) && event.data && /[^0-9]/.test(event.data) && event.inputType === 'insertText') event.preventDefault();
    }, true);
    document.addEventListener('input', event => validate(event.target, true), true);
    document.addEventListener('change', event => validate(event.target), true);
    document.addEventListener('focusin', event => validate(event.target), true);
    document.addEventListener('submit', event => {
        if (event.target instanceof HTMLFormElement && !validateForm(event.target)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            event.target.reportValidity();
        }
    }, true);
    const nativeSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        if (!validateForm(this) || !this.reportValidity()) return;
        return nativeSubmit.call(this);
    };
    document.querySelectorAll('input').forEach(input => validate(input));
    new MutationObserver(changes => changes.forEach(change => change.addedNodes.forEach(node => {
        if (!(node instanceof Element)) return;
        if (node.matches('input')) validate(node);
        node.querySelectorAll('input').forEach(input => validate(input));
    }))).observe(document.body, { childList: true, subtree: true });
})();
