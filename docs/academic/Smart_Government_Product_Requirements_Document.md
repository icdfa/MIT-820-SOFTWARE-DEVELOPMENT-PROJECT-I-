# Product Requirements Document

Smart Government Service
Delivery Platform

CivicFlow

Group 1

MIT 820 Software Development Project I

| Group member | Registration number |
| --- | --- |
| Aminu Idris | 2630802400002 |
| John Ojuekaiye Mayowa | 2630802400008 |
| Saidu Mustapha Suleman | 2630802400001 |
| Egbue Odira Celestine | 2630803400003 |
| Bolaji Watson | 2630802400009 |

Prepared by Group 1 for project supervisor review

Document version 1.1
Application baseline CivicFlow 1.0.4

23 October 2026

Final presentation 28 November 2026
Portal submission 29 November 2026
Before 11:59 p.m. West Africa Time

## Project team and document control

### Team allocation and contacts

| Member | Registration number | Sub team | Email |
| --- | --- | --- | --- |
| Aminu Idris | 2630802400002 | Development | aminuidris016@gmail.com |
| John Ojuekaiye Mayowa | 2630802400008 | Development | johnmayowaojuekaiye2@gmail.com |
| Saidu Mustapha Suleman | 2630802400001 | Documentation | mustydg74@gmail.com |
| Egbue Odira Celestine | 2630803400003 | Documentation | odiratranslator@gmail.com |
| Bolaji Watson | 2630802400009 | Project management | watsonbolaji@gmail.com |

### Purpose of the document

We propose CivicFlow as a shared service portal through which citizens submit complaints and permit applications, while authorised staff review requests, communicate decisions and maintain a traceable service record. This document sets out the product outcomes, scope and acceptance criteria for supervisor review.

Document owner: Group 1. Prepared for the project supervisor and group members. The PRD translates the project proposal into product decisions and measurable acceptance criteria. The Software Requirements Specification (SRS) remains the detailed technical requirements baseline.

PRD version 1.1 is dated 23 October 2026 and describes application release 1.0.4. The accompanying SRS and academic report now use the same application baseline. Existing FR and NFR identifiers are retained, with FR23 for the public landing experience and FR24 for controlled evaluation-record loading. The PID, ADD and monitoring records support the MIT 820 project gates.

Status: submitted for group and supervisor review. Approval has not been recorded. Bolaji maintains the change register; Development and Documentation assess the effect of each proposed change before it is accepted.

Project coordination: Bolaji Watson. Development contact: Aminu Idris.

### Priority terms

P0 means required for an acceptable core release. P1 means valuable to the service experience and included where specified. A priority does not imply that a feature is unfinished. Configured external channels need separate provider acceptance before their use can be approved.

## 1 Product purpose

### Product decision

Our proposed product is a browser-based portal for complaints, permits and service follow-up. A citizen should be able to identify a service, understand its requirements, submit a request and return to the same record for every response. Staff should receive a departmental queue and record the action taken, who took it and when.

The core workflow must remain usable without external messaging or AI credentials. In-app notifications, signed-in tracking and the local service guide provide a working service path. Email, SMS and AI extend that path after the operator configures and verifies each channel.

### Problem statement

The project addresses delays, repeated visits and poor visibility when citizens seek public services. Paper submissions and informal follow-up can leave a citizen unsure whether an application was received, which department owns it or what information is missing. Staff also need a consistent record of submissions, decisions and overdue work.

These are the service problems set out in our proposal. We have not completed a field study or measured a reduction in government processing time. Any claim of improvement must come from an authorised pilot using a recorded baseline and comparable service cases.

### Product objectives

- Give citizens a documented online route from service discovery to a recorded outcome, with a unique reference and private tracking code.

- Make departmental ownership, review stages, requests for information and service targets visible to the appropriate users.

- Reduce avoidable follow-up effort through reusable guidance, private documents, dated messages and notification records.

- Provide managers and auditors with scoped reports and an audit trail while protecting citizen records.

### Digital economy contribution

CivicFlow supports e-government service delivery through digital applications, remote follow-up and accountable workflows. It can reduce dependence on physical hand-offs and make service activity easier to review. Those benefits depend on staff adoption, published service rules, reliable hosting and an authorised operating organisation.

## 2 Users and ownership

