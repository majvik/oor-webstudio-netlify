// Масочный reveal для заголовков (слайд снизу вверх из клипа)
function initMaskedHeadingsReveal() {
  if (window.innerWidth <= 1024) return;

  const headingSelectors = [
    '.oor-hero-title',
    '.oor-hero-description-title',
    '.oor-challenge-title',
    '.oor-challenge-2-studio-title',
    '.oor-challenge-2-good-works-title',
    '.oor-out-of-talk-heading',
    '.oor-events-heading',
    '.oor-merch-title'
  ];
  const headings = headingSelectors.flatMap(sel => Array.from(document.querySelectorAll(sel)));
  if (headings.length === 0) return;

  // Оборачиваем текст в маску
  headings.forEach(h => {
    if (h.closest('.oor-heading-mask')) return;
    const mask = document.createElement('span');
    mask.className = 'oor-heading-mask';
    // Блочная маска, чтобы корректно охватывать многострочные заголовки
    mask.style.display = 'block';
    mask.style.overflow = 'hidden';
    mask.style.verticalAlign = 'bottom';
    // переносим детей: сначала в mask, затем в внутренний контейнер, чтобы анимировать весь текст целиком
    while (h.firstChild) mask.appendChild(h.firstChild);
    h.appendChild(mask);

    const inner = document.createElement('span');
    inner.className = 'oor-heading-inner';
    inner.style.display = 'inline-block';
    // перемещаем ВСЕ узлы mask во внутренний контейнер
    while (mask.firstChild) inner.appendChild(mask.firstChild);
    mask.appendChild(inner);

    // начальные стили содержимого
    inner.style.transform = 'translate3d(0, 120%, 0)';
    inner.style.opacity = '0';
    inner.style.transition = 'transform 900ms cubic-bezier(0.84, 0.00, 0.16, 1), opacity 900ms ease';
    inner.style.willChange = 'transform, opacity';
  });

  const io = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const h = entry.target;
        const mask = h.querySelector('.oor-heading-mask');
        const inner = mask && mask.querySelector('.oor-heading-inner');
        if (inner) {
          inner.style.transform = 'translate3d(0, 0, 0)';
          inner.style.opacity = '1';
          setTimeout(() => { try { inner.style.willChange = ''; } catch(_) {} }, 1000);
        }
        observer.unobserve(h);
      }
    });
  }, { threshold: 0.2, rootMargin: '0px 0px -10% 0px' });

  // Старт наблюдения с небольшим кадром задержки
  requestAnimationFrame(() => {
    headings.forEach(h => io.observe(h));
  });
}
// Главный файл JavaScript для проекта OOR
document.addEventListener('DOMContentLoaded', function() {
  if (typeof gsap === 'undefined') {
    console.error('[OOR] GSAP not loaded - some animations may not work');
    loadGSAPFallback();
  }
  // Страница товара: перечёркивание старой цены (сразу и с задержкой, если цена подгружается позже)
  initProductPriceStrikethrough();
  setTimeout(initProductPriceStrikethrough, 100);
  setTimeout(initProductPriceStrikethrough, 500);
  // Баблы вариаций (размер и т.д.) — клик меняет скрытый select
  initVariationBubbles();
  setTimeout(initVariationBubbles, 300);
  setTimeout(initVariationBubbles, 800);
  try {
    if (typeof window.initPreloader === 'function') {
      window.initPreloader();
    } else if (typeof initPreloader === 'function') {
      initPreloader();
    } else {
      console.error('[OOR] initPreloader function not found');
    }
    installScrollUnlockWatchdog();
    installScrollDiagnostics();
  } catch (error) {
    console.error('[OOR] DOMContentLoaded error:', error);
    document.documentElement.classList.remove('preloader-active');
    document.body.classList.remove('preloader-active');
    document.documentElement.style.overflow = 'auto';
    document.body.style.overflow = 'auto';
  }
});

function loadGSAPFallback() {
  const script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js';
  script.onload = function() {
    if (typeof initPreloader === 'function') {
      initPreloader();
    }
  };
  script.onerror = function() {
    console.error('[OOR] Failed to load GSAP fallback');
  };
  document.head.appendChild(script);
}

window.addEventListener('load', function() {
  // Инициализация навигации для страниц магазина
  initMerchNavigation();
  // Страница товара: перечёркивание старой цены (если PHP не вывел <del>/<ins>)
  initProductPriceStrikethrough();
  try {
    initNavigation();
    initDynamicYear();
    initHeroVideo();
    initFullscreenVideo();
    initMagneticElements();
    initOrphanControl();
    initRetinaSupport();
    initParallaxImages();
    initRevealOnScroll();
    initMaskedHeadingsReveal();
    
    const DISABLE_LENIS = (typeof window !== 'undefined') && window.location && (window.location.search.includes('nolenis') || window.location.search.includes('disablelenis'));
    if (DISABLE_LENIS) {
      document.documentElement.classList.remove('preloader-active');
      document.body.classList.remove('preloader-active');
      document.documentElement.style.overflow = 'auto';
      document.body.style.overflow = 'auto';
      document.documentElement.style.overflowY = 'auto';
      document.body.style.overflowY = 'auto';
    }
  } catch (error) {
    console.error('[OOR] Window load error:', error);
    document.documentElement.classList.remove('preloader-active');
    document.body.classList.remove('preloader-active');
    document.documentElement.style.overflow = 'auto';
    document.body.style.overflow = 'auto';
  }
});

