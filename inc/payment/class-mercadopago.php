<?php
/**
 * Mercado Pago – Wrapper da API REST
 * Usa wp_remote_post/get (sem Composer)
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

class TemaAventuras_MercadoPago {

    private string $access_token;
    private string $public_key;
    private bool   $sandbox;
    private string $base_url = 'https://api.mercadopago.com';

    public function __construct() {
        $config             = tema_aventuras_payment_config();
        $this->sandbox      = (bool) $config['sandbox'];
        $this->access_token = $this->sandbox ? $config['token_sandbox'] : $config['token_producao'];
        $this->public_key   = $this->sandbox ? $config['pubkey_sandbox'] : $config['pubkey_producao'];
    }

    /* =========================================
       HEADERS PADRÃO
       ========================================= */
    private function headers(): array {
        return [
            'Authorization' => 'Bearer ' . $this->access_token,
            'Content-Type'  => 'application/json',
            'X-Idempotency-Key' => wp_generate_uuid4(),
        ];
    }

    /* =========================================
       POST genérico
       ========================================= */
    private function post( string $endpoint, array $body ): array|WP_Error {
        $response = wp_remote_post( $this->base_url . $endpoint, [
            'headers' => $this->headers(),
            'body'    => wp_json_encode( $body ),
            'timeout' => 30,
            'method'  => 'POST',
        ] );

        if ( is_wp_error( $response ) ) return $response;

        $code = wp_remote_retrieve_response_code( $response );
        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code >= 400 ) {
            return new WP_Error(
                'mp_api_error',
                $data['message'] ?? 'Erro desconhecido do Mercado Pago',
                [ 'status' => $code, 'data' => $data ]
            );
        }
        return $data;
    }

    /* =========================================
       GET genérico
       ========================================= */
    private function get( string $endpoint ): array|WP_Error {
        $response = wp_remote_get( $this->base_url . $endpoint, [
            'headers' => $this->headers(),
            'timeout' => 30,
        ] );

        if ( is_wp_error( $response ) ) return $response;

        $code = wp_remote_retrieve_response_code( $response );
        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code >= 400 ) {
            return new WP_Error( 'mp_api_error', $data['message'] ?? 'Erro', [ 'status' => $code ] );
        }
        return $data;
    }

    /* =========================================
       CRIAR PAGAMENTO PIX
       ========================================= */
    public function criar_pagamento_pix( array $dados ): array|WP_Error {
        /*
         * $dados = [
         *   'valor'         => 150.00,
         *   'descricao'     => 'Rafting – 3 inscritos',
         *   'email'         => 'cliente@email.com',
         *   'cpf'           => '12345678909',
         *   'nome'          => 'João Silva',
         *   'reserva_id'    => 123,
         *   'external_ref'  => 'RESERVA-123',
         * ]
         */
        $config       = tema_aventuras_payment_config();
        $notif_url    = $config['notificacao_url'];

        $body = [
            'transaction_amount'    => (float) $dados['valor'],
            'description'           => sanitize_text_field( $dados['descricao'] ),
            'payment_method_id'     => 'pix',
            'notification_url'      => $notif_url,
            'external_reference'    => (string) $dados['external_ref'],
            'payer'                 => [
                'email'             => sanitize_email( $dados['email'] ),
                'first_name'        => sanitize_text_field( $dados['nome'] ),
                'identification'    => [
                    'type'          => 'CPF',
                    'number'        => preg_replace( '/\D/', '', $dados['cpf'] ),
                ],
            ],
            'date_of_expiration'    => gmdate( 'Y-m-d\TH:i:s.000-03:00', strtotime( '+30 minutes' ) ),
        ];

        $result = $this->post( '/v1/payments', $body );

        if ( is_wp_error( $result ) ) return $result;

        return [
            'payment_id'    => $result['id'],
            'status'        => $result['status'],
            'qr_code'       => $result['point_of_interaction']['transaction_data']['qr_code']        ?? '',
            'qr_code_base64'=> $result['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '',
            'ticket_url'    => $result['point_of_interaction']['transaction_data']['ticket_url']     ?? '',
            'expira_em'     => $result['date_of_expiration'] ?? '',
        ];
    }

    /* =========================================
       CRIAR PAGAMENTO CARTÃO DE CRÉDITO
       ========================================= */
    public function criar_pagamento_cartao( array $dados ): array|WP_Error {
        /*
         * $dados = [
         *   'valor'         => 150.00,
         *   'descricao'     => 'Rafting – 3 inscritos',
         *   'email'         => 'cliente@email.com',
         *   'cpf'           => '12345678909',
         *   'nome'          => 'João Silva',
         *   'token'         => 'card_token_from_frontend',
         *   'parcelas'      => 1,
         *   'issuer_id'     => '24',
         *   'pm_id'         => 'visa',
         *   'external_ref'  => 'RESERVA-123',
         * ]
         */
        $config    = tema_aventuras_payment_config();
        $notif_url = $config['notificacao_url'];

        $body = [
            'transaction_amount'    => (float) $dados['valor'],
            'description'           => sanitize_text_field( $dados['descricao'] ),
            'payment_method_id'     => sanitize_text_field( $dados['pm_id'] ),
            'installments'          => (int) ( $dados['parcelas'] ?? 1 ),
            'issuer_id'             => (int) ( $dados['issuer_id'] ?? 0 ),
            'token'                 => sanitize_text_field( $dados['token'] ),
            'notification_url'      => $notif_url,
            'external_reference'    => (string) $dados['external_ref'],
            'payer'                 => [
                'email'             => sanitize_email( $dados['email'] ),
                'identification'    => [
                    'type'          => 'CPF',
                    'number'        => preg_replace( '/\D/', '', $dados['cpf'] ),
                ],
            ],
        ];

        $result = $this->post( '/v1/payments', $body );

        if ( is_wp_error( $result ) ) return $result;

        return [
            'payment_id'        => $result['id'],
            'status'            => $result['status'],
            'status_detail'     => $result['status_detail'] ?? '',
            'aprovado'          => $result['status'] === 'approved',
        ];
    }

    /* =========================================
       CONSULTAR PAGAMENTO (Polling PIX)
       ========================================= */
    public function consultar_pagamento( string|int $payment_id ): array|WP_Error {
        $result = $this->get( '/v1/payments/' . intval( $payment_id ) );
        if ( is_wp_error( $result ) ) return $result;

        return [
            'payment_id'    => $result['id'],
            'status'        => $result['status'],
            'status_detail' => $result['status_detail'] ?? '',
            'aprovado'      => $result['status'] === 'approved',
            'valor'         => $result['transaction_amount'] ?? 0,
            'metodo'        => $result['payment_method_id'] ?? '',
        ];
    }

    /* =========================================
       RETORNAR PUBLIC KEY
       ========================================= */
    public function get_public_key(): string {
        return $this->public_key;
    }

    /* =========================================
       VERIFICAR CONEXÃO (teste de credenciais)
       ========================================= */
    public function testar_conexao(): array|WP_Error {
        return $this->get( '/v1/payment_methods' );
    }
}
