# Architecture and workflow

## Application boundary

The browser uses clean routes served through Apache's front controller. PHP controls authentication, permissions and workflow decisions; PDO persists records. Private storage holds uploads, session files and operational state. Scheduled CLI jobs perform escalation and external delivery.

```mermaid
flowchart TD
  B["Citizen / staff browser"] --> A["Apache clean routes"]
  A --> P["PHP front controller"]
  P --> G["Session, CSRF and permission checks"]
  G --> W["Request and administration workflows"]
  W --> D["PDO: MySQL / MariaDB"]
  W --> F["Private file storage"]
  C["Scheduled CLI worker"] --> D
  C --> N["SMTP / Termii delivery"]
  W --> Q["Public service guide / optional AI"]
```

`router.php` is a local development equivalent. It is not the production web server. All external calls are server-side, with TLS verification. API secrets do not enter page markup or client JavaScript.

## Request lifecycle

```mermaid
stateDiagram-v2
  [*] --> Draft: Save privately
  [*] --> Submitted: Submit online
  Draft --> Submitted: Complete and submit
  Draft --> Withdrawn: Owner withdraws
  Submitted --> InReview: Officer starts review
  Submitted --> Withdrawn: Owner withdraws
  InReview --> NeedsInformation: Officer requests details
  NeedsInformation --> Submitted: Citizen replies
  NeedsInformation --> InReview: Officer resumes review
  NeedsInformation --> Withdrawn: Owner withdraws
  InReview --> Approved: Permit approved
  InReview --> Resolved: Complaint resolved
  InReview --> Rejected: Decision recorded
  Resolved --> Submitted: Complaint reopened
  Approved --> [*]
  Rejected --> [*]
  Withdrawn --> [*]
```

A version number guards decisions, assignment and draft updates against stale writes. Workflow changes, history and notifications are committed together. Files moved during a failed transaction are removed. Reopening sets a fresh target and clears prior feedback.

## Core data relationships

```mermaid
erDiagram
  DEPARTMENT ||--o{ SERVICE : publishes
  DEPARTMENT ||--o{ USER : contains_staff
  USER ||--o{ REQUEST : submits
  SERVICE ||--o{ REQUEST : categorises
  DEPARTMENT ||--o{ REQUEST : processes
  REQUEST ||--o{ EVENT : records
  REQUEST ||--o{ ATTACHMENT : supports
  REQUEST ||--o| FEEDBACK : receives
  USER ||--o{ NOTIFICATION : receives
  USER ||--o{ OUTBOX : addressed_to
```

`requests.assigned_to` also references a staff user. Request ownership, selected service department and current routing department are separate fields so routing can change without replacing the request record.

```mermaid
erDiagram
  ROLE ||--o{ ROLE_PERMISSION : grants
  ROLE ||--o{ USER_ROLE : assigned_as_extra
  USER ||--o{ USER_ROLE : holds
  USER ||--o{ DEVICE_SESSION : authenticates
  USER ||--o{ RECOVERY_CODE : recovers
  USER ||--o{ PASSWORD_RESET : requests
  USER ||--o{ EMAIL_VERIFICATION : confirms
  EMAIL_TEMPLATE ||--o{ OUTBOX : renders_for
```

The primary role is stored on `users.role`; additional roles use `user_roles`. Template keys on outbox records are references for traceability, while each message stores its rendered body so later template edits do not alter queued text. Reset/verification tokens and recovery codes are stored as hashes. MFA and provider secrets are encrypted under the installation key.

## Permission evaluation

A request begins with an active session and account version. The application loads permissions from active primary/additional roles, except the protected administrator role which has the full catalogue. SQL lists use the user's scope and individual record reads enforce it again. Mutations require action permission independently of whether a page or menu item is visible. Downloads also require document permission, request scope and internal-document permission where applicable.

Draft privacy is enforced on individual reads as well as staff lists. Scope precedence is organisation, department, then own. Default citizen permissions can only remain within the allowed public capability set.

## Notification processing

```mermaid
flowchart TD
  E["Workflow / account event"] --> I["In-app notice"]
  E --> V["Preference and channel checks"]
  V --> H["Held: configuration required"]
  V --> O["Outbox: pending"]
  O --> C["Worker claim under lock"]
  C --> P["Provider request"]
  P --> S["Accepted: sent"]
  P --> R["Failure: retry with backoff"]
  R --> O
  R --> F["Five failures: stop"]
```

The job checks disabled channels before delivery and conditionally claims pending/retry rows. Cancellation cannot claim a processing record. Provider acceptance is recorded, but a crash around a provider acknowledgement can still cause a repeated delivery attempt. The system does not claim exactly-once transport.

## File and configuration boundary

- Root `.htaccess` denies private directories, dot paths, SQL/log/archive artifacts and setup instructions.
- Each private directory also carries a denial file. Uploaded files have random names, validated MIME types and scoped download routes.
- Application configuration and install lock are generated locally after setup. There is no bundled live database or account password.
- SQL reference files are generated from the same schema definitions used by the installer.
- The application uses a single-instance cPanel topology. Multi-node sessions, shared files, distributed rate limiting and operational monitoring are deployment extensions.
