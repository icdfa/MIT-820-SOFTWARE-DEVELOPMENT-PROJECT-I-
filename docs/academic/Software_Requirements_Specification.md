# Software Requirements Specification

Smart Government Service Delivery Platform

Group 1   MIT 820   Application 1.0.4   Document 1.1   23 October 2026

## 1 Introduction

This specification defines the functional behaviour and acceptance boundaries for CivicFlow release 1.0.4. It supports development, review and assessment of the Smart Government Service Delivery Platform. It covers the citizen portal, staff processing, administration, communications and the installation package. The companion academic report explains the rationale and evaluation, while the design document provides the complete schema and diagrams.

### 1.1 Intended readers

Citizens and staff use the manuals rather than this specification for daily work. Developers, testers, the project supervisor and the operating organisation use this document to assess what the application must do and how each requirement can be verified.

### 1.2 Terminology

| Term | Meaning |
| --- | --- |
| Request | A complaint or permit application and its recorded lifecycle. |
| Scope | The records a user may access: own, department or organisation. |
| Permission | A server-checked action right such as review, decide or export. |
| Service target | A configured deadline in calendar hours, not a statutory guarantee. |
| Sent | A provider accepted the queued message; destination receipt remains unconfirmed. |
| Live mode | Installation without demonstration accounts or requests and with unpublished starter services. |

### 1.3 Requirement notation

FR denotes a functional requirement. NFR denotes a non-functional or operational requirement. The word shall states required application behaviour. A proposed deployment check is identified separately from an executed automated assertion. This structure is informed by requirements-engineering principles, without claiming ISO/IEC/IEEE 29148 conformity.

## 2 Product description

The application is a single-instance PHP web system with a relational database and private filesystem storage. The public catalogue leads citizens to the appropriate service form. Staff use department or organisation scopes. Scheduled CLI work handles escalation and external notifications. Email, SMS and AI connections are optional; the request workflow and in-app notifications remain usable without them.

### 2.1 Actors

| Actor | Required scope and responsibility |
| --- | --- |
| Citizen | Own requests, documents, replies, preferences, recovery and feedback. |
| Service officer | Submitted records in assigned department and permitted processing actions. |
| Department head | Departmental processing and additional export oversight. |
| Auditor | Organisation-level submitted records, reports and audit, without default decisions or internal evidence. |
| Communications officer | Templates, announcements, FAQs, compose and outbox without case access. |
| Administrator | Workspace, identity, permissions, catalogue and integration configuration. |

### 2.2 Assumptions and constraints

An authorised organisation will define its actual services, departments and approval conditions. Staff identities and account permissions require operator management. Users need a modern browser and network access. The host must provide the stated PHP extensions, writable private storage and an available database. Cron, valid TLS and provider credentials are deployment dependencies. Live email verification confirms mailbox control rather than legal identity.

## 3 Functional requirements

### FR01 Citizen account and sign in

The system shall create Citizen accounts only; validate names, email, phone and a 12–72 byte password containing letters and numbers.

Acceptance condition: Registration remains Citizen even with a forged role; overlength passwords are refused.

### FR02 Verification and recovery

The system shall use single-use email-verification tokens valid for 24 hours and reset tokens valid for one hour.

Acceptance condition: Valid tokens work once; replay and old authenticated sessions are refused.

### FR03 Granular role permissions

The system shall support six default roles, custom roles and additional staff roles; check actions and record scope on the server.

Acceptance condition: Restricted reviewer can read its department request but cannot decide or assign.

### FR04 Complaint submission

The system shall capture service, subject, description, location, state, organisation if relevant, priority and declaration.

Acceptance condition: Submission creates a complaint reference, target and routing record.

### FR05 Permit applications

The system shall show service requirements and require at least one attachment where supporting requirements are listed.

Acceptance condition: No-document submission is refused and valid PDF submission succeeds.

### FR06 Private drafts

The system shall save an editable owner-only draft without a target or assignment until submitted.

