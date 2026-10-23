<img src="docs/assets/brand/civicflow.svg" alt="CivicFlow emblem" width="100">

# CivicFlow

**Smart Government Service Delivery Platform**

Group 1, MIT 820 Software Development Project I, Iconic University Sokoto Nigeria.

CivicFlow connects citizen complaints and permit applications to departmental review, private evidence, progress tracking and a recorded outcome. The citizen portal opens after account creation and sign-in. Staff work through roles, action permissions and record scopes.

Application baseline **1.0.4**. Documentation revision **1.1**, dated **23 October 2026**.

[Documentation](docs/README.md) · [Course alignment](docs/COURSE_ALIGNMENT.md) · [Diagrams](docs/DIAGRAMS.md) · [Setup](docs/technical/setup.md) · [Testing](docs/technical/testing.md)

![Citizen service imagery used by the landing page](src/assets/img/landing/citizen-services.webp)

## Main capabilities

| Area | Implemented capability |
| --- | --- |
| Citizen services | Complaints, permit forms, private documents, drafts, replies, withdrawal, reopening and feedback |
| Department processing | Routing, queues, review, information requests, reasons, decisions and internal notes |
| Status and communications | Private-code tracking, in-app updates, email and SMS outbox, templates and scheduled retries |
| Identity and permissions | Six default roles, additional staff roles, separate action rights, MFA and device revocation |
| Service discovery | Local image slider, service search, departments, notices and FAQs |
| Assistance | Local service guide with optional configured AI using public service context |
| Oversight and operations | Scoped reports, CSV protection, audit, clean URLs and a locked cPanel installer |

## Architecture

![Application architecture](docs/assets/diagrams/01_Architecture.png)

The baseline uses server-rendered PHP, PDO, a relational database and protected storage. A scheduled CLI worker handles delivery and overdue escalation. The [ADD](docs/academic/Architecture_Design_Document.md) explains the decisions, consequences and single-host limits.

## Quick start

Use PHP 8.2 or newer with PDO SQLite, Fileinfo, Mbstring, OpenSSL, cURL and DOM/XML for local evaluation.

```bash
python3 tools/prepare_local.py
cd src
php -S 127.0.0.1:8080 router.php
```

Read the generated `src/config/setup.key` locally. Open `http://127.0.0.1:8080/install`, choose SQLite and create your administrator. The setup key and installed credentials stay outside Git.

For a new cPanel installation, run `python3 tools/build_release.py` from the repository root. Upload and extract the generated private application archive. See [deployment instructions](docs/technical/setup.md). GitHub Pages cannot execute this PHP application.

## Project documents

The [documentation index](docs/README.md) links all twelve documents in Markdown, editable Word, PDF and offline HTML. It includes the five-chapter report, SRS, design dictionary, citizen and staff manuals, test summary, setup, demonstration guide, PRD, PID, ADD and monitoring pack. Presentation files appear in the [presentation folder](docs/presentation/README.md).

![Principal actors and use cases](docs/assets/diagrams/12_Use_Case.png)

## Repository map

| Location | Contents |
| --- | --- |
| `src/` | Deployable PHP application, database schema, local assets and isolated acceptance scripts |
| `docs/academic/` | Twelve academic documents in Markdown, DOCX, PDF and HTML |
| `docs/assets/` | Local emblem, diagrams, wireframes and embedded document figures |
| `docs/presentation/` | Editable defence deck, PDF, preview and speaker guide |
| `docs/project-management/` | Backlog, burndown, risks, minutes, weekly reports and contribution records |
| `docs/technical/` | Setup, architecture, routes, model sources and testing |
| `.github/` | CI, manual staging verification, issue forms and pull-request template |
| `tools/` | Link checks and private local or cPanel setup helpers |
| `START_HERE.html` | Offline documentation entry point |

## Roles

| Role | Default access |
| --- | --- |
| Citizen | Own requests, drafts, evidence and feedback |
| Service officer | Permitted actions on submitted records in the assigned department |
| Department head | Department processing and report export |
| Oversight auditor | Submitted records, reports and audit across departments, with no default decisions or internal evidence |
| Communications officer | Outbox, templates, notices and public guidance |
| Administrator | Workspace, people, services and permission configuration |

Every relevant read, update and download also checks record scope. Another person's unsubmitted draft stays private.

## Group 1

| Member | Registration number | Sub team | Email |
| --- | --- | --- | --- |
| Aminu Idris | 2630802400002 | Development | aminuidris016@gmail.com |
| Saidu Mustapha Suleman | 2630802400001 | Documentation | mustydg74@gmail.com |
| Bolaji Watson | 2630802400009 | Project management | watsonbolaji@gmail.com |
| John Ojuekaiye Mayowa | 2630802400008 | Development | johnmayowaojuekaiye2@gmail.com |
| Egbue Odira Celestine | 2630803400003 | Documentation | odiratranslator@gmail.com |

Members record actual work through their own commits, issues, pull requests and peer reviews. [Contribution guidance](CONTRIBUTING.md) explains the process.

## Delivery and assessment

The remaining plan starts **23 October 2026**. Final presentation is **28 November 2026**. Portal submission is **29 November 2026 before 11:59 p.m. WAT**.

The [course matrix](docs/COURSE_ALIGNMENT.md) maps the MIT 820 guide to the submission. The guide's eleven-week sequence and the supplied nine-week portal window require supervisor confirmation of the compressed calendar. The review gates remain in the plan.

The retained application evidence records **267 passed automated assertions**. The [test summary](docs/academic/Test_Summary_Report.md) explains the suite counts and remaining hosted, browser, performance, recovery and real-provider acceptance. Supplied workflow files become CI evidence after actual successful GitHub runs.

Team-supplied project address: [CivicFlow portal](https://iconicproject.icdfafoundation.com.ng/). Record independent verification and approved evaluation access in the [deployment record](docs/project-management/deployment-record.md).

## Working with this repository

Run `python3 tools/validate_repo.py` to check local document and image links. Follow [the upload guide](docs/GITHUB_UPLOAD.md) to put the extracted contents at the repository root. Images and diagrams use relative paths and travel with the ZIP.

Prepared assessment records use reserved contact addresses and normal access checks. Their data-origin information remains in the application's About and relevant approvals. They do not establish actual government adoption or authority.

Read [security reporting](SECURITY.md), [rights and third party material](LICENSE.md), [asset credits](docs/ASSET_REGISTER.md) and [the changelog](CHANGELOG.md).
