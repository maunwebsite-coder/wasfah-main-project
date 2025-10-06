/**
 * Visibility Manager - Frontend JavaScript
 * Manages visibility settings for website sections
 */

class VisibilityManager {
    constructor() {
        this.settings = {};
        this.initialized = false;
        this.init();
    }

    /**
     * Initialize the visibility manager
     */
    async init() {
        try {
            await this.loadSettings();
            this.applyVisibilitySettings();
            this.initialized = true;
            console.log('Visibility Manager initialized successfully');
        } catch (error) {
            console.error('Error initializing Visibility Manager:', error);
        }
    }

    /**
     * Load visibility settings from server
     */
    async loadSettings() {
        try {
            const response = await fetch('/admin/visibility/config', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.settings = data.sections || {};
                this.user = data.user || {};
            } else {
                console.warn('Failed to load visibility settings, using defaults');
                this.settings = this.getDefaultSettings();
            }
        } catch (error) {
            console.error('Error loading visibility settings:', error);
            this.settings = this.getDefaultSettings();
        }
    }

    /**
     * Get default visibility settings
     */
    getDefaultSettings() {
        return {
            header: true,
            navigation: true,
            footer: true,
            sidebar: true,
            search: true,
            recipes: true,
            tools: true,
            workshops: true,
            notifications: true,
            profile: true,
            admin: false
        };
    }

    /**
     * Apply visibility settings to DOM elements
     */
    applyVisibilitySettings() {
        Object.keys(this.settings).forEach(section => {
            const isVisible = this.settings[section];
            this.toggleSection(section, isVisible);
        });
    }

    /**
     * Toggle visibility of a section
     */
    toggleSection(section, isVisible) {
        const elements = this.getSectionElements(section);
        
        elements.forEach(element => {
            if (element) {
                if (isVisible) {
                    element.style.display = '';
                    element.classList.remove('visibility-hidden');
                    element.classList.add('visibility-visible');
                } else {
                    element.style.display = 'none';
                    element.classList.remove('visibility-visible');
                    element.classList.add('visibility-hidden');
                }
            }
        });
    }

    /**
     * Get DOM elements for a section
     */
    getSectionElements(section) {
        const selectors = {
            header: ['header', '.header', '#header', '[data-section="header"]'],
            navigation: ['nav', '.navigation', '.navbar', '#navigation', '[data-section="navigation"]'],
            footer: ['footer', '.footer', '#footer', '[data-section="footer"]'],
            sidebar: ['.sidebar', '#sidebar', '[data-section="sidebar"]'],
            search: ['.search', '#search', '[data-section="search"]'],
            recipes: ['.recipes', '#recipes', '[data-section="recipes"]'],
            tools: ['.tools', '#tools', '[data-section="tools"]'],
            workshops: ['.workshops', '#workshops', '[data-section="workshops"]'],
            notifications: ['.notifications', '#notifications', '[data-section="notifications"]'],
            profile: ['.profile', '#profile', '[data-section="profile"]'],
            admin: ['.admin', '#admin', '[data-section="admin"]']
        };

        const sectionSelectors = selectors[section] || [`[data-section="${section}"]`];
        const elements = [];

        sectionSelectors.forEach(selector => {
            const foundElements = document.querySelectorAll(selector);
            elements.push(...foundElements);
        });

        return elements;
    }

    /**
     * Check if a section is visible
     */
    isSectionVisible(section) {
        return this.settings[section] === true;
    }

    /**
     * Hide a section
     */
    hideSection(section) {
        this.settings[section] = false;
        this.toggleSection(section, false);
    }

    /**
     * Show a section
     */
    showSection(section) {
        this.settings[section] = true;
        this.toggleSection(section, true);
    }

    /**
     * Toggle a section visibility
     */
    toggleSectionVisibility(section) {
        const currentState = this.isSectionVisible(section);
        this.settings[section] = !currentState;
        this.toggleSection(section, !currentState);
        return !currentState;
    }

    /**
     * Update visibility settings
     */
    async updateSettings(newSettings) {
        try {
            const response = await fetch('/admin/visibility/bulk-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    settings: newSettings
                })
            });

            if (response.ok) {
                const data = await response.json();
                this.settings = { ...this.settings, ...newSettings };
                this.applyVisibilitySettings();
                return data;
            } else {
                throw new Error('Failed to update settings');
            }
        } catch (error) {
            console.error('Error updating visibility settings:', error);
            throw error;
        }
    }

    /**
     * Get current visibility settings
     */
    getSettings() {
        return { ...this.settings };
    }

    /**
     * Check if user can see admin sections
     */
    canSeeAdminSections() {
        return this.user?.is_admin === true;
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return this.user?.is_authenticated === true;
    }

    /**
     * Apply user-specific visibility rules
     */
    applyUserRules() {
        // Hide admin sections for non-admin users
        if (!this.canSeeAdminSections()) {
            this.hideSection('admin');
        }

        // Hide user-specific sections for non-authenticated users
        if (!this.isAuthenticated()) {
            this.hideSection('profile');
            this.hideSection('notifications');
        }
    }

    /**
     * Refresh settings from server
     */
    async refresh() {
        await this.loadSettings();
        this.applyUserRules();
        this.applyVisibilitySettings();
    }
}

// Initialize visibility manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.visibilityManager = new VisibilityManager();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VisibilityManager;
}
