/**
 * Merch Filter Functionality
 * Фильтрация товаров по категориям
 */

(function() {
  'use strict';

  function decode(str) {
    try { return decodeURIComponent(str); } catch (e) { return str; }
  }

  function initMerchFilter() {
    const filterButtons = document.querySelectorAll('.oor-merch-filter-btn');
    const products = document.querySelectorAll('.oor-merch-product');

    if (!filterButtons.length || !products.length) {
      return;
    }

    function getUrlFilter() {
      try {
        var params = new URLSearchParams(window.location.search);
        var value = params.get('product_cat');
        return value && value.trim() ? value.trim() : 'all';
      } catch (e) {
        return 'all';
      }
    }

    function applyFilter(filterRaw) {
      var raw = filterRaw ? String(filterRaw).trim() : 'all';
      var filterValue = raw ? decode(raw) : 'all';

      var availableFilters = Array.from(filterButtons).map(function(btn) {
        return decode(btn.getAttribute('data-filter') || 'all');
      });

      var activeFilter = (filterValue !== 'all' && availableFilters.indexOf(filterValue) === -1)
        ? 'all'
        : filterValue;

      filterButtons.forEach(function(btn) {
        var btnFilter = decode(btn.getAttribute('data-filter') || 'all');
        btn.classList.toggle('oor-merch-filter-btn--active', btnFilter === activeFilter);
      });

      products.forEach(function(product) {
        var categoryAttr = product.getAttribute('data-category') || '';
        var categories = categoryAttr.trim()
          ? categoryAttr.trim().split(/\s+/).map(decode)
          : [];

        product.style.display = (activeFilter === 'all' || categories.indexOf(activeFilter) !== -1)
          ? 'block'
          : 'none';
      });
    }

    function navigateToFilter(filterRaw) {
      var raw = filterRaw ? String(filterRaw).trim() : 'all';
      var filterValue = raw ? decode(raw) : 'all';

      var url = new URL(window.location.href);

      if (filterValue === 'all') {
        url.searchParams.delete('product_cat');
      } else {
        url.searchParams.set('product_cat', filterValue);
      }

      url.searchParams.delete('paged');
      window.location.href = url.toString();
    }

    var pageLoadedWithFilter = getUrlFilter() !== 'all';

    filterButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        var filter = this.getAttribute('data-filter');
        var clickedFilter = filter ? String(filter).trim() : 'all';

        applyFilter(clickedFilter);

        if (pageLoadedWithFilter) {
          navigateToFilter(clickedFilter);
        }
      });
    });

    applyFilter(getUrlFilter());
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMerchFilter);
  } else {
    initMerchFilter();
  }
})();
