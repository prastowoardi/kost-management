const originalWrite = process.stdout.write;
process.stdout.write = function (chunk, encoding, callback) {
    if (typeof chunk === 'string' && chunk.includes('Closing session')) return true;
    return originalWrite.apply(process.stdout, arguments);
};

const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require("@whiskeysockets/baileys");
const puppeteer = require('puppeteer');
const qrcode = require("qrcode-terminal");
const express = require("express");
const pino = require("pino");
const crypto = require("crypto");
const os = require("os");
const fs = require("fs");
const PQueue = require('p-queue').default || require('p-queue');

// Import monitoring & alerting
const { updateMetrics, getHealthStatus, metrics } = require('./monitoring');
const { sendAlert } = require('./alerts');
require('./health-checker'); // Auto-start periodic checks

// Loader .env sederhana agar WA_API_KEY bisa dibaca saat dijalankan via pm2
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
const API_KEY = process.env.WA_API_KEY;

if (!API_KEY || API_KEY.length < 32) {
    console.error('❌ FATAL: WA_API_KEY belum diatur atau terlalu pendek (min. 32 karakter).');
    console.error('   Buat key acak dengan: node -e "console.log(require(\'crypto\').randomBytes(32).toString(\'hex\'))"');
    console.error('   Lalu simpan di whatsapp-gateway/.env dan di .env Laravel (WHATSAPP_GATEWAY_API_KEY).');
    process.exit(1);
}

function verifyApiKey(req) {
    const provided = req.get('X-API-Key') || '';
    if (provided.length !== API_KEY.length) return false;
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
let renderPage = null;
const renderQueue = new PQueue({ concurrency: 1, timeout: 120000 });

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
    const args = ['--disable-dev-shm-usage', '--disable-background-networking', '--disable-renderer-backgrounding', '--disable-background-timer-throttling'];
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
        renderPage = null;
    });

    return browser;
}

async function getRenderPage() {
    const browserInstance = await getBrowser();
    if (renderPage && !renderPage.isClosed()) {
        return renderPage;
    }
    renderPage = await browserInstance.newPage();
    await renderPage.setJavaScriptEnabled(false);
    await renderPage.setRequestInterception(true);
    renderPage.on('request', (request) => {
        if (/^https?:/i.test(request.url())) {
            request.abort();
        } else {
            request.continue();
        }
    });
    await renderPage.setViewport({ width: 750, height: 1000, deviceScaleFactor: 2 });
    return renderPage;
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

            updateMetrics('connection', false, error);

            if (shouldReconnect) {
                console.log("    Action    : Menyambung ulang dalam 5 detik...\n");
                setTimeout(() => connectToWhatsApp(), 5000);
            } else {
                console.log("    Action    : Sesi berakhir (Logged Out). Hapus folder auth dan scan ulang.\n");
            }
        } else if (connection === 'open') {
            console.log('\n✅ WHATSAPP GATEWAY BERHASIL TERHUBUNG!');
            updateMetrics('connection', true);
        }
    });
}

function isWhatsAppConnected() {
    return Boolean(sock?.user);
}

function normalizeNumber(number) {
    if (typeof number !== 'string' && typeof number !== 'number') return null;

    const digits = String(number).replace(/\D/g, '');
    if (digits.length < 9) return null;

    if (digits.startsWith('0')) return '62' + digits.substring(1);
    if (digits.startsWith('62')) return digits;
    return '62' + digits;
}

function maskPhone(number) {
    if (!number) return '';
    const str = String(number);
    if (str.length <= 6) return '***';
    return str.slice(0, 4) + '****' + str.slice(-2);
}

function logBody(req) {
    let sanitizedBody = null;
    if (req.body && typeof req.body === 'object') {
        sanitizedBody = { ...req.body };
        if (sanitizedBody.number) {
            sanitizedBody.number = maskPhone(sanitizedBody.number);
        }
        if (sanitizedBody.html) {
            sanitizedBody.html = `[HTML Content: ${sanitizedBody.html.length} chars]`;
        }
        if (sanitizedBody.message && sanitizedBody.message.length > 50) {
            sanitizedBody.message = sanitizedBody.message.slice(0, 50) + '...';
        }
    }

    const summary = {
        ip: req.ip,
        path: req.path,
        contentType: req.get('content-type'),
        keys: req.body ? Object.keys(req.body) : null,
        body: sanitizedBody,
    };
    console.log('📥 REQUEST INCOMING:', JSON.stringify(summary));
}

