# Presentation and demonstration guide

**Group 1 · Software Development Project 1 · 28 November 2026**

## Suggested presentation sequence

| Part | Focus | Suggested presenter |
|---|---|---|
| 1 | Citizen delays, project objective, scope and delivery coordination | Bolaji Watson |
| 2 | Requirements, actors, process and acceptance criteria | Saidu Mustapha Suleman |
| 3 | Data relationships, workflow and documentation approach | Egbue Odira Celestine |
| 4 | Citizen account, complaints, permits, tracking and interface adaptation | John Ojuekaiye Mayowa |
| 5 | Permissions, back-office processing, automation, integrations and deployment | Aminu Idris |
| 6 | Test evidence, limitations and questions | All members |

Confirm the allocation together before rehearsal. A member should present only work they understand and can demonstrate. The source package and documentation do not establish that each planned team activity has already taken place.

## Prepare the demonstration environment

Use a separate demonstration installation with synthetic data. Enable demonstration mode during installation and choose the sample account password yourself. Sample citizen: `citizen@example.test`; departmental officers: `works.officer@example.test`, `trade.officer@example.test`, `env.officer@example.test`, `plan.officer@example.test`. The administrator is the account created during setup. No shared sample password is shipped.

Keep the ZIP, report, slides and harmless sample PDF offline as a contingency. Do not demonstrate real citizen data or display provider credentials, setup keys, MFA secrets or private tracking codes in public screenshots. Use an ordinary made-up request for the demo.

## Demonstration script

1. **Citizen journey:** Register, open the student-template-inspired dashboard, choose a complaint service and submit. Show the reference, target, private receipt and history.
2. **Authorised processing:** Sign in as the department officer, start review, request more information, then return to the citizen and reply. Show the return to Submitted and the next review/decision.
3. **Permit:** Submit a permit with a harmless PDF, approve it as the correct officer, print the approval record and open its public verification page.
4. **Access control:** Use a different citizen and an officer from another department to show that direct URLs and document downloads are denied. Show a custom review role without decision rights.
5. **Staff privacy:** Add an internal note with an attachment and confirm that neither appears for the citizen.
6. **Automation and communication:** Show the SLA target, a controlled overdue record, one-time escalation, in-app update and outbox. Explain the distinction between held, retry, failed and provider accepted.
7. **Assistance:** Ask a permit/tracking question using the local guide. If AI is configured and tested, demonstrate a chosen AI question and show that it does not decide applications or read private cases.
8. **Oversight:** Show scoped reports, CSV/print output, feedback and audit actions. End with the actual validation report and hosting acceptance status.

## Questions to prepare for

- How are department scope and action permission different?
- Why do hidden menus alone fail to secure an application?
- What prevents two officers from overwriting the same decision?
- Why are uploads private and MIME-checked?
- How does a citizen respond to missing information?
- What happens when SMTP, SMS or AI is unavailable?
- Does “sent” mean an SMS or email reached its destination?
- Why do live installations start without sample services activated?
- What does the application key protect, and why must it be backed up?
- What operational checks remain before admitting real citizen records?

Use the documented results. Do not claim independent certification, a guaranteed grade, statutory integration, verified government identity, online payments or successful live-provider delivery unless it has actually been established in the deployment.
