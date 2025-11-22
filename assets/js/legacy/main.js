/**
 * Main JavaScript
 * Core functionality for Mulyasuchi platform
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }
    
    // Auto-dismiss flash messages
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(function() {
            flashMessage.style.opacity = '0';
            flashMessage.style.transform = 'translateX(400px)';
            setTimeout(function() {
                flashMessage.remove();
            }, 300);
        }, 4000);
    }
    
    // Hero search functionality
    const heroSearchBtn = document.getElementById('heroSearchBtn');
    const heroSearch = document.getElementById('heroSearch');
    
    if (heroSearchBtn && heroSearch) {
        heroSearchBtn.addEventListener('click', function() {
            const query = heroSearch.value.trim();
            if (query) {
                window.location.href = 'search.php?q=' + encodeURIComponent(query);
            }
        });
        
        heroSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                heroSearchBtn.click();
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});
