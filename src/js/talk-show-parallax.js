/**
 * Talk Show Parallax Scroll
 * Создает эффект параллакс-скролла для двух колонок с изображениями
 * в секции "токшоу без правил"
 * 
 * Колонки начинают сдвигаться когда они появляются во viewport
 */

(function() {
  'use strict';

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTalkShowParallax);
  } else {
    initTalkShowParallax();
  }

  function initTalkShowParallax() {
    const rulesSection = document.querySelector('.oor-talk-show-rules');
    if (!rulesSection) return;

    const col1 = rulesSection.querySelector('.oor-talk-show-rules-col-1');
    const col2 = rulesSection.querySelector('.oor-talk-show-rules-col-2');
    const rulesRight = rulesSection.querySelector('.oor-talk-show-rules-right');
    
    if (!col1 || !col2 || !rulesRight) return;

    // Скорости колонок
    const speed1 = 0.35;
    const speed2 = 0.45;

    let rafId = null;

    function update() {
      const rect = rulesRight.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      
      // Прогресс начинается когда колонки появляются во viewport (снизу)
      let progress = 0;
      
      // Колонки видны если их верх меньше высоты viewport
      if (rect.top < viewportHeight) {
        // Диапазон: от появления снизу до ухода вверх
        const totalTravel = viewportHeight + rect.height;
        const traveled = viewportHeight - rect.top;
        progress = Math.max(0, Math.min(1, traveled / totalTravel));
      }
      
      // Максимальный сдвиг колонок
      const contentEl = rulesSection.querySelector('.oor-talk-show-rules-content');
      const contentHeight = contentEl ? contentEl.offsetHeight : viewportHeight * 0.6;
      
      const col1Height = col1.scrollHeight;
      const col2Height = col2.scrollHeight;
      
      const maxShift1 = Math.max(0, (col1Height - contentHeight) * 0.7);
      const maxShift2 = Math.max(0, (col2Height - contentHeight) * 0.9);
      
      const y1 = -progress * maxShift1 * speed1;
      const y2 = -progress * maxShift2 * speed2;
      
      col1.style.transform = `translate3d(0, ${y1}px, 0)`;
      col2.style.transform = `translate3d(0, ${y2}px, 0)`;
    }

    function onScroll() {
      if (rafId) return;
      rafId = requestAnimationFrame(() => {
        update();
        rafId = null;
      });
    }

    // Слушаем скролл
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    
    // Также слушаем Lenis если есть
    function attachLenis() {
      if (window.lenis) {
        window.lenis.on('scroll', onScroll);
        return true;
      }
      return false;
    }
    
    if (!attachLenis()) {
      const checkLenis = setInterval(() => {
        if (attachLenis()) {
          clearInterval(checkLenis);
        }
      }, 100);
      setTimeout(() => clearInterval(checkLenis), 5000);
    }

    // Начальный расчёт
    update();
  }
})();
