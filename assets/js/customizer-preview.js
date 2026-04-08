/**
 * customizer-preview.js
 * Atualização ao vivo no Customizer (postMessage)
 */

'use strict';

(function ($) {
  // Cores dinâmicas – atualiza :root CSS variables em tempo real
  const colorSettings = {
    'cor_primaria':   '--cor-primaria',
    'cor_secundaria': '--cor-secundaria',
    'cor_terciaria':  '--cor-terciaria',
    'cor_fundo':      '--fundo-base',
    'cor_texto':      '--texto-primario',
  };

  Object.entries(colorSettings).forEach(([setting, cssVar]) => {
    wp.customize(setting, val => {
      val.bind(newVal => {
        document.documentElement.style.setProperty(cssVar, newVal);
        // Atualiza gradientes derivados
        if (setting === 'cor_primaria') {
          document.documentElement.style.setProperty('--fundo-glass', newVal + '14');
          document.documentElement.style.setProperty('--borda-glass', newVal + '33');
          document.documentElement.style.setProperty('--sombra-glow', `0 0 30px ${newVal}4D`);
        }
        if (setting === 'cor_secundaria') {
          document.documentElement.style.setProperty('--sombra-glow-sec', `0 0 30px ${newVal}4D`);
        }
      });
    });
  });

  // Textos em tempo real
  const textSettings = {
    'hero_titulo':      '#hero-titulo',
    'hero_subtitulo':   '.hero__subtitulo',
    'hero_cta_texto':   '#hero-cta-principal',
    'empresa_nome':     '.navbar__logo-texto',
    'stat_1_numero':    '.stat-item:nth-child(1) .stat-item__numero',
    'stat_2_numero':    '.stat-item:nth-child(2) .stat-item__numero',
    'stat_3_numero':    '.stat-item:nth-child(3) .stat-item__numero',
    'stat_4_numero':    '.stat-item:nth-child(4) .stat-item__numero',
    'stat_1_label':     '.stat-item:nth-child(1) .stat-item__label',
    'stat_2_label':     '.stat-item:nth-child(2) .stat-item__label',
    'stat_3_label':     '.stat-item:nth-child(3) .stat-item__label',
    'stat_4_label':     '.stat-item:nth-child(4) .stat-item__label',
    'footer_texto_copy':   '.footer__copy:first-child',
  };

  Object.entries(textSettings).forEach(([setting, selector]) => {
    wp.customize(setting, val => {
      val.bind(newVal => {
        document.querySelectorAll(selector).forEach(el => {
          el.textContent = newVal;
        });
      });
    });
  });

  // Hero – imagem de fundo
  wp.customize('hero_imagem', val => {
    val.bind(newVal => {
      const heroFundo = document.querySelector('.hero__fundo');
      if (heroFundo && heroFundo.tagName === 'IMG') {
        heroFundo.src = newVal;
      }
    });
  });

  // blogname
  wp.customize('blogname', val => {
    val.bind(newVal => {
      document.querySelectorAll('.navbar__logo-texto').forEach(el => {
        el.textContent = newVal;
      });
    });
  });

})(jQuery);
