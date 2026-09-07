// Meta Data admin screen — pages the Features/Journal/Poetry content lists
// (each has its own `[data-meta-list]` container) via fetch, so browsing
// past the first 10 items never reloads the page or loses the active tab.

/**
 * Swaps one content list's HTML for the requested page, fetched from the
 * server-rendered partial (see MetaController::contentList).
 */
async function loadMetaPage(container, type, page) {
    container.setAttribute('aria-busy', 'true');

    const url = new URL(container.dataset.listUrl, window.location.origin);
    url.searchParams.set('page', page);

    let response;

    try {
        response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
    } catch {
        container.removeAttribute('aria-busy');
        return;
    }

    if (!response.ok) {
        container.removeAttribute('aria-busy');
        return;
    }

    const data = await response.json();
    container.innerHTML = data.html;
    container.removeAttribute('aria-busy');
}

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-meta-page]');
        if (!button || button.disabled) {
            return;
        }

        const type = button.dataset.metaType;
        const page = button.dataset.metaPage;
        const container = document.querySelector(`[data-meta-list="${type}"]`);

        if (container) {
            loadMetaPage(container, type, page);
        }
    });
});
