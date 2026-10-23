# Upload patch 1.0.2

For https://iconicproject.icdfafoundation.com.ng/

1. Download a backup of the existing website files and database in cPanel.
2. Upload CivicFlow_Installer_UI_Fix_v1.0.2.zip into this domain's document root, where index.php, app, assets, config and storage are located.
3. Extract it there and allow matching application files to be overwritten. Do not extract it into an extra nested directory.
4. Open https://iconicproject.icdfafoundation.com.ng/install and refresh with Ctrl+F5. Enter your existing private setup key, database details and administrator details.
5. Missing storage folders are recreated automatically. If a readiness check remains red, correct the named folder's ownership and write permission in File Manager. Account-owned directories normally use 0755. Ask the host to correct ownership if PHP still cannot write. Do not use 0777.
6. Finish installation. If it is already installed, /install redirects to sign-in. Confirm the services, citizen registration, request submission and staff review on your host.
7. Delete the uploaded patch ZIP from the public directory after extraction.

This patch contains application code, assets and private-folder denial files. It does not contain config/local.php, config/setup.key, config/installed.lock, uploaded documents, stored sessions, database dumps or logs. It does not reset or drop the database. Existing setup keys and credentials remain in place.

The separate CivicFlow_cPanel_Complete.zip is for a fresh installation. Use the patch above for the existing portal so that local configuration and stored data are preserved.

The original problem was a failed private-storage readiness check, with writes occurring before the installer handled that check. If the hosting account itself prevents PHP from creating or writing to a folder, the patch reports the path and required action; it cannot change hosting-account ownership.

The interface now uses a standard public menu and a separate signed-in portal layout, with larger text, flat panels and conventional tables/forms. Cache-busting URLs request the new CSS after upload.

Validation: 142 local HTTP assertions/scenarios passed. Complete the hosted database and browser acceptance after upload.
