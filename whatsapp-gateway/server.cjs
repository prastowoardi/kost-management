const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const puppeteer = require('puppeteer');
const path = require('path');
const os = require('os');

const app = express();
app.use(express.json());

const getChromePath = () => {
    const platform = os.platform();
    if (platform === 'darwin') {
        return '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
    } else if (platform === 'win32') {
        return 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    } else {
        return null; 
    }
};

const chromePath = getChromePath();

const client = new Client({
    authStrategy: new LocalAuth({ dataPath: './sessions' }),
    webVersionCache: {
        type: 'remote',
        remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-js/main/dist/wppconnect-wa.js',
    },
    puppeteer: {
        executablePath: chromePath || undefined,
        headless: true,
        args: [
            '--no-sandbox', 
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-extensions'
        ]
    }
});

client.on('qr', (qr) => {
    console.log('SCAN QR INI:');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => console.log('WhatsApp Gateway Ready!'));

client.on('disconnected', (reason) => {
    console.log('User was logged out', reason);
    setTimeout(() => {
        client.initialize();
    }, 5000);
});

app.post('/send-pdf', async (req, res) => {
    const { number, message, file_path } = req.body;
    try {
        let formattedNumber = number.replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const chatId = formattedNumber + "@c.us";

        const media = MessageMedia.fromFilePath(file_path);
        
        await client.sendMessage(chatId, media, { caption: message });
        res.json({ status: 'success' });
    } catch (err) {
        console.error(err);
        res.status(500).json({ status: 'error', message: err.message });
    }
});

app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;
    try {
        let formattedNumber = number.replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const chatId = formattedNumber + "@c.us";

        console.log(`\n--- Mengirim Pesan ---`);
        console.log(`Tujuan : ${formattedNumber}`);
        console.log(`Isi    : ${message}`);

        await client.sendMessage(chatId, message);
        console.log(`Pesan terkirim ke ${formattedNumber}`);

        res.json({ status: 'success' });
    } catch (err) {
        console.error(`Gagal kirim ke ${number}:`, err.message);
        res.status(500).json({ status: 'error', message: err.message });
    }
});

app.post('/send-image', async (req, res) => {
    const { number, html, message, url } = req.body; 
    let browser = null;

    if (!html) {
        return res.status(400).json({ status: 'error', message: 'HTML content is missing' });
    }

    try {
        let formattedNumber = number.replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const chatId = formattedNumber + "@c.us";

        console.log(`\n--- Mengirim Kwitansi ---`);
        console.log(`Tujuan   : ${formattedNumber}`);
        console.log(`Kwitansi : ${url || 'URL tidak terlampir'}`); 

        browser = await puppeteer.launch({
            executablePath: chromePath || undefined,
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--disable-gpu',
                '--transparent-background-color=#00000000'
            ]
        });

        const page = await browser.newPage();

        await page.setViewport({ 
            width: 750, 
            height: 1000, 
            deviceScaleFactor: 2 
        });

        await page.setContent(html, { 
            waitUntil: 'networkidle0',
            timeout: 15000 
        });

        await page.evaluate(() => {
            document.body.style.backgroundColor = 'transparent';
            document.documentElement.style.backgroundColor = 'transparent';
        });

        await new Promise(resolve => setTimeout(resolve, 2000));

        const element = await page.$('.card'); 
        if (!element) throw new Error("Elemen dengan class '.card' tidak ditemukan!");

        const screenshot = await element.screenshot({ 
            encoding: 'base64',
            omitBackground: true
        });

        const media = new MessageMedia('image/png', screenshot);
        await client.sendMessage(chatId, media, { caption: message });

        console.log(`Berhasil mengirim kwitansi ke ${formattedNumber}`);
        res.json({ status: 'success' });

    } catch (err) {
        console.error("❌ Gagal:", err.message);
        res.status(500).json({ status: 'error', message: err.message });
    } finally {
        if (browser) await browser.close();
    }
});

app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;

    if (!number || !message) {
        return res.status(400).json({ status: false, message: 'Nomor atau pesan kosong' });
    }

    const formattedNumber = number.includes('@c.us') ? number : `${number}@c.us`;

    try {
        if (!client.info || !client.info.wid) {
            throw new Error('WhatsApp belum terhubung (belum scan QR)');
        }

        await client.sendMessage(formattedNumber, message);
        res.status(200).json({ status: true, message: 'Pesan terkirim' });
    } catch (error) {
        console.error("Gagal kirim pesan:", error.message);
        res.status(500).json({ status: false, message: 'Gagal kirim: ' + error.message });
    }
});

app.listen(3000, () => console.log('WA Gateway berjalan di port 3000'));
client.initialize();
