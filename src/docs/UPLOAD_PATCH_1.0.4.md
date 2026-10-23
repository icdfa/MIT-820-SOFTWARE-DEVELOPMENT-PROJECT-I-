# CivicFlow 1.0.4 — portal display update

For the existing website: https://iconicproject.icdfafoundation.com.ng/

1. Keep a protected backup of the current files and database.
2. Upload **CivicFlow_Portal_Display_Update_v1.0.4.zip** to the existing document root, beside `index.php`, `app`, `assets`, `config` and `storage`.
3. Extract it there and allow matching application files to be overwritten.
4. Open the homepage while signed out or in a private browser window, then refresh with Ctrl+F5.
5. Remove the uploaded ZIP from the public hosting folder after extraction.

The large homepage demonstration banner and the repeated demonstration-mode footer label are removed. The image slider, services, citizen accounts, staff roles, requests, documents, messages, tracking and reporting continue through the normal working portal.

Existing populated records are retained. No SQL import, reinstall, data reset or second data load is required. Keep `config/local.php`, `config/setup.key`, `config/installed.lock`, the database and private uploads.

The archive excludes configuration credentials, setup keys, installed locks, databases, logs, sessions and existing uploaded evidence. It includes the current application and local image assets, so it can also be applied after the earlier 1.0.2 update. If the presentation dataset has not yet been loaded, the administrator-only Demonstration data page remains available; repeat completed loads are a no-op.

Prepared project records remain fictional. That information is retained in About and relevant approval documents. This display update does not turn illustrative cases into genuine government activity or confer legal validity on project approvals.

The separate **CivicFlow_cPanel_Complete.zip** is the full source package for a fresh installation. Use this update ZIP for the installed website.