// Retina поддержка для изображений и фоновых слоев
function initRetinaSupport() {
  const isHighDPR = (typeof window !== 'undefined') && (window.devicePixelRatio && window.devicePixelRatio >= 2);
  if (!isHighDPR) return;

  // Генерирует @2x версию URL для retina
  function build2xUrl(url) {
    try {
      const qIndex = url.indexOf('?');
      const base = qIndex >= 0 ? url.slice(0, qIndex) : url;
      const query = qIndex >= 0 ? url.slice(qIndex) : '';
      const dot = base.lastIndexOf('.');
      if (dot <= 0) return null;
      return base.slice(0, dot) + '@2x' + base.slice(dot) + query;
    } catch (_) { return null; }
  }

  // Проверяет существование изображения
  function imageExists(url) {
    return new Promise(resolve => {
      const img = new Image();
      img.onload = () => resolve(true);
      img.onerror = () => resolve(false);
      img.src = url;
    });
  }

  // Обновляем <img> без srcset для использования @2x версий
  (async () => {
    const imgs = Array.from(document.querySelectorAll('img'))
      .filter(img => !img.closest('picture'))
      .filter(img => !/\.svg($|\?)/i.test(img.src))
      .filter(img => !img.hasAttribute('srcset'));

    for (const img of imgs) {
      const src = img.getAttribute('src');
      if (!src) continue;
      if (/video-cover/i.test(src)) continue;
      if (/splash\.gif/i.test(src)) continue;
      if (/events-gallery/i.test(src)) continue;
      if (/events-card/i.test(src)) continue;
      const hi = build2xUrl(src);
      if (!hi) continue;
      const ok = await imageExists(hi);
      if (ok) {
        img.setAttribute('srcset', `${src} 1x, ${hi} 2x`);
      }
    }
  })();

  // Обновляем фоновые слои для параллакса
  (async () => {
    const layers = Array.from(document.querySelectorAll('.oor-parallax-bg'));
    for (const layer of layers) {
      const bg = getComputedStyle(layer).backgroundImage;
      const match = /url\(("|')?(.*?)("|')?\)/.exec(bg || '');
      if (!match || !match[2]) continue;
      const url = match[2];
      if (/\.svg($|\?)/i.test(url)) continue;
      const hi = build2xUrl(url);
      if (!hi) continue;
      const ok = await imageExists(hi);
      if (ok) {
        layer.style.backgroundImage = `image-set(url(${url}) 1x, url(${hi}) 2x)`;
      }
    }
  })();

  // Обновляем все элементы с фоновыми изображениями
  (async () => {
    const all = Array.from(document.querySelectorAll('*'));
    for (const el of all) {
      if (el.classList && el.classList.contains('oor-parallax-bg')) continue;
      const cs = getComputedStyle(el);
      const bg = cs.backgroundImage;
      if (!bg || bg === 'none') continue;
      if (/image-set\(/i.test(bg)) continue;
      if (/,/.test(bg)) continue;
      if (/gradient\(/i.test(bg)) continue;
      if (/video-cover/i.test(bg)) continue;
      const match = /url\(("|')?(.*?)("|')?\)/.exec(bg);
      if (!match || !match[2]) continue;
      const url = match[2];
      if (/\.svg($|\?)/i.test(url)) continue;
      const hi = build2xUrl(url);
      if (!hi) continue;
      const ok = await imageExists(hi);
      if (ok) {
        el.style.backgroundImage = `image-set(url(${url}) 1x, url(${hi}) 2x)`;
      }
    }
  })();
}

// Параллакс для изображений
function initParallaxImages() {
  if (window.innerWidth <= 460) return;

  // Отключаем параллакс на страницах магазина
  if (document.body.classList.contains('oor-merch-page') || document.body.classList.contains('oor-product-page')) {
    return;
  }

  const allImages = Array.from(document.querySelectorAll('img'));
  const candidates = allImages.filter(img => {
    const src = (img.getAttribute('src') || '').toLowerCase();
    const isSvg = src.endsWith('.svg');
    if (isSvg) return false;
    // Исключаем splash gif на прелоадере (и замороженный canvas), чтобы не оборачивать и не пересчитывать его
    if (img.id === 'splash-gif' || img.id === 'splash-gif-frozen' || img.closest('.oor-splash-screen')) return false;
    if (img.closest('#wsls')) return false;
    if (img.closest('.oor-merch-images-grid')) return false;
    if (img.closest('.oor-merch-products-grid')) return false;
    if (img.closest('.oor-product-images')) return false;
    if (img.closest('.oor-events-posters')) return false;
    if (img.closest('.oor-events-hero-gallery')) return false;
    if (img.closest('.oor-events-listing-card-image')) return false;
    if (img.closest('.oor-events-hero')) return false;
    if (img.closest('.oor-events-listing-section')) return false;
    if (img.closest('.oor-without-fear-image-2')) return false;
    if (img.closest('.oor-quality-img-container-2')) return false;
    if (img.closest('.oor-challenge-2-good-works-image')) return false;
    if (img.closest('.oor-out-of-talk-image-1')) return false;
    if (img.closest('.oor-out-of-talk-image-2')) return false;
    if (img.closest('.oor-out-of-talk-image-3')) return false;
    if (img.closest('.oor-studio-hero')) return false;
    if (img.closest('.oor-studio-content-section')) return false;
    if (img.closest('.oor-studio-equipment-section')) return false;
    if (img.closest('.oor-studio-recording-section')) return false;
    if (img.closest('.oor-talk-show-hero')) return false;
    if (img.closest('.oor-talk-show-episodes')) return false;
    if (img.closest('.oor-talk-show-rules')) return false;
    if (img.classList.contains('no-parallax')) return false;
    return true;
  });
  
  // Отдельно обрабатываем picture элементы - оборачиваем сам picture, а не img внутри
  const pictureElements = Array.from(document.querySelectorAll('picture')).filter(picture => {
    const img = picture.querySelector('img');
    if (!img) return false;
    const src = (img.getAttribute('src') || '').toLowerCase();
    const isSvg = src.endsWith('.svg');
    if (isSvg) return false;
    // Применяем те же исключения, что и для обычных img
    if (img.closest('#wsls')) return false;
    if (img.closest('.oor-merch-images-grid')) return false;
    if (img.closest('.oor-merch-products-grid')) return false;
    if (img.closest('.oor-product-images')) return false;
    if (img.closest('.oor-events-posters')) return false;
    if (img.closest('.oor-events-hero-gallery')) return false;
    if (img.closest('.oor-events-listing-card-image')) return false;
    if (img.closest('.oor-events-hero')) return false;
    if (img.closest('.oor-events-listing-section')) return false;
    if (img.closest('.oor-without-fear-image-2')) return false;
    if (img.closest('.oor-quality-img-container-2')) return false;
    if (img.closest('.oor-challenge-2-good-works-image')) return false;
    if (img.closest('.oor-out-of-talk-image-1')) return false;
    if (img.closest('.oor-out-of-talk-image-2')) return false;
    if (img.closest('.oor-out-of-talk-image-3')) return false;
    if (img.closest('.oor-studio-hero')) return false;
    if (img.closest('.oor-studio-content-section')) return false;
    if (img.closest('.oor-studio-equipment-section')) return false;
    if (img.closest('.oor-studio-recording-section')) return false;
    if (img.closest('.oor-talk-show-hero')) return false;
    if (img.closest('.oor-talk-show-episodes')) return false;
    if (img.closest('.oor-talk-show-rules')) return false;
    if (picture.closest('.oor-parallax-wrap')) return false;
    if (img.classList.contains('no-parallax')) return false;
    return true;
  });

  // Поддержка параллакса для блоков с фоном
  const bgTargets = [];
  document.querySelectorAll('.oor-musical-association-right').forEach(el => {
    const cs = getComputedStyle(el);
    if (!cs.backgroundImage || cs.backgroundImage === 'none') return;
    if (el.querySelector('.oor-parallax-bg')) return;
    if (!el.style.position) el.style.position = 'relative';
    if (!el.style.overflow) el.style.overflow = 'hidden';
    const layer = document.createElement('div');
    layer.className = 'oor-parallax-bg';
    layer.style.position = 'absolute';
    layer.style.inset = '0';
    layer.style.backgroundImage = cs.backgroundImage;
    layer.style.backgroundSize = cs.backgroundSize || 'cover';
    layer.style.backgroundRepeat = cs.backgroundRepeat || 'no-repeat';
    layer.style.backgroundPosition = cs.backgroundPosition || 'center';
    layer.style.willChange = 'transform';
    const initScaleAttr = parseFloat(el.getAttribute('data-parallax-scale'));
    // scale() принимает числовое значение, не calc(). 1.3 = 130% (100% + 30%)
    const initScale = Number.isFinite(initScaleAttr) ? initScaleAttr : 1.3;
    layer.setAttribute('data-parallax-scale', String(initScale));
    layer.setAttribute('data-parallax-speed', el.getAttribute('data-parallax-speed') || '');
    layer.setAttribute('data-parallax-max', el.getAttribute('data-parallax-max') || '');
    layer.style.transform = `translate3d(0,0,0) scale(${initScale})`;
    el.style.backgroundImage = 'none';
    el.appendChild(layer);
    bgTargets.push(layer);
  });

  // Оборачиваем picture элементы в контейнеры для параллакса
  pictureElements.forEach(picture => {
    if (picture.closest('.oor-parallax-wrap')) return;
    const wrap = document.createElement('div');
    wrap.className = 'oor-parallax-wrap';
    wrap.style.position = 'relative';
    wrap.style.overflow = 'hidden';
    wrap.style.display = 'block';
    wrap.style.contain = 'paint';
    picture.parentNode.insertBefore(wrap, picture);
    wrap.appendChild(picture);
    
    const img = picture.querySelector('img');
    if (!img) return;
    
    img.style.display = 'block';
    img.style.willChange = 'transform';
    img.style.transformOrigin = 'center center';
    img.style.transformStyle = 'preserve-3d';
    img.style.backfaceVisibility = 'hidden';
    
    if (img.closest('.oor-without-fear-image')) {
      img.setAttribute('data-parallax-max', '32');
    }
    if (img.closest('.oor-quality-img-container-1')) {
      img.setAttribute('data-parallax-max', '24');
    }
    
    // Рассчитываем scale для заполнения контейнера
    const calculateAndFreezeScale = () => {
      const container = wrap;
      const containerRect = container.getBoundingClientRect();
      const parallaxMax = parseFloat(img.getAttribute('data-parallax-max')) || 64;
      const customScale = parseFloat(img.getAttribute('data-parallax-scale'));
      
      if (Number.isFinite(customScale)) {
        return Promise.resolve(customScale);
      }
      
      const reserve = Math.max(parallaxMax + 32, 80);
      
      return new Promise((resolve) => {
        if (img.complete && img.naturalWidth > 0) {
          const naturalWidth = img.naturalWidth;
          const naturalHeight = img.naturalHeight;
          const neededWidth = containerRect.width + reserve;
          const neededHeight = containerRect.height + reserve;
          const scaleX = neededWidth / naturalWidth;
          const scaleY = neededHeight / naturalHeight;
          resolve(Math.max(scaleX, scaleY, 1.0));
        } else {
          img.onload = () => {
            const naturalWidth = img.naturalWidth;
            const naturalHeight = img.naturalHeight;
            const neededWidth = containerRect.width + reserve;
            const neededHeight = containerRect.height + reserve;
            const scaleX = neededWidth / naturalWidth;
            const scaleY = neededHeight / naturalHeight;
            resolve(Math.max(scaleX, scaleY, 1.0));
          };
        }
      });
    };
    
    calculateAndFreezeScale().then(frozenScale => {
      img.setAttribute('data-frozen-scale', frozenScale.toString());
      img.style.transform = `translate3d(0,0,0) scale(${frozenScale})`;
    });
    
    img._recalculateScale = calculateAndFreezeScale;
    candidates.push(img); // Добавляем img из picture в candidates для наблюдения
  });

  if (candidates.length === 0 && bgTargets.length === 0) return;

  // Оборачиваем изображения в контейнеры для параллакса
  candidates.forEach(img => {
    // Пропускаем img, которые уже обработаны через picture
    if (img.closest('picture') && img.closest('.oor-parallax-wrap')) return;
    if (img.closest('.oor-parallax-wrap')) return;
    const wrap = document.createElement('div');
    wrap.className = 'oor-parallax-wrap';
    wrap.style.position = 'relative';
    wrap.style.overflow = 'hidden';
    wrap.style.display = 'block';
    wrap.style.width = img.style.width || '';
    wrap.style.height = '';
    wrap.style.contain = 'paint';
    img.parentNode.insertBefore(wrap, img);
    wrap.appendChild(img);
    img.style.display = 'block';
    img.style.willChange = 'transform';
    img.style.transformOrigin = 'center center';
    img.style.transformStyle = 'preserve-3d';
    img.style.backfaceVisibility = 'hidden';
    if (img.closest('.oor-without-fear-image')) {
      img.setAttribute('data-parallax-max', '32');
    }
    if (img.closest('.oor-quality-img-container-1')) {
      img.setAttribute('data-parallax-max', '24');
    }
    
    // Рассчитываем scale для заполнения контейнера
    const calculateAndFreezeScale = () => {
      const container = img.closest('.oor-parallax-wrap') || img.parentElement;
      const containerRect = container.getBoundingClientRect();
      const parallaxMax = parseFloat(img.getAttribute('data-parallax-max')) || 64;
      const customScale = parseFloat(img.getAttribute('data-parallax-scale'));
      
      if (Number.isFinite(customScale)) {
        return Promise.resolve(customScale);
      }
      
      const reserve = Math.max(parallaxMax + 32, 80);
      
      return new Promise((resolve) => {
        if (img.complete && img.naturalWidth > 0) {
          const naturalWidth = img.naturalWidth;
          const naturalHeight = img.naturalHeight;
          const neededWidth = containerRect.width + reserve;
          const neededHeight = containerRect.height + reserve;
          const scaleX = neededWidth / naturalWidth;
          const scaleY = neededHeight / naturalHeight;
          resolve(Math.max(scaleX, scaleY, 1.0));
        } else {
          img.onload = () => {
            const naturalWidth = img.naturalWidth;
            const naturalHeight = img.naturalHeight;
            const neededWidth = containerRect.width + reserve;
            const neededHeight = containerRect.height + reserve;
            const scaleX = neededWidth / naturalWidth;
            const scaleY = neededHeight / naturalHeight;
            resolve(Math.max(scaleX, scaleY, 1.0));
          };
        }
      });
    };
    
    calculateAndFreezeScale().then(frozenScale => {
      img.setAttribute('data-frozen-scale', frozenScale.toString());
      img.style.transform = `translate3d(0,0,0) scale(${frozenScale})`;
    });
    
    img._recalculateScale = calculateAndFreezeScale;
  });

  const observed = new Set();
  const inView = new Set();
  const io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      const node = entry.target;
      if (entry.isIntersecting) {
        inView.add(node);
      } else {
        inView.delete(node);
      }
    });
  }, { root: null, rootMargin: '100px 0px', threshold: [0, 0.1, 0.5, 1] });

  // Наблюдаем за img элементами (включая те, что внутри picture)
  candidates.forEach(img => { 
    if (!observed.has(img)) { 
      io.observe(img); 
      observed.add(img); 
    } 
  });
  bgTargets.forEach(layer => { 
    if (!observed.has(layer)) { 
      io.observe(layer); 
      observed.add(layer); 
    } 
  });

  const getVH = () => window.innerHeight || document.documentElement.clientHeight;
  let rafId = null;
  const lastShiftMap = new WeakMap();

  function frame() {
    const vh = getVH();
    inView.forEach(node => {
      const maxAttr = parseFloat(node.getAttribute('data-parallax-max'));
      const maxShift = Number.isFinite(maxAttr) ? maxAttr : 64;

      const rect = node.getBoundingClientRect();
      const centerY = rect.top + rect.height / 2;
      
      const norm = (centerY - vh) / vh;
      let shift = Math.max(-1, Math.min(1, norm)) * maxShift;
      shift = Math.round(shift * 2) / 2;
      
      let baseScale;
      const frozenScale = parseFloat(node.getAttribute('data-frozen-scale'));
      if (Number.isFinite(frozenScale)) {
        baseScale = frozenScale;
      } else {
        const baseScaleAttr = parseFloat(node.getAttribute('data-parallax-scale'));
        baseScale = Number.isFinite(baseScaleAttr) ? baseScaleAttr : 1.3;
      }
      
      const last = lastShiftMap.get(node);
      if (last === undefined || Math.abs(shift - last) >= 0.5) {
        node.style.transform = `translate3d(0, ${shift.toFixed(2)}px, 0) scale(${baseScale})`;
        lastShiftMap.set(node, shift);
      }
    });
    rafId = requestAnimationFrame(frame);
  }

  function start() { if (rafId == null) rafId = requestAnimationFrame(frame); }
  function stop() {
    if (rafId != null) cancelAnimationFrame(rafId);
    rafId = null;
    inView.forEach(node => { node.style.transform = ''; });
  }

  start();

  const onResize = () => {
    if (window.innerWidth <= 460) {
      stop();
    } else {
      candidates.forEach(img => {
        // Проверяем, что элемент все еще в DOM и не был заменен (например, splash-gif на canvas)
        if (img.parentNode && img._recalculateScale) {
          try {
            img._recalculateScale().then(newScale => {
              // Проверяем еще раз перед обновлением (элемент мог быть удален)
              if (img.parentNode) {
                img.setAttribute('data-frozen-scale', newScale.toString());
                img.style.transform = `translate3d(0,0,0) scale(${newScale})`;
              }
            });
          } catch (error) {
            console.warn('[Parallax] Error recalculating scale for image:', error);
          }
        }
      });
      if (rafId == null) start();
    }
  };
  window.addEventListener('resize', onResize);
  window.addEventListener('orientationchange', onResize);
  document.addEventListener('visibilitychange', () => { if (document.hidden) stop(); else start(); });
}

// Анимация появления блоков при скролле
function initRevealOnScroll() {
  if (window.innerWidth <= 1024) return;
  
  // Отключаем анимацию появления на страницах магазина
  if (document.body.classList.contains('oor-merch-page') || document.body.classList.contains('oor-product-page')) {
    return;
  }

  const selectors = [
    '.slider-section',
    '.oor-musical-association-left',
    '.oor-musical-association-right',
    '.oor-challenge-left',
    '.oor-challenge-right',
    '.oor-challenge-2-left',
    '.oor-challenge-2-good-works',
    '.oor-out-of-talk-text',
    '.oor-out-of-talk-media',
    '.oor-events-text',
    '.oor-events-media',
    '.oor-merch-text',
    '.oor-merch-images-grid',
    // Manifest page (hero excluded - верх страницы не анимируется)
    '.oor-manifest-section-inner',
    '.oor-manifest-ceo-content',
    // Studio page (hero excluded - верх страницы не анимируется)
    '.oor-studio-content',
    '.oor-studio-equipment-wrapper',
    '.oor-studio-services-grid',
    '.oor-studio-recording-grid',
    // Talk-show page (hero excluded - верх страницы не анимируется)
    '.oor-talk-show-episodes-grid',
    '.oor-talk-show-rules-content',
    // Events page (hero excluded - верх страницы не анимируется)
    '.oor-events-listing-grid'
  ];

  let nodes = selectors
    .flatMap(sel => Array.from(document.querySelectorAll(sel)))
    .filter(el => !el.classList.contains('no-reveal'));
  
  const isLargeScreen = window.innerWidth > 1920;
  if (isLargeScreen) {
    nodes = nodes.filter(el => !el.matches('.slider-section'));
    const sliders = document.querySelectorAll('.slider-section');
    sliders.forEach(slider => {
      slider.style.opacity = '1';
      slider.style.transform = 'translate3d(0, 0, 0)';
      slider.style.transition = 'none';
    });
  }

  if (nodes.length === 0) return;

  const merchItems = Array.from(document.querySelectorAll('.oor-merch-images-wrapper .oor-merch-image-item'));
  if (merchItems.length > 0) {
    nodes = nodes.filter(el => !el.matches('.oor-merch-images-grid'));
  }

  nodes.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translate3d(0, 120px, 0)';
    el.style.transformOrigin = '';
    el.style.transition = 'opacity 800ms ease, transform 1200ms cubic-bezier(0.22, 1, 0.36, 1)';
    el.style.willChange = 'opacity, transform';
  });

  const MERCH_STAGGER_MS = 140;
  merchItems.forEach((el, idx) => {
    el.style.opacity = '0';
    el.style.transform = 'translate3d(0, 120px, 0)';
    el.style.transition = `opacity 800ms ease ${idx * MERCH_STAGGER_MS}ms, transform 1200ms cubic-bezier(0.22, 1, 0.36, 1) ${idx * MERCH_STAGGER_MS}ms`;
    el.style.willChange = 'opacity, transform';
  });

  // Стагер для двухколоночных секций: левая раньше, правая чуть позже
  const staggerPairs = [
    ['.oor-musical-association-left', '.oor-musical-association-right'],
    ['.oor-challenge-left', '.oor-challenge-right'],
    ['.oor-challenge-2-left', '.oor-challenge-2-good-works'],
    ['.oor-events-text', '.oor-events-media'],
    // Manifest page (hero excluded)
    ['.oor-manifest-section-left', '.oor-manifest-section-content'],
    ['.oor-manifest-ceo-text', '.oor-manifest-ceo-image'],
    // Studio page
    ['.oor-studio-content-left', '.oor-studio-content-right'],
    ['.oor-studio-recording-image', '.oor-studio-recording-list'],
    // Talk-show page (hero excluded)
    ['.oor-talk-show-rules-left', '.oor-talk-show-rules-right']
  ];
  const STAGGER_DELAY_MS = 200;
  staggerPairs.forEach(([firstSel, secondSel]) => {
    const first = document.querySelector(firstSel);
    const second = document.querySelector(secondSel);
    if (first) first.style.transitionDelay = '0ms';
    if (second) second.style.transitionDelay = `${STAGGER_DELAY_MS}ms`;
  });

  // Флаг активации: запускаем анимации только после первого взаимодействия/скролла
  let activated = false;

  // Наблюдатель видимости
  const io = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (!activated) return; // игнорируем авто-триггер на загрузке
      if (entry.isIntersecting) {
        const el = entry.target;
        el.style.opacity = '1';
        el.style.transform = 'translate3d(0, 0, 0)';
        // После анимации очистим will-change, чтобы не держать слои постоянно
        setTimeout(() => { try { el.style.willChange = ''; } catch(_) {} }, 800);
        observer.unobserve(el);
      }
    });
  }, { root: null, rootMargin: '0px 0px -10% 0px', threshold: 0.1 });

  // Даем браузеру применить initial-стили и начинаем наблюдение (без немедленного старта)
  requestAnimationFrame(() => {
    // принудительная раскладка
    // eslint-disable-next-line @typescript-eslint/no-unused-expressions
    document.body && document.body.offsetHeight;

    setTimeout(() => {
      nodes.forEach(el => io.observe(el));
      merchItems.forEach(el => io.observe(el));
    }, 50);
  });

  // Первое взаимодействие активирует анимации
  const activate = () => {
    if (activated) return;
    activated = true;
    // После активации проверим уже видимые элементы
    const all = nodes.concat(merchItems);
    all.forEach(el => {
      const rect = el.getBoundingClientRect();
      const vh = window.innerHeight || document.documentElement.clientHeight;
      if (rect.top < vh * 0.9 && rect.bottom > 0) {
        el.style.opacity = '1';
        el.style.transform = 'translate3d(0, 0, 0)';
        setTimeout(() => { try { el.style.willChange = ''; } catch(_) {} }, 800);
        io.unobserve(el);
      }
    });
    window.removeEventListener('scroll', activate, { capture: false });
    window.removeEventListener('wheel', activate, { capture: false });
    window.removeEventListener('touchstart', activate, { capture: false });
  };
  window.addEventListener('scroll', activate, { passive: true });
  window.addEventListener('wheel', activate, { passive: true });
  window.addEventListener('touchstart', activate, { passive: true });

  // При ресайзе: если ушли в мобильный — снимаем эффекты
  const onResize = () => {
    if (window.innerWidth <= 1024) {
      nodes.forEach(el => {
        el.style.opacity = '';
        el.style.transform = '';
        el.style.transition = '';
        el.style.willChange = '';
        el.style.transitionDelay = '';
      });
    }
  };
  window.addEventListener('resize', onResize);
}

