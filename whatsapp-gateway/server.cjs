const originalWrite = process.stdout.write;
process.stdout.write = function (chunk, encoding, callback) {
    if (typeof chunk === 'string' && chunk.includes('Closing session')) return true;
    return originalWrite.apply(process.stdout, arguments);
};

const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require("@whiskeysockets/baileys");
const { Boom } = require("@hapi/boom");
const puppeteer = require('puppeteer');
const qrcode = require("qrcode-terminal");
const express = require("express");
const pino = require("pino");
const crypto = require("crypto");
const os = require("os");
const fs = require("fs");

// Loader .env sederhana agar WA_API_KEY bisa dibaca saat dijalankan via pm2
// tanpa dependency tambahan (file .env diletakkan di folder whatsapp-gateway).
(function loadEnv() {
    const envPath = `${__dirname}/.env`;
    if (!fs.existsSync(envPath)) return;

    for (const line of fs.readFileSync(envPath, 'utf8').split('\n')) {
        const match = line.match(/^\s*([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)\s*$/);
        if (!match || process.env[match[1]] !== undefined) continue;

        let value = match[2];
        if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
            value = value.slice(1, -1);
        }
        process.env[match[1]] = value;
    }
})();

// ===== AUTENTIKASI API KEY =====
// Semua request wajib mengirim header X-API-Key yang cocok dengan WA_API_KEY.
// Tanpa ini, siapa pun yang bisa menjangkau port gateway bisa memakai nomor WA.
const API_KEY = process.env.WA_API_KEY;

if (!API_KEY || API_KEY.length < 32) {
    console.error('❌ FATAL: WA_API_KEY belum diatur atau terlalu pendek (min. 32 karakter).');
    console.error('   Buat key acak dengan: node -e "console.log(require(\'crypto\').randomBytes(32).toString(\'hex\'))"');
    console.error('   Lalu simpan di whatsapp-gateway/.env dan di .env Laravel (WHATSAPP_GATEWAY_API_KEY).');
    process.exit(1);
}

function verifyApiKey(req) {
    const provided = req.get('X-API-Key') || '';

    if (provided.length !== API_KEY.length) {
        return false;
    }

    // timingSafeEqual mencegah serangan timing pada perbandingan string
    return crypto.timingSafeEqual(Buffer.from(provided), Buffer.from(API_KEY));
}

function requireApiKey(req, res, next) {
    if (!verifyApiKey(req)) {
        console.warn(`⚠️  Request ditolak: API key tidak valid (IP: ${req.ip})`);
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }
    next();
}

const app = express();
app.use(express.json({ limit: '2mb' }));

let sock;

// Deteksi Path Chrome agar Puppeteer bisa jalan di Windows/Mac/Linux
const chromePath = (() => {
    switch (os.platform()) {
        case 'linux':
            return '/usr/bin/google-chrome';
        case 'darwin':
            return '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
        case 'win32':
            return 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
        default:
            return undefined;
    }
})();

// Instance browser di-reuse antar request (launch Chrome itu mahal).
// Jika browser crash/terputus, instance baru akan diluncurkan otomatis.
let browser = null;

