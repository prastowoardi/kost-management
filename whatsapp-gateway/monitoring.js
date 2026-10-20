/**
 * Monitoring & Health Check System untuk WhatsApp Gateway
 */

const metrics = {
    startTime: Date.now(),
    totalMessagesSent: 0,
    totalMessagesFailed: 0,
    totalImagesRendered: 0,
    lastError: null,
    lastErrorTime: null,
    whatsappConnected: false,
    chromeInstances: 0,
};

function updateMetrics(type, success = true, error = null) {
    switch (type) {
        case 'message':
            success ? metrics.totalMessagesSent++ : metrics.totalMessagesFailed++;
            break;
        case 'image':
            metrics.totalImagesRendered++;
            break;
        case 'connection':
            metrics.whatsappConnected = success;
            break;
        case 'chrome':
            metrics.chromeInstances++;
            break;
    }

    if (error) {
        metrics.lastError = error.message || String(error);
        metrics.lastErrorTime = Date.now();
    }
}

function getHealthStatus() {
    const uptime = Math.floor((Date.now() - metrics.startTime) / 1000);
    const uptimeHours = (uptime / 3600).toFixed(2);
    
    const errorRate = metrics.totalMessagesFailed / 
        (metrics.totalMessagesSent + metrics.totalMessagesFailed || 1);
    
    return {
        status: metrics.whatsappConnected ? 'healthy' : 'unhealthy',
        uptime: { seconds: uptime, human: `${uptimeHours} hours` },
        whatsapp: {
            connected: metrics.whatsappConnected,
            messagesSent: metrics.totalMessagesSent,
            messagesFailed: metrics.totalMessagesFailed,
            errorRate: (errorRate * 100).toFixed(2) + '%',
        },
        system: {
            memory: process.memoryUsage(),
            chromeInstances: metrics.chromeInstances,
        },
        lastError: metrics.lastError ? {
            message: metrics.lastError,
            timestamp: new Date(metrics.lastErrorTime).toISOString(),
        } : null,
    };
}

module.exports = { updateMetrics, getHealthStatus, metrics };
