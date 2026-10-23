# CivicFlow

**Smart Government Service Delivery Platform · Version 1.0.4**

CivicFlow connects citizens with service departments through online complaints, permit applications, status tracking and guided assistance. Its signed-in citizen workspace adapts the student portal shell from the supplied campus template. It uses a new CivicFlow emblem, green identity and service-specific screens.

Open **START_HERE.html from the downloaded ZIP on your computer** for your setup key and cPanel instructions. This file is deliberately blocked over the web.

## Updating the existing website

Use **CivicFlow_Portal_Display_Update_v1.0.4.zip** for an installed portal. See `docs/UPLOAD_PATCH_1.0.4.md`: extract the update in the document root, keep the existing configuration and database, then open **Administration → Demonstration data** if you want the complete fictional presentation dataset. No SQL import or reinstall is required.

## Install on cPanel

1. Use a new subdomain or empty directory. Keep your existing campus portal separate.
2. Select PHP 8.3 or a compatible newer supported release; minimum PHP 8.2. Enable PDO MySQL, Fileinfo, Mbstring, OpenSSL, cURL and DOM/XML. Apache 2.4 with `mod_rewrite` and `.htaccess` overrides is required. MySQL 8+ or MariaDB 10.6+ with InnoDB is recommended. PHP 8.3 and MariaDB 10.11 were used for local checks.
3. Create a new database and a database user in cPanel. Add the user to that database with ALL PRIVILEGES. Use the full names, including cPanel prefixes.
4. Upload the ZIP to the domain document root. Enable **Show Hidden Files** in File Manager, extract it and confirm that `.htaccess`, `index.php`, `app/`, `assets/`, `config/` and `storage/` are in the document root. No parent folder needs to be moved.
5. Enable SSL and open `https://your-domain.example/install`. For a subdirectory use the complete address, such as `https://your-domain.example/services/install`, and enter `https://your-domain.example/services` as the installation URL.
6. Enter the private setup key, organisation, database details and your own administrator account. Keep **demonstration mode off for live use**. The installer creates schema and starter configuration; do not import SQL first.
7. Sign in, configure departments and officers, review service requirements, fees and targets, then activate services. Live installation creates no sample citizens or requests. Starter services are inactive with zero fees until reviewed.
8. Configure SMTP, SMS and AI if needed. Add the scheduled job from Configuration to cPanel Cron Jobs, normally every five minutes. Test a real notification on your own account.
9. Remove `START_HERE.html`, the uploaded ZIP and any unneeded test/documentation files from the server after keeping a local copy. Keep `config/local.php`, `config/installed.lock`, private storage and all `.htaccess` files.

The browser installer locks itself when complete. There are no preset administrator credentials. Do not remove the installation lock to “repair” an existing installation.

## Included capabilities

| Area | Delivered functionality |
|---|---|
| Public landing | Three-image slideshow with accessible controls, service search, private-code tracking, department directory, notices, FAQs and account links |
| Presentation dataset | Administrator-only additive loader: 26 accounts, 36 cases, all six roles, private PDFs, feedback and cancelled outbox examples; data-origin notes in About and relevant approvals |
| Citizen workspace | Registration, sign-in, email verification, password reset, profile and delivery preferences, authenticator MFA, recovery codes and device revocation |
| Requests | Complaint and permit forms, drafts, document upload, private receipts, searchable history, citizen replies, withdrawal, complaint reopening and service feedback |
| Back office | Department queues, automatic officer assignment, controlled status transitions, information requests, decisions, cross-department routing with permission, internal notes and private staff documents |
| Permissions | Six built-in roles, custom role editor, additional staff roles, separate action rights, department/organisation scope, server-side enforcement and session revocation on permission changes |
| Tracking | Request reference plus private code; public permit-record verification without applicant details |
| Communications | In-app notices, filtered and paginated email/SMS outbox, message details, retry/cancel, controlled compose, HTML/plain-text templates and sanitised visual editor |
| Automation | SLA targets, one-time overdue escalation, scheduled delivery, retry backoff, five-attempt limit and worker locking |
| Oversight | Scoped operational reports, CSV export with formula protection, print-ready receipts/approvals and recorded audit actions |
| Assistance | Local FAQ service guide; optional OpenAI Responses integration grounded in public service facts with explicit user choice and fallback |
| Operations | New-install wizard, MySQL/SQLite schema references, encrypted provider credentials, clean URLs, local assets and cPanel deployment guide |

