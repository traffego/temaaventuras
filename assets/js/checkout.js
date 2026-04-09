/**
 * checkout.js – Fluxo de reserva de atividade
 * @package TemaAventuras
 */
'use strict';

(function () {

  console.log('TA Checkout: Script inicializado');

  // =========================================
  // FUNÇÕES GLOBAIS DE NAVEGAÇÃO (STEPPER)
  // Definido no início para ser acessível imediatamente via onclick
  // =========================================
  window.taNextStep = function(btn) {
    try {
      console.log('TA Checkout: Próximo clicado', btn);
      const stepDOM = btn.closest('[data-step]');
      if (!stepDOM) throw new Error("Não foi possível encontrar a seção [data-step] pai.");
      
      const currentStep = stepDOM.getAttribute('data-step');
      const nextStep = btn.getAttribute('data-next');
      
      if (validateStep(currentStep)) {
        showStep(nextStep);
      }
    } catch (e) {
      console.error('TA Checkout Erro (taNextStep):', e);
      alert('Erro de Navegação: ' + e.message);
    }
  };

  window.taPrevStep = function(btn) {
    try {
      console.log('TA Checkout: Voltar clicado');
      esconderErro();
      showStep(btn.getAttribute('data-prev'));
    } catch (e) {
      console.error('TA Checkout Erro (taPrevStep):', e);
    }
  };

  const CFG            = window.taCheckoutConfig || {};
  const { publicKey, ajaxUrl, nonce, parcelas_max } = CFG;
  const pricePerPerson = parseFloat(CFG.precoPorPessoa || 0);
  let mpInstance       = null;

  // =========================================
  // VALOR TOTAL
  // =========================================
  function atualizarValorTotal() {
    const qtdExtras = document.querySelectorAll('.inscrito-item').length;
    const qtd       = 1 + qtdExtras;
    const total     = qtd * pricePerPerson;

    const fmt = (v) => 'R$ ' + v.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

    const elBtn   = document.getElementById('btn-total-display');
    const elTotal = document.getElementById('resumo-total');
    const elQtd   = document.getElementById('resumo-qtd');

    if (elBtn)   elBtn.textContent   = fmt(total);
    if (elTotal) elTotal.textContent = fmt(total);
    if (elQtd)   elQtd.textContent   = qtd;

    const campoTotal = document.getElementById('campo-valor-total');
    const campoQtd   = document.getElementById('campo-qtd-inscritos');
    if (campoTotal) campoTotal.value = total.toFixed(2);
    if (campoQtd)   campoQtd.value   = qtd;
  }

  atualizarValorTotal();

  // =========================================
  // ACOMPANHANTES DINÂMICOS
  // =========================================
  let adicCount = 0;
  const addInscritoBtn = document.getElementById('add-inscrito');
  const inscritosWrap  = document.getElementById('inscritos-wrap');

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

  // =========================================
  // STEPPER (TABS) LOGIC
  // =========================================
  const steps = document.querySelectorAll('.checkout-step');
  const indicators = document.querySelectorAll('.step-indicator');

  function showStep(stepIndex) {
    try {
      console.log('showStep() chamado com ->', stepIndex);
      
      const stps = document.querySelectorAll('.checkout-step');
      if (!stps || stps.length === 0) throw new Error("A classe .checkout-step não foi encontrada na página.");
      
      stps.forEach(s => s.classList.remove('ativo'));
      
      const inds = document.querySelectorAll('.step-indicator');
      inds.forEach(i => i.classList.remove('ativado'));
      
      const targetStep = document.querySelector(`.checkout-step[data-step="${stepIndex}"]`);
      const targetInd = document.querySelector(`.step-indicator[data-target="${stepIndex}"]`);
      
      if (!targetStep) throw new Error(`.checkout-step com data-step="${stepIndex}" não encontrado.`);
      if (!targetInd) throw new Error(`.step-indicator com data-target="${stepIndex}" não encontrado.`);
      
      targetStep.classList.add('ativo');
      targetInd.classList.add('ativado');
      
      const stepper = document.querySelector('.checkout-stepper');
      if (stepper) stepper.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (e) {
      console.error('ERRO EM showStep:', e);
      alert('ERRO JS (showStep): ' + e.message);
    }
  }

  // Validação restrita ao Step atual
  function validateStep(stepIndex) {
    try {
    const step = document.querySelector(`.checkout-step[data-step="${stepIndex}"]`);
    if (!step) {
      console.warn('Step não encontrado:', stepIndex);
      return true;
    }
    
    // Capturamos todos os campos no step que possuam atributo required
    const requiredFields = step.querySelectorAll('input[required], select[required], textarea[required]');
    let valid = true;
    
    // Removemos os erros antigos
    step.querySelectorAll('.erro').forEach(el => el.classList.remove('erro'));
    
    requiredFields.forEach(f => {
      if (f.type === 'checkbox' || f.type === 'radio') {
        if (!f.checked) {
          if (f.parentElement) f.parentElement.style.color = '#f87171';
          valid = false;
        } else {
          if (f.parentElement) f.parentElement.style.color = '';
        }
      } else {
        if (!f.value || !f.value.trim()) {
          f.classList.add('erro');
          valid = false;
          
          // Remove a borda vermelha ao começar a digitar
          f.addEventListener('input', function removerErro() {
            f.classList.remove('erro');
            f.removeEventListener('input', removerErro);
          }, { once: true });
        }
      }
    });
    
    if (!valid) {
      mostrarErro('Por favor, preencha os campos obrigatórios ou assinale os termos para prosseguir.');
    } else {
      esconderErro();
    }
    return valid;
    } catch (e) {
      console.error('ERRO EM validateStep:', e);
      alert('ERRO JS (validateStep): ' + e.message);
      return false;
    }
  }

  function initStepper() {
    console.log('TA Checkout: Iniciando listeners do Stepper...');
    // Fallback para garantir que listeners existam mesmo sem onclick inline
    document.querySelectorAll('.btn-next').forEach(btn => {
      btn.onclick = function(e) {
        e.preventDefault();
        window.taNextStep(this);
        return false;
      };
    });
    document.querySelectorAll('.btn-prev').forEach(btn => {
      btn.onclick = function(e) {
        e.preventDefault();
        window.taPrevStep(this);
        return false;
      };
    });
  }

  // Registra no carregamento
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStepper);
  } else {
    initStepper();
  }
  setTimeout(initStepper, 500); // Reforço após 0.5s

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
        f.style.display = show ? '' : 'none';
      });
      document.getElementById('campo-metodo').value = metodo;

      const btnConfirmarCartao = document.getElementById('btn-confirmar-cartao');
      const btnConfirmarPix = document.getElementById('btn-gerar-pix');

      if (metodo === 'pix') {
        if (btnConfirmarCartao) btnConfirmarCartao.style.display = 'none';
        if (btnConfirmarPix) btnConfirmarPix.style.display = '';
      } else {
        if (btnConfirmarPix) btnConfirmarPix.style.display = 'none';
        if (btnConfirmarCartao) btnConfirmarCartao.style.display = '';
        if (!mpInstance) setTimeout(initCardForm, 300);
        setTimeout(preencherDadosCartao, 100);
      }
    });
  });

  // =========================================
  // BOTÕES DE PAGAMENTO FINAIS
  // =========================================
  document.getElementById('btn-confirmar-cartao')?.addEventListener('click', () => {
    if (!validateStep('4')) return;
    const form = document.getElementById('form-checkout');
    if (form) form.requestSubmit();
  });

  document.getElementById('btn-gerar-pix')?.addEventListener('click', () => {
    if (!validateStep('4')) return;
    const form = document.getElementById('form-checkout');
    if (form) form.requestSubmit();
  });

  // =========================================
  // COPIAR DADOS DO RESPONSÁVEL → CARTÃO
  // =========================================
  const chkUsarResp = document.getElementById('usar-dados-resp');
  const mpName      = document.getElementById('mp-cardholderName');
  const mpCpf       = document.getElementById('mp-identificationNumber');
  const mpEmail     = document.getElementById('mp-email');

  function preencherDadosCartao() {
    if (!chkUsarResp?.checked) return;
    if (mpName)  mpName.value  = document.getElementById('resp-nome')?.value  || '';
    if (mpCpf)   mpCpf.value   = document.getElementById('resp-cpf')?.value   || '';
    if (mpEmail) mpEmail.value = document.getElementById('resp-email')?.value || '';
  }

  chkUsarResp?.addEventListener('change', () => {
    const wrap = document.getElementById('campos-pagador');
    if (chkUsarResp.checked) {
      preencherDadosCartao();
      if (wrap) wrap.style.display = 'none';
    } else {
      if (mpName)  mpName.value  = '';
      if (mpCpf)   mpCpf.value   = '';
      if (mpEmail) mpEmail.value = '';
      if (wrap) wrap.style.display = '';
    }
  });

  // =========================================
  // MERCADO PAGO – CARDFORM
  // =========================================
  function initCardForm() {
    if (!window.MercadoPago || !publicKey) return;
    mpInstance = new window.MercadoPago(publicKey, { locale: 'pt-BR' });

    const cardForm = mpInstance.cardForm({
      amount: document.getElementById('campo-valor-total')?.value || '0',
      iframe: true,
      form: {
        id:                 'form-cartao',
        cardholderName:     { id: 'mp-cardholderName',        placeholder: 'Nome como no cartão' },
        cardholderEmail:    { id: 'mp-email',                 placeholder: 'email@dominio.com' },
        cardNumber:         { id: 'mp-cardNumber',            placeholder: '•••• •••• •••• ••••' },
        cardExpirationMonth:{ id: 'mp-cardExpirationMonth',   placeholder: 'MM' },
        cardExpirationYear: { id: 'mp-cardExpirationYear',    placeholder: 'YY' },
        securityCode:       { id: 'mp-securityCode',          placeholder: 'CVV' },
        installments:       { id: 'mp-installments' },
        identificationType: { id: 'mp-identificationType' },
        identificationNumber:{ id: 'mp-identificationNumber', placeholder: '000.000.000-00' },
        issuer:             { id: 'mp-issuer' },
      },
      style: {
        theme: 'default',
        customVariables: {
          textPrimaryColor:   '#ffffff',    /* Cor branca para texto digitado */
          textSecondaryColor: '#cccccc',    /* Placeholder */
          inputBackgroundColor: 'transparent',
          formBackgroundColor:  'transparent',
          baseColor: '#00c853',             /* Cor de sucesso/foco do MP */
          fontSize: '15px',                 /* Reduzindo tamanho da fonte */
          fontFamily: 'system-ui, -apple-system, sans-serif'
        },
      },
      callbacks: {
        onFormMounted: err => {
          if (err) return console.error('CardForm:', err);
          const btn = document.getElementById('btn-confirmar-cartao');
          if (btn) btn.disabled = false;
        },
        onSubmit: async e => {
          e.preventDefault();
          const {
            paymentMethodId, issuerId, cardholderEmail,
            amount, token, installments, identificationNumber, identificationType,
          } = cardForm.getCardFormData();

          setLoading(true);
          const fd = new FormData();
          fd.append('action',     'ta_processar_cartao');
          fd.append('nonce',      nonce);
          fd.append('reserva_id', document.getElementById('campo-reserva-id')?.value || '');
          fd.append('token',      token);
          fd.append('pm_id',      paymentMethodId);
          fd.append('issuer_id',  issuerId);
          fd.append('parcelas',   installments);
          fd.append('email',      cardholderEmail);
          fd.append('cpf',        identificationNumber);
          fd.append('valor',      amount);

          try {
            const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
            const json = await res.json();
            if (json.success && json.data.aprovado) {
              window.location.href = json.data.redirect;
            } else {
              mostrarErro(json.data?.message || 'Pagamento não aprovado. Tente novamente.');
            }
          } catch { mostrarErro('Erro de conexão. Verifique sua internet.'); }
          finally  { setLoading(false); }
        },
        onFetching: () => {
          const p = document.getElementById('mp-progress');
          if (p) p.style.display = 'block';
          return () => { if (p) p.style.display = 'none'; };
        },
      },
    });
  }

  // =========================================
  // SUBMIT PRINCIPAL (PIX)
  // =========================================
  document.getElementById('form-checkout')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validateStep('4')) return;

    setLoading(true);
    const fd = new FormData(this);
    fd.append('action', 'ta_criar_reserva_checkout');
    fd.append('nonce',  nonce);

    try {
      const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
      const json = await res.json();

      if (json.success) {
        document.getElementById('campo-reserva-id').value = json.data.reserva_id;
        const metodo = document.getElementById('campo-metodo').value;

        if (metodo === 'pix') {
          renderizarPix(json.data.pix);
          const modal = document.getElementById('pix-modal');
          if (modal) modal.style.display = 'flex';
          iniciarPollingPix(json.data.reserva_id);
        }
      } else {
        mostrarErro(json.data?.message || 'Erro ao processar. Tente novamente.');
      }
    } catch { mostrarErro('Erro de conexão. Verifique sua internet.'); }
    finally  { setLoading(false); }
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
      let seg = 30 * 60;
      const iv = setInterval(() => {
        seg--;
        const m = String(Math.floor(seg / 60)).padStart(2, '0');
        const s = String(seg % 60).padStart(2, '0');
        timer.textContent = `${m}:${s}`;
        if (seg <= 0) { clearInterval(iv); timer.textContent = 'Expirado'; }
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
  // FECHAR MODAL PIX
  // =========================================
  document.getElementById('pix-modal-fechar')?.addEventListener('click', () => {
    const modal = document.getElementById('pix-modal');
    if (modal) modal.style.display = 'none';
  });

  // =========================================
  // POLLING PIX
  // =========================================
  function iniciarPollingPix(reservaId) {
    if (!reservaId) return;
    const iv = setInterval(async () => {
      const fd = new FormData();
      fd.append('action',     'ta_consultar_pix');
      fd.append('nonce',      nonce);
      fd.append('reserva_id', reservaId);
      try {
        const res  = await fetch(ajaxUrl, { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success && json.data.status === 'aprovado') {
          clearInterval(iv);
          window.location.href = json.data.redirect;
        }
      } catch { /* silencioso */ }
    }, 5000);
  }

  // =========================================
  // MÁSCARAS
  // =========================================
  function aplicarMascaras(root = document) {
    root.querySelectorAll('.cpf-mask').forEach(el => {
      el.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/,       '$1.$2')
             .replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
             .replace(/\.(\d{3})(\d)/,     '.$1-$2');
        e.target.value = v;
      });
    });
    root.querySelectorAll('.tel-mask').forEach(el => {
      el.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = v;
      });
    });
  }

  aplicarMascaras();



  // =========================================
  // UTILITÁRIOS
  // =========================================
  function setLoading(on) {
    const spin = document.getElementById('checkout-spinner');
    if (spin) spin.style.display = on ? 'flex' : 'none';

    const btnPix = document.getElementById('btn-gerar-pix');
    const btnC = document.getElementById('btn-confirmar-cartao');
    if (btnPix && btnPix.style.display !== 'none') btnPix.disabled = on;
    if (btnC && btnC.style.display !== 'none') btnC.disabled = on;
  }

  function mostrarErro(msg) {
    const el = document.getElementById('checkout-erro');
    if (!el) { alert(msg); return; }
    el.textContent = msg;
    el.style.display = 'block';
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function esconderErro() {
    const el = document.getElementById('checkout-erro');
    if (el) el.style.display = 'none';
  }

})();
