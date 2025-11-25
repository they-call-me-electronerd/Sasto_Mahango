/**
 * Theme Toggle & Dark Mode Management
 * Handles light/dark theme switching with localStorage persistence
 */

(function () {
    'use strict';

    // ========================================================================
    // CONFIGURATION
    // ========================================================================

    const THEME_KEY = 'mulyasuchi-theme';
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';
    const THEME_AUTO = 'auto';

    // ========================================================================
    // THEME MANAGEMENT
    // ========================================================================

    /**
     * Get the current theme from localStorage or system preference
     */
    function getCurrentTheme() {
        const savedTheme = localStorage.getItem(THEME_KEY);

        if (savedTheme) {
            return savedTheme;
        }

        // Check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return THEME_DARK;
        }

        return THEME_LIGHT;
    }

    /**
     * Apply theme to document
     */
    function applyTheme(theme) {
        const html = document.documentElement;

        // Remove existing theme
        html.removeAttribute('data-theme');

        // Apply new theme
        if (theme === THEME_DARK) {
            html.setAttribute('data-theme', 'dark');
        } else {
            html.setAttribute('data-theme', 'light');
        }

        // Update meta theme-color for mobile browsers
        updateMetaThemeColor(theme);

        // Save to localStorage
        localStorage.setItem(THEME_KEY, theme);

        // Dispatch custom event for other scripts
        window.dispatchEvent(new CustomEvent('themechange', {
            detail: { theme }
        }));
    }

    /**
     * Update meta theme-color for mobile browsers
     */
    function updateMetaThemeColor(theme) {
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');

        if (!metaThemeColor) {
            metaThemeColor = document.createElement('meta');
            metaThemeColor.name = 'theme-color';
            document.head.appendChild(metaThemeColor);
        }

        metaThemeColor.content = theme === THEME_DARK ? '#111827' : '#ffffff';
    }

    /**
     * Toggle between light and dark theme
     */
    function toggleTheme() {
        const currentTheme = getCurrentTheme();
        const newTheme = currentTheme === THEME_DARK ? THEME_LIGHT : THEME_DARK;
        applyTheme(newTheme);
    }

    /**
     * Initialize theme on page load (must run immediately to prevent flash)
     */
    function initializeTheme() {
        const theme = getCurrentTheme();
        applyTheme(theme);
    }

    // ========================================================================
    // THEME TOGGLE BUTTON
    // ========================================================================

    /**
     * Setup theme toggle button event listeners
     */
    function setupThemeToggle() {
        const themeToggle = document.getElementById('themeToggle');

        if (!themeToggle) {
            return;
        }

        // Add click event listener
        themeToggle.addEventListener('click', function (e) {
            e.preventDefault();
            toggleTheme();

            // Add bounce animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });

        // Add keyboard support
        themeToggle.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleTheme();
            }
        });

        // Update ARIA label
        updateToggleAriaLabel(themeToggle);
    }

    /**
     * Update theme toggle button ARIA label
     */
    function updateToggleAriaLabel(button) {
        if (!button) return;

        const currentTheme = getCurrentTheme();
        const label = currentTheme === THEME_DARK
            ? 'Switch to light mode'
            : 'Switch to dark mode';

        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
    }

    // ========================================================================
    // SYSTEM THEME PREFERENCE LISTENER
    // ========================================================================

    /**
     * Listen for system theme preference changes
     */
    function watchSystemTheme() {
        if (!window.matchMedia) return;

        const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

        // Modern browsers
        if (darkModeQuery.addEventListener) {
            darkModeQuery.addEventListener('change', function (e) {
                // Only auto-switch if user hasn't manually set a preference
                if (!localStorage.getItem(THEME_KEY)) {
                    applyTheme(e.matches ? THEME_DARK : THEME_LIGHT);
                }
            });
        }
        // Older browsers
        else if (darkModeQuery.addListener) {
            darkModeQuery.addListener(function (e) {
                if (!localStorage.getItem(THEME_KEY)) {
                    applyTheme(e.matches ? THEME_DARK : THEME_LIGHT);
                }
            });
        }
    }

    // ========================================================================
    // THEME TRANSITION ANIMATION
    // ========================================================================

    /**
     * Add smooth transition animation when switching themes
     */
    function addThemeTransition() {
        document.documentElement.classList.add('theme-transitioning');

        setTimeout(() => {
            document.documentElement.classList.remove('theme-transitioning');
        }, 300);
    }

    /**
     * Listen for theme changes and add transition
     */
    function setupThemeTransitions() {
        window.addEventListener('themechange', function () {
            addThemeTransition();

            // Update toggle button aria label
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                updateToggleAriaLabel(themeToggle);
            }
        });
    }

    // ========================================================================
    // THEME PERSISTENCE & SYNC
    // ========================================================================

    /**
     * Sync theme across browser tabs/windows
     */
    function syncThemeAcrossTabs() {
        window.addEventListener('storage', function (e) {
            if (e.key === THEME_KEY && e.newValue) {
                applyTheme(e.newValue);
            }
        });
    }

    // ========================================================================
    // UTILITY FUNCTIONS
    // ========================================================================

    /**
     * Get current theme value
     */
    function getTheme() {
        return getCurrentTheme();
    }

    /**
     * Set theme programmatically
     */
    function setTheme(theme) {
        if (theme !== THEME_LIGHT && theme !== THEME_DARK) {
            console.error('Invalid theme:', theme);
            return;
        }
        applyTheme(theme);
    }

    /**
     * Clear theme preference (use system default)
     */
    function clearThemePreference() {
        localStorage.removeItem(THEME_KEY);
        initializeTheme();
    }

    // ========================================================================
    // ACCESSIBILITY HELPERS
    // ========================================================================

    /**
     * Announce theme change to screen readers
     */
    function announceThemeChange(theme) {
        const announcement = document.createElement('div');
        announcement.setAttribute('role', 'status');
        announcement.setAttribute('aria-live', 'polite');
        announcement.className = 'sr-only';
        announcement.textContent = `Theme switched to ${theme} mode`;

        document.body.appendChild(announcement);

        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    /**
     * Add screen reader only class
     */
    function addScreenReaderStyles() {
        if (document.querySelector('.sr-only')) return;

        const style = document.createElement('style');
        style.textContent = `
            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border-width: 0;
            }
        `;
        document.head.appendChild(style);
    }

    // ========================================================================
    // INITIALIZATION
    // ========================================================================

    /**
     * Initialize all theme functionality
     */
    function init() {
        // Apply theme immediately (before DOMContentLoaded to prevent flash)
        initializeTheme();

        // Wait for DOM to be ready for interactive features
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initInteractive);
        } else {
            initInteractive();
        }
    }

    /**
     * Initialize interactive features after DOM is ready
     */
    function initInteractive() {
        setupThemeToggle();
        watchSystemTheme();
        setupThemeTransitions();
        syncThemeAcrossTabs();
        addScreenReaderStyles();

        console.log('🎨 Theme system initialized');
    }

    // ========================================================================
    // PUBLIC API
    // ========================================================================

    // Expose public API
    window.ThemeManager = {
        getTheme: getTheme,
        setTheme: setTheme,
        toggleTheme: toggleTheme,
        clearPreference: clearThemePreference,
        LIGHT: THEME_LIGHT,
        DARK: THEME_DARK
    };

    // ========================================================================
    // START
    // ========================================================================

    // Initialize immediately
    init();

})();
