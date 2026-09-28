# Releasing this plugin

There are **two separate, unrelated pipelines**. Merging a PR into `master`
only ever triggers the first one — it does not ship a release.

## 1. `assets.yml` — "Sync readme & assets"

**Triggers on:** any push to `master` that touches `readme.txt` or
`.wordpress-org/**`.

**Does:** copies `readme.txt` and the `.wordpress-org/` assets (icon,
screenshots, `blueprints/`) to the SVN `trunk` and `assets` folders.

**Does NOT:** touch the plugin's PHP/CSS/JS, or create a version tag.

This exists so a "Tested up to" bump or a new screenshot can ship without
cutting a full release.

Uses `10up/action-wordpress-plugin-asset-update` with `IGNORE_OTHER_FILES: true`.
Without that flag, the action rsyncs the *entire* repo into SVN trunk (not
just readme.txt), so any unreleased code change trips its own safety check
and the workflow fails with `Other files have been modified; changes not
deployed`. Keep this flag set.

## 2. `deploy.yml` — "Deploy to WordPress.org"

**Triggers on:** pushing a git tag matching `*.*.*` (e.g. `1.6.0`).

**Does:**
1. `version-guard` job — fails the run if the tag, the `Version:` header in
   `customize-private-protected.php`, and `Stable tag:` in `readme.txt`
   don't all match exactly.
2. `deploy` job — pushes the plugin code to SVN `trunk` and cuts a new
   `tags/{version}` folder. **This is the actual release.**

## How to cut a release

1. Bump `Version:` in `customize-private-protected.php` and `Stable tag:` in
   `readme.txt` to the same number. Merge that into `master`.
2. Tag and push:
   ```bash
   git checkout master && git pull
   git tag 1.6.1
   git push origin 1.6.1
   ```
3. Watch `deploy.yml`: `gh run list --workflow=deploy.yml --limit 3`

Merging a PR to `master` never does step 2 on its own — a release only
happens when a tag is pushed.

## Known gotcha: readme says a version with no matching SVN tag

If `assets.yml` runs (e.g. bumping `Stable tag` in a PR) before the
corresponding `deploy.yml` tag push happens, SVN trunk's `readme.txt` will
claim a `Stable tag` that has no `tags/{version}` folder yet. WordPress.org
resolves `Stable tag` to that tag folder to decide what to serve — if it's
missing, the plugin page can end up advertising a version it isn't actually
serving. Check for this drift:

```bash
# What version does trunk's readme claim?
svn cat https://plugins.svn.wordpress.org/customize-private-protected/trunk/readme.txt | grep -i "stable tag"

# What version is the actual trunk code?
svn cat https://plugins.svn.wordpress.org/customize-private-protected/trunk/customize-private-protected.php | grep -oP '^Version:\s*\K.+'

# Does that tag actually exist?
svn list https://plugins.svn.wordpress.org/customize-private-protected/tags/
```

If the stable tag and trunk version don't match, or the tag folder doesn't
exist, cut the release (step above) to resolve it.

## Known gotcha: leaked `.git` in SVN trunk

If a `.git` directory ever gets committed into the SVN trunk (it happened
once, from an old deploy before `.distignore` excluded it), every future
`assets.yml`/`deploy.yml` run will fail with
`Other files have been modified; changes not deployed`, because SVN's
working copy still tracks those `.git` files but a fresh checkout won't
recreate them. `.distignore` only stops it from being *re-added* — it
doesn't remove what's already committed. Fix once, directly on SVN:

```bash
svn checkout https://plugins.svn.wordpress.org/customize-private-protected/trunk trunk-cleanup
cd trunk-cleanup
svn rm --force .git
svn commit -m "Remove accidental .git folder from trunk" --username YOUR_WP_ORG_USERNAME
rm -rf ../trunk-cleanup  # scratch checkout, safe to delete after committing
```
