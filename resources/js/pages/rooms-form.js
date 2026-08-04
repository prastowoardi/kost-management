let existingImagesCount = 0;

function previewImages(fileInput, previewContainer, maxFiles = 5) {
    const files = fileInput.files;

    previewContainer.innerHTML = '';

    if (files.length > maxFiles) {
        alert(`Maksimal ${maxFiles} foto!`);
        fileInput.value = '';
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
}

const createInput = document.getElementById('room-images');
const createPreview = document.getElementById('room-preview-container');

if (createInput && createPreview) {
    createInput.addEventListener('change', () => {
        previewImages(createInput, createPreview);
    });
}

const newImagesInput = document.getElementById('new-room-images');
const newImagesPreview = document.getElementById('new-images-preview');

if (newImagesInput && newImagesPreview) {
    existingImagesCount = parseInt(newImagesPreview.dataset.existingCount || '0', 10) || 0;

    newImagesInput.addEventListener('change', () => {
        const files = newImagesInput.files;
        const maxTotal = 5;

        newImagesPreview.innerHTML = '';

        const totalImages = existingImagesCount + files.length;
        if (totalImages > maxTotal) {
            alert(`Total maksimal ${maxTotal} foto! Saat ini ada ${existingImagesCount} foto, Anda bisa upload maksimal ${maxTotal - existingImagesCount} foto lagi.`);
            newImagesInput.value = '';
            return;
        }

        if (files.length > 0) {
            newImagesPreview.classList.remove('hidden');
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
                    <span class="absolute top-1 right-1 bg-green-600 text-white text-xs px-2 py-1 rounded">Baru</span>
                `;
                newImagesPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-remove-image]');
    if (!btn) return;

    e.preventDefault();

    const index = btn.dataset.removeImage;
    if (confirm('Hapus foto ini?')) {
        const imageEl = document.getElementById('existing-image-' + index);
        const keepEl = document.getElementById('keep-image-' + index);
        if (imageEl) imageEl.remove();
        if (keepEl) keepEl.remove();
        existingImagesCount--;
    }
});
