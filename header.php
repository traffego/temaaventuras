<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#009C3B">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link sr-only" href="#conteudo-principal">
    <?php _e( 'Ir para o conteúdo principal', 'temaaventuras' ); ?>
</a>

<?php
// Widget: barra de avisos antes do header
if ( is_active_sidebar( 'antes-hero' ) ) {
    echo '<div class="barra-aviso">';
    dynamic_sidebar( 'antes-hero' );
    echo '</div>';
}

// Tenta usar o Header do Elementor Pro; senão usa o header.php nativo
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) :
?>

<!-- =========================================
     NAVBAR
     ========================================= -->
<header class="navbar" id="navbar" role="banner">
    <div class="container navbar__inner">

        <!-- LOGO -->
        <div class="navbar__logo">
            <?php if ( has_custom_logo() ) : ?>
                <div class="navbar__logo-wrapper">
                    <?php the_custom_logo(); ?>
                </div>
            <?php endif; ?>
            
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="navbar__logo-texto" aria-label="<?php bloginfo( 'name' ); ?> – Página Inicial" style="text-decoration:none;">
                <?php
                $nome = ta_get( 'empresa_nome', get_bloginfo( 'name' ) );
                $partes = explode( ' ', $nome, 2 );
                echo esc_html( $partes[0] );
                if ( isset( $partes[1] ) ) {
                    echo ' <span>' . esc_html( $partes[1] ) . '</span>';
                }
                ?>
            </a>
        </div>

        <!-- MENU DESKTOP -->
        <nav class="navbar__menu" id="menu-desktop" role="navigation" aria-label="Menu Principal">
            <?php
            wp_nav_menu( [
                'theme_location' => 'menu-principal',
                'menu_class'     => 'navbar__menu',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'walker'         => new Walker_Nav_Menu(),
                'fallback_cb'    => function() {
                    echo '<li class="navbar__item"><a href="' . esc_url( home_url( '/' ) ) . '">Início</a></li>';
                    echo '<li class="navbar__item"><a href="' . esc_url( home_url( '/#atividades' ) ) . '">Atividades</a></li>';
                    if ( ta_get('mostrar_pacotes', true) ) {
                        echo '<li class="navbar__item"><a href="' . esc_url( home_url( '/#pacotes' ) ) . '">Pacotes</a></li>';
                    }
                    $p_guias  = get_page_by_path('guias')  ?: get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-guias.php'])[0]  ?? null;
                    $p_videos = get_page_by_path('videos') ?: get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-videos.php'])[0] ?? null;
                    $p_blog   = get_page_by_path('blog')   ?: get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-blog.php'])[0]   ?? null;
                    if ( $p_guias )  echo '<li class="navbar__item"><a href="' . esc_url( get_permalink($p_guias) )  . '">Guias</a></li>';
                    if ( $p_videos ) echo '<li class="navbar__item"><a href="' . esc_url( get_permalink($p_videos) ) . '">Vídeos</a></li>';
                    if ( $p_blog )   echo '<li class="navbar__item"><a href="' . esc_url( get_permalink($p_blog) )   . '">Blog</a></li>';
                    echo '<li class="navbar__item"><a href="' . esc_url( home_url( '/#contato' ) ) . '">Contato</a></li>';
                },
            ] );
            ?>
        </nav>

        <!-- CTA + HAMBURGER -->
        <div class="navbar__cta">
            <?php
            $wa_link = ta_whatsapp_link( 'Olá! Quero saber mais sobre os pacotes de aventura.' );
            if ( ta_get('mostrar_wa_header', true) ) :
            ?>
            <a href="<?php echo esc_url( $wa_link ); ?>"
               class="btn btn--primario btn--pequeno"
               id="navbar-whatsapp-btn"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Falar no WhatsApp">
                📲 WhatsApp
            </a>
            <?php endif; ?>

            <button class="navbar__hamburger"
                    id="navbar-hamburger"
                    aria-label="<?php _e( 'Abrir menu', 'temaaventuras' ); ?>"
                    aria-expanded="false"
                    aria-controls="menu-mobile">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

    </div><!-- /.navbar__inner -->
</header>

<!-- MENU MOBILE -->
<nav class="navbar__mobile" id="menu-mobile" aria-label="Menu Mobile" aria-hidden="true">
    <?php
    wp_nav_menu( [
        'theme_location' => 'menu-principal',
        'container'      => false,
        'items_wrap'     => '%3$s',
        'item_spacing'   => 'preserve',
        'fallback_cb'    => function() {
            $links = [ 'Início' => '/', 'Atividades' => '/#atividades', 'Contato' => '/#contato' ];
            if ( ta_get('mostrar_pacotes', true) ) {
                $links = [ 'Início' => '/', 'Atividades' => '/#atividades', 'Pacotes' => '/#pacotes', 'Contato' => '/#contato' ];
            }
            $p_guias  = get_page_by_path('guias')  ?: (get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-guias.php'])[0]  ?? null);
            $p_videos = get_page_by_path('videos') ?: (get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-videos.php'])[0] ?? null);
            $p_blog   = get_page_by_path('blog')   ?: (get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-blog.php'])[0]   ?? null);
            if ( $p_guias )  $links['Guias']  = get_permalink($p_guias);
            if ( $p_videos ) $links['Vídeos'] = get_permalink($p_videos);
            if ( $p_blog )   $links['Blog']   = get_permalink($p_blog);
            foreach ( $links as $label => $href ) {
                $url = str_starts_with($href, 'http') ? $href : home_url($href);
                echo '<li class="navbar__item"><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
            }
        },
    ] );
    ?>
    <?php if ( ta_get('mostrar_wa_header', true) ) : ?>
    <a href="<?php echo esc_url( $wa_link ); ?>"
       class="btn btn--primario btn--grande"
       target="_blank"
       rel="noopener noreferrer">
        📲 Falar no WhatsApp
    </a>
    <?php endif; ?>
</nav>

<?php endif; // end elementor header check ?>
