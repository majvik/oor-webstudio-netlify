// Курсор MouseFollower + кастомная реализация

(function (global, factory) {
  if (typeof exports === 'object' && typeof module !== 'undefined') {
    module.exports = factory();
  } else if (typeof define === 'function' && define.amd) {
    define(factory);
  } else {
    (global || self).MouseFollower = factory();
  }
})(this, function () {
  'use strict';

  function MouseFollower(options) {
    if (options === void 0) { options = {}; }
    
    this.options = Object.assign({}, {
      el: null,
      container: document.body,
      className: 'mf-cursor',
      innerClassName: 'mf-cursor-inner',
      textClassName: 'mf-cursor-text',
      mediaClassName: 'mf-cursor-media',
      mediaBoxClassName: 'mf-cursor-media-box',
      iconSvgClassName: 'mf-svgsprite',
      iconSvgNamePrefix: '-',
      iconSvgSrc: '',
      dataAttr: 'cursor',
      hiddenState: '-hidden',
      textState: '-text',
      iconState: '-icon',
      activeState: '-active',
      mediaState: '-media',
      stateDetection: {
        '-pointer': 'a,button'
      },
      visible: true,
      visibleOnState: false,
      speed: 0.55,
      ease: 'expo.out',
      overwrite: true,
      skewing: 0,
      skewingText: 2,
      skewingIcon: 2,
      skewingMedia: 2,
      skewingDelta: 0.001,
      skewingDeltaMax: 0.15,
      stickDelta: 0.15,
      showTimeout: 0,
      hideOnLeave: true,
      hideTimeout: 300,
      hideMediaTimeout: 300,
      initialPos: [-window.innerWidth, -window.innerHeight]
    }, options);

    if (this.options.visible && this.options.stateDetection == null) {
      this.options.stateDetection['-hidden'] = 'iframe';
    }

    this.gsap = MouseFollower.gsap || window.gsap;
    this.el = typeof this.options.el === 'string' ? document.querySelector(this.options.el) : this.options.el;
    this.container = typeof this.options.container === 'string' ? document.querySelector(this.options.container) : this.options.container;
    this.skewing = this.options.skewing;
    this.pos = { x: this.options.initialPos[0], y: this.options.initialPos[1] };
    this.vel = { x: 0, y: 0 };
    this.event = {};
    this.events = [];
    this.init();
  }

  MouseFollower.registerGSAP = function (gsap) {
    MouseFollower.gsap = gsap;
  };

  MouseFollower.prototype.init = function () {
    if (!this.el) this.create();
    this.createSetter();
    this.bind();
    this.render(true);
    this.ticker = this.render.bind(this, false);
    this.gsap.ticker.add(this.ticker);
  };

  MouseFollower.prototype.create = function () {
    this.el = document.createElement('div');
    this.el.className = this.options.className;
    this.el.classList.add(this.options.hiddenState);
    
    this.inner = document.createElement('div');
    this.inner.className = this.options.innerClassName;
    
    this.text = document.createElement('div');
    this.text.className = this.options.textClassName;
    
    this.media = document.createElement('div');
    this.media.className = this.options.mediaClassName;
    
    this.mediaBox = document.createElement('div');
    this.mediaBox.className = this.options.mediaBoxClassName;
    
    this.media.appendChild(this.mediaBox);
    this.inner.appendChild(this.media);
    this.inner.appendChild(this.text);
    this.el.appendChild(this.inner);
    this.container.appendChild(this.el);
  };

  MouseFollower.prototype.createSetter = function () {
    this.setter = {
      x: this.gsap.quickSetter(this.el, 'x', 'px'),
      y: this.gsap.quickSetter(this.el, 'y', 'px'),
      rotation: this.gsap.quickSetter(this.el, 'rotation', 'deg'),
      scaleX: this.gsap.quickSetter(this.el, 'scaleX'),
      scaleY: this.gsap.quickSetter(this.el, 'scaleY'),
      wc: this.gsap.quickSetter(this.el, 'willChange'),
      inner: {
        rotation: this.gsap.quickSetter(this.inner, 'rotation', 'deg')
      }
    };
  };

  MouseFollower.prototype.bind = function () {
    var self = this;
    
    this.event.mouseleave = function () {
      return self.hide();
    };
    
    this.event.mouseenter = function () {
      return self.show();
    };
    
    this.event.mousedown = function () {
      return self.addState(self.options.activeState);
    };
    
    this.event.mouseup = function () {
      return self.removeState(self.options.activeState);
    };
    
    this.event.mousemoveOnce = function () {
      return self.show();
    };
    
    this.event.mousemove = function (e) {
      const zoom = window.oorZoom || 1;
      const clientX = e.clientX / zoom;
      const clientY = e.clientY / zoom;
      
      self.gsap.to(self.pos, {
        x: self.stick ? self.stick.x - (self.stick.x - clientX) * self.options.stickDelta : clientX,
        y: self.stick ? self.stick.y - (self.stick.y - clientY) * self.options.stickDelta : clientY,
        overwrite: self.options.overwrite,
        ease: self.options.ease,
        duration: self.visible ? self.options.speed : 0,
        onUpdate: function () {
          return self.vel = { x: clientX - self.pos.x, y: clientY - self.pos.y };
        }
      });
    };
    
    this.event.mouseover = function (e) {
      for (var target = e.target; target && target !== self.container && (!e.relatedTarget || !target.contains(e.relatedTarget)); target = target.parentNode) {
        for (var state in self.options.stateDetection) {
          if (target.matches(self.options.stateDetection[state])) {
            self.addState(state);
          }
        }
        
        if (self.options.dataAttr) {
          var data = self.getFromDataset(target);
          if (data.state) self.addState(data.state);
          if (data.text) self.setText(data.text);
          if (data.icon) self.setIcon(data.icon);
          if (data.img) self.setImg(data.img);
          if (data.video) self.setVideo(data.video);
          if (data.show !== undefined) self.show();
          if (data.stick !== undefined) self.setStick(data.stick || target);
        }
      }
    };
    
    this.event.mouseout = function (e) {
      for (var target = e.target; target && target !== self.container && (!e.relatedTarget || !target.contains(e.relatedTarget)); target = target.parentNode) {
        for (var state in self.options.stateDetection) {
          if (target.matches(self.options.stateDetection[state])) {
            self.removeState(state);
          }
        }
        
        if (self.options.dataAttr) {
          var data = self.getFromDataset(target);
          if (data.state) self.removeState(data.state);
          if (data.text) self.removeText();
          if (data.icon) self.removeIcon();
          if (data.img) self.removeImg();
          if (data.video) self.removeVideo();
          if (data.show !== undefined) self.hide();
          if (data.stick !== undefined) self.removeStick();
        }
      }
    };

    if (this.options.hideOnLeave) {
      this.container.addEventListener('mouseleave', this.event.mouseleave, { passive: true });
    }
    
    if (this.options.visible) {
      this.container.addEventListener('mouseenter', this.event.mouseenter, { passive: true });
    }
    
    if (this.options.activeState) {
      this.container.addEventListener('mousedown', this.event.mousedown, { passive: true });
      this.container.addEventListener('mouseup', this.event.mouseup, { passive: true });
    }
    
    this.container.addEventListener('mousemove', this.event.mousemove, { passive: true });
    
    if (this.options.visible) {
      this.container.addEventListener('mousemove', this.event.mousemoveOnce, { passive: true, once: true });
    }
    
    if (this.options.stateDetection || this.options.dataAttr) {
      this.container.addEventListener('mouseover', this.event.mouseover, { passive: true });
      this.container.addEventListener('mouseout', this.event.mouseout, { passive: true });
    }
  };

  MouseFollower.prototype.render = function (force) {
    if (force === true || (this.vel.y !== 0 && this.vel.x !== 0)) {
      this.trigger('render');
      this.setter.wc('transform');
      this.setter.x(this.pos.x);
      this.setter.y(this.pos.y);
      
      if (this.skewing) {
        var velocity = Math.sqrt(Math.pow(this.vel.x, 2) + Math.pow(this.vel.y, 2));
        var skew = Math.min(velocity * this.options.skewingDelta, this.options.skewingDeltaMax) * this.skewing;
        var angle = 180 * Math.atan2(this.vel.y, this.vel.x) / Math.PI;
        
        this.setter.rotation(angle);
        this.setter.scaleX(1 + skew);
        this.setter.scaleY(1 - skew);
        this.setter.inner.rotation(-angle);
      }
    } else {
      this.setter.wc('auto');
    }
  };

  MouseFollower.prototype.show = function () {
    var self = this;
    this.trigger('show');
    clearInterval(this.visibleInt);
    this.visibleInt = setTimeout(function () {
      self.el.classList.remove(self.options.hiddenState);
      self.visible = true;
      self.render(true);
    }, this.options.showTimeout);
  };

  MouseFollower.prototype.hide = function () {
    var self = this;
    this.trigger('hide');
    clearInterval(this.visibleInt);
    this.el.classList.add(this.options.hiddenState);
    this.visibleInt = setTimeout(function () {
      return self.visible = false;
    }, this.options.hideTimeout);
  };

  MouseFollower.prototype.toggle = function (show) {
    if (show === true || (show !== false && !this.visible)) {
      this.show();
    } else {
      this.hide();
    }
  };

  MouseFollower.prototype.addState = function (state) {
    this.trigger('addState', state);
    if (state === this.options.hiddenState) return this.hide();
    this.el.classList.add.apply(this.el.classList, state.split(' '));
    if (this.options.visibleOnState) this.show();
  };

  MouseFollower.prototype.removeState = function (state) {
    this.trigger('removeState', state);
    if (state === this.options.hiddenState) return this.show();
    this.el.classList.remove.apply(this.el.classList, state.split(' '));
    if (this.options.visibleOnState && this.el.className === this.options.className) this.hide();
  };

  MouseFollower.prototype.toggleState = function (state, add) {
    if (add === true || (add !== false && !this.el.classList.contains(state))) {
      this.addState(state);
    } else {
      this.removeState(state);
    }
  };

  MouseFollower.prototype.setSkewing = function (skewing) {
    this.gsap.to(this, { skewing: skewing });
  };

  MouseFollower.prototype.removeSkewing = function () {
    this.gsap.to(this, { skewing: this.options.skewing });
  };

  MouseFollower.prototype.setStick = function (element) {
    var rect = (typeof element === 'string' ? document.querySelector(element) : element).getBoundingClientRect();
    this.stick = {
      y: rect.top + rect.height / 2,
      x: rect.left + rect.width / 2
    };
  };

  MouseFollower.prototype.removeStick = function () {
    this.stick = false;
  };

  MouseFollower.prototype.setText = function (text) {
    this.text.innerHTML = text;
    this.addState(this.options.textState);
    this.setSkewing(this.options.skewingText);
  };

  MouseFollower.prototype.removeText = function () {
    this.removeState(this.options.textState);
    this.removeSkewing();
  };

  MouseFollower.prototype.setIcon = function (icon, name) {
    if (name === void 0) { name = ''; }
    this.text.innerHTML = '';
    this.addState(this.options.iconState);
    this.setSkewing(this.options.skewingIcon);
  };

  MouseFollower.prototype.removeIcon = function () {
    this.removeState(this.options.iconState);
    this.removeSkewing();
  };

  MouseFollower.prototype.setMedia = function (media) {
    var self = this;
    clearTimeout(this.mediaInt);
    if (media) {
      this.mediaBox.innerHTML = '';
      this.mediaBox.appendChild(media);
    }
    this.mediaInt = setTimeout(function () {
      return self.addState(self.options.mediaState);
    }, 20);
    this.setSkewing(this.options.skewingMedia);
  };

  MouseFollower.prototype.removeMedia = function () {
    var self = this;
    clearTimeout(this.mediaInt);
    this.removeState(this.options.mediaState);
    this.mediaInt = setTimeout(function () {
      return self.mediaBox.innerHTML = '';
    }, this.options.hideMediaTimeout);
    this.removeSkewing();
  };

  MouseFollower.prototype.setImg = function (src) {
    if (!this.mediaImg) {
      this.mediaImg = new Image();
    }
    if (this.mediaImg.src !== src) {
      this.mediaImg.src = src;
    }
    this.setMedia(this.mediaImg);
  };

  MouseFollower.prototype.removeImg = function () {
    this.removeMedia();
  };

  MouseFollower.prototype.setVideo = function (src) {
    if (!this.mediaVideo) {
      this.mediaVideo = document.createElement('video');
      this.mediaVideo.muted = true;
      this.mediaVideo.loop = true;
      this.mediaVideo.autoplay = true;
    }
    if (this.mediaVideo.src !== src) {
      this.mediaVideo.src = src;
      this.mediaVideo.load();
    }
    
    // Безопасный play() с обработкой ошибок
    this.mediaVideo.play().catch(error => {
      if (error.name !== 'AbortError') {
        console.warn('[OOR] Video play error (ignored):', error.message);
      }
    });
    
    this.setMedia(this.mediaVideo);
  };

  MouseFollower.prototype.removeVideo = function () {
    if (this.mediaVideo && this.mediaVideo.readyState > 2) {
      this.mediaVideo.pause();
    }
    this.removeMedia();
  };

  MouseFollower.prototype.on = function (event, callback) {
    if (!(this.events[event] instanceof Array)) {
      this.off(event);
    }
    this.events[event].push(callback);
  };

  MouseFollower.prototype.off = function (event, callback) {
    this.events[event] = callback ? this.events[event].filter(function (cb) { return cb !== callback; }) : [];
  };

  MouseFollower.prototype.trigger = function (event) {
    var args = arguments;
    var self = this;
    if (this.events[event]) {
      this.events[event].forEach(function (callback) {
        return callback.call.apply(callback, [self, self].concat([].slice.call(args, 1)));
      });
    }
  };

  MouseFollower.prototype.getFromDataset = function (element) {
    var dataset = element.dataset;
    return {
      state: dataset[this.options.dataAttr],
      show: dataset[this.options.dataAttr + 'Show'],
      text: dataset[this.options.dataAttr + 'Text'],
      icon: dataset[this.options.dataAttr + 'Icon'],
      img: dataset[this.options.dataAttr + 'Img'],
      video: dataset[this.options.dataAttr + 'Video'],
      stick: dataset[this.options.dataAttr + 'Stick']
    };
  };

  MouseFollower.prototype.destroy = function () {
    this.trigger('destroy');
    this.gsap.ticker.remove(this.ticker);
    this.container.removeEventListener('mouseleave', this.event.mouseleave);
    this.container.removeEventListener('mouseenter', this.event.mouseenter);
    this.container.removeEventListener('mousedown', this.event.mousedown);
    this.container.removeEventListener('mouseup', this.event.mouseup);
    this.container.removeEventListener('mousemove', this.event.mousemove);
    this.container.removeEventListener('mousemove', this.event.mousemoveOnce);
    this.container.removeEventListener('mouseover', this.event.mouseover);
    this.container.removeEventListener('mouseout', this.event.mouseout);
    
    if (this.el) {
      this.container.removeChild(this.el);
      this.el = null;
      this.mediaImg = null;
      this.mediaVideo = null;
    }
  };

  return MouseFollower;
});

