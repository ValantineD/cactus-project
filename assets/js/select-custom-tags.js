import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

document.querySelectorAll('.select-custom-tags').forEach((tag) => {
    new TomSelect(tag, {
        plugins: ['remove_button', 'drag_drop', 'clear_button'],
        persist: false,
        create: true,
        delimiter: " ",
        placeholder: 'Ajouter des tags',
        maxItems: 10,
        createFilter: /^[a-zA-Z0-9\u00C0-\u017E]+$/,
        render: {
            option_create: function(data, escape) {
                return '<div class="create">Ajouter le tag <strong>' + escape(data.input) + '</strong></div>';
            },
            no_results: function (data, escape) {
                return '<div class="create">Votre tag ne peut pas contenir de charactère spécial.</div>';
            },
        },
    });
});
