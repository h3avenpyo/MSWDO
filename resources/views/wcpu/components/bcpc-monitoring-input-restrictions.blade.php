<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bcpc-monitoring-form');
    if (!form) return;

    const organizationSelector = 'input[name^="bcpc_organizations"][name$="[name]"]';
    const responsiblePersonSelector = 'input[name^="bcpc_organizations"][name$="[responsible_person]"]';
    const phoneSelector = 'input[name^="bcpc_organizations"][name$="[phone]"]';
    const numericSelector = 'input[type="number"]';
    const lettersOnlySelector = `${organizationSelector}, ${responsiblePersonSelector}`;
    const digitsOnlySelector = `${phoneSelector}, ${numericSelector}`;

    const replaceValue = (input, pattern) => {
        const originalValue = input.value;
        const sanitizedValue = originalValue.replace(pattern, '');
        if (sanitizedValue === originalValue) return;

        const selectionStart = input.selectionStart;
        const removedBeforeCaret = selectionStart === null
            ? 0
            : originalValue.slice(0, selectionStart).length - originalValue.slice(0, selectionStart).replace(pattern, '').length;

        input.value = sanitizedValue;
        if (selectionStart !== null && document.activeElement === input && input.type !== 'number') {
            const nextCaret = Math.max(0, selectionStart - removedBeforeCaret);
            input.setSelectionRange(nextCaret, nextCaret);
        }
    };

    const sanitizeInput = input => {
        if (!(input instanceof HTMLInputElement)) return;
        if (input.matches(lettersOnlySelector)) replaceValue(input, /[^\p{L}\p{M} ]/gu);
        if (input.matches(digitsOnlySelector)) replaceValue(input, /\D/g);
    };

    form.querySelectorAll(`${lettersOnlySelector}, ${digitsOnlySelector}`).forEach(sanitizeInput);

    form.addEventListener('keydown', event => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches(digitsOnlySelector)) return;
        if (['e', 'E', '+', '-', '.', ','].includes(event.key)) event.preventDefault();
    });

    form.addEventListener('input', event => sanitizeInput(event.target));
    form.addEventListener('submit', () => {
        form.querySelectorAll(`${lettersOnlySelector}, ${digitsOnlySelector}`).forEach(sanitizeInput);
    }, true);
});
</script>
