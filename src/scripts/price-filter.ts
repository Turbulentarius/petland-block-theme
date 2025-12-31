const slider = document.getElementById('price_slider') as HTMLInputElement;
const input = document.getElementById('price_input') as HTMLInputElement;
const button = document.getElementById('price_filter_apply') as HTMLButtonElement;

if (slider && input && button) {
    slider.addEventListener('input', () => input.value = slider.value);
    input.addEventListener('input', () => slider.value = input.value);
    button.addEventListener('click', () => {
        const url = new URL(window.location.href);
        url.searchParams.set('max_price', input.value);
        window.location.href = url.toString();
    });
}
