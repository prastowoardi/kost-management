const mainImage = document.getElementById('mainImage');
const thumbnails = document.querySelectorAll('[data-full-src]');
const modal = document.getElementById('imageModal');
const modalImage = document.getElementById('modalImage');
const closeButton = document.getElementById('modalCloseBtn');

function openImageModal(imageSrc) {
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

if (mainImage) {
    mainImage.addEventListener('click', () => {
        openImageModal(mainImage.src);
    });
}

thumbnails.forEach((thumbnail) => {
    if (thumbnail !== mainImage) {
        thumbnail.addEventListener('click', () => {
            const imageSrc = thumbnail.getAttribute('data-full-src');
            mainImage.src = imageSrc;
            mainImage.setAttribute('data-full-src', imageSrc);

            thumbnails.forEach((t) => {
                if (t !== mainImage) {
                    t.classList.remove('ring-2', 'ring-brand-500');
                }
            });

            thumbnail.classList.add('ring-2', 'ring-brand-500');
        });
    }
});

if (modal) {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeImageModal();
        }
    });
}

if (closeButton) {
    closeButton.addEventListener('click', closeImageModal);
}

if (modalImage) {
    modalImage.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
