<?php
/**
 * Handlers AJAX do checkout
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

/* =========================================
   AJAX: CRIAR RESERVA + GERAR PAGAMENTO
   ========================================= */
add_action('wp_ajax_nopriv_ta_criar_reserva_checkout', 'ta_ajax_criar_reserva_checkout');
add_action('wp_ajax_ta_criar_reserva_checkout',        'ta_ajax_criar_reserva_checkout');

function ta_ajax_criar_reserva_checkout(): void {
    check_ajax_referer('ta_checkout_nonce', 'nonce');

    $atividade_id  = intval($_POST['atividade_id'] ?? 0);
    $metodo        = sanitize_text_field($_POST['metodo'] ?? 'pix');
    $resp_nome     = sanitize_text_field($_POST['resp_nome'] ?? '');
    $resp_email    = sanitize_email($_POST['resp_email'] ?? '');
    $resp_tel      = sanitize_text_field($_POST['resp_telefone'] ?? '');
    $resp_cpf      = preg_replace('/\D/', '', $_POST['resp_cpf'] ?? '');
    $valor_total   = floatval($_POST['valor_total'] ?? 0);

    // Validações básicas
    if (!$atividade_id) wp_send_json_error(['message' => 'Atividade inválida.']);
    if (!$resp_nome || !$resp_email)    wp_send_json_error(['message' => 'Dados do responsável incompletos.']);
    if ($valor_total <= 0)              wp_send_json_error(['message' => 'Valor inválido.']);

    // Inscritos
    $nomes     = $_POST['inscrito_nome']     ?? [];
    $cpfs      = $_POST['inscrito_cpf']      ?? [];
    $telefones = $_POST['inscrito_telefone'] ?? [];
    $inscritos = [];

    foreach ($nomes as $i => $n) {
        if (empty($n)) continue;
        $inscritos[] = [
            'nome'     => sanitize_text_field($n),
            'cpf'      => preg_replace('/\D/', '', $cpfs[$i] ?? ''),
            'telefone' => sanitize_text_field($telefones[$i] ?? ''),
        ];
    }

    if (empty($inscritos)) wp_send_json_error(['message' => 'Adicione pelo menos um inscrito.']);

    // Obter dados do evento
    $data_atividade = get_post_meta($atividade_id, '_atividade_data', true);
    $hora_atividade = get_post_meta($atividade_id, '_atividade_horario', true);
    
    if (!$data_atividade) {
        wp_send_json_error(['message' => 'Esta atividade não possui uma data agendada.']);
    }

    // Criar reserva
    $reserva_id = ta_criar_reserva([
        'atividade_id'   => $atividade_id,
        'data_atividade' => $data_atividade,
        'hora_atividade' => $hora_atividade,
        'nome'           => $resp_nome,
        'email'          => $resp_email,
        'telefone'       => $resp_tel,
        'cpf'            => $resp_cpf,
        'valor_total'    => $valor_total,
        'metodo'         => $metodo,
        'inscritos'      => $inscritos,
    ]);

    if (is_wp_error($reserva_id)) {
        wp_send_json_error(['message' => $reserva_id->get_error_message()]);
    }

    $ref = 'RESERVA-' . $reserva_id;

    // Gerar pagamento PIX
    if ($metodo === 'pix') {
        $mp  = new TemaAventuras_MercadoPago();
        $pix = $mp->criar_pagamento_pix([
            'valor'        => $valor_total,
            'descricao'    => get_the_title($atividade_id) . ' — ' . count($inscritos) . ' inscrito(s)',
            'email'        => $resp_email,
            'cpf'          => $resp_cpf,
            'nome'         => $resp_nome,
            'reserva_id'   => $reserva_id,
            'external_ref' => $ref,
        ]);

        if (is_wp_error($pix)) {
            wp_delete_post($reserva_id, true);
            wp_send_json_error(['message' => 'Erro ao gerar PIX: ' . $pix->get_error_message()]);
        }

        // Salvar dados do PIX na reserva
        update_post_meta($reserva_id, '_reserva_mp_payment_id', $pix['payment_id']);
        update_post_meta($reserva_id, '_reserva_pix_qrcode',    $pix['qr_code_base64']);
        update_post_meta($reserva_id, '_reserva_pix_copia_cola',$pix['qr_code']);

        wp_send_json_success([
            'reserva_id' => $reserva_id,
            'metodo'     => 'pix',
            'pix'        => $pix,
        ]);
    }

    // Cartão — reserva criada, pagamento processado em ajax separado
    wp_send_json_success([
        'reserva_id' => $reserva_id,
        'metodo'     => 'credit_card',
    ]);
}

/* =========================================
   AJAX: PROCESSAR CARTÃO
   ========================================= */
add_action('wp_ajax_nopriv_ta_processar_cartao', 'ta_ajax_processar_cartao');
add_action('wp_ajax_ta_processar_cartao',        'ta_ajax_processar_cartao');

function ta_ajax_processar_cartao(): void {
    check_ajax_referer('ta_checkout_nonce', 'nonce');

    $reserva_id = intval($_POST['reserva_id'] ?? 0);
    $token      = sanitize_text_field($_POST['token'] ?? '');
    $pm_id      = sanitize_text_field($_POST['pm_id'] ?? '');
    $issuer_id  = sanitize_text_field($_POST['issuer_id'] ?? '0');
    $parcelas   = intval($_POST['parcelas'] ?? 1);
    $email      = sanitize_email($_POST['email'] ?? '');
    $cpf        = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $valor      = floatval($_POST['valor'] ?? 0);

    if (!$reserva_id || !$token) wp_send_json_error(['message' => 'Dados inválidos.']);

    $reserva = get_post($reserva_id);
    if (!$reserva || $reserva->post_type !== 'reserva') {
        wp_send_json_error(['message' => 'Reserva não encontrada.']);
    }

    $mp     = new TemaAventuras_MercadoPago();
    $result = $mp->criar_pagamento_cartao([
        'valor'        => $valor,
        'descricao'    => get_post_meta($reserva_id, '_reserva_atividade_id', true)
                          ? get_the_title(get_post_meta($reserva_id, '_reserva_atividade_id', true))
                          : 'Reserva #' . $reserva_id,
        'email'        => $email,
        'cpf'          => $cpf,
        'nome'         => get_post_meta($reserva_id, '_reserva_cliente_nome', true),
        'token'        => $token,
        'parcelas'     => $parcelas,
        'issuer_id'    => $issuer_id,
        'pm_id'        => $pm_id,
        'external_ref' => 'RESERVA-' . $reserva_id,
    ]);

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }

    ta_atualizar_status_reserva(
        $reserva_id,
        $result['aprovado'] ? 'aprovado' : 'rejeitado',
        $result['payment_id']
    );

    if ($result['aprovado']) {
        wp_send_json_success([
            'aprovado' => true,
            'redirect' => ta_get_url_confirmacao($reserva_id),
        ]);
    } else {
        $msgs = [
            'cc_rejected_insufficient_amount' => 'Saldo insuficiente no cartão.',
            'cc_rejected_bad_filled_security_code' => 'CVV incorreto.',
            'cc_rejected_bad_filled_date' => 'Data de validade incorreta.',
            'cc_rejected_call_for_authorize' => 'Cartão bloqueado. Ligue para sua operadora.',
        ];
        $detail = $result['status_detail'] ?? '';
        $msg    = $msgs[$detail] ?? 'Pagamento não autorizado. Tente outro cartão.';
        wp_send_json_error(['message' => $msg, 'detail' => $detail]);
    }
}
