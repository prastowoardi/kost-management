const imageInput = document.getElementById('images');
const previewContainer = document.getElementById('preview-container');

if (imageInput && previewContainer) {
    imageInput.addEventListener('change', (event) => {
        const files = event.target.files;

        previewContainer.innerHTML = '';

        if (files.length > 5) {
            alert('Maksimal 5 foto!');
            event.target.value = '';
            return;
        }

        if (files.length > 0) {
            previewContainer.classList.remove('hidden');
        }

        Array.from(files).forEach((file, index) => {
            if (file.size > 2048000) {
                alert(`File ${file.name} terlalu besar! Max 2MB per file.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border border-gray-300">
                    <span class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">${index + 1}</span>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
}

const modal = document.getElementById('imageModal');
const modalImage = document.getElementById('modalImage');

function openImageModal(imageSrc) {
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
}

function closeImageModal() {
    modal.classList.add('hidden');
}

if (modal && modalImage) {
    document.addEventListener('click', (e) => {
        const img = e.target.closest('img[data-open-image]');
        if (img) {
            openImageModal(img.dataset.openImage);
        }
    });

    modal.addEventListener('click', () => {
        closeImageModal();
    });
}
