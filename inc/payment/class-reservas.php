<?php
/**
 * CPT Reservas + CPT Sessões de Atividade
 * Gerenciamento de reservas e sessões agendadas
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

/* =========================================
   CPT: RESERVA
   ========================================= */
function ta_cpt_reservas() {
    register_post_type('reserva', [
        'labels'          => ['name' => 'Reservas', 'singular_name' => 'Reserva', 'menu_name' => 'Reservas'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => false,
        'supports'        => ['title', 'custom-fields'],
        'show_in_menu'    => 'gestao-aventuras',
        'capabilities'    => ['create_posts' => 'do_not_allow'],
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'ta_cpt_reservas');

/* =========================================
   META BOX: SESSÕES NA ATIVIDADE
   ========================================= */
add_action('add_meta_boxes', function() {
    add_meta_box('ta_reserva_detalhes', '📋 Detalhes da Reserva', 'ta_render_reserva_metabox', 'reserva', 'normal', 'high');
    add_meta_box('ta_reserva_inscritos', '👥 Inscritos', 'ta_render_inscritos_metabox', 'reserva', 'normal', 'default');
    add_meta_box('ta_participantes_atividade', '👥 Participantes Confirmados', 'ta_render_participantes_metabox', 'atividade', 'side', 'default');
});





/* =========================================
   HELPER: vagas disponíveis de uma sessão
   ========================================= */
function ta_vagas_disponiveis(int $atividade_id): array {
    $vagas_totais = (int) get_post_meta($atividade_id, '_atividade_vagas', true);

    // Contar inscritos confirmados
    $reservas = get_posts([
        'post_type'   => 'reserva',
        'numberposts' => -1,
        'meta_query'  => [
            ['key' => '_reserva_atividade_id', 'value' => $atividade_id],
            ['key' => '_reserva_status',       'value' => 'aprovado'],
        ],
    ]);

    $ocupadas = 0;
    foreach ($reservas as $r) {
        $ocupadas += (int) get_post_meta($r->ID, '_reserva_total_inscritos', true);
    }

    return [
        'total'    => $vagas_totais,
        'ocupadas' => $ocupadas,
        'livres'   => max(0, $vagas_totais - $ocupadas),
    ];
}

/* =========================================
   META BOX: DETALHES DA RESERVA
   ========================================= */
function ta_render_reserva_metabox($post) {
    $m = fn($k) => get_post_meta($post->ID, $k, true);
    $atividade = get_post($m('_reserva_atividade_id'));
    $status    = $m('_reserva_status') ?: 'pendente';
    $cores     = ['pendente' => '#f0ad4e', 'aprovado' => '#5cb85c', 'rejeitado' => '#d9534f', 'cancelado' => '#777'];
    ?>
    <table class="form-table">
        <tr><th>Status</th><td><strong style="color:<?php echo $cores[$status] ?? '#333'; ?>"><?php echo strtoupper($status); ?></strong></td></tr>
        <tr><th>Atividade</th><td><?php echo $atividade ? esc_html($atividade->post_title) : '—'; ?></td></tr>
        <tr><th>Data/Hora</th><td><?php echo esc_html($m('_reserva_data_atividade') . ' ' . $m('_reserva_hora_atividade')); ?></td></tr>
        <tr><th>Responsável</th><td><?php echo esc_html($m('_reserva_cliente_nome')); ?> (<?php echo esc_html($m('_reserva_cliente_email')); ?>)</td></tr>
        <tr><th>Telefone</th><td><?php echo esc_html($m('_reserva_cliente_telefone')); ?></td></tr>
        <tr><th>CPF Responsável</th><td><?php echo esc_html($m('_reserva_cliente_cpf')); ?></td></tr>
        <tr><th>Total Inscritos</th><td><?php echo esc_html($m('_reserva_total_inscritos')); ?></td></tr>
        <tr><th>Valor Total</th><td><strong>R$ <?php echo number_format((float)$m('_reserva_valor_total'), 2, ',', '.'); ?></strong></td></tr>
        <tr><th>Método</th><td><?php echo strtoupper($m('_reserva_metodo') ?: '—'); ?></td></tr>
        <tr><th>MP Payment ID</th><td><code><?php echo esc_html($m('_reserva_mp_payment_id') ?: '—'); ?></code></td></tr>
        <tr>
            <th>Ação Manual</th>
            <td>
                <form method="post" style="display:inline">
                    <?php wp_nonce_field('ta_acao_reserva_' . $post->ID); ?>
                    <input type="hidden" name="ta_reserva_id" value="<?php echo $post->ID; ?>">
                    <input type="hidden" name="ta_reserva_acao" value="aprovar">
                    <button class="button button-primary" onclick="return confirm('Confirmar aprovação manual?')">✅ Aprovar</button>
                </form>
                <form method="post" style="display:inline;margin-left:8px">
                    <?php wp_nonce_field('ta_acao_reserva_' . $post->ID); ?>
                    <input type="hidden" name="ta_reserva_id" value="<?php echo $post->ID; ?>">
                    <input type="hidden" name="ta_reserva_acao" value="cancelar">
                    <button class="button" style="color:red" onclick="return confirm('Cancelar reserva?')">❌ Cancelar</button>
                </form>
            </td>
        </tr>
    </table>
    <?php
}

/* =========================================
   META BOX: INSCRITOS DA RESERVA
   ========================================= */
function ta_render_inscritos_metabox($post) {
    $total    = (int) get_post_meta($post->ID, '_reserva_total_inscritos', true);
    $inscritos = get_post_meta($post->ID, '_reserva_inscritos', true) ?: [];
    if (empty($inscritos)) { echo '<p>Sem inscritos cadastrados.</p>'; return; }
    echo '<table class="widefat"><thead><tr><th>#</th><th>Nome</th><th>CPF</th><th>Telefone</th><th>Check-in</th></tr></thead><tbody>';
    foreach ($inscritos as $i => $p) {
        $checkin_tag = !empty($p['checkin']) ? '<span style="color:green;font-weight:bold;">✅ Presente</span>' : '<span style="color:#999;">Pendente</span>';
        echo '<tr><td>' . ($i+1) . '</td><td>' . esc_html($p['nome']) . '</td><td>' . esc_html($p['cpf']) . '</td><td>' . esc_html($p['telefone']) . '</td><td>' . $checkin_tag . '</td></tr>';
    }
    echo '</tbody></table>';
}

/* =========================================
   META BOX: PARTICIPANTES DA ATIVIDADE
   ========================================= */
function ta_render_participantes_metabox($post) {
    echo '<p style="font-size:11px;color:#666">Participantes com pagamento confirmado:</p>';
    $reservas = get_posts([
        'post_type'   => 'reserva',
        'numberposts' => -1,
        'meta_query'  => [
            ['key' => '_reserva_atividade_id', 'value' => $post->ID],
            ['key' => '_reserva_status', 'value' => 'aprovado'],
        ],
    ]);
    if (empty($reservas)) { echo '<p>Nenhum participante confirmado ainda.</p>'; return; }
    $total = 0;
    foreach ($reservas as $r) {
        $data  = get_post_meta($r->ID, '_reserva_data_atividade', true);
        $hora  = get_post_meta($r->ID, '_reserva_hora_atividade', true);
        $n     = (int)get_post_meta($r->ID, '_reserva_total_inscritos', true);
        $total += $n;
        echo '<div style="border:1px solid #eee;padding:6px;margin-bottom:6px;border-radius:3px;font-size:12px">';
        echo '<strong>' . esc_html(get_post_meta($r->ID,'_reserva_cliente_nome',true)) . '</strong>';
        echo ' (' . $n . ' inscrito' . ($n>1?'s':'') . ')';
        echo '<br><span style="color:#888">📅 ' . esc_html($data . ' ' . $hora) . '</span>';
        echo ' <a href="' . get_edit_post_link($r->ID) . '" style="float:right">ver →</a>';
        echo '</div>';
    }
    echo '<p><strong>Total: ' . $total . ' participante(s)</strong></p>';
    $url = admin_url('admin.php?page=ta-reservas&atividade=' . $post->ID . '&status=aprovado');
    echo '<a href="' . esc_url($url) . '" class="button button-primary" style="width:100%;text-align:center;margin-top:6px">📋 Ver lista completa</a>';
}

/* =========================================
   PROCESSAR AÇÕES MANUAIS (aprovar/cancelar)
   ========================================= */
add_action('admin_init', function() {
    if (!isset($_POST['ta_reserva_acao']) || !isset($_POST['ta_reserva_id'])) return;
    $reserva_id = intval($_POST['ta_reserva_id']);
    if (!wp_verify_nonce($_POST['_wpnonce'], 'ta_acao_reserva_' . $reserva_id)) return;
    if (!current_user_can('manage_options')) return;

    $acao = sanitize_text_field($_POST['ta_reserva_acao']);
    if ($acao === 'aprovar') {
        ta_atualizar_status_reserva($reserva_id, 'aprovado');
    } elseif ($acao === 'cancelar') {
        ta_atualizar_status_reserva($reserva_id, 'cancelado');
    }
});

/* =========================================
   HELPER: criar reserva
   ========================================= */
function ta_criar_reserva(array $dados): int|WP_Error {
    $inscritos = $dados['inscritos'] ?? [];
    $total     = count($inscritos);

    $vagas_info = ta_vagas_disponiveis((int)$dados['atividade_id']);
    if ($vagas_info['livres'] < $total) {
        return new WP_Error('sem_vagas', 'Não há vagas suficientes disponíveis para esta atividade.');
    }

    $titulo = sprintf('Reserva – %s – %s', get_the_title($dados['atividade_id']), date('d/m/Y', strtotime($dados['data_atividade'])));
    $post_id = wp_insert_post(['post_type' => 'reserva', 'post_title' => $titulo, 'post_status' => 'publish']);
    if (is_wp_error($post_id)) return $post_id;

    $metas = [
        '_reserva_atividade_id'     => (int)$dados['atividade_id'],
        '_reserva_data_atividade'   => sanitize_text_field($dados['data_atividade']),
        '_reserva_hora_atividade'   => sanitize_text_field($dados['hora_atividade'] ?? ''),
        '_reserva_cliente_nome'     => sanitize_text_field($dados['nome']),
        '_reserva_cliente_email'    => sanitize_email($dados['email']),
        '_reserva_cliente_telefone' => sanitize_text_field($dados['telefone']),
        '_reserva_cliente_cpf'      => preg_replace('/\D/', '', $dados['cpf']),
        '_reserva_total_inscritos'  => $total,
        '_reserva_valor_total'      => floatval($dados['valor_total']),
        '_reserva_metodo'           => sanitize_text_field($dados['metodo'] ?? ''),
        '_reserva_status'           => 'pendente',
        '_reserva_inscritos'        => array_map(fn($p) => [
            'nome'     => sanitize_text_field($p['nome']),
            'cpf'      => preg_replace('/\D/', '', $p['cpf']),
            'telefone' => sanitize_text_field($p['telefone']),
        ], $inscritos),
    ];

    foreach ($metas as $k => $v) update_post_meta($post_id, $k, $v);
    return $post_id;
}

/* =========================================
   HELPER: atualizar status da reserva
   ========================================= */
function ta_atualizar_status_reserva(int $reserva_id, string $status, string $payment_id = ''): void {
    $status_validos = ['pendente', 'aprovado', 'rejeitado', 'cancelado'];
    if (!in_array($status, $status_validos)) return;

    update_post_meta($reserva_id, '_reserva_status', $status);
    if ($payment_id) update_post_meta($reserva_id, '_reserva_mp_payment_id', $payment_id);

    if ($status === 'aprovado') {
        ta_enviar_email_confirmacao($reserva_id);
    }
}

/* =========================================
   PÁGINA ADMIN: LISTA DE RESERVAS
   ========================================= */
function ta_reservas_admin_page() {
    $status_filter    = sanitize_text_field($_GET['status'] ?? '');
    $atividade_filter = intval($_GET['atividade'] ?? 0);

    $meta_query = [];
    if ($status_filter) $meta_query[] = ['key' => '_reserva_status', 'value' => $status_filter];
    if ($atividade_filter) $meta_query[] = ['key' => '_reserva_atividade_id', 'value' => $atividade_filter];

    $reservas = get_posts(['post_type' => 'reserva', 'numberposts' => 50, 'meta_query' => $meta_query ?: null]);

    include TEMA_AVENTURAS_DIR . '/inc/payment/views/reservas-page.php';
}