app.use((req, res, next) => {
    if (req.method === 'POST') logBody(req);
    next();
});

app.post('/send-message', requireApiKey, async (req, res) => {
    const { number, message } = req.body;

    if (typeof message !== 'string' || !message.trim()) {
        return res.status(400).json({ status: 'error', message: 'Field "message" wajib diisi' });
    }

    const formattedNumber = normalizeNumber(number);
    if (!formattedNumber) {
        return res.status(400).json({ status: 'error', message: 'Field "number" wajib diisi dan harus berupa nomor WA yang valid' });
    }

    if (!isWhatsAppConnected()) {
        updateMetrics('connection', false);
        return res.status(503).json({ status: 'error', message: 'WhatsApp belum terhubung, coba beberapa saat lagi' });
    }

    try {
        const jid = `${formattedNumber}@s.whatsapp.net`;
        await sock.sendMessage(jid, { text: message });
        
        console.log(`✅ Pesan Berhasil Dikirim ke: ${formattedNumber}`);
        console.log(`Pesan: ${message}`);
        
        updateMetrics('message', true);
        res.json({ status: 'success' });
    } catch (err) {
        console.error("❌ Gagal kirim teks:", err.message);
        updateMetrics('message', false, err);
        res.status(500).json({ status: 'error', message: err.message });
    }
});

app.post('/send-image', requireApiKey, async (req, res) => {
    const { number, html, message } = req.body;

    if (!html) {
        console.log('⚠️  /send-image: html missing. body keys =', req.body ? Object.keys(req.body) : null, 'contentType =', req.get('content-type'));
        return res.status(400).json({ status: 'error', message: 'HTML content missing' });
    }

    const formattedNumber = normalizeNumber(number);
    if (!formattedNumber) return res.status(400).json({ status: 'error', message: 'Field "number" wajib diisi dan harus berupa nomor WA yang valid' });
    if (!isWhatsAppConnected()) {
        updateMetrics('connection', false);
        return res.status(503).json({ status: 'error', message: 'WhatsApp belum terhubung, coba beberapa saat ini' });
    }

    try {
        const result = await renderQueue.add(async () => {
            const jid = `${formattedNumber}@s.whatsapp.net`;
            let page = null;

            console.log(`\n--- Proses Render Kwitansi ---`);
            console.log(`Tujuan: ${formattedNumber}`);

            try {
                page = await getRenderPage();
                updateMetrics('chrome', true);

                await page.setContent(html, { waitUntil: 'domcontentloaded' });
                await page.waitForSelector('.card', { timeout: 5000 }).catch(() => {});
                try {
                    await page.evaluateHandle('document.fonts.ready');
                } catch (_) {}

                const element = await page.$('.card');
                if (!element) throw new Error("Elemen dengan class '.card' tidak ditemukan di HTML!");

                const imageBuffer = await element.screenshot({ omitBackground: true });

                await sock.sendMessage(jid, { 
                    image: imageBuffer, 
                    caption: message 
                });

                console.log(`Pesan: ${message}`);
                console.log(`✅ Kwitansi Berhasil dikirim ke ${formattedNumber}`);
                
                updateMetrics('image', true);
                updateMetrics('message', true);
            } catch (err) {
                if (page && !page.isClosed()) {
                    try {
                        await page.goto('about:blank', { waitUntil: 'domcontentloaded' }).catch(() => {});
                    } catch (_) {}
                }
                throw err;
            }
        });

        res.json({ status: 'success' });
    } catch (err) {
        console.error("❌ Gagal render/kirim gambar:", err.message);
        updateMetrics('message', false, err);
        res.status(500).json({ status: 'error', message: err.message });
    }
});

// Health check endpoint (public - no API key required)
app.get('/health', (req, res) => {
    const health = getHealthStatus();
    res.json(health);
});

const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || '127.0.0.1';

app.listen(PORT, HOST, () => {
    console.log(`🚀 Server API berjalan di http://${HOST}:${PORT}`);
    console.log(`📊 Health check: http://${HOST}:${PORT}/health`);
    
    // Send startup alert
    sendAlert('WhatsApp Gateway Started', 'Service berhasil dijalankan dan siap menerima request.', 'info');
    
    connectToWhatsApp();
});