# ReactWoo Reviews — Developer Notes

## Identity

| Surface | Value |
|---------|--------|
| Catalog / update slug | `reactwoo-reviews` |
| Install folder | `reactwoo-reviews` |
| Main PHP | `reactwoo-reviews.php` |
| Version | `GRP_PLUGIN_VERSION` |
| Text domain | `google-reviews-plugin` |
| Classes / options | `GRP_*` / `grp_*` |
| Blocks | `google-reviews/reviews`, `google-reviews/review-button` |
| Elementor widgets | `grp-reviews`, `grp-review-button` |
| Legacy license plugin id | `goorev` (aliased) |

## Packaging

```bash
npm run package:zip
```

CI uses the same script with `CI=true` for an unversioned `reactwoo-reviews.zip`.

## Release

1. Bump version in `reactwoo-reviews.php`, `readme.txt`, `package.json`.
2. Commit, annotated tag `vX.Y.Z`, `git push origin main "vX.Y.Z"`.
3. Verify tag on origin; `publish-update.yml` uploads to R2.

## License server

Run `migrations/add_reactwoo_reviews_alias.sql` on the license DB. Deploy `react-license` alias + free-activation allow-list changes. Add `reactwoo-reviews` to production `UPDATES_FREE_SLUGS` and reload PM2 on `api.reactwoo.com`.
