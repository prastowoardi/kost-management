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

    // `mouseover` hanya fired sekali saat kursor masuk satu slot hover, jadi
    // tooltip dulu "nempel" di titik masuk dan tidak mengikuti kursor lagi.
    // Posisi dihitung ulang di `mousemove`, dirapikan lewat requestAnimationFrame
    // supaya tidak memaksa reflow pada setiap mousemove.
    let activeTarget = null;
    let pointerX = 0;
    let pointerY = 0;
    let frame = null;

    const repaint = () => {
        frame = null;

        if (!activeTarget || !document.body.contains(activeTarget)) {
            return;
        }

        placeTooltip(tooltip, pointerX, pointerY);
        tooltip.style.opacity = '1';
    };

    const schedule = () => {
        if (frame === null) {
            frame = requestAnimationFrame(repaint);
        }
    };

    const show = (target, x, y) => {
        if (target !== activeTarget) {
            activeTarget = target;

            try {
                renderTooltip(tooltip, JSON.parse(target.dataset.tooltip));
            } catch (error) {
                activeTarget = null;
                hideTooltip(tooltip);
                return;
            }
        }

        pointerX = x;
        pointerY = y;
        schedule();
    };

    document.addEventListener('mouseover', (event) => {
        const target = event.target.closest('[data-tooltip]');

        if (!target || !document.body.contains(target)) {
            return;
        }

        show(target, event.clientX, event.clientY);
    });

    // Kursor bergerak di dalam slot yang sama -> tooltip harus ikut bergerak.
    document.addEventListener('mousemove', (event) => {
        if (!activeTarget || !document.body.contains(activeTarget)) {
            return;
        }

        show(activeTarget, event.clientX, event.clientY);
    });

    // Kursor keluar viewport (mis. scroll cepat) tidak memicu mouseout pada target.
    document.addEventListener('mouseout', (event) => {
        if (!event.target.closest('[data-tooltip]')) {
            return;
        }

        const to = event.relatedTarget;

        // Pindah antar slot hover: biarkan `mouseover` yang menanganinya.
        if (to && to.closest && to.closest('[data-tooltip]')) {
            return;
        }

        activeTarget = null;
        hideTooltip(tooltip);
    });

    document.addEventListener('scroll', () => {
        activeTarget = null;
        hideTooltip(tooltip);
    }, true);

    window.addEventListener('resize', () => {
        activeTarget = null;
        hideTooltip(tooltip);
    });
}

/**
 * Chart tren dirender dengan lebar penuh, tapi tingginya harus tetap (px).
 *
 * Server merender `viewBox` dengan lebar nominal + `h-auto`, jadi sebelum JS
 * jalan SVG sudah mengisi card tanpa margin kosong. Tapi `h-auto` membuat
 * tinggi ikut membesar di layar lebar, jadi di sini tinggi dikunci dan
 * `viewBox` disamakan dengan lebar pixel asli container: 1 unit viewBox = 1 px,
 * sehingga teks, stroke, dan titik tidak ikut teregang.
 */
const FIT_TOLERANCE = 2;

function initChartFit() {
    document.querySelectorAll('svg[data-chart-fit]').forEach((svg) => {
        const height = Number(svg.dataset.chartFit);

        if (!height) {
            return;
        }

        const apply = () => {
            const width = Math.round(svg.clientWidth || 0);

            if (!width) {
                return;
            }

            svg.style.height = `${height}px`;

            const box = svg.viewBox.baseVal;

            if (box && Math.abs(box.width - width) < FIT_TOLERANCE && box.height === height) {
                return;
            }

            svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        };

        apply();

        if (typeof ResizeObserver === 'undefined') {
            window.addEventListener('resize', apply);
            return;
        }

        new ResizeObserver(apply).observe(svg);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initChartFit();
        initChartTooltips();
    });
} else {
    initChartFit();
    initChartTooltips();
}
