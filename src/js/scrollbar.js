// Кастомный скроллбар
function initCustomScrollbar() {
  let scrollTimeout;
  const customScrollbar = document.getElementById('customScrollbar');
  const scrollbarThumb = document.getElementById('scrollbarThumb');
  
  if (!customScrollbar || !scrollbarThumb) {
    console.warn('Custom scrollbar elements not found');
    return;
  }
  
  function updateScrollbar() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    
    customScrollbar.classList.add('visible');
    const thumbHeight = (windowHeight / documentHeight) * windowHeight;
    const thumbTop = (scrollTop / (documentHeight - windowHeight)) * (windowHeight - thumbHeight);
    
    scrollbarThumb.style.height = thumbHeight + 'px';
    scrollbarThumb.style.top = thumbTop + 'px';
    
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
      customScrollbar.classList.remove('visible');
    }, 1000);
  }
  
  window.addEventListener('scroll', updateScrollbar);
  window.addEventListener('resize', updateScrollbar);
  
  updateScrollbar();
}

document.addEventListener('DOMContentLoaded', initCustomScrollbar);
