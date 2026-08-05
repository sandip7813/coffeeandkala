const reduceMotionQuery = () => window.matchMedia('(prefers-reduced-motion: reduce)');

const wrapQuoteWords = (node, { startLag, wordStep }) => {
    const raw = node.textContent?.replace(/\s+/g, ' ').trim() ?? '';

    if (raw === '') {
        return 0;
    }

    const words = raw.split(' ');

    node.setAttribute('aria-label', raw);
    node.textContent = '';

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
        }
    });

    return words.length;
};

export const initThoughtQuote = () => {
    const root = document.querySelector('[data-thought-quote]');

    if (!root || root.dataset.thoughtReady === '1') {
        return;
    }

    root.dataset.thoughtReady = '1';

    const text = root.querySelector('[data-thought-reveal]');
    const reduceMotion = reduceMotionQuery().matches;
    const wordStep = reduceMotion ? 40 : 85;
    const startLag = reduceMotion ? 120 : 280;

    if (text) {
        wrapQuoteWords(text, { startLag, wordStep });
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
