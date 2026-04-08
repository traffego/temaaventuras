<?php
/**
 * IPN / Webhook — Mercado Pago
 * Registra endpoint: /?ta_pagamento_notificacao=1
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

/* =========================================
   REGISTRAR ENDPOINT IPN
   ========================================= */
add_action('init', function() {
    if (!isset($_GET['ta_pagamento_notificacao'])) return;
    ta_processar_notificacao_mp();
    exit;
});

/* =========================================
   PROCESSAR NOTIFICAÇÃO
   ========================================= */
function ta_processar_notificacao_mp(): void {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    // Aceita também via query string (IPN legado)
    if (empty($data)) {
        $data = [
            'type'       => sanitize_text_field($_GET['type'] ?? ''),
            'data'       => ['id' => sanitize_text_field($_GET['data_id'] ?? '')],
        ];
    }

    ta_log_pagamento('IPN recebido: ' . wp_json_encode($data));

    // Apenas processar notificações de pagamento
    if (($data['type'] ?? '') !== 'payment') {
        http_response_code(200);
        return;
    }

    $payment_id = $data['data']['id'] ?? '';
    if (empty($payment_id)) {
        http_response_code(400);
        return;
    }

    // Consultar status real na API
    $mp     = new TemaAventuras_MercadoPago();
    $status = $mp->consultar_pagamento($payment_id);

    if (is_wp_error($status)) {
        ta_log_pagamento('Erro ao consultar pagamento ' . $payment_id . ': ' . $status->get_error_message());
        http_response_code(500);
        return;
    }

    // Buscar reserva pelo external_reference ou payment_id
    $reserva = ta_buscar_reserva_por_payment_id($payment_id)
             ?: ta_buscar_reserva_por_referencia($status['external_reference'] ?? '');

    if (!$reserva) {
        ta_log_pagamento('Reserva não encontrada para payment_id: ' . $payment_id);
        http_response_code(200); // OK para o MP não reenviar
        return;
    }

    // Mapear status MP → status interno
    $mapa_status = [
        'approved'    => 'aprovado',
        'pending'     => 'pendente',
        'in_process'  => 'pendente',
        'rejected'    => 'rejeitado',
        'cancelled'   => 'cancelado',
        'refunded'    => 'cancelado',
    ];

    $novo_status = $mapa_status[$status['status']] ?? 'pendente';
    ta_atualizar_status_reserva($reserva->ID, $novo_status, $payment_id);
    ta_log_pagamento("Reserva #{$reserva->ID} → status: {$novo_status} (payment: {$payment_id})");

    http_response_code(200);
}

/* =========================================
   BUSCAR RESERVA POR PAYMENT ID
   ========================================= */
function ta_buscar_reserva_por_payment_id(string $payment_id): ?WP_Post {
    $posts = get_posts([
        'post_type'  => 'reserva',
        'numberposts'=> 1,
        'meta_query' => [['key' => '_reserva_mp_payment_id', 'value' => $payment_id]],
    ]);
    return $posts[0] ?? null;
}

/* =========================================
   BUSCAR RESERVA POR REFERÊNCIA EXTERNA
   Format: RESERVA-{ID}
   ========================================= */
function ta_buscar_reserva_por_referencia(string $ref): ?WP_Post {
    if (!str_starts_with($ref, 'RESERVA-')) return null;
    $id = (int) str_replace('RESERVA-', '', $ref);
    return get_post($id) ?: null;
}

/* =========================================
   LOG DE PAGAMENTOS
   ========================================= */
function ta_log_pagamento(string $msg): void {
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    $file = WP_CONTENT_DIR . '/ta-payment-log.txt';
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n";
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}

/* =========================================
   AJAX: CONSULTAR STATUS (Polling PIX Frontend)
   ========================================= */
add_action('wp_ajax_nopriv_ta_consultar_pix', 'ta_ajax_consultar_pix');
add_action('wp_ajax_ta_consultar_pix',        'ta_ajax_consultar_pix');

