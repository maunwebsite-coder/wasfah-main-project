const disallowedParents = new Set(['SCRIPT', 'STYLE', 'NOSCRIPT', 'CODE', 'PRE']);

function collectTextNodes() {
    const nodes = [];

    if (!document.body) {
        return nodes;
    }

    const walker = document.createTreeWalker(
        document.body,
        NodeFilter.SHOW_TEXT,
        {
            acceptNode(node) {
                if (!node?.textContent) {
                    return NodeFilter.FILTER_REJECT;
                }

                const parent = node.parentElement;
                if (!parent || disallowedParents.has(parent.tagName)) {
                    return NodeFilter.FILTER_REJECT;
                }

                const trimmed = node.textContent.trim();
                if (!trimmed) {
                    return NodeFilter.FILTER_REJECT;
                }

                if (parent.hasAttribute('data-no-i18n')) {
                    return NodeFilter.FILTER_REJECT;
                }

                return NodeFilter.FILTER_ACCEPT;
            },
        },
        false,
    );

    let currentNode;
    // eslint-disable-next-line no-cond-assign
    while ((currentNode = walker.nextNode())) {
        nodes.push({
            node: currentNode,
            original: currentNode.textContent,
            trimmed: currentNode.textContent.trim(),
        });
    }

    return nodes;
}

function createLocaleApplier(nodes, translations = {}) {
    return function applyLocale(locale) {
        if (!locale || locale === 'ar') {
            nodes.forEach(({ node, original }) => {
                if (node.textContent !== original) {
                    node.textContent = original;
                }
            });
            return;
        }

        const dictionary = translations[locale];
        if (!dictionary) {
            return;
        }

        nodes.forEach(({ node, original, trimmed }) => {
            const replacement = dictionary[trimmed];

            if (replacement) {
                node.textContent = replacement;
            } else if (locale === 'ar' && node.textContent !== original) {
                node.textContent = original;
            }
        });
    };
}

function bootstrapContentLocalization() {
    const nodes = collectTextNodes();
    const translations = window.__CONTENT_TRANSLATIONS || {};
    const locale = window.__APP_LOCALE || document.documentElement.lang || 'ar';

    if (!nodes.length || !translations || Object.keys(translations).length === 0) {
        return;
    }

    const applyLocale = createLocaleApplier(nodes, translations);
    applyLocale(locale);

    window.applyContentLocale = applyLocale;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapContentLocalization, { once: true });
} else {
    bootstrapContentLocalization();
}