The product has six default roles. Access depends on both an action permission and the scope of the record. A visible menu or a known URL is never sufficient authority to read or change a case.

| Actor | Main need | Default access boundary |
| --- | --- | --- |
| Citizen | Submit and follow requests, supply evidence, receive decisions and give feedback. | Own requests and documents. Drafts remain private to their owner. |
| Service officer | Review a departmental queue, request information and record permitted decisions. | Submitted cases in the assigned department, subject to action permissions. |
| Department head | Oversee department workload, assignments, service targets and exports. | Department scope with the configured review and reporting rights. |
| Oversight auditor | Review case activity, reports and audit records independently. | Read-only oversight across departments. No default decisions or internal-document download. |
| Communications officer | Maintain notices, FAQs, templates and notification delivery records. | Communication tools; no default access to citizen case records. |
| Administrator | Manage accounts, permissions, departments, services and integrations. | Workspace administration and submitted-case oversight. Another person's draft remains private. |

### Service ownership

The operating organisation owns its published service catalogue, fees, processing rules and the authority behind each decision. A departmental officer must communicate the reason for an information request or decision in the request record. A department head follows up workload and overdue cases. An administrator maintains account and role assignments.

### Project ownership

Group 1 owns the project deliverables. Aminu Idris and John Ojuekaiye Mayowa lead development. Saidu Mustapha Suleman and Egbue Odira Celestine lead documentation. Bolaji Watson coordinates milestones, QA, risks, rehearsals and the final submission. All five members must understand the complete citizen-to-staff workflow.

## 3 Scope and priorities

The release scope covers the citizen journey, departmental processing, communications and the tools required to administer and deploy one portal instance. The current application includes the following product capabilities.

| Product ID | Capability | Priority | Baseline position |
| --- | --- | --- | --- |
| PRD01 | Accounts and account security | P0 | Implemented |
| PRD02 | Public landing page and service discovery | P1 | Implemented |
| PRD03 | Complaints permits and drafts | P0 | Implemented |
| PRD04 | Private supporting documents | P0 | Implemented |
| PRD05 | Department review and decisions | P0 | Implemented |
| PRD06 | Tracking receipts and verification | P0 | Implemented |
| PRD07 | Notifications outbox and templates | P0 | Implemented; external delivery conditional |
| PRD08 | Citizen guidance and optional AI | P1 | Local guide implemented; live AI conditional |
| PRD09 | Withdrawal reopening and feedback | P1 | Implemented |
| PRD10 | Reports exports and audit | P0 | Implemented |
| PRD11 | Catalogue people and permissions | P0 | Implemented |
| PRD12 | Installation and project record loading | P0 | Implemented |

### Release boundaries

The product does not collect payments, validate national identity, issue legally recognised digital signatures or integrate automatically with government registries. It is not an emergency-response system. Native mobile apps, offline synchronisation, multi-tenant government hosting, GIS analysis and automated retention or erasure are outside this release.

Prepared project records support the assessment walkthrough. They are stored and processed through normal role-based workflows, with their fictional origin described in About and relevant approval records. They must not be counted as operational adoption or real government service outcomes.

### Operating assumptions

The operator supplies an approved catalogue, trained staff, hosting, a database, private storage and a support contact. Citizens have browser and network access. Hosting may use a PHP-compatible cloud or cPanel environment. Valid TLS, scheduled workflow execution and any enabled delivery providers are required deployment dependencies.

## 4 Product requirements

### PRD01 Accounts and account security

PRD01 | P0 | Primary users: citizens and staff. As a user, I need a secure account so that my requests and permitted work are available without exposing another person's records.

- Public registration creates a Citizen account even if a forged staff role is submitted. Passwords contain letters and numbers and meet the 12 to 72 byte boundary.

- Verification and password recovery use expiring single-use links. Password resets and account changes invalidate the affected authenticated sessions.

- Users can enable authenticator MFA, retain eight single-use recovery codes and revoke other devices. A configured staff MFA requirement must be enforced.

### PRD02 Public landing page and service discovery

PRD02 | P1 | Primary user: a visitor choosing a service. As a citizen, I need to find the correct service and its requirements before beginning an application.

- The landing page provides three local images with usable slider controls, service search, tracking, department links, current notices, FAQs and account entry points.

