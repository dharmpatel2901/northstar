
(function ($) {
    'use strict';

    $(document).on('click', '.northstar-upload-image', function (event) {
        event.preventDefault();

        const button = $(this);
        const wrapper = button.closest('.northstar-image-field');
        const input = wrapper.find('input[type="hidden"]');
        const preview = wrapper.find('.northstar-image-preview');
        const removeButton = wrapper.find('.northstar-remove-image');

        const mediaFrame = wp.media({
            title: 'Select Hero Image',
            button: {
                text: 'Use this image'
            },
            library: {
                type: 'image'
            },
            multiple: false
        });

        mediaFrame.on('select', function () {
            const attachment = mediaFrame.state().get('selection').first().toJSON();

            input.val(attachment.id);

            const imageUrl = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;

            preview.html(
                $('<img>', {
                    src: imageUrl,
                    alt: '',
                    css: {
                        maxWidth: '100%',
                        height: 'auto'
                    }
                })
            );

            removeButton.prop('disabled', false);
        });

        mediaFrame.open();
    });

    $(document).on('click', '.northstar-remove-image', function (event) {
        event.preventDefault();

        const wrapper = $(this).closest('.northstar-image-field');

        wrapper.find('input[type="hidden"]').val('');
        wrapper.find('.northstar-image-preview').empty();

        $(this).prop('disabled', true);
    });

})(jQuery);