// Навигация - обработка удалена, теперь используется menu-sync.js
// function initNavigation() {
//   // Navigation logic moved to menu-sync.js
// }

// Динамическая дата
function initDynamicYear() {
  const yearElement = document.querySelector('.oor-hero-year');
  if (yearElement) {
    const currentYear = new Date().getFullYear();
    yearElement.textContent = `©${currentYear}`;
  }
}

// Глобальный флаг для отслеживания разрешений на автоплей
let videoAutoplayUnlocked = false;

/**
 * Разблокирует автовоспроизведение видео через user interaction
 * @function unlockVideoAutoplay
 * @returns {Promise<void>}
 */
function unlockVideoAutoplay() {
  if (videoAutoplayUnlocked) {
    console.log('[VideoAutoplay] Already unlocked');
    return Promise.resolve();
  }
  
  console.log('[VideoAutoplay] Attempting to unlock...');
  
  return new Promise((resolve) => {
    const videos = document.querySelectorAll('video');
    console.log(`[VideoAutoplay] Found ${videos.length} videos`);
    
    const playPromises = [];
    
    videos.forEach((video, index) => {
      // Пропускаем splash видео - оно будет запущено отдельно
      if (video.id === 'splash-video') {
        console.log(`[VideoAutoplay] Skipping splash video`);
        return;
      }
      
      console.log(`[VideoAutoplay] Unlocking video ${index + 1}/${videos.length}`);
      
      // Запускаем и сразу останавливаем видео для получения разрешений
      const playPromise = video.play()
        .then(() => {
          console.log(`[VideoAutoplay] Video ${index + 1} play successful`);
          // Для hero видео оставляем воспроизведение
          if (video.classList.contains('oor-hero-video')) {
            console.log(`[VideoAutoplay] Keeping hero video playing`);
            // Не останавливаем hero видео
          } else {
            video.pause();
            video.currentTime = 0;
          }
        })
        .catch(e => {
          console.warn(`[VideoAutoplay] Video ${index + 1} unlock failed:`, e);
        });
      
      playPromises.push(playPromise);
    });
    
    Promise.allSettled(playPromises).then(() => {
      videoAutoplayUnlocked = true;
      window.videoAutoplayUnlocked = true;
      console.log('[VideoAutoplay] All videos unlocked, autoplay enabled');
      resolve();
    });
  });
}

