# BCPC and SWD modal source audit

Scope: BCPC Admin, SWD/VAWC Admin, BCPC Desk Officer, SWD/VAWC Desk Officer, and the shared templates used by those roles. No database operations or authenticated actions were performed.

Inventory: 49 template/component files; 263 SweetAlert invocation sites (including conditionally exclusive variants), plus the native dialogs listed below.

## Corrections

- Shared presentation now distinguishes centered confirmations/result alerts from right-aligned form/detail actions. Existing action order is preserved.
- Removed the unconditional SweetAlert right alignment and the page-specific monitoring spacing overrides; monitoring confirmations and results use the common styles.
- Standardized headers, action gaps, editable input minimum heights, loading spinner colors, viewport limits, and internal scrolling. Readonly detail inputs retain their existing compact presentation.
- Native modal page scroll locks account for multiple open dialogs and release when the last one closes.
- Repeated header dismiss actions reuse existing Cancel buttons only where both handlers are known to match. Other dismiss handlers remain delegated to their original controls.
- Loading-only popups consistently hide confirmation controls and prevent Escape dismissal while requests run. Existing completion/error paths remain intact.
- Calendar/referral browser alerts use the common SweetAlert presentation with the original browser fallback. Calendar deletion failures now display an error instead of logging silently.
- Logout and toast presentations remain separate. Existing fields, permissions, routes and data operations were retained.

## Validation and limits

- Blade view compilation.
- JavaScript syntax checks and CSS parsing.
- Seven Node tests cover popup classification, reused popup cleanup, callback/content preservation, and native scroll-lock cleanup.
- No connected browser was available. These are source/runtime-unit checks, not visual or end-to-end verification. Desktop/tablet/mobile rendering and authenticated View/Edit/Save/Cancel/Confirm/Close workflows still require a browser pass.

## Individual template inventory

Each entry below was included in the source audit. Conditional variants are listed together and inherit the common modal presentation. Counts describe code sites, not unique visible dialogs.

### `resources/views/wcpu/admin-tailwind/_case-details-modal.blade.php`

- SweetAlert sites: 0.
- Shared details/form content: typography, field spacing, row presentation and overflow rules.

### `resources/views/wcpu/admin-tailwind/add-new-case.blade.php`

- SweetAlert sites: 5.
- Popup titles: 'Confirm Case Submission', 'Submitting Case...', 'Case Created Successfully!', 'Error', 'Error'.

### `resources/views/wcpu/admin-tailwind/archived-referrals.blade.php`

- SweetAlert sites: 5.
- Native dialogs: `viewArchivedRecipientModal`.
- Popup titles: 'Referral Details', 'Processing...', 'Error'.

### `resources/views/wcpu/admin-tailwind/archived-users.blade.php`

- SweetAlert sites: 6.
- Native dialogs: `userModal`.
- Popup titles: 'Restore User?', 'Permanently delete user?'.

### `resources/views/wcpu/admin-tailwind/barangay-cases-view.blade.php`

- SweetAlert sites: 12.
- Native dialogs: `caseModal`, `editCaseModal`.
- Popup titles: 'Print Cases', 'Print Cases', 'Updating Case', 'Success!', 'Success!', 'Validation Error!', 'Error!', 'Archiving Case...', 'Archived!', 'Archived!', 'Unable to Archive Case'.

### `resources/views/wcpu/admin-tailwind/barangay-cases.blade.php`

- SweetAlert sites: 2.
- Popup titles: 'Referral Unavailable', 'Print Cases'.

### `resources/views/wcpu/admin-tailwind/bcpc-case-monitoring-archived.blade.php`

- SweetAlert sites: 8.
- Popup titles: 'Restore Case Monitoring?', 'Restoring...', 'Restored!', 'Permanently delete record?', 'Deleting...', 'Deleted!'.

### `resources/views/wcpu/admin-tailwind/bcpc-case-monitoring-form.blade.php`

- SweetAlert sites: 0.
- Native dialogs: `printModal`.

### `resources/views/wcpu/admin-tailwind/bcpc-case-monitoring.blade.php`

- SweetAlert sites: 9.
- Native dialogs: `addMonitoringModal`, `generateReportModal`.
- Popup titles: 'Monitoring Saved', 'Success', 'Error', 'Validation Error', 'Archive Case Monitoring?', 'Archiving...', 'Archived!'.

### `resources/views/wcpu/admin-tailwind/calendar/index.blade.php`

- SweetAlert sites: 13.
- Native dialogs: `dateEventsModal`, `eventModal`, `conflictModal`, `eventDetailsModal`.
- Popup titles: 'Saving Event...', 'Saved!', 'Saving Event...', 'Saved!', 'Unable to Load Event', 'Delete Event', 'Delete Event', 'Unable to Delete Event', 'Unable to Delete Event'.

### `resources/views/wcpu/admin-tailwind/components/archive-view-modal.blade.php`