function ta_ajax_consultar_pix(): void {
    check_ajax_referer('ta_checkout_nonce', 'nonce');

    $reserva_id = intval($_POST['reserva_id'] ?? 0);
    $reserva    = get_post($reserva_id);

    if (!$reserva || $reserva->post_type !== 'reserva') {
        wp_send_json_error(['message' => 'Reserva não encontrada']);
    }

    $payment_id = get_post_meta($reserva_id, '_reserva_mp_payment_id', true);
    $status_db  = get_post_meta($reserva_id, '_reserva_status', true);

    // Se já aprovado no banco, retorna imediatamente
    if ($status_db === 'aprovado') {
        wp_send_json_success(['status' => 'aprovado', 'redirect' => ta_get_url_confirmacao($reserva_id)]);
    }

    // Consultar na API do MP
    if ($payment_id) {
        $mp     = new TemaAventuras_MercadoPago();
        $result = $mp->consultar_pagamento($payment_id);

        if (!is_wp_error($result) && $result['aprovado']) {
            ta_atualizar_status_reserva($reserva_id, 'aprovado', $payment_id);
            wp_send_json_success(['status' => 'aprovado', 'redirect' => ta_get_url_confirmacao($reserva_id)]);
        }
    }

    wp_send_json_success(['status' => $status_db]);
}

/* =========================================
   EXPORTAR CSV
   ========================================= */
add_action('admin_post_ta_exportar_reservas', function() {
    check_admin_referer('ta_exportar');
    if (!current_user_can('manage_options')) wp_die('Sem permissão');

    $status_filter    = sanitize_text_field($_GET['status'] ?? '');
    $atividade_filter = intval($_GET['atividade'] ?? 0);

    $meta_query = [];
    if ($status_filter) $meta_query[] = ['key' => '_reserva_status', 'value' => $status_filter];
    if ($atividade_filter) $meta_query[] = ['key' => '_reserva_atividade_id', 'value' => $atividade_filter];

    $reservas = get_posts(['post_type' => 'reserva', 'numberposts' => -1, 'meta_query' => $meta_query ?: null]);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="reservas-' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');

    $fp = fopen('php://output', 'w');
    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 para Excel

    fputcsv($fp, ['ID', 'Status', 'Responsável', 'E-mail', 'Telefone', 'CPF', 'Atividade', 'Data', 'Hora', 'Inscritos', 'Valor', 'Método', 'MP Payment ID'], ';');

    foreach ($reservas as $r) {
        $m = fn($k) => get_post_meta($r->ID, $k, true);
        fputcsv($fp, [
            $r->ID,
            $m('_reserva_status'),
            $m('_reserva_cliente_nome'),
            $m('_reserva_cliente_email'),
            $m('_reserva_cliente_telefone'),
            $m('_reserva_cliente_cpf'),
            get_the_title($m('_reserva_atividade_id')),
            $m('_reserva_data_atividade'),
            $m('_reserva_hora_atividade'),
            $m('_reserva_total_inscritos'),
            number_format((float)$m('_reserva_valor_total'), 2, ',', '.'),
            strtoupper($m('_reserva_metodo')),
            $m('_reserva_mp_payment_id'),
        ], ';');

        // Inscritos individuais
        $inscritos = $m('_reserva_inscritos') ?: [];
        foreach ($inscritos as $i => $p) {
            fputcsv($fp, ['', '', '  Inscrito ' . ($i+1) . ': ' . $p['nome'], '', $p['telefone'], $p['cpf']], ';');
        }
    }

    fclose($fp);
    exit;
});

/* =========================================
   HELPER: URL da página de confirmação
   ========================================= */
function ta_get_url_confirmacao(int $reserva_id): string {
    $page = get_page_by_path('confirmacao-reserva');
    $base = $page ? get_permalink($page) : home_url('/confirmacao-reserva/');
    return add_query_arg(['reserva' => $reserva_id, 'token' => ta_token_reserva($reserva_id)], $base);
}

function ta_token_reserva(int $reserva_id): string {
    return hash_hmac('sha256', "reserva-{$reserva_id}", wp_salt('auth'));
}
