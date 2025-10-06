/**
 * Centralized Notification Manager
 * Prevents duplicate API calls and manages notification state globally
 */
class NotificationManager {
    constructor() {
        this.isLoading = false;
        this.lastUpdate = 0;
        this.cacheDuration = 30000; // 30 seconds
        this.cachedData = null;
        this.pendingCallbacks = [];
        this.retryCount = 0;
        this.maxRetries = 3;
    }

    /**
     * Get notifications with caching and deduplication
     * @param {Function} callback - Callback function to handle the data
     * @param {boolean} forceRefresh - Force refresh even if cached
     */
    async getNotifications(callback, forceRefresh = false) {
        // Add callback to pending list
        this.pendingCallbacks.push(callback);

        // If already loading, just wait for the current request
        if (this.isLoading) {
            console.log('Notification request already in progress, queuing callback...');
            return;
        }

        // Check cache first
        const now = Date.now();
        if (!forceRefresh && this.cachedData && (now - this.lastUpdate) < this.cacheDuration) {
            console.log('Using cached notification data...');
            this.executeCallbacks(this.cachedData);
            return;
        }

        // Start loading
        this.isLoading = true;
        this.lastUpdate = now;

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch('/notifications/api', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            // Cache the data
            this.cachedData = data;
            this.retryCount = 0; // Reset retry count on success

            // Execute all pending callbacks
            this.executeCallbacks(data);

        } catch (error) {
            console.error('Error loading notifications:', error);
            
            // Retry logic
            if (this.retryCount < this.maxRetries) {
                this.retryCount++;
                console.log(`Retrying notification request (${this.retryCount}/${this.maxRetries})...`);
                
                // Exponential backoff
                const delay = Math.pow(2, this.retryCount) * 1000;
                setTimeout(() => {
                    this.isLoading = false;
                    this.getNotifications(callback, true);
                }, delay);
                return;
            }

            // If we have cached data, use it
            if (this.cachedData) {
                console.log('Using cached data due to error...');
                this.executeCallbacks(this.cachedData);
            } else {
                // Execute callbacks with error
                this.executeCallbacks(null, error);
            }
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Execute all pending callbacks with the data
     * @param {Object} data - Notification data
     * @param {Error} error - Error if any
     */
    executeCallbacks(data, error = null) {
        const callbacks = [...this.pendingCallbacks];
        this.pendingCallbacks = [];

        callbacks.forEach(callback => {
            try {
                callback(data, error);
            } catch (callbackError) {
                console.error('Error in notification callback:', callbackError);
            }
        });
    }

    /**
     * Clear cache and force refresh
     */
    clearCache() {
        this.cachedData = null;
        this.lastUpdate = 0;
    }

    /**
     * Get cached data without making a request
     * @returns {Object|null} Cached data or null
     */
    getCachedData() {
        const now = Date.now();
        if (this.cachedData && (now - this.lastUpdate) < this.cacheDuration) {
            return this.cachedData;
        }
        return null;
    }

    /**
     * Update cache with new data
     * @param {Object} data - New notification data
     */
    updateCache(data) {
        this.cachedData = data;
        this.lastUpdate = Date.now();
    }
}

// Create global instance
window.NotificationManager = new NotificationManager();

// Legacy compatibility functions
window.loadNotificationsCount = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading notifications count:', error);
            return;
        }

        const notificationsCountEl = document.getElementById('notifications-count-nav');
        if (notificationsCountEl) {
            const oldCount = parseInt(notificationsCountEl.textContent) || 0;
            const count = data?.unreadCount || 0;
            notificationsCountEl.textContent = count;
            
            if (count > 0) {
                notificationsCountEl.classList.remove('hidden');
                
                // Add visual effect for new notifications
                if (count > oldCount) {
                    notificationsCountEl.classList.add('animate-bounce');
                    setTimeout(() => {
                        notificationsCountEl.classList.remove('animate-bounce');
                    }, 2000);
                }
            } else {
                notificationsCountEl.classList.add('hidden');
            }
        }
    });
};

window.loadMobileNotificationsCount = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading mobile notifications count:', error);
            return;
        }

        const mobileNotificationsCountEl = document.getElementById('mobile-notifications-count');
        const mobileMenuNotificationsCountEl = document.getElementById('notifications-count-mobile');
        
        const count = data?.unreadCount || 0;
        
        // Update header notification count
        if (mobileNotificationsCountEl) {
            const oldCount = parseInt(mobileNotificationsCountEl.textContent) || 0;
            mobileNotificationsCountEl.textContent = count;
            if (count > 0) {
                mobileNotificationsCountEl.classList.remove('hidden');
                
                if (count > oldCount) {
                    mobileNotificationsCountEl.classList.add('animate-bounce');
                    setTimeout(() => {
                        mobileNotificationsCountEl.classList.remove('animate-bounce');
                    }, 2000);
                }
            } else {
                mobileNotificationsCountEl.classList.add('hidden');
            }
        }
        
        // Update mobile menu notification count
        if (mobileMenuNotificationsCountEl) {
            const oldCount = parseInt(mobileMenuNotificationsCountEl.textContent) || 0;
            mobileMenuNotificationsCountEl.textContent = count;
            if (count > 0) {
                mobileMenuNotificationsCountEl.classList.remove('hidden');
                
                if (count > oldCount) {
                    mobileMenuNotificationsCountEl.classList.add('animate-bounce');
                    setTimeout(() => {
                        mobileMenuNotificationsCountEl.classList.remove('animate-bounce');
                    }, 2000);
                }
            } else {
                mobileMenuNotificationsCountEl.classList.add('hidden');
            }
        }
    });
};

window.loadNotifications = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading notifications:', error);
            return;
        }

        updateNotificationsUI(data?.notifications || [], data?.unreadCount || 0);
    });
};

// Optimized notification loading for dropdown
window.loadNotificationsDropdown = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading notifications dropdown:', error);
            return;
        }

        updateNotificationsUI(data?.notifications || [], data?.unreadCount || 0);
    });
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationManager;
}