- Only active services are shown. Each entry identifies its department, requirements, published fee if any and target in calendar hours. Search can narrow by service type or department.

- The public layout uses the CivicFlow identity and clean URLs. It must not display invented adoption figures, endorsements or service results.

### PRD03 Complaints permits and drafts

PRD03 | P0 | Primary user: a citizen. As a citizen, I need to describe my issue or application once and keep a stable record for follow-up.

- Submission captures the chosen service, subject, description, location, state, priority, declaration and organisation where relevant. A permit with listed evidence requirements needs supporting evidence.

- A committed submission creates a unique reference, private code, department, service target and dated history. It assigns an eligible departmental reviewer where one is available.

- A citizen may save and edit a private draft. A draft has no active target or officer assignment and cannot be read by another user, including staff.

### PRD04 Private supporting documents

PRD04 | P0 | Primary users: citizens and authorised reviewers. Evidence must remain linked to its request and accessible only to an authorised reader.

- Accept PDF, JPG and PNG only, up to 5 MB per file and three files per upload. Enforce the existing limits of 20 attachments per request and 100 MB per uploading user.

- Validate file content and metadata, store a random private filename and integrity hash, and enforce case and document permissions on every download.

- Staff-only evidence requires internal-note access. Citizens and auditors without that permission must not receive it through a direct URL.

### PRD05 Department review and decisions

PRD05 | P0 | Primary users: officers and department heads. As a reviewer, I need to manage my department's cases without changing records outside my authority.

- The queue supports filters, permitted officer assignment and current service targets. Cross-department routing requires its separate permission and organisation scope.

- A case enters In review before a decision. Only permits can be Approved; only complaints can be Resolved. A reviewer may request more information or reject either kind with a reason.

- Committed changes record the actor, time, status and message. Stale or invalid updates must leave the case unchanged. Public messages and internal notes have separate visibility.

### PRD06 Tracking receipts and verification

PRD06 | P0 | Primary user: a citizen following a case. As an applicant, I need a consistent receipt and progress record after submission.

- Signed-in owners can view their request history, documents and relevant messages. Private receipts show the reference and tracking code.

- Public tracking requires both the reference and private code. It shows limited progress without the applicant's description, contact details, attachments or internal notes.

- Approved permit records can be printed and verified by reference without exposing the applicant. Project approval records retain their stated limits and do not imply official issuing authority.

### PRD07 Notifications outbox and templates

PRD07 | P0 | Primary users: citizens, communications staff and the operator. A service update must have an accurate, reviewable communication record.

- Create in-app updates for relevant activity. Queue routine email and SMS according to preferences and channel readiness; hold unconfigured delivery instead of marking it sent.

- Provide filtered, paginated outbox records, message details and authorised retry or cancellation. Support sanitised HTML, plain alternatives and escaped template fields.

- Scheduled jobs escalate overdue open requests once per open cycle and retry delivery with backoff. Stop automatic delivery after five failed attempts. Provider acceptance is distinct from recipient receipt.

### PRD08 Citizen guidance and optional AI

PRD08 | P1 | Primary user: a citizen seeking help. As a citizen, I need guidance on requirements, submissions and tracking without disclosing private case information.

- A labelled local FAQ guide works without an AI account. The interface identifies when configured AI assistance is available and allows the citizen to choose whether to use it.

- AI receives the question and public service facts, not private request records or uploaded evidence. It cannot approve applications or alter workflow decisions.

- A provider failure must not block submission or tracking. Keep the local guide and help centre available and explain the failure without exposing credentials.

### PRD09 Withdrawal reopening and feedback

PRD09 | P1 | Primary user: a citizen. As a citizen, I need to correct my participation in a case and comment on the service received.

- The owner can withdraw at Draft, Submitted or Needs information. Other users cannot withdraw the owner's request through a forged action.

- Only the owner can reopen a resolved complaint. Reopening sets a new service target, clears previous feedback and returns the complaint to processing.

- After approval, resolution or rejection, the owner may submit one rating from 1 to 5 with an optional comment. Prevent duplicate feedback for the same completed cycle.

### PRD10 Reports exports and audit

PRD10 | P0 | Primary users: heads, auditors and administrators. As an oversight user, I need a dependable view of workload and recorded actions within my authority.

- Show database totals for requests, open work, completions, overdue cases, departments and feedback. Apply the same record scope to the view and any export.

