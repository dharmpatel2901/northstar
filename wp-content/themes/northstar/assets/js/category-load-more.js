
(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-category-load-more]');

        if (!button) {
            return;
        }

        event.preventDefault();

        const categoryList = document.querySelector('[data-category-list]');
        const resultsWrapper = document.querySelector('.shop-results');

        if (
            !categoryList ||
            !resultsWrapper ||
            button.disabled
        ) {
            return;
        }

        const ajaxUrl = resultsWrapper.dataset.ajaxUrl;
        const nonce = resultsWrapper.dataset.nonce;

        if (!ajaxUrl || !nonce) {
            console.error('Northstar category AJAX configuration is missing.');
            return;
        }

        const formData = new URLSearchParams();

        formData.append('action', 'northstar_load_more_categories');
        formData.append('nonce', nonce);
        formData.append('page', button.dataset.page);

        button.disabled = true;
        button.textContent = 'Loading...';

        fetch(ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: formData.toString()
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load categories.');
                }

                return response.json();
            })
            .then(function (response) {
                if (!response.success) {
                    throw new Error('Unable to load categories.');
                }

                categoryList.insertAdjacentHTML(
                    'beforeend',
                    response.data.html
                );

                button.dataset.page = Number(button.dataset.page) + 1;

                if (!response.data.has_more) {
                    button.remove();
                    return;
                }

                button.disabled = false;
                button.textContent = 'Load More';
            })
            .catch(function (error) {
                console.error('Northstar category load more:', error);

                button.disabled = false;
                button.textContent = 'Load More';
            });
    });

})();
