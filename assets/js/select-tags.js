import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.min.css';

(function($) {
    $('.select2-tags').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: 'Add tags',
    });
})($);

//
// import TomSelect from 'tom-select';
// import 'tom-select/dist/css/tom-select.css';
//
// document.querySelectorAll('.select2-tags').forEach((tag) => {
//     new TomSelect(tag, {
//         plugins: ['remove_button', 'drag_drop'],
//         persist: false,
//         create: true,
//         delimiter: [',', ' '],
//         placeholder: 'Add tags',
//         hideSelected: true,
//         maxItems: 10
//     });
// });
