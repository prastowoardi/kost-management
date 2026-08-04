const root = document.getElementById('dashboard-root');
const sendUrl = root ? root.dataset.sendUrl : '';
const csrf = window.appCsrf || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

document.addEventListener('click', (e) => {
    const btn = e.target.closest('.send-wa-btn');
    if (!btn) return;

    e.preventDefault();

    const tenantId = btn.dataset.id;
    const tenantName = btn.dataset.name;
    const dueDate = btn.dataset.due;

    Swal.fire({
        title: 'Kirim Tagihan?',
        text: `Kirim pesan WhatsApp otomatis ke ${tenantName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kirim Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang Mengirim...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    tenant_id: tenantId,
                    due_date: dueDate,
                    days_left: btn.dataset.days
                })
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil!', data.message, 'success');
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server/gateway.', 'error');
                });
        }
    });
});
