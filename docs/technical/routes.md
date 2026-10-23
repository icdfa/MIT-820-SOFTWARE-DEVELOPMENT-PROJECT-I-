# Route and interface reference

`src/index.php` dispatches the clean public routes. Mutations run through `app/actions.php` and the corresponding domain or administration functions. Form mutations require a valid CSRF token and the relevant server-side access check. Browser forms normally redirect after success. The assistant interface uses JSON.

| Route or family | Method | Access and purpose |
| --- | --- | --- |
| `/`, `/services`, `/services/{id}` | GET | Public service discovery |
| `/register`, `/login` | GET and POST | Citizen registration and normal account sign-in |
| `/forgot-password`, `/reset-password`, `/verify-email` | GET and POST | Time-limited account flows |
| `/two-factor` | GET and POST | MFA sign-in completion |
| `/track` | GET and POST | Limited progress with reference and private code |
| `/verify/{reference}` | GET | Limited approved-permit record verification |
| `/assistant`, `/help`, `/privacy`, `/about` | GET | Public guidance and service information |
| `/api/assistant` | POST | CSRF-protected guidance request with public context |
| `/dashboard`, `/requests` | GET | Authenticated and scoped workspace |
| `/requests/new`, `/requests/{id}/edit` | GET and POST | Permitted citizen submission and owner draft editing |
| `/requests/{id}` | GET | Authorised record detail |
| `/requests/{id}/status`, `/reply`, `/assign` | POST | The action suffix applies beneath the request path and checks its own permission and scope |
| `/requests/{id}/withdraw`, `/reopen`, `/feedback` | POST | Owner and state-controlled follow-up |
| `/requests/{id}/receipt`, `/permit` | GET | Authorised printable records |
| `/documents/{id}` | GET | Authenticated parent-record, download and visibility checks |
| `/profile`, `/profile/security`, `/notifications` | GET and applicable POST | Account preferences and security |
| `/reports`, `/reports/export` | GET | Report and export permissions with record scope |
| `/admin/*` | GET and applicable POST | Separate management permissions |
| `/admin/demo-data` | GET and POST | Primary-administrator-only additive record preparation |
| `/install` | GET and POST | Setup-key-controlled new installation, locked after completion |

This is an internal application route reference, not a claim of a separate public REST API. Check the source dispatcher and action handlers when changing an interface. Provider contracts are described in [the source deployment guide](../../src/docs/DEPLOYMENT.md).
