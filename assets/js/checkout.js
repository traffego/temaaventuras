/**
 * checkout.js – Single-page checkout com Mercado Pago
 * Sem stepper. Tudo visível numa única tela.
 */

'use strict';

(function () {

  const CFG = window.taCheckoutConfig || {};
  const { publicKey, ajaxUrl, nonce, parcelas_max } = CFG;
  const pricePerPerson = parseFloat(CFG.precoPorPessoa || 0);

  // =========================================
  // SELEÇÃO DE MÉTODO DE PAGAMENTO
  // =========================================
  document.querySelectorAll('.metodo-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.metodo-card').forEach(c => {
        c.classList.remove('selecionado');
        c.setAttribute('aria-checked', 'false');
      });
      card.classList.add('selecionado');
      card.setAttribute('aria-checked', 'true');

      const metodo = card.dataset.metodo;
      document.querySelectorAll('[data-metodo-form]').forEach(f => {
        const show = f.dataset.metodoForm === metodo;
        f.classList.toggle('ativo', show);
        f.style.display = show ? '' : 'none';
      });
      document.getElementById('campo-metodo').value = metodo;

      if (metodo === 'credit_card' && !mpInstance) {
        initCardForm();
      }
    });
  });
  
  // Como PIX já está carregado de cara, liberamos o botão se for PIX
  document.getElementById('btn-finalizar').disabled = false;

  // =========================================
  // ACOMPANHANTES DINÂMICOS
  // =========================================
  const addInscritoBtn = document.getElementById('add-inscrito');
  const inscritosWrap  = document.getElementById('inscritos-wrap');
  let adicCount        = 0;

  addInscritoBtn?.addEventListener('click', () => {
    adicCount++;
    const div = document.createElement('div');
    div.className = 'inscrito-item';
    div.innerHTML = `
      <div class="inscrito-header">
        <h4>Acompanhante ${adicCount}</h4>
        <button type="button" class="btn-remover-inscrito btn btn--ghost btn--pequeno">✕</button>
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
          <input type="text" name="inscrito_telefone[]" required placeholder="(11) 99999-9999" class="tel-mask">
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

  document.addEventListener('click', e => {
    if (e.target.matches('.btn-remover-inscrito')) {
      e.target.closest('.inscrito-item')?.remove();
      atualizarValorTotal();
    }
  });

  // =========================================
  // CALCULAR VALOR TOTAL E ATUALIZAR BOTÃO
  // =========================================
  function atualizarValorTotal() {
    // 1 (Responsável) + qtd de acompanhantes
    const qtdExtras = document.querySelectorAll('.inscrito-item').length;
    const qtd       = 1 + qtdExtras;
    const total     = qtd * pricePerPerson;
    
    const btnTotal = document.getElementById('btn-total-display');
    if (btnTotal) {
      btnTotal.textContent = '- R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    }
    
    // Como apagamos o aside, não atualizar mais #valor-total-display ou #qtd-inscritos-display
    const campoTotal = document.getElementById('campo-valor-total');
    const campoQtd   = document.getElementById('campo-qtd-inscritos');
    
    if (campoTotal) campoTotal.value = total.toFixed(2);
    if (campoQtd)   campoQtd.value = qtd;
  }

  atualizarValorTotal();

  // =========================================
  // COPIAR DADOS DO RESPONSÁVEL PRO CARTÃO
  // =========================================
  const chkUsarResp = document.getElementById('usar-dados-resp');
  const mpName  = document.getElementById('mp-cardholderName');
  const mpCpf   = document.getElementById('mp-identificationNumber');
  const mpEmail = document.getElementById('mp-email');

  function preencherDadosCartao() {
    if (!chkUsarResp?.checked) return;
    const nome  = document.getElementById('resp-nome')?.value || '';
    const cpf   = document.getElementById('resp-cpf')?.value || '';
    const email = document.getElementById('resp-email')?.value || '';
    if (mpName)  mpName.value = nome;
    if (mpCpf)   mpCpf.value = cpf;
    if (mpEmail) mpEmail.value = email;
  }

  chkUsarResp?.addEventListener('change', () => {
    const wrap = document.getElementById('campos-pagador');
    if (chkUsarResp.checked) {
      preencherDadosCartao();
      if (wrap) wrap.style.display = 'none';
    } else {
      if (mpName)  mpName.value = '';
      if (mpCpf)   mpCpf.value = '';
      if (mpEmail) mpEmail.value = '';
      if (wrap) wrap.style.display = '';
    }
  });

  // Preenche ao selecionar cartão como método
  document.querySelectorAll('.metodo-card').forEach(card => {
    card.addEventListener('click', () => {
      if (card.dataset.metodo === 'credit_card') {
        setTimeout(preencherDadosCartao, 100);
      }
    });
  });

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
        onFormMounted: err => { 
          if (err) return console.error('CardForm error:', err); 
          document.getElementById('btn-finalizar').disabled = false;
        },
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
          fd.append('reserva_id', document.getElementById('campo-reserva-id')?.value || '');
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

  // =========================================
  // SUBMIT PRINCIPAL
  // =========================================
  // Botão está fora do form, então dispara submit via JS
  document.getElementById('btn-finalizar')?.addEventListener('click', () => {
    const form = document.getElementById('form-checkout');
    if (form) form.requestSubmit();
  });

  document.getElementById('form-checkout')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validarTudo()) return;

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
          document.getElementById('btn-finalizar').style.display = 'none';
          iniciarPollingPix(json.data.reserva_id);
        } else {
          // Cartão: já foi processado pelo CardForm
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
    const imgEl  = document.getElementById('pix-qrcode-img');
    const copyEl = document.getElementById('pix-copia-cola');
    const timer  = document.getElementById('pix-timer');

    if (imgEl && pix.qr_code_base64) {
      imgEl.src = 'data:image/png;base64,' + pix.qr_code_base64;
      imgEl.style.display = 'block';
    }
    if (copyEl) copyEl.value = pix.qr_code || '';

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
      setTimeout(() => btn.textContent = '📋 Copiar', 2000);
    });
  });

  // =========================================
  // POLLING PIX
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
  }

  // =========================================
  // MÁSCARAS
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
  // VALIDAÇÃO COMPLETA
  // =========================================
  function validarTudo() {
    const form = document.getElementById('form-checkout');
    if (!form) return true;
    const inputs = form.querySelectorAll('[required]');
    let valid = true;
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('erro');
        input.addEventListener('input', () => input.classList.remove('erro'), { once: true });
        valid = false;
      }
    });
    if (!valid) {
      mostrarErro('Preencha todos os campos obrigatórios.');
      const primeiro = form.querySelector('.erro');
      if (primeiro) primeiro.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
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
