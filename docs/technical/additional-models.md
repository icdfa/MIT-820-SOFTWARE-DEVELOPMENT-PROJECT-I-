# Additional formal model sources

These editable Mermaid sources complement the rendered SVG and PNG views in the diagram gallery.

## Use case associations

```mermaid
flowchart TD
    Citizen[Citizen] --> Access[Account access]
    Citizen --> Requests[Submit and track own requests]
    Citizen --> Replies[Reply and give feedback]
    Officer[Officer or department head] --> Review[Review and record decisions]
    Auditor[Auditor] --> Oversight[Reports and audit]
    Comms[Communications officer] --> Delivery[Outbox and public notices]
    Admin[Administrator] --> Settings[People permissions and services]
```

## Submission and information reply sequence

```mermaid
sequenceDiagram
    participant C as Citizen
    participant P as Portal
    participant D as Database
    participant O as Department officer
    C->>P: Submit completed complaint
    P->>D: Persist request target and event
    D-->>P: Return stable reference
    P-->>C: Show private receipt and code
    O->>P: Start review and ask for information
    P->>D: Check scope and version then save reason
    C->>P: Reply within owned request
    P->>D: Record reply and return to Submitted
    O->>P: Review and record reasoned outcome
```

## Deployment topology

```mermaid
flowchart TD
    Browser[Citizen and staff browsers] --> Web[HTTPS Apache and PHP]
    Web --> DB[MySQL or MariaDB]
    Web --> Files[Private storage]
    Web --> AI[Optional AI with public context]
    Cron[Scheduled CLI worker] --> DB
    Cron --> Providers[SMTP and SMS providers]
```
