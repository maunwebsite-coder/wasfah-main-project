/**
 * Mobile Menu Handler
 * Simple and direct mobile menu functionality
 */

console.log('Mobile Menu JS loaded');

// Mobile Menu Setup Function
function setupMobileMenu() {
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
        document.addEventListener('click', function(e) {
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
        });
        
        console.log('Mobile menu setup completed successfully!');
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
    return setupMobileMenu();
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing mobile menu...');
    setupMobileMenu();
});

console.log('Mobile Menu JS setup complete');

