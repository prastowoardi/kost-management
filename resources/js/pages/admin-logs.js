const select = document.querySelector('.select2-action');

if (select && window.jQuery) {
    const baseUrl = select.dataset.logsUrl || '';
    let currentParams = {};

    try {
        currentParams = JSON.parse(select.dataset.logsParams || '{}');
    } catch (e) {
        currentParams = {};
    }

    window.buildUrl = function (params) {
        const qs = Object.keys(params)
            .filter((k) => params[k] != null && params[k] !== '')
            .map((k) => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
            .join('&');

        return qs ? baseUrl + '?' + qs : baseUrl;
    };

    jQuery(function () {
        jQuery('.select2-action')
            .select2({
                placeholder: 'Cari aksi...',
                allowClear: true,
                width: '100%'
            })
            .on('change', function () {
                const val = jQuery(this).val();
                const params = Object.assign({}, currentParams);

                if (val) {
                    params.action = val;
                } else {
                    delete params.action;
                }

                window.location.href = window.buildUrl(params);
            });
    });
}
