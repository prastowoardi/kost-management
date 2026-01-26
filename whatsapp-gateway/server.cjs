const originalWrite = process.stdout.write;
process.stdout.write = function (chunk, encoding, callback) {
    if (typeof chunk === 'string' && chunk.includes('Closing session')) return true;
    return originalWrite.apply(process.stdout, arguments);
};

const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require("@whiskeysockets/baileys");
const { Boom } = require("@hapi/boom");
const puppeteer = require('puppeteer');
const qrcode = require("qrcode-terminal");
const express = require("express");
const pino = require("pino");
const os = require("os");

const app = express();
app.use(express.json({ limit: '50mb' }));

let sock;

// Deteksi Path Chrome agar Puppeteer bisa jalan di Windows/Mac/Linux
const chromePath = (() => {
    const platform = os.platform();
    if (platform === 'darwin') return '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
    if (platform === 'win32') return 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    return null; 
})();

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: true,
        logger: pino({ level: 'silent' }),
        shouldIgnoreJid: jid => isJidBroadcast(jid),
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;
        if (qr) {
            console.log("Scan QR untuk menyambungkan ke WhatsApp:");
            qrcode.generate(qr, { small: true });
        }
        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error instanceof Boom)?.output?.statusCode !== DisconnectReason.loggedOut;
            if (shouldReconnect) connectToWhatsApp();
        } else if (connection === 'open') {
            console.log('✅ WhatsApp Gateway Ready (Baileys)!');
        }
    });
}

app.post('/send-message', async (req, res) => {
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

app.post('/send-image', async (req, res) => {
    const { number, html, message } = req.body;
    let browser = null;

    if (!html) return res.status(400).json({ status: 'error', message: 'HTML content missing' });

    try {
        let formattedNumber = number.replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const jid = `${formattedNumber}@s.whatsapp.net`;

        console.log(`\n--- Proses Render Kwitansi ---`);
        console.log(`Tujuan: ${formattedNumber}`);

        const platform = os.platform();
        let executablePath = undefined;
        if (platform === 'darwin') {
            executablePath = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
        } else if (platform === 'win32') {
            executablePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
        }

        browser = await puppeteer.launch({
            executablePath: executablePath,
            headless: "new",
            args: [
                '--no-sandbox', 
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu'
            ]
        });

        const page = await browser.newPage();
        
        // Set ukuran layar agar screenshot pas
        await page.setViewport({ width: 750, height: 1000, deviceScaleFactor: 2 });
        
        // Masukkan HTML dari Laravel
        await page.setContent(html, { waitUntil: 'networkidle0' });

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
        if (browser) await browser.close();
    }
});

app.listen(3000, () => {
    console.log("Server API berjalan di port 3000");
    connectToWhatsApp();
});