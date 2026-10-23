# cPanel deployment guide

## 1. Prepare an isolated installation

Create a new subdomain such as `services.your-domain.example`, or a new empty subdirectory. Do not extract over the campus portal or over an existing CivicFlow installation. Make a cPanel backup of any existing site before changing its files.

Use an SSL certificate valid for the actual installation address. PHP 8.3 is the tested runtime, with PHP 8.2 as the minimum. Select extensions PDO, PDO MySQL, Mbstring, Fileinfo, OpenSSL, cURL and DOM/XML. DOM enables structured HTML email sanitisation; if absent, the application falls back to escaped plain content. SQLite is optional for local evaluation and requires PDO SQLite.

Recommended initial PHP limits:

```ini
upload_max_filesize = 6M
post_max_size = 20M
max_file_uploads = 3
memory_limit = 128M
max_execution_time = 120
expose_php = Off
```

The application enforces a 5 MB limit per file, three files per upload, 20 files per request and 100 MB per uploading account. These are limits in `app/domain.php`; adapt them deliberately to your storage budget. No executable uploads are accepted. Files are downloaded as attachments, never served from their storage path.

## 2. Create the database

In cPanel Database Wizard, create a fresh MySQL/MariaDB database, a unique database user and a strong password. Add the user to that database with ALL PRIVILEGES so the installer can create tables and indexes. The database host is usually `localhost`; use the value from your host. The installer uses port 3306. If your provider uses a different port, adapt the installer configuration before setup; do not guess credentials.

Do not import `database/schema-mysql.sql` before the installer. The SQL files are schema references for review and managed migration, while the installer also creates roles, permissions, templates and starter settings.

## 3. Extract the release

Upload the release ZIP using File Manager. Extract into the document root selected for the domain. Turn on Show Hidden Files and verify the root `.htaccess` exists. The ZIP contains the application at its root; it is not an SQL backup of the campus portal.

The PHP account must be able to write `config/` and `storage/` and their children. Use account-owned directories, typically 0755 or stricter permissions compatible with your PHP-FPM setup. Private generated files are 0600. Never use 0777 as a routine fix. If the host runs PHP under a different identity, ask the host for the correct ownership/permissions.

Open the local `START_HERE.html` to find the setup key. The server blocks this file and `config/setup.key` from HTTP access.

## 4. Run setup

Visit the clean route `/install`. Enter:

- Exact HTTPS installation base URL, without `/install`, a trailing query or fragment.
- Operating organisation and public support email.
- Fresh database credentials, including cPanel prefixes.
- Your administrator details and a password of 12–72 bytes containing letters and numbers.
- Require HTTPS for all live access.
- Leave demonstration mode off for live use.

For an installation at `https://example.org/civicflow`, use that full URL as the base. `.htaccess` uses relative front-controller rewrites and does not require a hard-coded RewriteBase.

After success the installer writes `config/local.php` and `config/installed.lock`. It cannot be rerun while installed. A failed setup after schema creation may leave tables: use a new disposable empty database for the retry, or inspect and remove only tables from that failed new installation. Never delete tables from a database in service.

## 5. Configure the workspace

1. Confirm the organisation name, support contact and branding.
2. Configure departments and active officers, including department allocation.
3. Review service requirements, published fees and calendar-hour targets. Activate each service only when ready. Live starter services are inactive and fees are zero.
4. Review default roles. Create additional roles where review and approval must be separated.
5. Enrol the administrator in Account security, save the recovery codes, then require staff MFA if appropriate.
6. Configure SMTP, SMS and optional AI through Configuration. Credentials are encrypted in database settings using the installation key.
7. Review public privacy text, FAQs, announcements and template wording against actual operational policies. Update operator-specific privacy/retention text before launch.

## 6. Schedule work

In cPanel Cron Jobs choose **every five minutes** and enter the command only in the command box, using the correct absolute path. For example:

```bash
/usr/local/bin/php /home/CPANEL_USER/public_html/civicflow/cron/run.php
```

Equivalent crontab notation:

```text
*/5 * * * * /usr/local/bin/php /home/CPANEL_USER/public_html/civicflow/cron/run.php
```

Some hosts require a versioned executable such as `/opt/cpanel/ea-php83/root/usr/bin/php`. Confirm the actual PHP 8.2+ command with the host and enable the same extensions for CLI. Configuration shows the actual application path. The scheduled job processes up to 20 deliveries per run; the manual web control uses up to five to limit request duration. The job is CLI-only. Do not expose it as a secret web URL.

