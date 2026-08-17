const reduceMotionQuery = () => window.matchMedia('(prefers-reduced-motion: reduce)');

const wrapQuoteWords = (node, { startLag, wordStep }, closeMark) => {
    const raw = node.textContent?.replace(/\s+/g, ' ').trim() ?? '';

    if (raw === '') {
        return 0;
    }

    const words = raw.split(' ');

    node.setAttribute('aria-label', raw);
    node.textContent = '';

    let lastWordWrap = null;

    words.forEach((word, index) => {
        const wordWrap = document.createElement('span');
        wordWrap.className = 'thought-reveal-word';
        wordWrap.style.setProperty('--word-delay', `${startLag + index * wordStep}ms`);

        const wordInner = document.createElement('span');
        wordInner.className = 'thought-reveal-word-inner';
        wordInner.textContent = word;

        wordWrap.appendChild(wordInner);
        node.appendChild(wordWrap);

        if (index < words.length - 1) {
            node.appendChild(document.createTextNode(' '));
        } else {
            lastWordWrap = wordWrap;
        }
    });

    // Group the last word together with the closing quote mark in a
    // non-breaking span. The word's own reveal wrapper (with its
    // overflow:hidden clip and rise animation) is left untouched — only the
    // line-break opportunity between the word and the mark is removed, so
    // the mark can never be pushed onto a line of its own.
    if (closeMark && lastWordWrap) {
        const lastGroup = document.createElement('span');
        lastGroup.className = 'thought-reveal-word-group';
        node.insertBefore(lastGroup, lastWordWrap);
        lastGroup.appendChild(lastWordWrap);
        lastGroup.appendChild(closeMark);
    }

    return words.length;
};

export const initThoughtQuote = () => {
    const root = document.querySelector('[data-thought-quote]');

    if (!root || root.dataset.thoughtReady === '1') {
        return;
    }

    root.dataset.thoughtReady = '1';

    const text = root.querySelector('[data-thought-reveal]');
    const closeMark = root.querySelector('.thought-quote-mark--close');
    const reduceMotion = reduceMotionQuery().matches;
    const wordStep = reduceMotion ? 40 : 85;
    const startLag = reduceMotion ? 120 : 280;

    if (text) {
        wrapQuoteWords(text, { startLag, wordStep }, closeMark);
    }

    const reveal = () => {
        root.classList.add('is-revealed');
    };

    if (!('IntersectionObserver' in window)) {
        reveal();
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    reveal();
                    observer.disconnect();
                }
            });
        },
        {
            root: null,
            rootMargin: '0px 0px -12% 0px',
            threshold: 0.28,
        },
    );

    observer.observe(root);
};
