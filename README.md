# Serrata Kos System

Serrata Kos adalah sistem manajemen kos terintegrasi yang menggabungkan kekuatan **Laravel** untuk pengelolaan data dan **Node.js (Baileys + Puppeteer)** sebagai engine otomatisasi pengiriman pesan dan kwitansi digital via WhatsApp.

---

## 🛠 1. Instalasi Laravel (Main App)

Aplikasi utama berfungsi sebagai dashboard admin untuk manajemen penghuni, kamar, dan keuangan.

### **Langkah Instalasi:**
1. **Clone & Install Dependencies**
   ```bash
   git clone [https://github.com/username/serrata-kos.git](https://github.com/username/serrata-kos.git)
   cd serrata-kos
   composer install
   npm install && npm run build

2. **Konfigurasi Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate

Penting: Atur konfigurasi database (DB_DATABASE, DB_USERNAME, DB_PASSWORD) dan arahkan WA_GATEWAY_URL=http://localhost:3000 di dalam file .env.

3. **Migrasi Database**
    ```bash
    php artisan migrate --seed

4. **Jalankan Aplikasi**
    ```bash
    php artisan serve
    npm run dev

## 💬 2. WhatsApp Gateway (Node.js + Baileys + Puppeteer)
Gateway ini berfungsi menerima data dari Laravel, merender HTML menjadi gambar kwitansi menggunakan Puppeteer, dan mengirimkannya via WhatsApp.

### **Langkah Instalasi:**
1. **Persiapan VPS Ubuntu (Wajib)**
    Puppeteer membutuhkan library sistem Linux agar Chrome dapat berjalan tanpa tampilan (headless). Jalankan ini di terminal VPS:
    ```bash
    install baileys
    npm install @whiskeysockets/baileys @hapi/boom qrcode-terminal express pino
    
    lalu install package:
    sudo apt-get update && sudo apt-get install -y libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 libxkbcommon0 libxcomposite1 libxdamage1 libxrandr2 libgbm1 libasound2 libpango-1.0-0 libpangocairo-1.0-0 libxshmfence1 libx11-xcb1 fonts-liberation libxfixes3 libxrender1

2. **Instalasi Gateway**
    ```bash
    cd whatsapp-gateway
    npm install
    sudo npm install pm2 -g
3. **Menjalankan & Menghubungkan WhatsApp**
Gunakan PM2 agar service tetap berjalan 24/7 meskipun terminal ditutup.
    ```bash
    # Menjalankan aplikasi pertama kali
    pm2 start server.cjs --name "wa-gateway"

    # Cara melihat QR Code untuk Scan
    pm2 logs wa-gateway --lines 100
**Instruksi:** Buka WhatsApp di HP > Perangkat Tertaut > Tautkan Perangkat > Scan QR yang muncul di terminal.

**Cara Ganti Akun WhatsApp**
    - Jika ingin mengganti nomor WA pengirim:
    - Hentikan service: pm2 stop wa-gateway
    - Hapus sesi lama: rm -rf .wwebjs_auth (di dalam folder whatsapp-gateway)
    - Mulai ulang: pm2 restart wa-gateway
    - Scan ulang: Jalankan pm2 logs wa-gateway untuk melihat QR baru.

## 💬 3. Maintenance & Reset Session

| Action | How to |
| :--- | :--- |
| **Ganti Nomor WhatsApp** | stop service `pm2 stop wa-gateway`,
| | hapus folder `auth_info_baileys`| 
| | restart PM2 `pm2 restart wa-gateway`|
| | scan QR baru |
| **Cek Status Server** | `pm2 status` untuk memastikan gateway online |
| **Restart Gateway** | `pm2 restart wa-gateway` |
| **Lihat Log Error** | `pm2 logs wa-gateway` |