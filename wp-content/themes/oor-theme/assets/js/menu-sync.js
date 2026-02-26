// Синхронизация меню
// Синхронизация активных состояний между десктопным и мобильным меню

class MenuSync {
  constructor() {
    this.desktopMenu = document.querySelector('.oor-nav');
    this.mobileMenu = document.getElementById('mobile-menu');
    this.currentActiveItem = 'main';
    
    this.init();
  }
  
  init() {
    if (!this.desktopMenu || !this.mobileMenu) {
      console.warn('Menu elements not found for sync');
      return;
    }
    
    // Fix header background for studio page
    if (document.body.classList.contains('oor-studio-page')) {
      const header = document.querySelector('.oor-header');
      if (header) {
        header.style.setProperty('background', '#000', 'important');
        header.style.setProperty('mix-blend-mode', 'normal', 'important');
      }
    }
    
    this.bindEvents();
    
    // Задержка для инициализации после загрузки других скриптов (включая rolling-text)
    setTimeout(() => {
      this.setActiveItemFromUrl();
    }, 200);
  }
  
  bindEvents() {
    // Синхронизация кликов по десктопному меню
    const desktopLinks = this.desktopMenu.querySelectorAll('[data-menu-item]');
    desktopLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        const menuItem = link.getAttribute('data-menu-item');
        
        // Если ссылка ведет на другую страницу (не якорь #), разрешаем переход
        if (href && href !== '#' && !href.startsWith('#')) {
          // Не вызываем preventDefault, позволяем браузеру перейти по ссылке
          if (menuItem) {
            this.setActiveItem(menuItem);
          }
          return;
        }
        
        // Для якорных ссылок (#) блокируем переход и только меняем активное состояние
        e.preventDefault();
        if (menuItem) {
          this.setActiveItem(menuItem);
        }
        this.closeMobileMenuIfOpen();
      });
    });
    
    // Синхронизация кликов по мобильному меню
    const mobileLinks = this.mobileMenu.querySelectorAll('[data-menu-item]');
    mobileLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        const menuItem = link.getAttribute('data-menu-item');
        
        // Если ссылка ведет на другую страницу (не якорь #), разрешаем переход
        if (href && href !== '#' && !href.startsWith('#')) {
          // Не вызываем preventDefault, позволяем браузеру перейти по ссылке
          if (menuItem) {
            this.setActiveItem(menuItem);
          }
          // Добавляем небольшую задержку перед закрытием меню, чтобы переход успел произойти
          setTimeout(() => {
            this.closeMobileMenuIfOpen();
          }, 100);
          return;
        }
        
        // Для якорных ссылок (#) блокируем переход и только меняем активное состояние
        e.preventDefault();
        if (menuItem) {
          this.setActiveItem(menuItem);
        }
        this.closeMobileMenuIfOpen();
      });
    });
    
    // Синхронизация кликов по футер-меню
    const footerLinks = document.querySelectorAll('.oor-footer-link[data-menu-item]');
    footerLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        const menuItem = link.getAttribute('data-menu-item');
        
        // Если ссылка ведет на другую страницу (не якорь #), разрешаем переход
        if (href && href !== '#' && !href.startsWith('#')) {
          // Не вызываем preventDefault, позволяем браузеру перейти по ссылке
          if (menuItem) {
            this.setActiveItem(menuItem);
          }
          return;
        }
        
        // Для якорных ссылок (#) блокируем переход и только меняем активное состояние
        e.preventDefault();
        if (menuItem) {
          this.setActiveItem(menuItem);
        }
      });
    });
    
    // Слушаем изменения URL для автоматической синхронизации
    window.addEventListener('popstate', () => {
      this.setActiveItemFromUrl();
    });
    
    // Синхронизация кнопок "Стать артистом"
    const desktopBtn = document.querySelector('.oor-btn-small[data-action="become-artist"]');
    const mobileBtn = document.querySelector('.oor-mobile-menu-btn[data-action="become-artist"]');
    
    if (desktopBtn) {
      desktopBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleBecomeArtistClick();
      });
    }
    
    if (mobileBtn) {
      mobileBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleBecomeArtistClick();
        this.closeMobileMenuIfOpen();
      });
    }
    
    // Слушаем события открытия/закрытия мобильного меню
    window.addEventListener('mobileMenuOpen', () => {
      this.syncActiveStates();
    });
    
    window.addEventListener('mobileMenuClose', () => {
      // Дополнительная синхронизация при закрытии
      this.syncActiveStates();
    });
    
    // Временно отключаем MutationObserver для избежания бесконечных циклов
    // Синхронизация будет происходить через события клика и popstate
  }
  
  setActiveItem(menuItem) {
    this.currentActiveItem = menuItem;
    
    // Обновляем десктопное меню
    this.updateDesktopMenu(menuItem);
    
    // Обновляем мобильное меню
    this.updateMobileMenu(menuItem);
    
    // Уведомляем другие компоненты об изменении активного пункта
    window.dispatchEvent(new CustomEvent('menuItemChanged', {
      detail: { menuItem }
    }));
  }
  
  updateDesktopMenu(menuItem) {
    if (!this.desktopMenu) return;
    
    const desktopLinks = this.desktopMenu.querySelectorAll('[data-menu-item]');
    
    desktopLinks.forEach(link => {
      const linkMenuItem = link.getAttribute('data-menu-item');
      const atom = link.querySelector('.tn-atom');
      
      if (linkMenuItem === menuItem) {
        link.classList.add('oor-nav-link--active');
        
        // Добавляем скобки для rolling text структуры
        // ВАЖНО: Добавляем только в первый блок, т.к. второй блок скрыт (translateY(100%))
        if (atom && !atom.dataset.bracketsAdded) {
          const blocks = atom.querySelectorAll('.block');
          
          // Обрабатываем только первый блок
          const firstBlock = blocks[0];
          if (firstBlock) {
            // Проверяем, есть ли уже скобки
            const hasBrackets = firstBlock.querySelector('.bracket-start') || firstBlock.querySelector('.bracket-end');
            if (!hasBrackets) {
              // Получаем первый и последний элементы с классом letter
              const letters = firstBlock.querySelectorAll('.letter');
              if (letters.length > 0) {
                const firstLetter = letters[0];
                const lastLetter = letters[letters.length - 1];
                
                // Проверяем текстовое содержимое
                if (firstLetter.textContent.trim() !== '[' && lastLetter.textContent.trim() !== ']') {
                  // Добавляем открывающую скобку
                  const bracketStart = document.createElement('span');
                  bracketStart.classList.add('letter', 'bracket-start');
                  bracketStart.innerText = '[';
                  bracketStart.style.marginRight = '2px';
                  firstBlock.insertBefore(bracketStart, firstLetter);
                  
                  // Добавляем закрывающую скобку
                  const bracketEnd = document.createElement('span');
                  bracketEnd.classList.add('letter', 'bracket-end');
                  bracketEnd.innerText = ']';
                  bracketEnd.style.marginLeft = '2px';
                  firstBlock.appendChild(bracketEnd);
                }
              }
            }
          }
          
          // Помечаем, что скобки добавлены
          atom.dataset.bracketsAdded = 'true';
        }
      } else {
        link.classList.remove('oor-nav-link--active');
        // Удаляем скобки
        if (atom) {
          const blocks = atom.querySelectorAll('.block');
          blocks.forEach(block => {
            const bracketStart = block.querySelector('.bracket-start');
            const bracketEnd = block.querySelector('.bracket-end');
            if (bracketStart) bracketStart.remove();
            if (bracketEnd) bracketEnd.remove();
          });
          // Сбрасываем флаг, чтобы скобки можно было добавить снова при активации
          delete atom.dataset.bracketsAdded;
        }
      }
    });
    
    // Также обновляем футер, если там есть ссылки с data-menu-item
    const footerLinks = document.querySelectorAll('.oor-footer-link[data-menu-item]');
    footerLinks.forEach(link => {
      const linkMenuItem = link.getAttribute('data-menu-item');
      if (linkMenuItem === menuItem) {
        link.classList.add('oor-footer-link--active');
      } else {
        link.classList.remove('oor-footer-link--active');
      }
    });
  }
  
  updateMobileMenu(menuItem) {
    if (!this.mobileMenu) return;
    
    const mobileLinks = this.mobileMenu.querySelectorAll('[data-menu-item]');
    
    mobileLinks.forEach(link => {
      const linkMenuItem = link.getAttribute('data-menu-item');
      if (linkMenuItem === menuItem) {
        link.classList.add('oor-mobile-menu-link--active');
      } else {
        link.classList.remove('oor-mobile-menu-link--active');
      }
    });
  }
  
  syncActiveStates() {
    // Двусторонняя синхронизация состояний
    // Сначала проверяем десктопное меню
    const activeDesktopLink = this.desktopMenu.querySelector('.oor-nav-link--active');
    if (activeDesktopLink) {
      const menuItem = activeDesktopLink.getAttribute('data-menu-item');
      if (menuItem && menuItem !== this.currentActiveItem) {
        this.setActiveItem(menuItem);
      }
      this.updateMobileMenu(menuItem || this.currentActiveItem);
      return;
    }
    
    // Если нет активного в десктопном, проверяем мобильное
    const activeMobileLink = this.mobileMenu.querySelector('.oor-mobile-menu-link--active');
    if (activeMobileLink) {
      const menuItem = activeMobileLink.getAttribute('data-menu-item');
      if (menuItem && menuItem !== this.currentActiveItem) {
        this.setActiveItem(menuItem);
      }
    }
  }
  
  setInitialActiveState() {
    // Устанавливаем начальное активное состояние
    const activeDesktopLink = this.desktopMenu.querySelector('.oor-nav-link--active');
    if (activeDesktopLink) {
      const menuItem = activeDesktopLink.getAttribute('data-menu-item');
      this.setActiveItem(menuItem);
    } else if (!document.body.classList.contains('oor-become-artist-page')) {
      this.setActiveItem('main');
    }
  }
  
  closeMobileMenuIfOpen() {
    // Закрываем мобильное меню если оно открыто
    if (window.mobileMenuInstance && window.mobileMenuInstance.isMenuOpen()) {
      window.mobileMenuInstance.closeMenu();
    }
  }
  
  handleBecomeArtistClick() {
    // Обработка клика по кнопке "Стать артистом"
    // Переход на страницу /become-artist и опциональный эвент
    let targetUrl = null;

    // Пытаемся взять href из десктопной кнопки, если она существует
    const desktopBtn = document.querySelector('.oor-btn-small[data-action=\"become-artist\"]');
    if (typeof window !== 'undefined' && desktopBtn) {
      targetUrl = desktopBtn.getAttribute('href');
    }

    // Fallback: если по какой-то причине href не найден, используем стандартный путь
    if (!targetUrl) {
      if (window.oorPaths && window.oorPaths.base) {
        targetUrl = window.oorPaths.base.replace(/\/$/, '') + '/become-artist/';
      } else {
        targetUrl = '/become-artist/';
      }
    }

    // Уведомляем другие компоненты о клике
    window.dispatchEvent(new CustomEvent('becomeArtistClicked', {
      detail: { url: targetUrl }
    }));

    // Выполняем реальный переход
    window.location.href = targetUrl;
  }
  
  // Публичные методы
  getCurrentActiveItem() {
    return this.currentActiveItem;
  }
  
  setActiveItemFromUrl() {
    // Устанавливаем активный пункт меню на основе URL
    const path = window.location.pathname;
    const hash = window.location.hash;
    
    // Простая логика определения активного пункта по URL
    let menuItem = 'main'; // По умолчанию главная
    
    // Проверяем hash первым (более приоритетный)
    if (hash) {
      if (hash === '#manifest') {
        menuItem = 'manifest';
      } else if (hash === '#artists') {
        menuItem = 'artists';
      } else if (hash === '#studio') {
        menuItem = 'studio';
      } else if (hash === '#services') {
        menuItem = 'services';
      } else if (hash === '#dawgs') {
        menuItem = 'dawgs';
      } else if (hash === '#talk-show') {
        menuItem = 'talk-show';
      } else if (hash === '#events') {
        menuItem = 'events';
      } else if (hash === '#merch') {
        menuItem = 'merch';
      } else if (hash === '#contacts') {
        menuItem = 'contacts';
      }
    } else if (path && path !== '/' && path !== '/index.html' && path !== '') {
      if (path.includes('/manifest')) {
        menuItem = 'manifest';
      } else if (path.includes('/artists')) {
        menuItem = 'artists';
      } else if (path.includes('/studio')) {
        menuItem = 'studio';
      } else if (path.includes('/services')) {
        menuItem = 'services';
      } else if (path.includes('/dawgs')) {
        menuItem = 'dawgs';
      } else if (path.includes('/talk-show')) {
        menuItem = 'talk-show';
      } else if (path.includes('/events')) {
        menuItem = 'events';
      } else if (path.includes('/merch') || path.includes('/shop') || path.includes('/cart') || path.includes('/checkout')) {
        menuItem = 'merch';
      } else if (path.includes('/contacts')) {
        menuItem = 'contacts';
      } else if (path.includes('/become-artist')) {
        menuItem = null;
      }
    }
    // Страница магазина WooCommerce (любой slug): подсветка «Мерч»
    const body = document.body.classList;
    if ((body.contains('oor-merch-page') || body.contains('oor-cart-page') || body.contains('oor-checkout-page') || body.contains('oor-product-page')) && (!menuItem || menuItem === 'main')) {
      menuItem = 'merch';
    }
    // Страница «Стать артистом» — ни один пункт не активен
    if (body.contains('oor-become-artist-page')) {
      menuItem = null;
    }

    if (menuItem) {
      this.setActiveItem(menuItem);
    } else {
      this.clearAllActive();
    }
  }
  
  clearAllActive() {
    this.currentActiveItem = null;
    if (this.desktopMenu) {
      this.desktopMenu.querySelectorAll('.oor-nav-link--active').forEach(link => {
        link.classList.remove('oor-nav-link--active');
        const atom = link.querySelector('.tn-atom');
        if (atom) {
          atom.querySelectorAll('.block').forEach(block => {
            const bs = block.querySelector('.bracket-start');
            const be = block.querySelector('.bracket-end');
            if (bs) bs.remove();
            if (be) be.remove();
          });
          delete atom.dataset.bracketsAdded;
        }
      });
    }
    if (this.mobileMenu) {
      this.mobileMenu.querySelectorAll('.oor-mobile-menu-link--active').forEach(link => {
        link.classList.remove('oor-mobile-menu-link--active');
      });
    }
  }
}

// Инициализация синхронизации меню
let menuSyncInstance = null;

function initMenuSync() {
  if (menuSyncInstance) {
    // Очищаем предыдущий экземпляр если есть
    menuSyncInstance = null;
  }
  
  menuSyncInstance = new MenuSync();
  window.menuSyncInstance = menuSyncInstance;
}

// Автоматическая инициализация при загрузке DOM
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initMenuSync);
} else {
  initMenuSync();
}

// Экспорт для внешнего использования
window.MenuSync = MenuSync;
