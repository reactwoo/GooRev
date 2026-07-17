# Install folder note

The WordPress install folder **must** be `reactwoo-reviews/`.

This workspace may still appear as `GooRev` while the IDE has the path locked. A directory junction `plugins/reactwoo-reviews` → `plugins/GooRev` is used so Local Sites can activate the plugin under the correct basename.

When the folder is no longer locked, rename permanently:

```powershell
# Remove junction first, then rename:
cmd /c rmdir "c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\reactwoo-reviews"
Rename-Item "c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev" "reactwoo-reviews"
```

GitHub remote remains `reactwoo/GooRev` until you rename the GitHub repository (optional). Zip packaging always uses prefix `reactwoo-reviews/`.
