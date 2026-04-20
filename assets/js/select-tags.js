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
