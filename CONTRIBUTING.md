# Contributing to CivicFlow

Group 1 uses issues, focused branches and reviewed pull requests to connect work to the SRS and the MIT 820 gates.

1. Create a task or defect issue with the affected FR or NFR and an acceptance condition.
2. Use a branch such as `feature/issue-number-short-description` or `docs/issue-number-short-description`.
3. Keep changes focused. Check the relevant successful, denied and failure paths.
4. Update the affected report, SRS, diagram, manual and test evidence.
5. Open a pull request and ask another member to review it. Link the issue and evidence.
6. Record accepted work in the sprint backlog and weekly report after review.

Use each contributor's own Git identity. Link actual commits and reviews in the contribution register. Preserve original dates and do not create a reconstructed history to imply earlier activity.

Run `python3 tools/validate_repo.py` before a documentation change. Application checks are described in [testing](docs/technical/testing.md). The supplied workflow definitions become execution evidence only after GitHub runs them successfully.

Bolaji coordinates gates and scope. Aminu and John lead Development. Mustapha and Odira lead Documentation. Every member should understand the complete citizen and staff journey.