Acceptance condition: Other citizens and staff cannot inspect the draft by direct URL.

### FR07 Private documents

The system shall validate MIME, size, count and quota; store a random private filename and a SHA-256 integrity record.

Acceptance condition: MIME-spoofed PHP is refused and unauthorised direct downloads fail.

### FR08 Department routing

The system shall route by service and select a least-loaded active authorised departmental reviewer where available.

Acceptance condition: A submitted request has its service department and an appropriate assigned officer where available.

### FR09 Controlled processing

The system shall enforce allowed review, information, decision and closure transitions with reasons and version checks.

Acceptance condition: Invalid transition and stale write leave state unchanged.

### FR10 Status tracking

The system shall provide full signed-in history and limited public progress using reference plus private code.

Acceptance condition: Correct code shows progress; incorrect code and personal-data exposure are refused.

### FR11 Notifications and preferences

The system shall create in-app updates and queue routine email/SMS according to preferences and enabled channels.

Acceptance condition: Unconfigured external channels are held without marking them sent.

### FR12 Outbox management

The system shall provide channel/state/search filters, 50-record pages, details and authorised retry/cancel controls.

Acceptance condition: Eligible notice is cancelled; disabled channel cannot be released.

### FR13 Reusable templates

The system shall provide sanitised HTML, plain alternatives, previews and escaped placeholders.

Acceptance condition: Script/event markup is stripped and substituted citizen content remains escaped.

### FR14 Scheduled automation

The system shall escalate overdue open cycles once and process delivery with lock, conditional claim and retry backoff.

Acceptance condition: Repeated escalation does not duplicate history; five SMTP failures stop automatic retries.

### FR15 Citizen assistance

The system shall offer a labelled FAQ guide and optional configured AI limited to public service facts.

Acceptance condition: Guide answers without credentials; live AI acceptance needs a configured account.

### FR16 Feedback and follow up

The system shall permit one rating after a decision, owner withdrawal at eligible stages and reopening of resolved complaints.

Acceptance condition: Feedback is recorded; reopening refreshes target and clears prior feedback.

### FR17 Reports and audit

The system shall present scoped totals and workload, export at most 5,000 matching CSV records, and record key actions.

Acceptance condition: Export respects scope and prefixes potential spreadsheet formulas.

### FR18 Printable and verifiable records

The system shall provide private receipts and printable approved permit records; public verification excludes applicant details.

Acceptance condition: Approved record renders and public verification omits the applicant.

### FR19 Workspace administration

The system shall manage people, departments, catalogue, public guidance, announcements and settings under permission checks.

Acceptance condition: Citizen admin GET/POST is denied; administrator screens render.

### FR20 Account security

The system shall provide TOTP MFA, eight single-use recovery codes, session listing, revocation and optional staff policy.

Acceptance condition: MFA gates login, recovery replay fails and revoked devices lose access.

### FR21 Repeatable installation

The system shall use a private setup key, fresh schema, chosen administrator, installation lock and clean Apache routes.

Acceptance condition: Missing/incorrect key is refused; completed installation locks; extracted storage folders exist.

### FR22 Citizen portal adaptation

The system shall adapt the supplied student portal shell to citizen services with independent branding and local assets.

Acceptance condition: Live workspace has no academic branding and its local assets are available.

### FR23 Public landing and service discovery

The system shall provide three locally stored slider images, previous and next controls, direct slide selection and pause. It shall stop automatic playback for reduced motion, keyboard focus, hover and a hidden page. Service search, departments, current notices, FAQs and account entry shall remain available. With scripts disabled, the first image and server-backed service and tracking forms shall remain usable.

Acceptance condition: Search and tracking use normal routes; the interaction fixture checks the relevant script states. A rendered desktop, mobile, keyboard and reduced-motion review remains a separate acceptance activity.

### FR24 Controlled evaluation record loading

The system shall allow only a primary administrator to add the prepared 26-account and 36-request dataset after explicit confirmation and a chosen password. It shall preserve existing accounts, configuration, requests and private files. A repeated completed load shall not duplicate records or change prior passwords. Prepared contact addresses shall use reserved domains and notification examples shall remain cancelled without provider attempts.