// Экспорт для использования в preloader.js
window.unlockVideoAutoplay = unlockVideoAutoplay;

/**
 * Проверяет режим энергосбережения
 * @function detectLowPowerMode
 * @returns {boolean}
 */
function detectLowPowerMode() {
  // Проверяем различные индикаторы режима энергосбережения
  if (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4) {
    return true;
  }
  
  if (navigator.deviceMemory && navigator.deviceMemory < 4) {
    return true;
  }
  
  // Проверяем производительность
  const start = performance.now();
  for (let i = 0; i < 1000000; i++) {
    Math.random();
  }
  const duration = performance.now() - start;
  
  return duration > 50; // Если вычисления занимают больше 50ms, возможно низкая производительность
}

// Hero Video оптимизация с улучшенным автоплеем
function initHeroVideo() {
  const video = document.querySelector('.oor-hero-video');
  if (!video) return;

  // Оптимизация загрузки
  video.addEventListener('loadstart', function() {
  });

  video.addEventListener('canplay', function() {
    // Убираем fallback изображение когда видео готово
    const fallback = video.querySelector('div');
    if (fallback) {
      fallback.style.display = 'none';
    }
  });

  video.addEventListener('error', function(e) {
    console.warn('Hero video: Ошибка загрузки, используем fallback');
    // Показываем fallback изображение при ошибке
    const fallback = video.querySelector('div');
    if (fallback) {
      fallback.style.display = 'block';
    }
  });

  // Улучшенная логика автовоспроизведения
  function attemptAutoplay() {
    console.log('[HeroVideo] Attempting autoplay, unlocked:', window.videoAutoplayUnlocked || videoAutoplayUnlocked);
    
    video.play().catch(e => {
      console.warn('[HeroVideo] Autoplay failed:', e);
      // Если автоплей не удался, ждем разблокировки
      if (!window.videoAutoplayUnlocked && !videoAutoplayUnlocked) {
        console.log('[HeroVideo] Waiting for user interaction to unlock');
      }
    });
  }

  // Оптимизация производительности
  video.addEventListener('loadeddata', function() {
    console.log('[HeroVideo] Video loaded, checking autoplay status');
    
    // Если уже разблокировано (через splash), запускаем сразу
    if (window.videoAutoplayUnlocked || videoAutoplayUnlocked) {
      console.log('[HeroVideo] Autoplay already unlocked, starting video');
      attemptAutoplay();
    } else {
      console.log('[HeroVideo] Autoplay not unlocked yet, will start after splash');
    }
  });

  // Используем Intersection Observer для запуска видео когда оно в viewport
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && video.paused) {
        console.log('[HeroVideo] Video in viewport, attempting autoplay');
        if (window.videoAutoplayUnlocked || videoAutoplayUnlocked) {
          attemptAutoplay();
        }
      }
    });
  }, { threshold: 0.5 });

  observer.observe(video);

  // Обработка видимости страницы для экономии ресурсов
  document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
      video.pause();
    } else {
      video.play().catch(e => {
        console.warn('Hero video: Не удалось возобновить воспроизведение', e);
      });
    }
  });

  // Обработка изменения размера окна
  window.addEventListener('resize', function() {
    // Пересчитываем размеры видео при изменении размера окна
    video.style.width = '100%';
    video.style.height = '100%';
  });
}

