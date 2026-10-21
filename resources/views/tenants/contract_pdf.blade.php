<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Sewa Kos - {{ $tenant->name }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf-contract.css') }}">
</head>
<body>

    <div class="header">
        <h1>SERRATA KOST</h1>
        <p>Jl. Pandowoharjo, Kleben Moncosan, Mancasan, Kec. Sleman, Yogyakarta 55512.</p>
    </div>

    <div class="doc-meta">
        <span class="contract-title">SURAT PERJANJIAN SEWA MENYEWA KAMAR KOS</span>
        <span class="contract-number">Nomor: SPK/{{ date('Y') }}/K-{{ $tenant->room->room_number ?? '00' }}/{{ str_pad($tenant->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>

    <p class="paragraph">
        Pada hari ini, <strong>{{ \Carbon\Carbon::parse($tenant->entry_date ?? now())->translatedFormat('l, d F Y') }}</strong>, telah disepakati perjanjian sewa kamar kos antara pihak-pihak sebagai berikut:
    </p>

    <table class="party">
        <tr>
            <td class="label">1. Nama Pengelola / Pemilik</td>
            <td class="colon">:</td>
            <td><strong>Pengelola Serrata Kost</strong> (disebut sebagai <strong>PIHAK PERTAMA</strong>)</td>
        </tr>
        <tr>
            <td class="label">2. Nama Lengkap Penyewa</td>
            <td class="colon">:</td>
            <td><strong>{{ $tenant->name }}</strong> (disebut sebagai <strong>PIHAK KEDUA</strong>)</td>
        </tr>
        <tr>
            <td class="label">&nbsp;&nbsp;&nbsp;No. KTP / Identitas</td>
            <td class="colon">:</td>
            <td>{{ $tenant->id_card }}</td>
        </tr>
        <tr>
            <td class="label">&nbsp;&nbsp;&nbsp;No. Telepon / WhatsApp</td>
            <td class="colon">:</td>
            <td>{{ $tenant->phone }}</td>
        </tr>
        <tr>
            <td class="label">&nbsp;&nbsp;&nbsp;Email</td>
            <td class="colon">:</td>
            <td>{{ $tenant->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">&nbsp;&nbsp;&nbsp;Alamat Asal</td>
            <td class="colon">:</td>
            <td>{{ $tenant->address }}</td>
        </tr>
        <tr>
            <td class="label">&nbsp;&nbsp;&nbsp;Kontak Darurat</td>
            <td class="colon">:</td>
            <td>{{ $tenant->emergency_contact_name ? $tenant->emergency_contact_name.' ('.$tenant->emergency_contact_phone.')' : '-' }}</td>
        </tr>
    </table>

    <p class="paragraph">
        Kedua belah pihak sepakat mengikatkan diri dalam perjanjian sewa kamar dengan ketentuan sebagai berikut:
    </p>

    <div class="section-title">PASAL 1: OBJEK SEWA & JANGKA WAKTU</div>
    <ol>
        <li>PIHAK PERTAMA menyewakan kamar nomor <strong>{{ $tenant->room->room_number ?? '-' }}</strong> (Tipe: {{ ucfirst($tenant->room->type ?? 'Standard') }}) di Serrata Kost kepada PIHAK KEDUA.</li>
        <li>Sewa dimulai sejak tanggal <strong>{{ \Carbon\Carbon::parse($tenant->entry_date)->translatedFormat('d F Y') }}</strong> dan diperpanjang secara bulanan sesuai pembayaran rutin.</li>
    </ol>

    <div class="section-title">PASAL 2: HARGA SEWA & PEMBAYARAN</div>
    <ol>
        <li>Harga sewa kamar disepakati sebesar <strong>Rp {{ number_format($tenant->room->price ?? 0, 0, ',', '.') }}</strong> per bulan.</li>
        <li>Pembayaran sewa wajib dilunasi paling lambat pada tanggal jatuh tempo setiap bulannya (setiap tanggal {{ \Carbon\Carbon::parse($tenant->entry_date)->day }}).</li>
        <li>Pembayaran dapat ditransfer melalui rekening resmi Serrata Kost atau secara cash kepada pengelola Serrata Kost.</li>
    </ol>

    <div class="section-title">PASAL 3: HAK & KEWAJIBAN SERTA TATA TERTIB</div>
    <ol>
        <li>PIHAK KEDUA wajib menjaga fasilitas kamar, kebersihan, ketertiban, dan ketenangan bersama di lingkungan kos.</li>
        <li>Dilarang keras membawa, menyimpan, atau mengonsumsi narkoba, minuman keras, senjata tajam/api, serta dilarang melakukan tindakan asusila/perjudian di area kos.</li>
        <li>Tamu lawan jenis dilarang menginap di dalam kamar. Tamu berkunjung wajib lapor dan mematuhi batas jam bertamu (pukul 22.00 WIB).</li>
        <li>Kerusakan fasilitas kamar yang disebabkan oleh kelalaian atau kesengajaan PIHAK KEDUA menjadi tanggung jawab PIHAK KEDUA untuk biaya perbaikan atau penggantian.</li>
    </ol>

    <div class="section-title">PASAL 4: PENGAKHIRAN SEWA</div>
    <ol>
        <li>Apabila PIHAK KEDUA bermaksud mengakhiri masa sewa (check-out), wajib memberikan pemberitahuan resmi sekurang-kurangnya <strong>7 (tujuh) hari</strong> sebelum tanggal jatuh tempo berikutnya.</li>
        <li>Pada saat keluar, kamar harus dalam kondisi bersih, kosong dari barang pribadi, dan kunci diserahkan kembali kepada PIHAK PERTAMA.</li>
    </ol>

    <p class="paragraph closing">
        Demikian surat perjanjian ini dibuat atas kesadaran bersama tanpa paksaan dari pihak manapun untuk dipergunakan sebagaimana mestinya.
    </p>

    <div class="signatures">
        <table>
            <tr>
                <td>
                    <p>PIHAK PERTAMA<br>Pengelola Serrata Kost,</p>
                    <div class="sig-space"></div>
                    <p class="sig-name">( Pengelola Kos )</p>
                </td>
                <td>
                    <p>PIHAK KEDUA<br>Penyewa Kamar,</p>
                    <div class="sig-space"></div>
                    <p class="sig-name">( {{ $tenant->name }} )</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
