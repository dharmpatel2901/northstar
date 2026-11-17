document.addEventListener('DOMContentLoaded', function () {

    const tabContainers = document.querySelectorAll('.details');

    tabContainers.forEach(function (container) {

        const tabs = container.querySelectorAll('.tab');
        const panels = container.querySelectorAll('.tab-panel');

        tabs.forEach(function (tab) {

            tab.addEventListener('click', function () {

                const target = tab.dataset.tab;

                // Remove active class from all tabs.
                tabs.forEach(function (item) {
                    item.classList.remove('active');
                });

                // Hide all panels.
                panels.forEach(function (panel) {
                    panel.hidden = true;
                });

                // Activate selected tab.
                tab.classList.add('active');

                // Show selected panel.
                const activePanel = container.querySelector(
                    '[data-tab-panel="' + target + '"]'
                );

                if (activePanel) {
                    activePanel.hidden = false;
                }

            });

        });

    });

});

document.addEventListener('DOMContentLoaded', function () {

    const reviewModal = document.querySelector('[data-review-modal]');
    const openButton = document.querySelector('[data-review-modal-open]');
    const closeButton = document.querySelector('[data-review-modal-close]');

    if (!reviewModal || !openButton || !closeButton) {
        return;
    }

    openButton.addEventListener('click', function () {
        reviewModal.hidden = false;
        document.body.classList.add('review-modal-open');
    });

    closeButton.addEventListener('click', function () {
        reviewModal.hidden = true;
        document.body.classList.remove('review-modal-open');
    });

    reviewModal.addEventListener('click', function (event) {
        if (event.target === reviewModal) {
            reviewModal.hidden = true;
            document.body.classList.remove('review-modal-open');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !reviewModal.hidden) {
            reviewModal.hidden = true;
            document.body.classList.remove('review-modal-open');
        }
    });

});

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.product-gallery').forEach(function (gallery) {

        const mainImage = gallery.querySelector('.main-product-image');
        const thumbnails = gallery.querySelectorAll('.thumb');

        if (!mainImage || !thumbnails.length) {
            return;
        }

        thumbnails.forEach(function (thumbnail) {

            thumbnail.addEventListener('click', function () {

                const imageUrl = thumbnail.dataset.galleryImage;

                if (!imageUrl) {
                    return;
                }

                mainImage.src = imageUrl;
                mainImage.srcset = '';

                thumbnails.forEach(function (item) {
                    item.classList.remove('active');
                });

                thumbnail.classList.add('active');

            });

        });

    });

});