// Экспортируем в window для совместимости и независимости от порядка загрузки
window.initHeroVideo = initHeroVideo;

// Полноэкранное видео
function initFullscreenVideo() {
  const heroVideoOverlay = document.getElementById('hero-video-overlay');
  const fullscreenVideo = document.getElementById('fullscreen-video');
  const fullscreenVideoElement = document.querySelector('.oor-fullscreen-video-element');
  const fullscreenClose = document.getElementById('fullscreen-close');
  const plusTopRight = document.querySelector('.oor-hero-plus-top-right');
  
  if (!heroVideoOverlay || !fullscreenVideo || !fullscreenVideoElement || !fullscreenClose || !plusTopRight) {
    // Элементы не найдены (нормально для страниц без fullscreen video)
    return;
  }
  
  const plusIcon = plusTopRight.querySelector('img');

  // Флаг для блокировки кликов сразу после скрытия прелоадера
  let overlayClickBlocked = false;
  
  // Разблокируем клики через небольшую задержку после скрытия прелоадера
  function unblockOverlayClicks() {
    setTimeout(() => {
      overlayClickBlocked = false;
    }, 500); // 500ms задержка после скрытия прелоадера
  }
  
  // Проверяем, не активен ли прелоадер
  function isPreloaderActive() {
    return document.getElementById('preloader') || 
           document.getElementById('splash-screen') ||
           document.body.classList.contains('preloader-active') ||
           document.documentElement.classList.contains('preloader-active');
  }
  
  // Слушаем событие скрытия прелоадера
  const observer = new MutationObserver(() => {
    if (!isPreloaderActive() && overlayClickBlocked) {
      unblockOverlayClicks();
    }
  });
  
  observer.observe(document.body, {
    attributes: true,
    attributeFilter: ['class'],
    childList: true,
    subtree: true
  });
  
  // Блокируем клики на overlay пока прелоадер активен
  if (isPreloaderActive()) {
    overlayClickBlocked = true;
  }

  // Клик по оверлею фонового видео открывает полноэкранное
  heroVideoOverlay.addEventListener('click', function(e) {
    // Блокируем клики если прелоадер активен, недавно был скрыт, или глобальный флаг установлен
    if (overlayClickBlocked || isPreloaderActive() || (window.overlayClickBlocked === true)) {
      e.preventDefault();
      e.stopPropagation();
      return;
    }
    openFullscreenVideo();
  });

  // Клик по plus иконке справа вверху открывает полноэкранное
  plusTopRight.addEventListener('click', function(e) {
    e.stopPropagation();
    openFullscreenVideo();
  });

  // Клик по кнопке закрытия закрывает полноэкранное
  fullscreenClose.addEventListener('click', function() {
    closeFullscreenVideo();
  });

  // Клик по фону закрывает полноэкранное
  fullscreenVideo.addEventListener('click', function(e) {
    if (e.target === fullscreenVideo) {
      closeFullscreenVideo();
    }
  });

  // ESC закрывает полноэкранное
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && fullscreenVideo.classList.contains('active')) {
      closeFullscreenVideo();
    }
  });

  function openFullscreenVideo() {
    // Показываем полноэкранное видео
    fullscreenVideo.classList.add('active');
    
    // Блокируем скролл страницы через класс (работает с CSS !important)
    document.body.classList.add('scroll-locked');
    document.documentElement.classList.add('scroll-locked');
    
    // Запускаем видео
    fullscreenVideoElement.play();
    
    // Анимируем иконку закрытия через CSS класс
    fullscreenClose.classList.add('active');
    
  }

  function closeFullscreenVideo() {
    // Останавливаем видео
    fullscreenVideoElement.pause();
    fullscreenVideoElement.currentTime = 0;
    
    // Скрываем полноэкранное видео
    fullscreenVideo.classList.remove('active');
    
    // Разблокируем скролл страницы
    document.body.classList.remove('scroll-locked');
    document.documentElement.classList.remove('scroll-locked');
    
    // Возвращаем иконку в исходное состояние через CSS класс
    fullscreenClose.classList.remove('active');
    
  }
}

