# Staff and Administrator Manual

Review decisions communications and operational controls

Group 1   MIT 820   Application 1.0.4   Document 1.1   23 October 2026

This manual covers the service officer, department head, auditor, communications and administrator workspaces. Your current permissions determine which menus and actions appear. Direct requests and downloads use the same permission and record-scope checks.

## 1 Roles and operating responsibilities

| Role | Default operational access |
| --- | --- |
| Service officer | Submitted requests in the assigned department, review, decisions, assignment, citizen messages and internal evidence. |
| Department head | Officer functions plus department CSV export. |
| Auditor | Submitted records and reports across departments, CSV and audit. No default case decisions, attachment downloads or internal notes. |
| Communications officer | Knowledge centre, announcements, outbox, compose and templates without case access. |
| Administrator | Workspace configuration, catalogue, accounts, permissions, integrations and oversight. |

An assignment filter does not replace scope: a department officer may see the department’s submitted records even when another officer is assigned. Custom roles can separate reviewing from deciding. Additional staff roles combine rights. Drafts remain owner-private and do not enter staff processing.

## 2 Sign in and protect the account

Sign in with the account created or approved by the administrator. Complete MFA when enabled. Use Account security to enrol an authenticator, save recovery codes and revoke other devices. If staff MFA is required, enrolment is needed before protected work. Do not share a department login between officers, because actions must remain attributable to an individual account.

## 3 Review the queue

- Open Request workspace or the incoming queue from Dashboard.

- Use Assigned to me, Overdue queue, reference/subject search and status/type filters.

- Open a request and read the service, citizen statement, department, target and complete public history.

- Download only the evidence needed for the review. Keep downloaded material in the organisation’s authorised location.

- Check whether the next action is review, an information request, routing or a decision within your permission.

## 4 Start review and ask for information

Choose In review for a submitted request and record a clear reason or progress update. Examine the evidence against the published service requirements. If information is missing, choose Needs information and explain the exact document or clarification required. The citizen can reply through the same record, returning it to Submitted. Begin review again, or resume from Needs information where permitted, before deciding.

Targets use calendar hours and continue while information is awaited. Do not describe the target as a statutory deadline unless the responsible authority has separately established that rule. If another update changes the request version before your action saves, reload and review the new record before trying again.

## 5 Decisions and routing

### 5.1 Record an outcome

- Confirm the record is In review and you have decision permission.

- Select Approved for a permit, Resolved for a complaint, or Rejected with a clear reason.

- Record the evidence and explanation necessary for the citizen to understand the result.

- Save and confirm that the new status and history appear. The application records closure and queues the associated notices.

A decision is an operational record within the portal. The authority must define who can issue approvals and what conditions apply. The assistant does not decide cases. Ordinary conversation closes with a final outcome, while the citizen may rate a decision or reopen a resolved complaint through the appropriate controls.

### 5.2 Assign or reroute

Use Assignment and routing to select an active authorised reviewing officer in the current department. Choose Unassigned if no suitable officer is available. Enter a routing reason. A department change requires separate cross-department routing permission and organisation-wide scope. Select an officer from the new department or leave unassigned. Closed requests and drafts cannot use this control.

## 6 Citizen messages and internal notes

Use Conversation for a public message on an open submitted record. Select Internal note only when the text should be restricted to authorised staff. Files attached with an internal note retain staff-only visibility. A citizen must not receive an internal file simply because they own the request. Review visibility before saving and keep personal information limited to what the service requires.

The history displays the actor and time. Internal-note permission governs reading as well as writing those notes. An auditor’s organisation-wide case view does not automatically give internal-note access. Use the authority’s approved channel for any discussion that should not be stored as part of the case record.

## 7 Communications workspace

### 7.1 Outbox

Open Notification outbox and filter by state, channel or recipient/subject. The list uses pages of 50 records. Open a record to inspect its queued text, attempts, provider result and dates. Authentication-message content is hidden to protect recovery or verification links.

| State | Meaning and staff action |
| --- | --- |
| Configuration required | Channel disabled. Configure and test it, review whether the notice is still relevant, then requeue explicitly. |
| Pending | Ready for the next worker. It can be cancelled before processing. |
| Processing | Worker has claimed it. Do not retry or cancel while sending. |
| Retry | A failed attempt has a scheduled next attempt. Investigate repeated failures. |
| Failed | Five failed attempts reached. Correct the cause before explicit requeue. |
| Sent | Provider accepted. Confirm destination receipt separately where necessary. |
| Cancelled | Operator cancelled the eligible queued message. |

Run workflow and delivery is available only with workflow permission. The manual web control processes up to five deliveries; the scheduled CLI command processes up to 20. A slow provider can extend a run. A private worker lock and conditional record claim reduce duplicate processing. A crash around a provider acknowledgement may still lead to another attempt, so exactly-once delivery is not guaranteed.

### 7.2 Compose and templates

Compose targets an active registered user, an available template and a permitted channel. Routine email/SMS follow recipient preferences. Review the message and create the notice; in-app recording is immediate and external delivery uses the outbox. This is not an unrestricted mailing tool for arbitrary addresses.

Email templates support a visual editor, HTML source, a plain-text alternative and a saved preview. Unsafe markup and event attributes are removed on save. Placeholders include {{name}}, {{title}}, {{message}}, {{reference}}, {{status}}, {{service}}, {{department}}, {{link}} and {{app_name}}. Existing outbox records retain their rendered copy after a template changes. Maintain public FAQs and announcements in Knowledge centre.

## 8 Reports audit and account administration

Reports show scoped totals, status distribution, open overdue records, ratings and the share of closed decisions within target. Export, where permitted, contains at most 5,000 matching records. Narrow filters for a larger dataset. The print control produces a readable report or receipt through the browser. Audit trail records key account, configuration, workflow and export actions. It is not a substitute for separate hosting or security monitoring.

Administrators manage staff accounts and departments in People and roles. Assign a department whenever a primary or additional role uses department scope. Review custom and additional roles carefully. Role/account changes invalidate affected sessions. Public citizens cannot receive additional staff roles, and the protected administrator role cannot be removed through the role editor. Keep at least one active primary administrator.

## 9 Service and operational maintenance

Review each service’s department, type, requirements, published fee and target before activating it. Live installations begin with inactive zero-fee starter services. Deactivate a service rather than deleting its history. Suspend accounts when access must stop. The application does not automatically erase retained records, rotate logs or scan attachments for malware.

Configuration holds the organisation identity and SMTP/SMS/AI settings. Blank credential fields preserve stored secrets; explicit remove checkboxes clear them. Enrol the administrator in MFA before requiring it for staff. Use the Setup and Deployment Guide for hosting, cron and verification. Back up the database, private uploads and installation key together and prove restoration on a separate environment. Keep sensitive support evidence out of public messages.

## 10 Preparing records for an assessment walkthrough

Only the primary administrator can open the prepared-data page. On a separate assessment or staging instance, review the confirmation, choose a password and load the supplied records. The additive loader creates 26 accounts and 36 requests covering the non-administrator roles and all eight request states. Your existing administrator remains unchanged. The account directory identifies the role and department for each evaluation account.

Keep that chosen password outside the report and public repository. Export the account directory when needed, then distribute authorised evaluation access through the agreed private channel. Existing data, configuration and account passwords are retained. A repeated completed load does not add the records again. The example email and SMS records remain cancelled and do not establish provider receipt.

## 11 Defence and operating records

Before defence, record the tested address, release, checker, role journeys and access results in the deployment record. Keep actual meeting decisions, defects and blockers in the project registers.
