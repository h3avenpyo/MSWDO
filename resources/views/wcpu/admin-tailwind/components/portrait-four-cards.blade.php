@php
    $fourCardTabletQuery = request()->routeIs('case-profiles.index', 'admin.quarterly-reports.index', 'admin.referral-info.index', 'admin.referrals', 'admin.referrals.index')
        ? '(min-width: 48rem) and (max-width: 85.375rem)'
        : '(min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)';
@endphp
<script>
@if(auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin.quarterly-reports.index'))
window.quarterlyReportFeedbackPending = @json((bool) (session('generated_bcpc_quarterly_report') || session('generated_vawc_quarterly_report') || session('duplicate_quarterly_report') || session('no_cases_for_quarterly_report') || session('archived_quarterly_report')));
@endif
(() => {
    const media = matchMedia(@json($fourCardTabletQuery));
    const sync = () => {
        if (window.quarterlyReportFeedbackPending) return;
        const url = new URL(location.href);
        if (media.matches === (url.searchParams.get('portrait_cards') === '1')) return;
        if (media.matches) url.searchParams.set('portrait_cards', '1');
        else url.searchParams.delete('portrait_cards');
        [...url.searchParams.keys()].filter(key => key === 'page' || key.endsWith('_page')).forEach(key => url.searchParams.delete(key));
        location.replace(url.href);
    };
    sync();
    window.addEventListener('quarterly-report-feedback-dismissed', sync);
    media.addEventListener('change', sync);
})();
</script>
<style>
@media (min-width: 48rem) and (max-width: 85.375rem)@unless(request()->routeIs('admin.quarterly-reports.index', 'admin.referral-info.index')) and (orientation: portrait)@endunless {
    html body.portal-ui :is(.case-profiles-table-view, #referrals-table-panel, #recipients-table-panel, .quarterly-table-view, .bcpc-monitoring-table-view) { display: none !important; }
    html body.portal-ui :is(#referrals-mobile-cards, #recipients-mobile-cards) { display: block !important; }
    html body.portal-ui :is(.case-profiles-mobile-cards, #referrals-mobile-cards .referral-cards-list, #recipientsCardsMobile, #quarterly-mobile-cards, .bcpc-monitoring-mobile-cards) { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 1rem !important; padding: 1rem !important; max-height: none !important; overflow: visible !important; }
    html body.portal-ui :is(.case-profile-card, .referral-card, #recipientsCardsMobile .recipient-row, .quarterly-report-card, .bcpc-monitoring-mobile-card) { display: flex; flex-direction: column; min-width: 0; margin: 0 !important; padding: 1rem; border: 1px solid #dbe1e8; border-radius: .625rem; background: #fff; overflow-wrap: anywhere; }
    html body.portal-ui :is(.referral-card-actions, .recipient-card-actions, .quarterly-report-card-actions, .bcpc-monitoring-card-actions) { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: .375rem; margin-top: auto; }
    html body.portal-ui :is(.referral-card-actions, .recipient-card-actions, .quarterly-report-card-actions, .bcpc-monitoring-card-actions) > :is(button, a) { width: 100% !important; min-width: 0 !important; min-height: 2.75rem; padding: .375rem .125rem !important; justify-content: center; border-radius: .5rem !important; }
    .case-profiles-mobile-cards > .case-profiles-pagination-container, #quarterly-mobile-cards > div:last-child { grid-column: 1 / -1; }
    html body.portal-ui :is(#users-desktop-table, #archived-users-table-view, .archived-barangay-cases-table-view, .archived-referrals-table-view, .archived-quarterly-table-view, .archived-bcpc-monitoring-table-view) { display: none !important; }
    html body.portal-ui :is(#users-mobile-view, #users-cards-mobile, .archived-barangay-cases-mobile-cards, .archived-referrals-mobile-cards, .archived-quarterly-mobile-cards, .archived-bcpc-monitoring-mobile-cards) { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 1rem !important; padding: 1rem !important; min-width: 0; }
    html body.portal-ui :is(.user-profile-card, #users-cards-mobile > .user-row, .archived-barangay-case-card, .archived-referral-card, .archived-quarterly-mobile-cards > article, .archived-bcpc-monitoring-card) { display: flex; flex-direction: column; min-width: 0; margin: 0 !important; padding: 1rem !important; border: 1px solid #dbe1e8; border-radius: .625rem; background: #fff; overflow-wrap: anywhere; }
    html body.portal-ui :is(.user-profile-card-actions, .archived-user-card-actions, .archived-barangay-case-actions, .archived-referral-card-actions, .archived-quarterly-card-actions, .archived-bcpc-monitoring-card-actions) { margin-top: auto; }
    #users-cards-mobile > :not(.user-row), .archived-quarterly-mobile-cards > :not(article) { grid-column: 1 / -1; }
}
</style>