// Экспортируем в window для совместимости и независимости от порядка загрузки
window.initFullscreenVideo = initFullscreenVideo;

// Глобальная страховка от зависшей блокировки скролла (Netlify кейс)
function installScrollUnlockWatchdog() {
  let unlockedOnce = false;

  function unlockScroll(reason) {
    if (unlockedOnce) return;
    unlockedOnce = true;
    try {
      document.documentElement.classList.remove('preloader-active');
      document.body.classList.remove('preloader-active');
      document.documentElement.style.overflow = '';
      document.body.style.overflow = '';
    } catch(_) {}
    // Удаляем слушатели, чтобы не держать ссылки
    window.removeEventListener('load', onLoadUnlock);
    document.removeEventListener('visibilitychange', onVisibility);
    document.removeEventListener('pointerdown', onFirstInteract, { capture: true });
    document.removeEventListener('wheel', onFirstInteract, { capture: true });
    document.removeEventListener('keydown', onFirstInteract, { capture: true });
  }

  function onLoadUnlock(){
    // Даем время Netlify подгрузить ассеты, затем снимаем блокировки
    setTimeout(() => unlockScroll('load-timeout'), 1200);
  }

  function onVisibility(){
    if (!document.hidden) setTimeout(() => unlockScroll('visibility'), 600);
  }

  function onFirstInteract(){
    unlockScroll('first-interaction');
  }

  // Подстраховываемся по нескольким событиям
  window.addEventListener('load', onLoadUnlock, { once: true });
  document.addEventListener('visibilitychange', onVisibility);
  document.addEventListener('pointerdown', onFirstInteract, { capture: true, once: true });
  document.addEventListener('wheel', onFirstInteract, { capture: true, once: true });
  document.addEventListener('keydown', onFirstInteract, { capture: true, once: true });
}

