/**
 * Products Page JavaScript
 * Enhanced with AJAX filtering, live search, and dynamic updates
 */

(function() {
    'use strict';
    
    // DOM Elements
    const filterForm = document.getElementById('filterForm');
    const productsGrid = document.getElementById('productsGrid');
    const resultsCount = document.querySelector('.results-count');
    const searchInput = filterForm?.querySelector('input[name="search"]');
    const categorySelect = filterForm?.querySelector('select[name="category"]');
    const minPriceInput = filterForm?.querySelector('input[name="min_price"]');
    const maxPriceInput = filterForm?.querySelector('input[name="max_price"]');
    const sortSelect = filterForm?.querySelector('select[name="sort"]');
    const applyButton = filterForm?.querySelector('.btn-apply');
    const resetButton = filterForm?.querySelector('.btn-reset');
    const headerSearchInput = document.querySelector('.header-search-input');
    const paginationWrapper = document.querySelector('.pagination-wrapper');
    
    // State
    let debounceTimer = null;
    let currentPage = 1;
    let isLoading = false;
    let currentFilters = {};
    
    // Configuration
    const DEBOUNCE_DELAY = 300;
    
    /**
     * Get the base path for AJAX calls
     */
    function getAjaxBasePath() {
        const path = window.location.pathname;
        // Check if we're in /public/ directory
        if (path.includes('/public/')) {
            return path.substring(0, path.lastIndexOf('/public/') + 8);
        }
        // Otherwise, assume the current directory
        const currentDir = path.substring(0, path.lastIndexOf('/') + 1);
        return currentDir;
    }
    
    const AJAX_BASE_PATH = getAjaxBasePath();
    
    /**
     * Initialize the filter functionality
     */
    function init() {
        if (!filterForm || !productsGrid) {
            return;
        }
        
        // Initialize current filters from URL params
        initFiltersFromURL();
        
        // Setup event listeners
        setupEventListeners();
        
        // Setup view toggle
        setupViewToggle();
        
        // Setup product card animations
        setupCardAnimations();
        
        // Setup mobile filter toggle
        setupMobileFilterToggle();
    }
    
    /**
     * Initialize filters from URL parameters
     */
    function initFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        currentFilters = {
            search: urlParams.get('search') || '',
            category: urlParams.get('category') || '',
            min_price: urlParams.get('min_price') || '',
            max_price: urlParams.get('max_price') || '',
            sort: urlParams.get('sort') || 'name_asc',
            page: parseInt(urlParams.get('page')) || 1
        };
        currentPage = currentFilters.page;
    }
    
    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Live search on name input with debounce
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentFilters.search = e.target.value;
                    currentPage = 1;
                    fetchAndRenderProducts();
                }, DEBOUNCE_DELAY);
            });
        }
        
        // Header search input sync
        if (headerSearchInput) {
            headerSearchInput.addEventListener('input', function(e) {
                if (searchInput) {
                    searchInput.value = e.target.value;
                }
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentFilters.search = e.target.value;
                    currentPage = 1;
                    fetchAndRenderProducts();
                }, DEBOUNCE_DELAY);
            });
            
            // Sync sidebar search to header search
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    headerSearchInput.value = e.target.value;
                });
            }
        }
        
        // Category dropdown change
        if (categorySelect) {
            categorySelect.addEventListener('change', function(e) {
                currentFilters.category = e.target.value;
                currentPage = 1;
                fetchAndRenderProducts();
            });
        }
        
        // Price range inputs with debounce
        if (minPriceInput) {
            minPriceInput.addEventListener('input', function(e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentFilters.min_price = e.target.value;
                    currentPage = 1;
                    fetchAndRenderProducts();
                }, DEBOUNCE_DELAY);
            });
        }
        
        if (maxPriceInput) {
            maxPriceInput.addEventListener('input', function(e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentFilters.max_price = e.target.value;
                    currentPage = 1;
                    fetchAndRenderProducts();
                }, DEBOUNCE_DELAY);
            });
        }
        
        // Sort dropdown change
        if (sortSelect) {
            sortSelect.addEventListener('change', function(e) {
                currentFilters.sort = e.target.value;
                currentPage = 1;
                fetchAndRenderProducts();
            });
        }
        
        // Apply Filters button
        if (applyButton) {
            applyButton.addEventListener('click', function(e) {
                e.preventDefault();
                collectFilters();
                currentPage = 1;
                fetchAndRenderProducts();
                
                // Close mobile filter on apply
                closeMobileFilter();
            });
        }
        
        // Reset button
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                resetFilters();
                
                // Close mobile filter on reset
                closeMobileFilter();
            });
        }
        
        // Prevent form submission (we handle it via AJAX)
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            collectFilters();
            currentPage = 1;
            fetchAndRenderProducts();
        });
        
        // Prevent header search form submission
        const headerSearchForm = document.getElementById('headerSearchForm');
        if (headerSearchForm) {
            headerSearchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Search is already handled by input event
            });
        }
    }
    
    /**
     * Collect all filter values from the form
     */
    function collectFilters() {
        currentFilters = {
            search: searchInput?.value || '',
            category: categorySelect?.value || '',
            min_price: minPriceInput?.value || '',
            max_price: maxPriceInput?.value || '',
            sort: sortSelect?.value || 'name_asc',
            page: currentPage
        };
    }
    
    /**
     * Reset all filters to default
     */
    function resetFilters() {
        // Reset form fields
        if (searchInput) searchInput.value = '';
        if (headerSearchInput) headerSearchInput.value = '';
        if (categorySelect) categorySelect.selectedIndex = 0;
        if (minPriceInput) minPriceInput.value = '';
        if (maxPriceInput) maxPriceInput.value = '';
        if (sortSelect) sortSelect.value = 'name_asc';
        
        // Reset state
        currentFilters = {
            search: '',
            category: '',
            min_price: '',
            max_price: '',
            sort: 'name_asc',
            page: 1
        };
        currentPage = 1;
        
        // Update URL
        updateURL();
        
        // Fetch products
        fetchAndRenderProducts();
    }
    
    /**
     * Fetch products from AJAX endpoint
     */
    async function fetchAndRenderProducts() {
        if (isLoading) return;
        
        isLoading = true;
        showLoadingState();
        
        try {
            const params = new URLSearchParams();
            if (currentFilters.search) params.append('search', currentFilters.search);
            if (currentFilters.category) params.append('category', currentFilters.category);
            if (currentFilters.min_price) params.append('min_price', currentFilters.min_price);
            if (currentFilters.max_price) params.append('max_price', currentFilters.max_price);
            if (currentFilters.sort) params.append('sort', currentFilters.sort);
            params.append('page', currentPage);
            
            const response = await fetch(`${AJAX_BASE_PATH}ajax/filter_products.php?${params.toString()}`);
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.success) {
                renderProducts(data.items, data.is_admin, data.site_url);
                updateResultsCount(data.pagination.total_items);
                renderPagination(data.pagination);
                updateURL();
            } else {
                showError('Failed to load products. Please try again.');
            }
        } catch (error) {
            showError('An error occurred while loading products.');
        } finally {
            isLoading = false;
        }
    }
    
    /**
     * Render products to the grid
     */
    function renderProducts(items, isAdmin, siteUrl) {
        if (!productsGrid) return;
        
        // Preserve current view mode
        const isListView = productsGrid.classList.contains('list-view');
        
        if (items.length === 0) {
            productsGrid.innerHTML = '';
            showEmptyState();
            return;
        }
        
        // Remove empty state if exists
        const emptyState = document.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }
        
        let html = '';
        items.forEach((item, index) => {
            const delay = index * 0.05;
            html += createProductCard(item, isAdmin, siteUrl, delay);
        });
        
        productsGrid.innerHTML = html;
        
        // Restore view mode
        if (isListView) {
            productsGrid.classList.add('list-view');
            productsGrid.classList.remove('grid-view');
        }
        
        // Re-apply animations
        setupCardAnimations();
    }
    
    /**
     * Create HTML for a single product card
     */
    function createProductCard(item, isAdmin, siteUrl, delay) {
        const nepaliName = item.item_name_nepali 
            ? `<p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.75rem;">${item.item_name_nepali}</p>` 
            : '';
        
        const marketLocation = item.market_location 
            ? `<span class="meta-item"><i class="bi bi-geo-alt"></i>${item.market_location}</span>` 
            : '';
        
        const adminButton = isAdmin 
            ? `<a href="${siteUrl}/admin/edit_item.php?id=${item.item_id}" class="btn-edit-icon" title="Edit Item"><i class="bi bi-pencil-square"></i></a>` 
            : '';
        
        return `
            <div class="product-card ${item.category_class}" style="animation-delay: ${delay}s;">
                <div class="product-image">
                    <span class="product-badge ${item.status_class}">${item.status_label}</span>
                    <img src="${item.image_path}" 
                         alt="${item.item_name}"
                         class="product-main-image"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                    <div class="product-image-placeholder" style="display: none;">
                        <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="product-details">
                    <span class="product-category">${item.category_name}</span>
                    <h3 class="product-name" style="color: #000000;">${item.item_name}</h3>
                    ${nepaliName}
                    <div class="product-meta">
                        <span class="meta-item">
                            <i class="bi bi-tag"></i>
                            Per ${item.unit}
                        </span>
                        ${marketLocation}
                    </div>
                    <div class="product-price" style="color: #000000;">
                        NPR ${item.current_price}
                    </div>
                    <div class="product-actions">
                        <a href="item.php?slug=${encodeURIComponent(item.slug)}" class="btn-view">
                            View Details
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        ${adminButton}
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Show empty state when no products found
     */
    function showEmptyState() {
        const productsContent = document.querySelector('.products-content');
        const existingEmpty = productsContent?.querySelector('.empty-state');
        
        if (existingEmpty) return;
        
        const emptyHTML = `
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <h3 class="empty-title">No products found</h3>
                <p class="empty-text">Try adjusting your filters or search criteria</p>
                <button class="btn-apply" onclick="document.querySelector('.btn-reset').click()">Clear All Filters</button>
            </div>
        `;
        
        if (productsGrid) {
            productsGrid.insertAdjacentHTML('afterend', emptyHTML);
        }
    }
    
    /**
     * Show loading state
     */
    function showLoadingState() {
        if (!productsGrid) return;
        
        productsGrid.style.opacity = '0.5';
        productsGrid.style.pointerEvents = 'none';
        
        // Add loading indicator if not exists
        if (!document.querySelector('.products-loading')) {
            const loadingHTML = `
                <div class="products-loading">
                    <div class="loading-spinner"></div>
                    <span>Loading products...</span>
                </div>
            `;
            productsGrid.insertAdjacentHTML('beforebegin', loadingHTML);
        }
    }
    
    /**
     * Hide loading state
     */
    function hideLoadingState() {
        if (!productsGrid) return;
        
        productsGrid.style.opacity = '1';
        productsGrid.style.pointerEvents = 'auto';
        
        const loading = document.querySelector('.products-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    /**
     * Show error message
     */
    function showError(message) {
        hideLoadingState();
        
        if (productsGrid) {
            productsGrid.innerHTML = `
                <div class="error-state">
                    <div class="error-icon">⚠️</div>
                    <h3 class="error-title">Oops! Something went wrong</h3>
                    <p class="error-text">${message}</p>
                    <button class="btn-apply" onclick="location.reload()">Try Again</button>
                </div>
            `;
        }
    }
    
    /**
     * Update results count
     */
    function updateResultsCount(total) {
        if (resultsCount) {
            resultsCount.innerHTML = `
                <span class="count">${total}</span> 
                ${total === 1 ? 'product' : 'products'} found
            `;
        }
        hideLoadingState();
    }
    
    /**
     * Render pagination
     */
    function renderPagination(pagination) {
        if (!paginationWrapper) return;
        
        const { current_page, total_pages, total_items } = pagination;
        
        if (total_pages <= 1) {
            paginationWrapper.innerHTML = '';
            return;
        }
        
        let html = '<ul class="pagination">';
        
        // Previous button
        if (current_page > 1) {
            html += `
                <li>
                    <a href="#" class="page-link" data-page="${current_page - 1}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;
        } else {
            html += `
                <li>
                    <span class="page-link disabled">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            `;
        }
        
        // Page numbers
        const startPage = Math.max(1, current_page - 2);
        const endPage = Math.min(total_pages, current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === current_page ? 'active' : '';
            html += `
                <li>
                    <a href="#" class="page-link ${activeClass}" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Next button
        if (current_page < total_pages) {
            html += `
                <li>
                    <a href="#" class="page-link" data-page="${current_page + 1}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;
        } else {
            html += `
                <li>
                    <span class="page-link disabled">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            `;
        }
        
        html += '</ul>';
        paginationWrapper.innerHTML = html;
        
        // Add click handlers for pagination
        paginationWrapper.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page !== currentPage) {
                    currentPage = page;
                    currentFilters.page = page;
                    fetchAndRenderProducts();
                    
                    // Smooth scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }
    
    /**
     * Update browser URL with current filters
     */
    function updateURL() {
        const params = new URLSearchParams();
        
        if (currentFilters.search) params.set('search', currentFilters.search);
        if (currentFilters.category) params.set('category', currentFilters.category);
        if (currentFilters.min_price) params.set('min_price', currentFilters.min_price);
        if (currentFilters.max_price) params.set('max_price', currentFilters.max_price);
        if (currentFilters.sort && currentFilters.sort !== 'name_asc') params.set('sort', currentFilters.sort);
        if (currentPage > 1) params.set('page', currentPage);
        
        const newURL = params.toString() 
            ? `${window.location.pathname}?${params.toString()}`
            : window.location.pathname;
        
        window.history.replaceState({}, '', newURL);
    }
    
    /**
     * Setup view toggle (grid/list)
     */
    function setupViewToggle() {
        const viewButtons = document.querySelectorAll('.view-btn');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                viewButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const view = btn.dataset.view;
                if (view === 'list') {
                    productsGrid.classList.add('list-view');
                    productsGrid.classList.remove('grid-view');
                } else {
                    productsGrid.classList.add('grid-view');
                    productsGrid.classList.remove('list-view');
                }
            });
        });
    }
    
    /**
     * Setup product card animations using Intersection Observer
     */
    function setupCardAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    }
    
    /**
     * Setup mobile filter toggle
     */
    function setupMobileFilterToggle() {
        // Create mobile filter toggle button if it doesn't exist
        if (!document.querySelector('.mobile-filter-toggle')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'mobile-filter-toggle';
            toggleBtn.innerHTML = '<i class="bi bi-funnel"></i> Filters';
            toggleBtn.addEventListener('click', openMobileFilter);
            
            const resultsHeader = document.querySelector('.results-header');
            if (resultsHeader) {
                resultsHeader.insertBefore(toggleBtn, resultsHeader.firstChild);
            }
        }
        
        // Create overlay if it doesn't exist
        if (!document.querySelector('.filter-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'filter-overlay';
            overlay.addEventListener('click', closeMobileFilter);
            document.body.appendChild(overlay);
        }
        
        // Add close button to filter sidebar if it doesn't exist
        const filterSidebar = document.querySelector('.filter-sidebar');
        if (filterSidebar && !filterSidebar.querySelector('.filter-close-btn')) {
            const closeBtn = document.createElement('button');
            closeBtn.className = 'filter-close-btn';
            closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
            closeBtn.addEventListener('click', closeMobileFilter);
            filterSidebar.insertBefore(closeBtn, filterSidebar.firstChild);
        }
    }
    
    /**
     * Open mobile filter panel
     */
    function openMobileFilter() {
        const filterSidebar = document.querySelector('.filter-sidebar');
        const overlay = document.querySelector('.filter-overlay');
        
        if (filterSidebar) {
            filterSidebar.classList.add('active');
        }
        if (overlay) {
            overlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    }
    
    /**
     * Close mobile filter panel
     */
    function closeMobileFilter() {
        const filterSidebar = document.querySelector('.filter-sidebar');
        const overlay = document.querySelector('.filter-overlay');
        
        if (filterSidebar) {
            filterSidebar.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
