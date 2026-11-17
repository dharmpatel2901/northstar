document.addEventListener('click', function (event) {

    const minusButton = event.target.closest('.qty-minus');
    const plusButton  = event.target.closest('.qty-plus');

    if (!minusButton && !plusButton) {
        return;
    }

    const quantityWrapper = event.target.closest('.qty');
    const quantityInput   = quantityWrapper.querySelector('input.qty');

    if (!quantityInput) {
        return;
    }

    const min = parseFloat(quantityInput.min) || 1;
    const max = quantityInput.max ? parseFloat(quantityInput.max) : Infinity;
    const step = parseFloat(quantityInput.step) || 1;

    let value = parseFloat(quantityInput.value) || min;

    if (minusButton) {
        value = Math.max(min, value - step);
    }

    if (plusButton) {
        value = Math.min(max, value + step);
    }

    quantityInput.value = value;

    quantityInput.dispatchEvent(new Event('change', {
        bubbles: true
    }));

});