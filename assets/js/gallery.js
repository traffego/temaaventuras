/**
 * gallery.js – Lightbox para galeria de fotos
 */

'use strict';

(function () {
  const lightbox   = document.getElementById('lightbox');
  const lightboxImg    = document.getElementById('lightbox-img');
  const lightboxLegend = document.getElementById('lightbox-legenda');
  const closeBtn   = document.getElementById('lightbox-fechar');

  if (!lightbox) return;

  // Captura clicks nos links da galeria
  document.addEventListener('click', e => {
    const link = e.target.closest('[data-lightbox]');
    if (link) {
      e.preventDefault();
      openLightbox(
        link.dataset.lightbox,
        link.dataset.caption || link.querySelector('img')?.alt || ''
      );
    }
  });

  // Fechar ao clicar no botão
  closeBtn?.addEventListener('click', closeLightbox);

  // Fechar ao clicar no fundo
  lightbox.addEventListener('click', e => {
    if (e.target === lightbox) closeLightbox();
  });

  // Fechar com ESC
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') navigateLightbox(1);
    if (e.key === 'ArrowLeft')  navigateLightbox(-1);
  });

  // Swipe
  let touchStartX = 0;
  lightbox.addEventListener('touchstart', e => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });
  lightbox.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) navigateLightbox(diff > 0 ? 1 : -1);
  });

  let currentLinks = [];
  let currentIndex = 0;

  function openLightbox(src, caption) {
    // Coleta todos os links da galeria para navegação
    currentLinks = Array.from(document.querySelectorAll('[data-lightbox]'));
    currentIndex = currentLinks.findIndex(l => l.dataset.lightbox === src);

    lightboxImg.src    = src;
    lightboxImg.alt    = caption;
    if (lightboxLegend) lightboxLegend.textContent = caption;

    lightbox.classList.add('ativo');
    document.body.style.overflow = 'hidden';
    closeBtn?.focus();
  }

  function closeLightbox() {
    lightbox.classList.remove('ativo');
    lightboxImg.src = '';
    document.body.style.overflow = '';
  }

  function navigateLightbox(direction) {
    if (!currentLinks.length) return;
    currentIndex = (currentIndex + direction + currentLinks.length) % currentLinks.length;
    const link = currentLinks[currentIndex];
    lightboxImg.src = link.dataset.lightbox;
    lightboxImg.alt = link.dataset.caption || link.querySelector('img')?.alt || '';
    if (lightboxLegend) lightboxLegend.textContent = lightboxImg.alt;
  }
})();
