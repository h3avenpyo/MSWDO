<style>
    .user-logs-page { display:flex;flex-direction:column;gap:1.25rem;min-width:0; }
    .activity-header { background:#fff;color:#1f2937;border:1px solid #f1f5f9;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;box-shadow:0 1px 3px rgba(0,0,0,.05); }
    .user-logs-page .activity-header { margin:0; }
    .activity-header h1 { margin:0 0 .25rem;font-size:1.5rem;font-weight:700; }
    .activity-header p { margin:0;color:#6b7280;font-size:.875rem; }
    .activity-card { background:#fff;border:1px solid #f1f5f9;border-radius:12px;padding:1.5rem;margin-bottom:1.25rem;min-width:0;box-shadow:0 1px 3px rgba(0,0,0,.05); }
    .user-logs-page .activity-card { margin:0; }
    .activity-toolbar-top { display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap; }
    .activity-toolbar-heading { display:flex;align-items:center;gap:.75rem; }
    .activity-history-icon { display:flex;align-items:center;justify-content:center;flex-shrink:0;width:40px;height:40px;border-radius:8px;background:#fff1f2;color:#b11116; }
    .activity-history-icon svg { width:21px;height:21px; }
    .activity-toolbar-heading h2 { margin:0;font-size:1.125rem;font-weight:600;color:#1f2937; }
    .activity-toolbar-heading p { margin:.15rem 0 0;color:#6b7280;font-size:.875rem; }
    .activity-search { position:relative;width:288px;max-width:100%; }
    .activity-search > svg { position:absolute;width:17px;height:17px;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
    .activity-search input { box-sizing:border-box;width:100%;border:1px solid #d1d5db;border-radius:8px;padding:.65rem 2.5rem .65rem 2.5rem;font:inherit;font-size:.875rem; }
    .activity-search button { position:absolute;right:4px;top:4px;bottom:4px;display:flex;align-items:center;justify-content:center;background:transparent;border:0;border-radius:5px;color:#b11116;cursor:pointer; }
    .activity-search button svg { width:18px;height:18px; }
    .activity-filter-panel { margin-top:1rem;border-top:1px solid #f3f4f6;padding-top:.75rem; }
    .activity-filter-panel summary { color:#6b7280;font-size:.825rem;font-weight:500;cursor:pointer;width:fit-content; }
    .activity-filter-panel summary svg { display:inline-block;width:15px;height:15px;vertical-align:middle;margin:0 .35rem; }
    .activity-filters { display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-top:1rem; }
    .activity-filters label { display:block;font-weight:600;font-size:.8rem;margin-bottom:.4rem;color:#4b5563; }
    .activity-filters input,.activity-filters select { box-sizing:border-box;width:100%;min-width:0;padding:.65rem;border:1px solid #d1d5db;border-radius:8px;background:#fff;font:inherit;font-size:.875rem;color:#374151; }
    .activity-search input:focus,.activity-filters input:focus,.activity-filters select:focus { outline:2px solid #fda4af;outline-offset:1px;border-color:#b11116; }
    .activity-actions { display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;margin-top:1rem; }
    .activity-button { display:inline-block;padding:.65rem 1rem;background:#b11116;color:white;text-decoration:none;border:0;border-radius:8px;font:inherit;cursor:pointer; }
    .activity-link { color:#b11116;text-decoration:underline; }
    .activity-results { border:1px solid #dbe5e7;border-radius:12px;background:white;overflow:hidden;box-shadow:0 2px 8px rgba(15,23,42,.05); }
    .activity-scroll { overflow:auto; }
    .activity-desktop { max-height:65vh; }
    .activity-table { width:100%;border-collapse:collapse;font-size:.875rem;text-align:left; }
    .activity-table th { background:#f9fafb;color:#4b5563;font-size:.75rem; }
    .activity-table td,.activity-table th { padding:1rem 1.25rem;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
    .activity-table td { overflow-wrap:anywhere;color:#4b5563; }
    .activity-log-table { table-layout:fixed;min-width:680px; }
    .activity-log-table thead { position:sticky;top:0;z-index:1; }
    .activity-log-table thead th { background:#f2f7f7;color:#365563;text-transform:uppercase;letter-spacing:.02em;font-weight:600;border:0;padding:.8rem 1rem; }
    .activity-log-table tbody td { height:72px;padding:.85rem 1rem;color:#24445d;border-bottom:1px solid #e5edf0; }
    .activity-log-table tbody tr:hover { background:#f8fcfc; }
    .activity-log-table thead th:nth-child(3) { text-align:center; }
    .activity-log-table td:nth-child(3) .activity-action { justify-content:center; }
    .activity-log-table .activity-details-cell { text-align:center; }
    .activity-action { display:flex;align-items:flex-start;gap:.6rem;min-width:0; }
    .activity-action > svg { width:16px;height:16px;flex-shrink:0;color:#e11d48;margin-top:2px; }
    .activity-action > div { min-width:0; }
    .activity-action-description { color:#24445d;display:block;overflow-wrap:anywhere; }
    .activity-action-module { color:#6b7280;display:block;font-size:.75rem;margin-top:.3rem; }
    .activity-user { display:flex;align-items:center;gap:.75rem;min-width:0; }
    .activity-user > div { min-width:0; }
    .activity-user-name { display:block;font-size:.875rem;font-weight:500;color:#173b56;overflow-wrap:anywhere; }
    .activity-user-role { display:block;font-size:.75rem;color:#6b7280;margin-top:.15rem; }
    .activity-view { display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:8px;background:#2563eb;color:white;text-decoration:none; }
    .activity-view:hover { background:#1d4ed8; }
    .activity-view svg { width:17px;height:17px; }
    .activity-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem 1rem; }
    .activity-empty > span { display:flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:50%;background:#f3f4f6;color:#9ca3af; }
    .activity-empty p { color:#6b7280;margin:1rem 0 .3rem; }
    .activity-empty small { color:#9ca3af; }
    .activity-results-footer { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;padding:1rem 1.5rem;background:#fff;color:#24445d;font-size:.825rem;border-top:1px solid #e5edf0; }
    .activity-timezone { display:block;color:#9ca3af;font-size:.75rem;margin-top:.2rem; }
    .activity-pagination { display:flex;gap:.4rem;flex-wrap:wrap;align-items:center; }
    .activity-pagination a,.activity-pagination > span:not(.activity-sr-only) { box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 .5rem;border:1px solid #e2e8f0;border-radius:8px;color:#c41e3a;background:white;text-decoration:none; }
    .activity-pagination a[rel] { color:#365563; }
    .activity-pagination svg { width:16px;height:16px; }
    .activity-pagination a:hover { background:#f3f4f6; }
    .activity-pagination [aria-current] { background:#c41e3a !important;color:white !important;border-color:#c41e3a !important; }
    .activity-pagination [aria-disabled] { opacity:.45;background:#f3f4f6 !important; }
    .activity-meta { display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.25rem; }
    .activity-meta dt { color:#6b7280;font-size:.75rem;margin-bottom:.4rem; }
    .activity-meta dd { margin:0;overflow-wrap:anywhere;color:#1f2937;font-size:.875rem; }
    .activity-changes-heading { font-size:1rem;margin:1.5rem 0 .5rem;color:#1f2937; }
    .activity-privacy-note { color:#6b7280;font-size:.8rem; }
    .activity-errors { color:#9f1239;background:#fff1f2;border-radius:8px;padding:1rem;margin-top:1rem; }
    .activity-mobile { display:none; }
    .activity-dialog { box-sizing:border-box;width:min(640px,calc(100% - 2rem));max-height:90vh;max-height:90dvh;margin:auto;padding:0;border:0;border-radius:12px;background:#fff;box-shadow:0 20px 40px rgba(0,0,0,.25);color:#374151; }
    body:has(.activity-dialog[open]) { overflow:hidden; }
    .activity-dialog::backdrop { background:rgba(15,23,42,.55); }
    .activity-dialog-header { display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.5rem;background:#b11116;color:white;position:sticky;top:0;z-index:1; }
    .activity-dialog-header h2 { display:flex;align-items:center;gap:.5rem;font-size:1.1rem;margin:0; }
    .activity-dialog-header svg { width:20px;height:20px; }
    .activity-dialog-header button { display:flex;align-items:center;justify-content:center;width:36px;height:36px;padding:0;background:transparent;border:0;border-radius:6px;color:white;cursor:pointer; }
    .activity-dialog-header button:hover { background:rgba(255,255,255,.15); }
    .activity-dialog-header button:focus-visible { outline:2px solid #fff;outline-offset:2px; }
    .activity-dialog-body { padding:1.5rem; }
    .activity-dialog-description { font-size:.95rem;overflow-wrap:anywhere;margin:0 0 1.5rem;color:#1f2937; }
    .activity-dialog .activity-meta { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .activity-dialog .activity-table { min-width:360px; }
    .activity-sr-only { position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0; }
    @media(max-width:1024px) { .activity-filters,.activity-meta { grid-template-columns:repeat(2,minmax(0,1fr)); } .activity-log-table td,.activity-log-table th { padding:1rem .75rem; } }
    @media(max-width:767px) {
        .activity-desktop { display:none; }
        .activity-mobile { display:flex;flex-direction:column;gap:1rem; }
        .activity-results { background:transparent;border:0;box-shadow:none;overflow:visible; }
        .activity-mobile-card { display:block;box-sizing:border-box;background:white;border:1px solid #f1f5f9;border-radius:12px;padding:1rem;text-decoration:none;color:inherit;box-shadow:0 1px 3px rgba(0,0,0,.05); }
        a.activity-mobile-card:hover { border-color:#fda4af; }
        .activity-mobile-top { display:flex;align-items:center;justify-content:space-between;gap:.75rem; }
        .activity-mobile-eye { width:18px;height:18px;color:#2563eb;flex-shrink:0; }
        .activity-mobile-date { font-size:.75rem;color:#6b7280;margin:.75rem 0; }
        .activity-mobile-card .activity-action { font-size:.875rem; }
        .activity-results-footer { margin-top:1rem;background:white;border:1px solid #f1f5f9;border-radius:12px;padding:1rem; }
        .activity-toolbar,.activity-header { padding:1rem; }
        .activity-toolbar-top { align-items:stretch;flex-direction:column; }
        .activity-search { width:100%; }
    }
    @media(max-width:480px) { .activity-filters,.activity-meta,.activity-dialog .activity-meta { grid-template-columns:minmax(0,1fr); } .activity-dialog-body { padding:1rem; } .activity-dialog-header { padding:.75rem 1rem; } .activity-pagination { gap:.25rem; } .activity-pagination a,.activity-pagination > span:not(.activity-sr-only) { min-width:32px;height:32px; } }
</style>
