<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-card-layout.css') }}?v={{ filemtime(public_path('css/bcpc-monitoring-card-layout.css')) }}" media="screen">
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-tablet-cards.css') }}?v={{ filemtime(public_path('css/bcpc-monitoring-tablet-cards.css')) }}" media="screen and (min-width: 48rem)">
<script src="{{ asset('js/archived-monitoring-preview.js') }}?v={{ filemtime(public_path('js/archived-monitoring-preview.js')) }}"></script>
<style>
    /* Admin previews use the same content-sized shell and scroll area as desk officers. */
    body.portal-ui .swal2-popup.admin-archive-view-popup.archived-monitoring-view-popup {
        height: auto;
        min-width: 0;
    }
    body.portal-ui .swal2-popup.admin-archive-view-popup.archived-monitoring-view-popup > .swal2-html-container {
        display: block !important;
        overflow: auto !important;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        padding: 16px 20px !important;
    }
    body.portal-ui .swal2-popup.admin-archive-view-popup.archived-monitoring-view-popup .swd-archive-detail {
        max-height: none !important;
        overflow: visible;
        padding: 0;
        width: 100%;
        min-width: 0;
    }
    .archived-monitoring-view-popup #bcpc-monitoring-form.archived-monitoring-preview .archived-monitoring-value {
        min-width: 0;
        padding: .625rem .375rem;
        border-radius: .5rem;
        background: #f3f4f6;
        line-height: 1.5;
        text-align: center;
        overflow-wrap: anywhere;
    }
    .archived-monitoring-view-popup #bcpc-monitoring-form.archived-monitoring-preview .bcpc-organizations-table .archived-monitoring-value { text-align: left; }
    .archived-monitoring-view-popup #bcpc-monitoring-form.archived-monitoring-preview .bcpc-monitoring-row-label {
        border: 0 !important;
    }
    body.portal-ui.bcpc-admin-header-theme .swal2-popup.admin-archive-view-popup.archived-monitoring-view-popup > .swal2-title {
        padding: 14px 52px 14px 20px !important;
    }
    body.portal-ui.bcpc-admin-header-theme .swal2-popup.archived-monitoring-view-popup .archived-monitoring-section-filter > label {
        font-size: .875rem;
    }
    body.portal-ui.bcpc-admin-header-theme .swal2-popup.archived-monitoring-view-popup .archived-monitoring-filter-controls > select {
        font-size: 1rem;
    }
    @media (max-width: 47.9375rem) {
        body.portal-ui .swal2-popup.admin-archive-view-popup.archived-monitoring-view-popup { width: calc(100vw - 1rem) !important; }
        body.portal-ui .swal2-popup.admin-archive-view-popup.archived-monitoring-view-popup > .swal2-html-container { padding: 12px !important; }
    }
    .archived-monitoring-preview [data-monitoring-section][hidden] { display: none !important; }
    .archived-monitoring-preview .archived-monitoring-section-filter {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: .75rem;
        background: #fff;
    }
    .archived-monitoring-preview .archived-monitoring-section-filter > label {
        display: block;
        margin-bottom: .5rem;
        font-size: .875rem;
        font-weight: 600;
    }
    .archived-monitoring-preview .archived-monitoring-filter-controls {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .5rem;
    }
    .archived-monitoring-preview .archived-monitoring-filter-controls > :is(select, button) {
        min-width: 0;
        min-height: 2.75rem;
        padding: .625rem .75rem;
        border: 1px solid #d1d5db;
        border-radius: .5rem;
        background-color: #fff;
        color: #374151;
        font-size: 1rem;
    }
    .archived-monitoring-preview .archived-monitoring-filter-controls > button { background: #f3f4f6; }
    @media (max-width: 26.875rem) {
        .archived-monitoring-preview .archived-monitoring-filter-controls { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 85.375rem) {
        .swal2-popup.archived-monitoring-view-popup {
            display: flex !important;
            flex-direction: column;
            min-width: 0;
            max-height: calc(100dvh - 1.5rem);
        }
        .swal2-popup.archived-monitoring-view-popup .swal2-title { flex: 0 0 auto; }
        .swal2-popup.archived-monitoring-view-popup .swal2-close { position: absolute !important; top: 0; right: 0; }
        .swal2-popup.archived-monitoring-view-popup .swal2-html-container {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow: auto;
        }
        .swal2-popup.archived-monitoring-view-popup .swd-archive-detail {
            max-height: none;
            overflow: visible;
            padding: 0;
        }
        .swal2-popup.archived-monitoring-view-popup .swal2-actions { flex: 0 0 auto; margin: .5rem 0 1rem; }
        #bcpc-monitoring-form.archived-monitoring-preview { min-width: 0; width: 100%; }
        #bcpc-monitoring-form.archived-monitoring-preview .archived-monitoring-value {
            min-width: 0;
            padding: .625rem .375rem;
            border-radius: .5rem;
            background: #f3f4f6;
            line-height: 1.5;
            text-align: center;
            overflow-wrap: anywhere;
        }
        #bcpc-monitoring-form.archived-monitoring-preview .bcpc-organizations-table .archived-monitoring-value { text-align: left; }
    }
</style>
<style>
    .archived-monitoring-view-popup .swd-archive-detail { text-align: left; max-height: 65vh; overflow: auto; padding: .25rem; font-size: .875rem; color: #374151; }
    .archived-monitoring-view-popup .swd-archive-detail table { width: 100%; border-collapse: collapse; }
    .archived-monitoring-view-popup .swd-archive-detail :is(th, td) { padding: .75rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
    .archived-monitoring-view-popup .swd-archive-detail th { background: #f9fafb; font-weight: 600; }
</style>
