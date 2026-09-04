import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

window.jQuery = window.$ = $;
select2(window, $);

document.querySelectorAll('.log-select2').forEach((el) => {
    $(el)
        .select2({
            placeholder: 'Semua',
            allowClear: true,
            width: '100%',
        });
});
