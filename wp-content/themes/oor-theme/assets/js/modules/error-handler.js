// Централизованная обработка ошибок для OOR проекта
class ErrorHandler {
  constructor() {
    this.errors = [];
    this.maxErrors = 100;
    this.isDebugMode = this.checkDebugMode();
    
    this.init();
  }

  checkDebugMode() {
    return (
      typeof window !== 'undefined' && 
      window.location && 
      (window.location.search.includes('debug') || window.location.search.includes('verbose'))
    );
  }

  init() {
    this.scheduleDependencyCheck();
    
    window.addEventListener('error', (event) => {
      this.handleError({
        type: 'javascript',
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        error: event.error,
        stack: event.error?.stack
      });
    });

    window.addEventListener('unhandledrejection', (event) => {
      this.handleError({
        type: 'promise',
        message: event.reason?.message || 'Unhandled Promise Rejection',
        error: event.reason,
        stack: event.reason?.stack
      });
    });

    window.addEventListener('error', (event) => {
      if (event.target !== window) {
        this.handleError({
          type: 'resource',
          message: `Failed to load resource: ${event.target.src || event.target.href}`,
          element: event.target.tagName,
          src: event.target.src || event.target.href
        });
      }
    }, true);
  }

  scheduleDependencyCheck() {
    setTimeout(() => {
      this.checkCriticalDependencies();
    }, 2000);

    const preloader = document.getElementById('preloader');
    if (preloader) {
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.type === 'attributes' && 
              mutation.attributeName === 'class' && 
              preloader.classList.contains('oor-preloader-hidden')) {
            this.checkCriticalDependencies();
            observer.disconnect();
          }
        });
      });
      observer.observe(preloader, { attributes: true });
    }
  }

  checkCriticalDependencies() {
    const preloader = document.getElementById('preloader');
    if (preloader && !preloader.classList.contains('oor-preloader-hidden')) {
      return;
    }

    if (typeof gsap === 'undefined' && this.isGSAPRequired()) {
      this.handleError({
        type: 'dependency',
        message: 'GSAP library not loaded',
        dependency: 'gsap'
      });
    }

    if (typeof MouseFollower === 'undefined' && this.isCursorRequired()) {
      this.handleError({
        type: 'dependency',
        message: 'MouseFollower library not loaded',
        dependency: 'MouseFollower'
      });
    }

    if (typeof Lenis === 'undefined') {
      console.info('[OOR] Lenis library not loaded - smooth scrolling disabled');
    }
  }

  isGSAPRequired() {
    return document.querySelector('.oor-preloader, .oor-hero-video, .slider-section') !== null;
  }

  isCursorRequired() {
    return document.querySelector('[data-cursor-text], [data-cursor-video], [data-cursor-img]') !== null;
  }

  handleError(errorData) {
    if (this.shouldIgnoreError(errorData)) {
      return;
    }

    const error = {
      ...errorData,
      timestamp: new Date().toISOString(),
      userAgent: navigator.userAgent,
      url: window.location.href
    };

    this.errors.push(error);
    
    if (this.errors.length > this.maxErrors) {
      this.errors.shift();
    }

    this.logError(error);

    if (this.isDebugMode) {
      this.showUserError(error);
    }

    this.sendToAnalytics(error);
  }

  shouldIgnoreError(errorData) {
    if (errorData.type === 'promise' && 
        errorData.message && 
        errorData.message.includes('play() request was interrupted')) {
      return true;
    }

    if (errorData.error && 
        errorData.error.name === 'AbortError' && 
        errorData.message && 
        errorData.message.includes('play()')) {
      return true;
    }

    if (errorData.type === 'resource' && 
        errorData.src && 
        errorData.src.includes('video')) {
      return true;
    }

    return false;
  }

  logError(error) {
    const logMethod = this.isDebugMode ? 'error' : 'warn';
    
    console[logMethod](`[OOR Error] ${error.type.toUpperCase()}:`, {
      message: error.message,
      timestamp: error.timestamp,
      ...(this.isDebugMode && {
        stack: error.stack,
        filename: error.filename,
        lineno: error.lineno,
        colno: error.colno
      })
    });
  }

  showUserError(error) {
    const notification = document.createElement('div');
    notification.className = 'oor-error-notification';
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #ff4444;
      color: white;
      padding: 12px 16px;
      border-radius: 4px;
      font-family: monospace;
      font-size: 12px;
      z-index: 10000;
      max-width: 300px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    `;
    
    notification.innerHTML = `
      <strong>Error: ${error.type}</strong><br>
      ${error.message}<br>
      <small>${error.timestamp}</small>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 5000);
  }

  sendToAnalytics(error) {
    if (typeof gtag !== 'undefined') {
      gtag('event', 'exception', {
        description: error.message,
        fatal: false
      });
    }
  }

  getErrors() {
    return [...this.errors];
  }

  clearErrors() {
    this.errors = [];
  }

  getStats() {
    const stats = {
      total: this.errors.length,
      byType: {},
      recent: this.errors.slice(-10)
    };

    this.errors.forEach(error => {
      stats.byType[error.type] = (stats.byType[error.type] || 0) + 1;
    });

    return stats;
  }
}

const errorHandler = new ErrorHandler();

function safeExecute(fn, fallback = null) {
  try {
    return fn();
  } catch (error) {
    errorHandler.handleError({
      type: 'safeExecute',
      message: error.message,
      error: error,
      stack: error.stack
    });
    return fallback;
  }
}

async function safeExecuteAsync(fn, fallback = null) {
  try {
    return await fn();
  } catch (error) {
    errorHandler.handleError({
      type: 'safeExecuteAsync',
      message: error.message,
      error: error,
      stack: error.stack
    });
    return fallback;
  }
}

function checkAPI(apiName, checkFn) {
  try {
    const result = checkFn();
    if (!result) {
      errorHandler.handleError({
        type: 'apiCheck',
        message: `API ${apiName} is not available`,
        apiName: apiName
      });
    }
    return !!result;
  } catch (error) {
    errorHandler.handleError({
      type: 'apiCheck',
      message: `API ${apiName} check failed: ${error.message}`,
      apiName: apiName,
      error: error
    });
    return false;
  }
}

function loadScript(src, options = {}) {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = src;
    script.async = options.async !== false;
    script.defer = options.defer || false;
    
    script.onload = () => {
      errorHandler.handleError({
        type: 'scriptLoad',
        message: `Script loaded successfully: ${src}`,
        src: src
      });
      resolve(script);
    };
    
    script.onerror = (error) => {
      errorHandler.handleError({
        type: 'scriptLoad',
        message: `Failed to load script: ${src}`,
        src: src,
        error: error
      });
      reject(error);
    };
    
    document.head.appendChild(script);
  });
}

window.OOR = window.OOR || {};
window.OOR.errorHandler = errorHandler;
window.OOR.safeExecute = safeExecute;
window.OOR.safeExecuteAsync = safeExecuteAsync;
window.OOR.checkAPI = checkAPI;
window.OOR.loadScript = loadScript;
