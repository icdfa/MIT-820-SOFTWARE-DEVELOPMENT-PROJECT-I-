# Release validation record

**Release:** CivicFlow 1.0.4  
**Validation date:** 19 October 2026  
**Result:** 267 automated checks passed: 124 workflow/security HTTP assertions, 13 fresh live-mode HTTP assertions, 5 installer regressions, 113 landing/dataset HTTP assertions and 12 carousel interaction checks.

Machine-readable results are included in `tests/acceptance-sqlite.json` and `tests/acceptance-live.json`. The executable standard-library Python harness is `tests/http_acceptance.py`. It creates an isolated copy and synthetic records; those test databases and session files are not part of the release ZIP.

## Executed checks

| Area | Evidence |
|---|---|
| PHP | All 26 PHP files, including bundled library files, passed native PHP syntax checks |
| JavaScript | `assets/js/app.js` and `assets/js/landing.js` passed Node syntax checking |
| Runtime | PHP 8.3.6 with PDO SQLite, Mbstring, Fileinfo, OpenSSL, cURL and DOM |
| Installation | Pre-database installer, incorrect/missing setup key rejection, schema creation, first administrator, installation lock and fresh live-mode setup |
| Public and workspace screens | Installer, dashboard, directory/service details, requests, reports, admin pages, profile/security, assistant, help, privacy and about rendered through HTTP |
| Citizens | Registration, forced citizen role despite a forged role field, submission, drafts, receipt, information reply, withdrawal, reopening and feedback |
| Permits | Supporting-document requirement, valid PDF submission, review/approval, printable record and limited public verification |
| Scope | Second citizen and other-department officer denied direct case/document access; unpublished drafts denied to staff |
| Action rights | Citizen decision/admin POST denial; custom role review without decisions or assignment; auditor and communications restrictions |
| Files | MIME-spoofed PHP rejected; private storage URL denied; owner/staff downloads checked; internal-note document hidden from citizen and denied on direct download |
| Workflow integrity | Invalid transition and stale-version update refused; deadline/routing set; repeated escalation does not duplicate the event |
| Tracking | Reference plus private code succeeds; incorrect code fails; applicant, documents and internal notes absent from public progress |
| Communications | Held external delivery without credentials, retry gating, cancellation, compose, escaped placeholders and unsafe HTML stripping |
| SMTP failure handling | Controlled loopback connection refusal, queued retry/backoff and stop after five attempts; no actual external message sent |
| Secrets | SMTP credential and MFA secret encrypted at rest; passwords hashed; sensitive authentication message content hidden in outbox |
| Account security | Single-use email verification/reset tokens, session invalidation after reset, MFA, eight recovery codes displayed once, code replay denied and device revocation enforced |
| Output and request safety | CSRF rejection, malformed query-array rejection, escaped script content, unique form IDs and CSV formula protection |
| Error hygiene | The completed acceptance run recorded no unexpected application warning or exception |
| Live-mode defaults | Only the chosen administrator exists; no sample cases; services inactive with zero fees; reviewed service can be published and a citizen can submit |

## MariaDB compatibility validation

MariaDB **10.11.14** executed the final 21-table MySQL schema, indexes and synthetic fixture inserts in native bootstrap mode. Representative officer-assignment, department reporting and role-permission queries also executed under strict SQL modes, including `ONLY_FULL_GROUP_BY`.

A complete HTTP/PDO-MySQL acceptance run was **not** completed here: the local environment prohibited the Unix socket required for the MariaDB server listener. Bootstrap execution validated native schema/query compatibility, rather than a live database connection or a hosted MySQL installation. Re-run the deployment checklist against the actual cPanel database.

## Apache configuration validation

Apache **2.4.58** accepted the release `.htaccess` directives and patterns in directory context with `mod_rewrite`, `mod_headers`, directory and authorisation modules loaded: **Syntax OK**. The rules also preserve narrowly scoped certificate-validation files.

The HTTP suite tested clean routes and private-path denial through the local PHP router. Full Apache HTTP rewrite behaviour, including the target host's subdirectory configuration and AllowOverride policy, still needs the documented hosting checks. No browser visual automation was available for this build; desktop/mobile layout, keyboard and print inspection remain manual acceptance tasks.

## Checks requiring the deployment environment

- Real cPanel database connection and end-to-end operation on that database.
- Actual Apache/LiteSpeed clean redirects, denied private paths and valid TLS.
- Real SMTP acceptance and inbox receipt with the operator's sender/domain.
- Termii sender/route acceptance and handset receipt.
- OpenAI model/key access, live response and provider-outage behaviour in that account.
- Cron environment, ongoing log rotation and operational monitoring.
- Browser/device/accessibility review, upload malware controls and intended workload testing.
- A complete backup restoration rehearsal and operator-specific privacy/retention approval.

No live provider credentials, production citizen data, independent penetration-test results or load-test results were supplied or fabricated. The release is a deployable application package with documented acceptance evidence; operational approval belongs to the target deployment.

## Installer repair and interface revision

See CHANGELOG.md and tests/acceptance-installer.json. Five additional installer scenarios verify automatic folder recreation, blocked cache/mutex/session handling and a refused PDO-MySQL connection. Release 1.0.2 passed 142 checks; release 1.0.3 adds the landing/dataset and carousel suites listed below. Browser appearance and target-host database acceptance remain deployment checks.

## Landing and demonstration dataset checks

`tests/acceptance-landing-demo.json` records 113 passing HTTP assertions. The harness creates an isolated live-mode installation, adds an existing citizen/request/upload, and then explicitly loads the dataset. It verifies 26 normal account sign-ins and populated workspaces; six-role coverage; all eight case stages; private evidence; department and owner access; loader authorization and CSRF; strong password/confirmation validation; conflict rollback; idempotent repeat loading; preserved operator data/configuration/fees; reserved contact addresses; cancelled delivery examples; CSV formula protection; valid readable PDFs; public tracking; and demonstration approval labelling. No unexpected application warning or exception was recorded.

`tests/acceptance-carousel.json` records 12 passing JavaScript interaction checks in a minimal DOM fixture. They cover rotation, wraparound, manual controls, slide selection, announcements, keyboard controls, pause/play, hover/focus, page visibility, reduced motion and non-landing pages. This is a unit fixture, not a browser rendering test.

The role/dataset harness uses Python's pypdf package to parse generated private PDF fixtures. The core HTTP harness uses Python's standard library. Reproduce from the extracted source with:

```
python3 tests/http_acceptance.py --php /path/to/php
python3 tests/http_acceptance.py --php /path/to/php --live
python3 tests/installer_regression.py --php /path/to/php
python3 tests/landing_demo_acceptance.py --php /path/to/php
node tests/carousel_interactions.cjs
```

No new schema table is introduced. The existing 21-table schema and PDO helper API are retained. The new loader was exercised end-to-end using SQLite; a complete 1.0.3 PDO-MySQL run and target-host/browser review remain pending deployment checks.

## Portal display update 1.0.4

The landing/dataset suite checks the absence of the large homepage banner and repeated footer label while prepared records remain fully functional. About retains the prepared-data note and sample approval verification retains its legal-effect notice. The package overlay check verifies preservation of configuration, database, uploaded evidence, logs, sessions and the installation lock.
