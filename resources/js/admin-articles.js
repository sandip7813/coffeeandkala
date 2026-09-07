// Powers the Features/Journals article form:
//  - AJAX validation + submission (no full-page reload, ever — errors are
//    rendered inline and the page only navigates away on success).
//  - a loading overlay while the request is in flight.
//  - the repeatable "Content" sections and "FAQs": adding/removing blocks
//    from the <template> tags (with a slow, smooth slide-down entrance and
//    a removal confirmation), and the media-type toggle (Image / YouTube
//    Video / Gallery Images — mutually exclusive; Content is common to all
//    three).
//  - client-side upload validation (format/size/count) and a Quill editor
//    on each section's Content field, plus Introduction/Editor's Note/
//    Author's Note on the Essentials tab.
//  - drag-and-drop section reordering (edit form only) via its drag handle,
//    saved instantly.

import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import Swal from 'sweetalert2';
import { Tab } from 'bootstrap';
import Sortable from 'sortablejs';

const swalButtonClasses = {
    popup: 'swal2-adminlte',
    actions: 'gap-2',
    confirmButton: 'btn btn-primary px-3',
    cancelButton: 'btn btn-outline-secondary px-3',
};

// --- Repeatable block helpers -----------------------------------------------

function nextIndex(container, selector) {
    return container.querySelectorAll(selector).length;
}

function renumber(container, blockSelector, numberSelector) {
    container.querySelectorAll(blockSelector).forEach((block, position) => {
        const label = block.querySelector(numberSelector);
        if (label) {
            label.textContent = String(position + 1);
        }
    });
}

/**
 * A real slide-down: animates max-height from 0 to the block's natural
 * height (plus opacity), then clears the inline styles so the block behaves
 * normally afterwards (e.g. if its own content later changes height).
 */
function slideIn(block) {
    block.style.overflow = 'hidden';
    block.style.maxHeight = '0px';
    block.style.opacity = '0';
    block.style.transition = 'none';

    // Force a reflow so the browser registers the collapsed state before
    // the transition to the measured height begins.
    void block.offsetHeight;

    const targetHeight = block.scrollHeight;

    block.style.transition = 'max-height .5s ease, opacity .5s ease';
    block.style.maxHeight = `${targetHeight}px`;
    block.style.opacity = '1';

    block.addEventListener('transitionend', function onEnd(event) {
        if (event.propertyName !== 'max-height') {
            return;
        }
        block.style.maxHeight = '';
        block.style.overflow = '';
        block.style.transition = '';
        block.removeEventListener('transitionend', onEnd);
    });
}

async function confirmRemoval(itemLabel) {
    const result = await Swal.fire({
        title: `Remove this ${itemLabel}?`,
        text: 'Any content already entered in it will be discarded.',
        icon: 'warning',
        showCancelButton: true,
        focusCancel: true,
        reverseButtons: true,
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel',
        buttonsStyling: false,
        customClass: { ...swalButtonClasses, confirmButton: 'btn btn-danger px-3' },
    });

    return result.isConfirmed;
}

// --- Quill --------------------------------------------------------------

/**
 * Mounts Quill on a single editor element, syncing its HTML into the
 * paired hidden textarea (the pair shares an immediate parent — see
 * Introduction/Editor's Note/Author's Note on the Essentials tab, and each
 * section's Content field). A no-op if already mounted, or no pair is
 * found.
 */
function initQuillEditor(editorEl) {
    const input = editorEl.parentElement?.querySelector('[data-quill-input]');

    if (!input || editorEl.dataset.quillReady) {
        return;
    }

    editorEl.dataset.quillReady = '1';

    const quill = new Quill(editorEl, {
        theme: 'snow',
        placeholder: '',
    });

    // Kept for the media-type switch guard (below) to blank the editor when
    // a section's media-type choice is abandoned with content still in it.
    editorEl.quillInstance = quill;

    quill.on('text-change', () => {
        input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
    });
}

/**
 * Mounts every Quill editor found within $root — one section block has one
 * (its Content field); the Essentials tab has three (Introduction, Editor's
 * Note, Author's Note).
 */
