import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

window.jQuery = window.$ = $;
select2(window, $);

const roomSelect = document.querySelector('#room_id');

if (roomSelect) {
    $(roomSelect).select2({
        placeholder: 'Pilih Kamar',
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: 0
    });
}
