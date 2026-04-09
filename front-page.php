<?php
/**
 * Front Page – Página Inicial
 * Usado quando uma página estática está definida como home no WP
 * ou como fallback quando o Elementor não controla a página.
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">

<?php
// Se o Elementor controla esta página, renderiza e sai
if ( ta_is_elementor_page() ) {
    the_content();
    get_footer();
    return;
}
?>

<?php get_template_part( 'template-parts/hero' ); ?>
<?php get_template_part( 'template-parts/stats-counter' ); ?>
<?php get_template_part( 'template-parts/activities-grid' ); ?>
<?php 
if ( ta_get('mostrar_pacotes', true) ) {
    get_template_part( 'template-parts/packages' );
}
?>
<?php get_template_part( 'template-parts/gallery-section' ); ?>
<?php if ( ta_get('mostrar_depoimentos', true) ) : ?>
<?php get_template_part( 'template-parts/testimonials' ); ?>
<?php endif; ?>


</main>

<?php get_footer(); ?>
