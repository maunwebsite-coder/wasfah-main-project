/**
 * Mobile Menu Handler
 * Simple and direct mobile menu functionality
 */

console.log('Mobile Menu JS loaded');

let isMobileMenuInitialized = false;
let outsideClickHandler = null;

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
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('show');
                mobileMenu.style.display = 'block';
                mobileMenu.style.visibility = 'visible';
                mobileMenu.style.opacity = '1';
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
                console.log('Mobile menu opened successfully');
            } else {
                console.log('Closing mobile menu...');
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('show');
                mobileMenu.style.display = 'none';
                mobileMenu.style.visibility = 'hidden';
                mobileMenu.style.opacity = '0';
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
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
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('show');
                    mobileMenu.style.display = 'none';
                    mobileMenu.style.visibility = 'hidden';
                    mobileMenu.style.opacity = '0';
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            }
        };

        document.addEventListener('click', outsideClickHandler);

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
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('show');
                mobileMenu.style.display = 'block';
                mobileMenu.style.visibility = 'visible';
                mobileMenu.style.opacity = '1';
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
                console.log('Menu opened via test function');
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('show');
                mobileMenu.style.display = 'none';
                mobileMenu.style.visibility = 'hidden';
                mobileMenu.style.opacity = '0';
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
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
