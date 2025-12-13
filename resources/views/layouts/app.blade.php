<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        @php
            $successMessage = session('success') ? str_replace("'", "\'", session('success')) : null;
            $errorMessage = session('error') ? str_replace("'", "\'", session('error')) : null;
        @endphp

        <script>
            @if($successMessage)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json($successMessage),
                    showConfirmButton: false,
                    timer: 2000,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if($errorMessage)
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: @json($errorMessage),
                    showConfirmButton: true
                });
            @endif

            // Delete Confirmation Function
            function confirmDelete(event, formId, itemName = 'data ini') {
                event.preventDefault();
                
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
            }

            // General Confirmation Function
            function confirmAction(message, confirmText = 'Ya, Lanjutkan!') {
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
            }

            // Status Update Confirmation
            function confirmStatusUpdate(event, formId, newStatus) {
                event.preventDefault();
                
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
            }

            // Loading Alert
            function showLoading(message = 'Memproses...') {
                Swal.fire({
                    title: message,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // Success Toast
            function showSuccessToast(message) {
                Swal.fire({
                    icon: 'success',
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            // Error Toast
            function showErrorToast(message) {
                Swal.fire({
                    icon: 'error',
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        </script>

        <style>
            .swal2-popup {
                font-family: inherit;
            }
            
            .swal2-styled.swal2-confirm {
                padding: 10px 24px;
                font-weight: 600;
            }
            
            .swal2-styled.swal2-cancel {
                padding: 10px 24px;
                font-weight: 600;
            }
        </style>
    </body>
</html>