function wireQuillEditor(root) {
    root.querySelectorAll('[data-quill-target]').forEach(initQuillEditor);
}

// --- Upload validation -----------------------------------------------------

function formatSize(bytes) {
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function validateImageInput(input) {
    const errorEl = input.parentElement.querySelector('[data-image-error]');
    const maxKb = Number(input.dataset.maxKb || 0);
    const formats = (input.dataset.formats || '').split(',').filter(Boolean);
    const file = input.files && input.files[0];

    if (!errorEl) {
        return;
    }

    if (!file) {
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();
    const tooBig = maxKb > 0 && file.size > maxKb * 1024;
    const wrongFormat = formats.length > 0 && !formats.includes(extension);

    if (tooBig || wrongFormat) {
        errorEl.textContent = wrongFormat
            ? `"${file.name}" isn't an accepted format (${formats.join(', ').toUpperCase()}).`
            : `"${file.name}" is ${formatSize(file.size)}, over the ${(maxKb / 1024).toFixed(1)} MB limit.`;
        errorEl.classList.remove('d-none');
        input.value = '';
    } else {
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
    }
}

// --- Gallery Images: repeatable file+caption slots --------------------------
// A section's total gallery isn't capped — only how many brand-new slots
// (no data-gallery-slot-id-field, i.e. not yet saved) can be added in a
// single edit: at most 4, matching UpdateArticleRequest's server-side check.

function galleryAddButtonFor(gallerySlots) {
    return gallerySlots.parentElement?.querySelector('[data-add-gallery-slot]') ?? null;
}

function isNewGallerySlot(slot) {
    return !slot.querySelector('[data-gallery-slot-id-field]');
}

function updateGalleryAddButtonState(gallerySlots) {
    const addButton = galleryAddButtonFor(gallerySlots);
    if (!addButton) {
        return;
    }
    const newSlotCount = Array.from(gallerySlots.querySelectorAll('[data-gallery-slot]')).filter(isNewGallerySlot).length;
    addButton.disabled = newSlotCount >= 4;
}

function wireGallerySlot(slot, gallerySlots) {
    const imageInput = slot.querySelector('[data-image-input]');
    if (imageInput) {
        imageInput.addEventListener('change', () => validateImageInput(imageInput));
    }

    const removeButton = slot.querySelector('[data-remove-gallery-slot]');
    if (removeButton) {
        removeButton.addEventListener('click', async () => {
            if (!(await confirmRemoval('image'))) {
                return;
            }
            slot.remove();
            updateGalleryAddButtonState(gallerySlots);
        });
    }
}

/**
 * Wires one section's Gallery Images block: each image is its own slot
 * (file input + caption input), added via the same slide-down "Add Image"
 * pattern as sections/FAQs — so every image can carry a distinct caption
 * instead of being one flat multi-file upload. Up to 4 *new* slots can be
 * added per edit; the section's total gallery size isn't capped.
 */
function wireGalleryBlock(block) {
    const gallerySlots = block.querySelector('[data-gallery-slots]');
    const template = block.querySelector('template[data-gallery-slot-template]');
    const addButton = block.querySelector('[data-add-gallery-slot]');

    if (!gallerySlots) {
        return;
    }

    gallerySlots.querySelectorAll('[data-gallery-slot]').forEach((slot) => wireGallerySlot(slot, gallerySlots));
    updateGalleryAddButtonState(gallerySlots);

    if (addButton && template) {
        addButton.addEventListener('click', () => {
            const newSlotCount = Array.from(gallerySlots.querySelectorAll('[data-gallery-slot]')).filter(isNewGallerySlot).length;
            if (newSlotCount >= 4) {
                return;
            }
            // The slot's own index just needs to be unique within this
            // section's gallery array — total slot count works for that,
            // independent of the new-slot cap enforced above.
            const index = gallerySlots.querySelectorAll('[data-gallery-slot]').length;
            const html = template.innerHTML.replaceAll('__SLOT__', String(index));
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const slot = wrapper.firstElementChild;
            gallerySlots.appendChild(slot);
            wireGallerySlot(slot, gallerySlots);
            slideIn(slot);
            updateGalleryAddButtonState(gallerySlots);
        });
    }
}

// --- Media-type toggle (Image / Video / Gallery) ----------------------------

function wireMediaTypeToggle(block) {
    const blocks = {
        image: block.querySelector('[data-media-block="image"]'),
        video: block.querySelector('[data-media-block="video"]'),
        gallery: block.querySelector('[data-media-block="gallery"]'),
    };
    const options = block.querySelectorAll('[data-media-type-option]');

    // Content is common to every media type (including Gallery Images) —
    // only the media-specific blocks above it toggle.
    options.forEach((option) => {
        option.addEventListener('change', () => {
            if (!option.checked) {
                return;
            }
            Object.entries(blocks).forEach(([type, el]) => {
                if (el) {
                    el.style.display = option.value === type ? '' : 'none';
                }
            });
        });
    });
}

async function confirmSectionStatusChange(activating) {
    const result = await Swal.fire({
        title: activating ? 'Activate this section?' : 'Deactivate this section?',
        text: activating
            ? 'It will become visible on the site frontend.'
            : 'It will be hidden from the site frontend.',
        icon: 'warning',
        showCancelButton: true,
        focusCancel: true,
        reverseButtons: true,
        confirmButtonText: activating ? 'Yes, activate it' : 'Yes, deactivate it',
        cancelButtonText: 'Cancel',
        buttonsStyling: false,
        customClass: { ...swalButtonClasses, confirmButton: activating ? 'btn btn-primary px-3' : 'btn btn-danger px-3' },
    });

    return result.isConfirmed;
}

/**
 * Wires the per-section Active/Inactive switch (edit form only). Always
 * confirms via SweetAlert before applying a change. A section that already
 * exists in the database (data-status-url present) saves the flip instantly
 * via its own PUT request; a section only just added in this edit session
 * (no id yet) just updates the checkbox/card styling locally, saved along
 * with the rest of the form on submit.
 */
function wireSectionActiveToggle(block) {
    const toggle = block.querySelector('[data-section-active-toggle]');

    if (!toggle) {
        return;
    }

    toggle.addEventListener('click', async (event) => {
        // A checkbox's `checked` has already flipped to its new value by
        // the time 'click' fires (the browser's activation behavior runs
        // before dispatch) — preventDefault() below reverts it back to the
        // pre-click value, but that happens *after* this handler returns,
        // so `toggle.checked` here already IS the intended new state.
        const activating = toggle.checked;

        event.preventDefault();

        if (!(await confirmSectionStatusChange(activating))) {
            return;
        }

        const statusUrl = toggle.dataset.statusUrl;

        if (!statusUrl) {
            toggle.checked = activating;
            block.classList.toggle('article-section--inactive', !activating);
            await alertSuccess(activating ? 'Section activated.' : 'Section deactivated.');
            return;
        }

        toggle.disabled = true;
        showFormLoader(activating ? 'Activating section…' : 'Deactivating section…');

        try {
            const response = await fetch(statusUrl, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
            });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            const data = await response.json();
            toggle.checked = data.is_active;
            block.classList.toggle('article-section--inactive', !data.is_active);
            hideFormLoader();
            await alertSuccess(data.is_active ? 'Section activated.' : 'Section deactivated.');
        } catch {
            hideFormLoader();
            await alertError('Error', 'Could not update this section\'s status. Please try again.');
        } finally {
            toggle.disabled = false;
        }
    });
}

async function confirmHomeSectionChange(activating, sectionLabel) {
    const result = await Swal.fire({
        title: activating ? `Add to "${sectionLabel}"?` : `Remove from "${sectionLabel}"?`,
        text: activating
            ? 'It will appear in this homepage carousel.'
            : 'It will no longer appear in this homepage carousel.',
        icon: 'warning',
        showCancelButton: true,
        focusCancel: true,
        reverseButtons: true,
        confirmButtonText: activating ? 'Yes, add it' : 'Yes, remove it',
        cancelButtonText: 'Cancel',
        buttonsStyling: false,
        customClass: { ...swalButtonClasses, confirmButton: activating ? 'btn btn-primary px-3' : 'btn btn-danger px-3' },
    });

    return result.isConfirmed;
}

/**
 * Home Page Sections tab (edit form only): each switch instantly adds/
 * removes this article from that homepage carousel. Always confirms via
 * SweetAlert first, same as the section Active/Inactive switch.
 */
function wireHomeSectionToggles() {
    document.querySelectorAll('[data-home-section-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', async (event) => {
            const activating = toggle.checked;

            event.preventDefault();

            if (!(await confirmHomeSectionChange(activating, toggle.dataset.sectionLabel ?? 'this section'))) {
                return;
            }

            const toggleUrl = toggle.dataset.toggleUrl;

            if (!toggleUrl) {
                return;
            }

            toggle.disabled = true;

            try {
                const response = await fetch(toggleUrl, {
                    method: 'PUT',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const data = await response.json();
                toggle.checked = data.active;
                await alertSuccess(data.active ? 'Added to homepage section.' : 'Removed from homepage section.');
            } catch {
                toggle.checked = !activating;
                await alertError('Error', 'Could not update this homepage section. Please try again.');
            } finally {
                toggle.disabled = false;
            }
        });
    });
}

function wireSectionBlock(block) {
    const imagePosition = block.querySelector('[data-image-position]');
    const contentPositionWrap = block.querySelector('[data-content-position-wrap]');
    const videoCompanionType = block.querySelector('[data-video-companion-type]');
    const videoCompanionImageWrap = block.querySelector('[data-video-companion-image-wrap]');
    const removeButton = block.querySelector('[data-remove-section]');

    if (imagePosition && contentPositionWrap) {
        const toggle = () => {
            contentPositionWrap.style.display = ['left', 'right'].includes(imagePosition.value) ? '' : 'none';
        };
        imagePosition.addEventListener('change', toggle);
        toggle();
    }

    if (videoCompanionType && videoCompanionImageWrap) {
        const toggle = () => {
            videoCompanionImageWrap.style.display = videoCompanionType.value === 'image' ? '' : 'none';
        };
        videoCompanionType.addEventListener('change', toggle);
        toggle();
    }

    wireMediaTypeToggle(block);
    wireMediaTypeGuard(block);
    wireGalleryBlock(block);
    wireSectionActiveToggle(block);

    // Section Image / Video Companion Image single-file inputs (the
    // gallery's own per-slot inputs are wired inside wireGalleryBlock).
    ['[data-media-block="image"] [data-image-input]', '[data-media-block="video"] [data-image-input]'].forEach((selector) => {
        const input = block.querySelector(selector);
        if (input) {
            input.addEventListener('change', () => validateImageInput(input));
        }
    });

    if (removeButton) {
        removeButton.addEventListener('click', async () => {
            if (await confirmRemoval('section')) {
                block.remove();
                renumber(document.getElementById('article-sections'), '.article-section', '[data-section-number]');
            }
        });
    }

    wireQuillEditor(block);
}

// --- Section reordering (drag handle, instant save) -------------------------

// Only these id prefixes embed a section index (gallery-slot ids like
// "remove-gallery-42" embed a MediaFile id instead and must never match
// here, which is why this is an explicit list rather than a "ends in
// digits" pattern).
const SECTION_INDEXED_ID_PREFIXES = [
    'media-image-', 'media-video-', 'media-gallery-', 'section-active-',
    'remove-image-', 'remove-video-companion-image-', 'section-body-',
];

/**
 * After a drag reorders the section cards, every field inside them is still
 * named/id'd for its OLD position (sections[2][title], "section-active-2",
 * …) — this rewrites all of that to match the new DOM order, recursing into
 * each section's gallery slots (sections[2][gallery][1][...]) so only the
 * outer section index changes there, not the slot index.
 */
function renumberSectionAttributes(container) {
    container.querySelectorAll(':scope > .article-section').forEach((block, newIndex) => {
        block.querySelectorAll('[name^="sections["]').forEach((el) => {
            el.name = el.name.replace(/^sections\[\d+\]/, `sections[${newIndex}]`);
        });

        block.querySelectorAll('[data-quill-target^="sections["]').forEach((el) => {
            el.dataset.quillTarget = el.dataset.quillTarget.replace(/^sections\[\d+\]/, `sections[${newIndex}]`);
        });

        SECTION_INDEXED_ID_PREFIXES.forEach((prefix) => {
            const field = block.querySelector(`[id^="${prefix}"]`);
            if (field) {
                field.id = `${prefix}${newIndex}`;
            }
            const label = block.querySelector(`label[for^="${prefix}"]`);
            if (label) {
                label.htmlFor = `${prefix}${newIndex}`;
            }
        });

        // The collapse toggle button references the body's id via
        // data-bs-target/aria-controls, not id/for, so it needs its own fix.
        const collapseToggle = block.querySelector('[data-bs-target^="#section-body-"]');
        if (collapseToggle) {
            collapseToggle.dataset.bsTarget = `#section-body-${newIndex}`;
            collapseToggle.setAttribute('aria-controls', `section-body-${newIndex}`);
        }
    });

    renumber(container, '.article-section', '[data-section-number]');
}

/**
 * The ids of already-saved sections (data-section-id-field), in their
 * current DOM order — a section added in this edit session but not yet
 * submitted has no id yet, so it's simply left out; its position is
 * captured normally on the next full form Save.
 */
function savedSectionIds(container) {
    return Array.from(container.querySelectorAll(':scope > .article-section'))
        .map((block) => block.querySelector('[data-section-id-field]')?.value)
        .filter(Boolean);
}

function wireSectionReordering(container) {
    const reorderUrl = container.dataset.reorderUrl;

    if (!reorderUrl) {
        return;
    }

    Sortable.create(container, {
        handle: '[data-drag-handle]',
        // Interactive controls (Active switch, collapse toggle, Remove)
        // sit inside the same header row as the drag handle — filter keeps
        // clicks on them working normally instead of starting a drag.
        filter: '[data-no-drag]',
        preventOnFilter: false,
        animation: 150,
        onEnd: async () => {
            renumberSectionAttributes(container);

            const sectionIds = savedSectionIds(container);
            if (sectionIds.length === 0) {
                return;
            }

            showFormLoader('Saving new order…');

            try {
                const response = await fetch(reorderUrl, {
                    method: 'PUT',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({ section_ids: sectionIds }),
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                hideFormLoader();
                await alertSuccess('Section order saved.');
            } catch {
                hideFormLoader();
                await alertError('Error', 'Could not save the new section order. Please try again.');
            }
        },
    });
}

/**
 * Swaps each section's collapse-toggle chevron between up (open) and down
 * (closed) as Bootstrap expands/collapses its body. Delegated on the
 * sections container so it keeps working for sections added later, without
 * needing to be re-wired per block.
 */
function wireSectionCollapseIcons(container) {
    const setIcon = (collapseEl, expanded) => {
        const trigger = document.querySelector(`[data-bs-target="#${collapseEl.id}"]`);
        const icon = trigger?.querySelector('[data-collapse-icon]');
        if (icon) {
            icon.classList.toggle('bi-chevron-up', expanded);
            icon.classList.toggle('bi-chevron-down', !expanded);
        }
    };

    container.addEventListener('show.bs.collapse', (event) => setIcon(event.target, true));
    container.addEventListener('hide.bs.collapse', (event) => setIcon(event.target, false));
}

function wireFaqBlock(block) {
    const removeButton = block.querySelector('[data-remove-faq]');

    if (removeButton) {
        removeButton.addEventListener('click', async () => {
            if (await confirmRemoval('FAQ')) {
                block.remove();
                renumber(document.getElementById('article-faqs'), '.article-faq', '[data-faq-number]');
            }
        });
    }
}

// --- Loading overlay (shared markup/classes with adminlte.js's page loader) -

function ensureFormLoader() {
    let loader = document.getElementById('admin-page-loader');

    if (loader) {
        return loader;
    }

    const spinnerSrc = document.body.dataset.pageLoaderSrc || '/logo-spinner.gif';

    loader = document.createElement('div');
    loader.id = 'admin-page-loader';
    loader.className = 'admin-page-loader';
    loader.setAttribute('role', 'status');
    loader.setAttribute('aria-live', 'polite');
    loader.setAttribute('aria-busy', 'true');
    loader.innerHTML = `
        <div class="admin-page-loader__card">
            <img class="admin-page-loader__spinner" src="${spinnerSrc}" alt="" width="64" height="64" aria-hidden="true">
            <p class="admin-page-loader__text">Saving…</p>
        </div>
    `;
    document.body.appendChild(loader);

    return loader;
}

function showFormLoader(message = 'Saving…') {
    const loader = ensureFormLoader();
    const text = loader.querySelector('.admin-page-loader__text');
    if (text) {
        text.textContent = message;
    }
    loader.classList.add('is-visible');
    document.body.style.overflow = 'hidden';
}

function hideFormLoader() {
    const loader = document.getElementById('admin-page-loader');
    if (loader) {
        loader.classList.remove('is-visible');
    }
    document.body.style.overflow = '';
}

// --- AJAX form: validation + submission, no page reload ---------------------

/**
 * Laravel error key 'sections.0.gallery.2.caption' → looks for an input
 * named 'sections[0][gallery][2][caption]', then '…][gallery][2]', '…
 * ][gallery]', and finally 'sections[0]' — walking up a segment at a time
 * until something in the form actually matches, so item-level errors still
 * land somewhere sensible.
 */
function fieldElement(form, dotKey) {
    const parts = dotKey.split('.');

    for (let end = parts.length; end >= 1; end--) {
        const slice = parts.slice(0, end);
        const bracket = slice[0] + slice.slice(1).map((p) => `[${p}]`).join('');

        const exact = form.querySelector(`[name="${bracket}"]`);
        if (exact) {
            return exact;
        }

        const multi = form.querySelector(`[name="${bracket}[]"]`);
        if (multi) {
            return multi;
        }
    }

    return null;
}

function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
}

/**
 * Marks each invalid field's red border (matching the site-wide convention
 * — see initValidationErrorAlert in adminlte.js — where per-field message
 * text is suppressed in favour of one summary alert) and returns the first
 * invalid field plus the flat list of every message, for that alert.
 */
function applyErrors(form, errors) {
    let firstInvalid = null;
    const messages = [];

    Object.entries(errors).forEach(([key, fieldMessages]) => {
        messages.push(...fieldMessages);

        const el = fieldElement(form, key);
        if (!el) {
            return;
        }

        el.classList.add('is-invalid');

        if (!firstInvalid) {
            firstInvalid = el;
        }
    });

    return { firstInvalid, messages };
}

function revealTabFor(el) {
    const pane = el.closest('.tab-pane');
    if (!pane || !pane.id) {
        return;
    }
    const trigger = document.querySelector(`[data-bs-target="#${pane.id}"]`);
    if (trigger) {
        Tab.getOrCreateInstance(trigger).show();
    }
}

async function alertError(title, text) {
    await Swal.fire({
        title,
        text,
        icon: 'error',
        confirmButtonText: 'OK',
        buttonsStyling: false,
        customClass: swalButtonClasses,
    });
}

async function alertSuccess(title) {
    await Swal.fire({
        title,
        icon: 'success',
        timer: 1800,
        showConfirmButton: false,
        customClass: swalButtonClasses,
    });
}

function escapeHtml(value) {
    return value.replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    })[char]);
}

