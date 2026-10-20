/**
 * Auto Health Checker - Periodic monitoring & alerting
 */

const { metrics, getHealthStatus } = require('./monitoring');
const { sendAlert } = require('./alerts');

let consecutiveFailures = 0;
const MAX_CONSECUTIVE_FAILURES = 5;

function checkAndAlert() {
    const health = getHealthStatus();
    const errorRate = metrics.totalMessagesFailed / 
        (metrics.totalMessagesSent + metrics.totalMessagesFailed || 1);

    // Alert 1: High error rate (>30%)
    if (errorRate > 0.3 && metrics.totalMessagesSent > 10) {
        sendAlert(
            'High Error Rate Detected',
            `Error rate: ${(errorRate * 100).toFixed(1)}%\nTotal failed: ${metrics.totalMessagesFailed}\nTotal sent: ${metrics.totalMessagesSent}`,
            'warning'
        );
    }

    // Alert 2: WhatsApp disconnected
    if (!metrics.whatsappConnected) {
        consecutiveFailures++;
        if (consecutiveFailures >= MAX_CONSECUTIVE_FAILURES) {
            sendAlert(
                'WhatsApp Gateway Disconnected',
                `Gateway disconnected for ${consecutiveFailures * 5} minutes.\nPlease scan QR code or restart service.`,
                'critical'
            );
            consecutiveFailures = 0; // Reset to prevent spam
        }
    } else {
        consecutiveFailures = 0;
    }

    // Alert 3: High memory usage (>500MB)
    const memMB = process.memoryUsage().heapUsed / 1024 / 1024;
    if (memMB > 500) {
        sendAlert(
            'High Memory Usage',
            `Memory: ${memMB.toFixed(0)}MB\nChrome instances: ${metrics.chromeInstances}\nConsider restarting.`,
            'warning'
        );
    }

    // Log status every check
    console.log(`[Health Check] Status: ${health.status} | Uptime: ${health.uptime.human} | Sent: ${metrics.totalMessagesSent} | Failed: ${metrics.totalMessagesFailed}`);
}

// Run checks every 5 minutes
setInterval(checkAndAlert, 5 * 60 * 1000);

// Initial check after 1 minute
setTimeout(checkAndAlert, 60 * 1000);

module.exports = { checkAndAlert };
