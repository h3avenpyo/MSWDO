@php
    $isMultiroleReferral = $isMultiroleReferral ?? (auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment());
    $lockedReferralFields = $isMultiroleReferral ? \App\Services\MultiroleReferralFields::forCase(auth()->user(), $case) : [];
@endphp
@if($isMultiroleReferral)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fields = @json($lockedReferralFields);
    Object.entries(fields).forEach(([name, value]) => {
        if (name === 'referred_by_contact') return;
        const control = document.querySelector(`[name="${name}"]`);
        if (!control) return;
        control.value = value ?? '';
        control.setAttribute('aria-readonly', 'true');
        if (control.tagName === 'SELECT') {
            const hidden = document.createElement('input');
            hidden.type = 'hidden'; hidden.name = name; hidden.value = value ?? '';
            control.after(hidden);
            control.disabled = true;
        } else control.readOnly = true;
    });
});
</script>
@endif
