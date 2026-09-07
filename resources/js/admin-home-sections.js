// Admin > Home Page Sections: pick which articles appear in each homepage
// carousel (Latest Pieces / The Selection / Features / Journal), and drag to
// reorder the picks. Each section is its own independent form.
//
// The "Add an article" field is a Select2 autocomplete (see
// [data-select2-search] in adminlte.js) rather than a full list of every
// active article — that's what lets this scale to hundreds of articles:
// nothing is fetched until the admin actually searches, and only a handful
// of matches ever come back.

import Sortable from 'sortablejs';

function syncHiddenInputs(section) {
    const pickedList = section.querySelector('[data-picked-list]');
    const inputsHost = section.querySelector('[data-picked-inputs]');

    inputsHost.innerHTML = '';

    Array.from(pickedList.querySelectorAll(':scope > li[data-article-id]')).forEach((li) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'article_ids[]';
        input.value = li.dataset.articleId;
        inputsHost.append(input);
    });
}

function articleRow(id, label) {
    const li = document.createElement('li');
    li.className = 'home-section-article';
    li.dataset.articleId = String(id);

    const text = document.createElement('span');
    text.className = 'home-section-article-label';
    text.innerHTML = '<i class="bi bi-grip-vertical text-body-secondary me-1" aria-hidden="true"></i>';
    text.append(label);
    li.append(text);

    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'btn btn-sm btn-outline-danger';
    remove.dataset.removeArticle = '';
    remove.textContent = 'Remove';
    li.append(remove);

    return li;
}

function wireSection(section) {
    const pickedList = section.querySelector('[data-picked-list]');
    const addSelect = section.querySelector('.home-section-add-select');
    const addButton = section.querySelector('[data-add-article]');

    const removeEmptyHint = () => pickedList.querySelector('[data-empty-hint]')?.remove();

    const restoreEmptyHintIfNeeded = () => {
        if (pickedList.querySelector(':scope > li[data-article-id]') === null) {
            const hint = document.createElement('li');
            hint.className = 'home-section-list-empty text-body-secondary';
            hint.dataset.emptyHint = '';
            hint.textContent = 'Nothing picked yet — this section is hidden on the homepage.';
            pickedList.append(hint);
        }
    };

    addButton.addEventListener('click', () => {
        const jq = window.jQuery ?? window.$;
        const selected = jq ? jq(addSelect).select2('data')[0] : null;

        if (!selected || !selected.id) {
            return;
        }

        const alreadyPicked = pickedList.querySelector(`[data-article-id="${CSS.escape(String(selected.id))}"]`);

        if (alreadyPicked) {
            jq?.(addSelect).val(null).trigger('change');

            return;
        }

        removeEmptyHint();
        pickedList.append(articleRow(selected.id, selected.text));
        syncHiddenInputs(section);

        jq?.(addSelect).val(null).trigger('change');
    });

    section.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove-article]');

        if (!button || !section.contains(button)) {
            return;
        }

        button.closest('li')?.remove();
        restoreEmptyHintIfNeeded();
        syncHiddenInputs(section);
    });

    Sortable.create(pickedList, {
        animation: 150,
        filter: '[data-remove-article], [data-empty-hint]',
        preventOnFilter: false,
        onEnd: () => syncHiddenInputs(section),
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-home-section]').forEach(wireSection);
});
