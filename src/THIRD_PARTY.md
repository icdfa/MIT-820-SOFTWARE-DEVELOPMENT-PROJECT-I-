# Third-party materials and provenance

## User-provided campus portal template

The user supplied `backup-stable.zip` as the template reference and authorised reuse/adaptation. The citizen shell reuses the campus student portal's sidebar/main layout, header, panel, form, table and responsive patterns. `assets/css/portal-template.css` is the adapted base style block from the supplied campus CSS, with CivicFlow colours and a separate `app.css` layer.

No campus database, credentials, account records, student results, private documents or institutional logo are distributed in this release. The original large backup is not included. Reuse permission for the supplied template remains the responsibility of its owner; this package does not grant a new licence over third-party template material.

## PHPMailer

- Project: PHPMailer, https://github.com/PHPMailer/PHPMailer
- Pinned release: **7.1.1**
- Source: https://github.com/PHPMailer/PHPMailer/tree/v7.1.1
- Included files: `PHPMailer.php`, `SMTP.php`, `Exception.php`, `LICENSE`
- Licence: GNU Lesser General Public License 2.1, retained in `vendor/phpmailer/LICENSE`
- Files are unmodified upstream source. Only the essential SMTP classes are bundled; no Composer install is required for this usage. OAuth/POP-before-SMTP transports are not used.

The release was checked against the upstream latest-release page during preparation. Maintain and review dependencies over time; a pinned release is not a guarantee against future vulnerabilities.

## CivicFlow identity and other assets

`assets/img/logo.svg` is an original code-native civic-building/check emblem created for this platform. It does not reproduce the campus logo or the Nigerian coat of arms. Interface icons are simple inline SVG geometry. Fonts use the system font stack; there are no remote font, JavaScript or CSS CDN requirements.

## Optional external services

OpenAI, Termii and an SMTP provider are optional operator-configured connections. They are not bundled accounts. Their credentials, charges, sender approvals and terms remain with the operator's chosen accounts. No live provider key is included.
