function initSearchResultTabs() {
    const tabs = document.querySelectorAll('.search-tab');
    const panels = document.querySelectorAll('.search-tab-panel');

    if (tabs.length === 0 || panels.length === 0) {
        return;
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tabTarget;

            tabs.forEach((t) => t.classList.toggle('is-active', t === tab));

            panels.forEach((panel) => {
                const matches = target === 'all' || panel.dataset.tabPanel === target;
                panel.classList.toggle('is-hidden', !matches);
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', initSearchResultTabs);
