/**
 * MulyaSuchi Landing Page Interactive Features
 * Bloomberg Terminal-Inspired Experience
 */

(function () {
    'use strict';

    // Configuration
    const CONFIG = {
        tickerUpdateInterval: 30000, // 30 seconds
        marketMoversUpdateInterval: 60000, // 1 minute
        searchPlaceholders: [
            'Search for Rice, Gold, or iPhone 15...',
            'Find prices for Cement, Tomato, or Milk...',
            'Looking for Sugar, Oil, or Laptop prices?',
            'Check prices of Onion, Flour, or Headphones...'
        ]
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initDynamicPlaceholder();
        loadMarketMovers();
        updateLiveTicker();
        initLastUpdateTimer();
        initTooltips();

        // Set up periodic updates
        setInterval(updateLiveTicker, CONFIG.tickerUpdateInterval);
        setInterval(loadMarketMovers, CONFIG.marketMoversUpdateInterval);
    });

    /**
     * Dark/Light Mode Toggle
     */
    function initDarkMode() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (!darkModeToggle) return;

        // Check for saved preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';

        // Apply saved preference
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }

        // Toggle handler
        darkModeToggle.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'false');
            }
        });
    }

    /**
     * Dynamic Search Placeholder
     */
    function initDynamicPlaceholder() {
        const searchInput = document.getElementById('heroSearchInput');
        if (!searchInput) return;

        let currentIndex = 0;

        function rotatePlaceholder() {
            currentIndex = (currentIndex + 1) % CONFIG.searchPlaceholders.length;
            searchInput.placeholder = CONFIG.searchPlaceholders[currentIndex];
        }

        // Rotate every 3 seconds
        setInterval(rotatePlaceholder, 3000);
    }

    /**
     * Load Market Movers Data
     */
    function loadMarketMovers() {
        fetch('ajax/market-movers.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderGainers(data.gainers);
                    renderLosers(data.losers);
                    renderEssentials(data.essentials);
                }
            })
            .catch(error => {
                console.error('Failed to load market movers:', error);
                document.getElementById('gainersList').innerHTML =
                    '<li class="text-center py-4 text-danger">Failed to load data</li>';
                document.getElementById('losersList').innerHTML =
                    '<li class="text-center py-4 text-danger">Failed to load data</li>';
                document.getElementById('essentialsList').innerHTML =
                    '<li class="text-center py-4 text-danger">Failed to load data</li>';
            });
    }

    /**
     * Render Gainers List
     */
    function renderGainers(gainers) {
        const list = document.getElementById('gainersList');
        if (!list || !gainers || gainers.length === 0) return;

        list.innerHTML = gainers.map(item => `
            <li class="market-mover-item">
                <div class="mover-icon">${item.emoji}</div>
                <div class="mover-info">
                    <h4>${escapeHtml(item.item_name)}</h4>
                    <p>${escapeHtml(item.category_name)}</p>
                </div>
                <div class="mover-price price-display">NPR ${formatPrice(item.current_price)}</div>
                <div class="mover-change positive">
                    <i class="bi bi-arrow-up"></i>
                    ${Math.abs(item.price_change_percent).toFixed(1)}%
                </div>
                <canvas class="sparkline-chart" data-sparkline='${JSON.stringify(item.sparkline_data)}'></canvas>
            </li>
        `).join('');

        // Render sparklines
        renderSparklines();
    }

    /**
     * Render Losers List
     */
    function renderLosers(losers) {
        const list = document.getElementById('losersList');
        if (!list || !losers || losers.length === 0) return;

        list.innerHTML = losers.map(item => `
            <li class="market-mover-item">
                <div class="mover-icon">${item.emoji}</div>
                <div class="mover-info">
                    <h4>${escapeHtml(item.item_name)}</h4>
                    <p>${escapeHtml(item.category_name)}</p>
                </div>
                <div class="mover-price price-display">NPR ${formatPrice(item.current_price)}</div>
                <div class="mover-change negative">
                    <i class="bi bi-arrow-down"></i>
                    ${Math.abs(item.price_change_percent).toFixed(1)}%
                </div>
                <canvas class="sparkline-chart" data-sparkline='${JSON.stringify(item.sparkline_data)}'></canvas>
            </li>
        `).join('');

        // Render sparklines
        renderSparklines();
    }

    /**
     * Render Daily Essentials
     */
    function renderEssentials(essentials) {
        const list = document.getElementById('essentialsList');
        if (!list || !essentials || essentials.length === 0) return;

        list.innerHTML = essentials.map(item => `
            <li class="essential-item">
                <div>
                    <div class="essential-name">${escapeHtml(item.item_name)}</div>
                    <small class="text-muted" style="font-size: 0.75rem;">${item.last_updated}</small>
                </div>
                <div class="essential-price price-display">
                    NPR ${formatPrice(item.current_price)}
                    <small class="text-muted" style="font-size: 0.75rem; font-weight: normal;">/${escapeHtml(item.unit)}</small>
                </div>
            </li>
        `).join('');
    }

    /**
     * Render Sparkline Charts
     */
    function renderSparklines() {
        const sparklines = document.querySelectorAll('.sparkline-chart');

        sparklines.forEach(canvas => {
            const data = JSON.parse(canvas.getAttribute('data-sparkline'));
            if (!data || data.length === 0) return;

            const ctx = canvas.getContext('2d');
            const width = canvas.width = 80;
            const height = canvas.height = 30;

            const max = Math.max(...data);
            const min = Math.min(...data);
            const range = max - min || 1;

            // Calculate points
            const points = data.map((value, index) => ({
                x: (index / (data.length - 1)) * width,
                y: height - ((value - min) / range) * height
            }));

            // Draw line
            ctx.strokeStyle = data[data.length - 1] > data[0] ? '#10b981' : '#ef4444';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);

            points.forEach(point => {
                ctx.lineTo(point.x, point.y);
            });

            ctx.stroke();
        });
    }

    /**
     * Update Live Ticker
     */
    function updateLiveTicker() {
        fetch('ajax/market-movers.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && (data.gainers.length > 0 || data.losers.length > 0)) {
                    const ticker = document.getElementById('priceTicker');
                    if (!ticker) return;

                    // Combine gainers and losers
                    const allItems = [...data.gainers, ...data.losers].slice(0, 8);

                    const tickerHTML = allItems.map(item => {
                        const isPositive = item.price_change_percent > 0;
                        const changeClass = isPositive ? 'price-change-up' : 'price-change-down';
                        const arrow = isPositive ? '▲' : '▼';

                        return `
                            <span class="ticker-item">
                                ${item.emoji} <strong>${escapeHtml(item.item_name)}</strong>: 
                                <span class="price-display">NPR ${formatPrice(item.current_price)}</span> 
                                <span class="${changeClass}">${arrow} ${Math.abs(item.price_change_percent).toFixed(1)}%</span>
                            </span>
                        `;
                    }).join('');

                    // Duplicate for seamless loop
                    ticker.innerHTML = tickerHTML + tickerHTML;
                }
            })
            .catch(error => console.error('Failed to update ticker:', error));
    }

    /**
     * Update "Last Updated" Timer
     */
    function initLastUpdateTimer() {
        const updateTime = document.getElementById('lastUpdateTime');
        if (!updateTime) return;

        let minutes = 5;

        function updateTimer() {
            updateTime.textContent = `${minutes} min${minutes !== 1 ? 's' : ''}`;

            if (minutes < 60) {
                minutes++;
            }
        }

        // Update every minute
        setInterval(updateTimer, 60000);
    }

    /**
     * Initialize Bootstrap Tooltips
     */
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Utility: Format Price
     */
    function formatPrice(price) {
        return parseFloat(price).toLocaleString('en-NP', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    /**
     * Utility: Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

})();
