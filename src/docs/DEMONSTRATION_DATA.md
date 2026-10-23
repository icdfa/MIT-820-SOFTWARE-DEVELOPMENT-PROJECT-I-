# Presentation dataset and landing page

CivicFlow 1.0.4 includes a photo-led public landing page and an optional complete role walkthrough. Existing installations do not need a schema migration. The administrator explicitly loads the dataset from `/admin/demo-data` after uploading the update. See `UPLOAD_PATCH_1.0.4.md` for the full deployment and sign-in instructions.

## Landing page

The public page includes three original locally hosted WebP illustrations; previous/next and slide-selection controls; an automatic slideshow with pause, focus, hover, visibility and reduced-motion handling; service search and published service cards; private-code request tracking; department links; the request process; published notices; FAQs; registration and support links. It works with the existing clean URL routing and local-only content security policy. With JavaScript disabled the first slide, navigation, service search and tracking form remain usable.

The homepage reads published content from the database. It does not display fabricated adoption counts, response statistics, customer endorsements or government affiliations. Image provenance and generation prompts are in `ASSET_CREDITS.md`.

## Data ownership and integrity

The loader requires a primary administrator, `settings.manage` permission, a valid CSRF token, a strong chosen demonstration password and explicit confirmation. A private file mutex prevents overlapping loads. Records are added in a database transaction, and newly written private files are removed if that transaction fails. The `demo_dataset_v103` setting records inserted IDs and completion metadata. A repeated completed load is a no-op.

It neither resets the administrator password nor overwrites existing citizens, cases or uploads. Existing service requirements, targets, fees, role permissions and delivery configuration are retained. Only the confirmed starter services are activated; missing starter departments and services are added. A conflicting email, service type or department stops the transaction.

The `demo_dataset_mode` setting retains the prepared data origin even when the original configuration started in live mode. As of 1.0.4, the public landing banner and repeated footer label are removed. Data-origin information remains in About, sample approvals and public verification. Existing configuration files and populated records are retained.

## Representation of external communication

The dataset inserts real in-app records and illustrative email outbox entries. Email examples are **cancelled**, not sent or provider-accepted. Attempts remain zero; there are no invented provider receipts. Demonstration users have blank phone numbers, reserved test email addresses and routine delivery preferences disabled. The loader invokes no SMTP, SMS or AI delivery API. Existing live provider settings are not changed.

MFA policies and account/role checks continue to apply to demonstration accounts. To use these accounts for external delivery after a presentation, an administrator would need to review their contact data and delivery preferences; reserved mailboxes themselves cannot receive real mail.

## Coverage

The dataset provides all six existing roles with normal permission enforcement: the operator's administrator plus 26 new accounts. The 36 new requests cover every lifecycle stage, all nine starter services and all four departments. Nine completed requests contain feedback, and 27 private PDFs support document and internal-note demonstrations. These documents explicitly identify themselves as illustrative records with no legal or identity value.

All people and businesses are fictional. Do not present this activity as actual service delivery, field work or government approval.
