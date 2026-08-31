import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

window.jQuery = window.$ = $;
select2(window, $);

function formatRupiah(input) {
    let number_string = input.value.replace(/[^0-9]/g, '').toString();

    if (number_string.includes('.')) {
        number_string = number_string.split('.')[0];
    }

    const sisa = number_string.length % 3;
    let rupiah = number_string.substr(0, sisa);
    const ribuan = number_string.substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    input.value = rupiah;
}

document.addEventListener('input', (e) => {
    if (e.target.matches('[data-rupiah]')) {
        formatRupiah(e.target);
    }
});

const form = document.querySelector('.js-payment-form');

if (form) {
    const amountInput = document.getElementById('amount');
    const lateFeeInput = document.getElementById('late_fee');
    const totalDisplay = document.getElementById('total_display');

    function parseRupiah(value) {
        return parseInt((value || '0').replace(/\./g, ''), 10) || 0;
    }

    function updateTotal() {
        if (!totalDisplay) return;
        const total = parseRupiah(amountInput?.value) + parseRupiah(lateFeeInput?.value);
        totalDisplay.value = total.toLocaleString('id-ID');
    }

    form.addEventListener('submit', () => {
        if (amountInput && amountInput.value) {
            amountInput.value = amountInput.value.replace(/\./g, '').replace(/,/g, '');
        }
        if (lateFeeInput && lateFeeInput.value) {
            lateFeeInput.value = lateFeeInput.value.replace(/\./g, '').replace(/,/g, '');
        }

        const btn = form.querySelector('[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });

    if (amountInput && amountInput.value) {
        formatRupiah(amountInput);
    }
    if (lateFeeInput && lateFeeInput.value) {
        formatRupiah(lateFeeInput);
    }

    updateTotal();

    [amountInput, lateFeeInput].forEach((el) => {
        if (el) el.addEventListener('input', updateTotal);
    });

    const tenantSelect = document.getElementById('tenant_id');
    if (tenantSelect) {
        $(tenantSelect).select2({
            placeholder: 'Pilih Penghuni',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });

        if (tenantSelect.value && amountInput && !amountInput.value) {
            tenantSelect.dispatchEvent(new Event('change'));
        }

        $(tenantSelect).on('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            let price = selectedOption.getAttribute('data-price');
            const nextPeriod = selectedOption.getAttribute('data-next-period');

            if (price) {
                if (price.includes('.')) {
                    price = price.split('.')[0];
                }
                amountInput.value = price;
                formatRupiah(amountInput);
            } else {
                amountInput.value = '';
            }

            const periodInput = document.getElementById('period_month');
            if (periodInput && nextPeriod) {
                periodInput.value = nextPeriod;
            }

            updateTotal();
        });
    }
}

const fileInput = document.getElementById('receipt_file_input');
const previewImage = document.getElementById('receiptPreview');

if (fileInput && previewImage) {
    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        } else {
            previewImage.classList.add('hidden');
            previewImage.src = '#';
        }
    });
}
