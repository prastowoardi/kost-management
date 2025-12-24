# Serrata Kos Management System


---

## 🛠 Instalasi Laravel (Main App)

1. **Clone & Install Dependencies**
    ```bash
    composer install
    npm install && npm run build

2. **Konfigurasi Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    
    Sesuaikan DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di file .env.

3. **Migrasi Database**
    ```bash
    php artisan migrate --seed

4. **Jalankan Aplikasi**
    ```bash
    php artisan serve
    npm run dev

🟢 **WhatsApp Gateway (Node.js + Puppeteer)**
    Gateway ini berfungsi merender HTML menjadi gambar transparan dan mengirimkannya via WhatsApp.

1. **Persiapan VPS Ubuntu (Wajib)**
    Puppeteer membutuhkan library sistem Linux agar Chrome dapat berjalan tanpa tampilan (headless). Jalankan ini di terminal VPS:
    ```bash
    sudo apt-get update && sudo apt-get install -y libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 libxkbcommon0 libxcomposite1 libxdamage1 libxrandr2 libgbm1 libasound2 libpango-1.0-0 libpangocairo-1.0-0 libxshmfence1 libx11-xcb1 fonts-liberation libxfixes3 libxrender1

2. **Instalasi Gateway**
    ```bash
    cd /var/www/kost-management/whatsapp-gateway
    npm install
    sudo npm install pm2 -g

3. Menjalankan & Cara Lihat QR Code
    Gunakan PM2 untuk menjalankan service di latar belakang:
    ```bash
    # Menjalankan aplikasi pertama kali
    pm2 start server.cjs --name "wa-gateway"

    # CARA LIHAT QR CODE (Untuk Scan)
    pm2 logs wa-gateway --lines 100
    ```

    Gunakan fitur "Tautkan Perangkat" di WhatsApp HP untuk scan QR yang muncul di terminal

4. **Cara Ganti Akun WhatsApp**
    - Jika ingin mengganti nomor WA pengirim:
    - Hentikan service: pm2 stop wa-gateway
    - Hapus sesi lama: rm -rf .wwebjs_auth (di dalam folder whatsapp-gateway)
    - Mulai ulang: pm2 restart wa-gateway
    - Scan ulang: Jalankan pm2 logs wa-gateway untuk melihat QR baru.