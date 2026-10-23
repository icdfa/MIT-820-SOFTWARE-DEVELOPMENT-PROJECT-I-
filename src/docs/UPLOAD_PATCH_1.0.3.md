# Update the existing CivicFlow portal to 1.0.3

Target website: https://iconicproject.icdfafoundation.com.ng/

Use **CivicFlow_Landing_and_Demo_Update_v1.0.3.zip** for the website that is already working. The separate **CivicFlow_cPanel_Complete.zip** is for a fresh installation.

## 1. Upload the update

1. Keep a protected backup of the current website files and database.
2. In cPanel File Manager, open this domain's document root: the folder containing `index.php`, `app`, `assets`, `config` and `storage`.
3. Upload **CivicFlow_Landing_and_Demo_Update_v1.0.3.zip** into that folder. Enable Show Hidden Files.
4. Extract it in that same folder and allow matching application files to be overwritten. Do not put the files inside an additional nested folder.
5. Open https://iconicproject.icdfafoundation.com.ng/ in a signed-out or private browser window. Refresh with Ctrl+F5. The existing signed-in home route opens the dashboard by design.
6. Confirm that the three images, slider controls, service search, tracking form and help sections load. Existing published services and notices appear immediately.
7. Remove the uploaded ZIP from the public hosting folder after extraction.

No SQL import, database credential entry or installer rerun is required. Keep the existing `config/local.php`, `config/setup.key`, `config/installed.lock`, database and private uploads.

The update archive contains application code, assets, access-denial files and release instructions. It excludes operator configuration, setup keys, installation locks, database files, stored sessions, logs and uploaded evidence. Applying the ZIP does not itself create demonstration accounts or change service records.

## 2. Add the realistic presentation records

1. Sign in using your existing administrator account.
2. Open **Administration → Demonstration data**, or visit https://iconicproject.icdfafoundation.com.ng/admin/demo-data.
3. Choose and confirm a demonstration account password containing 12–72 bytes, including letters and numbers. Save it securely.
4. Read the page and select the checkbox to mark the installation as a demonstration portal and publish its nine starter services.
5. Select **Load demonstration records**. Wait for the completion page; do not close the tab while it is loading.
6. Download the account directory if needed. All 26 new demonstration accounts use the password you chose. Your existing administrator account keeps its previous password.

The loader adds records once. Repeat submissions do not duplicate data or reset passwords. Existing accounts, requests, documents, service fees and integration settings are retained. Missing starter services and departments are added; the nine starter services are made visible with your explicit confirmation. A catalogue/email conflict stops loading rather than overwriting the conflicting record.

The entire installation is visibly marked as a demonstration portal after this action, including approval and verification pages. Use this option on the presentation installation. It is not an option for making fictional approvals appear genuine.

## 3. Demonstrate all six roles

Sign out before switching roles, or use separate private browser windows. Use the password you chose for the new demonstration accounts.

| Role | Example login | Demonstration |
|---|---|---|
| Administrator | Your existing administrator email | All submitted cases, people, catalogue, permissions, reports and audit |
| Citizen | amina.bello@civicflow.example.test | Own requests, supporting evidence, private receipt and feedback |
| Service officer | abdullahi.musa@civicflow.example.test | Public Works queue, review, internal notes and decisions |
| Department head | usman.shuaibu@civicflow.example.test | Department workload, assignments, reporting and exports |
| Oversight auditor | joseph.nwosu@civicflow.example.test | Read-only oversight, reports and audit records |
| Communications officer | hauwa.saleh@civicflow.example.test | Cancelled notification examples, message templates, notices and FAQs |

The account directory lists 12 citizens, 8 service officers, 4 department heads, an auditor and a communications officer. The existing administrator provides the sixth role. Existing MFA policy and custom role permissions still apply.

## 4. Records included

- 36 detailed complaints and permit applications across 9 services and 4 departments.
- All 8 stages: 2 drafts, 9 submitted, 7 in review, 5 needing information, 5 resolved, 4 approved, 2 rejected and 2 withdrawn.
- Dated public history and staff-only notes, appropriate officer assignments and service targets.
- 27 private illustrative PDF documents, including staff-only review evidence.
- 9 completed-service feedback entries, in-app notifications and auditable actions.
- 34 email outbox examples with state **Cancelled**, zero provider attempts and no fabricated delivery receipts.
- 6 practical service notices in addition to existing content.

People, businesses and case activity are fictional. Local place context is used to make the walkthrough understandable. Emails use the reserved `example.test` domain; no random real phone numbers or identity documents are created. Routine email/SMS preferences start off for these accounts. The loader sends no email or SMS. Existing provider configuration is retained.

## If something does not appear

- Landing page: open the site while signed out, then refresh. Signed-in users go to their dashboard.
- Slider images missing: confirm that `assets/img/landing` contains all three `.webp` files and that the ZIP was extracted into the correct document root.
- No service cards: publish your services, or load the demonstration dataset from the administrator page.
- No Demonstration data menu: use the primary administrator account. The feature is intentionally unavailable to the other roles.
- Loader reports an existing email/catalogue conflict: review that conflicting record in People and roles or Manage services. Do not delete operator records or remove the installation lock to force loading.
- Host error: use `storage/logs/application.log` through cPanel, keeping it private. Do not publish passwords or database credentials.

There is no automatic deletion/reset button. Retain your pre-load backup if you intend to return this presentation installation to its exact previous state.
