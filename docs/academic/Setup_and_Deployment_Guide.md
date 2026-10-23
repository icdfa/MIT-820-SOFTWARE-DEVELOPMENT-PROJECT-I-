# Setup and Deployment Guide

New cPanel installation configuration and verification

Group 1   MIT 820   Application 1.0.4   Document 1.1   23 October 2026

This guide prepares a new CivicFlow 1.0.4 installation on cPanel or a compatible PHP host. Upload only the deployment ZIP inside this submission pack to the selected web directory. Do not upload the full academic submission ZIP as the website. Keep the campus portal and existing databases separate.

## 1 Files and prerequisites

| Requirement | Configuration |
| --- | --- |
| Location | New subdomain or empty directory with a valid TLS certificate. |
| Runtime | PHP 8.3 is the tested runtime; minimum 8.2 with a compatible maintained version. |
| Extensions | PDO MySQL, Mbstring, Fileinfo, OpenSSL, cURL and DOM/XML. PDO SQLite is optional for local evaluation. |
| Web server | Apache 2.4 or compatible hosting with rewrite and allowed .htaccess overrides. |
| Database | Fresh MySQL 8+ or MariaDB 10.6+ database, InnoDB and a dedicated database user. |
| Private storage | Writable account-owned config and storage directories; private denial files retained. |

The corrected ZIP retains uploads, cache, sessions and logs through protection files inside each folder. Do not delete those files or install over an existing application. No default administrator account, password or production database is supplied.

## 2 Create the domain and database

- Select the new subdomain or empty directory in your hosting account. Enable its SSL certificate.

- Select the required PHP version and extensions for both web and scheduled CLI operation.

- Open cPanel Database Wizard, create a fresh database and a dedicated user, and add the user with ALL PRIVILEGES on that database.

- Keep the full cPanel-prefixed database name and username and the chosen password ready. Use the host name supplied by your provider, normally localhost.

The browser installer uses database port 3306. If your provider uses a different port, adapt the configuration deliberately before setup. Do not import the schema reference SQL before running the installer, because the installer creates both tables and seed configuration.

## 3 Extract and run the installer

- Upload CivicFlow_cPanel_Complete.zip from 07_Source_and_Deployment.

- Extract it directly into the selected domain document root. Enable Show Hidden Files and confirm .htaccess sits beside index.php, app, assets, config and storage.

- Open START_HERE.html from the extracted ZIP on your computer and copy the private setup key. The file is intentionally blocked over the web.

- Visit https://your-domain.example/install, replacing the example address with your actual domain.

- Enter the setup key, organisation, public support email, exact installation base URL, fresh database details and your administrator account.

- Require HTTPS for live operation. Leave demonstration mode off for live use. Submit and confirm the workspace opens.

For a subdirectory such as https://example.org/civicflow, open /civicflow/install and enter https://example.org/civicflow as the base URL. Keep the configured URL consistent with the actual path. The examples in this guide are address patterns, not hosted demonstration URLs.

## 4 Live and demonstration modes

Live mode creates only your administrator, leaves starter services unpublished with zero fees and creates no sample citizen requests. Review each service before activation. Demonstration mode adds nine active sample services, six synthetic requests, a sample citizen and four departmental officers. You choose the sample-account password during setup. Demonstration approvals carry a no-legal-effect label.

The installer writes config/local.php and config/installed.lock. Keep both protected. Do not remove the lock to repair an in-service database. A failed fresh setup may leave partial tables; use another new database or inspect only the failed installation tables before retrying.

## 5 Configure operations and providers

- Review organisation identity and support contacts.

- Maintain departments and create authorised staff with the correct scope and role permissions.

- Review service descriptions, required documents, published fees and targets, then activate the services you provide.

- Enrol the administrator in MFA and save recovery codes before requiring staff MFA.

- Configure SMTP host, sender, TLS mode, port and credentials if email is needed.

- Configure Termii’s account HTTPS base URL, key, approved sender and route if SMS is needed.

- Configure an available Responses-compatible OpenAI model and key if AI is needed. Retain the local service guide.

- Test each enabled channel with an account and recipient you control. Review held messages before explicitly requeueing them.

Credentials are encrypted in the database using the installation key. Blank secret fields preserve stored values; explicit remove options clear them. Keep the key and database backed up together. Sent in the outbox means provider acceptance, not guaranteed destination receipt.

