const originalWrite = process.stdout.write;
process.stdout.write = function (chunk, encoding, callback) {
    if (typeof chunk === 'string' && chunk.includes('Closing session')) return true;
    return originalWrite.apply(process.stdout, arguments);
};

const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion, downloadMediaMessage } = require("@whiskeysockets/baileys");
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

// ===== KONFIGURASI WEBHOOK PESAN MASUK =====
// Gateway meneruskan pesan yang masuk ke Laravel supaya bisa di-parse
// menjadi data (komplain, bukti bayar, transaksi keuangan, dll).
// Fitur ini opsional: tanpa diset, bot inbound tidak aktif tetapi
// pengiriman keluar (kwitansi/notifikasi) tetap berfungsi.
const LARAVEL_WEBHOOK_URL = process.env.LARAVEL_WEBHOOK_URL;
const WA_WEBHOOK_KEY = process.env.WA_WEBHOOK_KEY;

if (!LARAVEL_WEBHOOK_URL || !WA_WEBHOOK_KEY) {
    console.log('ℹ️  Webhook pesan masuk NONAKTIF (atur LARAVEL_WEBHOOK_URL & WA_WEBHOOK_KEY di .env untuk mengaktifkan bot).');
}

async function relayToLaravel(payload) {
    if (!LARAVEL_WEBHOOK_URL || !WA_WEBHOOK_KEY) return;

    try {
        const res = await fetch(LARAVEL_WEBHOOK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Webhook-Key': WA_WEBHOOK_KEY,
            },
            body: JSON.stringify(payload),
        });

        if (!res.ok) {
            console.error(`⚠️  Webhook ke Laravel gagal (HTTP ${res.status}): ${await res.text()}`);
        }
    } catch (err) {
        console.error('⚠️  Webhook error:', err.message);
    }
}

const app = express();
app.use(express.json({ limit: '10mb' }));

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

    // ===== MAPPING LID → PHONE =====
    // WhatsApp v7 mengirim pesan dengan JID @lid, bukan @s.whatsapp.net.
    // Kita bangun mapping dari contacts.upsert + signalRepository sebagai fallback.
    const lidToPhone = new Map();

    sock.ev.on('contacts.upsert', (contacts) => {
        for (const c of contacts) {
            if (c.lid && c.phoneNumber) {
                const lidNum = c.lid.split('@')[0];
                const phoneNum = c.phoneNumber.split('@')[0].split(':')[0];
                lidToPhone.set(lidNum, phoneNum);
                console.log(`📇 Contact sync: LID ${lidNum} -> phone ${phoneNum}`);
            }
        }
    });

    async function resolveLidToPhone(lidJid) {
        const lidNum = lidJid.split('@')[0];

        // 1. cek dari contacts.upsert cache
        if (lidToPhone.has(lidNum)) return lidToPhone.get(lidNum);

        // 2. coba via signalRepository (Baileys internal mapping)
        try {
            const pnJid = await sock.signalRepository.lidMapping.getPNForLID(lidJid);
            if (pnJid) {
                const phone = pnJid.split('@')[0].split(':')[0];
                lidToPhone.set(lidNum, phone);
                console.log(`🔗 Resolved LID ${lidNum} -> phone ${phone} (via signalRepository)`);
                return phone;
            }
        } catch (_) { /* ignore */ }

        return null;
    }

    // ===== BOT PESAN MASUK =====
    sock.ev.on('messages.upsert', (update) => {
        (async () => {
            const { messages, type } = update;
            if (type !== 'notify') return;

            for (const msg of messages) {
                if (!msg.message || msg.key.fromMe) continue;

                const jid = msg.key.remoteJid;
                const server = jid.split('@')[1];
                if (server !== 's.whatsapp.net' && server !== 'lid') continue;

                let phone;
                if (server === 'lid') {
                    phone = await resolveLidToPhone(jid);
                    if (!phone) {
                        console.log(`⚠️  LID ${jid} belum ter-resolve. Pesan di-skip.`);
                        continue;
                    }
                } else {
                    phone = jid.split('@')[0];
                }

                const name = msg.pushName || '';
                let text = extractIncomingText(msg);
                let media = null;

                if (msg.message.imageMessage) {
                    console.log(`📩 GAMBAR masuk dari ${phone}, sedang diunduh...`);
                    media = await downloadIncomingImage(msg);
                }

                console.log(`📩 PESAN MASUK dari ${name} (${phone}): ${text}${media ? ' [gambar]' : ''}`);
                await relayToLaravel({ phone, name, text, media });
            }
        })().catch(err => console.error('❌ messages.upsert error:', err));
    });

    // Populate mapping dari contacts yang sudah ada di state
    try {
        const creds = sock.authState?.creds;
        if (creds?.me?.lid && creds?.me?.id) {
            const lidNum = creds.me.lid.split('@')[0];
            const phoneNum = creds.me.id.split('@')[0];
            lidToPhone.set(lidNum, phoneNum);
        }
    } catch (_) {}

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
            const userJid = sock?.user?.id || 'unknown';
            console.log(`📱 Nomor WhatsApp aktif: ${userJid.split('@')[0]}${sock?.user?.name ? ' (' + sock.user.name + ')' : ''}`);
        }
    });
}

