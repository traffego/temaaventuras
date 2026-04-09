<?php
/**
 * Template Name: Checkout – Reserva e Pagamento
 * Template Post Type: page
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

$atividade_id = intval($_GET['id'] ?? 0);
$atividade = $atividade_id ? get_post($atividade_id) : null;

if (!$atividade || $atividade->post_type !== 'atividade') {
    wp_redirect(home_url('/atividades'));
    exit;
}

$preco_base = (float) (get_post_meta($atividade_id, '_atividade_preco', true) ?: 0);
$nivel = get_post_meta($atividade_id, '_atividade_nivel', true) ?: 'facil';
$duracao = get_post_meta($atividade_id, '_atividade_duracao', true);
$data_atv = get_post_meta($atividade_id, '_atividade_data', true);
$hora_atv = get_post_meta($atividade_id, '_atividade_horario', true);
$cfg = tema_aventuras_payment_config();
$mp_pubkey = $cfg['sandbox'] ? $cfg['pubkey_sandbox'] : $cfg['pubkey_producao'];
$parcelas = $cfg['parcelas_max'];

wp_enqueue_style('ta-checkout-css', TEMA_AVENTURAS_URI . '/assets/css/checkout.css', [], TEMA_AVENTURAS_VERSION);
wp_enqueue_script('mercadopago-sdk', 'https://sdk.mercadopago.com/js/v2', [], null, true);
wp_enqueue_script('ta-checkout-js', TEMA_AVENTURAS_URI . '/assets/js/checkout.js', ['mercadopago-sdk'], TEMA_AVENTURAS_VERSION, true);
wp_localize_script('ta-checkout-js', 'taCheckoutConfig', [
    'publicKey' => $mp_pubkey,
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('ta_checkout_nonce'),
    'precoPorPessoa' => $preco_base,
    'parcelas_max' => $parcelas,
]);

get_header();
?>

<main id="conteudo-principal" role="main">

    <?php
    $at_img_id = (int) get_post_meta($atividade_id, '_atividade_imagem', true);
    $img_url = $at_img_id ? wp_get_attachment_image_url($at_img_id, 'full') : get_the_post_thumbnail_url($atividade_id, 'full');
    if (!$img_url) {
        if (ta_get('hero_imagem')) {
            $img_url = ta_get('hero_imagem');
        }
    }
    ?>

    <!-- HERO DA ATIVIDADE -->
    <div class="checkout-hero"
        style="position:relative; width:100%; height:40vh; min-height:280px; background: url('<?php echo esc_url($img_url); ?>') center/cover no-repeat; display:flex; align-items:flex-end; padding-bottom:var(--espaco-xl);">
        <div
            style="position:absolute; inset:0; background: linear-gradient(to top, var(--fundo-base) 0%, rgba(10,17,13,0.2) 60%, rgba(10,17,13,0.7) 100%); z-index:1;">
        </div>
        <div class="container" style="position:relative; z-index:2;">
            <span
                style="display:inline-block; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.15em; color:var(--cor-secundaria); font-weight:700; margin-bottom:var(--espaco-sm);">🎫
                Checkout Seguro</span>
            <h1
                style="font-size: clamp(2.5rem, 5vw, 4rem); text-shadow: 0 4px 15px rgba(0,0,0,0.8); margin-bottom: 0; line-height: 1;">
                <?php echo esc_html(get_the_title($atividade_id)); ?>
            </h1>
            <div style="display:flex; gap:16px; margin-top:12px; color:#ddd; font-size:0.9rem;">
                <?php if ($data_atv): ?><span>📅
                        <?php echo date_i18n('d/m/Y', strtotime($data_atv)); ?></span><?php endif; ?>
                <?php if ($hora_atv): ?><span>⏰ <?php echo esc_html($hora_atv); ?></span><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Spinner -->
    <div id="checkout-spinner" aria-live="polite" aria-label="Processando pagamento">
        <div class="spinner-ring"></div>
        <p style="color:var(--texto-primario)">Processando...</p>
    </div>

    <!-- ERRO GLOBAL -->
    <div class="checkout-erro" id="checkout-erro" role="alert"></div>

    <section class="checkout-page">
        <div class="container">
            <div class="checkout-grid">

                <!-- ==================== COLUNA PRINCIPAL ==================== -->
                <div class="checkout-main">
                    <form id="form-checkout" method="post" novalidate>
                        <?php wp_nonce_field('ta_checkout_nonce', '_ta_nonce'); ?>
                        <input type="hidden" name="atividade_id" value="<?php echo $atividade_id; ?>">
                        <input type="hidden" id="campo-metodo" name="metodo" value="pix">
                        <input type="hidden" id="campo-reserva-id" name="reserva_id" value="">
                        <input type="hidden" id="campo-valor-total" name="valor_total" value="">
                        <input type="hidden" id="campo-qtd-inscritos" name="qtd_inscritos" value="1">

                        <!-- SEÇÃO 1: DADOS DO RESPONSÁVEL -->
                        <div class="checkout-section">
                            <h2 class="checkout-section__titulo">👤 Dados do Responsável</h2>
                            <div class="grid grid--2">
                                <div class="form-grupo">
                                    <label for="resp-nome">Nome completo *</label>
                                    <input type="text" id="resp-nome" name="resp_nome" required
                                        placeholder="Seu nome completo">
                                </div>
                                <div class="form-grupo">
                                    <label for="resp-email">E-mail *</label>
                                    <input type="email" id="resp-email" name="resp_email" required
                                        placeholder="seu@email.com">
                                </div>
                                <div class="form-grupo">
                                    <label for="resp-tel">Telefone / WhatsApp *</label>
                                    <input type="text" id="resp-tel" name="resp_telefone" required
                                        placeholder="(11) 99999-9999" class="tel-mask">
                                </div>
                                <div class="form-grupo">
                                    <label for="resp-cpf">CPF *</label>
                                    <input type="text" id="resp-cpf" name="resp_cpf" required
                                        placeholder="000.000.000-00" class="cpf-mask">
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 2: DEMAIS INSCRITOS (OPCIONAL) -->
                        <div class="checkout-section">
                            <h2 class="checkout-section__titulo">👥 Acompanhantes (Opcional)</h2>
                            <p class="checkout-section__desc">Você (Responsável) já conta como o 1º inscrito. Se for
                                acompanhado, adicione as demais pessoas abaixo:</p>

                            <div id="inscritos-wrap">
                                <!-- Acompanhantes dinâmicos via JS -->
                            </div>

                            <button type="button" id="add-inscrito" class="btn btn--ghost btn--pequeno"
                                style="margin-top:var(--espaco-md);">
                                + Adicionar Acompanhante
                            </button>
                        </div>

                        <!-- SEÇÃO 3: PAGAMENTO -->
                        <div class="checkout-section">
                            <h2 class="checkout-section__titulo">💳 Forma de Pagamento</h2>

                            <!-- Seletor de método -->
                            <div class="metodo-grid" role="radiogroup" aria-label="Método de pagamento">
                                <div class="metodo-card selecionado" data-metodo="pix" role="radio" aria-checked="true"
                                    tabindex="0">
                                    <span class="metodo-card__icon">🏦</span>
                                    <div class="metodo-card__nome">PIX</div>
                                    <div class="metodo-card__desc">Aprovação imediata</div>
                                </div>
                                <div class="metodo-card" data-metodo="credit_card" role="radio" aria-checked="false"
                                    tabindex="0">
                                    <span class="metodo-card__icon">💳</span>
                                    <div class="metodo-card__nome">Cartão</div>
                                    <div class="metodo-card__desc">Até <?php echo $parcelas; ?>x</div>
                                </div>
                            </div>

                            <!-- PIX info -->
                            <div data-metodo-form="pix" class="ativo">
                                <div class="pix-container">
                                    <p style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);">Após
                                        confirmar, um QR Code PIX será gerado. Você terá <strong>30 minutos</strong>
                                        para pagar.</p>
                                    <div class="pix-display-wrap">
                                        <div id="pix-display" style="display:none;">
                                            <p style="color:var(--cor-primaria);font-weight:bold;">✅ QR Code gerado!
                                                Escaneie para pagar:</p>
                                            <div class="pix-qrcode-wrap">
                                                <img id="pix-qrcode-img" src="" alt="QR Code PIX" style="display:none;">
                                            </div>
                                            <p>Expira em: <span class="pix-timer" id="pix-timer">30:00</span></p>
                                            <div class="pix-copia-cola-wrap">
                                                <input type="text" id="pix-copia-cola" readonly
                                                    placeholder="Código PIX aparecerá aqui">
                                                <button type="button" id="btn-copiar-pix"
                                                    class="btn btn--secundario btn--pequeno">📋 Copiar</button>
                                            </div>
                                            <p
                                                style="font-size:0.75rem;color:var(--texto-muted);text-align:center;margin-top:var(--espaco-sm);">
                                                Aguardando confirmação automaticamente...
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cartão -->
                            <div data-metodo-form="credit_card" style="display:none;">
                                <div id="form-cartao">
                                    <div class="mp-card-form">
                                        <div class="grid grid--2">
                                            <!-- Campos IFRAME (sensíveis – o SDK injeta iframes aqui) -->
                                            <div class="form-grupo" style="grid-column:1/-1;">
                                                <label>Número do Cartão *</label>
                                                <div class="mp-field-wrapper" id="mp-cardNumber"></div>
                                            </div>
                                            <div class="form-grupo">
                                                <label>Validade *</label>
                                                <div style="display:flex; gap:8px;">
                                                    <div class="mp-field-wrapper" id="mp-cardExpirationMonth" style="flex:1;"></div>
                                                    <span style="color:var(--texto-muted); display:flex; align-items:center; font-size:1.2rem;">/</span>
                                                    <div class="mp-field-wrapper" id="mp-cardExpirationYear" style="flex:1;"></div>
                                                </div>
                                            </div>
                                            <div class="form-grupo">
                                                <label>CVV *</label>
                                                <div class="mp-field-wrapper" id="mp-securityCode"></div>
                                            </div>

                                            <!-- Parcelas -->
                                            <div class="form-grupo" style="grid-column:1/-1;">
                                                <label>Parcelas</label>
                                                <select id="mp-installments" style="height:44px;"></select>
                                            </div>

                                            <!-- Usar dados do responsável -->
                                            <div class="form-grupo" style="grid-column:1/-1;">
                                                <label class="checkbox-inline"
                                                    style="display:flex; align-items:center; gap:8px; cursor:pointer; text-transform:none; letter-spacing:0;">
                                                    <input type="checkbox" id="usar-dados-resp" checked
                                                        style="width:auto; min-height:auto;">
                                                    Usar dados do responsável para o pagamento
                                                </label>
                                            </div>

                                            <!-- Campos NATIVOS (ocultos quando usar dados do responsável) -->
                                            <div id="campos-pagador" style="display:none; grid-column:1/-1;">
                                                <div class="grid grid--2">
                                                    <div class="form-grupo" style="grid-column:1/-1;">
                                                        <label>Nome no Cartão *</label>
                                                        <input type="text" id="mp-cardholderName"
                                                            placeholder="Nome como no cartão">
                                                    </div>
                                                    <div class="form-grupo">
                                                        <label>Tipo de Documento</label>
                                                        <select id="mp-identificationType"></select>
                                                    </div>
                                                    <div class="form-grupo">
                                                        <label>CPF *</label>
                                                        <input type="text" id="mp-identificationNumber"
                                                            placeholder="000.000.000-00">
                                                    </div>
                                                    <div class="form-grupo" style="grid-column:1/-1;">
                                                        <label>E-mail *</label>
                                                        <input type="email" id="mp-email"
                                                            placeholder="email@dominio.com">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-grupo" style="display:none;">
                                                <select id="mp-issuer"></select>
                                            </div>
                                        </div>
                                        <div id="mp-progress"
                                            style="display:none;color:var(--texto-muted);font-size:var(--tamanho-pequeno);">
                                            ⏳ Verificando cartão...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>

<!-- BOTÃO FLUTUANTE (fora de main e footer para garantir position:fixed) -->
<div class="checkout-submit-wrapper" id="checkout-submit-wrapper"
    style=" position: fixed; bottom: 10px; width: 90%; left: 20px; ">
    <button type="button" id="btn-finalizar" class="btn btn--primario btn--grande checkout-submit" disabled>
        🔒 Pagar <span id="btn-total-display">R$ 0,00</span>
    </button>
</div>