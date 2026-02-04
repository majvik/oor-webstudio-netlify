/**
 * Events Listing Horizontal Slider with Drag-and-Drop
 * Простой горизонтальный слайдер для блока событий
 */

(function() {
  'use strict';

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEventsSlider);
  } else {
    initEventsSlider();
  }

  function initEventsSlider() {
    const slider = document.querySelector('.oor-events-listing-grid');
    if (!slider) return;

    // Отключаем слайдер на мобильных устройствах (только обычная прокрутка)
    const isMobile = window.innerWidth <= 768 || 'ontouchstart' in window;
    if (isMobile) {
      // Убеждаемся, что на мобильных прокрутка работает нормально
      slider.style.touchAction = 'pan-x';
      return;
    }

    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;
    let hasMoved = false;

    // Mouse events
    slider.addEventListener('mousedown', (e) => {
      if (e.button !== 0) return; // только левая кнопка мыши
      isDown = true;
      slider.classList.add('is-dragging');
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
      hasMoved = false;
      e.preventDefault();
    });

    slider.addEventListener('mouseleave', () => {
      if (isDown) {
        isDown = false;
        slider.classList.remove('is-dragging');
      }
    });

    slider.addEventListener('mouseup', () => {
      if (isDown) {
        isDown = false;
        slider.classList.remove('is-dragging');
      }
    });

    slider.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 1.6; // скорость перетаскивания
      slider.scrollLeft = scrollLeft - walk;
      if (Math.abs(walk) > 3) {
        hasMoved = true;
      }
    });

    // Touch events для мобильных устройств
    let touchStartX = 0;
    let touchScrollLeft = 0;

    slider.addEventListener('touchstart', (e) => {
      touchStartX = e.touches[0].pageX - slider.offsetLeft;
      touchScrollLeft = slider.scrollLeft;
      hasMoved = false;
    }, { passive: true });

    slider.addEventListener('touchmove', (e) => {
      const x = e.touches[0].pageX - slider.offsetLeft;
      const walk = (x - touchStartX) * 1.6;
      slider.scrollLeft = touchScrollLeft - walk;
      if (Math.abs(walk) > 3) {
        hasMoved = true;
      }
    }, { passive: true });

    // Предотвращаем клики после drag
    slider.addEventListener('click', (e) => {
      if (hasMoved) {
        e.preventDefault();
        e.stopPropagation();
        hasMoved = false;
      }
    }, true);
  }
})();

