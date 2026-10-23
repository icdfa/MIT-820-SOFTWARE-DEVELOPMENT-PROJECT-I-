# Workspace administration and operations

## Daily service work

Review the incoming and overdue queues. Confirm unassigned requests have an active, appropriately authorised officer in the correct department. Officers record an explanation with each review, information request and decision. Department heads can export their department's records; administrators manage the whole workspace.

Published targets run in calendar hours. Waiting for information does not pause the clock. Reopening a resolved complaint starts a fresh target and clears prior feedback. Escalation is recorded once per open cycle when cron runs; it is not a substitute for a human response.

Requests and audit records are retained rather than deleted through the application. Deactivate services and suspend accounts to preserve history. This release has no automated retention/deletion engine. The operator must define and implement its retention, erasure and access-request process for the deployment.

## Permission governance

Only authorised administrators should manage roles. A role's permissions affect both navigation and direct actions. Default department scope covers every submitted request in the assigned department, rather than only assigned requests. “Assigned to me” is a convenience filter.

Create a custom intake role with department access and `requests.review` but no `requests.decide` to separate preparation from decisions. Give the approving role `requests.decide`. Assignment, routing, reports, exports, documents, communications and configuration have independent permissions. Cross-department routing also requires organisation scope.

Additional roles combine permissions. An account with any department role needs a department. Citizen accounts cannot receive additional staff roles. The primary administrator role is protected. Changes to accounts and roles revoke affected sessions. At least one active primary administrator must remain; an administrator cannot remove its own access through account administration.

Public registration creates citizens only. Staff are created through People and roles. Review all additional permissions before assigning them. `users.manage` without `roles.manage` cannot assign privileged/additional roles or modify accounts that already hold them.

## Account security and recovery

Every account can enable authenticator-based two-factor login. Setup shows a manual secret and authenticator link. Confirm a current code to enrol, then save eight single-use recovery codes. Enrolment revokes other authenticated sessions. A new sign-in then needs the password plus a current authenticator code or unused recovery code.

Account security lists device sessions and permits revocation of other devices. Password changes and resets revoke old sign-ins. Sessions also expire after 30 minutes of inactivity. An email reset does not disable MFA. A user who loses both the authenticator and all recovery codes needs a controlled operator recovery process; there is deliberately no public MFA bypass.

Require staff MFA only after enrolling the administrator and storing recovery codes. Staff without MFA are directed to Account security before protected workspace access.

Email-verification links expire after 24 hours and are single-use. Password-reset links expire after one hour and are single-use. Resending invalidates earlier verification links. Authentication message bodies are hidden in the outbox interface. Email verification confirms an email address; it is not government identity verification. Registration and basic request access remain available when SMTP is not configured.

## Notifications and the outbox

In-app updates are immediate. Routine email and SMS follow account preferences. Authentication messages are sent to the registered email for verification/recovery. The compose screen targets active registered users and respects routine preferences; it is not an unrestricted address mailing tool.

| State | Meaning and action |
|---|---|
| Configuration required | Channel disabled. Configure it first, review whether the held notice is still useful and explicitly requeue |
| Pending | Ready for the next delivery worker; cancellation is available before processing |
| Processing | Worker has claimed the message; do not retry or cancel while sending |
| Retry | Failed attempt; next attempt uses backoff, capped at one hour |
| Failed | Five failed attempts; investigate configuration, then explicitly requeue if still appropriate |
| Sent | Provider accepted the message; destination receipt is not confirmed |
| Cancelled | Operator cancelled queued delivery |

The outbox supports recipient/subject search, channel/state filters, 50-record pages and detailed results. Provider errors shown to staff do not reveal SMTP passwords or API keys. Retry/cancel actions are audited. Existing messages retain their rendered template copy when the template changes.

Delivery is best effort with retries. A provider may accept a message immediately before a process crash, leading to another attempt during recovery. This release does not promise exactly-once delivery or recipient delivery receipts. Stale processing records become retry-eligible after ten minutes.

## Provider configuration

**SMTP:** Set the authorised hostname, port, sender, username and password. Choose STARTTLS (usually 587) or implicit TLS (usually 465), according to the mail provider. Check SPF/DKIM and outbound ports with the host. Do not disable TLS certificate checks. Test with a recipient you control.

**SMS:** Set the HTTPS API base URL shown in your Termii account, API key, approved sender and generic or DND route. Hosts must belong to `termii.com` or `termii.ai`. Nigerian numbers beginning with 0 are normalised to country code 234. Confirm account balance, sender approval, route and applicable consent before enabling. An accepted API response does not prove handset delivery.

**AI:** Set an available Responses-compatible OpenAI model and API key. AI sends the user's chosen question and public service/FAQ facts, with response storage disabled. It has no tools to change requests, decide applications or retrieve private account data. The interface offers a local service guide when AI is disabled, not selected or unavailable. AI may still produce errors: use department messages for individual decisions or authoritative clarification.

Credentials are stored encrypted under the application key in `config/local.php`. Blank fields preserve existing credentials; explicit remove checkboxes clear them. Never include that file in public support tickets or source control.

## Templates and public content

The template editor supports headings, paragraphs, lists, emphasis, links and tables. Unsafe elements, event handlers and unsupported attributes are stripped server-side. The visual editor synchronises with the HTML source. Plain-text alternatives are stored as well. Preview saved templates before use.

Placeholders: `name`, `title`, `message`, `reference`, `status`, `service`, `department`, `link`, `app_name`, written as `{{name}}`. Use `service_update` for workflow updates, `welcome` for registration and `general_notice` for general notices. Missing values render blank. Placeholders are escaped in HTML. Automatic updates continue as plain messages if their template is disabled.

Maintain FAQ answers and announcements in Knowledge centre. Review public operator details and privacy text whenever service operations change. The supplied content describes software behaviour and must be adapted to the authority's actual procedures.

## Backup and restoration

Use cPanel full-account backup or a protected offsite backup covering these together:

- MySQL/MariaDB database, or a consistent SQLite backup for evaluation installations.
- `storage/uploads/` and its access-protection file.
- `config/local.php` and `config/installed.lock`.
- The matching application release and branding assets.

Back up daily at minimum if records change daily, and before changes to the application. Encrypt backups and restrict access. Define retention based on the operator's policy. Session/cache files are replaceable; do not restore old signed-in sessions onto a new host.

For a restoration rehearsal, create a separate private staging domain/database, restore files and records together, update `base_url`, disable external channels initially, and verify login, MFA, document access, role boundaries and cron. Never test a restoration over the only live copy. A backup without a successful restoration check is not sufficient evidence of recoverability.

## Maintenance

Review `storage/logs/application.log`, outbox failures, disk usage, expired account tokens and inactive device sessions. Set host-managed log rotation and monitor HTTPS, errors, database health and cron. Cache/session cleanup is not automated by the application; use the hosting retention tools and avoid deleting live lock files while jobs run.

Keep PHP and the bundled PHPMailer dependency maintained. Test dependency upgrades against the acceptance suite and a staging SMTP account. Use staged, backed-up deployments; no automatic updater or multi-server storage configuration is supplied.

For capacity growth, use MySQL/MariaDB, monitor real traffic and storage, and benchmark before admitting the intended workload. Multi-node deployment needs shared private uploads, coordinated rate limiting/session storage and deployment-specific locking; the packaged configuration targets one cPanel application instance.
