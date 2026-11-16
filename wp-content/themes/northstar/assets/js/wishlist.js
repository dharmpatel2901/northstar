
(function () {
    'use strict';

    const wishlistKey = 'northstar_wishlist';

    /**
     * Get saved wishlist products.
     *
     * @return {Array}
     */
    function getWishlist() {
        try {
            const saved = JSON.parse(localStorage.getItem(wishlistKey) || '[]');

            return Array.isArray(saved) ? saved.map(String) : [];
        } catch (error) {
            return [];
        }
    }

    /**
     * Update wishlist button state.
     *
     * @param {HTMLElement} button Wishlist button.
     * @param {boolean} isSaved Whether product is saved.
     */
    function updateButton(button, isSaved) {
        button.classList.toggle('is-saved', isSaved);
        button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
        button.setAttribute(
            'aria-label',
            isSaved ? 'Remove from wishlist' : 'Add to wishlist'
        );
    }

    /**
     * Display wishlist notification.
     *
     * @param {string} message Notification message.
     */
    function showWishlistMessage(message) {
        let toast = document.querySelector('.wishlist-toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'wishlist-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.classList.add('show');

        clearTimeout(toast.hideTimeout);

        toast.hideTimeout = setTimeout(function () {
            toast.classList.remove('show');
        }, 2500);
    }

    /**
     * Initialise wishlist buttons.
     *
     * @return {void}
     */
    function initialiseWishlist() {
        const wishlist = getWishlist();

        document.querySelectorAll('[data-wishlist-button]').forEach(function (button) {
            const productId = String(button.dataset.productId);

            updateButton(button, wishlist.includes(productId));
        });
    }

    /**
     * Handle wishlist button clicks.
     */
    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-wishlist-button]');

        if (!button) {
            return;
        }

        event.preventDefault();

        const productId = String(button.dataset.productId);

        if (!productId || productId === 'undefined') {
            return;
        }

        let wishlist = getWishlist();

        if (wishlist.includes(productId)) {
            wishlist = wishlist.filter(function (id) {
                return id !== productId;
            });

            updateButton(button, false);
            showWishlistMessage('Removed from wishlist');
        } else {
            wishlist.push(productId);

            updateButton(button, true);
            showWishlistMessage('Added to wishlist');
        }

        localStorage.setItem(wishlistKey, JSON.stringify(wishlist));
    });

    initialiseWishlist();

})();
