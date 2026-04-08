<?php
/**
 * Helpers – Funções utilitárias do tema
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retorna dados do Customizer com fallback
 */
function ta_get( $key, $default = '' ) {
    return get_theme_mod( $key, $default );
}

/**
 * Exibe estrelas de avaliação
 */
function ta_estrelas( $nota = 5 ) {
    $nota = intval( $nota );
    $html = '<div class="estrelas" aria-label="' . $nota . ' de 5 estrelas">';
    for ( $i = 1; $i <= 5; $i++ ) {
        $html .= $i <= $nota ? '★' : '☆';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Badge de nível de dificuldade
 */
function ta_nivel_badge( $nivel ) {
    $mapa = [
        'facil'   => [ 'label' => 'Fácil',   'class' => 'badge--verde' ],
        'medio'   => [ 'label' => 'Médio',   'class' => 'badge--amarelo' ],
        'dificil' => [ 'label' => 'Difícil', 'class' => 'badge--azul' ],
        'extremo' => [ 'label' => 'Extremo', 'class' => 'badge--vermelho' ],
    ];
    $info = $mapa[ $nivel ] ?? $mapa['facil'];
    return '<span class="badge ' . esc_attr( $info['class'] ) . '">' . esc_html( $info['label'] ) . '</span>';
}

/**
 * Formata preço em BRL
 */
function ta_preco( $valor, $prefix = 'R$ ' ) {
    return $prefix . number_format( floatval( $valor ), 2, ',', '.' );
}

/**
 * Verifica se é uma página gerenciada pelo Elementor
 */
function ta_is_elementor_page() {
    if ( ! defined( 'ELEMENTOR_VERSION' ) ) return false;
    $page_id = get_queried_object_id();
    return \Elementor\Plugin::$instance->documents->get( $page_id ) &&
           \Elementor\Plugin::$instance->documents->get( $page_id )->is_built_with_elementor();
}

/**
 * Renderiza local pelo Elementor Pro ou fallback
 */
function ta_elementor_location_or( $location, $fallback_callback ) {
    if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location ) ) {
        return;
    }
    if ( is_callable( $fallback_callback ) ) {
        call_user_func( $fallback_callback );
    }
}

/**
 * Link do WhatsApp com mensagem pré-definida
 */
function ta_whatsapp_link( $mensagem = '' ) {
    $numero = ta_get( 'empresa_whatsapp', '5511999999999' );
    $msg    = urlencode( $mensagem ?: 'Olá! Gostaria de saber mais sobre os pacotes de aventura.' );
    return "https://wa.me/{$numero}?text={$msg}";
}

/**
 * Imagem destacada com fallback para placeholder
 */
function ta_thumbnail( $post_id = null, $size = 'aventura-card', $classes = '' ) {
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail( $post_id, $size, [ 'class' => $classes, 'loading' => 'lazy' ] );
    }
    return '<img src="' . TEMA_AVENTURAS_URI . '/assets/images/placeholder.jpg" class="' . esc_attr( $classes ) . '" alt="" loading="lazy" />';
}

/**
 * Retorna atividades mais recentes
 */
function ta_get_atividades( $limit = 6, $nivel = '' ) {
    $args = [
        'post_type'      => 'atividade',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ( $nivel ) {
        $args['meta_query'] = [
            [ 'key' => '_atividade_nivel', 'value' => $nivel, 'compare' => '=' ],
        ];
    }
    return new WP_Query( $args );
}

/**
 * Retorna pacotes ativos
 */
function ta_get_pacotes( $limit = 3 ) {
    return new WP_Query( [
        'post_type'      => 'pacote',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );
}

/**
 * Retorna depoimentos aleatórios
 */
function ta_get_depoimentos( $limit = 6 ) {
    return new WP_Query( [
        'post_type'      => 'depoimento',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'rand',
    ] );
}

/**
 * Tempo de leitura estimado (minutos)
 */
function ta_reading_time( $post_id = null ): int {
    $content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
    $words   = str_word_count( wp_strip_all_tags( $content ) );
    return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * URL da página de checkout para uma atividade
 */
function ta_checkout_url( int $atividade_id ): string {
    $page = get_page_by_path( 'reservar' ) ?? get_page_by_path( 'checkout' );
    $base = $page ? get_permalink( $page ) : home_url( '/reservar/' );
    $args = [ 'id' => $atividade_id ];
    return add_query_arg( $args, $base );
}

/**
 * URL da página "Minha Reserva"
 */
function ta_minha_reserva_url(): string {
    $page = get_page_by_path( 'minha-reserva' );
    return $page ? get_permalink( $page ) : home_url( '/minha-reserva/' );
}

/**
 * Verifica se a URL é do YouTube
 */
function ta_is_youtube_url( $url ) {
    return ( strpos( $url, 'youtube.com/' ) !== false || strpos( $url, 'youtu.be/' ) !== false );
}

/**
 * Converte link comum do YouTube em link Embed
 */
function ta_get_youtube_embed_url( $url ) {
    // Regex para capturar ID do vídeo do YouTube
    preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $match );
    if ( isset( $match[1] ) ) {
        $id = $match[1];
        // Retorna URL embed com parâmetros de autoplay, mute e loop infinito
        return "https://www.youtube.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&showinfo=0&rel=0&modestbranding=1&iv_load_policy=3&enablejsapi=1";
    }
    return $url;
}
