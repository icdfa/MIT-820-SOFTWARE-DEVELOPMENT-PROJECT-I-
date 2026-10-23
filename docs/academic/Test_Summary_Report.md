Iconic University

Sokoto Nigeria

Smart Government Service Delivery Platform

CivicFlow

# Test Summary Report

Recorded release evidence and remaining acceptance

Group 1

MIT 820 Software Development Project I

| Group member | Registration number |
| --- | --- |
| Aminu Idris | 2630802400002 |
| Saidu Mustapha Suleman | 2630802400001 |
| Bolaji Watson | 2630802400009 |
| John Ojuekaiye Mayowa | 2630802400008 |
| Egbue Odira Celestine | 2630803400003 |

Document version 1.1   Application baseline 1.0.4   23 October 2026

Final presentation 28 November 2026
Portal submission 29 November 2026 before 11:59 p.m. WAT

## 1 Scope and result

This report summarises the machine-readable acceptance evidence retained for CivicFlow 1.0.4. The files record 267 passed assertions across five suites. Most timestamped suites ran on 19 October 2026. The documentation revision date is 23 October 2026 and does not represent a new execution date for those results.

An assertion count describes the checks exercised under the recorded conditions. It is not a count of citizens, an independent penetration test, a workload benchmark or proof that every possible failure has been covered.

| Suite | Pass | Total | Recorded date | Scope |
| --- | --- | --- | --- | --- |
| Workflow and security HTTP | 124 | 124 | 19 October 2026 | SQLite and loopback PHP server |
| Fresh installation HTTP | 13 | 13 | 19 October 2026 | SQLite live installation defaults |
| Installer regression | 5 | 5 | 19 October 2026 | Storage and database error handling |
| Landing and role dataset | 113 | 113 | 19 October 2026 | Landing, 26 accounts, 36 cases and access rules |
| Carousel interaction fixture | 12 | 12 | Recorded release evidence | JavaScript fixture, not a rendered browser test |
| Total recorded assertions | 267 | 267 | Release 1.0.4 evidence | Assertion count, not users or complete security coverage |

## 2 Environment and evidence provenance

The workflow and fresh-installation suites use a disposable application copy, a PHP loopback HTTP server and SQLite. The recorded workflow runtime is PHP 8.3.6. Test accounts and harmless documents remain confined to that temporary environment. The harness checks both the HTTP response and persisted state.

Native MariaDB schema and representative-query checks and Apache directive syntax checks remain separate compatibility evidence. They do not establish a full hosted HTTP/PDO-MySQL run. The carousel report explicitly records browser_rendering_test as false. The supplied JSON files retain individual check names and results.

| Evidence file | Checks |
| --- | --- |
| acceptance-sqlite.json | 124 workflow and security assertions |
| acceptance-live.json | 13 fresh-installation assertions |
| acceptance-installer.json | 5 installer regression assertions |
| acceptance-landing-demo.json | 113 landing and prepared-record assertions |
| acceptance-carousel.json | 12 JavaScript interaction-fixture assertions |

## 3 Method and representative outcomes

Successful scenarios cover citizen registration, complaints, permits, review, requests for information, replies, decisions, tracking and feedback. Negative cases cover forged registration roles, other-owner access, unrelated departments, internal documents, MIME spoofing, invalid transitions, stale updates and token replay.

The outbox scenarios verify held configuration states, cancellation, escaped templates, controlled SMTP connection refusal, retry backoff and the five-failure stop. No real message receipt is inferred from a refused local connection.

The dataset scenarios verify additive loading, reserved contact addresses, unchanged existing accounts and records, all role boundaries and idempotency. The slider fixture checks script state and controls. A manual browser and accessibility evaluation is still required.

## 4 Release corrections

Version 1.0.1 retained runtime subdirectories in the extracted archive and aligned password input with the 72-byte hashing boundary. Version 1.0.2 improved storage readiness and database diagnostics. Versions 1.0.3 and 1.0.4 added the public landing experience and controlled evaluation records and adjusted the public presentation. The application source is unchanged by this documentation and repository revision.

## 5 Requirements and test traceability

FR01 to FR22 retain their original identifiers and evidence groups. FR23 covers public landing and slider behaviour. FR24 covers administrator-controlled record preparation. NFR09 contains proposed workload and recovery targets for later measurement. The SRS and PRD contain the requirement-to-evidence mappings.

| Acceptance activity | Method and result field | Owner |
| --- | --- | --- |
| Hosted MySQL and HTTPS | Repeat complete citizen and staff journeys on staging. Record build, date and result. | Aminu and John |
| Security and permissions | Verify negative direct access, session, upload and private-path scenarios. Record defects. | Aminu; Bolaji reviews |
| Performance | Measure 95th percentile response time under 10 concurrent users and 10,000 stored requests. Compare with proposed 2.5-second target. | Aminu |
| Browser and accessibility | Desktop and mobile layout, keyboard focus, forms, reduced motion and print review. | John |
| Recovery | Restore a protected database, configuration and private-file backup into a separate instance. Record actual RPO and RTO. | Aminu |
| Human usability | Obtain consent, use anonymised participant IDs and record completion and assistance. Proposed success target is 8 of 10 participants. | Bolaji coordinates |
| Providers | Check the operator-configured SMTP inbox, SMS handset and live AI response separately. | Aminu |

## 6 Reproduction and defect management

From the repository, run python3 src/tests/http_acceptance.py --php /path/to/php and repeat with --live. Run src/tests/installer_regression.py, src/tests/landing_demo_acceptance.py and node src/tests/carousel_interactions.cjs. The included repository CI defines the same checks for a suitable PHP environment.

Use a disposable empty database for test execution. Record a defect with the affected FR or NFR, expected and actual result, reproducible steps and evidence. A developer links the fix and a reviewer records the repeated result before closure. The repository contains issue and pull-request templates for this process.

## 7 Review and acceptance record

Test coordinator and date __________________________________________

Developer reviewer and date _______________________________________

Supervisor or assessor and date ___________________________________

Hosted acceptance evidence location ________________________________
