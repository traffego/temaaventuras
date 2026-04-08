/**
 * navbar.js – Sticky navbar + Mobile menu toggle
 */

'use strict';

(function () {
  const navbar      = document.getElementById('navbar');
  const hamburger   = document.getElementById('navbar-hamburger');
  const mobileMenu  = document.getElementById('menu-mobile');

  if (!navbar) return;

  // =========================================
  // STICKY NAVBAR ao rolar
  // =========================================
  let lastScroll = 0;

  window.addEventListener('scroll', () => {
    const currentScroll = window.scrollY;

    // Adicionar classe scrolled
    navbar.classList.toggle('navbar--scrolled', currentScroll > 60);

    // Esconder navbar ao rolar para baixo, mostrar ao subir
    if (currentScroll > 200) {
      if (currentScroll > lastScroll && !mobileMenu?.classList.contains('ativo')) {
        navbar.style.transform = 'translateY(-100%)';
      } else {
        navbar.style.transform = 'translateY(0)';
      }
    } else {
      navbar.style.transform = 'translateY(0)';
    }

    lastScroll = currentScroll;
  }, { passive: true });

  // =========================================
  // MOBILE MENU TOGGLE
  // =========================================
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      const isOpen = hamburger.classList.contains('ativo');
      hamburger.classList.toggle('ativo');
      mobileMenu.classList.toggle('ativo');
      hamburger.setAttribute('aria-expanded', String(!isOpen));
      mobileMenu.setAttribute('aria-hidden', String(isOpen));
      document.body.style.overflow = isOpen ? '' : 'hidden';
    });

    // Fechar ao clicar em link
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });

    // Fechar com ESC
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeMobileMenu();
    });

    // Fechar ao clicar fora
    document.addEventListener('click', e => {
      if (mobileMenu.classList.contains('ativo') &&
          !mobileMenu.contains(e.target) &&
          !hamburger.contains(e.target)) {
        closeMobileMenu();
      }
    });
  }

  function closeMobileMenu() {
    hamburger?.classList.remove('ativo');
    mobileMenu?.classList.remove('ativo');
    hamburger?.setAttribute('aria-expanded', 'false');
    mobileMenu?.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // =========================================
  // HIGHLIGHT do item de menu ativo via scroll
  // =========================================
  const sections = document.querySelectorAll('section[id]');
  const navItems = document.querySelectorAll('.navbar__item a, .navbar__mobile .navbar__item a');

  if (sections.length && navItems.length) {
    const sectionObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.getAttribute('id');
          navItems.forEach(item => {
            const href = item.getAttribute('href');
            item.classList.toggle('ativo-scroll', href === `#${id}` || href?.endsWith(`#${id}`));
          });
        }
      });
    }, { threshold: 0.4 });

    sections.forEach(s => sectionObserver.observe(s));
  }
})();
