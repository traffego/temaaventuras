/**
 * checkout.js – Lógica do checkout com Mercado Pago
 * CardForm JS + polling PIX + stepper de etapas
 */

'use strict';

(function () {

  // =========================================
  // CONFIGURAÇÃO (injetada via wp_localize_script)
  // =========================================
  const CFG = window.taCheckoutConfig || {};
  const { publicKey, ajaxUrl, nonce, reservaId, metodoPagamento, parcelas_max } = CFG;

  // =========================================
  // STEPPER
  // =========================================
  const steps     = document.querySelectorAll('.checkout-step');
  const stepBtns  = document.querySelectorAll('[data-step-next]');
  const stepPrevs = document.querySelectorAll('[data-step-prev]');
  let currentStep = 0;

  function goToStep(n) {
    steps.forEach((s, i) => {
      s.classList.toggle('ativo', i === n);
      s.setAttribute('aria-hidden', i !== n);
    });
    document.querySelectorAll('.step-indicator').forEach((dot, i) => {
      dot.classList.toggle('ativo', i <= n);
    });
    currentStep = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  stepBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = parseInt(btn.dataset.stepNext);
      if (validarEtapa(currentStep)) goToStep(target);
    });
  });

  stepPrevs.forEach(btn => {
    btn.addEventListener('click', () => goToStep(parseInt(btn.dataset.stepPrev)));
  });

  // =========================================
  // SELEÇÃO DE MÉTODO DE PAGAMENTO
  // =========================================
  document.querySelectorAll('.metodo-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.metodo-card').forEach(c => c.classList.remove('selecionado'));
      card.classList.add('selecionado');
      const metodo = card.dataset.metodo;
      document.querySelectorAll('[data-metodo-form]').forEach(f => {
        f.classList.toggle('ativo', f.dataset.metodoForm === metodo);
      });
      document.getElementById('campo-metodo').value = metodo;
    });
  });

  // =========================================
  // INSCRITOS DINÂMICOS
  // =========================================
  const addInscritoBtn = document.getElementById('add-inscrito');
  const inscritosWrap  = document.getElementById('inscritos-wrap');
  let inscritoCount    = parseInt(document.querySelectorAll('.inscrito-item').length) || 1;

  addInscritoBtn?.addEventListener('click', () => {
    inscritoCount++;
    const div = document.createElement('div');
    div.className = 'inscrito-item';
    div.innerHTML = `
      <div class="inscrito-header">
        <h4>Inscrito ${inscritoCount}</h4>
        <button type="button" class="btn-remover-inscrito btn btn--ghost btn--pequeno">✕ Remover</button>
      </div>
      <div class="grid grid--3">
        <div class="form-grupo">
          <label>Nome completo *</label>
          <input type="text" name="inscrito_nome[]" required placeholder="Nome completo">
        </div>
        <div class="form-grupo">
          <label>CPF *</label>
          <input type="text" name="inscrito_cpf[]" required placeholder="000.000.000-00" class="cpf-mask">
        </div>
        <div class="form-grupo">
          <label>Telefone *</label>
          <input type="text" name="inscrito_telefone[]" required placeholder="(11) 99999-9999">
        </div>
      </div>`;
    inscritosWrap?.appendChild(div);
    div.querySelector('.btn-remover-inscrito')?.addEventListener('click', () => {
      div.remove();
      atualizarValorTotal();
    });
    aplicarMascaras(div);
    atualizarValorTotal();
  });

  // Remover inscrito existente
  document.addEventListener('click', e => {
    if (e.target.matches('.btn-remover-inscrito')) {
      e.target.closest('.inscrito-item')?.remove();
      atualizarValorTotal();
    }
  });

  // =========================================
  // CALCULAR VALOR TOTAL
  // =========================================
  const pricePerPerson = parseFloat(CFG.precoPorPessoa || 0);

  function atualizarValorTotal() {
    const qtd   = document.querySelectorAll('.inscrito-item').length;
    const total = qtd * pricePerPerson;
    const el    = document.getElementById('valor-total-display');
    if (el) el.textContent = 'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    document.getElementById('campo-valor-total').value = total.toFixed(2);
    document.getElementById('campo-qtd-inscritos').value = qtd;
  }

  atualizarValorTotal();

  // =========================================
  // MERCADO PAGO – CARDFORM
  // =========================================
  let mpInstance = null;

  function initCardForm() {
    if (!window.MercadoPago || !publicKey) return;

    mpInstance = new window.MercadoPago(publicKey, { locale: 'pt-BR' });

    const cardForm = mpInstance.cardForm({
      amount: document.getElementById('campo-valor-total')?.value || '0',
      iframe: true,
      form: {
        id: 'form-cartao',
        cardholderName: { id: 'mp-cardholderName', placeholder: 'Nome como no cartão' },
        cardholderEmail: { id: 'mp-email', placeholder: 'email@dominio.com' },
        cardNumber: { id: 'mp-cardNumber', placeholder: '•••• •••• •••• ••••' },
        cardExpirationMonth: { id: 'mp-cardExpirationMonth', placeholder: 'MM' },
        cardExpirationYear: { id: 'mp-cardExpirationYear', placeholder: 'YY' },
        securityCode: { id: 'mp-securityCode', placeholder: 'CVV' },
        installments: { id: 'mp-installments' },
        identificationType: { id: 'mp-identificationType' },
        identificationNumber: { id: 'mp-identificationNumber', placeholder: '000.000.000-00' },
        issuer: { id: 'mp-issuer' },
      },
      callbacks: {
        onFormMounted: err => { if (err) console.error('CardForm error:', err); },
        onSubmit: async e => {
          e.preventDefault();
          const {
            paymentMethodId, issuerId, cardholderEmail,
            amount, token, installments, identificationNumber, identificationType,
          } = cardForm.getCardFormData();

          setLoading(true);

          const fd = new FormData();
          fd.append('action', 'ta_processar_cartao');
          fd.append('nonce', nonce);
          fd.append('reserva_id', reservaId || document.getElementById('campo-reserva-id')?.value || '');
          fd.append('token', token);
          fd.append('pm_id', paymentMethodId);
          fd.append('issuer_id', issuerId);
          fd.append('parcelas', installments);
          fd.append('email', cardholderEmail);
          fd.append('cpf', identificationNumber);
          fd.append('valor', amount);

          try {
            const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
            const json = await res.json();

            if (json.success && json.data.aprovado) {
              window.location.href = json.data.redirect;
            } else {
              mostrarErro(json.data?.message || 'Pagamento não aprovado. Tente novamente.');
            }
          } catch (err) {
            mostrarErro('Erro de conexão. Verifique sua internet.');
          } finally {
            setLoading(false);
          }
        },
        onFetching: resource => {
          const progress = document.getElementById('mp-progress');
          if (progress) progress.style.display = 'block';
          return () => { if (progress) progress.style.display = 'none'; };
        },
      },
    });
  }

  // Iniciar CardForm quando a aba de cartão for selecionada
  document.querySelectorAll('.metodo-card').forEach(card => {
    card.addEventListener('click', () => {
      if (card.dataset.metodo === 'credit_card' && !mpInstance) {
        initCardForm();
      }
    });
  });

  // =========================================
  // SUBMIT DO FORMULÁRIO PRINCIPAL (DADOS + SESSÃO)
  // =========================================
  document.getElementById('form-checkout')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validarEtapa(0)) return;

    setLoading(true);
    const fd = new FormData(this);
    fd.append('action', 'ta_criar_reserva_checkout');
    fd.append('nonce', nonce);

    try {
      const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
      const json = await res.json();

      if (json.success) {
        document.getElementById('campo-reserva-id').value = json.data.reserva_id;
        const metodo = document.getElementById('campo-metodo').value;

        if (metodo === 'pix') {
          renderizarPix(json.data.pix);
          document.getElementById('pix-display').style.display = 'block';
          const btnFinalizar = document.getElementById('btn-finalizar');
          if(btnFinalizar) btnFinalizar.style.display = 'none';
          document.querySelector('.checkout-stepper').style.display = 'none';
          goToStep(1);
          iniciarPollingPix(json.data.reserva_id);
        } else {
          goToStep(1);
        }
      } else {
        mostrarErro(json.data?.message || 'Erro ao processar. Tente novamente.');
      }
    } catch (err) {
      mostrarErro('Erro de conexão. Verifique sua internet.');
    } finally {
      setLoading(false);
    }
  });

  // =========================================
  // RENDERIZAR PIX
  // =========================================
  function renderizarPix(pix) {
    const imgEl   = document.getElementById('pix-qrcode-img');
    const copyEl  = document.getElementById('pix-copia-cola');
    const timer   = document.getElementById('pix-timer');

    if (imgEl && pix.qr_code_base64) {
      imgEl.src = 'data:image/png;base64,' + pix.qr_code_base64;
      imgEl.style.display = 'block';
    }
    if (copyEl) copyEl.value = pix.qr_code || '';

    // Timer 30 minutos
    if (timer) {
      let segundos = 30 * 60;
      const interval = setInterval(() => {
        segundos--;
        const m = String(Math.floor(segundos / 60)).padStart(2, '0');
        const s = String(segundos % 60).padStart(2, '0');
        timer.textContent = `${m}:${s}`;
        if (segundos <= 0) {
          clearInterval(interval);
          timer.textContent = 'Expirado';
        }
      }, 1000);
    }
  }

  // =========================================
  // COPIAR PIX
  // =========================================
  document.getElementById('btn-copiar-pix')?.addEventListener('click', () => {
    const val = document.getElementById('pix-copia-cola')?.value;
    if (!val) return;
    navigator.clipboard.writeText(val).then(() => {
      const btn = document.getElementById('btn-copiar-pix');
      btn.textContent = '✅ Copiado!';
      setTimeout(() => btn.textContent = '📋 Copiar código', 2000);
    });
  });

  // =========================================
  // POLLING PIX (verificar a cada 5s)
  // =========================================
  function iniciarPollingPix(reservaId) {
    if (!reservaId) return;

    const interval = setInterval(async () => {
      const fd = new FormData();
      fd.append('action', 'ta_consultar_pix');
      fd.append('nonce', nonce);
      fd.append('reserva_id', reservaId);

      try {
        const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success && json.data.status === 'aprovado') {
          clearInterval(interval);
          window.location.href = json.data.redirect;
        }
      } catch { /* silencioso */ }
    }, 5000);

    window.taPixPolling = interval;
  }

  // =========================================
  // MÁSCARAS DE INPUT
  // =========================================
  function aplicarMascaras(root = document) {
    root.querySelectorAll('.cpf-mask').forEach(input => {
      input.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2')
             .replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
             .replace(/\.(\d{3})(\d)/, '.$1-$2');
        e.target.value = v;
      });
    });

    root.querySelectorAll('.tel-mask').forEach(input => {
      input.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = v;
      });
    });
  }

  aplicarMascaras();

  // =========================================
  // VALIDAÇÃO DE ETAPA
  // =========================================
  function validarEtapa(step) {
    const currentEl = steps[step];
    if (!currentEl) return true;
    const inputs = currentEl.querySelectorAll('[required]');
    let valid = true;
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('erro');
        input.addEventListener('input', () => input.classList.remove('erro'), { once: true });
        valid = false;
      }
    });
    if (!valid) mostrarErro('Preencha todos os campos obrigatórios.');
    return valid;
  }

  // =========================================
  // UTILITÁRIOS
  // =========================================
  function setLoading(loading) {
    const btn  = document.getElementById('btn-finalizar');
    const spin = document.getElementById('checkout-spinner');
    if (btn)  btn.disabled = loading;
    if (spin) spin.style.display = loading ? 'flex' : 'none';
  }

  function mostrarErro(msg) {
    const el = document.getElementById('checkout-erro');
    if (!el) { alert(msg); return; }
    el.textContent = msg;
    el.style.display = 'block';
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => el.style.display = 'none', 6000);
  }

})();