function extractIncomingText(msg) {
    const c = msg.message;
    if (c.conversation) return c.conversation;
    if (c.extendedTextMessage?.text) return c.extendedTextMessage.text;
    if (c.imageMessage?.caption) return c.imageMessage.caption;
    if (c.videoMessage?.caption) return c.videoMessage.caption;
    if (c.documentMessage?.caption) return c.documentMessage.caption;
    if (c.buttonsResponseMessage?.selectedDisplayText) return c.buttonsResponseMessage.selectedDisplayText;
    if (c.listResponseMessage?.singleSelectReply?.selectedRowId) return c.listResponseMessage.singleSelectReply.selectedRowId;

    return '';
}

async function downloadIncomingImage(msg) {
    try {
        let buffer;
        // Baileys v7: downloadMediaMessage(msg, options); fallback ke signature lama.
        try {
            buffer = await downloadMediaMessage(msg, {}, {
                logger: pino({ level: 'silent' }),
                reuploadRequest: sock ? sock.updateMediaMessage : undefined,
            });
        } catch (_) {
            buffer = await downloadMediaMessage(msg, 'buffer', {}, {
                logger: pino({ level: 'silent' }),
            });
        }

        const mime = msg.message.imageMessage?.mimetype || 'image/jpeg';
        return { mimetype: mime, data: buffer.toString('base64') };
    } catch (err) {
        console.error('⚠️  Gagal mengunduh gambar pesan masuk:', err.message);
        return null;
    }
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

function logBody(req) {
    const summary = {
        ip: req.ip,
        path: req.path,
        contentType: req.get('content-type'),
        keys: req.body ? Object.keys(req.body) : null,
        body: req.body,
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
        return res.status(503).json({ status: 'error', message: 'WhatsApp belum terhubung, coba beberapa saat lagi' });
    }

    try {
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

    if (!html) {
        console.log('⚠️  /send-image: html missing. body keys =', req.body ? Object.keys(req.body) : null, 'contentType =', req.get('content-type'));
        return res.status(400).json({ status: 'error', message: 'HTML content missing' });
    }

    const formattedNumber = normalizeNumber(number);
    if (!formattedNumber) return res.status(400).json({ status: 'error', message: 'Field "number" wajib diisi dan harus berupa nomor WA yang valid' });
    if (!isWhatsAppConnected()) return res.status(503).json({ status: 'error', message: 'WhatsApp belum terhubung, coba beberapa saat ini' });

    try {
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

app.get('/status', requireApiKey, (req, res) => {
    res.json({
        status: isWhatsAppConnected() ? 'connected' : 'disconnected',
        number: sock?.user?.id?.split('@')[0] ?? null,
        name: sock?.user?.name ?? null,
    });
});

app.listen(3000, () => {
    console.log("Server API berjalan di port 3000");
    connectToWhatsApp();
});