- Authorised CSV export returns at most 5,000 matching requests and neutralises values that could be interpreted as spreadsheet formulas.

- Record significant account, permission, submission, review and administrative actions. Audit access is read-only through the application; it does not certify tamper-proof storage.

### PRD11 Catalogue people and permissions

PRD11 | P0 | Primary user: an administrator. As the operator, I need to publish the services and staff permissions that match the organisation's actual authority.

- Manage services, departments, active accounts, role definitions and additional staff roles through separate action rights. Deactivation preserves historical records.

- Keep public citizens separate from staff privileges. Protect the primary administrator role, retain an active administrator and revoke affected sessions after relevant changes.

- Maintain notices, FAQs, support details and optional integrations. Keep provider credentials on the server and preserve stored secrets when a password field is left blank.

### PRD12 Installation and project record loading

PRD12 | P0 | Primary user: the operator. As an operator, I need a repeatable cPanel package and a controlled way to prepare the project walkthrough.

- A new installation uses a private setup key, chosen administrator and locked installer. Retain clean URLs, private-folder denial rules and actionable storage or database errors.

- An update preserves configuration, database records, private uploads and the installation lock. Never require a reinstall to apply a display update.

- Only a primary administrator may load the prepared dataset, after explicit confirmation and a chosen password. Add 26 accounts and 36 cases once; repeated completed loads must not duplicate records or reset passwords.

## 5 User journeys

### Complaint and departmental response

A citizen searches for a road, lighting, waste or planning service, reads the published information and signs in. The citizen describes the issue, identifies the location, adds evidence where available and submits. The portal records a reference, private code, department and target. An authorised officer opens the departmental queue and starts review. If the location is unclear, the officer asks for more information. The citizen replies within the same case and the request returns to Submitted. After further review, the officer records a reasoned resolution or rejection. The citizen reads the outcome and may give feedback. A resolved complaint can be reopened if the issue persists.

Exception handling: invalid evidence, an expired form or a stale update gives a specific message. The system must not present a partial failed submission as a successful request. An unavailable reviewer leaves departmental ownership visible for follow-up.

### Permit application and decision

A business applicant selects a permit, checks its requirements and prepares the evidence. The applicant saves a draft if documents are incomplete, then submits the completed application. The reviewer checks the evidence and requests clarification where necessary. Following review, an authorised officer approves or rejects the permit with a recorded reason. The applicant can read the decision, print the applicable record and provide feedback. A public verification page confirms the recorded approval status without publishing the applicant's details.

Exception handling: a permit requiring evidence cannot be submitted without it. The assistant cannot decide the application. Any actual legal authority, fee collection or registry validation remains the operating organisation's responsibility.

### Service communication and delivery failure

A communications officer reviews a queued update or composes an authorised notice to a registered user. The in-app record is available immediately. An enabled external channel is handled by the scheduled worker; an unconfigured channel is held. A failed attempt records its state and a retry time. The communications officer can inspect an eligible message and retry or cancel it. An administrator corrects integration settings when needed. A provider acceptance is recorded accurately and does not imply that the recipient has read the message.

### Department management and independent oversight

A department head reviews the department's queue, assignments and overdue cases, then exports permitted records for follow-up. An auditor reviews organisation-level submitted cases, reports and audit actions without receiving decision controls or internal evidence by default. The administrator adjusts staff access when duties change. Those permission changes revoke affected sessions so the next protected action uses current access rights.

## 6 Workflow and information rules

| Stage | Who acts next | Permitted next stages |
| --- | --- | --- |
| Draft | Owner completes or withdraws the draft. | Submitted or Withdrawn |
| Submitted | Authorised department reviewer begins review; owner may withdraw. | In review or Withdrawn |
| In review | Authorised reviewer checks evidence and records a reasoned action. | Needs information; Approved for a permit; Resolved for a complaint; or Rejected |
| Needs information | Owner replies or withdraws; reviewer can resume review. | Submitted on owner reply; In review on reviewer action; or Withdrawn |
| Approved | Owner reads the permit outcome and may rate the service. | Closed record |
| Resolved | Owner reads the complaint outcome and may rate or reopen it. | Closed unless owner reopens to Submitted |
| Rejected | Owner reads the reason and may rate the service. | Closed record |
| Withdrawn | Owner retains the recorded history. | Closed record |