/**
 * Same bulleted-list SweetAlert style as initValidationErrorAlert in
 * adminlte.js (used for the classic full-page-reload validation flow) —
 * kept consistent here for the AJAX flow.
 */
async function alertValidationMessages(messages) {
    const list = messages.map((message) => `<li>${escapeHtml(String(message))}</li>`).join('');

    await Swal.fire({
        icon: 'error',
        title: messages.length === 1 ? 'Please fix the following' : `Please fix the following ${messages.length} issues`,
        html: `<ul class="text-start mb-0 ps-3">${list}</ul>`,
        confirmButtonText: 'OK',
        buttonsStyling: false,
        customClass: swalButtonClasses,
    });
}

/**
 * "Save as Draft" and "Publish"/"Submit for Review" are both type="submit"
 * buttons on the same form — clicking either records which one in a hidden
 * field (save_action) before the browser's native submit event fires, so
 * the AJAX handler's plain `new FormData(form)` picks it up like any other
 * field, without depending on `event.submitter` support.
 */
function wireSaveActionButtons(form) {
    const field = form.querySelector('[data-save-action-field]');
    if (!field) {
        return;
    }
    form.querySelectorAll('[data-save-action-button]').forEach((button) => {
        button.addEventListener('click', () => {
            field.value = button.dataset.saveActionButton;
        });
    });
}

