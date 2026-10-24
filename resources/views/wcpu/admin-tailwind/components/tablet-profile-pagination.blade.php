<style>
@media (min-width: 48rem) and (max-width: 85.375rem) {
    html body.portal-ui #mainContent .tablet-profile-pagination {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 1rem !important;
        padding: 1rem 1.5rem !important;
        border: 0 !important;
        border-top: 1px solid #f3f4f6 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        width: 100%;
        box-sizing: border-box;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination > div:first-child { color: #6b7280; font-size: .875rem; text-align: left; }
    html body.portal-ui #mainContent .tablet-profile-pagination > div:last-child { display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: .5rem; margin-left: auto; max-width: 100%; }
    html body.portal-ui #mainContent .tablet-profile-pagination :is(#page-numbers, #mobile-page-numbers) { display: contents !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box;
        min-width: 2.25rem !important;
        width: 2.25rem !important;
        height: 2.25rem !important;
        min-height: 2.25rem !important;
        padding: .4rem .7rem !important;
        margin: 0 .15rem !important;
        border: 1px solid #e5e7eb !important;
        border-radius: .5rem !important;
        background: #fff !important;
        color: #C41E3A !important;
        font-size: .875rem !important;
        font-weight: 600;
        box-shadow: 0 1px 2px rgb(0 0 0 / 4%) !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination button[class~="bg-[#C41E3A]"] { background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important; box-shadow: 0 2px 5px rgb(126 34 206 / 18%) !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button:disabled { opacity: .4 !important; cursor: default; box-shadow: none !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button > i { color: inherit !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button:hover:not(:disabled) { border-color: #C41E3A !important; transform: translateY(-1px); }
}
</style>