async function getBrowser() {
    if (browser && browser.connected) {
        return browser;
    }

    if (browser) {
        try {
            await browser.close();
        } catch (_) {
            // abaikan, browser memang sudah mati
        }
        browser = null;
    }

    console.log('🚀 Meluncurkan instance Chrome headless...');

    // --no-sandbox hanya dipakai jika benar-benar berjalan sebagai root
    // (mis. container/VPS). Jika bukan root, sandbox Chrome tetap aktif.
    const isRoot = os.platform() !== 'win32' && typeof process.getuid === 'function' && process.getuid() === 0;
    const args = ['--disable-dev-shm-usage'];
    if (isRoot) {
        args.push('--no-sandbox', '--disable-setuid-sandbox');
    }

    browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: true,
        args
    });
    browser.once('disconnected', () => {
        console.log('⚠️  Chrome terputus, akan diluncurkan ulang saat dibutuhkan.');
        browser = null;
    });

    return browser;
}

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
    
    const { version, isLatest } = await fetchLatestBaileysVersion();
    console.log(` Menggunakan WA Web v${version.join('.')}, isLatest: ${isLatest}`);

    sock = makeWASocket({
        version,
        auth: state,
        logger: pino({ level: 'silent' }),
        browser: ["Ubuntu", "Chrome", "20.0.04"], 
        connectTimeoutMs: 60000,
        defaultQueryTimeoutMs: undefined,
        keepAliveIntervalMs: 30000,
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log("\n[!] SCAN QR CODE SEKARANG:");
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            const error = lastDisconnect?.error;
            const statusCode = error?.output?.statusCode || error?.code;
            
            let reasonText = "Unknown Reason";
            for (const [key, value] of Object.entries(DisconnectReason)) {
                if (value === statusCode) {
                    reasonText = key; // Mengambil nama variabel (misal: 'loggedOut')
                    break;
                }
            }

            console.log(`\n[!] KONEKSI TERPUTUS!`);
            console.log(`    Status Code : ${statusCode}`);
            console.log(`    Reason      : ${reasonText}`);
            console.log(`    Detail      : ${error?.message || 'No extra info'}`);

            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            if (shouldReconnect) {
                console.log("    Action    : Menyambung ulang dalam 5 detik...\n");
                setTimeout(() => connectToWhatsApp(), 5000);
            } else {
                console.log("    Action    : Sesi berakhir (Logged Out). Hapus folder auth dan scan ulang.\n");
            }
        } else if (connection === 'open') {
            console.log('\n✅ WHATSAPP GATEWAY BERHASIL TERHUBUNG!');
        }
    });
}

app.post('/send-message', requireApiKey, async (req, res) => {
    const { number, message } = req.body;
    try {
        let formattedNumber = number.replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const jid = `${formattedNumber}@s.whatsapp.net`;

        await sock.sendMessage(jid, { text: message });
        
        console.log(`✅ Pesan Berhasil Dikirim ke: ${formattedNumber}`);
        console.log(`Pesan: ${message}`);
        res.json({ status: 'success' });
    } catch (err) {
        console.error("❌ Gagal kirim teks:", err.message);
        res.status(500).json({ status: 'error', message: err.message });
    }
});

app.post('/send-image', requireApiKey, async (req, res) => {
    const { number, html, message } = req.body;
    let page = null;

    if (!html) return res.status(400).json({ status: 'error', message: 'HTML content missing' });

    try {
        let formattedNumber = number.replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const jid = `${formattedNumber}@s.whatsapp.net`;

        console.log(`\n--- Proses Render Kwitansi ---`);
        console.log(`Tujuan: ${formattedNumber}`);

        const browserInstance = await getBrowser();
        page = await browserInstance.newPage();

        // Hardening: HTML dari request tidak boleh mengeksekusi script
        // atau memuat resource eksternal (mencegah XSS & SSRF).
        await page.setJavaScriptEnabled(false);
        await page.setRequestInterception(true);
        page.on('request', (request) => {
            if (/^https?:/i.test(request.url())) {
                request.abort();
            } else {
                request.continue();
            }
        });

        // Set ukuran layar agar screenshot pas
        await page.setViewport({ width: 750, height: 1000, deviceScaleFactor: 2 });
        
        // Masukkan HTML dari Laravel
        await page.setContent(html, { waitUntil: 'domcontentloaded' });

        // Tunggu render
        await new Promise(resolve => setTimeout(resolve, 1000));

        const element = await page.$('.card');
        if (!element) throw new Error("Elemen dengan class '.card' tidak ditemukan di HTML!");

        // Ambil foto elemen .card
        const imageBuffer = await element.screenshot({ omitBackground: true });

        // Kirim
        await sock.sendMessage(jid, { 
            image: imageBuffer, 
            caption: message 
        });

        console.log(`Pesan: ${message}`);
        console.log(`✅ Kwitansi Berhasil dikirim ke ${formattedNumber}`);
        res.json({ status: 'success' });

    } catch (err) {
        console.error("❌ Gagal render/kirim gambar:", err.message);
        res.status(500).json({ status: 'error', message: err.message });
    } finally {
        if (page) await page.close().catch(() => {});
    }
});

app.listen(3000, () => {
    console.log("Server API berjalan di port 3000");
    connectToWhatsApp();
});