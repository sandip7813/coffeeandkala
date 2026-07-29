const GALLERIA_THEME_URL =
    'https://cdnjs.cloudflare.com/ajax/libs/galleria/1.6.1/themes/classic/galleria.classic.min.js';
const THUMB_RAIL_WIDTH = 148;
const AUTOPLAY_MS = 4500;

function readPlatesData() {
    const dataEl = document.getElementById('galleryPlatesData');

    if (!dataEl) {
        return [];
    }

    try {
        const parsed = JSON.parse(dataEl.textContent || '[]');

        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        return [];
    }
}

function getGallery() {
    if (typeof Galleria === 'undefined') {
        return null;
    }

    try {
        return Galleria.get(0);
    } catch (error) {
        return null;
    }
}

function scrollActiveThumbIntoView(gallery) {
    const active = gallery?.$('thumbnails')?.find('.active')?.get(0);

    if (active && typeof active.scrollIntoView === 'function') {
        active.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
    }
}

function initGalleryPage() {
    const modal = document.getElementById('galleriaModal');
    const closeBtn = document.getElementById('galleriaModalClose');
    const dataSource = readPlatesData();

    if (!modal || typeof Galleria === 'undefined' || typeof jQuery === 'undefined') {
        return;
    }

    let galleryStarted = false;
    const modalHome = modal.parentElement;

    function mountModalOnBody() {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    }

    function restoreModal() {
        if (modalHome && modal.parentElement !== modalHome) {
            modalHome.appendChild(modal);
        }
    }

    function setHeaderHidden(hidden) {
        document.querySelectorAll('.about-topbar, .site-header').forEach((el) => {
            if (hidden) {
                el.setAttribute('aria-hidden', 'true');
                el.setAttribute('inert', '');
            } else {
                el.removeAttribute('aria-hidden');
                el.removeAttribute('inert');
            }
        });
    }

    function openGalleria(index) {
        mountModalOnBody();
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        document.body.classList.add('galleria-open');
        setHeaderHidden(true);

        const showIndex = Number.isFinite(index) ? index : 0;

        if (!galleryStarted) {
            Galleria.loadTheme(GALLERIA_THEME_URL);

            Galleria.run('#galleriaInstance', {
                dataSource,
                show: showIndex,
                height: window.innerHeight,
                width: '100%',
                responsive: true,
                thumbCrop: true,
                imageCrop: false,
                transition: 'fade',
                transitionSpeed: 450,
                carousel: false,
                thumbWidth: 112,
                thumbHeight: 112,
                thumbMargin: 12,
                showInfo: true,
                _toggleInfo: false,
                swipe: true,
                trueFullscreen: false,
                autoplay: AUTOPLAY_MS,
                pauseOnInteraction: true,
                extend: function () {
                    const gallery = this;

                    gallery.bind('loadfinish', function () {
                        scrollActiveThumbIntoView(gallery);
                    });

                    gallery.bind('rescale', function () {
                        gallery.$('thumbnails-container').css({
                            width: window.innerWidth > 700 ? THUMB_RAIL_WIDTH : '100%',
                        });
                    });
                },
            });

            galleryStarted = true;
        } else {
            const gallery = getGallery();

            if (gallery) {
                gallery.show(showIndex);
                gallery.rescale(window.innerWidth, window.innerHeight);
                gallery.play(AUTOPLAY_MS);
            }
        }

        closeBtn?.focus({ preventScroll: true });
    }

    function closeGalleria() {
        const gallery = getGallery();

        if (gallery) {
            gallery.pause();
        }

        modal.hidden = true;
        document.body.style.overflow = '';
        document.body.classList.remove('galleria-open');
        setHeaderHidden(false);
        restoreModal();
    }

    document.querySelectorAll('[data-galleria-index]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            openGalleria(Number(link.getAttribute('data-galleria-index')));
        });
    });

    closeBtn?.addEventListener('click', closeGalleria);

    document.addEventListener('keydown', (event) => {
        if (modal.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeGalleria();

            return;
        }

        const gallery = getGallery();

        if (!gallery || event.repeat) {
            return;
        }

        if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
            event.preventDefault();
            gallery.prev();
        } else if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
            event.preventDefault();
            gallery.next();
        }
    });

    window.addEventListener('resize', () => {
        if (galleryStarted && !modal.hidden) {
            getGallery()?.rescale(window.innerWidth, window.innerHeight);
        }
    });
}

document.addEventListener('DOMContentLoaded', initGalleryPage);
