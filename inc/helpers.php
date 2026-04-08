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
