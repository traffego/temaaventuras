<?php
/**
 * View: Lista de Reservas Admin
 * Variável: $reservas (array de WP_Post), $status_filter, $atividade_filter
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

$cores_status = ['pendente' => '#f0ad4e', 'aprovado' => '#5cb85c', 'rejeitado' => '#d9534f', 'cancelado' => '#aaa'];
$atividades   = get_posts(['post_type' => 'atividade', 'numberposts' => -1, 'post_status' => 'publish']);
?>
<div class="wrap">
<h1>📋 Reservas</h1>

<!-- Filtros -->
<div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <input type="hidden" name="page" value="ta-reservas">
        <select name="status">
            <option value="">Todos os Status</option>
            <?php foreach (['pendente','aprovado','rejeitado','cancelado'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php selected($status_filter, $s); ?>><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="atividade">
            <option value="">Todas as Atividades</option>
            <?php foreach ($atividades as $at): ?>
            <option value="<?php echo $at->ID; ?>" <?php selected($atividade_filter, $at->ID); ?>><?php echo esc_html($at->post_title); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="button">Filtrar</button>
        <a href="<?php echo admin_url('admin.php?page=ta-reservas'); ?>" class="button">Limpar</a>
    </form>

    <!-- Exportar CSV -->
    <?php if (!empty($reservas)): ?>
    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=ta_exportar_reservas&status=' . $status_filter . '&atividade=' . $atividade_filter), 'ta_exportar'); ?>"
       class="button button-primary">📥 Exportar CSV</a>
    <?php endif; ?>
</div>

<!-- Totais -->
<?php if (!empty($reservas)):
    $total_inscritos  = 0;
    $total_valor      = 0;
    foreach ($reservas as $r) {
        $total_inscritos += (int)get_post_meta($r->ID, '_reserva_total_inscritos', true);
        $total_valor     += (float)get_post_meta($r->ID, '_reserva_valor_total', true);
    }
?>
<div style="display:flex;gap:16px;margin-bottom:16px;">
    <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:12px 20px;text-align:center;">
        <div style="font-size:1.8em;font-weight:bold"><?php echo count($reservas); ?></div>
        <div style="color:#666;font-size:12px">Reservas</div>
    </div>
    <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:12px 20px;text-align:center;">
        <div style="font-size:1.8em;font-weight:bold"><?php echo $total_inscritos; ?></div>
        <div style="color:#666;font-size:12px">Inscritos</div>
    </div>
    <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:12px 20px;text-align:center;">
        <div style="font-size:1.8em;font-weight:bold">R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></div>
        <div style="color:#666;font-size:12px">Receita Total</div>
    </div>
</div>

<table class="widefat striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Status</th>
            <th>Responsável</th>
            <th>Atividade</th>
            <th>Data/Hora</th>
            <th>Inscritos</th>
            <th>Valor</th>
            <th>Método</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($reservas as $r):
        $status    = get_post_meta($r->ID, '_reserva_status', true) ?: 'pendente';
        $nome      = get_post_meta($r->ID, '_reserva_cliente_nome', true);
        $email     = get_post_meta($r->ID, '_reserva_cliente_email', true);
        $tel       = get_post_meta($r->ID, '_reserva_cliente_telefone', true);
        $atv_id    = get_post_meta($r->ID, '_reserva_atividade_id', true);
        $data_atv  = get_post_meta($r->ID, '_reserva_data_atividade', true);
        $hora_atv  = get_post_meta($r->ID, '_reserva_hora_atividade', true);
        $total_i   = get_post_meta($r->ID, '_reserva_total_inscritos', true);
        $valor     = get_post_meta($r->ID, '_reserva_valor_total', true);
        $metodo    = get_post_meta($r->ID, '_reserva_metodo', true);
        $cor       = $cores_status[$status] ?? '#333';
    ?>
    <tr>
        <td><?php echo $r->ID; ?></td>
        <td><span style="background:<?php echo $cor; ?>;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px"><?php echo strtoupper($status); ?></span></td>
        <td>
            <strong><?php echo esc_html($nome); ?></strong><br>
            <span style="color:#666;font-size:11px"><?php echo esc_html($email); ?></span><br>
            <span style="color:#666;font-size:11px"><?php echo esc_html($tel); ?></span>
        </td>
        <td><?php echo get_the_title($atv_id) ?: '—'; ?></td>
        <td style="white-space:nowrap"><?php echo esc_html(date('d/m/Y', strtotime($data_atv)) . ' ' . $hora_atv); ?></td>
        <td style="text-align:center"><strong><?php echo esc_html($total_i); ?></strong></td>
        <td style="white-space:nowrap">R$ <?php echo number_format((float)$valor, 2, ',', '.'); ?></td>
        <td><?php echo strtoupper($metodo ?: '—'); ?></td>
        <td style="white-space:nowrap">
            <a href="<?php echo get_edit_post_link($r->ID); ?>" class="button button-small">✏️ Editar</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div style="background:#fff;border:1px solid #ddd;padding:40px;text-align:center;border-radius:4px">
    <p style="font-size:3em;margin:0">📋</p>
    <p>Nenhuma reserva encontrada.</p>
</div>
<?php endif; ?>
</div>
