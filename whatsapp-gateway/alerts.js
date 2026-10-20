/**
 * Alert System untuk WhatsApp Gateway
 * Supports: Telegram, Slack, Laravel webhook
 */

async function sendAlert(title, message, severity = 'warning') {
    const TELEGRAM_BOT_TOKEN = process.env.TELEGRAM_BOT_TOKEN;
    const TELEGRAM_CHAT_ID = process.env.TELEGRAM_CHAT_ID;

    // Telegram Alert (recommended)
    if (TELEGRAM_BOT_TOKEN && TELEGRAM_CHAT_ID) {
        try {
            const emoji = severity === 'critical' ? '🚨' : severity === 'warning' ? '⚠️' : 'ℹ️';
            const text = `${emoji} *${title}*\n\n${message}\n\n_Time: ${new Date().toLocaleString('id-ID')}_`;
            
            await fetch(`https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: TELEGRAM_CHAT_ID,
                    text,
                    parse_mode: 'Markdown',
                }),
            });
        } catch (error) {
            console.error('Failed to send Telegram alert:', error.message);
        }
    }

    // Slack Webhook
    const SLACK_WEBHOOK_URL = process.env.SLACK_WEBHOOK_URL;
    if (SLACK_WEBHOOK_URL) {
        try {
            await fetch(SLACK_WEBHOOK_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: `*${title}*\n${message}`,
                    username: 'WA Gateway Monitor',
                }),
            });
        } catch (error) {
            console.error('Failed to send Slack alert:', error.message);
        }
    }

    // Kirim ke Laravel
    const LARAVEL_ALERT_URL = process.env.LARAVEL_WEBHOOK_URL?.replace('/webhook', '/alerts');
    if (LARAVEL_ALERT_URL) {
        try {
            await fetch(LARAVEL_ALERT_URL, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-API-Key': process.env.WA_WEBHOOK_KEY || '',
                },
                body: JSON.stringify({ title, message, severity, timestamp: Date.now() }),
            });
        } catch (error) {
            console.error('Failed to send alert to Laravel:', error.message);
        }
    }
}

module.exports = { sendAlert };
