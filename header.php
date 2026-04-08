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
                <?php the_custom_logo(); ?>
            <?php else : ?>
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
            <?php endif; ?>
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
                    echo '<li class="navbar__item"><a href="#atividades">Atividades</a></li>';
                    echo '<li class="navbar__item"><a href="#pacotes">Pacotes</a></li>';
                    echo '<li class="navbar__item"><a href="#contato">Contato</a></li>';
                },
            ] );
            ?>
        </nav>

        <!-- CTA + HAMBURGER -->
        <div class="navbar__cta">
            <?php
            $wa_link = ta_whatsapp_link( 'Olá! Quero saber mais sobre os pacotes de aventura.' );
            ?>
            <a href="<?php echo esc_url( $wa_link ); ?>"
               class="btn btn--primario btn--pequeno"
               id="navbar-whatsapp-btn"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Falar no WhatsApp">
                📲 WhatsApp
            </a>

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
            $links = [ 'Início' => '/', 'Atividades' => '#atividades', 'Pacotes' => '#pacotes', 'Galeria' => '#galeria', 'Contato' => '#contato' ];
            foreach ( $links as $label => $href ) {
                echo '<li class="navbar__item"><a href="' . esc_url( home_url( $href ) ) . '">' . esc_html( $label ) . '</a></li>';
            }
        },
    ] );
    ?>
    <a href="<?php echo esc_url( $wa_link ); ?>"
       class="btn btn--primario btn--grande"
       target="_blank"
       rel="noopener noreferrer">
        📲 Falar no WhatsApp
    </a>
</nav>

<?php endif; // end elementor header check ?>
