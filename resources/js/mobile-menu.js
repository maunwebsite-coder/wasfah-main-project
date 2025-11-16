/**
 * Mobile Menu Handler
 * Simple and direct mobile menu functionality
 */

console.log('Mobile Menu JS loaded');

let isMobileMenuInitialized = false;
let outsideClickHandler = null;
let escapeKeyHandler = null;

function liftMobileMenuLayer(mobileMenu) {
    if (!mobileMenu || mobileMenu.dataset.layerLifted === 'true') {
        return;
    }

    if (document.body && mobileMenu.parentElement !== document.body) {
        document.body.appendChild(mobileMenu);
    }

    mobileMenu.dataset.layerLifted = 'true';
}

function openMobileMenu(mobileMenu, menuIcon, closeIcon) {
    mobileMenu.classList.remove('hidden');
    mobileMenu.classList.add('show');
    mobileMenu.style.display = 'block';
    mobileMenu.style.visibility = 'visible';
    mobileMenu.style.opacity = '1';
    mobileMenu.style.position = 'fixed';
    mobileMenu.style.top = '0';
    mobileMenu.style.right = '0';
    mobileMenu.style.bottom = '0';
    mobileMenu.style.left = '0';
    mobileMenu.style.zIndex = '10000';
    menuIcon?.classList.add('hidden');
    closeIcon?.classList.remove('hidden');
    document.body?.classList.add('mobile-menu-open');
    document
        .querySelector('[data-navbar-layer]')
        ?.classList.add('mobile-menu-layer-active');
}

function closeMobileMenu(mobileMenu, menuIcon, closeIcon) {
    mobileMenu.classList.add('hidden');
    mobileMenu.classList.remove('show');
    mobileMenu.style.display = 'none';
    mobileMenu.style.visibility = 'hidden';
    mobileMenu.style.opacity = '0';
    mobileMenu.style.removeProperty('position');
    mobileMenu.style.removeProperty('top');
    mobileMenu.style.removeProperty('right');
    mobileMenu.style.removeProperty('bottom');
    mobileMenu.style.removeProperty('left');
    mobileMenu.style.removeProperty('z-index');
    menuIcon?.classList.remove('hidden');
    closeIcon?.classList.add('hidden');
    document.body?.classList.remove('mobile-menu-open');
    document
        .querySelector('[data-navbar-layer]')
        ?.classList.remove('mobile-menu-layer-active');
}

// Mobile Menu Setup Function
function setupMobileMenu(force = false) {
    console.log('Setting up mobile menu...');

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    console.log('Elements found:', {
        mobileMenuBtn: !!mobileMenuBtn,
        mobileMenu: !!mobileMenu,
        menuIcon: !!menuIcon,
        closeIcon: !!closeIcon
    });

    if (mobileMenuBtn && mobileMenu && menuIcon && closeIcon) {
        liftMobileMenuLayer(mobileMenu);
        console.log('All elements found! Setting up click handler...');

        if (isMobileMenuInitialized && !force) {
            console.log('Mobile menu already initialized - refreshing handlers');
        }

        // Clear any existing handlers
        mobileMenuBtn.onclick = null;

        // Add click handler
        mobileMenuBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile menu button clicked!');

            if (mobileMenu.classList.contains('hidden')) {
                console.log('Opening mobile menu...');
                openMobileMenu(mobileMenu, menuIcon, closeIcon);
                console.log('Mobile menu opened successfully');
            } else {
                console.log('Closing mobile menu...');
                closeMobileMenu(mobileMenu, menuIcon, closeIcon);
                console.log('Mobile menu closed successfully');
            }
        };

        // Close menu when clicking outside
        if (outsideClickHandler) {
            document.removeEventListener('click', outsideClickHandler);
        }

        outsideClickHandler = function(e) {
            if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                if (mobileMenu.classList.contains('show')) {
                    console.log('Closing menu - clicked outside');
                    closeMobileMenu(mobileMenu, menuIcon, closeIcon);
                }
            }
        };

        document.addEventListener('click', outsideClickHandler);

        if (escapeKeyHandler) {
            document.removeEventListener('keydown', escapeKeyHandler);
        }

        escapeKeyHandler = function(e) {
            if (e.key === 'Escape' && mobileMenu.classList.contains('show')) {
                console.log('Closing menu - escape key');
                closeMobileMenu(mobileMenu, menuIcon, closeIcon);
            }
        };

        document.addEventListener('keydown', escapeKeyHandler);

        // Close menu buttons inside panel
        mobileMenu
            .querySelectorAll('[data-close-mobile-menu]')
            .forEach((button) => {
                button.onclick = (event) => {
                    event.preventDefault();
                    closeMobileMenu(mobileMenu, menuIcon, closeIcon);
                };
            });

        console.log('Mobile menu setup completed successfully!');
        isMobileMenuInitialized = true;
        return true;
    } else {
        console.error('Mobile menu elements not found!', {
            mobileMenuBtn: mobileMenuBtn,
            mobileMenu: mobileMenu,
            menuIcon: menuIcon,
            closeIcon: closeIcon
        });
        return false;
    }
}

if (typeof window !== 'undefined') {
    window.setupMobileMenu = setupMobileMenu;

    // Test function for debugging
    window.testMobileMenu = function() {
        console.log('Testing mobile menu...');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        if (mobileMenu && menuIcon && closeIcon) {
            if (mobileMenu.classList.contains('hidden')) {
                openMobileMenu(mobileMenu, menuIcon, closeIcon);
                console.log('Menu opened via test function');
            } else {
                closeMobileMenu(mobileMenu, menuIcon, closeIcon);
                console.log('Menu closed via test function');
            }
        } else {
            console.error('Menu elements not found for test');
        }
    };

    // Force setup function
    window.forceSetupMobileMenu = function() {
        console.log('Force setting up mobile menu...');
        return setupMobileMenu(true);
    };
}

function initMobileMenuWhenReady() {
    if (typeof document === 'undefined') {
        return;
    }

    const init = () => {
        console.log('Initializing mobile menu after DOM is ready...');
        setupMobileMenu();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}

initMobileMenuWhenReady();

console.log('Mobile Menu JS setup complete');
