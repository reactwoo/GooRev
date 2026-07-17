# ReactWoo Reviews

WordPress plugin for displaying Google Business Profile reviews (shortcodes, widgets, Elementor, Gutenberg).

**Catalog slug:** `reactwoo-reviews`  
**Install folder:** `reactwoo-reviews`  
**Main file:** `reactwoo-reviews.php`  
**Version constant:** `GRP_PLUGIN_VERSION`  
**Text domain:** `google-reviews-plugin` (kept for translation BC)

## Productization

- Updates: free catalog slug on `api.reactwoo.com` (`UPDATES_FREE_SLUGS`).
- Licensing: Pro/Enterprise via `license.reactwoo.com`; packages remain `goorev-free|pro|enterprise` with alias `goorev` → `reactwoo-reviews`.
- Cloud API: `cloud.reactwoo.com/grp-api/v1/` (unchanged path).

## Build / release

```bash
npm run package:zip
# commit → git tag -a vVERSION → git push origin main "vVERSION"
```

See `.cursor/rules/release.mdc`.

## Agent notes

Prefer one coherent implementation thread. Do not rename `GRP_*` or block/widget IDs without a migration plan.
