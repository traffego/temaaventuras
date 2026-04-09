<?php
/**
 * Admin – Página de Configurações de Pagamento
 * Admin → Aventuras → 💳 Pagamento
 *
 * @package TemaAventuras
 */
defined( 'ABSPATH' ) || exit;

/* =========================================
   REGISTRAR SUBMENUS NO MENU GESTÃO
   ========================================= */
function ta_payment_admin_menu() {
    add_submenu_page( 'gestao-aventuras', 'Configurações de Pagamento', '💳 Pagamento', 'manage_options', 'ta-pagamento', 'ta_payment_config_page' );
    add_submenu_page( 'gestao-aventuras', 'Reservas', '📋 Reservas', 'manage_options', 'ta-reservas', 'ta_reservas_admin_page' );
    add_submenu_page( 'gestao-aventuras', 'Check-in', '✅ Check-in', 'manage_options', 'ta-checkin', 'ta_checkin_admin_page' );
}
add_action( 'admin_menu', 'ta_payment_admin_menu', 25 );

/* =========================================
   HELPER: ler config de pagamento
   ========================================= */
function tema_aventuras_payment_config(): array {
    $sandbox = (bool) get_option( 'ta_mp_sandbox', 1 );
    return [
        'sandbox'         => $sandbox,
        'token_sandbox'   => get_option( 'ta_mp_token_sandbox',   '' ),
        'token_producao'  => get_option( 'ta_mp_token_producao',  '' ),
        'pubkey_sandbox'  => get_option( 'ta_mp_pubkey_sandbox',  '' ),
        'pubkey_producao' => get_option( 'ta_mp_pubkey_producao', '' ),
        'email_admin'     => get_option( 'ta_mp_email_admin',     get_option( 'admin_email' ) ),
        'parcelas_max'    => (int) get_option( 'ta_mp_parcelas_max', 6 ),
        // URL canônica do webhook (usada na API do MP e exibida na tela de config)
        'webhook_url'     => add_query_arg( 'ta_pagamento_notificacao', '1', home_url( '/' ) ),
        // Alias legado — mantido para compatibilidade com class-mercadopago.php
        'notificacao_url' => add_query_arg( 'ta_pagamento_notificacao', '1', home_url( '/' ) ),
    ];
}

/* =========================================
   SALVAR
   ========================================= */
function ta_payment_save_config() {
    if ( ! isset( $_POST['ta_payment_nonce'] ) || ! wp_verify_nonce( $_POST['ta_payment_nonce'], 'ta_salvar_pagamento' ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $campos = [
        'ta_mp_token_producao'  => 'sanitize_text_field',
        'ta_mp_token_sandbox'   => 'sanitize_text_field',
        'ta_mp_pubkey_producao' => 'sanitize_text_field',
        'ta_mp_pubkey_sandbox'  => 'sanitize_text_field',
        'ta_mp_notificacao_url' => 'esc_url_raw',
        'ta_mp_email_admin'     => 'sanitize_email',
        'ta_mp_parcelas_max'    => 'intval',
        'ta_mp_juros'           => 'sanitize_text_field',
    ];

    foreach ( $campos as $campo => $fn ) {
        if ( isset( $_POST[ $campo ] ) ) update_option( $campo, $fn( $_POST[ $campo ] ) );
    }
    update_option( 'ta_mp_sandbox', isset( $_POST['ta_mp_sandbox'] ) ? 1 : 0 );

    add_action( 'admin_notices', fn() => print '<div class="notice notice-success is-dismissible"><p>✅ Configurações salvas!</p></div>' );
}
add_action( 'admin_init', 'ta_payment_save_config' );

/* =========================================
   RENDERIZAR PÁGINA
   ========================================= */
function ta_payment_config_page() {
    $c = tema_aventuras_payment_config();
    include TEMA_AVENTURAS_DIR . '/inc/payment/views/config-page.php';
}

/* =========================================
   AJAX: Testar conexão
   ========================================= */
add_action( 'wp_ajax_ta_testar_mp', function() {
    check_ajax_referer( 'ta_testar_mp', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();
    $mp  = new TemaAventuras_MercadoPago();
    $res = $mp->testar_conexao();
    if ( is_wp_error( $res ) ) wp_send_json_error( ['message' => $res->get_error_message()] );
    $cfg = tema_aventuras_payment_config();
    wp_send_json_success( ['ambiente' => $cfg['sandbox'] ? 'Sandbox' : 'Produção'] );
} );

/* =========================================
   CHECKIN ADMIN PAGE
   ========================================= */
function ta_checkin_admin_page() {
    // Select filter
    $atividade_filter = isset($_GET['atividade']) ? intval($_GET['atividade']) : 0;
    
    // Get reservas if atividade selected
    $reservas = [];
    if ($atividade_filter > 0) {
        $reservas = get_posts([
            'post_type' => 'reserva',
            'numberposts' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                ['key' => '_reserva_atividade_id', 'value' => $atividade_filter],
                ['key' => '_reserva_status', 'value' => 'aprovado']
            ]
        ]);
    }

    include TEMA_AVENTURAS_DIR . '/inc/payment/views/checkin-page.php';
}

/* =========================================
   AJAX: Fazer Check-in
   ========================================= */
add_action( 'wp_ajax_ta_fazer_checkin', function() {
    check_ajax_referer( 'ta_admin_checkin', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error('Sem permissão.');

    $reserva_id = intval($_POST['reserva_id']);
    $indice     = intval($_POST['indice']);
    $estado     = $_POST['estado'] === '1'; // true para checkin, false para desfazer

    $inscritos = get_post_meta($reserva_id, '_reserva_inscritos', true) ?: [];
    if (!isset($inscritos[$indice])) {
        wp_send_json_error('Inscrito não encontrado.');
    }

    $inscritos[$indice]['checkin'] = $estado;
    update_post_meta($reserva_id, '_reserva_inscritos', $inscritos);

    wp_send_json_success(['mensagem' => $estado ? 'Check-in realizado!' : 'Check-in desfeito.']);
} );
