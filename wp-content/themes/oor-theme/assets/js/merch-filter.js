/**
 * Merch Filter Functionality
 * Фильтрация товаров по категориям
 */

(function() {
  'use strict';

  function initMerchFilter() {
    const filterButtons = document.querySelectorAll('.oor-merch-filter-btn');
    const products = document.querySelectorAll('.oor-merch-product');

    if (!filterButtons.length || !products.length) {
      return;
    }

    filterButtons.forEach(button => {
      button.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');

        // Удаляем активный класс со всех кнопок
        filterButtons.forEach(btn => {
          btn.classList.remove('oor-merch-filter-btn--active');
        });

        // Добавляем активный класс к нажатой кнопке
        this.classList.add('oor-merch-filter-btn--active');

        // Фильтруем товары (data-category может содержать несколько slug через пробел)
        products.forEach(product => {
          const categoryAttr = product.getAttribute('data-category') || '';
          const categories = categoryAttr.trim().split(/\s+/);

          if (filter === 'all' || categories.includes(filter)) {
            product.style.display = 'block';
          } else {
            product.style.display = 'none';
          }
        });
      });
    });
  }

  // Инициализация при загрузке DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMerchFilter);
  } else {
    initMerchFilter();
  }
})();
