/**
 * Utility Functions
 * Shared helper functions used across the application
 */

(function () {
    'use strict';

    // ========================================================================
    // DEBOUNCE FUNCTION
    // ========================================================================

    /**
     * Debounce function to limit how often a function can fire
     * @param {Function} func - The function to debounce
     * @param {number} wait - The delay in milliseconds
     * @returns {Function} - The debounced function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ========================================================================
    // THROTTLE FUNCTION
    // ========================================================================

    /**
     * Throttle function to ensure function fires at most once per interval
     * @param {Function} func - The function to throttle
     * @param {number} limit - The time limit in milliseconds
     * @returns {Function} - The throttled function
     */
    function throttle(func, limit) {
        let inThrottle;
        return function (...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // ========================================================================
    // REQUEST ANIMATION FRAME THROTTLE
    // ========================================================================

    /**
     * Throttle using requestAnimationFrame for smooth animations
     * @param {Function} func - The function to throttle
     * @returns {Function} - The RAF throttled function
     */
    function rafThrottle(func) {
        let rafId = null;
        return function (...args) {
            if (rafId === null) {
                rafId = requestAnimationFrame(() => {
                    func.apply(this, args);
                    rafId = null;
                });
            }
        };
    }

    // ========================================================================
    // DOM READY
    // ========================================================================

    /**
     * Execute function when DOM is ready
     * @param {Function} fn - The function to execute
     */
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    // ========================================================================
    // ELEMENT VISIBILITY CHECK
    // ========================================================================

    /**
     * Check if element is in viewport
     * @param {HTMLElement} element - The element to check
     * @returns {boolean} - True if element is visible
     */
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // ========================================================================
    // SMOOTH SCROLL TO ELEMENT
    // ========================================================================

    /**
     * Smooth scroll to an element
     * @param {string|HTMLElement} target - Element or selector to scroll to
     * @param {number} offset - Optional offset from top
     */
    function scrollToElement(target, offset = 0) {
        const element = typeof target === 'string' ? document.querySelector(target) : target;
        if (!element) return;

        const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }

    // ========================================================================
    // GET SCROLL POSITION
    // ========================================================================

    /**
     * Get current scroll position
     * @returns {number} - Current scroll position
     */
    function getScrollPosition() {
        return window.pageYOffset || document.documentElement.scrollTop;
    }

    // ========================================================================
    // LOCAL STORAGE HELPERS
    // ========================================================================

    /**
     * Safely get item from localStorage
     * @param {string} key - The key to retrieve
     * @param {*} defaultValue - Default value if key doesn't exist
     * @returns {*} - The stored value or default
     */
    function getLocalStorage(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            return defaultValue;
        }
    }

    /**
     * Safely set item in localStorage
     * @param {string} key - The key to set
     * @param {*} value - The value to store
     * @returns {boolean} - True if successful
     */
    function setLocalStorage(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (error) {
            return false;
        }
    }

    // ========================================================================
    // EXPORT TO GLOBAL SCOPE
    // ========================================================================

    window.Utils = {
        debounce,
        throttle,
        rafThrottle,
        ready,
        isInViewport,
        scrollToElement,
        getScrollPosition,
        getLocalStorage,
        setLocalStorage
    };

})();
