/**
 * counter.js – Animação dos contadores numéricos
 * Usa Intersection Observer para disparar apenas quando visível
 */

'use strict';

(function () {
  const counters = document.querySelectorAll('[data-contador]');
  if (!counters.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(el => observer.observe(el));

  /**
   * Anima o número de 0 até o valor final
   */
  function animateCounter(el) {
    const raw     = el.dataset.contador || el.textContent || '0';
    // Extrai apenas dígitos e o separador decimal
    const numStr  = raw.replace(/[^\d.]/g, '');
    const suffix  = raw.replace(/[\d.]/g, '').trim(); // Ex: "+", "%"
    const end     = parseFloat(numStr) || 0;
    const isFloat = numStr.includes('.');
    const decimals = isFloat ? (numStr.split('.')[1]?.length || 0) : 0;

    const duration = 2000; // ms
    const startTime = performance.now();

    function update(now) {
      const elapsed  = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      // Easing easeOutCubic
      const eased    = 1 - Math.pow(1 - progress, 3);
      const current  = end * eased;

      el.textContent = (isFloat
        ? current.toFixed(decimals)
        : Math.floor(current).toLocaleString('pt-BR')
      ) + suffix;

      if (progress < 1) {
        requestAnimationFrame(update);
      } else {
        el.textContent = (isFloat
          ? end.toFixed(decimals)
          : Math.floor(end).toLocaleString('pt-BR')
        ) + suffix;
      }
    }

    requestAnimationFrame(update);
  }
})();
