
(function () {
    'use strict';

    const filterContainer = document.querySelector('#northstar-product-filters');
    const resultsContainer = document.querySelector('.shop-results');
    const productResults = document.querySelector('#northstar-shop-results');

    if (!filterContainer || !resultsContainer || !productResults) {
        return;
    }

    const ajaxUrl = resultsContainer.dataset.ajaxUrl;
    const nonce = resultsContainer.dataset.nonce;

    let requestController = null;
    let priceTimeout = null;
    let searchTimeout = null;

    /**
     * Collect selected filter values.
     *
     * @param {number} page Current pagination page.
     * @return {URLSearchParams}
     */
    function getFilterData(page) {
        const formData = new URLSearchParams();

        formData.append('action', 'northstar_filter_products');
        formData.append('nonce', nonce);

        // Categories.
        filterContainer.querySelectorAll(
            'input[data-filter="category"]:checked'
        ).forEach(function (input) {
            formData.append('category[]', input.value);
        });

        // Stock status.
        filterContainer.querySelectorAll(
            'input[data-filter="stock"]:checked'
        ).forEach(function (input) {
            formData.append('stock[]', input.value);
        });

        // Price range.
        const minPrice = filterContainer.querySelector(
            '[data-filter="min-price"]'
        );

        const maxPrice = filterContainer.querySelector(
            '[data-filter="max-price"]'
        );

        if (minPrice && minPrice.value !== '') {
            formData.append('min_price', minPrice.value);
        }

        if (maxPrice && maxPrice.value !== '') {
            formData.append('max_price', maxPrice.value);
        }

        // Search.
        const searchInput = document.querySelector(
            '.shop-toolbar-controls input[name="s"], ' +
            '.shop-search input[name="s"], ' +
            '.woocommerce-product-search input[name="s"]'
        );

        if (searchInput && searchInput.value.trim() !== '') {
            formData.append('search', searchInput.value.trim());
        }

        // Sorting.
        const sortSelect = document.querySelector(
            '.shop-toolbar-controls select[name="orderby"], ' +
            '.shop-sorting select[name="orderby"], ' +
            'form.woocommerce-ordering select[name="orderby"]'
        );

        if (sortSelect) {
            const sortMap = {
                menu_order: 'featured',
                popularity: 'popularity',
                rating: 'rating',
                date: 'latest',
                price: 'low',
                'price-desc': 'high'
            };

            formData.append(
                'sort',
                sortMap[sortSelect.value] || 'featured'
            );
        }

        formData.append('paged', String(page));

        return formData;
    }

    /**
     * Set loading state.
     *
     * @param {boolean} isLoading Loading status.
     */
    function setLoading(isLoading) {
        resultsContainer.classList.toggle('is-loading', isLoading);

        resultsContainer.setAttribute(
            'aria-busy',
            isLoading ? 'true' : 'false'
        );
    }

    /**
     * Update product results.
     *
     * @param {string} html Product results HTML.
     */
    function updateProductResults(html) {
        productResults.innerHTML = html;

        // Reinitialise wishlist buttons after AJAX replacement.
        document.querySelectorAll('[data-wishlist-button]').forEach(function (button) {
            const wishlist = JSON.parse(
                localStorage.getItem('northstar_wishlist') || '[]'
            );

            const productId = String(button.dataset.productId);
            const isSaved = Array.isArray(wishlist) &&
                wishlist.map(String).includes(productId);

            button.classList.toggle('is-saved', isSaved);
            button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
            button.setAttribute(
                'aria-label',
                isSaved ? 'Remove from wishlist' : 'Add to wishlist'
            );
        });
    }

    /**
     * Update WooCommerce result count.
     *
     * @param {Object} data AJAX response data.
     */
    function updateProductCount(data) {
        const resultCount = document.querySelector('.woocommerce-result-count');

        if (!resultCount) {
            return;
        }

        const total = Number(data.found_posts);
        const current = Number(data.current_page);
        const perPage = Number(data.per_page);

        if (
            !Number.isFinite(total) ||
            !Number.isFinite(current) ||
            !Number.isFinite(perPage) ||
            current < 1 ||
            perPage <= 0
        ) {
            console.error('Invalid WooCommerce result count data:', data);
            return;
        }

        if (total === 0) {
            resultCount.textContent =
                'No products were found matching your selection.';
            return;
        }

        const first = ((current - 1) * perPage) + 1;
        const last = Math.min(current * perPage, total);

        if (total <= perPage) {
            resultCount.textContent = `Showing all ${total} results`;
        } else {
            resultCount.textContent =
                `Showing ${first}–${last} of ${total} results`;
        }
    }

    /**
     * Fetch filtered products.
     *
     * @param {number} page Requested page.
     */
    async function applyFilters(page) {
        if (!ajaxUrl || !nonce) {
            console.error('Northstar AJAX configuration is missing.');
            return;
        }

        if (requestController) {
            requestController.abort();
        }

        requestController = new AbortController();

        const currentController = requestController;

        setLoading(true);

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: getFilterData(page || 1).toString(),
                signal: currentController.signal
            });

            if (!response.ok) {
                throw new Error('Unable to fetch products.');
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(
                    result.data && result.data.message
                        ? result.data.message
                        : 'Something went wrong.'
                );
            }

            updateProductResults(result.data.html);
            updateProductCount(result.data);

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Northstar product filter:', error);
            }
        } finally {
            if (requestController === currentController) {
                requestController = null;
                setLoading(false);
            }
        }
    }

    // Category and stock filtering.
    filterContainer.addEventListener('change', function (event) {
        if (
            event.target.matches(
                'input[data-filter="category"], input[data-filter="stock"]'
            )
        ) {
            applyFilters(1);
        }
    });

    // Price filtering.
    filterContainer.addEventListener('input', function (event) {
        if (
            !event.target.matches(
                '[data-filter="min-price"], [data-filter="max-price"]'
            )
        ) {
            return;
        }

        clearTimeout(priceTimeout);

        priceTimeout = setTimeout(function () {
            applyFilters(1);
        }, 500);
    });

    // Product search.
    document.addEventListener('input', function (event) {
        if (
            !event.target.matches(
                '.shop-toolbar-controls input[name="s"], ' +
                '.shop-search input[name="s"], ' +
                '.woocommerce-product-search input[name="s"]'
            )
        ) {
            return;
        }

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(function () {
            applyFilters(1);
        }, 500);
    });

    // Prevent normal product search form submission.
    window.addEventListener('submit', function (event) {
        const searchForm = event.target;

        if (!(searchForm instanceof HTMLFormElement)) {
            return;
        }

        const searchInput = searchForm.querySelector('input[name="s"]');

        const isProductSearch =
            searchForm.matches('.shop-search, .woocommerce-product-search') ||
            (
                searchForm.closest('.shop-toolbar-controls') &&
                searchInput
            );

        if (!isProductSearch) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        clearTimeout(searchTimeout);

        applyFilters(1);
    }, true);

    // Product sorting.
    window.addEventListener('change', function (event) {
        if (
            !event.target.matches(
                '.shop-toolbar-controls select[name="orderby"], ' +
                '.shop-sorting select[name="orderby"], ' +
                'form.woocommerce-ordering select[name="orderby"]'
            )
        ) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        applyFilters(1);
    }, true);

    // Clear all filters.
    const clearButton = document.querySelector('#northstar-clear-filters');

    if (clearButton) {
        clearButton.addEventListener('click', function () {
            filterContainer.querySelectorAll(
                'input[type="checkbox"]'
            ).forEach(function (input) {
                input.checked = false;
            });

            filterContainer.querySelectorAll(
                '[data-filter="min-price"], [data-filter="max-price"]'
            ).forEach(function (input) {
                input.value = '';
            });

            applyFilters(1);
        });
    }

    /**
     * Get pagination page from URL.
     *
     * @param {string} url Pagination URL.
     * @return {number}
     */
    function getPaginationPage(url) {
        const paginationUrl = new URL(url, window.location.href);

        let page =
            paginationUrl.searchParams.get('paged') ||
            paginationUrl.searchParams.get('product-page');

        if (!page) {
            const match = paginationUrl.pathname.match(/\/page\/(\d+)(?:\/|$)/);
            page = match ? match[1] : '1';
        }

        page = parseInt(page, 10);

        return page > 0 ? page : 1;
    }

    // AJAX pagination.
    productResults.addEventListener('click', function (event) {
        const paginationLink = event.target.closest(
            '.woocommerce-pagination a'
        );

        if (!paginationLink) {
            return;
        }

        event.preventDefault();

        applyFilters(getPaginationPage(paginationLink.href));

        resultsContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });

})();
