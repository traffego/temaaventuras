<?php
/**
 * Funções principais do Tema Aventuras
 *
 * @package TemaAventuras
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// =========================================
// CONSTANTES
// =========================================
define( 'TEMA_AVENTURAS_VERSION', '1.0.0' );
define( 'TEMA_AVENTURAS_DIR', get_template_directory() );
define( 'TEMA_AVENTURAS_URI', get_template_directory_uri() );

// =========================================
// INCLUDES
// =========================================
require_once TEMA_AVENTURAS_DIR . '/inc/custom-post-types.php';
require_once TEMA_AVENTURAS_DIR . '/inc/customizer.php';
require_once TEMA_AVENTURAS_DIR . '/inc/widgets.php';
require_once TEMA_AVENTURAS_DIR . '/inc/helpers.php';

// =========================================
// MÓDULO DE PAGAMENTOS
// =========================================
require_once TEMA_AVENTURAS_DIR . '/inc/payment/class-mercadopago.php';
require_once TEMA_AVENTURAS_DIR . '/inc/payment/admin-config.php';
require_once TEMA_AVENTURAS_DIR . '/inc/payment/class-reservas.php';
require_once TEMA_AVENTURAS_DIR . '/inc/payment/webhook.php';
require_once TEMA_AVENTURAS_DIR . '/inc/payment/emails.php';
require_once TEMA_AVENTURAS_DIR . '/inc/payment/ajax-checkout.php';

// =========================================
// SETUP DO TEMA
// =========================================
function tema_aventuras_setup() {

    // Internacionalização
    load_theme_textdomain( 'temaaventuras', TEMA_AVENTURAS_DIR . '/languages' );

    // Tag <title> gerenciada pelo WP
    add_theme_support( 'title-tag' );

    // Imagens destacadas em posts/páginas
    add_theme_support( 'post-thumbnails' );

    // Logo personalizado no Customizer
    add_theme_support( 'custom-logo', [
        'height'               => 100,
        'width'                => 400,
        'flex-height'          => true,
        'flex-width'           => true,
        'unlink-homepage-logo' => false,
    ] );

    // HTML5 semântico
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ] );

    // Suporte a cores do editor de blocos (Gutenberg)
    add_theme_support( 'editor-color-palette', [
        [ 'name' => __( 'Verde Brasil', 'temaaventuras' ),   'slug' => 'verde',     'color' => '#009C3B' ],
        [ 'name' => __( 'Amarelo Brasil', 'temaaventuras' ), 'slug' => 'amarelo',   'color' => '#FFDF00' ],
        [ 'name' => __( 'Azul Brasil', 'temaaventuras' ),    'slug' => 'azul',      'color' => '#002776' ],
        [ 'name' => __( 'Branco', 'temaaventuras' ),         'slug' => 'branco',    'color' => '#FFFFFF' ],
        [ 'name' => __( 'Escuro', 'temaaventuras' ),         'slug' => 'escuro',    'color' => '#0A110D' ],
    ] );

    add_theme_support( 'custom-background' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );

    // Tamanhos de imagem personalizados
    add_image_size( 'aventura-card',    600,  800, true  );
    add_image_size( 'aventura-banner',  1920, 800, true  );
    add_image_size( 'aventura-galeria', 800,  600, true  );
    add_image_size( 'aventura-thumb',   400,  300, true  );

    // Registro de menus de navegação
    register_nav_menus( [
        'menu-principal' => __( 'Menu Principal', 'temaaventuras' ),
        'menu-footer'    => __( 'Menu Rodapé', 'temaaventuras' ),
        'menu-mobile'    => __( 'Menu Mobile', 'temaaventuras' ),
    ] );

    // ---- ELEMENTOR COMPATIBILITY ----
    // Suporte ao sistema de locações do Elementor Pro (Header/Footer/etc)
    add_theme_support( 'elementor-location', [ 'header', 'footer', 'archive', 'single' ] );
}
add_action( 'after_setup_theme', 'tema_aventuras_setup' );

// =========================================
// REGISTRO DE LOCAÇÕES ELEMENTOR
// =========================================
function tema_aventuras_register_elementor_locations( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'tema_aventuras_register_elementor_locations' );

// =========================================
// ENQUEUE DE SCRIPTS E ESTILOS
// =========================================
function tema_aventuras_scripts() {
    $ver = TEMA_AVENTURAS_VERSION;

    // === ESTILOS ===
    wp_enqueue_style(
        'tema-aventuras-style',
        get_stylesheet_uri(),
        [],
        $ver
    );

    // Pré-conectar Google Fonts para performance
    wp_enqueue_style(
        'google-fonts-preconnect',
        'https://fonts.googleapis.com',
        [],
        null
    );

    // === SCRIPTS ===
    wp_enqueue_script(
        'tema-aventuras-main',
        TEMA_AVENTURAS_URI . '/assets/js/main.js',
        [],
        $ver,
        true // no footer
    );

    wp_enqueue_script(
        'tema-aventuras-navbar',
        TEMA_AVENTURAS_URI . '/assets/js/navbar.js',
        [],
        $ver,
        true
    );

    wp_enqueue_script(
        'tema-aventuras-counter',
        TEMA_AVENTURAS_URI . '/assets/js/counter.js',
        [],
        $ver,
        true
    );

    wp_enqueue_script(
        'tema-aventuras-gallery',
        TEMA_AVENTURAS_URI . '/assets/js/gallery.js',
        [],
        $ver,
        true
    );

    // Comentários com threads
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Passar variáveis PHP → JS
    wp_localize_script( 'tema-aventuras-main', 'temaAventurasData', [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'tema_aventuras_nonce' ),
        'homeUrl'   => home_url(),
        'themeUri'  => TEMA_AVENTURAS_URI,
        'isLoggedIn'=> is_user_logged_in(),
    ] );
}
add_action( 'wp_enqueue_scripts', 'tema_aventuras_scripts' );

// =========================================
// INLINE CSS DINÂMICO (CUSTOMIZER → :root)
// =========================================
function tema_aventuras_inline_css() {
    $cor_primaria   = get_theme_mod( 'cor_primaria',   '#009C3B' );
    $cor_secundaria = get_theme_mod( 'cor_secundaria', '#FFDF00' );
    $cor_terciaria  = get_theme_mod( 'cor_terciaria',  '#002776' );
    $cor_fundo      = get_theme_mod( 'cor_fundo',      '#0A110D' );
    $cor_texto      = get_theme_mod( 'cor_texto',      '#F4F9F5' );

    $css = "
    :root {
        --cor-primaria:    {$cor_primaria};
        --cor-secundaria:  {$cor_secundaria};
        --cor-terciaria:   {$cor_terciaria};
        --fundo-base:      {$cor_fundo};
        --texto-primario:  {$cor_texto};
        --fundo-glass:     {$cor_primaria}14;
        --borda-glass:     {$cor_primaria}33;
        --sombra-glow:     0 0 30px {$cor_primaria}4D;
        --sombra-glow-sec: 0 0 30px {$cor_secundaria}4D;
        --gradiente-cta:   linear-gradient(90deg, {$cor_primaria}, {$cor_terciaria});
        --gradiente-botao: linear-gradient(135deg, {$cor_secundaria}, {$cor_primaria});
        --gradiente-hero:  linear-gradient(135deg, {$cor_primaria} 0%, {$cor_terciaria} 100%);
    }
    ";

    wp_add_inline_style( 'tema-aventuras-style', $css );
}
add_action( 'wp_enqueue_scripts', 'tema_aventuras_inline_css', 20 );

// =========================================
// ELEMENTOR: CSS dinâmico no editor também
// =========================================
function tema_aventuras_elementor_preview_css() {
    if ( ! defined( 'ELEMENTOR_VERSION' ) ) return;
    tema_aventuras_inline_css();
}
add_action( 'elementor/preview/enqueue_styles', 'tema_aventuras_elementor_preview_css' );

// =========================================
// BODY CLASSES EXTRAS
// =========================================
function tema_aventuras_body_classes( $classes ) {
    if ( is_singular() ) {
        $classes[] = 'pagina-singular';
    }
    if ( defined( 'ELEMENTOR_VERSION' ) ) {
        $classes[] = 'elementor-ativo';
    }
    return $classes;
}
add_filter( 'body_class', 'tema_aventuras_body_classes' );

// =========================================
// ATUALIZAR LARGURA DO CONTEÚDO (ELEMENTOR)
// =========================================
function tema_aventuras_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'tema_aventuras_content_width', 1400 );
}
add_action( 'after_setup_theme', 'tema_aventuras_content_width', 0 );

// =========================================
// ELEMENTOR: SUPORTE A TEMPLATE FULL WIDTH
// =========================================
function tema_aventuras_elementor_locations_support( $location, $args ) {
    return true;
}

// Página fullwidth no Elementor Canvas
function tema_aventuras_page_templates( $templates ) {
    $templates['page-templates/page-fullwidth.php'] = __( 'Full Width (Elementor)', 'temaaventuras' );
    $templates['page-templates/page-canvas.php']    = __( 'Canvas (Elementor)', 'temaaventuras' );
    return $templates;
}
add_filter( 'theme_page_templates', 'tema_aventuras_page_templates' );

// =========================================
// ELEMENTOR: ESCONDER HEADER/FOOTER
// quando Elementor Pro controla o local
// =========================================
function tema_aventuras_elementor_has_location( $location ) {
    if ( function_exists( 'elementor_theme_do_location' ) ) {
        return elementor_theme_do_location( $location );
    }
    return false;
}

// =========================================
// REMOVER EMOJIS (PERFORMANCE)
// =========================================
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// =========================================
// ADICIONAR SUPORTE AO ELEMENTOR HELLO THEME
// (compatibilidade com libs do Elementor)
// =========================================
add_theme_support( 'elementor-full-page-template' );

// =========================================
// PERMITIR SVG NAS MÍDIAS
// =========================================
function tema_aventuras_allow_svg( $mimes ) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'tema_aventuras_allow_svg' );

// =========================================
// EXCERPT PERSONALIZADO
// =========================================
function tema_aventuras_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'tema_aventuras_excerpt_length' );

function tema_aventuras_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'tema_aventuras_excerpt_more' );
