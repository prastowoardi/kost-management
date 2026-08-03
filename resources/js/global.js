const csrfMeta = document.querySelector('meta[name="csrf-token"]');
window.appCsrf = csrfMeta ? csrfMeta.content : '';

window.confirmDelete = function (event, formId, itemName = 'data ini') {
    if (event) event.preventDefault();

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `${itemName} akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
};

window.confirmAction = function (message, confirmText = 'Ya, Lanjutkan!') {
    return new Promise((resolve) => {
        Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            resolve(result.isConfirmed);
        });
    });
};

const statusMessages = {
    'active': 'mengaktifkan',
    'inactive': 'menonaktifkan',
    'available': 'membuat kamar tersedia',
    'occupied': 'menandai kamar terisi',
    'maintenance': 'menandai kamar dalam perbaikan',
    'paid': 'menandai pembayaran lunas',
    'pending': 'menandai pembayaran pending',
    'resolved': 'menyelesaikan keluhan',
    'closed': 'menutup keluhan'
};

window.confirmStatusUpdate = function (event, formId, newStatus) {
    if (event) event.preventDefault();

    const message = statusMessages[newStatus] || 'mengubah status';

    Swal.fire({
        title: 'Update Status?',
        text: `Anda akan ${message}`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Update!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
};

window.showLoading = function (message = 'Memproses...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
};

window.showSuccessToast = function (message) {
    Swal.fire({
        icon: 'success',
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
};

window.showErrorToast = function (message) {
    Swal.fire({
        icon: 'error',
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
};

document.addEventListener('click', (e) => {
    if (!('ontouchstart' in window)) {
        const dateInput = e.target.closest('input[type="date"], input[type="month"]');
        if (dateInput && typeof dateInput.showPicker === 'function') {
            dateInput.showPicker();
        }
    }

    const deleteBtn = e.target.closest('[data-confirm-delete]');
    if (deleteBtn) {
        e.preventDefault();
        window.confirmDelete(e, deleteBtn.dataset.confirmDelete, deleteBtn.dataset.itemName || 'data ini');
        return;
    }

    const statusBtn = e.target.closest('[data-confirm-status]');
    if (statusBtn) {
        e.preventDefault();
        window.confirmStatusUpdate(e, statusBtn.dataset.confirmStatus, statusBtn.dataset.status);
        return;
    }

    const submitFormLink = e.target.closest('[data-submit-closest-form]');
    if (submitFormLink) {
        e.preventDefault();
        const form = submitFormLink.closest('form');
        if (form) form.submit();
        return;
    }

    const printBtn = e.target.closest('[data-print]');
    if (printBtn) {
        e.preventDefault();
        window.print();
        return;
    }
});

document.addEventListener('submit', (e) => {
    const form = e.target.closest('[data-confirm-delete-form]');
    if (form) {
        e.preventDefault();
        window.confirmDelete(e, form.id, form.dataset.confirmDeleteForm || 'data ini');
    }
});

document.addEventListener('change', (e) => {
    const select = e.target.closest('select[data-auto-submit]');
    if (select && select.form) {
        select.form.submit();
    }
});

const body = document.body;
const flashSuccess = body ? (body.getAttribute('data-flash-success') || '') : '';
const flashError = body ? (body.getAttribute('data-flash-error') || '') : '';

if (flashSuccess) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: flashSuccess,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        position: 'top-end'
    });
}

if (flashError) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: flashError,
        showConfirmButton: true
    });
}
