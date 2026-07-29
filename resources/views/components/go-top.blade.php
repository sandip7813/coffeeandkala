<button type="button" class="go-top" id="goTop" aria-label="Go to top" title="Go to top">
    <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
    <span>Top</span>
</button>

<script>
    (() => {
        const button = document.getElementById('goTop');

        if (!button || button.dataset.goTopBound === '1') {
            return;
        }

        button.dataset.goTopBound = '1';

        let frameId = null;

        const scrollingElement = () => document.scrollingElement || document.documentElement;

        const currentScrollTop = () => scrollingElement().scrollTop || window.scrollY || 0;

        const setScrollTop = (value) => {
            scrollingElement().scrollTop = value;
            document.documentElement.scrollTop = value;
            document.body.scrollTop = value;
            window.scrollTo(0, value);
        };

        const stopAnimation = () => {
            if (frameId !== null) {
                cancelAnimationFrame(frameId);
                frameId = null;
            }
        };

        const updateVisibility = () => {
            button.classList.toggle('is-visible', currentScrollTop() > 420);
        };

        const slideToTop = () => {
            stopAnimation();

            const start = currentScrollTop();

            if (start <= 0) {
                return;
            }

            const duration = Math.min(3200, Math.max(2400, start * 0.9));
            const startedAt = performance.now();

            const easeInOutCubic = (t) =>
                t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;

            const tick = (now) => {
                const progress = Math.min(1, (now - startedAt) / duration);
                setScrollTop(start * (1 - easeInOutCubic(progress)));

                if (progress < 1) {
                    frameId = requestAnimationFrame(tick);
                    return;
                }

                setScrollTop(0);
                frameId = null;
            };

            frameId = requestAnimationFrame(tick);
        };

        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            slideToTop();
        });

        window.addEventListener('scroll', updateVisibility, { passive: true });
        window.addEventListener('load', updateVisibility);
        updateVisibility();
    })();
</script>
