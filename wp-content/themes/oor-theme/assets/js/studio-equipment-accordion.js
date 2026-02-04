(function() {
  'use strict';

  function calculateMaxImageHeight(rows, imageContainer) {
    return new Promise((resolve) => {
      const imageUrls = Array.from(rows)
        .map(row => row.getAttribute('data-image-png') || row.getAttribute('data-image'))
        .filter(Boolean);
      let loadedCount = 0;
      let maxHeight = 0;

      if (imageUrls.length === 0) {
        resolve(0);
        return;
      }

      imageUrls.forEach(url => {
        const img = new Image();
        img.onload = function() {
          // Вычисляем высоту изображения с учетом padding контейнера (116px * 2 = 232px)
          const containerPadding = 232;
          const availableWidth = imageContainer.offsetWidth - containerPadding;
          const imageAspectRatio = img.width / img.height;
          const calculatedHeight = availableWidth / imageAspectRatio;
          
          maxHeight = Math.max(maxHeight, calculatedHeight);
          loadedCount++;
          
          if (loadedCount === imageUrls.length) {
            resolve(maxHeight);
          }
        };
        img.onerror = function() {
          loadedCount++;
          if (loadedCount === imageUrls.length) {
            resolve(maxHeight);
          }
        };
        img.src = url;
      });
    });
  }

  function setImageHeight(equipmentImage, imageContainer, maxHeight) {
    if (maxHeight > 0) {
      // Устанавливаем фиксированную высоту для самого изображения
      equipmentImage.style.height = maxHeight + 'px';
      
      // Вычисляем ширину на основе aspect-ratio (1440 / 960 = 1.5)
      const aspectRatio = 1440 / 960;
      const calculatedWidth = maxHeight * aspectRatio;
      equipmentImage.style.width = calculatedWidth + 'px';
      
      // Контейнер автоматически подстроится под высоту изображения + padding
      // Также устанавливаем min-height для wrapper, чтобы он соответствовал высоте контейнера
      const containerTotalHeight = maxHeight + 232; // высота изображения + padding
      const wrapper = imageContainer.closest('.oor-studio-equipment-wrapper');
      if (wrapper) {
        wrapper.style.minHeight = containerTotalHeight + 'px';
      }
    }
  }

  function initEquipmentAccordion() {
    const rows = document.querySelectorAll('.oor-studio-equipment-row');
    const equipmentImage = document.getElementById('equipment-image');
    const imageContainer = equipmentImage ? equipmentImage.closest('.oor-studio-equipment-image') : null;
    const equipmentPicture = equipmentImage ? equipmentImage.closest('picture') : null;
    const equipmentImageAvifSource = equipmentPicture ? equipmentPicture.querySelector('[data-equipment-image-avif]') : null;
    const equipmentImageWebpSource = equipmentPicture ? equipmentPicture.querySelector('[data-equipment-image-webp]') : null;

    if (!rows.length || !equipmentImage || !imageContainer) return;

    // Вычисляем максимальную высоту и устанавливаем её
    function updateImageHeight() {
      calculateMaxImageHeight(rows, imageContainer).then(maxHeight => {
        setImageHeight(equipmentImage, imageContainer, maxHeight);
      });
    }

    // Обновляем высоту при загрузке и изменении размера окна
    updateImageHeight();
    window.addEventListener('resize', updateImageHeight);

    rows.forEach(row => {
      const headerRow = row.querySelector('.oor-studio-equipment-header-row');
      if (!headerRow) return;

      headerRow.addEventListener('click', function() {
        const isActive = row.classList.contains('oor-studio-equipment-row-active');
        const icon = row.querySelector('.oor-studio-equipment-item-icon');
        const iconImg = icon ? icon.querySelector('img') : null;
        const description = row.querySelector('.oor-studio-equipment-item-description');
        const imageUrlPng = row.getAttribute('data-image-png') || row.getAttribute('data-image');
        const imageUrlWebp = row.getAttribute('data-image-webp');
        const imageUrlAvif = row.getAttribute('data-image-avif');

        // Закрываем все остальные строки
        rows.forEach(otherRow => {
          if (otherRow !== row) {
            otherRow.classList.remove('oor-studio-equipment-row-active');
            const otherIcon = otherRow.querySelector('.oor-studio-equipment-item-icon');
            const otherIconImg = otherIcon ? otherIcon.querySelector('img') : null;
            const otherDescription = otherRow.querySelector('.oor-studio-equipment-item-description');

            if (otherIcon) {
              otherIcon.classList.remove('oor-studio-equipment-item-icon-open');
            }

            if (otherIconImg) {
              const paths = (window.OOR_PATHS ? window.OOR_PATHS.assets : '/public/assets');
              otherIconImg.src = paths + '/plus-large.svg';
              otherIconImg.style.transform = 'rotate(180deg)';
            }

            if (otherDescription) {
              otherDescription.style.display = 'none';
            }
          }
        });

        // Переключаем текущую строку
        if (isActive) {
          // Закрываем
          row.classList.remove('oor-studio-equipment-row-active');
          if (icon) {
            icon.classList.remove('oor-studio-equipment-item-icon-open');
          }
          if (iconImg) {
            const paths = (window.OOR_PATHS ? window.OOR_PATHS.assets : '/public/assets');
            iconImg.src = paths + '/plus-large.svg';
            iconImg.style.transform = 'rotate(180deg)';
          }
          if (description) {
            description.style.display = 'none';
          }
        } else {
          // Открываем
          row.classList.add('oor-studio-equipment-row-active');
          if (icon) {
            icon.classList.add('oor-studio-equipment-item-icon-open');
          }
          if (iconImg) {
            const paths = (window.OOR_PATHS ? window.OOR_PATHS.assets : '/public/assets');
            iconImg.src = paths + '/minus-icon.svg';
            iconImg.style.transform = 'none';
          }
          if (description) {
            description.style.display = 'block';
          }
          if (imageUrlPng && equipmentImage.src !== imageUrlPng) {
            // Fade out
            equipmentImage.style.opacity = '0';
            
            // После fade out меняем изображение и fade in
            setTimeout(() => {
              if (equipmentImageAvifSource) {
                equipmentImageAvifSource.srcset = imageUrlAvif || '';
              }
              if (equipmentImageWebpSource) {
                equipmentImageWebpSource.srcset = imageUrlWebp || '';
              }
              equipmentImage.src = imageUrlPng;
              // Fade in
              setTimeout(() => {
                equipmentImage.style.opacity = '1';
              }, 50);
            }, 400);
          }
        }
      });
    });
  }

  // Инициализация при загрузке DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEquipmentAccordion);
  } else {
    initEquipmentAccordion();
  }
})();

