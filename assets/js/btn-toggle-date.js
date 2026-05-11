document.addEventListener('DOMContentLoaded', function () {

    const btnExact    = document.getElementById('btnExact');
    const btnFlexible = document.getElementById('btnFlexible');
    const exactPicker = document.getElementById('exactDatePicker');

    if (btnFlexible) {
        btnFlexible.addEventListener('click', function () {
            btnFlexible.classList.add('date-toggle-btn--active');
            btnExact.classList.remove('date-toggle-btn--active');
            exactPicker.style.display = 'none';
        });
        btnExact.addEventListener('click', function () {
            btnExact.classList.add('date-toggle-btn--active');
            btnFlexible.classList.remove('date-toggle-btn--active');
            exactPicker.style.display = 'block';
        });
    }
});
