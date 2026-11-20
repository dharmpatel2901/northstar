
(function () {
    'use strict';

    const wishlistKey = 'northstar_wishlist';

    let wishlistReady = false;
    let wishlistQueue = Promise.resolve();

    /**
     * Get saved wishlist products.
     *
     * @return {Array}
     */
    function getWishlist() {
        try {
            const saved = JSON.parse(
                localStorage.getItem(wishlistKey) || '[]'
            );

            return Array.isArray(saved) ? saved.map(String) : [];
        } catch (error) {
            return [];
        }
    }

    /**
     * Save wishlist to localStorage.
     *
     * @param {Array} wishlist Product IDs.
     */
    function saveWishlist(wishlist) {
        localStorage.setItem(
            wishlistKey,
            JSON.stringify(wishlist)
        );
    }

    /**
     * Update wishlist button state.
     *
     * @param {HTMLElement} button Wishlist button.
     * @param {boolean} isSaved Whether product is saved.
     */
    function updateButton(button, isSaved) {
        button.classList.toggle('is-saved', isSaved);

        button.setAttribute(
            'aria-pressed',
            isSaved ? 'true' : 'false'
        );

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
     * Send wishlist data to WordPress.
     *
     * @param {string} action AJAX action.
     * @param {Array} wishlist Product IDs.
     * @return {Promise}
     */
    function sendWishlistRequest(action, wishlist) {
        const formData = new FormData();

        formData.append('action', action);
        formData.append('nonce', northstarWishlist.nonce);

        wishlist.forEach(function (productId) {
            formData.append('wishlist[]', productId);
        });

        return fetch(northstarWishlist.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        }).then(function (response) {
            return response.json();
        }).then(function (response) {
            if (!response.success) {
                throw new Error('Wishlist synchronisation failed.');
            }

            return response.data.wishlist.map(String);
        });
    }

    /**
     * Synchronise guest wishlist with account wishlist.
     *
     * @return {Promise}
     */
    function synchroniseWishlist() {
        const guestWishlist = getWishlist();

        return sendWishlistRequest(
            'northstar_sync_wishlist',
            guestWishlist
        ).then(function (wishlist) {
            saveWishlist(wishlist);

            return wishlist;
        });
    }

    /**
     * Save updated wishlist to logged-in account.
     *
     * @param {Array} wishlist Product IDs.
     * @return {Promise}
     */
    function updateAccountWishlist(wishlist) {
        return sendWishlistRequest(
            'northstar_update_wishlist',
            wishlist
        );
    }

    function initialiseWishlist() {

        let wishlist = getWishlist();

        if (northstarWishlist.loggedIn) {

            // Fetch the account wishlist from WordPress.
            return sendWishlistRequest(
                'northstar_get_wishlist',
                []
            )
                .then(function (savedWishlist) {
                    wishlist = savedWishlist;

                    // Keep localStorage in sync.
                    saveWishlist(wishlist);
                })
                .catch(function (error) {
                    console.error(error);

                    showWishlistMessage(
                        'Unable to load account wishlist.'
                    );
                })
                .finally(function () {
                    wishlistReady = true;
                    updateAllButtons(wishlist);
                });
        }

        wishlistReady = true;
        updateAllButtons(wishlist);

        return Promise.resolve();
    }

    /**
     * Update all wishlist buttons.
     *
     * @param {Array} wishlist Product IDs.
     */
    function updateAllButtons(wishlist) {
        document.querySelectorAll('[data-wishlist-button]').forEach(
            function (button) {
                const productId = String(button.dataset.productId);

                updateButton(
                    button,
                    wishlist.includes(productId)
                );
            }
        );
    }

    /**
     * Queue account updates to preserve click order.
     *
     * @param {Array} wishlist Product IDs.
     */
    function queueAccountUpdate(wishlist) {

        if (!northstarWishlist.loggedIn) {
            return;
        }

        wishlistQueue = wishlistQueue
            .then(function () {
                return updateAccountWishlist(wishlist);
            })
            .catch(function (error) {
                console.error(error);

                showWishlistMessage(
                    'Wishlist could not be saved to your account.'
                );
            });
    }

    /**
     * Handle wishlist button clicks.
     */
    document.addEventListener('click', function (event) {

        const button = event.target.closest('[data-wishlist-button]');

        if (!button || !wishlistReady) {
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

            showWishlistMessage('Removed from wishlist');

        } else {

            wishlist.push(productId);

            showWishlistMessage('Added to wishlist');
        }

        saveWishlist(wishlist);

        updateAllButtons(wishlist);

        // Update WordPress account if logged in.
        queueAccountUpdate(wishlist);
    });

    // Start wishlist initialisation.
    initialiseWishlist();

})();
