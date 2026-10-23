Iconic University

Sokoto Nigeria

Smart Government Service Delivery Platform

CivicFlow

# Project Initiation Document

Project proposal business case and delivery plan

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

## 1 Project proposal and authority

We propose CivicFlow as a shared service-delivery portal for citizen complaints, permit applications and traceable departmental processing. The problem described in our brief is delayed access, repeated visits and poor visibility of progress. Our intended outcome is a dependable digital route from service discovery to a recorded decision, with private evidence and controlled staff access.

This PID defines the business case, objectives, scope, responsibilities, delivery gates and risks for Group 1. It accompanies the five-chapter report and detailed SRS. MIT 820 requires supervisor approval before coding. The approval record below must reference the actual dated proposal decision; the existing software baseline does not supply that historical record.

| Control field | Record |
| --- | --- |
| Project | Smart Government Service Delivery Platform |
| Product and baseline | CivicFlow application 1.0.4; documentation 1.1 |
| Institution and course | Iconic University Sokoto Nigeria; MIT 820 |
| Project manager | Bolaji Watson |
| Academic supervisor | ___________________________________ |
| Proposal approval reference and date | ___________________________________ |
| Scope or schedule conditions | ___________________________________ |
| Supervisor signature | ___________________________________ |

## 2 Business need and options

A citizen must be able to identify the responsible service, learn the required documents, submit information once and return to the same record for progress and further questions. Staff need an accountable queue and an auditable reason for each decision. A general contact form provides receipt but leaves ownership, evidence and status fragmented.

The options considered are continued counter-based handling, an online form forwarding messages, and an integrated request workflow. Continued counter handling requires little software change but preserves the visibility problem. A form has a smaller build but still needs separate tracking and document coordination. The integrated workflow carries greater implementation and operating responsibility, while matching the proposal objectives. We select the integrated option for the bounded course project.

| Option | Service record | Decision accountability | Delivery consequence |
| --- | --- | --- | --- |
| Counter and informal follow-up | Separate paper or message records | Depends on local manual practice | Retains the problem model in the brief |
| Online form and email | Electronic intake without one lifecycle | Separate email or spreadsheet follow-up | Lower build scope; weaker traceability |
| Integrated CivicFlow workflow | One request, private files and event history | Department, actor, reason and date recorded | Chosen option; needs trained staff and service ownership |

## 3 Intended benefits and evaluation

The intended benefits are clearer receipt and ownership, fewer avoidable follow-up enquiries, remote service access and better management visibility. They remain expected benefits until an authorised pilot compares actual results with a recorded baseline. Technical pass counts do not establish service-time savings or citizen adoption.

A proposed pilot will measure task completion, time to first review, completion within the service target, traceable decisions and citizen feedback. The PRD defines the proposed targets and their denominators. The team will retain anonymised observations and identify service types so that unlike cases are not treated as equivalent work.

## 4 Feasibility and resource case

Technical feasibility rests on PHP 8.2 or newer, PDO and a relational database supported by the target cPanel host. The application uses a modular monolith with server-rendered pages and local assets. This simplifies deployment to the existing hosting model. The ADD records the tradeoff against a separate front end, API and distributed services.

Operational feasibility depends on an authorised service owner, configured catalogue, trained officers, support contact, scheduled jobs and monitored delivery. The platform cannot replace unavailable departmental staff or create legal authority for a permit. A deployment must define privacy, retention, incident and recovery procedures.

The cost case separates already available resources from recurring consumption. Hosting, domain administration, protected backup storage, mail delivery, SMS and optional AI usage require an approved operating budget. No supplier quotation or realised saving is asserted. The manager should approve the cost model and provider limits before public operation.

| Resource or cost | Planning basis | Approval owner |
| --- | --- | --- |
| Team effort | Five members, with sprint capacity agreed at planning; actual effort recorded weekly. | Bolaji and each member |
| Hosting and database | Existing account capacity and an isolated staging database to be confirmed. | Aminu |
| Domain and TLS | Use the authorised project address and valid certificate. | Aminu |
| Backup storage | Database, application key and private-file backup with restoration rehearsal. | Aminu |
| Email, SMS and AI | Provider tariff multiplied by approved expected usage; spending limits recorded before enabling. | Project service owner |
| Contingency | Reserve review and correction capacity before defence; approve any extra cost separately. | Bolaji |

## 5 Scope and acceptance

The project includes complaints, permits, private attachments, drafts, replies, workflow decisions, tracking, notifications, outbox and templates, reports, audit, permissions, account security, public discovery and optional assistance. The SRS defines FR01 to FR24 and NFR01 to NFR10. The PRD defines product priorities and success measures.