### Processing rules

- Keep the request reference stable throughout processing. The private tracking code is a separate access secret and must not appear in public reports or case links.

- Use calendar hours for service targets. Time continues while the request is waiting for more information. Display dates and times consistently in West Africa Time.

- A draft has no target or active assignment. Submitted and reopened cases acquire a target from the selected service; a reopened complaint starts a new open cycle.

- Require the relevant permission, authorised scope, current version and valid transition for every staff update. Record decisions in the case history.

- Keep internal notes and evidence separate from citizen-visible content. Access to reports or audit logs does not automatically grant internal-document access.

- Keep routine delivery preferences separate from requested account verification and password-recovery messages. A delivery error must not reverse a committed service action.

The system workflow expresses the project's service process. Statutory appeal, legal review and department-specific approval chains require separate agreement before an authorised operating organisation uses the portal.

## 7 User experience and data

### Interface requirements

Use a conventional public masthead, readable service navigation and separate citizen and staff workspaces. The landing page should lead to a service, tracking or help without requiring the visitor to understand the internal departmental structure. Signed-in users return to the appropriate dashboard.

The three-image slider must support previous, next, slide selection and pause. Automatic rotation stops for keyboard focus, hover and a hidden page; reduced-motion preferences stop automatic playback. With scripts disabled, the first slide and the server-backed service and tracking forms remain usable.

Forms need visible labels, clear required fields and specific validation feedback. Preserve the entered non-secret information when practical after an error. Do not expose passwords, tracking codes or provider credentials in validation messages. On smaller screens, controls must remain readable and reachable; table scrolling must not hide essential actions.

Use the existing project emblem rather than an unrelated campus logo or an unsupported government seal. Do not publish invented service statistics or endorsements. The normal homepage omits the large demonstration banner; prepared-data information remains in About and relevant project approvals.

### Information requirements

| Information | Purpose | Access rule |
| --- | --- | --- |
| Account and preferences | Identify a user, secure access and select routine notification channels. | Account owner and authorised administration. |
| Request and evidence | Record the service need, location, submission, supporting evidence and decision. | Owner or authorised staff within record and document scope. |
| History and internal notes | Explain progress and retain staff reasoning. | Public history within the case; internal material requires its separate permission. |
| Notifications and audit | Track communication attempts and significant user or staff actions. | Permitted recipient, communications or oversight role as applicable. |
| Catalogue and guidance | Explain available services, requirements, targets, notices and FAQs. | Public when published; edits require the relevant management right. |

### Data handling responsibilities

The current schema contains 21 tables. Minimise collected evidence and public exposure, protect database and upload backups together, and keep production credentials outside public downloads. The operator must approve retention, support, incident handling and authorised removal procedures before public operation. This PRD does not treat the software alone as evidence of legal compliance.

## 8 Quality requirements

Security and workflow requirements are release conditions. The numeric performance, usability and recovery values below are proposed pilot targets for review. They are not measured results, hosting guarantees or features that the current tests have proven.

| Quality area | Product acceptance condition | Verification |
| --- | --- | --- |
| Access and confidentiality | Refuse unauthorised request reads, decisions, administration and downloads; keep drafts owner-private. | Repeat the own-user, other-user, department and role tests; manually inspect the hosted configuration. |
| Workflow integrity | Prevent invalid transitions, duplicate completed data loads and stale writes; avoid partial database or file success. | Exercise failure, rollback, repeat submission and concurrent-update scenarios. |
| Usability and inclusion | All principal forms have labels and keyboard access. Proposed target: 8 of 10 pilot participants complete the agreed main task without facilitator intervention. | Run an observed browser and mobile task exercise, recording completion and assistance. |
| Response time | Proposed target: 95 percent of core form and page responses within 2.5 seconds under 10 concurrent test users and 10,000 stored requests. Exclude file transfer and external-provider latency. | Benchmark on the target hosting plan with a defined dataset; retain the measurements. |
| Continuity and recovery | Core case work remains available when an optional provider fails. Proposed recovery targets: data loss no greater than 24 hours and restoration within 4 hours. | Test configured-provider failure and an actual protected backup restoration. |
| Maintainability and deployment | Separate configuration, private storage and application code; provide schema references, clean routes, setup guidance and reproducible checks. | Validate the extracted package, target database, rewrite rules, cron environment and protected paths. |

