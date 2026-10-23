# Demonstration Access and Recording Guide

Local evaluation hosted access record and recording checklist

Group 1   MIT 820   Application 1.0.4   Document 1.1   23 October 2026

This document provides reproducible local evaluation access, the hosted-access record and a live defence run sheet for CivicFlow 1.0.4. MIT 820 requires a functioning system that can be accessed or run independently at defence. The team-supplied project address is https://iconicproject.icdfafoundation.com.ng/. Complete the check and access fields after verifying the intended evaluation installation. Add a real recording where the submission instructions require one.

## 1 Local demonstration access

- Extract the source directory to a machine with PHP 8.2+ and PDO SQLite, Mbstring, Fileinfo, OpenSSL, cURL and DOM/XML.

- Open a terminal in that source directory and run php -S 127.0.0.1:8080 router.php.

- Open http://127.0.0.1:8080/install in that machine’s browser.

- For the GitHub source, first run python3 tools/prepare_local.py from the repository root and read the generated src/config/setup.key locally. For the private cPanel installation archive, use its local START_HERE.html. Choose SQLite and keep HTTPS enforcement off for loopback-only evaluation.

- Create your administrator, enable demonstration mode and choose a sample-account password. Use synthetic information.

- Complete setup and test the accounts below. Keep the administrator password separate from shared presentation access.

| Role | Email or sign in identity | Password |
| --- | --- | --- |
| Administrator | The administrator you create in setup | Your chosen administrator password. |
| Citizen | citizen@example.test | The sample password chosen during setup. |
| Public Works officer | works.officer@example.test | The same chosen sample password. |
| Commerce officer | trade.officer@example.test | The same chosen sample password. |
| Environment officer | env.officer@example.test | The same chosen sample password. |
| Planning officer | plan.officer@example.test | The same chosen sample password. |

Loopback access is available only on the machine running the server. The PHP built-in server is a local development tool. For supervisor remote access, deploy a separate protected demonstration instance using the cPanel guide and verify it from another connection.

## 2 Hosted access record

Complete these fields only after confirming the demonstration installation. Do not insert the production administrator credentials into a broadly shared report.

- Verified demonstration URL __________________________________

- Date checked and checker __________________________________

- Shared citizen account _____________________________________

- Shared officer account and department ________________________

- Password delivery method __________________________________

- Access expiry or removal date ________________________________

## 3 Demonstration sequence

| Step | Action | Evidence to show |
| --- | --- | --- |
| 1 | Citizen creates a complaint. | Reference, target, department and receipt. |
| 2 | Correct officer starts review and asks for information. | Controlled state and recorded reason. |
| 3 | Citizen replies with harmless evidence. | Return to Submitted and private document. |
| 4 | Officer records a decision. | Closed record, history and feedback control. |
| 5 | Submit and approve a permit. | Approval record and limited public verification. |
| 6 | Use a different citizen and another department. | Denied direct record and attachment access. |
| 7 | Add an internal note with evidence. | Citizen cannot see or download either. |
| 8 | Show notifications, outbox, guide and reports. | Accurate held/provider states and scoped evidence. |

If a provider is not configured, demonstrate the local guide and the held outbox honestly. A local guide answer is not evidence of a successful AI-provider call. Do not manufacture an accepted email, SMS receipt or hosted access result.

## 4 Recording checklist when required

- Confirm the actual submission rules for duration, file type, naming and maximum upload size.

- Record a real browser session on the intended demonstration instance. Show the address sufficiently to identify the environment.

- Use synthetic requests and harmless documents. Hide setup keys, real passwords, MFA secrets, provider keys and private citizen information.

- Narrate the problem, the main workflow, a permission-denial example and what the test evidence establishes.

- Keep provider limitations visible and explain any local fallback.

- Play the complete exported recording and confirm readable text, audible narration, no accidental secrets and an accessible file.

- Place the checked recording in the submission folder only if required, and enter its real filename or verified access link below.

Recording filename or verified link ______________________________

Recorded by ____________________   Reviewed by ____________________

Date ____________________   Duration ____________________

## 5 Suggested presentation allocation

| Member | Suggested presentation focus |
| --- | --- |
| Bolaji Watson | Problem, coordination, scope and final readiness. |
| Saidu Mustapha Suleman | Requirements, acceptance criteria and manuals. |
| Egbue Odira Celestine | Data relationships, workflow diagrams and report documentation. |
| John Ojuekaiye Mayowa | Citizen portal and request demonstration. |
| Aminu Idris | Architecture, permissions, automation and deployment. |

Review the allocation together and ensure each member can explain the part they present. Confirm actual individual contributions and any institution-specific declarations before submission.

## 6 Current role walkthrough and presentation gates

Load the prepared 26-account dataset on a separate evaluation instance for all role journeys. The account directory identifies citizen, officer, head, auditor and communications access; the existing administrator supplies the sixth role. Share approved credentials privately.

Verify independent live access and a local fallback before 28 November. A permitted recording supports the independently accessible or runnable system. Record panel corrections and submit by 29 November before 11:59 p.m. WAT.
