const MIN_QUERY_LENGTH = 3;
const DEBOUNCE_MS = 250;
const CLOSE_TRANSITION_MS = 350;

function buildResultItem(item, sectionKey) {
    const li = document.createElement('li');
    li.className = 'search-result-item';
    li.setAttribute('role', 'option');
    li.dataset.url = item.url;
    li.dataset.section = sectionKey;

    const thumb = document.createElement('span');
    thumb.className = 'search-result-thumb';

    if (item.thumbnail) {
        const img = document.createElement('img');
        img.src = item.thumbnail;
        img.alt = '';
        img.loading = 'lazy';
        thumb.appendChild(img);
    } else {
        thumb.classList.add('search-result-thumb--empty');
    }

    const body = document.createElement('span');
    body.className = 'search-result-body';

    const title = document.createElement('span');
    title.className = 'search-result-title';
    title.textContent = item.title;

    const description = document.createElement('span');
    description.className = 'search-result-description';
    description.textContent = item.description || '';

    body.appendChild(title);
    body.appendChild(description);

    li.appendChild(thumb);
    li.appendChild(body);

    return li;
}

function buildSection(section, query) {
    const wrapper = document.createElement('div');
    wrapper.className = 'search-result-section';

    const heading = document.createElement('div');
    heading.className = 'search-result-heading';

    const label = document.createElement('span');
    label.className = 'search-result-heading-label';
    label.textContent = section.label;

    const count = document.createElement('span');
    count.className = 'search-result-heading-count';
    count.textContent = section.total;

    heading.appendChild(label);
    heading.appendChild(count);
    wrapper.appendChild(heading);

    const list = document.createElement('ul');
    list.className = 'search-result-list';

    section.items.forEach((item) => {
        list.appendChild(buildResultItem(item, section.key));
    });

    wrapper.appendChild(list);

    if (section.total > section.items.length) {
        const viewAll = document.createElement('a');
        viewAll.className = 'search-result-view-all';
        viewAll.href = `/search?q=${encodeURIComponent(query)}#search-${section.key}`;
        viewAll.textContent = `View all ${section.total} in ${section.label}`;
        wrapper.appendChild(viewAll);
    }

    return wrapper;
}

export function initSiteSearch() {
    const modal = document.getElementById('searchModal');
    const form = document.getElementById('searchForm');
    const input = document.getElementById('searchInput');
    const results = document.getElementById('searchResults');
    const hint = document.getElementById('searchInputHint');

    if (!modal || !form || !input || !results) {
        return;
    }

    let debounceTimer = null;
    let closeTimer = null;
    let abortController = null;
    let activeIndex = -1;

    function setLoading(isLoading) {
        form.classList.toggle('is-loading', isLoading);
    }

    function updateHint() {
        if (!hint) {
            return;
        }

        const query = input.value.trim();
        const shouldShow = document.activeElement === input && query.length > 0 && query.length < MIN_QUERY_LENGTH;

        if (shouldShow) {
            const remaining = MIN_QUERY_LENGTH - query.length;
            hint.textContent = `Type ${remaining} more ${remaining === 1 ? 'character' : 'characters'} to open the auto suggestion`;
        }

        hint.classList.toggle('is-visible', shouldShow);
    }

    function openResults() {
        window.clearTimeout(closeTimer);
        results.hidden = false;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => results.classList.add('is-open'));
        });
        input.setAttribute('aria-expanded', 'true');
    }

    function optionEls() {
        return Array.from(results.querySelectorAll('.search-result-item'));
    }

    function clearActive() {
        optionEls().forEach((el) => el.classList.remove('is-active'));
        activeIndex = -1;
    }

    function setActive(index) {
        const options = optionEls();

        if (options.length === 0) {
            return;
        }

        clearActive();
        activeIndex = ((index % options.length) + options.length) % options.length;
        const active = options[activeIndex];
        active.classList.add('is-active');
        active.scrollIntoView({ block: 'nearest' });
    }

    function closeResults() {
        results.classList.remove('is-open');
        input.setAttribute('aria-expanded', 'false');
        clearActive();

        window.clearTimeout(closeTimer);
        closeTimer = window.setTimeout(() => {
            results.hidden = true;
            results.innerHTML = '';
        }, CLOSE_TRANSITION_MS);
    }

    function renderEmpty(message) {
        results.innerHTML = '';
        const empty = document.createElement('p');
        empty.className = 'search-result-empty';
        empty.textContent = message;
        results.appendChild(empty);
        openResults();
    }

    function renderSections(sections, query) {
        results.innerHTML = '';

        if (sections.length === 0) {
            renderEmpty(`No matches for "${query}"`);

            return;
        }

        sections.forEach((section) => {
            results.appendChild(buildSection(section, query));
        });

        openResults();
        clearActive();
    }

    async function fetchSuggestions(query) {
        abortController?.abort();
        abortController = new AbortController();
        setLoading(true);

        try {
            const response = await fetch(`/search/suggest?q=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
                signal: abortController.signal,
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            renderSections(data.sections || [], query);
        } catch (error) {
            if (error.name !== 'AbortError') {
                closeResults();
            }
        } finally {
            setLoading(false);
        }
    }

    input.addEventListener('input', () => {
        const query = input.value.trim();

        window.clearTimeout(debounceTimer);
        updateHint();

        if (query.length < MIN_QUERY_LENGTH) {
            abortController?.abort();
            closeResults();

            return;
        }

        debounceTimer = window.setTimeout(() => fetchSuggestions(query), DEBOUNCE_MS);
    });

    input.addEventListener('focus', updateHint);
    input.addEventListener('blur', updateHint);

    results.addEventListener('click', (event) => {
        const item = event.target.closest('.search-result-item');

        if (item?.dataset.url) {
            window.location.href = item.dataset.url;
        }
    });

    input.addEventListener('keydown', (event) => {
        const options = optionEls();

        if (event.key === 'ArrowDown') {
            if (options.length > 0) {
                event.preventDefault();
                setActive(activeIndex + 1);
            }

            return;
        }

        if (event.key === 'ArrowUp') {
            if (options.length > 0) {
                event.preventDefault();
                setActive(activeIndex - 1);
            }

            return;
        }

        if (event.key === 'Enter') {
            if (activeIndex >= 0 && options[activeIndex]) {
                event.preventDefault();
                window.location.href = options[activeIndex].dataset.url;
            }

            return;
        }

        if (event.key === 'Escape') {
            closeResults();
        }
    });

    form.addEventListener('submit', (event) => {
        const query = input.value.trim();

        if (query.length === 0) {
            event.preventDefault();
        }
    });

    document.getElementById('openSearch')?.addEventListener('click', () => {
        window.setTimeout(() => input.focus(), 50);
    });

    document.getElementById('closeSearch')?.addEventListener('click', closeResults);
}
