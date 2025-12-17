---
description: Save current progress to the git repository
---

This workflow saves the current code changes to the git repository. It only adds specific source directories to avoid accidentally committing large binary assets or backup folders.

1. Add core source files
// turbo
```bash
git add models/ views/ controllers/ create_tables.sql commands/ config/ mail/ tests/
```

### 2. Commit changes
```bash
git config user.email "antigravity@bapa.rocks"
git config user.name "Antigravity AI"
git commit -m "Update from Antigravity session"
```
3. (Optional) Push to remote
> Note: Pushing usually requires SSH keys configured for the remote. Run this manually if configured.
```bash
# git push origin master
```
