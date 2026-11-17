
document.addEventListener('DOMContentLoaded', function () {
    console.log("Hello")

    const container = document.getElementById('northstar-trust-messages');
    const addButton = document.getElementById('northstar-add-trust-message');

    if (!container || !addButton) {
        return;
    }

    addButton.addEventListener('click', function () {

        const wrapper = document.createElement('div');
        wrapper.className = 'northstar-trust-message';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'northstar_trust_messages[]';
        input.className = 'widefat';
        input.placeholder = 'e.g. Secure checkout';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'button northstar-remove-trust-message';
        removeButton.textContent = 'Remove';

        wrapper.appendChild(input);
        wrapper.appendChild(removeButton);

        container.appendChild(wrapper);

    });

    container.addEventListener('click', function (event) {

        if (event.target.classList.contains('northstar-remove-trust-message')) {
            event.target.closest('.northstar-trust-message').remove();
        }

    });

});
