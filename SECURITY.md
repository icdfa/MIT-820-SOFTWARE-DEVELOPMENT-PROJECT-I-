# Security reporting

Report a suspected exposure privately to the project manager at watsonbolaji@gmail.com. Include the affected release and a minimal reproduction using test records. Do not post passwords, setup keys, private uploads or citizen information in a public issue.

The team should acknowledge and triage the report, preserve evidence and coordinate a reviewed fix and regression check. This repository does not establish a guaranteed response SLA or an independent security certification.

Keep `src/config/local.php`, installation locks, setup keys, database files, private uploads and logs outside Git. The build tools create a fresh private setup key for a local installation or a privately distributed cPanel archive.

See the [Architecture Design Document](docs/academic/Architecture_Design_Document.md) and [operating guide](src/docs/OPERATIONS.md) for access, storage, backup and provider boundaries.
