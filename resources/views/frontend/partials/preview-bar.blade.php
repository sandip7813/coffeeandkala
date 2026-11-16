{{--
    Shown on admin previews only (see Admin\PreviewController, which sets
    $previewBar to the label) — floats at the bottom so it never covers the
    site header. Self-contained inline styles: it must look the same on
    every frontend layout without touching their stylesheets.
--}}
<div role="status" aria-live="polite"
     style="position:fixed; left:50%; bottom:16px; transform:translateX(-50%); z-index:2147483000; display:flex; align-items:center; gap:12px; width:max-content; max-width:calc(100% - 32px); padding:10px 12px 10px 16px; border-radius:999px; background:#1f1a17; color:#fff; font:500 14px/1.35 system-ui, -apple-system, 'Segoe UI', sans-serif; box-shadow:0 8px 28px rgba(0,0,0,.28);">
    <span style="flex-shrink:0; padding:3px 10px; border-radius:999px; background:#c8102e; font-weight:700; font-size:12px; letter-spacing:.06em; text-transform:uppercase;">{{ __('Preview') }}</span>
    <span style="min-width:0;">{{ __('Not published') }} · {{ $previewBar }}</span>
    <button type="button" onclick="window.close()"
            style="flex-shrink:0; border:1px solid rgba(255,255,255,.35); border-radius:999px; background:transparent; color:#fff; padding:4px 12px; font:inherit; font-size:13px; cursor:pointer;">{{ __('Close') }}</button>
</div>
