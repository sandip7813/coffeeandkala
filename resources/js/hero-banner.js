import EmblaCarousel from 'embla-carousel';
import { smoothScrollTo } from './smooth-scroll';

const reduceMotionQuery = () => window.matchMedia('(prefers-reduced-motion: reduce)');

const initHeroEditorialReveal = () => {
    const root = document.querySelector('[data-hero-editorial]');

    if (!root) {
        return;
    }

    const nodes = Array.from(root.querySelectorAll('[data-hero-reveal]'));
    const reduceMotion = reduceMotionQuery().matches;
    const wordStep = reduceMotion ? 45 : 90;
    const startLag = reduceMotion ? 80 : 120;

    nodes.forEach((node) => {
        const raw = node.textContent?.replace(/\s+/g, ' ').trim() ?? '';

        if (raw === '') {
            return;
        }

        const baseDelay = Number(node.getAttribute('data-reveal-delay') ?? 0);
        const words = raw.split(' ');

        node.setAttribute('aria-label', raw);
        node.textContent = '';

        words.forEach((word, index) => {
            const wordWrap = document.createElement('span');
            wordWrap.className = 'hero-reveal-word';
            wordWrap.style.setProperty('--word-delay', `${baseDelay + startLag + index * wordStep}ms`);

            const wordInner = document.createElement('span');
            wordInner.className = 'hero-reveal-word-inner';
            wordInner.textContent = word;

            wordWrap.appendChild(wordInner);
            node.appendChild(wordWrap);

            if (index < words.length - 1) {
                node.appendChild(document.createTextNode(' '));
            }
        });
    });

    window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
            root.classList.add('is-revealed');
        });
    });
};

const initHeroScrollCue = () => {
    const button = document.querySelector('[data-hero-scroll-to]');

    if (!button) {
        return;
    }

    button.addEventListener('click', () => {
        const targetId = button.getAttribute('data-hero-scroll-to');
        const target = targetId ? document.getElementById(targetId) : null;

        if (!target) {
            return;
        }

        smoothScrollTo(target);
    });
};

const initHeroBanner = () => {
    const root = document.querySelector('[data-hero-banner]');
    const viewport = root?.querySelector('[data-hero-viewport]');

    if (!root || !viewport) {
        return;
    }

    const slides = Array.from(root.querySelectorAll('.hero-embla__slide'));
    const thumbButtons = Array.from(root.querySelectorAll('[data-hero-thumb]'));
    const progressBar = root.querySelector('[data-hero-progress]');
    const prevBtn = root.querySelector('[data-hero-prev]');
    const nextBtn = root.querySelector('[data-hero-next]');
    const reduceMotion = reduceMotionQuery().matches;
    const autoplayDelay = 5500;
    const tweenFactorBase = 0.92;
    let autoplayTimer = null;
    let revealTimer = null;
    let tweenFactor = 0;

    const numberWithinRange = (number, min, max) => Math.min(Math.max(number, min), max);

    const setTweenFactor = (emblaApi) => {
        tweenFactor = tweenFactorBase * emblaApi.scrollSnapList().length;
    };

    const tweenOpacity = (emblaApi) => {
        const engine = emblaApi.internalEngine();
        const scrollProgress = emblaApi.scrollProgress();

        emblaApi.scrollSnapList().forEach((scrollSnap, snapIndex) => {
            let diffToTarget = scrollSnap - scrollProgress;

            if (engine.options.loop) {
                engine.slideLooper.loopPoints.forEach((loopItem) => {
                    const target = typeof loopItem.target === 'function' ? loopItem.target() : loopItem.target;

                    if (snapIndex === loopItem.index && target !== 0) {
                        const sign = Math.sign(target);

                        if (sign === -1) {
                            diffToTarget = scrollSnap - (1 + scrollProgress);
                        }

                        if (sign === 1) {
                            diffToTarget = scrollSnap + (1 - scrollProgress);
                        }
                    }
                });
            }

            const tweenValue = 1 - Math.abs(diffToTarget * tweenFactor);
            const opacity = numberWithinRange(tweenValue, 0, 1);
            const slideNodes = engine.slideRegistry[snapIndex] ?? [snapIndex];

            slideNodes.forEach((slideIndex) => {
                const slide = emblaApi.slideNodes()[slideIndex];

                if (slide) {
                    slide.style.opacity = String(opacity);
                }
            });
        });
    };

    const embla = EmblaCarousel(viewport, {
        loop: true,
        align: 'center',
        skipSnaps: false,
        containScroll: false,
        duration: reduceMotion ? 20 : 70,
    });

    const syncThumbs = () => {
        const selected = embla.selectedScrollSnap();

        thumbButtons.forEach((button, index) => {
            const isActive = index === selected;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-selected', String(isActive));
        });
    };

    const syncActiveSlide = () => {
        const selected = embla.selectedScrollSnap();

        slides.forEach((slide, index) => {
            slide.classList.toggle('is-active', index === selected);

            if (index !== selected) {
                slide.classList.remove('is-text-in');
            }
        });
    };

    const revealActiveText = () => {
        window.clearTimeout(revealTimer);

        const selected = embla.selectedScrollSnap();
        const activeSlide = slides[selected];

        slides.forEach((slide) => {
            slide.classList.remove('is-text-in');
        });

        if (!activeSlide) {
            return;
        }

        revealTimer = window.setTimeout(
            () => {
                void activeSlide.offsetWidth;
                activeSlide.classList.add('is-text-in');
            },
            reduceMotion ? 0 : 200,
        );
    };

    const resetProgress = () => {
        if (!progressBar || reduceMotion) {
            return;
        }

        progressBar.style.transition = 'none';
        progressBar.style.transform = 'scaleX(0)';
        void progressBar.offsetWidth;
        progressBar.style.transition = `transform ${autoplayDelay}ms linear`;
        progressBar.style.transform = 'scaleX(1)';
    };

    const stopAutoplay = () => {
        if (autoplayTimer !== null) {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    };

    const startAutoplay = () => {
        stopAutoplay();
        resetProgress();

        autoplayTimer = window.setInterval(() => {
            if (document.hidden) {
                return;
            }

            embla.scrollNext();
        }, autoplayDelay);
    };

    const onSelect = () => {
        syncThumbs();
        syncActiveSlide();
        revealActiveText();
        resetProgress();
        tweenOpacity(embla);
    };

    prevBtn?.addEventListener('click', () => {
        embla.scrollPrev();
        startAutoplay();
    });

    nextBtn?.addEventListener('click', () => {
        embla.scrollNext();
        startAutoplay();
    });

    thumbButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const index = Number(button.getAttribute('data-hero-thumb'));

            if (Number.isNaN(index)) {
                return;
            }

            embla.scrollTo(index);
            startAutoplay();
        });
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();

            if (progressBar && !reduceMotion) {
                progressBar.style.transition = 'none';
            }

            return;
        }

        startAutoplay();
    });

    setTweenFactor(embla);
    tweenOpacity(embla);

    embla.on('scroll', () => {
        tweenOpacity(embla);
    });
    embla.on('slideFocus', () => {
        tweenOpacity(embla);
    });
    embla.on('select', onSelect);
    embla.on('reInit', () => {
        setTweenFactor(embla);
        onSelect();
        tweenOpacity(embla);
    });

    onSelect();
    startAutoplay();
};

document.addEventListener('DOMContentLoaded', () => {
    initHeroEditorialReveal();
    initHeroScrollCue();
    initHeroBanner();
});
