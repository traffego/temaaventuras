<?php
/**
 * Template Name: Confirmação de Reserva
 * Template Post Type: page
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

$reserva_id = intval($_GET['reserva'] ?? 0);
$token      = sanitize_text_field($_GET['token'] ?? '');

// Validar token de segurança
if (!$reserva_id || $token !== ta_token_reserva($reserva_id)) {
    wp_safe_redirect(home_url()); exit;
}

$reserva = get_post($reserva_id);
if (!$reserva || $reserva->post_type !== 'reserva') {
    wp_safe_redirect(home_url()); exit;
}

$m           = fn($k) => get_post_meta($reserva_id, $k, true);
$status      = $m('_reserva_status');
$nome        = $m('_reserva_cliente_nome');
$email       = $m('_reserva_cliente_email');
$atividade   = get_the_title($m('_reserva_atividade_id'));
$data_atv    = date('d/m/Y', strtotime($m('_reserva_data_atividade')));
$hora_atv    = $m('_reserva_hora_atividade');
$inscritos   = $m('_reserva_inscritos') ?: [];
$valor       = number_format((float)$m('_reserva_valor_total'), 2, ',', '.');
$metodo      = strtoupper($m('_reserva_metodo') ?: '');
$wa_link     = ta_whatsapp_link('Olá! Tenho dúvidas sobre minha reserva #' . $reserva_id);

get_header();
?>

<main id="conteudo-principal" role="main" style="padding-top:var(--altura-nav);">
<section class="section">
<div class="container--estreito">

    <?php if ($status === 'aprovado'): ?>
    <!-- APROVADO -->
    <div class="texto-centro" style="margin-bottom:var(--espaco-3xl);">
        <div style="font-size:5rem;animation:float 4s ease-in-out infinite;" aria-hidden="true">🎉</div>
        <h1 style="font-size:clamp(2rem,5vw,3.5rem);color:var(--cor-primaria);margin-top:var(--espaco-md);">
            Reserva Confirmada!
        </h1>
        <p style="color:var(--texto-muted);">Parabéns, <?php echo esc_html($nome); ?>! Sua aventura está garantida.</p>
    </div>

    <?php else: ?>
    <!-- PENDENTE / OUTRO STATUS -->
    <div class="texto-centro" style="margin-bottom:var(--espaco-3xl);">
        <div style="font-size:5rem;" aria-hidden="true">⏳</div>
        <h1 style="font-size:clamp(2rem,5vw,3rem);color:var(--cor-secundaria);margin-top:var(--espaco-md);">
            Aguardando Confirmação
        </h1>
        <p style="color:var(--texto-muted);">Status: <strong><?php echo strtoupper($status); ?></strong></p>
        <p style="color:var(--texto-muted);">Assim que o pagamento for confirmado, você receberá um e-mail em <strong><?php echo esc_html($email); ?></strong></p>
    </div>
    <?php endif; ?>

    <!-- DETALHES -->
    <div style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-2xl);margin-bottom:var(--espaco-2xl);">
        <h2 style="font-size:1.4rem;margin-bottom:var(--espaco-xl);padding-bottom:var(--espaco-md);border-bottom:1px solid var(--borda-glass);">
            📋 Detalhes da Reserva #<?php echo $reserva_id; ?>
        </h2>

        <div class="grid grid--2" style="gap:var(--espaco-md);">
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--texto-muted);margin-bottom:4px;">Atividade</div>
                <div style="font-weight:bold;color:var(--texto-primario);"><?php echo esc_html($atividade); ?></div>
            </div>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--texto-muted);margin-bottom:4px;">Data e Horário</div>
                <div style="font-weight:bold;color:var(--texto-primario);">📅 <?php echo esc_html($data_atv . ' às ' . $hora_atv); ?></div>
            </div>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--texto-muted);margin-bottom:4px;">Total de Inscritos</div>
                <div style="font-weight:bold;color:var(--texto-primario);">👥 <?php echo count($inscritos); ?> pessoa(s)</div>
            </div>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--texto-muted);margin-bottom:4px;">Valor Pago</div>
                <div style="font-family:var(--fonte-titulo);font-size:1.8rem;color:var(--cor-secundaria);">R$ <?php echo $valor; ?></div>
            </div>
        </div>

        <?php if (!empty($inscritos)): ?>
        <div style="margin-top:var(--espaco-xl);padding-top:var(--espaco-lg);border-top:1px solid var(--borda-glass);">
            <h3 style="font-size:1rem;margin-bottom:var(--espaco-md);">👥 Lista de Inscritos</h3>
            <table style="width:100%;border-collapse:collapse;font-size:var(--tamanho-pequeno);">
                <thead>
                    <tr style="background:var(--fundo-glass);">
                        <th style="padding:8px;text-align:left;">#</th>
                        <th style="padding:8px;text-align:left;">Nome</th>
                        <th style="padding:8px;text-align:left;">CPF</th>
                        <th style="padding:8px;text-align:left;">Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscritos as $i => $p): ?>
                    <tr style="border-bottom:1px solid var(--borda-glass);">
                        <td style="padding:8px;"><?php echo ($i + 1); ?></td>
                        <td style="padding:8px;"><?php echo esc_html($p['nome']); ?></td>
                        <td style="padding:8px;"><?php echo esc_html($p['cpf']); ?></td>
                        <td style="padding:8px;"><?php echo esc_html($p['telefone']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- INFORMAÇÕES IMPORTANTES -->
    <?php if ($status === 'aprovado'): ?>
    <div style="background:rgba(255,223,0,0.08);border:1px solid rgba(255,223,0,0.2);border-radius:var(--raio-xl);padding:var(--espaco-xl);margin-bottom:var(--espaco-2xl);">
        <h3 style="font-size:1rem;color:var(--cor-secundaria);margin-bottom:var(--espaco-md);">⚠️ Informações Importantes</h3>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:var(--espaco-sm);">
            <li style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);">📍 Chegue com 30 minutos de antecedência</li>
            <li style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);">👕 Use roupas adequadas para atividades ao ar livre</li>
            <li style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);">🪪 Traga documento com foto (todos os inscritos)</li>
            <li style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);">🌧️ Em caso de chuva, aguarde contato sobre reagendamento</li>
            <li style="color:var(--texto-secundario);font-size:var(--tamanho-pequeno);">📧 Um e-mail de confirmação foi enviado para <?php echo esc_html($email); ?></li>
        </ul>
    </div>
    <?php endif; ?>

    <!-- AÇÕES -->
    <div style="display:flex;gap:var(--espaco-md);flex-wrap:wrap;justify-content:center;">
        <a href="<?php echo esc_url($wa_link); ?>"
           class="btn btn--primario btn--grande"
           target="_blank" rel="noopener noreferrer">
            📲 Falar com a equipe
        </a>
        <a href="<?php echo esc_url(home_url('/atividades')); ?>"
           class="btn btn--ghost btn--grande">
            🌊 Ver outras atividades
        </a>
        <?php if ($status === 'aprovado'): ?>
        <button onclick="window.print()" class="btn btn--secundario btn--grande">
            🖨️ Imprimir comprovante
        </button>
        <?php endif; ?>
    </div>

</div>
</section>
</main>

<style>
@media print {
    .navbar, .footer, .whatsapp-float, .btn { display: none !important; }
    body { background: white; color: black; }
    .fundo-card, [style*="background"] { background: white !important; color: black !important; }
}
</style>

<?php get_footer(); ?>
