# Requirements and acceptance matrix

**Project:** Smart Government Service Delivery Platform (CivicFlow)  
**Release:** 1.0.2  
**Prepared for:** Group 1, Software Development Project 1  
**Purpose:** Provide a traceable digital route from a citizen's complaint or application to a departmental response.

## Problem and proposed outcome

Citizens lose time through queues, repeat visits and unclear service status. Departments also need a reliable record of submissions, documents, ownership and decisions. CivicFlow provides one citizen account, service-specific online forms, permission-controlled staff processing, a visible status history and scheduled notifications. The platform supports e-government service delivery through digital records and connected departmental workflows.

## Users and responsibilities

Citizens create their own records and respond to information requests. Service officers process submissions within their department. Department heads oversee departmental workload and reports. Communications officers maintain service guidance and delivery messages. Auditors inspect submitted records and recorded actions. Administrators control accounts, permissions, catalogue and integrations.

Permissions are additive across staff roles and checked on the server for pages, mutations and downloads. Public registration cannot request a staff role. The protected administrator role retains full access. Drafts remain private to their owner before submission.

## Functional coverage

| ID | Requirement | Implementation / acceptance evidence |
|---|---|---|
| FR01 | Citizen registration and login | Public registration creates citizen only; password hash, CSRF, rate limits and session checks |
| FR02 | Account recovery and verification | Single-use one-hour reset and 24-hour email-verification tokens; queued authentication messages |
| FR03 | Role-based access with granular permissions | Built-in/custom roles, additional staff roles, scope and individual review/decision/routing/export rights |
| FR04 | Complaint submission | Service form, location, state, priority, optional evidence and unique reference |
| FR05 | Permit applications and documents | Requirements shown, supporting upload required when listed, officer document review |
| FR06 | Drafts | Private editable draft; no routing or target until submitted |
| FR07 | Private document handling | MIME/size/count/quota validation, random stored names, scoped download and internal-file visibility |
| FR08 | Department routing | Service selects department; least-loaded authorised available officer assigned where possible |
| FR09 | Back-office processing | Review, information request, citizen reply, approval/resolution/rejection and reason history |
| FR10 | Status tracking | Signed-in full history; public limited progress requires reference and private code |
| FR11 | Notifications | Immediate in-app messages; optional email/SMS preferences; durable delivery outbox |
| FR12 | Email outbox management | Search/filters/pages, details, retry, cancellation, compose and rendered message record |
| FR13 | Reusable message templates | HTML/plain alternatives, visual editor, placeholders, sanitisation and preview |
| FR14 | Automated workflows | Calendar-hour targets, overdue escalation, CLI cron, lock, retry backoff and maximum attempts |
| FR15 | AI-powered assistance | Optional Responses API with grounded public facts, explicit choice, no private-case access and labelled guide fallback |
| FR16 | Feedback and follow-up | One rating per closed decision; complaint reopening and eligible withdrawal |
| FR17 | Reports and audit | Scoped totals, SLA/feedback/workload, CSV formula protection and recorded actions |
| FR18 | Printable records | Browser print/PDF receipt and approved permit record; public verification excludes applicant details |
| FR19 | Administrative controls | People, roles, departments, services, announcements, FAQs, workspace identity and provider settings |
| FR20 | Stronger account protection | TOTP MFA, single-use recovery codes, staff policy, device list and revocation |
| FR21 | cPanel deployment | PHP/PDO, browser installer, locked setup, bundled SMTP library and Apache clean routes |
| FR22 | Template adaptation | Reused campus student shell/layout patterns; citizen terminology, green identity and new emblem |

## Quality and security requirements

| Area | Delivered control | Operational dependency |
|---|---|---|
| Confidentiality | Own/department/organisation scopes; private uploads; internal-note/file checks | Correct role assignment, hosting permissions and protected backups |
| Integrity | Prepared SQL, server validation, CSRF, workflow rules, transactions and optimistic versions | Database availability and managed releases |
| Authentication | Password hashing, limits, expiry, recovery, MFA and revoked-session checks | TLS, working recovery email and protection of recovery codes |
| Output safety | Escaped user content; sanitised email markup; CSV formula protection | Maintained PHP/dependencies and browser/host configuration |
| Availability | Local assets, guide fallback, durable queue and retry worker | Hosting capacity, monitoring, cron and provider connectivity |
| Maintainability | Separated app modules, schema references, runbook and executable acceptance tests | Staging, backup/restore and change control |
| Accessibility | Semantic forms, labels, visible focus, skip link, text status, responsive layout and live chat status | Manual device, screen-reader and keyboard acceptance on target hosting |
| Privacy | Limited public tracking/verification, configurable preferences and no private-case AI context | Authority-specific lawful processing, notice, retention and incident procedures |

No independent accessibility certification, load benchmark, penetration-test certification or legal-compliance certification is asserted.

## Business rules

1. A complaint is resolved; a permit is approved. Both can be rejected after review.
2. Submitted requests must enter review before a decision. Closed records cannot receive ordinary conversation replies.
3. A citizen's response to Needs information returns the record to Submitted.
4. Published deadlines use calendar hours. Waiting for information does not pause the deadline.
5. An overdue open cycle is escalated once by the scheduled job. Reopening a resolved complaint refreshes its target and permits a new escalation.
6. No officer may process a request outside their authorised scope. Cross-department routing is separately controlled.
7. Internal notes and attached staff documents require internal-note permission. Public tracking exposes status only.
8. Routine external channels follow account preferences. Authentication recovery/verification mail is sent for the account-security action.
9. “Sent” is provider acceptance, not guaranteed inbox/handset receipt. Delivery stops automatically after five failed attempts.
10. Live installation starts without demonstration accounts or requests. Starter services require operator activation.

## Data scope and integrations

Core entities are departments, services, users, roles, role permissions, additional roles, requests, events, attachments, notifications, outbox, templates, settings, reset/verification tokens, device sessions, recovery codes, audit logs and feedback.

MySQL/MariaDB is the deployment database. SQLite supports local evaluation. SMTP handles email, Termii provides optional SMS, and the OpenAI Responses API provides optional AI. cPanel or a compatible cloud-hosted PHP environment supplies the runtime. Provider credentials and enabled channels are configured by the operator rather than included in source.

## Acceptance and exclusions

The attached validation report records what was actually run. The included matrix maps the supplied proposal to functionality; no separate marking rubric was provided, so no grade or complete rubric coverage is claimed.

The release excludes payment processing, national identity checks, government-system synchronisation, cryptographically signed statutory permits, antivirus scanning, automatic records-retention enforcement and recipient delivery webhooks. These require additional authority/provider requirements. Hosting and live-provider acceptance remain deployment activities.