### Operational measurement

Measure response time at the web boundary and record the workload, hosting plan, database size and test date. Measure recovery from the start of a declared restoration until authorised users can verify the recovered records. Backups, monitoring and operator procedures must be configured and tested; the product package does not perform an unconfigured backup service.

Accessibility and device acceptance require direct review of keyboard navigation, focus, contrast, form errors, slider controls and downloadable documents. Passing structural or HTTP checks alone does not establish full accessibility or browser conformity.

## 9 Success measures and acceptance

### Product success measures

The following are proposed targets for an authorised pilot after a baseline is recorded. Prepared project records and automated tests are excluded from adoption and service-outcome calculations. Review results by service type so that simple and complex cases are not treated as equivalent work.

| Measure | Definition and evidence | Proposed pilot target |
| --- | --- | --- |
| Task completion | Participants completing the agreed submission or tracking task without facilitator intervention, divided by participants attempting it. | At least 8 of 10 observed participants. |
| Time to first review | Median elapsed calendar time from submission to the first In review event, compared with a recorded equivalent service baseline. | At least 20 percent lower than baseline. |
| Within-target completion | Eligible closed service cases completed by their recorded target, divided by all eligible closed cases in the pilot period. | At least 90 percent; report by service type. |
| Traceable decisions | Decisions with an authorised actor, reason, time and linked case history, divided by all decisions. | 100 percent. |
| Citizen feedback | Mean rating among genuine pilot decisions with feedback; also disclose the number of ratings and response rate. | At least 4 out of 5; interpret with the response count. |

### Acceptance for the academic release

The group will accept the academic release when the citizen submission, departmental review, information reply, decision, private tracking and permission-boundary journeys are reproducible; the extracted source matches the deployment archive; and the report, SRS, diagrams, manuals, slides and PRD agree on the core process.

The release 1.0.4 evidence records 267 passed automated checks: 124 workflow and security HTTP assertions, 13 fresh live-installation assertions, 5 installer regressions, 113 landing and dataset assertions and 12 carousel interaction checks. These establish selected application behaviour under the recorded local test conditions.

### Acceptance for public operation

Before public operation, the operator must accept the hosted MySQL workflow, TLS and private-path protection, cron processing, browser and accessibility review, backup restoration and service ownership. Each enabled provider needs its own successful account-level test. SMS, email receipt, live AI, workload performance and measured service improvement are not established by the local automated results.

## 10 Delivery plan and responsibilities

The remaining working schedule starts on 23 October 2026. The overall portal project window began on 28 September. The MIT 820 guide describes an eleven-week sequence, while the supplied portal window contains nine calendar weeks. We retain the required review gates and the stated final dates and submit the compressed mapping for supervisor confirmation. All dates below are in West Africa Time.

| Dates in 2026 | Milestone and evidence | Lead |
| --- | --- | --- |
| 23 to 25 October | Confirm proposal approval record, business case, scope and evidence owners. | Bolaji with all members |
| 26 October to 1 November | Review SRS, ADD, models, interface designs and repository baseline. | Development and Documentation |
| 2 to 8 November | Repeat core citizen and staff integration journeys on staging. | Aminu and John |
| 9 to 15 November | Review security boundaries, consented usability checks and defect fixes. | Bolaji coordinates QA |
| 16 to 22 November | Verify hosted MySQL, HTTPS, providers, workload and restoration. | Aminu and John |
| 23 to 27 November | Freeze release, complete report and manuals, rehearse live defence. | All members |
| 28 November | Final presentation and independent live system demonstration. | All five members |
| 29 November | Apply agreed corrections and submit before 11:59 p.m. WAT. | Bolaji coordinates submission |

### Contribution ownership

Aminu leads architecture, database, authentication, permissions, workflow automation and deployment integration. John leads citizen and staff screens, forms, document interactions, tracking and notification integration. Mustapha leads requirements, process descriptions, manuals and report editing. Odira leads data and workflow diagrams, technical evidence and presentation materials. Bolaji maintains the task and bug registers, coordinates QA and manages the presentation and submission checklist.

Each milestone needs a named owner, evidence of completion and review by another group member. A feature is not complete solely because its page opens; its success path and relevant permission or failure path must also work.

