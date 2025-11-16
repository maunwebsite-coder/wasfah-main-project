const hasWindow = typeof window !== 'undefined';
const hasDocument = typeof document !== 'undefined';
const supportsHistory =
    hasWindow &&
    typeof window.history !== 'undefined' &&
    typeof window.history.pushState === 'function';
const mobileQuery = hasWindow ? window.matchMedia('(max-width: 768px)') : null;
const SPA_NAVIGATION_ENABLED = false;

const isMobileViewport = () => (mobileQuery ? mobileQuery.matches : true);

const scrollToTop = () => {
    if (!hasWindow) {
        return;
    }
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    window.scrollTo({
        top: 0,
        behavior: prefersReducedMotion ? 'auto' : 'smooth',
    });
};

const executeScripts = (container) => {
    container.querySelectorAll('script').forEach((script) => {
        const scriptType = script.type?.trim();
        const shouldExecute =
            !scriptType || scriptType === 'text/javascript' || scriptType === 'module';

        if (!shouldExecute) {
            return;
        }

        const clone = document.createElement('script');
        Array.from(script.attributes).forEach(({ name, value }) => clone.setAttribute(name, value));
        clone.textContent = script.textContent;
        script.replaceWith(clone);
    });
};

const findHeadMarker = (root, label) => {
    return Array.from(root.childNodes).find(
        (node) => node.nodeType === Node.COMMENT_NODE && node.nodeValue.trim() === label,
    );
};

const syncHeadSection = (sectionLabel, nextDocument) => {
    const startLabel = `${sectionLabel}:START`;
    const endLabel = `${sectionLabel}:END`;

    const currentStart = findHeadMarker(document.head, startLabel);
    const currentEnd = findHeadMarker(document.head, endLabel);
    const nextStart = findHeadMarker(nextDocument.head, startLabel);
    const nextEnd = findHeadMarker(nextDocument.head, endLabel);

    if (!currentStart || !currentEnd || !nextStart || !nextEnd) {
        return;
    }

    let node = currentStart.nextSibling;
    while (node && node !== currentEnd) {
        const toRemove = node;
        node = node.nextSibling;
        document.head.removeChild(toRemove);
    }

    let nextNode = nextStart.nextSibling;
    while (nextNode && nextNode !== nextEnd) {
        const clone = nextNode.cloneNode(true);
        document.head.insertBefore(clone, currentEnd);
        nextNode = nextNode.nextSibling;
    }
};

const injectInlinePageStyles = (root) => {
    if (!root) {
        return;
    }

    const containers = root.querySelectorAll('[data-inline-style]');
    if (!containers.length) {
        return;
    }

    const headEndMarker = findHeadMarker(document.head, 'PAGE_STYLES:END');

    containers.forEach((container) => {
        const styleIdentifier = container.dataset.inlineStyle || null;
        const tagName = container.tagName ? container.tagName.toLowerCase() : '';
        const isTemplate = tagName === 'template';
        const sourceRoot = isTemplate
            ? container.content
                ? container.content.cloneNode(true)
                : null
            : container;

        if (!sourceRoot) {
            container.remove();
            return;
        }

        sourceRoot.querySelectorAll('style').forEach((styleEl) => {
            const pageStyleKey = styleEl.dataset.pageStyle || styleIdentifier;
            if (pageStyleKey) {
                document.head
                    .querySelectorAll(`[data-page-style="${pageStyleKey}"]`)
                    .forEach((existing) => existing.remove());
                styleEl.dataset.pageStyle = pageStyleKey;
            }

            const clone = styleEl.cloneNode(true);
            if (headEndMarker) {
                document.head.insertBefore(clone, headEndMarker);
            } else {
                document.head.appendChild(clone);
            }
        });

        container.remove();
    });
};