// Магнетизм для кликабельных элементов
function initMagneticElements() {
  // Отключаем магнетизм на мобильных устройствах (≤1024px с запасом)
  if (window.innerWidth <= 1024) {
    return;
  }
  
  const magneticElements = document.querySelectorAll(`
    .oor-fullscreen-close,
    .oor-btn-small,
    .oor-social-icon,
    .oor-manifesto-button,
    .oor-become-artist-button,
    .oor-challenge-2-music-icon,
    .oor-challenge-2-good-works-icon,
    .oor-events-sold-out,
    .oor-events-buy-ticket,
    .oor-merch-button
  `);

  magneticElements.forEach(element => {
    element.addEventListener('mouseenter', function() {
      this.addEventListener('mousemove', handleMouseMove);
    });

    element.addEventListener('mouseleave', function() {
      this.removeEventListener('mousemove', handleMouseMove);
      // Возвращаем элемент в исходное положение
      this.style.setProperty('--mouse-x', '0px');
      this.style.setProperty('--mouse-y', '0px');
    });
  });

  function handleMouseMove(e) {
    const element = e.currentTarget;
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    
    // Рассчитываем смещение относительно центра элемента
    const deltaX = e.clientX - centerX;
    const deltaY = e.clientY - centerY;
    
    // Ограничиваем смещение 16 пикселями
    const maxDistance = 16;
    const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
    
    let offsetX = deltaX;
    let offsetY = deltaY;
    
    if (distance > maxDistance) {
      // Если расстояние больше максимума, нормализуем вектор
      offsetX = (deltaX / distance) * maxDistance;
      offsetY = (deltaY / distance) * maxDistance;
    }
    
    // Применяем смещение через CSS переменные
    element.style.setProperty('--mouse-x', `${offsetX}px`);
    element.style.setProperty('--mouse-y', `${offsetY}px`);
  }
}

// Контроль висячих строк (orphans)
function initOrphanControl() {
  // Селекторы для текстовых элементов, где нужно контролировать висячие строки
  const textSelectors = [
    'p',
    '.oor-musical-association-text',
    '.oor-challenge-title',
    '.oor-without-fear-text',
    '.oor-quality-title',
    '.oor-out-of-talk-description',
    '.oor-events-description',
    '.oor-merch-description'
  ];
  
  textSelectors.forEach(selector => {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
      preventOrphans(element);
    });
  });
}

// === Диагностика блокировок скролла (активна только с ?debug) ===
function installScrollDiagnostics() {
  try {
    const DEBUG = (typeof window !== 'undefined') && window.location && window.location.search.includes('debug');
    if (!DEBUG) return;

    function logStyles(tag) {
      const htmlCS = getComputedStyle(document.documentElement);
      const bodyCS = getComputedStyle(document.body);
      const info = {
        tag,
        html: {
          overflow: htmlCS.overflow,
          overflowY: htmlCS.overflowY,
          position: htmlCS.position,
          height: htmlCS.height
        },
        body: {
          overflow: bodyCS.overflow,
          overflowY: bodyCS.overflowY,
          position: bodyCS.position,
          height: bodyCS.height
        },
        classes: {
          html: Array.from(document.documentElement.classList),
          body: Array.from(document.body.classList)
        },
        sizes: {
          innerHeight: window.innerHeight,
          scrollHeight: document.documentElement.scrollHeight,
          clientHeight: document.documentElement.clientHeight,
          pageYOffset: window.pageYOffset
        }
      };
    }

    function canScrollCheck(tag) {
      const before = window.pageYOffset;
      window.scrollTo(0, before + 2);
      setTimeout(() => {
        const after = window.pageYOffset;
      }, 50);
    }

    // Логи на ключевых этапах
    logStyles('DOMContentLoaded');
    setTimeout(() => logStyles('t+300ms'), 300);
    setTimeout(() => logStyles('t+1200ms'), 1200);
    setTimeout(() => logStyles('t+3000ms'), 3000);
    setTimeout(() => canScrollCheck('t+1200ms'), 1200);

    // Глобальные ловушки событий для диагностики
    window.addEventListener('load', () => logStyles('load'));
    document.addEventListener('wheel', () => logStyles('wheel'), { passive: true });
    window.addEventListener('resize', () => logStyles('resize'));

    // Экстренная разблокировка из консоли
    window.forceUnlockScroll = () => {
      console.warn('[ScrollDiag] forceUnlockScroll invoked');
      try {
        document.documentElement.classList.remove('preloader-active');
        document.body.classList.remove('preloader-active');
        document.documentElement.style.overflow = 'auto';
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflowY = 'auto';
        document.body.style.overflowY = 'auto';
      } catch (e) { console.warn(e); }
      logStyles('forceUnlockScroll');
    };

  } catch (e) {
    console.warn('installScrollDiagnostics error', e);
  }
}

