/**
 * Price Chart Component
 * Renders price history charts using Chart.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const chartCanvas = document.getElementById('priceChart');
    
    if (!chartCanvas || typeof priceData === 'undefined') {
        return;
    }
    
    // Prepare data
    const dates = priceData.map(item => new Date(item.updated_at).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
    }));
    
    const prices = priceData.map(item => parseFloat(item.new_price));
    
    // Create gradient
    const ctx = chartCanvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(249, 115, 22, 0.3)');
    gradient.addColorStop(1, 'rgba(249, 115, 22, 0.01)');
    
    // Create chart
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Price (NPR)',
                data: prices,
                borderColor: '#f97316',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#f97316',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#ea580c',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2.5,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#111827',
                    titleColor: '#f9fafb',
                    bodyColor: '#f9fafb',
                    padding: 12,
                    borderColor: '#f97316',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'NPR ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 12,
                            weight: 500
                        }
                    }
                },
                y: {
                    grid: {
                        color: '#f3f4f6',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 12,
                            weight: 500
                        },
                        callback: function(value) {
                            return 'NPR ' + value.toFixed(0);
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