function wireAjaxForm(form) {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        showFormLoader(form.dataset.loadingText || 'Saving…');

        let response;

        try {
            response = await fetch(form.action, {
                method: 'POST', // Laravel reads the spoofed _method (PUT) hidden field itself
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });
        } catch {
            hideFormLoader();
            await alertError('Network error', 'Please check your connection and try again.');
            return;
        }

        if (response.status === 422) {
            const data = await response.json();
            const { firstInvalid, messages } = applyErrors(form, data.errors || {});
            hideFormLoader();

            if (firstInvalid) {
                revealTabFor(firstInvalid);
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus({ preventScroll: true });
            }

            await alertValidationMessages(messages.length > 0 ? messages : [data.message || 'Some information is missing or invalid.']);
            return;
        }

        if (!response.ok) {
            hideFormLoader();
            let message = 'Something went wrong. Please try again.';
            try {
                const data = await response.json();
                message = data.message || message;
            } catch {
                // response wasn't JSON — keep the generic message
            }
            await alertError('Error', message);
            return;
        }

        // Success: the server responded with a redirect, which fetch already
        // followed, so response.url is the final destination — navigate
        // there directly rather than reloading the current page.
        window.location.href = response.url;
    });
}

// --- "This section has" (Image/Video/Gallery) switch guard -----------------
// Warn + clear when switching a section's media-type choice away from an
// option that already has data entered or a file selected.