The worker escalates overdue requests, processes up to 20 email/SMS records and records the last run. One invocation can take longer if a provider is slow. A private file lock prevents overlapping delivery workers on the same installation. Retry claiming is conditional in the database as well.

## 7. Prove the deployed installation

Use your own test accounts and a real recipient you control:

1. Open `/login`, `/services` and `/assistant`; confirm assets load and links contain no `.php`.
2. Request `/index.php` and `/login.php`; expect canonical clean-address redirects. Try `/config/setup.key`, `/config/local.php`, `/storage/` and `/app/`; expect access denied.
3. Register a citizen, verify its email, submit a complaint, create a permit with a harmless PDF and confirm a second citizen cannot view either request or its attachments.
4. Sign in as an officer from the correct department, start review, request information and complete a decision. Confirm an officer from another department cannot read the record.
5. Confirm internal notes and staff attachments are absent from citizen screens and direct downloads.
6. Use a reference plus private tracking code, and verify an approved permit record without applicant details.
7. Confirm cron's last-run time advances and a configured SMTP message is accepted. Check receipt in the destination mailbox. Test SMS and an AI question separately if enabled.
8. Check desktop, narrow mobile, keyboard navigation, document printing and your current browsers. These visual checks remain hosting acceptance tasks.
9. Create a backup, restore it to a private staging location and repeat sign-in and document download.

## Troubleshooting

| Symptom | Check |
|---|---|
| `/install` or `/login` returns hosting 404 | Root `.htaccess`, `mod_rewrite`, correct document root and AllowOverride support |
| Hosting 500 before the installer | PHP version and enabled extensions; Apache log; whether Options/Headers directives are permitted by the host |
| Application error reference | Inspect `storage/logs/application.log` privately for that reference; do not expose errors to visitors |
| Database connection fails | Full prefixed database/user names, database user privileges, host and password; use a new empty database |
| Configuration cannot be written | PHP account ownership and writable `config/` and `storage/`; do not make the whole tree world-writable |
| Redirect loop | Exact `base_url` and actual HTTPS status; use the host's native HTTPS configuration. Trusting arbitrary proxy headers is intentionally not enabled |
| Upload fails | PHP post/upload limits, available disk space, actual PDF/JPG/PNG MIME type and application quota |
| Outbox says Configuration required | Enable and test that channel, then explicitly requeue eligible held messages |
| SMTP fails | Correct TLS mode/port, sender authorisation, credentials, DNS and outbound port access; keep certificate verification enabled |
| AI falls back to service guide | Responses-compatible model available to your account, key, credit, cURL and outbound HTTPS |
| Cron not updating | Absolute file path, CLI PHP version/extensions, cPanel cron environment and application logs |
| Need to change domain | Back up, edit `base_url` in the private `config/local.php`, confirm HTTPS and relocate the whole application consistently |

For HTTPS terminated by a reverse proxy, configure a trusted proxy integration at the server/application boundary. The packaged application reads native `HTTPS=on`; it does not trust client-supplied forwarding headers.

## Release cleanup and moving hosts

Keep a copy of the ZIP and setup instructions offline. Delete the ZIP and `START_HERE.html` from the public hosting directory after setup. The root rules also deny documentation, test, schema and vendor paths; those private directories can remain or documentation/tests can be removed from a live server. Keep all nested protection files.

To move an existing installation, back up and restore the database, `config/local.php`, `config/installed.lock` and `storage/uploads/` together. Keep the same application key or encrypted integration credentials and MFA secrets cannot be decrypted. Use fresh sessions/rate-limit files on the new host, update the base URL, verify account ownership and cron paths, then test before changing live traffic. This first release does not supply automatic schema upgrades for future releases.

## Primary implementation references

- cPanel File Manager: https://docs.cpanel.net/cpanel/files/file-manager/110/
- cPanel Database Wizard: https://docs.cpanel.net/cpanel/databases/database-wizard/120/
- Apache URL remapping: https://httpd.apache.org/docs/2.4/rewrite/remapping.html
- Apache rewrite flags: https://httpd.apache.org/docs/2.4/rewrite/flags.html
- PHPMailer: https://github.com/PHPMailer/PHPMailer
- OpenAI Responses API: https://platform.openai.com/docs/api-reference/responses
- Termii Messaging API: https://developers.termii.com/messaging-api

Host screens and provider settings may differ. Use the current values from your own accounts.
