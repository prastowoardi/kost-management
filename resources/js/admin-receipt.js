import html2canvas from 'html2canvas';
import html2pdf from 'html2pdf.js';

const element = document.querySelector('.card');
const container = document.querySelector('.action-container');
const filename = container ? (container.dataset.filename || 'Kwitansi') : 'Kwitansi';

function downloadPDF() {
    const opt = {
        margin: 0,
        filename: filename + '.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 3, useCORS: true, backgroundColor: '#ffffff' },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}

function downloadImage() {
    html2canvas(element, { scale: 4, backgroundColor: '#ffffff', useCORS: true }).then((canvas) => {
        const link = document.createElement('a');
        link.download = filename + '.jpg';
        link.href = canvas.toDataURL('image/jpeg', 1.0);
        link.click();
    });
}

if (container) {
    const pdfBtn = container.querySelector('[data-download-pdf]');
    const imageBtn = container.querySelector('[data-download-image]');

    if (pdfBtn) pdfBtn.addEventListener('click', downloadPDF);
    if (imageBtn) imageBtn.addEventListener('click', downloadImage);
}

window.addEventListener('message', function (event) {
    if (event.data === 'trigger-download-image') {
        downloadImage();
    }
});
