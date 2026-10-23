# Installation and deployment

CivicFlow needs PHP 8.2 or newer with PDO, Fileinfo, Mbstring, OpenSSL, cURL and DOM/XML. Enable PDO SQLite for local evaluation and PDO MySQL for MySQL or MariaDB hosting.

## Local evaluation

From the extracted repository root:

```bash
python3 tools/prepare_local.py
cd src
php -S 127.0.0.1:8080 router.php
```

Read the generated `src/config/setup.key` locally. Open `http://127.0.0.1:8080/install`, enter that key, choose SQLite and create your administrator. Keep HTTPS enforcement off only for this loopback evaluation. A prepared record set is optional and uses a chosen password.

## New cPanel installation

```bash
python3 tools/build_release.py
```

Upload the generated `dist/CivicFlow_cPanel_Complete.zip` to a new document root and extract it. Retain every `.htaccess`. Read its local `START_HERE.html`, open `/install` and supply a fresh empty database, valid HTTPS base URL and your administrator details. The archive contains a unique setup key and excludes prior runtime data.

GitHub Pages displays static documentation and cannot run this PHP application. Use a PHP and database host for the live system.

## Existing installation

Preserve the database, private uploads, `config/local.php`, `config/installed.lock` and application key. This documentation revision requires no schema migration or reinstall. Back up and test changes on staging before live promotion.

Read the [full setup guide](../academic/Setup_and_Deployment_Guide.md) and [source deployment guide](../../src/docs/DEPLOYMENT.md). Record independent live access and the deployed build before defence.