Payments, verified national identity, statutory registry integration, cryptographic government signatures, malware scanning and automatic retention are outside the current release. A single-host installation does not supply multi-host failover. Acceptance requires demonstrated core workflows and permission boundaries, consistent documents, independently runnable or accessible software and recorded review of the remaining hosted checks.

## 6 Team structure and communication

| Member | Registration number | Sub team | Email |
| --- | --- | --- | --- |
| Aminu Idris | 2630802400002 | Development | aminuidris016@gmail.com |
| Saidu Mustapha Suleman | 2630802400001 | Documentation | mustydg74@gmail.com |
| Bolaji Watson | 2630802400009 | Project management | watsonbolaji@gmail.com |
| John Ojuekaiye Mayowa | 2630802400008 | Development | johnmayowaojuekaiye2@gmail.com |
| Egbue Odira Celestine | 2630803400003 | Documentation | odiratranslator@gmail.com |

Aminu and John lead Development. Mustapha and Odira lead Documentation. Bolaji manages the project and coordinates QA. These allocations remain the agreed Group 1 structure. Actual implementation, documentation and review contributions must be confirmed through commits, issues, reviewed changes and peer assessment.

The proposed working rhythm is a weekly planning and progress review, short asynchronous updates during each sprint and a supervisor review at each gate. Meeting dates, attendance, decisions and actions belong in actual minutes. The manager escalates a blocker that threatens a gate promptly and records its owner and next review date.

## 7 Delivery schedule and review gates

| Dates in 2026 | Gate or milestone | Lead |
| --- | --- | --- |
| 23 to 25 October | Confirm proposal approval record, business case, scope and evidence owners. | Bolaji with all members |
| 26 October to 1 November | Review SRS, ADD, models, interface designs and repository baseline. | Development and Documentation |
| 2 to 8 November | Repeat core citizen and staff integration journeys on staging. | Aminu and John |
| 9 to 15 November | Review security boundaries, consented usability checks and defect fixes. | Bolaji coordinates QA |
| 16 to 22 November | Verify hosted MySQL, HTTPS, providers, workload and restoration. | Aminu and John |
| 23 to 27 November | Freeze release, complete report and manuals, rehearse live defence. | All members |
| 28 November | Final presentation and independent live system demonstration. | All five members |
| 29 November | Apply agreed corrections and submit before 11:59 p.m. WAT. | Bolaji coordinates submission |

Sprint 1 runs from 23 October to 1 November, Sprint 2 from 2 to 15 November, and Sprint 3 from 16 to 27 November. The proposed review backlog contains 79 estimated points. Estimates support planning and are not hours or marks. Actual remaining points stay blank until the team records reviewed completion.

The course guide presents an eleven-week sequence; the portal window supplied to the group is nine calendar weeks. The dates above preserve the stated presentation on 28 November and submission on 29 November. The supervisor should confirm the compressed mapping while retaining the proposal, SRS, ADD, prototype, test and live-defence gates.

## 8 Risk and change control

The main risks are missing approval evidence, inaccessible hosting, privacy exposure, provider failure, incomplete testing, scope growth and inadequate contribution records. The risk register assigns owners, treatments and evidence fields. A proposed change states its reason, affected requirements, effort, test consequences and effect on the final dates.

Bolaji records the request. Development assesses code, schema and operations. Documentation checks report, model, manual and slide changes. The group records its recommendation and the supervisor resolves changes affecting the approved academic scope. No item is closed until its evidence and reviewer are recorded.

## 9 MIT 820 coverage and completion record

| Requirement | Artifact | Final evidence to attach |
| --- | --- | --- |
| Supervisor approval before coding | PID approval register and original signed proposal | Attach the actual dated approval record. |
| Live operational system | Deployment record, independent access check and defence run sheet | Verify the hosted address and approved evaluation accounts. |
| Individual contributions | Git commits, issues, reviewed pull requests and peer assessment | Each member records and confirms their own work. |
| Weekly monitoring | Weekly reports, minutes and sprint register | Complete records from actual meetings and progress. |
| Performance and recovery | Measured workload results and restoration record | Execute and record the planned checks. |
| Human evaluation ethics | Consent and anonymised usability observations | Obtain consent before any participant session. |

## 10 Initiation review

Project manager and date __________________________________________

Development representative and date ________________________________

Documentation representative and date ______________________________

Supervisor decision and date ______________________________________

Conditions and next review ________________________________________
