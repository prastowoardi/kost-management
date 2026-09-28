/**
 * Tooltip untuk chart SVG yang dirender server-side.
 *
 * Chart digambar sebagai SVG murni (tanpa library charting), sehingga
 * interaksi cukup satu hal: baca `data-tooltip` pada elemen yang di-hover
 * lalu tampilkan kartu kecil yang mengikuti kursor.
 */

const TOOLTIP_ID = 'chart-tooltip';

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    })[char]);
}

function ensureTooltip() {
    let el = document.getElementById(TOOLTIP_ID);

    if (!el) {
        el = document.createElement('div');
        el.id = TOOLTIP_ID;
        el.setAttribute('role', 'tooltip');
        el.style.cssText = [
            'position:fixed',
            'z-index:9999',
            'pointer-events:none',
            'opacity:0',
            'transition:opacity .12s ease',
            'background:#292524',
            'color:#fafaf9',
            'font-size:12px',
            'line-height:1.4',
            'padding:8px 10px',
            'border-radius:10px',
            'box-shadow:0 8px 24px -6px rgba(0,0,0,.35)',
            'max-width:260px',
        ].join(';');

        document.body.appendChild(el);
    }

    return el;
}

function renderTooltip(el, payload) {
    const rows = Array.isArray(payload.rows) ? payload.rows : [];

    const body = rows.map((row) => `
        <div style="display:flex;align-items:center;gap:6px;margin-top:3px">
            <span style="width:8px;height:8px;border-radius:999px;background:${escapeHtml(row.color)};flex:none"></span>
            <span style="opacity:.75">${escapeHtml(row.label)}</span>
            <span style="margin-left:auto;font-weight:700;font-variant-numeric:tabular-nums">${escapeHtml(row.value)}</span>
        </div>
    `).join('');

    el.innerHTML = `<p style="margin:0;font-weight:700">${escapeHtml(payload.title)}</p>${body}`;
}

function placeTooltip(el, x, y) {
    const rect = el.getBoundingClientRect();
    const margin = 12;

    let left = x + margin;
    let top = y - rect.height - margin;

    if (left + rect.width > window.innerWidth - 8) {
        left = x - rect.width - margin;
    }

    if (top < 8) {
        top = y + margin;
    }

    el.style.left = `${Math.max(8, left)}px`;
    el.style.top = `${Math.max(8, top)}px`;
}

function hideTooltip(el) {
    el.style.opacity = '0';
}

function initChartTooltips() {
    // Dimuat ulang (mis. dua @push script di halaman yang sama) -> jangan
    // pasang listener tooltip dua kali, cukup lewati.
    if (window.__chartTooltipsReady) {
        return;
    }

    window.__chartTooltipsReady = true;

    const tooltip = ensureTooltip();

    const onOver = (event) => {
        const target = event.target.closest('[data-tooltip]');

        if (!target || !document.body.contains(target)) {
            hideTooltip(tooltip);
            return;
        }

        try {
            const payload = JSON.parse(target.dataset.tooltip);
            renderTooltip(tooltip, payload);
            placeTooltip(tooltip, event.clientX, event.clientY);
            tooltip.style.opacity = '1';
        } catch (error) {
            hideTooltip(tooltip);
        }
    };

    document.addEventListener('mouseover', onOver);

    // Kursor keluar viewport (mis. scroll cepat) tidak memicu mouseout pada target.
    document.addEventListener('mouseout', (event) => {
        if (!event.target.closest('[data-tooltip]')) {
            return;
        }

        const to = event.relatedTarget;

        if (!to || !to.closest || !to.closest('[data-tooltip]')) {
            hideTooltip(tooltip);
        }
    });

    document.addEventListener('scroll', () => hideTooltip(tooltip), true);
    window.addEventListener('resize', () => hideTooltip(tooltip));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChartTooltips);
} else {
    initChartTooltips();
}