## 6 Scheduled workflow

In cPanel Cron Jobs choose every five minutes. Enter the command only in its command field and replace the example account/path with the actual values shown by Configuration:

/usr/local/bin/php /home/CPANEL_USER/public_html/civicflow/cron/run.php

Some hosts use a versioned executable such as /opt/cpanel/ea-php83/root/usr/bin/php. Confirm the correct path and CLI extensions. The scheduled worker processes up to 20 external messages per run and escalation checks; the manual web control uses up to five. Do not expose cron/run.php through HTTP.

## 7 Hosted acceptance checklist

- Open login, services and assistant pages and confirm assets and clean addresses.

- Test index.php and login.php canonical redirects and deny config/setup.key, config/local.php, storage, app and documentation paths.

- Register a citizen, verify email, submit a complaint and a permit with a harmless PDF.

- Use another citizen and another department officer to confirm direct record and file denial.

- Complete review, information reply and a decision. Verify internal notes and attached staff evidence remain private.

- Confirm tracking, approval verification, print output, CSV and role-specific reports.

- Observe cron’s last-run time, actual inbox/handset receipt and live AI access where enabled.

- Inspect browsers, narrow screens and keyboard use and rehearse restoration to a separate environment.

## 8 Backup maintenance and cleanup

Back up the database, storage/uploads, config/local.php, config/installed.lock and matching application version together. Test restoration privately before relying on the backup. Maintain log rotation, available disk space, provider failures and current dependencies. Automatic retention, malware scanning and multi-instance storage coordination are not included.

After setup, remove the uploaded ZIP and START_HERE.html from the hosting directory while retaining an offline copy. Keep all private denial files and the installation lock. The report, slides, manuals and demonstration paperwork belong in the academic submission rather than the public website.

## 9 Troubleshooting

| Symptom | Checks |
| --- | --- |
| Clean route returns 404 | Correct document root, root .htaccess, rewrite module and override policy. |
| Installer fails readiness | Required extensions and ownership/write access to every private storage folder. |
| Database connection fails | Full prefixed names, credentials, database user grants, host and fresh schema. |
| Redirect loop | Exact base URL and native HTTPS state. The app does not trust arbitrary forwarding headers. |
| Upload rejected | Actual MIME, per-file and total limits, PHP post/upload configuration and disk space. |
| Configuration required or failed mail | Channel enabled, authorised sender, correct TLS/port, credentials and outbound connectivity. |
| Cron not advancing | Correct absolute path, CLI PHP/extension selection and private logs. |

## 10 Building an installation from the repository

The GitHub repository contains source in src and academic material in docs. GitHub Pages cannot run this PHP application. Use the PHP and database host described in this guide. For a new installation, run python3 tools/build_release.py from the extracted repository root. The tool writes dist/CivicFlow_cPanel_Complete.zip with the application at the archive root, a newly generated private setup key and matching local START_HERE.html. Upload that generated application ZIP to cPanel.

For loopback evaluation, run python3 tools/prepare_local.py, then open a terminal in src and run php -S 127.0.0.1:8080 router.php. Read src/config/setup.key locally and enter it at /install. The helper refuses to replace an installed configuration. Generated keys, credentials, databases, uploads and runtime logs are excluded by .gitignore and must remain outside the public repository.

For an existing deployment, back up the database, config/local.php, config/installed.lock and private uploads. Preserve the application key. This documentation revision requires no schema migration or reinstallation. Do not overwrite an active installation with a new-install configuration or remove its lock.

## 11 Deployment record and independent access

| Field | Record |
| --- | --- |
| Project address supplied by the team | https://iconicproject.icdfafoundation.com.ng/ |
| Verified application release | ___________________________________ |
| Date checked and independent checker | ___________________________________ |
| Database and HTTPS result | ___________________________________ |
| Citizen and staff journey evidence | ___________________________________ |
| Private-path and clean-route evidence | ___________________________________ |
| Cron and recovery evidence | ___________________________________ |
| Release decision and supervisor review | ___________________________________ |

Use a separate staging or evaluation instance for assessment accounts and negative tests. Keep passwords in the agreed private delivery channel. The supplied address is a project reference until the team records an independent successful check; no current uptime measurement is asserted here.