/**
 * True if any field inside $el has been filled in — text/url inputs,
 * textareas (including a section's hidden Quill-mirror textarea), selects,
 * or a chosen file.
 */
function hasFilledFields(el) {
    for (const field of el.querySelectorAll('input[type="text"], input[type="url"], textarea')) {
        if (field.value && field.value.trim() !== '') {
            return true;
        }
    }
    for (const field of el.querySelectorAll('select')) {
        if (field.value) {
            return true;
        }
    }
    for (const field of el.querySelectorAll('input[type="file"]')) {
        if (field.files && field.files.length > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Blanks every field inside $el back to its empty state — text/select/file
 * inputs, Quill editors (via their tracked instance), and the
 * dependent-field visibility that goes with them.
 */
function clearFieldsWithin(el) {
    el.querySelectorAll('input[type="text"], input[type="url"], textarea').forEach((field) => {
        field.value = '';
    });
    el.querySelectorAll('select').forEach((field) => {
        field.value = '';
    });
    el.querySelectorAll('input[type="file"]').forEach((field) => {
        field.value = '';
    });
    el.querySelectorAll('input[type="checkbox"]').forEach((field) => {
        field.checked = false;
    });
    el.querySelectorAll('[data-quill-target]').forEach((editorEl) => {
        editorEl.quillInstance?.setText('');
    });
    el.querySelectorAll('[data-content-position-wrap], [data-video-companion-image-wrap]').forEach((wrap) => {
        wrap.style.display = 'none';
    });
    el.querySelectorAll('[data-image-error]').forEach((error) => {
        error.classList.add('d-none');
        error.textContent = '';
    });
    // Gallery slots are removed entirely rather than just blanked — a pile
    // of empty file/caption rows isn't a meaningful "cleared" state.
    el.querySelectorAll('[data-gallery-slot]').forEach((slot) => slot.remove());
    el.querySelectorAll('[data-add-gallery-slot]').forEach((button) => {
        button.disabled = false;
    });
}

async function confirmMediaTypeSwitch() {
    const result = await Swal.fire({
        title: 'Switch this section’s media type?',
        text: 'The information entered for the current choice will be cleared.',
        icon: 'warning',
        showCancelButton: true,
        focusCancel: true,
        reverseButtons: true,
        confirmButtonText: 'Yes, clear and switch',
        cancelButtonText: 'Stay here',
        buttonsStyling: false,
        customClass: { ...swalButtonClasses, confirmButton: 'btn btn-danger px-3' },
    });

    return result.isConfirmed;
}

/**
 * Intercepts clicks on the "This section has: An Image / A YouTube Video /
 * Gallery Images" radios. If the option currently active has any data
 * entered or a file selected, the click is blocked and a confirmation is
 * shown before that option's fields are cleared and the switch proceeds —
 * cancelling leaves the current choice (and its data) untouched.
 */
function wireMediaTypeGuard(block) {
    const radios = block.querySelectorAll('[data-media-type-option]');

    radios.forEach((radio) => {
        radio.addEventListener('click', (event) => {
            if (radio.checked) {
                return;
            }

            const current = block.querySelector('[data-media-type-option]:checked');
            const currentBlock = current ? block.querySelector(`[data-media-block="${current.value}"]`) : null;

            if (!currentBlock || !hasFilledFields(currentBlock)) {
                return;
            }

            event.preventDefault();

            confirmMediaTypeSwitch().then((confirmed) => {
                if (!confirmed) {
                    return;
                }
                clearFieldsWithin(currentBlock);
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });
}

// --- Boot --------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    const essentialsTab = document.getElementById('tab-essentials');
    if (essentialsTab) {
        wireQuillEditor(essentialsTab);
    }

    const sectionsContainer = document.getElementById('article-sections');
    const sectionTemplate = document.getElementById('section-template');
    const addSectionButton = document.getElementById('add-section');

    if (sectionsContainer) {
        sectionsContainer.querySelectorAll('.article-section').forEach(wireSectionBlock);
        wireSectionReordering(sectionsContainer);
        wireSectionCollapseIcons(sectionsContainer);
    }

    if (addSectionButton && sectionsContainer && sectionTemplate) {
        addSectionButton.addEventListener('click', () => {
            const index = nextIndex(sectionsContainer, '.article-section');
            const html = sectionTemplate.innerHTML.replaceAll('__INDEX__', String(index));
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const block = wrapper.firstElementChild;
            sectionsContainer.appendChild(block);
            wireSectionBlock(block);
            slideIn(block);
            renumber(sectionsContainer, '.article-section', '[data-section-number]');
        });
    }

    const faqsContainer = document.getElementById('article-faqs');
    const faqTemplate = document.getElementById('faq-template');
    const addFaqButton = document.getElementById('add-faq');

    if (faqsContainer) {
        faqsContainer.querySelectorAll('.article-faq').forEach(wireFaqBlock);
    }

    if (addFaqButton && faqsContainer && faqTemplate) {
        addFaqButton.addEventListener('click', () => {
            const index = nextIndex(faqsContainer, '.article-faq');
            const html = faqTemplate.innerHTML.replaceAll('__INDEX__', String(index));
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const block = wrapper.firstElementChild;
            faqsContainer.appendChild(block);
            wireFaqBlock(block);
            slideIn(block);
            renumber(faqsContainer, '.article-faq', '[data-faq-number]');
        });
    }

    const articleForm = document.querySelector('[data-ajax-form]');
    if (articleForm) {
        wireAjaxForm(articleForm);
        wireSaveActionButtons(articleForm);
    }

    wireHomeSectionToggles();
});
