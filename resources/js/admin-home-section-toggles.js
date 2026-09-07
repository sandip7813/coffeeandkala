// Any admin page's "Home Page" switches — Features/Journals list & edit,
// Gallery/Studio list & edit (visible only to admins with
// manage-home-sections). Each one instantly adds/removes that article/image
// from its homepage carousel, after a SweetAlert confirmation.

import Swal from 'sweetalert2';

const swalButtonClasses = {
    popup: 'swal2-adminlte',
    actions: 'gap-2',
    confirmButton: 'btn btn-primary px-3',
    cancelButton: 'btn btn-outline-secondary px-3',
};

async function confirmHomeSectionChange(activating, sectionLabel, articleTitle) {
    const result = await Swal.fire({
        title: activating ? `Add to "${sectionLabel}"?` : `Remove from "${sectionLabel}"?`,
        text: activating
            ? `"${articleTitle}" will appear in this homepage carousel.`
            : `"${articleTitle}" will no longer appear in this homepage carousel.`,
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

async function alertSuccess(title) {
    await Swal.fire({
        title,
        icon: 'success',
        timer: 1800,
        showConfirmButton: false,
        customClass: swalButtonClasses,
    });
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

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-home-section-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', async (event) => {
            const activating = toggle.checked;

            event.preventDefault();

            const sectionLabel = toggle.dataset.sectionLabel ?? 'this section';
            const articleTitle = toggle.dataset.articleTitle ?? 'This article';

            if (!(await confirmHomeSectionChange(activating, sectionLabel, articleTitle))) {
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
});
