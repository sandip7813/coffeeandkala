// Fancybox lightbox for frontend images (e.g. the About page story photos).
//
// Loaded on demand via *dynamic* imports so jQuery + Fancybox are only pulled
// in on pages that actually have a `[data-fancybox]` element. Fancybox 3
// calls several $.xxx helpers (isArray, isFunction, isNumeric, isPlainObject,
// trim, type) that jQuery removed in v4 — this project is on jQuery ^4, so
// without these shims opening the lightbox throws "n.isArray is not a function".
function shimJqueryForFancybox(jQuery) {
    jQuery.isArray ??= Array.isArray;
    jQuery.isFunction ??= (value) => typeof value === 'function';
    jQuery.isNumeric ??= (value) => !isNaN(parseFloat(value)) && isFinite(value);
    jQuery.isPlainObject ??= (value) =>
        typeof value === 'object' && value !== null && value.constructor === Object;
    jQuery.trim ??= (value) => (value == null ? '' : String(value).trim());
    jQuery.type ??= (value) => (value === null ? 'null' : typeof value);
}

export function initFrontendFancybox() {
    const elements = document.querySelectorAll('[data-fancybox]');

    if (elements.length === 0) {
        return;
    }

    import('jquery').then(({ default: jQuery }) => {
        window.$ ??= jQuery;
        window.jQuery ??= jQuery;

        shimJqueryForFancybox(jQuery);

        import('@fancyapps/fancybox/dist/jquery.fancybox.js').then(() => {
            jQuery('[data-fancybox]').fancybox();
        });
    });
}
