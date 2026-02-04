// UI Scaling для больших мониторов

(function() {
  'use strict';
  
  const BASE_WIDTH = 1920;
  const MAX_ZOOM = null;
  const DEBUG = false;
  
  const COMPENSATE_SELECTORS = [
    '#preloader',
    '.custom-scrollbar'
  ];
  
  const COMPENSATE_CONTENT_SELECTORS = [
    '.oor-preloader-content'
  ];
  
  function compensateFixedElements(zoom) {
    COMPENSATE_SELECTORS.forEach(selector => {
      const elements = document.querySelectorAll(selector);
      elements.forEach(el => {
        el.style.zoom = 1 / zoom;
      });
    });
    
    COMPENSATE_CONTENT_SELECTORS.forEach(selector => {
      const elements = document.querySelectorAll(selector);
      elements.forEach(el => {
        el.style.zoom = 1 / zoom;
      });
    });
    
    if (DEBUG && zoom !== 1) {
      console.log('[Scale] Compensated fixed elements with zoom:', (1 / zoom).toFixed(3));
    }
  }
  
  function updateZoom() {
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    let zoom = 1;
    
    if (vw > BASE_WIDTH) {
      zoom = vw / BASE_WIDTH;
      
      if (MAX_ZOOM && zoom > MAX_ZOOM) {
        zoom = MAX_ZOOM;
      }
    }
    
    document.documentElement.style.zoom = zoom;
    document.documentElement.style.setProperty('--oor-zoom', zoom);
    
    compensateFixedElements(zoom);
    
    if (DEBUG) {
      const computedStyle = window.getComputedStyle(document.documentElement);
      const appliedZoom = computedStyle.zoom;
      console.log('[Scale] Applied zoom to html:', appliedZoom);
      console.log('[Scale] Window innerWidth:', window.innerWidth);
      console.log('[Scale] Document body offsetWidth:', document.body.offsetWidth);
    }
    
    const scaledVh = (vh / zoom) * 0.01;
    document.documentElement.style.setProperty('--oor-vh', `${scaledVh}px`);
    
    if (window.lenis && typeof window.lenis.resize === 'function') {
      window.lenis.resize();
    }
    
    if (DEBUG) {
      console.log(`[Scale] Width: ${vw}px, Zoom: ${zoom.toFixed(3)}x, VH: ${scaledVh.toFixed(2)}px`);
    }
    
    window.oorZoom = zoom;
  }
  
  function init() {
    if (DEBUG) {
      console.log('[Scale] Initializing zoom on html element');
    }
    
    requestAnimationFrame(() => {
      updateZoom();
    });
    
    window.addEventListener('resize', updateZoom, { passive: true });
    
    window.addEventListener('orientationchange', updateZoom, { passive: true });
    
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            if (node.classList && node.classList.contains('mf-cursor')) {
              const zoom = window.oorZoom || 1;
              if (zoom !== 1) {
                node.style.zoom = 1 / zoom;
                if (DEBUG) {
                  console.log('[Scale] Compensated dynamically added cursor');
                }
              }
            }
          }
        });
      });
    });
    
    observer.observe(document.body, {
      childList: true,
      subtree: false
    });
    
    if (DEBUG) {
      console.log('[Scale] Initialized successfully');
    }
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  
  window.oorUpdateZoom = updateZoom;
  
})();
