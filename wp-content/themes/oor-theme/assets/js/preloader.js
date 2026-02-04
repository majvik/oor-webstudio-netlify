// Модуль прелоадера с splash screen
function initPreloader() {
  const preloader = document.getElementById('preloader');
  const progressBar = document.getElementById('preloader-progress-bar');
  const enterButton = document.getElementById('enter-button-splash') || document.getElementById('enter-button');
  const splashScreen = document.getElementById('splash-screen');
  const splashGif = document.getElementById('splash-gif');
  
  if (!preloader || !progressBar) {
    try {
      document.documentElement.classList.remove('preloader-active');
      document.body.classList.remove('preloader-active');
      document.documentElement.style.overflow = '';
      document.body.style.overflow = '';
    } catch(_) {}
    return;
  }

  const isMainPage = window.location.pathname === '/' || 
                    window.location.pathname === '/index.html' || 
                    window.location.pathname === '';
  
  const scrollY = window.scrollY;
  
  document.documentElement.classList.add('preloader-active');
  document.body.classList.add('preloader-active');
  document.body.style.position = 'fixed';
  document.body.style.top = `-${scrollY}px`;
  document.body.style.width = '100%';

  let progress = 0;
  let loadedResources = 0;
  let totalResources = 0;
  let gifCompletionStarted = false;
  let gifStopped = false;
  
  // Используем OOR_PATHS если доступен, иначе fallback на абсолютные пути
  // Используем OOR_PATHS если доступен, иначе fallback на абсолютные пути (для статической версии)
  const paths = (typeof window !== 'undefined' && window.OOR_PATHS) 
    ? window.OOR_PATHS 
    : { 
        assets: (typeof window !== 'undefined' && window.oorPaths) ? window.oorPaths.assets : '/public/assets', 
        fonts: (typeof window !== 'undefined' && window.oorPaths) ? window.oorPaths.fonts : '/public/fonts' 
      };
  
  const resourcesToLoad = [
    paths.assets + '/plus-large.svg',
    paths.assets + '/plus-small.svg',
    paths.assets + '/line-small.svg',
    paths.assets + '/hero-bg.png',
    paths.assets + '/OUTOFREC_reel_v4_nologo.mp4',
    paths.assets + '/splash-last-frame.png',
    // Загружаем WOFF2 шрифты (приоритет) с fallback на TTF
    paths.fonts + '/pragmatica-book.woff2',
    paths.fonts + '/pragmatica-book-oblique.woff2',
    paths.fonts + '/pragmatica-extended-book.woff2',
    paths.fonts + '/pragmatica-extended-book-oblique.woff2',
    paths.fonts + '/pragmatica-extended-light.woff2',
    paths.fonts + '/pragmatica-extended-light-oblique.woff2',
    paths.fonts + '/pragmatica-extended-medium.woff2',
    paths.fonts + '/pragmatica-extended-medium-oblique.woff2',
    paths.fonts + '/pragmatica-extended-bold.woff2',
    paths.fonts + '/pragmatica-extended-bold-oblique.woff2',
    paths.fonts + '/pragmatica-extended-extralight.woff2',
    paths.fonts + '/pragmatica-extended-extralight-oblique.woff2'
  ];

  // Используем динамические пути для CSS/JS файлов
  const cssJsBase = (typeof window !== 'undefined' && window.OOR_PATHS) 
    ? (window.OOR_PATHS.css || window.OOR_PATHS.js || '')
    : (typeof window !== 'undefined' && window.oorPaths)
      ? (window.oorPaths.css || window.oorPaths.js || '')
      : '/src';
  
  const expectedCssJsFiles = [
    cssJsBase + '/css/reset.css',
    cssJsBase + '/css/fonts.css',
    cssJsBase + '/css/tokens.css',
    cssJsBase + '/css/base.css',
    cssJsBase + '/css/grid.css',
    cssJsBase + '/css/layout.css',
    cssJsBase + '/css/components.css',
    cssJsBase + '/js/main.js'
  ];

  totalResources = resourcesToLoad.length + expectedCssJsFiles.length + 1;

  function updateProgress() {
    const actualProgress = Math.min(loadedResources, totalResources);
    progress = (actualProgress / totalResources) * 100;
    progressBar.style.width = progress + '%';

    if (loadedResources >= totalResources) {
      handleProgressComplete();
    }
  }

  function handleProgressComplete() {
    progressBar.classList.add('hidden');
    
    setTimeout(() => {
      if (isMainPage && splashScreen && splashGif) {
        showSplashGif();
      } else {
        // For non-main pages, hide preloader immediately without splash
        hidePreloaderQuick();
      }
    }, 300);
  }

  function loadResource(url) {
    return new Promise((resolve) => {
      const isImage = /\.(jpg|jpeg|png|gif|svg|webp)$/i.test(url);
      const isVideo = /\.(mp4|webm|ogg)$/i.test(url);
      const isFont = /\.(ttf|otf|woff|woff2)$/i.test(url);
      
      if (isImage) {
        const resource = new Image();
        resource.onload = () => {
          loadedResources++;
          updateProgress();
          resolve();
        };
        resource.onerror = () => {
          console.warn(`Failed to load image: ${url}`);
          loadedResources++;
          updateProgress();
          resolve();
        };
        resource.src = url;
      } else if (isVideo) {
        const resource = document.createElement('video');
        resource.oncanplaythrough = () => {
          loadedResources++;
          updateProgress();
          resolve();
        };
        resource.onerror = () => {
          console.warn(`Failed to load video: ${url}`);
          loadedResources++;
          updateProgress();
          resolve();
        };
        resource.src = url;
        resource.load();
      } else if (isFont) {
        const fontName = url.split('/').pop().split('.')[0];
        const font = new FontFace(fontName, `url(${url})`);
        
        font.load().then(() => {
          loadedResources++;
          updateProgress();
          resolve();
        }).catch(() => {
          console.warn(`Failed to load font: ${url}`);
          loadedResources++;
          updateProgress();
          resolve();
        });
      } else {
        fetch(url)
          .then(response => {
            if (response.ok) {
              loadedResources++;
              updateProgress();
              resolve();
            } else {
              throw new Error(`HTTP ${response.status}`);
            }
          })
          .catch(error => {
            console.warn(`Failed to load resource: ${url}`, error);
            // Все равно считаем как загруженное, чтобы не блокировать прелоадер
            loadedResources++;
            updateProgress();
            resolve();
          });
      }
    });
  }

  resourcesToLoad.forEach(url => loadResource(url));
  
  let checkedResources = new Set();
  let missingResources = [];
  
  function checkLoadedResources() {
    const performanceEntries = performance.getEntriesByType('resource');
    
    expectedCssJsFiles.forEach(expectedFile => {
      if (checkedResources.has(expectedFile)) return;
      
      // Извлекаем имя файла для более гибкого поиска
      const fileName = expectedFile.split('/').pop();
      const fileBaseName = fileName.split('.')[0]; // Без расширения
      
      // Проверяем разные варианты путей (с доменом, без, с версией и т.д.)
      const foundEntry = performanceEntries.find(entry => {
        const entryName = entry.name.toLowerCase();
        const entryFileName = entryName.split('/').pop().split('?')[0]; // Убираем query параметры
        
        // Проверяем по полному имени файла или по базовому имени
        return entryFileName === fileName.toLowerCase() || 
               entryName.includes(fileName.toLowerCase()) ||
               entryName.includes(fileBaseName.toLowerCase());
      });
      
      if (foundEntry) {
        checkedResources.add(expectedFile);
        loadedResources++;
      } else {
        // Сохраняем информацию о не найденном ресурсе
        if (!missingResources.includes(expectedFile)) {
          missingResources.push(expectedFile);
        }
      }
    });
    
    updateProgress();
  }
  
  const checkInterval = setInterval(() => {
    checkLoadedResources();
    if (loadedResources >= totalResources) {
      clearInterval(checkInterval);
    }
  }, 100);
  
  let domLoaded = false;
  
  function checkDomLoad() {
    if (!domLoaded && document.readyState === 'complete') {
      domLoaded = true;
      loadedResources++;
      updateProgress();
    }
  }
  
  if (document.readyState === 'complete') {
    checkDomLoad();
  } else {
    window.addEventListener('load', checkDomLoad);
  }

  setTimeout(() => {
    if (loadedResources < totalResources) {
      // Проверяем еще раз перед таймаутом
      checkLoadedResources();
      
      if (loadedResources < totalResources) {
        console.warn('Preloader timeout, forcing completion', {
          loaded: loadedResources,
          total: totalResources,
          remaining: totalResources - loadedResources,
          missingResources: missingResources,
          allResources: {
            static: resourcesToLoad,
            cssJs: expectedCssJsFiles,
            domLoad: 'document.readyState'
          }
        });
        loadedResources = totalResources;
        updateProgress();
      }
    }
  }, 10000); // Увеличено с 5 до 10 секунд
  
  // Failsafe: unlock scroll after maximum timeout
  setTimeout(() => {
    if (document.documentElement.classList.contains('preloader-active') || 
        document.body.classList.contains('preloader-active')) {
      console.warn('Preloader failsafe triggered - forcing scroll unlock');
      unlockScroll();
      if (preloader) preloader.remove();
      if (splashScreen) splashScreen.remove();
    }
  }, 8000);
  
  const progressCheckInterval = setInterval(() => {
    if (loadedResources >= totalResources) {
      clearInterval(progressCheckInterval);
    }
  }, 500);

  function showSplashGif() {
    if (!splashScreen || !splashGif) {
      console.warn('[Preloader] splashScreen or splashGif not found, hiding preloader');
      hidePreloader();
      return;
    }
    
    const parent = splashGif.parentNode || splashScreen;
    
    const lastFrameImg = document.createElement('img');
    lastFrameImg.id = 'splash-gif-frozen';
    const paths = (typeof window !== 'undefined' && window.OOR_PATHS) 
      ? window.OOR_PATHS 
      : { 
          assets: (typeof window !== 'undefined' && window.oorPaths) ? window.oorPaths.assets : '/public/assets' 
        };
    lastFrameImg.src = paths.assets + '/splash-last-frame.png';
    lastFrameImg.alt = 'Splash';
    lastFrameImg.width = 400;
    lastFrameImg.height = 400;
    lastFrameImg.style.zIndex = '1';
    lastFrameImg.style.cursor = 'pointer';
    lastFrameImg.addEventListener('click', handleEnterClick, { once: true });
    
    splashGif.style.zIndex = '2';
    
    parent.insertBefore(lastFrameImg, splashGif);
    
    splashScreen.classList.add('visible');
    gifCompletionStarted = false;
    
    splashGif.onload = () => {
      if (!gifCompletionStarted) {
        gifCompletionStarted = true;
        waitForGifCompletion();
      }
    };
    
    if (splashGif.complete && splashGif.naturalHeight !== 0) {
      if (!gifCompletionStarted) {
        gifCompletionStarted = true;
        waitForGifCompletion();
      }
    }
  }

  function waitForGifCompletion() {
    if (!splashGif) return;
    
    const estimatedDuration = 3800;
    
    setTimeout(() => {
      stopGifLoop();
    }, estimatedDuration);
  }

  function stopGifLoop() {
    if (gifStopped) return;
    gifStopped = true;
    
    const currentGif = document.getElementById('splash-gif');
    if (!currentGif) {
      setTimeout(() => {
        showEnterButton();
      }, 300);
      return;
    }
    
    try {
      if (currentGif && currentGif.parentNode) {
        currentGif.remove();
      }
      
      setTimeout(() => {
        showEnterButton();
      }, 100);
    } catch (error) {
      console.error('[Preloader] Error removing GIF:', error);
      setTimeout(() => {
        showEnterButton();
      }, 300);
    }
  }

  function showEnterButton() {
    if (!enterButton) return;
    
    if (enterButton.dataset.initialized === 'true') {
      return;
    }
    
    enterButton.dataset.initialized = 'true';
    
    enterButton.addEventListener('click', handleEnterClick, { once: true });
    
    const isMobile = window.innerWidth <= 768;
    if (!isMobile) {
      enterButton.style.transform = 'translateX(-50%)';
    }
    
    requestAnimationFrame(() => {
      enterButton.classList.add('visible');
    });
  }

  function handleEnterClick(e) {
    if (!enterButton) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    if (typeof window.unlockVideoAutoplay === 'function') {
      window.unlockVideoAutoplay().then(() => {
        console.log('[Preloader] Video autoplay unlocked successfully');
        window.videoAutoplayUnlocked = true;
      }).catch(e => {
        console.warn('[Preloader] Failed to unlock video autoplay:', e);
      });
    }
    
    hidePreloader();
  }

  function unlockScroll() {
    const scrollY = document.body.style.top;
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    
    window.scrollTo(0, parseInt(scrollY || '0') * -1);
    
    document.documentElement.classList.remove('preloader-active');
    document.body.classList.remove('preloader-active');
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
  }

  function hidePreloaderQuick() {
    try {
      if (preloader) {
        preloader.classList.add('hidden');
      }
      if (splashScreen) {
        splashScreen.classList.add('hidden');
      }
      
      setTimeout(() => {
        if (preloader) {
          preloader.remove();
        }
        if (splashScreen) {
          splashScreen.remove();
        }
        
        unlockScroll();
        
        setTimeout(() => {
          try {
            const DISABLE_LENIS = (typeof window !== 'undefined') && window.location && 
                                 (window.location.search.includes('nolenis') || window.location.search.includes('disablelenis'));
            const IS_ARTIST_PAGE = (typeof window !== 'undefined') && window.location && 
                                 (window.location.pathname.includes('artist.html') || document.body.classList.contains('oor-artist-page'));
            const IS_PRODUCT_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-product-page');
            const IS_MERCH_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-merch-page');
            const IS_CART_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-cart-page');
            const IS_CHECKOUT_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-checkout-page');
            if (DISABLE_LENIS || IS_ARTIST_PAGE || IS_PRODUCT_PAGE || IS_MERCH_PAGE || IS_CART_PAGE || IS_CHECKOUT_PAGE) {
              return;
            }

            function initLenis() {
              try {
                if (window.Lenis && !window.lenis) {
                  window.lenis = new window.Lenis({
                    smoothWheel: true,
                    smoothTouch: false,
                    normalizeWheel: true,
                    lerp: 0.09,
                    wheelMultiplier: 1.0,
                    duration: 1.0,
                    easing: (t) => 1 - Math.pow(1 - t, 3),
                    orientation: 'vertical',
                    gestureOrientation: 'vertical',
                    touchMultiplier: 2,
                    infinite: false
                  });
                  
                  function raf(time) {
                    if (window.lenis) {
                      window.lenis.raf(time);
                      requestAnimationFrame(raf);
                    }
                  }
                  if (window.lenis) {
                    requestAnimationFrame(raf);
                  }
                }
              } catch(e) { console.warn('Lenis init error', e); }
            }
            
            const s = document.createElement('script');
            // Пробуем загрузить с основного CDN
            s.src = 'https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1/bundled/lenis.min.js';
            s.async = true;
            s.onerror = () => {
              // Если основной CDN не работает, пробуем альтернативный
              const s2 = document.createElement('script');
              s2.src = 'https://unpkg.com/@studio-freight/lenis@1/bundled/lenis.min.js';
              s2.async = true;
              s2.onload = initLenis;
              s2.onerror = () => {
                console.warn('[OOR] Lenis library could not be loaded from any CDN - smooth scrolling disabled');
              };
              document.head.appendChild(s2);
            };
            s.onload = initLenis;
            document.head.appendChild(s);
          } catch(e) { console.warn('Lenis load error', e); }
        }, 100);
        
      }, 300);
      
    } catch(_) {
      unlockScroll();
    }
  }

  function hidePreloader() {
    try {
      if (preloader) {
        preloader.classList.add('hidden');
      }
      if (splashScreen) {
        splashScreen.classList.add('hidden');
      }
      
      setTimeout(() => {
        if (preloader) {
          preloader.remove();
        }
        if (splashScreen) {
          splashScreen.remove();
        }
        
        unlockScroll();
        
        setTimeout(() => {
          try {
            const DISABLE_LENIS = (typeof window !== 'undefined') && window.location && 
                                 (window.location.search.includes('nolenis') || window.location.search.includes('disablelenis'));
            const IS_ARTIST_PAGE = (typeof window !== 'undefined') && window.location && 
                                 (window.location.pathname.includes('artist.html') || document.body.classList.contains('oor-artist-page'));
            const IS_PRODUCT_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-product-page');
            const IS_MERCH_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-merch-page');
            const IS_CART_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-cart-page');
            const IS_CHECKOUT_PAGE = (typeof window !== 'undefined') && document.body.classList.contains('oor-checkout-page');
            if (DISABLE_LENIS || IS_ARTIST_PAGE || IS_PRODUCT_PAGE || IS_MERCH_PAGE || IS_CART_PAGE || IS_CHECKOUT_PAGE) {
              return;
            }

            function initLenis() {
              try {
                if (window.Lenis && !window.lenis) {
                  window.lenis = new window.Lenis({
                    smoothWheel: true,
                    smoothTouch: false,
                    normalizeWheel: true,
                    lerp: 0.09,
                    wheelMultiplier: 1.0,
                    duration: 1.0,
                    easing: (t) => 1 - Math.pow(1 - t, 3),
                    orientation: 'vertical',
                    gestureOrientation: 'vertical',
                    touchMultiplier: 2,
                    infinite: false
                  });
                  
                  function raf(time) {
                    if (window.lenis) {
                      window.lenis.raf(time);
                      requestAnimationFrame(raf);
                    }
                  }
                  if (window.lenis) {
                    requestAnimationFrame(raf);
                  }
                }
              } catch(e) { console.warn('Lenis init error', e); }
            }
            
            const s = document.createElement('script');
            // Пробуем загрузить с основного CDN
            s.src = 'https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1/bundled/lenis.min.js';
            s.async = true;
            s.onerror = () => {
              // Если основной CDN не работает, пробуем альтернативный
              const s2 = document.createElement('script');
              s2.src = 'https://unpkg.com/@studio-freight/lenis@1/bundled/lenis.min.js';
              s2.async = true;
              s2.onload = initLenis;
              s2.onerror = () => {
                console.warn('[OOR] Lenis library could not be loaded from any CDN - smooth scrolling disabled');
              };
              document.head.appendChild(s2);
            };
            s.onload = initLenis;
            document.head.appendChild(s);
          } catch(e) { console.warn('Lenis load error', e); }
        }, 100);
        
      }, 800);
      
    } catch(_) {
      unlockScroll();
    }
  }
}
window.initPreloader = initPreloader;
