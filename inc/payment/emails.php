<?php
/**
 * E-mails transacionais do sistema de pagamento
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

/* =========================================
   CONFIRMAÇÃO DE RESERVA (cliente)
   ========================================= */
function ta_enviar_email_confirmacao(int $reserva_id): void {
    $m          = fn($k) => get_post_meta($reserva_id, $k, true);
    $nome       = $m('_reserva_cliente_nome');
    $email      = $m('_reserva_cliente_email');
    $atividade  = get_the_title($m('_reserva_atividade_id'));
    $data_atv   = date('d/m/Y', strtotime($m('_reserva_data_atividade')));
    $hora_atv   = $m('_reserva_hora_atividade');
    $inscritos  = $m('_reserva_inscritos') ?: [];
    $valor      = number_format((float)$m('_reserva_valor_total'), 2, ',', '.');
    $empresa    = ta_get('empresa_nome', get_bloginfo('name'));
    $tel_emp    = ta_get('empresa_telefone', '');
    $url_conf   = ta_get_url_confirmacao($reserva_id);

    $rows_inscritos = '';
    foreach ($inscritos as $i => $p) {
        $rows_inscritos .= "<tr style='border-bottom:1px solid #eee'>
            <td style='padding:6px'>".($i+1)."</td>
            <td style='padding:6px'>".esc_html($p['nome'])."</td>
            <td style='padding:6px'>".esc_html($p['cpf'])."</td>
            <td style='padding:6px'>".esc_html($p['telefone'])."</td>
        </tr>";
    }

    ob_start();
    ?>
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1)">

  <!-- Header -->
  <div style="background:linear-gradient(135deg,#009C3B,#002776);padding:30px;text-align:center">
    <h1 style="color:#FFDF00;margin:0;font-size:28px">✅ Reserva Confirmada!</h1>
    <p style="color:rgba(255,255,255,0.85);margin:8px 0 0"><?php echo esc_html($empresa); ?></p>
  </div>

  <!-- Corpo -->
  <div style="padding:30px">
    <p>Olá, <strong><?php echo esc_html($nome); ?></strong>! 🎉</p>
    <p>Sua reserva foi <strong style="color:#009C3B">confirmada com sucesso</strong>. Aguardamos você!</p>

    <!-- Detalhes -->
    <div style="background:#f9f9f9;border-left:4px solid #009C3B;border-radius:4px;padding:16px;margin:20px 0">
      <h3 style="margin:0 0 12px;color:#002776">📋 Detalhes da Reserva</h3>
      <p style="margin:4px 0">🌊 <strong>Atividade:</strong> <?php echo esc_html($atividade); ?></p>
      <p style="margin:4px 0">📅 <strong>Data:</strong> <?php echo esc_html($data_atv); ?></p>
      <p style="margin:4px 0">⏰ <strong>Horário:</strong> <?php echo esc_html($hora_atv); ?></p>
      <p style="margin:4px 0">👥 <strong>Inscritos:</strong> <?php echo count($inscritos); ?></p>
      <p style="margin:4px 0">💰 <strong>Valor pago:</strong> R$ <?php echo esc_html($valor); ?></p>
    </div>

    <!-- Lista de inscritos -->
    <?php if ($rows_inscritos): ?>
    <h3 style="color:#002776">👥 Lista de Inscritos</h3>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr style="background:#002776;color:#fff">
        <th style="padding:8px">#</th><th style="padding:8px">Nome</th><th style="padding:8px">CPF</th><th style="padding:8px">Telefone</th>
      </tr></thead>
      <tbody><?php echo $rows_inscritos; ?></tbody>
    </table>
    <?php endif; ?>

    <!-- Avisos -->
    <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:4px;padding:16px;margin:20px 0">
      <h4 style="margin:0 0 8px;color:#e65100">⚠️ Informações Importantes</h4>
      <ul style="margin:0;padding-left:20px;color:#555;font-size:13px">
        <li>Chegue com 30 minutos de antecedência</li>
        <li>Use roupas adequadas para atividades ao ar livre</li>
        <li>Traga documento com foto no dia</li>
        <li>Em caso de chuva, aguarde contato sobre reagendamento</li>
      </ul>
    </div>

    <!-- CTA -->
    <div style="text-align:center;margin:24px 0">
      <a href="<?php echo esc_url($url_conf); ?>" style="background:linear-gradient(135deg,#FFDF00,#009C3B);color:#000;text-decoration:none;padding:14px 28px;border-radius:25px;font-weight:bold;display:inline-block">
        🎟️ Ver Minha Reserva
      </a>
    </div>

    <?php if ($tel_emp): ?>
    <p style="text-align:center;color:#666;font-size:13px">Dúvidas? Entre em contato: <strong><?php echo esc_html($tel_emp); ?></strong></p>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <div style="background:#f4f4f4;padding:16px;text-align:center;color:#999;font-size:12px">
    <p style="margin:0"><?php echo esc_html($empresa); ?> — <?php echo esc_html(ta_get('empresa_endereco','')); ?></p>
  </div>
</div>
</body></html>
    <?php
    $html = ob_get_clean();

    $assunto = "✅ Reserva confirmada — {$atividade} — {$data_atv}";

    add_filter('wp_mail_content_type', fn() => 'text/html');
    wp_mail($email, $assunto, $html, ['From: ' . $empresa . ' <' . ta_get('empresa_email', get_option('admin_email')) . '>']);
    remove_filter('wp_mail_content_type', fn() => 'text/html');

    // Notificar admin
    ta_enviar_email_admin($reserva_id, $atividade, $data_atv, $nome, count($inscritos), $valor);
}

/* =========================================
   NOTIFICAÇÃO PARA O ADMIN
   ========================================= */
function ta_enviar_email_admin(int $reserva_id, string $atividade, string $data, string $cliente, int $qtd, string $valor): void {
    $cfg      = tema_aventuras_payment_config();
    $email    = $cfg['email_admin'];
    $empresa  = ta_get('empresa_nome', get_bloginfo('name'));
    $edit_url = get_edit_post_link($reserva_id, '&');

    $html = "
    <h2>💰 Nova reserva confirmada!</h2>
    <p><strong>Atividade:</strong> {$atividade}</p>
    <p><strong>Data:</strong> {$data}</p>
    <p><strong>Cliente:</strong> {$cliente}</p>
    <p><strong>Inscritos:</strong> {$qtd}</p>
    <p><strong>Valor:</strong> R$ {$valor}</p>
    <p><a href='{$edit_url}' style='background:#002776;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none'>Ver Reserva no Admin</a></p>
    ";

    add_filter('wp_mail_content_type', fn() => 'text/html');
    wp_mail($email, "💰 Nova reserva — {$atividade} ({$qtd} inscritos)", $html);
    remove_filter('wp_mail_content_type', fn() => 'text/html');
}
