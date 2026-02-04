// Мобильное меню и сайдбар
// Управление открытием/закрытием мобильного меню

class MobileMenu {
  constructor() {
    this.burgerButton = document.getElementById('burger-menu');
    this.mobileMenu = document.getElementById('mobile-menu');
    this.closeButton = document.getElementById('close-menu');
    this.overlay = null;
    
    this.isOpen = false;
    
    this.init();
  }
  
  init() {
    if (!this.burgerButton || !this.mobileMenu || !this.closeButton) {
      console.warn('Mobile menu elements not found');
      return;
    }
    
    this.bindEvents();
  }
  
  bindEvents() {
    // Открытие меню
    this.burgerButton.addEventListener('click', () => {
      this.openMenu();
    });
    
    // Закрытие меню
    this.closeButton.addEventListener('click', () => {
      this.closeMenu();
    });
    
    
    // Закрытие по Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen) {
        this.closeMenu();
      }
    });
    
    // Закрытие при изменении размера окна (переход на десктоп)
    window.addEventListener('resize', () => {
      if (window.innerWidth > 1024 && this.isOpen) {
        this.closeMenu();
      }
    });
  }
  
  openMenu() {
    if (this.isOpen) return;
    
    this.isOpen = true;
    this.mobileMenu.classList.add('active');
    
    // Блокируем скролл страницы через класс (работает с CSS !important)
    document.body.classList.add('scroll-locked');
    document.documentElement.classList.add('scroll-locked');
    
    // Анимация появления
    requestAnimationFrame(() => {
      this.mobileMenu.style.right = '0';
    });
    
    // Уведомляем другие компоненты об открытии меню
    window.dispatchEvent(new CustomEvent('mobileMenuOpen'));
  }
  
  closeMenu() {
    if (!this.isOpen) return;
    
    this.isOpen = false;
    this.mobileMenu.classList.remove('active');
    
    // Разблокируем скролл страницы
    document.body.classList.remove('scroll-locked');
    document.documentElement.classList.remove('scroll-locked');
    
    // Анимация исчезновения
    this.mobileMenu.style.right = window.innerWidth <= 460 ? '-100%' : '-360px';
    
    // Уведомляем другие компоненты о закрытии меню
    window.dispatchEvent(new CustomEvent('mobileMenuClose'));
  }
  
  toggleMenu() {
    if (this.isOpen) {
      this.closeMenu();
    } else {
      this.openMenu();
    }
  }
  
  // Публичные методы для внешнего управления
  isMenuOpen() {
    return this.isOpen;
  }
  
  destroy() {
    // Очистка при необходимости
  }
}

// Инициализация мобильного меню
let mobileMenuInstance = null;

function initMobileMenu() {
  if (mobileMenuInstance) {
    mobileMenuInstance.destroy();
  }
  
  mobileMenuInstance = new MobileMenu();
}

// Автоматическая инициализация при загрузке DOM
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initMobileMenu);
} else {
  initMobileMenu();
}

// Экспорт для внешнего использования
window.MobileMenu = MobileMenu;
window.mobileMenuInstance = mobileMenuInstance;
