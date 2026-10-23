# Upload the repository to GitHub

Extract the repository ZIP first. Its `civicflow-mit820` folder contains `README.md`, `src`, `docs`, `tools` and the hidden repository configuration files. Upload those contents to the root of your GitHub repository. Uploading the ZIP itself does not turn its contents into a repository tree.

## Git command route

Create an empty repository in your GitHub account, then run these commands from the extracted `civicflow-mit820` folder. Replace `YOUR_REPOSITORY_URL` with the actual HTTPS or SSH URL shown by GitHub.

```bash
git init -b main
git add .
git status
git commit -m "docs: establish MIT 820 source and documentation baseline"
git remote add origin YOUR_REPOSITORY_URL
git push -u origin main
```

Use your own configured Git identity. For an existing repository, work in a branch and review the changes before merging. Preserve real history and do not force-push to replace other members' work.

## Browser upload route

Use **Add file** and **Upload files** in the empty repository. Upload the extracted files in batches as needed, including `.github`, `.gitignore`, `.gitattributes` and each `.htaccess`. Confirm the final tree matches [the repository map](../README.md#repository-map).

## After upload

1. Open `README.md` and the diagram gallery and confirm images render.
2. Open Actions and inspect the real CI run. Retain its URL in the weekly report after success.
3. Invite the actual members using their verified GitHub accounts and assign the backlog issues.
4. Agree branch protection and a reviewer policy appropriate to the account plan.
5. Build a private cPanel archive locally with `python3 tools/build_release.py`; keep generated keys and credentials outside Git.

Relative image and document links point to files inside the repository. They work across owner names and branches and remain usable after extraction. The offline `START_HERE.html` opens the documentation without an internet connection.
