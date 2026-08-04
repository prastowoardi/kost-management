import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.data('dateRangePicker', (el) => ({
        startDate: el.dataset.startDate || '',
        endDate: el.dataset.endDate || '',

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

    Alpine.data('financeAmount', (el) => ({
        amountClean: el.dataset.amountClean || '',
        amountDisplay: el.dataset.amountDisplay || '',

        init() {
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
});
