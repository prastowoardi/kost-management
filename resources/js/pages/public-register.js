const form = document.querySelector('.register-form');

if (form) {
    form.addEventListener('submit', () => {
        window.showLoading('Sedang memproses pendaftaran...');
    });
}

const transferDetails = document.getElementById('transfer-details-container');
const receiptInput = document.getElementById('receipt_input');

function toggleTransferDetails(isTransfer) {
    if (!transferDetails) return;

    if (isTransfer) {
        transferDetails.classList.remove('hidden');
        if (receiptInput) receiptInput.setAttribute('required', 'required');
    } else {
        transferDetails.classList.add('hidden');
        if (receiptInput) {
            receiptInput.removeAttribute('required');
            receiptInput.value = '';
        }
    }
}

document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
    radio.addEventListener('change', () => {
        toggleTransferDetails(radio.value === 'transfer');
    });
});

const checkedMethod = document.querySelector('input[name="payment_method"]:checked');
if (checkedMethod) {
    toggleTransferDetails(checkedMethod.value === 'transfer');
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;

    e.preventDefault();

    const target = document.getElementById(btn.dataset.copy);
    if (!target) return;

    navigator.clipboard.writeText(target.innerText).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil disalin!',
            text: target.innerText + ' telah siap ditempel.',
            showConfirmButton: false,
            timer: 1200,
            toast: true,
            position: 'top-end'
        });
    }).catch((err) => {
        console.error('Gagal menyalin: ', err);
    });
});