Acceptance condition: The 113 landing and dataset assertions verify the supplied record set, idempotency, ordinary sign-in and role boundaries. The directory export excludes passwords. Data-origin information remains available in About and relevant project approvals.

## 4 Business rules

| ID | Rule |
| --- | --- |
| BR01 | A draft stays owner-private and has no active target or assignment. |
| BR02 | Submitted must enter In review before an approval, resolution or rejection. |
| BR03 | Only a permit can become Approved and only a complaint can become Resolved. |
| BR04 | A citizen reply to Needs information returns the request to Submitted. |
| BR05 | Target time continues while more information is awaited. |
| BR06 | Withdrawal is limited to the owner at Draft, Submitted or Needs information. |
| BR07 | Only the owner can reopen a resolved complaint; reopening sets a fresh target and clears feedback. |
| BR08 | A citizen account cannot receive staff permissions or additional staff roles through public registration. |
| BR09 | An officer must have the required action right and authorised record scope. |
| BR10 | Internal documents require internal-note visibility as well as document and case access. |
| BR11 | Sent represents provider acceptance and automatic delivery stops after five failed attempts. |
| BR12 | Retain an active primary administrator and prevent an administrator removing its own access. |

## 5 Non functional and operational requirements

| ID | Requirement | Verification state |
| --- | --- | --- |
| NFR01 | Confidentiality: restrict request reads, actions and downloads using server-side checks. | Selected own/department/custom-role paths verified by HTTP assertions. |
| NFR02 | Integrity: use prepared calls, transactions, workflow rules and request versions. | Invalid transitions, stale writes and recorded outcomes verified. |
| NFR03 | Account protection: hash passwords, enforce the byte limit, expire tokens and revoke invalid sessions. | Password boundary, token replay, MFA and device assertions verified. |
| NFR04 | Safe output: escape citizen content, sanitise email HTML and protect CSV formula values. | Selected script, template and CSV checks passed. |
| NFR05 | Failure handling: preserve outbox state and provide a local guide when external channels are unavailable. | Held delivery and controlled SMTP failure verified; live account outage acceptance pending. |
| NFR06 | Usability: label forms and actions, maintain coherent navigation and support keyboard/mobile use. | Structural labels checked; manual browser and accessibility review pending. |
| NFR07 | Maintainability: provide separated modules, schema references and an executable acceptance harness. | Files supplied and syntax checked; future changes require regression review. |
| NFR08 | Deployability: retain private directories, support locked installation and use clean routes. | Extracted packaging gate and local-router checks passed; target host HTTP checks pending. |
| NFR09 | Capacity and recovery: proposed pilot targets are 95 percent of principal page responses within 2.5 seconds with 10 concurrent test users and 10,000 requests, RPO no greater than 24 hours and RTO within four hours. Exclude file transfer and provider latency. | Proposed targets for host acceptance. No completed workload benchmark or restoration result is asserted. |
| NFR10 | Privacy governance: minimise exposed data and apply authority-specific notices, retention and incident procedures. | Public tracking/verification boundaries checked; organisational policy approval pending. |

## 6 External interfaces

The browser interface uses server-rendered forms and local CSS/JavaScript. Mutating requests require CSRF protection. `/api/assistant` accepts a question and the chosen AI-use flag. Public tracking accepts both the reference and private code. Document routes enforce the parent's scope and return attachment downloads.

PDO connects to MySQL/MariaDB for deployment or SQLite for evaluation. SMTP uses the bundled PHPMailer 7.1.1 classes with TLS. Optional SMS uses the operator's HTTPS Termii endpoint and approved sender. Optional AI uses a configured Responses-compatible model and server-side API key. No provider secret is exposed to browser scripts. The CLI worker is not a public HTTP endpoint.

## 7 Data requirements