- SweetAlert sites: 1.

### `resources/views/wcpu/admin-tailwind/components/notifications.blade.php`

- SweetAlert sites: 1.
- Native dialogs: `notification-action-details`.

### `resources/views/wcpu/admin-tailwind/partials/swd-archive-view-modal.blade.php`

- SweetAlert sites: 0.
- Native dialogs: `swdArchiveViewDialog`.

### `resources/views/wcpu/admin-tailwind/quarterly-reports/archived.blade.php`

- SweetAlert sites: 1.
- Native dialogs: `restoreModal`.
- Popup titles: 'Restored!'.

### `resources/views/wcpu/admin-tailwind/quarterly-reports/bcpc-archived.blade.php`

- SweetAlert sites: 13.
- Popup titles: 'Restore archived record?', 'Restoring...', 'Restored!', 'Deleted!', 'Permanently delete archived record?', 'Deleting...', 'Delete BCPC Monitoring Report?', 'Deleting...', 'Report Deleted Successfully', 'Success'.

### `resources/views/wcpu/admin-tailwind/quarterly-reports/index.blade.php`

- SweetAlert sites: 6.
- Native dialogs: `generateQuarterlyModal`, `archiveModal`.
- Popup titles: 'Report Generated Successfully!', 'Report Generated Successfully!', 'Report Archived Successfully!', 'Report Already Generated', 'No Cases Available', 'Report Already Generated'.

### `resources/views/wcpu/admin-tailwind/referral-form.blade.php`

- SweetAlert sites: 5.
- Popup titles: 'Missing Guardian Contact', 'Confirm Referral', 'Submitting Referral...', 'Success!', 'Error!'.

### `resources/views/wcpu/admin-tailwind/referral-info.blade.php`

- SweetAlert sites: 13.
- Native dialogs: `recipientModal`, `viewRecipientModal`.

### `resources/views/wcpu/admin-tailwind/referrals.blade.php`

- SweetAlert sites: 8.
- Native dialogs: `referralModal`.
- Popup titles: 'Updated!', 'Archive Referral?', 'Processing...', 'Archived!'.

### `resources/views/wcpu/admin-tailwind/register-user.blade.php`

- SweetAlert sites: 1.
- Popup titles: 'Confirm User Registration'.

### `resources/views/wcpu/admin-tailwind/user-logs.blade.php`

- SweetAlert sites: 0.
- Native dialogs: `activityModal`.

### `resources/views/wcpu/admin-tailwind/users-table.blade.php`

- SweetAlert sites: 24.
- Native dialogs: `userModal`, `editModal`.
- Popup titles: `${action} User`, `${action} ${department} Access`, 'Archive User', 'Archived!', 'Reset Password', 'Resetting Password...', 'Reject Password Request', 'Rejecting Request...', 'Request Rejected', 'Updated!'.

### `resources/views/wcpu/admin-tailwind/view-case.blade.php`

- SweetAlert sites: 2.
- Popup titles: 'Refer Case'.

### `resources/views/wcpu/case-profiles/index.blade.php`

- SweetAlert sites: 0.
- Native dialogs: `{{ $guardianDialogId }}`.

### `resources/views/wcpu/components/swd-dashboard-announcements.blade.php`

- SweetAlert sites: 1.
- Popup titles: 'Announcement Details'.

### `resources/views/wcpu/desk-officer/_case-details-modal.blade.php`

- SweetAlert sites: 0.
- Shared details/form content: typography, field spacing, row presentation and overflow rules.

### `resources/views/wcpu/desk-officer/archived-barangay-cases.blade.php`

- SweetAlert sites: 2.
- Popup titles: 'Are you sure you want to restore this case?', 'Delete archived case?'.

### `resources/views/wcpu/desk-officer/archived-referrals.blade.php`

- SweetAlert sites: 2.
- Native dialogs: `archivedReferralDetailsModal`.
- Popup titles: 'Delete archived referral?', 'Restore archived referral?'.

### `resources/views/wcpu/desk-officer/bcpc-case-monitoring-archived.blade.php`

- SweetAlert sites: 8.
- Popup titles: 'Restore Case Monitoring?', 'Restoring...', 'Restored!', 'Delete archived report?', 'Deleted!', 'Unable to Delete'.

### `resources/views/wcpu/desk-officer/bcpc-case-monitoring-form.blade.php`

- SweetAlert sites: 11.
- Popup titles: 'Successfully Sent to Admin', 'Saving changes...', 'Send to Admin again?', 'Sending to Admin...', 'Successfully Sent to Admin', 'Unable to Complete Request', 'Send to Admin?', 'Sending to Admin...', 'Sent Successfully!'.

### `resources/views/wcpu/desk-officer/bcpc-case-monitoring.blade.php`

- SweetAlert sites: 13.
- Native dialogs: `addMonitoringModal`.
- Popup titles: 'Success', 'Error', 'Validation Error', 'Sending to Admin...', 'Sent Successfully!', 'Archive Case Monitoring?', 'Archiving...', 'Archived!'.

