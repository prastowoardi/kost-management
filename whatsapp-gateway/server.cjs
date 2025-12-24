const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const puppeteer = require('puppeteer');
const path = require('path');

const app = express();
app.use(express.json());

const client = new Client({
    authStrategy: new LocalAuth({ dataPath: './sessions' }),
    puppeteer: {
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    }
});

client.on('qr', (qr) => {
    console.log('SCAN QR INI:');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => console.log('WhatsApp Gateway Ready!'));

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

        await client.sendMessage(chatId, message);
        console.log(`Pesan terkirim ke ${formattedNumber}`);
        res.json({ status: 'success' });
    } catch (err) {
        console.error(err);
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

        console.log(`\n--- Memproses Kiriman Baru ---`);
        console.log(`Tujuan   : ${formattedNumber}`);
        console.log(`Kwitansi : ${url || 'URL tidak terlampir'}`); 

        browser = await puppeteer.launch({
            headless: "new",
            args: [
                '--no-sandbox',
                '--disable-gpu',
                '--transparent-background-color=#00000000'
            ]
        });

        const page = await browser.newPage();
        
        await page.setViewport({ 
            width: 800, 
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
app.listen(3000, () => console.log('WA Gateway berjalan di port 3000'));
client.initialize();