const initMobileTabBar = () => {
    if (!supportsHistory || !hasDocument) {
        return;
    }

    const nav = document.querySelector('[data-mobile-tab-bar]');
    const main = document.querySelector('main');
    const scriptStack = document.querySelector('[data-page-scripts]');

    if (!nav || !main) {
        return;
    }

    const links = Array.from(nav.querySelectorAll('a[href]'));
    if (!links.length) {
        return;
    }

    let isNavigating = false;

    const setActiveLink = (url) => {
        const normalized = new URL(url, window.location.origin);
        links.forEach((link) => {
            const linkUrl = new URL(link.href, window.location.origin);
            const isActive =
                linkUrl.pathname === normalized.pathname && linkUrl.search === normalized.search;
            link.classList.toggle('is-active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const setLoadingState = (state) => {
        nav.classList.toggle('mobile-tab-bar--loading', state);
        nav.setAttribute('aria-busy', state ? 'true' : 'false');
    };

    const rehydrateModules = () => {
        if (typeof window.__bootstrapLazyModules === 'function') {
            window.__bootstrapLazyModules();
        }
    };

    const loadPage = async (url, { pushState = true } = {}) => {
        if (!SPA_NAVIGATION_ENABLED) {
            window.location.href = url;
            return;
        }

    if (!isMobileViewport()) {
        window.location.href = url;
        return;
    }

        if (isNavigating) {
            return;
        }

        isNavigating = true;
        setLoadingState(true);

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Mobile-Tab-Bar': '1',
                    Accept: 'text/html,application/xhtml+xml',
                },
                credentials: 'same-origin',
            });

            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            if (!response.ok) {
                throw new Error(`Failed to fetch ${url}: ${response.status}`);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const newDocument = parser.parseFromString(html, 'text/html');
            const nextMain = newDocument.querySelector('main');
            const nextScripts = newDocument.querySelector('[data-page-scripts]');

            if (!nextMain) {
                throw new Error('Response did not include a <main> element.');
            }

            syncHeadSection('PAGE_STYLES', newDocument);

            main.innerHTML = nextMain.innerHTML;
            executeScripts(main);
            injectInlinePageStyles(main);

            if (scriptStack && nextScripts) {
                scriptStack.innerHTML = nextScripts.innerHTML;
                executeScripts(scriptStack);
            }

            document.title = newDocument.title || document.title;

            if (pushState) {
                window.history.pushState({}, '', url);
            }

            setActiveLink(url);
            scrollToTop();
            rehydrateModules();
        } catch (error) {
            console.error('[mobile-tab-bar] SPA navigation failed, reloading page instead.', error);
            window.location.href = url;
        } finally {
            setLoadingState(false);
            isNavigating = false;
        }
    };

    const handleClick = (event) => {
        const link = event.currentTarget;
        if (!link || event.defaultPrevented) {
            return;
        }

        if (!isMobileViewport()) {
            return;
        }

        if (
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey ||
            link.target === '_blank'
        ) {
            return;
        }

        const destination = new URL(link.href, window.location.origin);
        const current = new URL(window.location.href);

        if (destination.origin !== current.origin) {
            return;
        }

        event.preventDefault();

        if (!SPA_NAVIGATION_ENABLED) {
            if (
                destination.pathname === current.pathname &&
                destination.search === current.search
            ) {
                window.location.reload();
            } else {
                window.location.href = destination.href;
            }
            return;
        }

        if (destination.pathname === current.pathname && destination.search === current.search) {
            setActiveLink(destination.href);
            scrollToTop();
            return;
        }

        loadPage(destination.href);
    };

    setActiveLink(window.location.href);

    if (!SPA_NAVIGATION_ENABLED) {
        return;
    }

    links.forEach((link) => link.addEventListener('click', handleClick));

    if (SPA_NAVIGATION_ENABLED) {
        window.addEventListener('popstate', () => {
            if (!isMobileViewport()) {
                return;
            }
            loadPage(window.location.href, { pushState: false });
        });
    }
};

if (hasDocument) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileTabBar, { once: true });
    } else {
        initMobileTabBar();
    }
}
