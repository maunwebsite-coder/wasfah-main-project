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
        this.badgeSelector = '[data-notification-badge]';
        this.badgeAnimationDuration = 2000;
        this.realtimeSubscription = null;
        this.maxRealtimeNotifications = 10;
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
        if (!error && data) {
            const unreadCount = data?.unreadCount ?? 0;
            this.updateBadgeElements(unreadCount);
            this.dispatchUpdateEvent(data);
        }

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

    /**
     * Merge realtime notification payload into cache and UI
     * @param {Object} payload
     */
    handleRealtimeNotification(payload) {
        if (!payload?.notification) {
            return;
        }

        const existing = Array.isArray(this.cachedData?.notifications)
            ? this.cachedData.notifications
            : [];

        const notifications = [payload.notification, ...existing];
        const deduped = [];
        const seen = new Set();

        notifications.forEach(notification => {
            if (!notification || seen.has(notification.id)) {
                return;
            }
            seen.add(notification.id);
            deduped.push(notification);
        });

        const unreadCount = Number(
            payload.unread_count ?? this.cachedData?.unreadCount ?? 0
        );

        const updatedData = {
            ...(this.cachedData || {}),
            notifications: deduped.slice(0, this.maxRealtimeNotifications),
            unreadCount,
            timestamp: payload.timestamp ?? Date.now()
        };

        this.updateCache(updatedData);
        this.updateBadgeElements(unreadCount);
        this.dispatchUpdateEvent(updatedData);
    }

    /**
     * Subscribe to the authenticated user's notification channel
     */
    initializeRealtimeChannel() {
        if (typeof window === 'undefined' || typeof document === 'undefined') {
            return;
        }

        const attach = () => {
            const userId = document.body?.dataset?.userId;
            if (!userId || typeof window.Echo === 'undefined' || this.realtimeSubscription) {
                return;
            }

            this.realtimeSubscription = window.Echo
                .private(`notifications.${userId}`)
                .listen('.notification.created', event => {
                    this.handleRealtimeNotification(event);
                })
                .error(error => {
                    console.error('Realtime notifications error:', error);
                });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', attach, { once: true });
        } else {
            attach();
        }

        document.addEventListener('echo:initialized', () => {
            if (!this.realtimeSubscription) {
                attach();
            }
        });
    }

    /**
     * Update all notification badges in the UI
     * @param {number} count - Number of unread notifications
     */
    updateBadgeElements(count) {
        const badges = document.querySelectorAll(this.badgeSelector);
        badges.forEach(badge => {
            const previousCount = parseInt(badge.dataset.previousCount || badge.textContent || '0', 10) || 0;

            badge.textContent = count;
            badge.dataset.previousCount = count;

            if (count > 0) {
                badge.classList.remove('hidden');
                badge.setAttribute('aria-hidden', 'false');

                if (count > previousCount) {
                    badge.classList.remove('animate-bounce');
                    void badge.offsetWidth; // trigger reflow to restart animation
                    badge.classList.add('animate-bounce');
                    setTimeout(() => {
                        badge.classList.remove('animate-bounce');
                    }, this.badgeAnimationDuration);
                }
            } else {
                badge.classList.add('hidden');
                badge.setAttribute('aria-hidden', 'true');
            }
        });

        // Update body attribute for styling conditions if needed
        const body = document.body;
        if (body) {
            body.dataset.unreadNotifications = String(count);
        }
    }

    /**
     * Broadcast a notification update event for other scripts
     * @param {Object} detail - Notification payload
     */
    dispatchUpdateEvent(detail) {
        if (typeof document === 'undefined' || typeof CustomEvent === 'undefined') {
            return;
        }

        const event = new CustomEvent('notifications:updated', {
            detail: {
                ...detail,
                timestamp: Date.now()
            }
        });

        document.dispatchEvent(event);
    }
}

// Create global instance
window.NotificationManager = new NotificationManager();
window.NotificationManager.initializeRealtimeChannel();

// Legacy compatibility functions
window.loadNotificationsCount = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading notifications count:', error);
            return;
        }

        const count = data?.unreadCount || 0;
        window.NotificationManager.updateBadgeElements(count);
    });
};

window.loadMobileNotificationsCount = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading mobile notifications count:', error);
            return;
        }

        const count = data?.unreadCount || 0;
        window.NotificationManager.updateBadgeElements(count);
    });
};

window.loadNotifications = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading notifications:', error);
            return;
        }

        if (typeof updateNotificationsUI === 'function') {
            updateNotificationsUI(data?.notifications || [], data?.unreadCount || 0);
        }
    });
};

// Optimized notification loading for dropdown
window.loadNotificationsDropdown = function() {
    window.NotificationManager.getNotifications((data, error) => {
        if (error) {
            console.error('Error loading notifications dropdown:', error);
            return;
        }

        if (typeof updateNotificationsUI === 'function') {
            updateNotificationsUI(data?.notifications || [], data?.unreadCount || 0);
        }
    });
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationManager;
}