// Кастомная реализация курсора для OOR
let cursorInstance = null;

function initCursor() {
  // Отключить курсор на страницах магазина
  const IS_MERCH_PAGE = document.body.classList.contains('oor-merch-page');
  const IS_PRODUCT_PAGE = document.body.classList.contains('oor-product-page');
  const IS_CART_PAGE = document.body.classList.contains('oor-cart-page');
  const IS_CHECKOUT_PAGE = document.body.classList.contains('oor-checkout-page');
  
  if (IS_MERCH_PAGE || IS_PRODUCT_PAGE || IS_CART_PAGE || IS_CHECKOUT_PAGE) {
    if (cursorInstance) {
      cursorInstance.destroy();
      cursorInstance = null;
    }
    return;
  }
  
  if (window.innerWidth <= 1024) {
    if (cursorInstance) {
      cursorInstance.destroy();
      cursorInstance = null;
    }
    return;
  }
  
  if (typeof MouseFollower === 'undefined') {
    console.error('[OOR] MouseFollower not loaded - cursor disabled');
    return;
  }

  if (typeof gsap === 'undefined') {
    console.error('[OOR] GSAP not loaded - cursor animations disabled');
    return;
  }

  if (cursorInstance) {
    cursorInstance.destroy();
    cursorInstance = null;
  }

  cursorInstance = new MouseFollower({
    speed: 0.6,
    skewing: 2.4,
    skewingText: 3,
    skewingDeltaMax: 0.25
  });

  // Настройка data-атрибутов для курсора
  document.querySelectorAll('.video-cuberto-cursor-1').forEach(el => {
    el.setAttribute('data-cursor-video', '/public/assets/OUTOFREC_reel_v4_nologo.mp4');
  });

  document.querySelectorAll('.img-cuberto-cursor-1').forEach(el => {
    el.setAttribute('data-cursor-img', '/public/assets/challenge-studio.png');
  });
  document.querySelectorAll('.img-cuberto-cursor-2').forEach(el => {
    el.setAttribute('data-cursor-img', '/public/assets/good-works.png');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  if (typeof gsap !== 'undefined') {
    initCursor();
  } else {
    const checkGSAP = setInterval(() => {
      if (typeof gsap !== 'undefined') {
        clearInterval(checkGSAP);
        initCursor();
      }
    }, 100);
    
    setTimeout(() => {
      clearInterval(checkGSAP);
      console.warn('[OOR] GSAP timeout - cursor may not work properly');
    }, 5000);
  }

  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      initCursor();
    }, 150);
  });
});