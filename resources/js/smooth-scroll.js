let scrollAnimationFrame = null;

function cancelScrollAnimation() {
    if (scrollAnimationFrame !== null) {
        cancelAnimationFrame(scrollAnimationFrame);
        scrollAnimationFrame = null;
    }
}

function scrollingElement() {
    return document.scrollingElement || document.documentElement;
}

function getScrollTop() {
    return scrollingElement().scrollTop || window.scrollY || window.pageYOffset || 0;
}

function setScrollTop(value) {
    const next = Math.max(0, value);

    scrollingElement().scrollTop = next;
    document.documentElement.scrollTop = next;
    document.body.scrollTop = next;
    window.scrollTo(0, next);
}

function easeInOutCubic(t) {
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

function animateWindowScroll(targetY, durationMs) {
    cancelScrollAnimation();

    const startY = getScrollTop();
    const distance = targetY - startY;

    if (Math.abs(distance) < 1) {
        return;
    }

    const startTime = performance.now();

    function step(now) {
        const progress = Math.min(1, (now - startTime) / durationMs);
        setScrollTop(startY + distance * easeInOutCubic(progress));

        if (progress < 1) {
            scrollAnimationFrame = requestAnimationFrame(step);
            return;
        }

        setScrollTop(targetY);
        scrollAnimationFrame = null;
    }

    scrollAnimationFrame = requestAnimationFrame(step);
}

/**
 * Slow editorial slide to a section. Always animates — intentional motion for the left nav.
 */
export function smoothScrollTo(target, offset = null) {
    if (!target) {
        return;
    }

    const header = document.querySelector('.site-header');
    const headerOffset = offset ?? (header ? header.offsetHeight : 0);
    const targetY = Math.max(
        0,
        target.getBoundingClientRect().top + getScrollTop() - headerOffset,
    );
    const distance = Math.abs(targetY - getScrollTop());
    const durationMs = Math.min(2800, Math.max(1600, distance * 0.9));

    animateWindowScroll(targetY, durationMs);
}
