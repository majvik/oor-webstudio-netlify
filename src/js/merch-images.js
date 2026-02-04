// Динамическое изменение размеров изображений мерча
function resizeMerchImages() {
    if (window.innerWidth <= 460) {
        const imageEls = document.querySelectorAll('.oor-merch-image-item img');
        imageEls.forEach(img => {
            img.style.width = '100%';
            img.style.height = 'auto';
        });
        return;
    }

    const grid = document.querySelector('.oor-merch-images-grid');
    const wrapper = document.querySelector('.oor-merch-images-wrapper');
    const imageItems = document.querySelectorAll('.oor-merch-image-item');
    
    if (!grid || !wrapper || imageItems.length === 0) return;
    
    imageItems.forEach(item => {
        const img = item.querySelector('img');
        if (img) {
            img.style.width = 'auto';
            img.style.height = 'auto';
        }
    });
    
    const availableWidth = grid.offsetWidth - 96;
    const gap = 16;
    const gapsTotal = (imageItems.length - 1) * gap;
    const imagesWidth = availableWidth - gapsTotal;
    
    let totalNaturalWidth = 0;
    const naturalWidths = [];
    
    imageItems.forEach(item => {
        const img = item.querySelector('img');
        if (img) {
            const naturalWidth = img.naturalWidth;
            const naturalHeight = img.naturalHeight;
            const aspectRatio = naturalWidth / naturalHeight;
            naturalWidths.push({ width: naturalWidth, height: naturalHeight, ratio: aspectRatio });
            totalNaturalWidth += naturalWidth;
        }
    });
    
    if (totalNaturalWidth === 0) return;
    
    const scaleFactor = imagesWidth / totalNaturalWidth;
    
    imageItems.forEach((item, index) => {
        const img = item.querySelector('img');
        if (img && naturalWidths[index]) {
            const newWidth = naturalWidths[index].width * scaleFactor;
            const newHeight = naturalWidths[index].height * scaleFactor;
            img.style.width = newWidth + 'px';
            img.style.height = newHeight + 'px';
        }
    });
}

window.addEventListener('load', () => {
    setTimeout(resizeMerchImages, 100);
});
window.addEventListener('resize', resizeMerchImages);
document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('.oor-merch-image-item img');
    images.forEach(img => {
        if (img.complete) {
            resizeMerchImages();
        } else {
            img.addEventListener('load', resizeMerchImages);
        }
    });
});