## Accounts and roles

| Role | Default scope and responsibilities |
|---|---|
| Citizen | Own submitted requests and drafts, documents, replies, withdrawal, reopening and feedback |
| Service officer | Requests in assigned department; review, decisions, assignment, citizen messages and internal notes |
| Department head | Officer capabilities plus department CSV export |
| Oversight auditor | Read-only submitted requests across departments, reports, CSV and audit trail; no attachment downloads or internal notes by default |
| Communications officer | FAQs, announcements, templates, compose and delivery outbox; no request workspace access |
| Administrator | Full workspace configuration and permission management |

The role editor defines the current permissions. Changes invalidate affected sessions. Public registration always creates a citizen. Citizen roles cannot acquire staff permissions or additional staff roles. Custom staff roles can separate review from final decisions. Unsubmitted drafts remain private to their owner, including against direct staff URLs.

## Structure and documentation

- `index.php`: front controller; all public links use extension-free routes.
- `app/`: authentication, permissions, workflows, pages and integrations.
- `assets/`: adapted portal styles, local JavaScript and CivicFlow SVG emblem.
- `config/`: installer-generated secrets and installation lock; blocked from web access.
- `storage/`: private database for SQLite, uploads, sessions, rate-limit state and logs.
- `cron/run.php`: command-line scheduled workflows; HTTP access denied.
- `database/`: schema references; the installer is the normal setup route.
- `docs/DEPLOYMENT.md`: detailed hosting, migration, troubleshooting and verification.
- `docs/OPERATIONS.md`: administration, delivery, backup and recovery.
- `docs/USER_GUIDE.md`: citizen and staff instructions.
- `docs/REQUIREMENTS.md`: requirements, acceptance matrix and operating boundaries.
- `docs/ARCHITECTURE.md`: data relationships and workflow diagrams.
- `docs/GROUP_1_PLAN.md`: team allocation and project schedule.
- `docs/VALIDATION.md`: actual test results and checks still needed on hosting.
- `THIRD_PARTY.md`: template provenance, PHPMailer source and license.

## Local evaluation and automated checks

With PHP and PDO SQLite enabled:

```bash
php -S 127.0.0.1:8080 router.php
```

Open `http://127.0.0.1:8080/install`, choose SQLite and do not require HTTPS for this local-only evaluation. The built-in server is for development. For acceptance checks, Python 3 uses only its standard library:

```bash
python3 tests/http_acceptance.py --php /path/to/php
python3 tests/http_acceptance.py --php /path/to/php --live
```

The tests create a separate temporary application and run their own loopback web server. Their synthetic accounts and passwords are confined to that copy. Do not run the optional MySQL test argument against an existing database; it requires a disposable empty database named by `--db-name` and a local test-only root account.

## Operating boundaries

This package supplies working application code and deployment materials. Live hosting, successful provider delivery, accessibility across actual devices, restoration and capacity require checks in the target environment. It has not been independently penetration-tested or legally certified. Configure the authority's actual services, privacy/retention policy and approval conditions before admitting real citizen records.

Payments, government identity verification, statutory integrations, digital signatures, antivirus scanning, public-facing malware scanning and SMS delivery receipts are not implemented. Permit downloads are printable application approval records; their legal effect depends on the issuing authority. There is no claim of official government affiliation. The original academic scope did not include payment collection.
