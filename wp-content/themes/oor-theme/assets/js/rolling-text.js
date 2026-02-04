// Rolling Text Effect
document.addEventListener('DOMContentLoaded', function () {
  const isDesktop = window.innerWidth > 1024;
  
  setTimeout(() => {
    const selector = isDesktop
      ? '.rolling-button .tn-atom'
      : '.oor-splash-enter-button.rolling-button .tn-atom';
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
      if (el.dataset.processed === 'true') return;

      const text = (el.textContent || '').replace(/\s+/g, ' ').trim();
      if (!text) return;

      el.dataset.processed = 'true';
      el.innerHTML = '';

      for (let i = 0; i < 2; i++) {
        const block = document.createElement('div');
        block.classList.add('block');
        let delay = 0;
        for (const ch of text) {
          const span = document.createElement('span');
          span.classList.add('letter');
          span.innerText = ch === ' ' ? '\u00A0' : ch;
          span.style.transitionDelay = `${delay}s`;
          block.appendChild(span);
          delay += 0.03;
        }
        el.appendChild(block);
      }
    });
  }, 50);
});