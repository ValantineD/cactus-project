import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

new TomSelect('#activity_form_themes', {
    plugins: ['remove_button'],
    maxItems: 2,
    placeholder: 'Choisissez un Thème...',
    persist: false,
    create: true,

    function() {
        this.input.querySelectorAll('option').forEach(option => {
            if (this.options[option.value]) {
                this.options[option.value].icon = option.dataset.icon;
            }
        });
    },

    render: {
        option: function (data, escape) {
            return `<div class="d-flex align-items-center justify-content-start gap-1">
                <img class="me-2" src="${escape(data.icon)}" alt="${escape(data.text)}" style="height: 20px;">
                <span style="font-size: 20px"><strong>${escape(data.text)}</strong></span>
            </div>`;
        },
        item: function (data, escape) {
            return `<div class="d-flex align-items-center align" style="background: var(--primary-color)">
                <img class="me-2" src="${escape(data.icon)}" alt="${escape(data.text)}" style="height: 18px">
                <span style="font-size: 20px"><strong>${escape(data.text)}</strong></span>
            </div>`;
        }
    }

});