The physical schema has 21 tables, seven explicit secondary indexes and the primary, unique and foreign-key constraints defined in app/database.php. Users and requests have independent identifiers. A request reference is unique and differs from the private tracking code. Files store MIME, byte count, a random stored name and SHA-256 metadata. History distinguishes public and internal entries.

Records are retained through the interface rather than deleted. Account suspension and service deactivation preserve history. Automated retention/erasure is outside this release. The operating organisation must define backup, retention and authorised support procedures. The detailed design file includes every column and declared foreign key, including the distinction between logical and database-enforced associations.

## 8 Principal use cases

### UC01 Submit a service request

Actor: Citizen. Preconditions: an active account and an active selected service. Main flow: read requirements; open the request form; enter details; attach supporting evidence; confirm accuracy; submit. The application validates the input, records the request and history, assigns its department, creates a target and queues notices.

Alternative flows: save a draft without routing; reject an unsupported MIME type or file limit; reject a permit without required supporting evidence; refuse an expired or reused form token. Postcondition: the citizen sees the recorded request or a specific validation message, and no partial failed file transaction is presented as successful.

### UC02 Review and request information

Actor: authorised department reviewer. Preconditions: accessible Submitted request and review permission. Main flow: read the record; start review; record a clear information request; citizen supplies a reply and optional evidence; request returns to Submitted for further review. An authorised officer can also resume from Needs information.

Alternative flows: refuse another department's record; refuse a stale update version; hide internal notes and their files from citizens. Postcondition: state, public history and relevant notices reflect the committed action.

### UC03 Record a final decision

Actor: authorised decision maker. Preconditions: request In review, decision permission and current version. Main flow: approve a permit, resolve a complaint or reject either; enter the reason; commit the decision. Postcondition: the record closes, history and notices are stored, and applicable print/feedback functions become available.

Alternative flows: reject an invalid transition, wrong request-kind outcome or stale version. A resolved complaint may later reopen through the owner's separate action.

### UC04 Process external delivery

Actor: scheduled worker or authorised manual operator. Preconditions: installed system and eligible queued records. Main flow: take the worker lock; check channel readiness; conditionally claim a record; attempt the configured provider; store acceptance or a retry result. Postcondition: an accurate outbox state and next-attempt value.

Alternative flows: retain Configuration required when disabled; skip a record that another operation changed; retry with backoff; stop after five failures. A provider acceptance is not treated as proof of destination delivery.

## 9 Acceptance traceability

| Requirement group | Primary evidence |
| --- | --- |
| FR01–FR03 and FR20 | Registration, forged role, token replay, MFA, session and custom-role assertions. |
| FR04–FR10 and FR16 | Complaint/permit, routing, draft privacy, file, state, tracking, feedback and reopening assertions. |
| FR11–FR14 | Held outbox, cancellation, escaped templates, controlled SMTP refusal, retry limit and escalation assertions. |
| FR15 | Local guide assertion; operator-configured live AI acceptance remains pending. |
| FR17–FR19 | Reports, CSV protection, audit population and direct admin denial checks. |
| FR21–FR22 | Setup-key and live-mode suites, asset availability, branding check and extraction gate. |
| FR23 and FR24 | 113 landing/dataset assertions and 12 carousel fixture assertions, with separate manual browser acceptance. |

## 10 Approval and change control

The project manager coordinates requirement changes with Development and Documentation. Each change should identify the affected FR/NFR, owner, implementation revision, regression evidence and document update. The supervisor and group should review this specification before using it as the final acceptance baseline. Approval signatures, where required by the institution, belong in the main report or the institution's own form.

## 11 Stakeholder validation and course review

The stakeholder groups are citizens, service officers, department heads, auditors, communications staff and technical administrators. Requirements currently derive from the brief and implemented behaviour. A planned walkthrough will record service rules, exceptions and acceptance decisions for each group. Consent and anonymisation apply where people participate in evaluation. Bolaji maintains the review and change register; Development and Documentation assess the affected FR, NFR, diagrams, tests and manuals before the supervisor accepts a scope change.
