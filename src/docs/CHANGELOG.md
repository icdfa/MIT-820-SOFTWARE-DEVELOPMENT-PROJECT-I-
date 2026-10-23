# Release 1.0.4 — 19 October 2026

- Remove the large demonstration banner from the public homepage and the repeated demonstration-mode text from the shared footer.
- Keep the existing image slider, service content, populated database records, account access and all citizen/staff workflows.
- Retain factual information about prepared project records in About and relevant approval/verification documents. No data is reclassified as genuine government activity.
- Preserve existing configuration and private files. No SQL import, schema migration, reinstall or data reload is required.
- Refresh versioned assets and release instructions for the existing-site update.

# Release 1.0.3 — 19 October 2026

- Add a public landing page with three original locally hosted service illustrations, accessible carousel controls, service search, published service cards, private-code tracking, department links, process steps, notices, FAQs and account actions.
- Preserve the conventional role-scoped portal and the installer fixes from 1.0.2. Versioned assets request the new interface after upload.
- Add an explicit primary-administrator-only presentation loader with CSRF protection, a chosen account password, transaction rollback, private file cleanup, loader mutex, conflict checks and a completion manifest that prevents duplicate loads.
- Add 26 fictional accounts and 36 complete cases, covering all six roles through the existing administrator, nine services, four departments and all eight lifecycle stages. Include 27 illustrative private PDFs, feedback, in-app notifications, dated conversations and audit entries.
- Add 34 cancelled outbox examples with zero delivery attempts and no fictitious provider receipts. Use reserved test addresses and blank phone numbers. Existing delivery configuration is retained.
- Explicitly mark the installation as a demonstration portal after loading; sample approval and verification records have no legal effect. Existing accounts, cases, uploads, service fees and administrator credentials remain in place.
- Validation: 255 HTTP assertions plus 12 carousel interaction checks passed (267 checks). All 26 PHP files and both JavaScript files passed syntax checks. Browser rendering and target-host MySQL acceptance remain deployment checks.

# Release 1.0.2 — 19 October 2026

- Automatically prepare missing private storage folders and deny files before sessions, cache or installer writes.
- Validate readiness before rate limiting and mutex writes. Failed storage or mutex checks now show the exact relative path requiring correction.
- Show specific database connection, privilege and schema error guidance without echoing database passwords.
- Add a configurable database port and retain non-secret form values after a failed setup attempt.
- Check DOM/XML and cURL requirements before installation.
- Replace the public sidebar and marketing homepage with a standard masthead, service navigation and service action links.
- Use a flat institutional workspace design: white navigation, navy header, readable Arial text, bordered tables and larger forms. Role and record permissions are unchanged.
- Add versioned asset URLs so the new interface is requested after the patch.
- Validation: 124 main HTTP assertions, 13 fresh live-mode assertions and 5 installer regression scenarios passed (142 total).

The prior private-storage failure was reproduced against release 1.0.1. Tests of missing folders, a blocked cache path and a blocked mutex produced its generic HTTP 500 response. The new installer succeeds after rebuilding missing folders, or returns a specific setup error when the hosting permissions need correction.

The cPanel host's exact error-log entry for reference d0690cdebe was not available. A read-only request to its installer confirmed a failed Writable private storage readiness check. No hosted database, configuration, password or citizen record was changed during diagnosis.

Browser layout acceptance and a complete HTTP/PDO-MySQL run remain target-host checks. The packaged regression suite verifies PDO connection-failure handling; the local database listener cannot start because Unix sockets are restricted in the development environment.

# Release 1.0.1 — 19 October 2026

- Preserve required private storage directories in the source ZIP through per-directory access-denial files.
- Bound passwords to 72 bytes to avoid bcrypt input truncation and reject longer values consistently.
- Refresh the acceptance results: 124 workflow/security assertions and 13 fresh live-mode assertions passed.
- Add the separate academic submission pack, including the five-chapter report and editable presentation.

The included test JSON describes the local SQLite HTTP runs. Target-host acceptance and live-provider delivery remain deployment checks.
