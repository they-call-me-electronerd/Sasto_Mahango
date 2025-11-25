/**
 * Price Ticker JavaScript
 * Live price updates with auto-refresh
 */

document.addEventListener('DOMContentLoaded', function () {
    const tickerContent = document.getElementById('priceTicker');
    if (!tickerContent) return;

    const TICKER_UPDATE_INTERVAL = 5000; // 5 seconds

    function fetchTickerData() {
        fetch('ajax/get_price_ticker.php')
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    tickerContent.innerHTML = '<p>No significant price changes in the past week.</p>';
                    return;
                }

                // Duplicate data for seamless scroll
                const tickerItems = [...data, ...data];

                let html = '';
                tickerItems.forEach(item => {
                    const directionClass = item.direction === 'up' ? 'price-up' : 'price-down';
                    const arrow = item.direction === 'up' ? '▲' : '▼';

                    html += `
                        <div class="ticker-item">
                            <span class="item-name">${escapeHtml(item.item_name)}</span>
                            <span class="${directionClass}">
                                ${arrow} ${Math.abs(item.change_percent)}%
                            </span>
                            <span style="font-size: 0.875rem;">Rs. ${item.new_price}</span>
                        </div>
                    `;
                });

                tickerContent.innerHTML = html;
            })
            .catch(error => {
                tickerContent.innerHTML = '<p>Unable to load price updates.</p>';
            });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial fetch
    fetchTickerData();

    // Auto-refresh
    setInterval(fetchTickerData, TICKER_UPDATE_INTERVAL);
});