// Функция для предотвращения висячих строк
function preventOrphans(element) {
  if (!element || !element.textContent) return;
  
  const text = element.textContent.trim();
  const words = text.split(/\s+/);
  
  // Если слов меньше 2, не обрабатываем
  if (words.length < 2) return;
  
  // Если последнее слово короткое (до 4 символов), связываем с предыдущим
  const lastWord = words[words.length - 1];
  if (lastWord.length <= 4) {
    // Заменяем последний пробел на неразрывный пробел
    const newText = text.replace(/\s+$/, '\u00A0');
    element.textContent = newText;
  }
  
  // Дополнительная проверка: если последние два слова короткие, связываем их
  if (words.length >= 2) {
    const lastTwoWords = words.slice(-2);
    if (lastTwoWords.every(word => word.length <= 4)) {
      const textWithoutLastTwo = words.slice(0, -2).join(' ');
      const lastTwoJoined = lastTwoWords.join('\u00A0');
      element.textContent = textWithoutLastTwo + ' ' + lastTwoJoined;
    }
  }
}

// Страница товара: если цена выведена без <del>/<ins>, оборачиваем две цены вручную
// Поддерживает "20,00&nbsp;₽ 18,00&nbsp;₽" и аналогичные форматы
function initProductPriceStrikethrough() {
  const priceEls = document.querySelectorAll('.summary.entry-summary .price, .oor-product-section .price, .price, p.price');
  priceEls.forEach(el => {
    if (el.querySelector('del')) return; // уже есть разметка
    const raw = (el.textContent || '').trim();
    const text = raw.replace(/\s+/g, ' '); // в т.ч. &nbsp; → обычный пробел
    // Вариант 1: разделитель " ₽ " (U+20BD) → ["20,00", "18,00"]
    let parts = text.split(/\s+\u20BD\s+/);
    let first = '';
    let second = '';
    if (parts.length === 2 && /^[\d\s.,]+$/.test(parts[0].trim()) && /^[\d\s.,]+$/.test(parts[1].trim())) {
      first = (parts[0].trim() + ' \u20BD').trim();
      second = (parts[1].trim() + ' \u20BD').trim();
    } else {
      // Вариант 2: режем по " пробел+цифра" → ["20,00 ₽", "18,00 ₽"]
      parts = text.split(/\s+(?=\d)/);
      if (parts.length === 2 && parts[0].trim() && parts[1].trim()) {
        first = parts[0].trim();
        second = parts[1].trim();
      }
    }
    if (first && second) {
      el.innerHTML = '<del aria-hidden="true" style="text-decoration: line-through !important">' + escapeHtml(first) + '</del> <ins style="text-decoration: none !important">' + escapeHtml(second) + '</ins>';
      el.classList.add('oor-price-fixed');
      return;
    }
    // Запас: два фрагмента "число + валюта" (€ и др.)
    const twoPrices = text.match(/^([\d\s.,]+)\s*([^\d\s]+)\s+([\d\s.,]+)\s*\2\s*$/);
    if (twoPrices) {
      const [, amount1, currency, amount2] = twoPrices;
      el.innerHTML = '<del aria-hidden="true" style="text-decoration: line-through !important">' + escapeHtml(amount1) + ' ' + escapeHtml(currency) + '</del> <ins style="text-decoration: none !important">' + escapeHtml(amount2) + ' ' + escapeHtml(currency) + '</ins>';
      el.classList.add('oor-price-fixed');
    }
  });
}
function escapeHtml(s) {
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

// Страница товара: баблы вариаций (размер и т.д.) — синхрон с скрытым select для формы WooCommerce
function initVariationBubbles() {
  const form = document.querySelector('form.variations_form.cart');
  if (!form) return;
  const wraps = form.querySelectorAll('.oor-variation-attribute-wrap');
  wraps.forEach(function (wrap) {
    const select = wrap.querySelector('.oor-variation-select-wrapper select');
    const buttons = wrap.querySelectorAll('.oor-product-size-btn');
    if (!select || !buttons.length) return;
    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const value = btn.getAttribute('data-value');
        if (value == null) return;
        select.value = value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        buttons.forEach(function (b) {
          b.classList.remove('oor-product-size-btn--active');
        });
        btn.classList.add('oor-product-size-btn--active');
      });
    });
  });
  // При клике «Очистить» снимаем активное состояние со всех баблов
  const resetLink = form.querySelector('.reset_variations');
  if (resetLink) {
    resetLink.addEventListener('click', function () {
      form.querySelectorAll('.oor-product-size-btn').forEach(function (b) {
        b.classList.remove('oor-product-size-btn--active');
      });
    });
  }
}

// Навигация для страниц магазина
function initMerchNavigation() {
  // merch.html -> product.html: клик на товар (только для статичных HTML-страниц)
  // На WordPress/WooCommerce не перехватываем — ссылка ведёт на страницу товара
  const productLinks = document.querySelectorAll('.oor-merch-product-link');
  productLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = (link.getAttribute('href') || '').trim();
      const isStaticProductLink = !href || href === '#' || href.endsWith('product.html') || (href.startsWith('/') && href.endsWith('.html'));
      if (isStaticProductLink) {
        e.preventDefault();
        window.location.href = '/product.html';
      }
      // иначе — обычная навигация по ссылке (WordPress permalink)
    });
  });

  // product.html -> cart.html: кнопка "В корзину"
  const addToCartBtn = document.querySelector('.oor-product-add-to-cart[data-action="add-to-cart"]');
  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function(e) {
      e.preventDefault();
      window.location.href = '/cart.html';
    });
  }

  // cart.html -> checkout.html: кнопка "ПЕРЕЙТИ К ОПЛАТЕ"
  const checkoutBtn = document.querySelector('.oor-cart-checkout-btn[data-action="checkout"]');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      window.location.href = '/checkout.html';
    });
  }

  // checkout.html -> index.html: кнопка "КУПИТЬ"
  const buyBtn = document.querySelector('.oor-checkout-buy-btn[data-action="place-order"]');
  if (buyBtn) {
    buyBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      // Предотвращаем отправку формы
      const form = document.getElementById('checkout-form');
      if (form) {
        form.addEventListener('submit', function(ev) {
          ev.preventDefault();
        }, { once: true });
      }
      window.location.href = '/index.html';
    });
  }
}