## 11 Risks and change control

| Risk or dependency | Product effect | Treatment and owner |
| --- | --- | --- |
| Unclear service authority or rules | A catalogue or approval may imply powers that the operator does not hold. | Operator approves service ownership, requirements, fees and approval conditions before public use; Bolaji records the decision. |
| Excessive access or exposed evidence | Citizen documents or case activity could reach the wrong reader. | Aminu reviews role and scope controls; John tests direct URLs and upload/download paths; Documentation records the evidence. |
| Host or configuration failure | Installer, private storage, clean routes or scheduled jobs may fail on the target account. | Development rehearses the actual hosting setup and recovery; preserve the last working package and protected backup. |
| Provider account or delivery failure | Citizens may miss external notices or the AI service may be unavailable. | Use the working in-app and local-guide paths; validate each enabled provider, queue state and receipt separately. |
| Uncontrolled scope changes | Additional features may displace testing, manuals and defence preparation. | Bolaji records impact and agreement before scheduling; keep the presentation freeze on 23 to 27 November. |
| Unreviewed data and evidence | Prepared records may be mistaken for actual service outcomes or reported tests may lack support. | Documentation distinguishes fixture data, automated results, proposed targets and pilot evidence; retain dated source records. |

### Change procedure

Record the request, affected PRD and SRS identifiers, reason, owner and expected effect on users, security, data and the schedule. Development estimates the implementation and regression work. Documentation identifies the report, diagrams and manual changes. Bolaji coordinates the group decision and any supervisor review that the course requires.

After an accepted change, update the implementation version, this document where needed, the traceability record and the relevant tests. Reopen a milestone if the change invalidates its evidence. Preserve previous released packages so that the group can explain what was submitted and reproduce the assessed build.

### Release decisions

The project group reviews academic readiness. The supervisor assesses the submission under the course guidelines. A future operating organisation separately accepts authority, service rules, data governance, hosting and ongoing support. An academic presentation or a passing local test suite does not substitute for that operating decision.

## 12 Traceability and review

This matrix connects product intent to the existing SRS identifiers. FR means functional requirement and NFR means non-functional requirement. Interface and dataset additions in release 1.0.4 retain the same core requirements and are documented in the release notes.

| Product requirement | Related SRS requirements | Acceptance evidence |
| --- | --- | --- |
| PRD01 Accounts | FR01 FR02 FR20; NFR01 NFR03 | Registration, recovery, replay, MFA and session checks. |
| PRD02 Discovery | FR19 FR22 FR23; NFR06 | Published catalogue, landing assets and carousel interactions. |
| PRD03 Submissions | FR04 FR05 FR06 FR08; NFR02 | Complaint, permit, draft and routing journeys. |
| PRD04 Evidence | FR07; NFR01 NFR04 | Content validation, private download and internal-evidence checks. |
| PRD05 Processing | FR03 FR08 FR09; NFR01 NFR02 | Department scope, transitions, permissions and stale writes. |
| PRD06 Tracking | FR10 FR18; NFR01 NFR10 | Private-code tracking, receipt and limited verification. |
| PRD07 Communications | FR11 FR12 FR13 FR14; NFR04 NFR05 | Held delivery, templates, cancellation, retry and escalation. |
| PRD08 Assistance | FR15; NFR05 NFR10 | Local guide; provider-specific live acceptance when configured. |
| PRD09 Follow up | FR16; NFR02 | Withdrawal, reopening and one feedback entry per completed cycle. |
| PRD10 Oversight | FR17; NFR01 NFR04 | Scoped totals, protected CSV output and recorded audit actions. |
| PRD11 Administration | FR03 FR19; NFR01 NFR03 | Account, role, catalogue and direct admin access checks. |
| PRD12 Deployment | FR21 FR24; NFR07 NFR08 NFR09 | Setup-key, storage, package and repeat-loading checks; hosted and recovery acceptance. |

### Reference documents

Group 1 project proposal and team schedule dated 23 October 2026; Software Requirements Specification for release 1.0.4; System Design and Data Dictionary; CivicFlow release 1.0.4 source, release notes and machine-readable acceptance records. These project records provide the basis for the scope and traceability in this PRD.

### Review record

Group review and date __________________________________________
Supervisor review and date ______________________________________