### `resources/views/wcpu/desk-officer/calendar/index.blade.php`

- SweetAlert sites: 9.
- Native dialogs: `dateEventsModal`, `eventModal`, `conflictModal`, `eventDetailsModal`.
- Popup titles: 'Event Not Saved', 'Event Not Saved', 'Event Not Saved', 'Event Not Saved', 'Unable to Load Event', 'Delete Event', 'Delete Event', 'Unable to Delete Event', 'Unable to Delete Event'.

### `resources/views/wcpu/desk-officer/case-details.blade.php`

- SweetAlert sites: 1.
- Native dialogs: `referModal`.

### `resources/views/wcpu/desk-officer/components/notifications.blade.php`

- SweetAlert sites: 1.

### `resources/views/wcpu/desk-officer/officer-cases.blade.php`

- SweetAlert sites: 20.
- Native dialogs: `caseModal`, `referModal`, `sendCopyModal`, `editCaseModal`.
- Popup titles: 'Edit Case?', 'Updating Case', 'Success!', 'Success!', 'Error!', 'Referring Case', 'Success!', 'Error', 'Error', 'Sending Copy', 'Success!', 'Error', 'Error', `Preview Case #${caseNumber || ''}`, 'Print All Cases'.

### `resources/views/wcpu/desk-officer/officers-create.blade.php`

- SweetAlert sites: 7.
- Popup titles: 'Invalid Victim Age', 'Perpetrator Age Required', 'Abuse Explanation Required', 'Confirm Case Submission', 'Submitting Case...', 'Case Created Successfully!', 'Submission Error'.

### `resources/views/wcpu/desk-officer/partials/referral-details.blade.php`

- SweetAlert sites: 0.
- Shared details/form content: typography, field spacing, row presentation and overflow rules.

### `resources/views/wcpu/desk-officer/partials/referral-edit.blade.php`

- SweetAlert sites: 2.
- Popup titles: 'Unable to Update Referral', 'Unable to Update Referral'.

### `resources/views/wcpu/desk-officer/partials/swd-archive-interactions.blade.php`

- SweetAlert sites: 7.
- Popup titles: 'Referral Details', `${restore ? 'Restore' : 'Delete'} archived ${kind}?`, 'Report Restored', 'Unable to Restore', 'Processing...'.

### `resources/views/wcpu/desk-officer/quarterly-reports/_bcpc-result.blade.php`

- SweetAlert sites: 2.
- Native dialogs: `bcpc-report-result`.
- Popup titles: 'Generating Report...'.

### `resources/views/wcpu/desk-officer/quarterly-reports/_tables.blade.php`

- SweetAlert sites: 8.
- Popup titles: 'Processing...', 'Action Not Completed', 'Print Report?', 'Delete archived report?', 'Send report to Admin?', 'Archive Case Report?', 'Restore archived report?'.

### `resources/views/wcpu/desk-officer/quarterly-reports/create.blade.php`

- SweetAlert sites: 3.
- Native dialogs: `generated-report-result`.
- Popup titles: 'Generating Report...', 'Report Generated', 'Report Not Generated'.

### `resources/views/wcpu/desk-officer/quarterly-reports/index.blade.php`

- SweetAlert sites: 2.
- Popup titles: 'Print All Reports', 'No Reports to Print'.

### `resources/views/wcpu/desk-officer/quarterly-reports/show.blade.php`

- SweetAlert sites: 8.
- Popup titles: 'Print Report?', 'Send to Admin?', 'Sending to Admin...', 'Sent Successfully!', 'Restore archived report?', 'Print Report?'.

### `resources/views/wcpu/desk-officer/referral-details.blade.php`

- SweetAlert sites: 0.
- Shared details/form content: typography, field spacing, row presentation and overflow rules.

### `resources/views/wcpu/desk-officer/referral-form.blade.php`

- SweetAlert sites: 3.
- Popup titles: 'Confirm Referral', 'Success!', 'Error!'.

### `resources/views/wcpu/desk-officer/referrals.blade.php`

- SweetAlert sites: 9.
- Native dialogs: `referralDetailsModal`.
- Popup titles: 'Referral Updated', 'Unable to Update Referral', 'No Referrals to Print', 'Choose department to print', 'No Referrals to Print', 'Archive Referral?', 'Archived', 'Error'.

### `resources/views/wcpu/layouts/admin-tailwind.blade.php`

- SweetAlert sites: 3.
- Popup titles: 'Logout', 'Logging out...'.

### `resources/views/wcpu/layouts/desk-officer.blade.php`

- SweetAlert sites: 6.
- Native dialogs: `caseTypeModal`, `reportTypeModal`.
- Popup titles: 'Print Preview', 'Processing...', 'Password Reset Ready', 'Logout', 'Logging out...'.
