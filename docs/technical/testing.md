# Testing and evidence

The retained baseline records 267 passed assertions across five suites: 124 workflow and security, 13 fresh-installation, 5 installer, 113 landing and dataset, and 12 carousel interaction-fixture assertions. The fixture report does not claim a rendered browser evaluation. Most timestamped results are from 19 October 2026.

## Reproduce locally

Use a disposable source copy with the PHP extensions listed in [setup](setup.md). Python PDF-fixture inspection also needs the pinned `pypdf` dependency.

```bash
python3 -m venv .venv
.venv/bin/pip install -r requirements-dev.txt
.venv/bin/python src/tests/http_acceptance.py --php /path/to/php
.venv/bin/python src/tests/http_acceptance.py --php /path/to/php --live
.venv/bin/python src/tests/installer_regression.py --php /path/to/php
.venv/bin/python src/tests/landing_demo_acceptance.py --php /path/to/php
node src/tests/carousel_interactions.cjs
python3 tools/validate_repo.py
```

On Windows, use the corresponding virtual-environment Python path. The harnesses create temporary applications and their own test records. Do not use an active service database.

The [test summary](../academic/Test_Summary_Report.md) separates executed assertions from pending hosted MySQL, browser, performance, recovery and real provider checks. Store actual new run links and results in the project registers. The CI workflow is supplied configuration until an actual run succeeds on GitHub.
