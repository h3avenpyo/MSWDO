# MSWDO user activity logs

The administrator sidebar links to `/admin/activity-logs`. Both the list and detail
routes check the current MSWDO database account for the active `admin` role; a session
role alone does not grant access. There are no create, edit, delete or clear routes.

Apply the additive migration before serving requests:

```sh
php artisan migrate --path=database/migrations/2026_11_10_000001_create_mswdo_activity_logs_table.php
```

## Scope and integration

- `MswdoActivityLogger` only accepts sessions with `auth_source=mswdo`, a current
  `App\Models\User` account, and a recognized `UserRole`. SWD and BCPC officers and
  their administrators authenticate through WCPU and are excluded, even when their
  numeric account IDs overlap MSWDO IDs.
- Existing WCPU Spatie logging and social-case/senior recent-activity feeds are
  preserved. They are not copied into this table or trusted as audit input. Their
  clear-history actions cannot clear the central audit trail.
- `LogsMswdoActivity` records creation on applicable domain models. Its Eloquent
  builder records updates and deletions, including existing bulk query operations,
  without also recording the same model update through an observer. Update/delete
  auditing runs in the same database transaction as the write.
- Password and email-code login and centralized logout are recorded explicitly.
  The web middleware records successful named print/export/download/send/release
  actions only when a model/authentication log has not already covered the request.
- Old/new values use a strict workflow-field allowlist. Names of beneficiaries,
  contact details, IDs, medical/case narratives, passwords, tokens, attachments,
  arbitrary JSON and signatures are excluded. An action can therefore have no
  displayable field differences. User name/role are actor snapshots required for
  accountability, retained even when that account changes or is deleted.
- Times are stored in the existing application timezone (UTC) and displayed and
  filtered in Philippine time. Date boundaries are inclusive local calendar dates.

For new domain models, add `LogsMswdoActivity` only if they belong to MSWDO.
Do not add it to WCPU models or activity/history feed models. Raw `DB::table`
writes, `toBase()` writes, upserts, quiet inserts and database cascades bypass
Eloquent auditing; new important write paths should use the audited model builder
or call the service explicitly within their transaction. Existing raw writes in
these controllers maintain document counters, which are not audited records.

The feature begins tracking when installed; it does not reconstruct historical
activity. Database administrators retain direct database maintenance privileges.

## Verification

```sh
php artisan test tests/Feature/MswdoActivityLogTest.php
```

The suite uses isolated SQLite memory tables and does not migrate or truncate the
configured MySQL database. It covers authorization, source exclusion, sensitive
field exclusion, CRUD, archiving/restoring, bulk operations, transaction rollback,
duplicate suppression, authentication, date filters, pagination and escaped output.
