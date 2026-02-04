/**
 * Become Artist Page Scripts
 * Auto-expanding textarea for "О себе" field
 * Hide text labels before fields (use placeholder instead)
 */

(function() {
  'use strict';

  function hideTextLabels() {
    // Скрываем текст перед полями и помечаем примечание про демо
    const form = document.querySelector('.wpcf7-form');
    if (!form) return;

    const paragraphs = form.querySelectorAll('p');
    paragraphs.forEach(p => {
      const text = (p.textContent || '').trim();

      // если это примечание про демо-материалы — навешиваем класс и больше не трогаем
      if (text.indexOf('Демо-материалы рассматриваются только в виде ссылок') !== -1) {
        p.classList.add('oor-demo-note');
        return;
      }

      // Пропускаем если уже обработан
      if (p.dataset.processed === 'true') return;
      
      const wrap = p.querySelector('.wpcf7-form-control-wrap');
      if (!wrap) return;

      // Удаляем текстовые узлы и <br> перед оберткой поля
      let node = p.firstChild;
      const nodesToRemove = [];
      
      while (node && node !== wrap) {
        if (node.nodeType === Node.TEXT_NODE) {
          const t = node.textContent.trim();
          if (t) {
            nodesToRemove.push(node);
          }
        } else if (node.nodeType === Node.ELEMENT_NODE && node.tagName === 'BR') {
          nodesToRemove.push(node);
          break;
        }
        node = node.nextSibling;
      }

      nodesToRemove.forEach(n => {
        if (n.parentNode) {
          n.parentNode.removeChild(n);
        }
      });
      
      // Помечаем как обработанный
      p.dataset.processed = 'true';
    });
  }

  function initAutoExpandTextarea() {
    const textareas = document.querySelectorAll('.oor-form-textarea, .wpcf7-form textarea[name="about-yourself"]');
    
    textareas.forEach(textarea => {
      // Пропускаем если уже обработан
      if (textarea.dataset.autoExpand === 'true') return;
      
      // КРИТИЧНО: удаляем атрибут rows, который CF7 добавляет по умолчанию
      textarea.removeAttribute('rows');
      textarea.removeAttribute('cols');
      
      const minHeight = 40; // начальная высота из CSS
      const maxHeight = 300; // max-height из CSS
      
      // Принудительно устанавливаем начальную высоту
      textarea.style.setProperty('height', minHeight + 'px', 'important');
      textarea.style.setProperty('overflow-y', 'hidden', 'important');
      
      // Функция для установки высоты
      function setHeight() {
        const currentValue = textarea.value || '';
        
        // Если поле пустое, возвращаем начальную высоту
        if (!currentValue.trim()) {
          textarea.style.setProperty('height', minHeight + 'px', 'important');
          textarea.style.setProperty('overflow-y', 'hidden', 'important');
          return;
        }
        
        // Временно сбрасываем для расчёта
        textarea.style.height = 'auto';
        
        const scrollHeight = textarea.scrollHeight;
        
        // Устанавливаем высоту в зависимости от контента
        if (scrollHeight <= minHeight) {
          textarea.style.setProperty('height', minHeight + 'px', 'important');
          textarea.style.setProperty('overflow-y', 'hidden', 'important');
        } else if (scrollHeight <= maxHeight) {
          textarea.style.setProperty('height', scrollHeight + 'px', 'important');
          textarea.style.setProperty('overflow-y', 'hidden', 'important');
        } else {
          textarea.style.setProperty('height', maxHeight + 'px', 'important');
          textarea.style.setProperty('overflow-y', 'auto', 'important');
        }
      }
      
      // Обновляем при вводе
      textarea.addEventListener('input', setHeight);
      
      // Помечаем как обработанный
      textarea.dataset.autoExpand = 'true';
    });
  }

  function init() {
    hideTextLabels();
    initAutoExpandTextarea();
  }

  // Инициализация при загрузке DOM
  function runInit() {
    // Несколько попыток, так как форма может загружаться асинхронно
    init();
    
    // Повторяем через небольшие интервалы для надежности
    setTimeout(init, 100);
    setTimeout(init, 500);
    setTimeout(init, 1000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runInit);
  } else {
    runInit();
  }

  // Также инициализируем после загрузки Contact Form 7 (если форма загружается динамически)
  if (typeof jQuery !== 'undefined') {
    jQuery(document).on('wpcf7mailsent wpcf7invalid wpcf7spam wpcf7mailfailed wpcf7submit DOMContentLoaded', function() {
      setTimeout(init, 100);
    });
  }

  // Наблюдатель за изменениями DOM (на случай динамической загрузки формы)
  if (window.MutationObserver) {
    const observer = new MutationObserver(function(mutations) {
      let shouldInit = false;
      mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
          for (let i = 0; i < mutation.addedNodes.length; i++) {
            const node = mutation.addedNodes[i];
            if (node.nodeType === Node.ELEMENT_NODE && 
                (node.classList.contains('wpcf7-form') || node.querySelector('.wpcf7-form'))) {
              shouldInit = true;
              break;
            }
          }
        }
      });
      if (shouldInit) {
        setTimeout(init, 100);
      }
    });

    // document.body может быть ещё не создан, если скрипт грузится в <head>
    const startObserver = function () {
      const target = document.body || document.documentElement;
      if (!target) return;
      observer.observe(target, {
        childList: true,
        subtree: true
      });
    };

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', startObserver);
    } else {
      startObserver();
    }
  }
})();
