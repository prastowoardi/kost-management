const csrf = window.appCsrf || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

function sendWhatsApp(paymentId, tenantName) {
    Swal.fire({
        title: 'Kirim Kwitansi?',
        text: `Kirim kwitansi ke WhatsApp ${tenantName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Ya, Kirim!',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/payments/${paymentId}/send-wa`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then((response) => {
                    if (!response.ok) throw new Error('Gagal mengirim pesan');
                    return response.json();
                })
                .catch((error) => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Terkirim!',
                text: 'Kwitansi telah dikirim oleh sistem.',
                icon: 'success'
            });
        }
    });
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-send-wa]');
    if (!btn) return;

    e.preventDefault();
    sendWhatsApp(btn.dataset.sendWa, btn.dataset.tenantName);
});

const newPayment = document.getElementById('new-payment-data');
if (newPayment && newPayment.dataset.uuid) {
    sendWhatsApp(newPayment.dataset.uuid, newPayment.dataset.name);
}
