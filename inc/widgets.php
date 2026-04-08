<?php
/**
 * Widgets – Registro de áreas de widget
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

function tema_aventuras_widgets_init() {

    $config_base = [
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget__titulo">',
        'after_title'   => '</h3>',
    ];

    register_sidebar( array_merge( $config_base, [
        'name' => __( 'Barra Lateral Principal', 'temaaventuras' ),
        'id'   => 'sidebar-principal',
        'description' => __( 'Barra lateral para páginas de blog e conteúdo.', 'temaaventuras' ),
    ] ) );

    register_sidebar( array_merge( $config_base, [
        'name' => __( 'Rodapé – Coluna 1', 'temaaventuras' ),
        'id'   => 'footer-col-1',
    ] ) );

    register_sidebar( array_merge( $config_base, [
        'name' => __( 'Rodapé – Coluna 2', 'temaaventuras' ),
        'id'   => 'footer-col-2',
    ] ) );

    register_sidebar( array_merge( $config_base, [
        'name' => __( 'Rodapé – Coluna 3', 'temaaventuras' ),
        'id'   => 'footer-col-3',
    ] ) );

    register_sidebar( array_merge( $config_base, [
        'name' => __( 'Antes do Hero (Notificação)', 'temaaventuras' ),
        'id'   => 'antes-hero',
        'description' => __( 'Exibido acima do hero na página inicial. Ideal para banners de promoção.', 'temaaventuras' ),
    ] ) );
}
add_action( 'widgets_init', 'tema_aventuras_widgets_init' );
