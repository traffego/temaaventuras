<?php
/**
 * Template Name: Checkout – Reserva e Pagamento
 * Template Post Type: page
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

$atividade_id = intval($_GET['id'] ?? 0);
$atividade    = $atividade_id ? get_post($atividade_id) : null;

if (!$atividade || $atividade->post_type !== 'atividade') {
    wp_redirect(home_url('/atividades'));
    exit;
}

// Dados da atividade
$preco_base = (float)(get_post_meta($atividade_id, '_atividade_preco',   true) ?: 0);
$nivel      =         get_post_meta($atividade_id, '_atividade_nivel',   true) ?: 'facil';
$duracao    =         get_post_meta($atividade_id, '_atividade_duracao', true);
$data_atv   =         get_post_meta($atividade_id, '_atividade_data',    true);
$hora_atv   =         get_post_meta($atividade_id, '_atividade_horario', true);
$vagas      = (int)   get_post_meta($atividade_id, '_atividade_vagas',   true);
$obs        =         get_post_meta($atividade_id, '_atividade_obs',     true);
$pessoas_min= (int)   get_post_meta($atividade_id, '_atividade_pessoas', true) ?: 1;

// Imagem
$at_img_id = (int) get_post_meta($atividade_id, '_atividade_imagem', true);
$img_url   = $at_img_id
    ? wp_get_attachment_image_url($at_img_id, 'full')
    : get_the_post_thumbnail_url($atividade_id, 'full');
if (!$img_url) $img_url = ta_get('hero_imagem', '');

// Descrição
$descricao = get_the_excerpt($atividade_id) ?: wp_trim_words(
    wp_strip_all_tags(get_the_content(null, false, $atividade_id)), 25, '…'
);

// Vagas restantes (descontar reservas aprovadas)
$reservas_aprovadas = get_posts([
    'post_type'   => 'reserva',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        ['key' => '_reserva_atividade_id', 'value' => $atividade_id],
        ['key' => '_reserva_status',       'value' => 'aprovado'],
    ],
]);
$inscritos_confirmados = 0;
foreach ($reservas_aprovadas as $r) {
    $ins = get_post_meta($r->ID, '_reserva_inscritos', true) ?: [];
    $inscritos_confirmados += count($ins);
}
$vagas_restantes = $vagas > 0 ? max(0, $vagas - $inscritos_confirmados) : null;

// Config pagamento
$cfg       = tema_aventuras_payment_config();
$mp_pubkey = $cfg['sandbox'] ? $cfg['pubkey_sandbox'] : $cfg['pubkey_producao'];
$parcelas  = $cfg['parcelas_max'];

wp_enqueue_style('ta-checkout-css',  TEMA_AVENTURAS_URI . '/assets/css/checkout.css', [], TEMA_AVENTURAS_VERSION);
wp_enqueue_script('mercadopago-sdk', 'https://sdk.mercadopago.com/js/v2', [], null, true);
wp_enqueue_script('ta-checkout-js',  TEMA_AVENTURAS_URI . '/assets/js/checkout.js', ['mercadopago-sdk'], TEMA_AVENTURAS_VERSION, true);
wp_localize_script('ta-checkout-js', 'taCheckoutConfig', [
    'publicKey'     => $mp_pubkey,
    'ajaxUrl'       => admin_url('admin-ajax.php'),
    'nonce'         => wp_create_nonce('ta_checkout_nonce'),
    'precoPorPessoa'=> $preco_base,
    'parcelas_max'  => $parcelas,
]);

get_header();
?>

<main id="conteudo-principal" role="main">

    <!-- ===== HERO ===== -->
    <?php if ($img_url): ?>
    <div class="checkout-hero" style="position:relative;width:100%;height:38vh;min-height:260px;background:url('<?php echo esc_url($img_url); ?>') center/cover no-repeat;display:flex;align-items:flex-end;padding-bottom:var(--espaco-xl);">
        <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--fundo-base) 0%,rgba(10,17,13,.15) 55%,rgba(10,17,13,.75) 100%);z-index:1;"></div>
        <div class="container" style="position:relative;z-index:2;">
            <span style="display:inline-block;font-size:.75rem;text-transform:uppercase;letter-spacing:.15em;color:var(--cor-secundaria);font-weight:700;margin-bottom:var(--espaco-xs);">🎫 Checkout Seguro</span>
            <h1 style="font-size:clamp(2rem,5vw,3.5rem);text-shadow:0 4px 15px rgba(0,0,0,.8);margin-bottom:0;line-height:1.1;">
                <?php echo esc_html(get_the_title($atividade_id)); ?>
            </h1>
            <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:10px;color:#ddd;font-size:.85rem;">
                <?php if ($data_atv): ?><span>📅 <?php echo date_i18n('d/m/Y', strtotime($data_atv)); ?></span><?php endif; ?>
                <?php if ($hora_atv): ?><span>⏰ <?php echo esc_html($hora_atv); ?></span><?php endif; ?>
                <?php if ($duracao):  ?><span>⌛ <?php echo esc_html($duracao); ?></span><?php endif; ?>
                <?php if ($vagas_restantes !== null): ?>
                    <span style="color:<?php echo $vagas_restantes <= 3 ? '#f59e0b' : 'var(--cor-primaria)'; ?>;">
                        🎟️ <?php echo $vagas_restantes; ?> vaga<?php echo $vagas_restantes !== 1 ? 's' : ''; ?> disponível<?php echo $vagas_restantes !== 1 ? 'eis' : ''; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Spinner -->
    <div id="checkout-spinner" aria-live="polite" aria-label="Processando">
        <div class="spinner-ring"></div>
        <p style="color:var(--texto-primario)">Processando...</p>
    </div>

    <!-- Erro global -->
    <div class="checkout-erro" id="checkout-erro" role="alert"></div>

    <section class="checkout-page">
        <div class="container">
            <div class="checkout-grid">
                <div class="checkout-main">
                    <form id="form-checkout" method="post" novalidate>
                        <?php wp_nonce_field('ta_checkout_nonce', '_ta_nonce'); ?>
                        <input type="hidden" name="atividade_id"   value="<?php echo $atividade_id; ?>">
                        <input type="hidden" id="campo-metodo"     name="metodo"       value="">
                        <input type="hidden" id="campo-reserva-id" name="reserva_id"   value="">
                        <input type="hidden" id="campo-valor-total"name="valor_total"  value="">
                        <input type="hidden" id="campo-qtd-inscritos" name="qtd_inscritos" value="1">

                        <!-- ── RESUMO DA ATIVIDADE ── -->
                        <div class="checkout-section co-resumo-atividade">
                            <div style="display:flex;gap:var(--espaco-md);align-items:flex-start;flex-wrap:wrap;">
                                <div style="flex:1;min-width:200px;">
                                    <h2 class="checkout-section__titulo" style="margin-bottom:var(--espaco-xs);">
                                        <?php echo esc_html(get_the_title($atividade_id)); ?>
                                    </h2>
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:var(--espaco-sm);">
                                        <?php echo ta_nivel_badge($nivel); ?>
                                        <?php if ($duracao): ?><span class="badge badge--verde">⌛ <?php echo esc_html($duracao); ?></span><?php endif; ?>
                                    </div>
                                    <?php if ($descricao): ?>
                                        <p style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);line-height:1.6;margin-bottom:var(--espaco-sm);"><?php echo esc_html($descricao); ?></p>
                                    <?php endif; ?>
                                    <div style="display:flex;flex-wrap:wrap;gap:var(--espaco-md);font-size:.8rem;">
                                        <?php if ($data_atv): ?>
                                            <div style="color:var(--texto-muted);">📅 <strong style="color:var(--texto-primario);"><?php echo date_i18n('d \d\e F \d\e Y', strtotime($data_atv)); ?></strong></div>
                                        <?php endif; ?>
                                        <?php if ($hora_atv): ?>
                                            <div style="color:var(--texto-muted);">⏰ <strong style="color:var(--texto-primario);"><?php echo esc_html($hora_atv); ?></strong></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($obs): ?>
                                        <div style="margin-top:var(--espaco-sm);padding:var(--espaco-sm);background:var(--fundo-elevado);border-radius:var(--raio-md);border-left:3px solid var(--cor-secundaria);font-size:.8rem;color:var(--texto-secundario);">
                                            📝 <?php echo esc_html($obs); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;min-width:120px;">
                                    <div style="font-size:.7rem;color:var(--texto-muted);text-align:right;">por pessoa</div>
                                    <div style="font-family:var(--fonte-titulo);font-size:2rem;color:var(--cor-secundaria);line-height:1;"><?php echo ta_preco($preco_base); ?></div>
                                    <?php if ($vagas_restantes !== null): ?>
                                        <div style="font-size:.72rem;color:<?php echo $vagas_restantes <= 3 ? '#f59e0b' : 'var(--texto-muted)'; ?>;">
                                            <?php if ($vagas_restantes === 0): ?>
                                                ⛔ Esgotado
                                            <?php else: ?>
                                                🎟️ <?php echo $vagas_restantes; ?> vaga<?php echo $vagas_restantes !== 1 ? 's' : ''; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- STEPPER HEADER -->
                        <div class="checkout-stepper">
                            <div class="step-indicator ativado" data-target="1"><span>1</span> <span class="step-label">Responsável</span></div>
                            <div class="step-indicator" data-target="2"><span>2</span> <span class="step-label">Participantes</span></div>
                            <div class="step-indicator" data-target="3"><span>3</span> <span class="step-label">Termos</span></div>
                            <div class="step-indicator" data-target="4"><span>4</span> <span class="step-label">Pagamento</span></div>
                        </div>

                        <!-- ── PASSO 1: RESPONSÁVEL ── -->
                        <div class="checkout-step ativo" data-step="1">
                            <div class="checkout-section">
                                <h2 class="checkout-section__titulo">👤 Dados do Responsável</h2>
                                <p class="checkout-section__desc">Responsável pela reserva — já conta como 1º participante.</p>
                                <div class="grid grid--2">
                                    <div class="form-grupo" style="grid-column:1/-1;">
                                        <label for="resp-nome">Nome completo *</label>
                                        <input type="text" id="resp-nome" name="resp_nome" required placeholder="Seu nome completo">
                                    </div>
                                    <div class="form-grupo">
                                        <label for="resp-email">E-mail *</label>
                                        <input type="email" id="resp-email" name="resp_email" required placeholder="seu@email.com">
                                    </div>
                                    <div class="form-grupo">
                                        <label for="resp-tel">WhatsApp *</label>
                                        <input type="text" id="resp-tel" name="resp_telefone" required placeholder="(11) 99999-9999" class="tel-mask">
                                    </div>
                                    <div class="form-grupo">
                                        <label for="resp-cpf">CPF *</label>
                                        <input type="text" id="resp-cpf" name="resp_cpf" required placeholder="000.000.000-00" class="cpf-mask">
                                    </div>
                                </div>
                            </div>
                            <div class="step-nav">
                                <div></div> <!-- Espaçador -->
                                <button type="button" class="btn btn--primario btn-next" data-next="2" onclick="if(window.taNextStep) window.taNextStep(this); return false;">Próximo: Participantes →</button>
                            </div>
                        </div>

                        <!-- ── PASSO 2: ACOMPANHANTES ── -->
                        <div class="checkout-step" data-step="2" style="display:none;">
                            <div class="checkout-section">
                                <h2 class="checkout-section__titulo">👥 Acompanhantes <span style="font-size:.75rem;font-weight:400;color:var(--texto-muted);">(opcional)</span></h2>
                                <p class="checkout-section__desc">Preencha apenas se houver mais pessoas além do responsável.</p>
                                <div id="inscritos-wrap"></div>
                                <button type="button" id="add-inscrito" class="btn btn--ghost btn--pequeno" style="margin-top:var(--espaco-md);">
                                    + Adicionar Acompanhante
                                </button>
                            </div>
                            <div class="step-nav">
                                <button type="button" class="btn btn--secundario btn-prev" data-prev="1" onclick="if(window.taPrevStep) window.taPrevStep(this); return false;">← Voltar</button>
                                <button type="button" class="btn btn--primario btn-next" data-next="3" onclick="if(window.taNextStep) window.taNextStep(this); return false;">Próximo: Termos →</button>
                            </div>
                        </div>

                        <!-- ── PASSO 3: TERMOS ── -->
                        <div class="checkout-step" data-step="3" style="display:none;">
                            <div class="checkout-section">
                                <h2 class="checkout-section__titulo">✅ Termos e Políticas</h2>
                                <p class="checkout-section__desc">Por favor, confirme que está ciente das regras da atividade.</p>
                                <div style="background:var(--fundo-base); padding:var(--espaco-md); border-radius:var(--raio-md); border:1px solid var(--borda-glass); margin-bottom:var(--espaco-md);">
                                    <label class="checkbox-inline" style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;text-transform:none;letter-spacing:0;color:var(--texto-primario);">
                                        <input type="checkbox" id="aceite-termos" required style="width:24px;height:24px;margin-top:2px;accent-color:var(--cor-primaria);">
                                        <span>Confirmo que li e aceito as condições de reserva, regras de segurança e a política de cancelamento para esta atividade.</span>
                                    </label>
                                </div>
                            </div>
                            <div class="step-nav">
                                <button type="button" class="btn btn--secundario btn-prev" data-prev="2" onclick="if(window.taPrevStep) window.taPrevStep(this); return false;">← Voltar</button>
                                <button type="button" class="btn btn--primario btn-next" data-next="4" onclick="if(window.taNextStep) window.taNextStep(this); return false;">Revisar e Pagar →</button>
                            </div>
                        </div>

                        <!-- ── PASSO 4: PAGAMENTO ── -->
                        <div class="checkout-step" data-step="4" style="display:none;">
                            <!-- Resumo Financeiro movido para o último passo -->
                            <div class="checkout-section co-resumo-financeiro">
                                <h2 class="checkout-section__titulo">💰 Resumo Final</h2>
                                <div style="display:flex;flex-direction:column;gap:var(--espaco-xs);">
                                    <div class="resumo-linha" style="display:flex;justify-content:space-between;font-size:.9rem;color:var(--texto-secundario);">
                                        <span>Preço por pessoa</span>
                                        <span><?php echo ta_preco($preco_base); ?></span>
                                    </div>
                                    <div class="resumo-linha" style="display:flex;justify-content:space-between;font-size:.9rem;color:var(--texto-secundario);">
                                        <span>Participantes</span>
                                        <span id="resumo-qtd">1</span>
                                    </div>
                                    <div style="height:1px;background:var(--borda-glass);margin:var(--espaco-xs) 0;"></div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                        <span style="font-size:1rem;font-weight:600;color:var(--texto-primario);">Total</span>
                                        <span id="resumo-total" style="font-family:var(--fonte-titulo);font-size:1.8rem;color:var(--cor-secundaria);"><?php echo ta_preco($preco_base); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Seleção de Pagamento -->
                            <div class="checkout-section" id="secao-pagamento">
                                <h2 class="checkout-section__titulo">💳 Como quer pagar?</h2>

                                <div class="metodo-grid" role="radiogroup" aria-label="Método de pagamento">
                                    <div class="metodo-card" data-metodo="pix" role="radio" aria-checked="false" tabindex="0">
                                        <span class="metodo-card__icon">🏦</span>
                                        <div class="metodo-card__nome">PIX</div>
                                        <div class="metodo-card__desc">Aprovação imediata</div>
                                    </div>
                                    <div class="metodo-card" data-metodo="credit_card" role="radio" aria-checked="false" tabindex="0">
                                        <span class="metodo-card__icon">💳</span>
                                        <div class="metodo-card__nome">Cartão</div>
                                        <div class="metodo-card__desc">Até <?php echo $parcelas; ?>x</div>
                                    </div>
                                </div>

                                <!-- PIX: info -->
                                <div data-metodo-form="pix" style="display:none; text-align:center;">
                                    <p style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);margin-top:calc(-1 * var(--espaco-sm));">
                                        Ao confirmar, o QR Code será gerado para o pagamento.
                                    </p>
                                    <button type="button" id="btn-gerar-pix" class="btn btn--primario btn--grande checkout-submit" style="margin-top:var(--espaco-md); width:100%;">
                                        🏦 Pagar com PIX
                                    </button>
                                </div>

                                <!-- Cartão -->
                                <div data-metodo-form="credit_card" style="display:none;">
                                    <div id="form-cartao">
                                        <div class="mp-card-form">
                                            <div class="grid grid--2">
                                                <div class="form-grupo" style="grid-column:1/-1; margin-bottom:12px;">
                                                    <label style="font-size:0.8rem; font-weight:600; margin-bottom:6px; display:block; color:var(--texto-secundario); letter-spacing:0.5px;">NÚMERO DO CARTÃO *</label>
                                                    <div class="mp-field-wrapper" id="mp-cardNumber" style="background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; height: 48px; display: flex; align-items: center; width: 100%; box-sizing: border-box;"></div>
                                                </div>
                                                <div class="form-grupo" style="margin-bottom:12px;">
                                                    <label style="font-size:0.8rem; font-weight:600; margin-bottom:6px; display:block; color:var(--texto-secundario); letter-spacing:0.5px;">VALIDADE *</label>
                                                    <div style="display:flex;gap:8px;align-items:center;">
                                                        <div class="mp-field-wrapper" id="mp-cardExpirationMonth" style="flex:1; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 12px; height: 48px; display: flex; align-items: center; box-sizing: border-box;"></div>
                                                        <span style="color:var(--texto-muted); font-size:1.2rem; line-height:1;">/</span>
                                                        <div class="mp-field-wrapper" id="mp-cardExpirationYear" style="flex:1; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 12px; height: 48px; display: flex; align-items: center; box-sizing: border-box;"></div>
                                                    </div>
                                                </div>
                                                <div class="form-grupo" style="margin-bottom:12px;">
                                                    <label style="font-size:0.8rem; font-weight:600; margin-bottom:6px; display:block; color:var(--texto-secundario); letter-spacing:0.5px;">CVV *</label>
                                                    <div class="mp-field-wrapper" id="mp-securityCode" style="background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; height: 48px; display: flex; align-items: center; width: 100%; box-sizing: border-box;"></div>
                                                </div>
                                                <div class="form-grupo" style="grid-column:1/-1; margin-bottom:12px;">
                                                    <label style="font-size:0.8rem; font-weight:600; margin-bottom:6px; display:block; color:var(--texto-secundario); letter-spacing:0.5px;">PARCELAS *</label>
                                                    <select id="mp-installments" style="height:48px; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; width: 100%; color:var(--texto-primario); font-size: 15px; box-sizing: border-box;"></select>
                                                </div>
                                                <div class="form-grupo" style="grid-column:1/-1; margin: 16px 0;">
                                                    <label class="checkbox-inline" style="display:flex;align-items:center;gap:10px;cursor:pointer;text-transform:none;letter-spacing:0; font-size: 0.9rem; color:var(--texto-primario); background: var(--fundo-elevado); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--borda-glass);">
                                                        <input type="checkbox" id="usar-dados-resp" checked style="width:20px;height:20px;accent-color:var(--cor-primaria);">
                                                        Usar dados do responsável para o pagamento
                                                    </label>
                                                </div>
                                                <div id="campos-pagador" style="display:none;grid-column:1/-1; background: var(--fundo-elevado); padding: 16px; border-radius: 12px; border: 1px dashed var(--borda-glass);">
                                                    <div class="grid grid--2" style="gap: 12px;">
                                                        <div class="form-grupo" style="grid-column:1/-1;">
                                                            <label style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block; color:var(--texto-secundario);">NOME NO CARTÃO *</label>
                                                            <input type="text" id="mp-cardholderName" placeholder="Nome como no cartão" style="height:48px; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; width: 100%; box-sizing: border-box;">
                                                        </div>
                                                        <div class="form-grupo">
                                                            <label style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block; color:var(--texto-secundario);">DOCUMENTO</label>
                                                            <select id="mp-identificationType" style="height:48px; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; width: 100%; box-sizing: border-box;"></select>
                                                        </div>
                                                        <div class="form-grupo">
                                                            <label style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block; color:var(--texto-secundario);">CPF *</label>
                                                            <input type="text" id="mp-identificationNumber" placeholder="000.000.000-00" style="height:48px; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; width: 100%; box-sizing: border-box;">
                                                        </div>
                                                        <div class="form-grupo" style="grid-column:1/-1;">
                                                            <label style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block; color:var(--texto-secundario);">E-MAIL *</label>
                                                            <input type="email" id="mp-email" placeholder="email@dominio.com" style="height:48px; background:var(--fundo-base); border: 1px solid var(--borda-glass); border-radius: 8px; padding: 0 16px; width: 100%; box-sizing: border-box;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-grupo" style="display:none;">
                                                    <select id="mp-issuer"></select>
                                                </div>
                                            </div>
                                            <div id="mp-progress" style="display:none;color:var(--texto-muted);font-size:var(--tamanho-pequeno);">⏳ Verificando cartão...</div>
                                        </div>
                                    </div>
                                    <button type="button" id="btn-confirmar-cartao" class="btn btn--primario btn--grande checkout-submit" style="margin-top:var(--espaco-md); width:100%; display:none;">
                                        💳 Confirmar Pagamento
                                    </button>
                                </div>
                            </div>
                            <div class="step-nav">
                                <button type="button" class="btn btn--secundario btn-prev" data-prev="3" onclick="if(window.taPrevStep) window.taPrevStep(this); return false;">← Voltar</button>
                                <div></div> <!-- Espaçador -->
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>

<!-- BOTÃO FLUTUANTE REMOVIDO -->

<!-- MODAL PIX -->
<div id="pix-modal" style="display:none;position:fixed;inset:0;z-index:999999;background:rgba(0,0,0,.88);backdrop-filter:blur(12px);align-items:center;justify-content:center;">
    <div style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-2xl);max-width:420px;width:90%;text-align:center;position:relative;">
        <button type="button" id="pix-modal-fechar" style="position:absolute;top:12px;right:16px;background:none;border:none;color:var(--texto-muted);font-size:1.5rem;cursor:pointer;" aria-label="Fechar">✕</button>
        <p style="color:var(--cor-primaria);font-weight:700;font-size:1.1rem;margin-bottom:var(--espaco-md);">✅ QR Code PIX gerado!</p>
        <div class="pix-qrcode-wrap" style="margin-bottom:var(--espaco-md);">
            <img id="pix-qrcode-img" src="" alt="QR Code PIX" style="display:none;">
        </div>
        <p style="color:var(--texto-primario);margin-bottom:var(--espaco-xs);">Expira em: <span id="pix-timer" style="font-family:var(--fonte-titulo);font-size:1.8rem;color:var(--cor-secundaria);">30:00</span></p>
        <div style="display:flex;gap:var(--espaco-sm);margin:var(--espaco-md) auto;max-width:350px;">
            <input type="text" id="pix-copia-cola" readonly placeholder="Código PIX" style="flex:1;background:var(--fundo-elevado);border:1px solid var(--borda-glass);border-radius:var(--raio-md);padding:8px 12px;color:var(--texto-secundario);font-size:.75rem;font-family:monospace;">
            <button type="button" id="btn-copiar-pix" class="btn btn--secundario btn--pequeno">📋 Copiar</button>
        </div>
        <p style="font-size:.75rem;color:var(--texto-muted);">Aguardando confirmação automaticamente...</p>
    </div>
</div>