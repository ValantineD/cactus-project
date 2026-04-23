import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

new TomSelect('#activity_form_themes', {
    plugins: ['remove_button'],
    maxItems: 2,
    placeholder: 'Choisissez un Thème...',

    function() {
        this.input.querySelectorAll('option').forEach(opt => {
            if (this.options[opt.value]) {
                this.options[opt.value].icon = opt.dataset.icon;
            }
        });
    },

    render: {
        option: function (data, escape) {
            return `<div>
                <img class="me-2" src="${escape(data.src)}" alt="${escape(data.text)}">
            </div>`;
        },
        item: function (data, escape) {
            return `<div>
                <img class="me-2" src="${escape(data.src)}" alt="${escape(data.text)}">
            </div>`;
        }
    }

});
