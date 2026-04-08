/**
 * main.js – Inicialização geral do Tema Aventuras
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
  initIntersectionObserver();
  initTestimonialsCarousel();
  initSmoothScroll();
});

// =========================================
// INTERSECTION OBSERVER – Animações de entrada
// =========================================
function initIntersectionObserver() {
  const elementos = document.querySelectorAll('.animar-entrada');
  if (!elementos.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visivel');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

  elementos.forEach(el => observer.observe(el));
}

// =========================================
// TESTIMONIALS CAROUSEL
// =========================================
function initTestimonialsCarousel() {
  const track  = document.getElementById('testimonials-track');
  const dotsEl = document.getElementById('testimonials-dots');
  const prev   = document.getElementById('testimonial-prev');
  const next   = document.getElementById('testimonial-next');

  if (!track) return;

  const slides   = track.querySelectorAll('.testimonial-slide');
  let current    = 0;
  let perView    = getPerView();
  let total      = Math.ceil(slides.length / perView);
  let autoPlay   = null;

  // Criar dots
  function buildDots() {
    if (!dotsEl) return;
    dotsEl.innerHTML = '';
    total = Math.ceil(slides.length / perView);
    for (let i = 0; i < total; i++) {
      const btn = document.createElement('button');
      btn.className = 'dot' + (i === current ? ' ativo' : '');
      btn.setAttribute('role', 'tab');
      btn.setAttribute('aria-label', `Depoimento ${i + 1}`);
      btn.setAttribute('aria-selected', i === current);
      btn.addEventListener('click', () => goTo(i));
      dotsEl.appendChild(btn);
    }
  }

  function getPerView() {
    if (window.innerWidth <= 640)  return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
  }

  function goTo(index) {
    current = Math.max(0, Math.min(index, total - 1));
    const offset = current * perView;
    const slideWidth = slides[0]?.offsetWidth || 0;
    const gap = 24;
    track.style.transform = `translateX(-${offset * (slideWidth + gap)}px)`;
    buildDots();
  }

  function startAuto() {
    autoPlay = setInterval(() => goTo((current + 1) % total), 5000);
  }

  function stopAuto() {
    clearInterval(autoPlay);
  }

  prev?.addEventListener('click', () => { stopAuto(); goTo(current - 1); startAuto(); });
  next?.addEventListener('click', () => { stopAuto(); goTo((current + 1) % total); startAuto(); });

  // Swipe touch
  let touchStartX = 0;
  track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) {
      stopAuto();
      goTo(diff > 0 ? current + 1 : current - 1);
      startAuto();
    }
  });

  // Keyboard
  track.closest('[role="region"]')?.addEventListener('keydown', e => {
    if (e.key === 'ArrowLeft') { stopAuto(); goTo(current - 1); startAuto(); }
    if (e.key === 'ArrowRight') { stopAuto(); goTo(current + 1); startAuto(); }
  });

  // Resize
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      const newPerView = getPerView();
      if (newPerView !== perView) {
        perView = newPerView;
        current = 0;
        buildDots();
        goTo(0);
      }
    }, 200);
  });

  buildDots();
  startAuto();
}

// =========================================
// SMOOTH SCROLL para links âncora
// =========================================
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const id = link.getAttribute('href').slice(1);
      if (!id) return;
      const target = document.getElementById(id);
      if (!target) return;
      e.preventDefault();
      const navH = document.getElementById('navbar')?.offsetHeight || 80;
      const top  = target.getBoundingClientRect().top + window.scrollY - navH - 16;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });
}
