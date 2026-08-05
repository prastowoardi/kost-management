import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.data('dateRangePicker', () => ({
        startDate: '',
        endDate: '',

        init() {
            const ds = this.$root.dataset;
            this.startDate = ds.startDate || '';
            this.endDate = ds.endDate || '';
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        },

        getDaysCount() {
            if (!this.startDate || !this.endDate) return 0;
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            return Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
        },

        setRange(daysStart, daysEnd) {
            const today = new Date();
            const start = new Date(today);
            start.setDate(today.getDate() - daysEnd);
            this.startDate = start.toISOString().split('T')[0];
            this.endDate = today.toISOString().split('T')[0];
        },

        setRangeMonth(offset = 0) {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth() + offset;

            this.startDate = new Date(year, month, 1).toISOString().split('T')[0];
            this.endDate = new Date(year, month + 1, 0).toISOString().split('T')[0];
        }
    }));

    Alpine.data('financeAmount', () => ({
        amountClean: '',
        amountDisplay: '',

        init() {
            const ds = this.$root.dataset;
            this.amountClean = ds.amountClean || '';
            this.amountDisplay = ds.amountDisplay || '';

            if (this.amountDisplay !== '') {
                this.formatNumber();
            } else if (this.amountClean !== '') {
                this.amountDisplay = Number(this.amountClean).toLocaleString('id-ID');
            }
        },

        formatNumber() {
            const rawValue = this.amountDisplay.replace(/[^0-9]/g, '');

            this.amountClean = rawValue;

            if (rawValue !== '') {
                this.amountDisplay = Number(rawValue).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            } else {
                this.amountDisplay = '';
            }
        }
    }));

    Alpine.data('migrateWidget', () => ({
        running: false,
        percent: 0,
        done: 0,
        total: 0,

        init() {
            const ds = this.$root.dataset;
            this.running = ds.running === '1';
            this.percent = Number(ds.percent || 0);
            this.done = Number(ds.done || 0);
            this.total = Number(ds.total || 0);

            if (this.running) {
                this.poll();
            }
        },

        async start(event) {
            const root = this.$root;
            const ok = await window.confirmAction(root.dataset.confirmMsg, 'Ya, Mulai!');
            if (!ok) return;

            this.running = true;
            this.percent = 0;
            this.done = 0;
            this.total = 0;
            this.poll();
        },

        async poll() {
            const form = this.$root.querySelector('form');

            try {
                while (true) {
                    const fd = new FormData(form);
                    const r = await fetch(form.action, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-CSRF-TOKEN': window.appCsrf },
                    });
                    const d = await r.json();

                    if (!r.ok || d.error) {
                        throw new Error(d.error || 'Terjadi kesalahan server.');
                    }

                    this.done = d.done ?? this.done;
                    this.total = d.total ?? this.total;
                    this.percent = d.percent ?? this.percent;

                    if (d.finished) {
                        this.running = false;
                        const note = d.failed ? ' (' + d.failed + ' gagal)' : '';
                        window.showSuccessToast('Migrasi selesai: ' + d.done + ' file' + note);
                        setTimeout(() => window.location.reload(), 1200);
                        return;
                    }
                }
            } catch (e) {
                this.running = false;
                window.showErrorToast('Migrasi gagal: ' + (e.message || 'kesalahan server'));
            }
        },
    }));
});
