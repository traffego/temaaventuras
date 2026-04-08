<?php
/**
 * Template Name: Full Width (Elementor)
 * Template Post Type: page
 *
 * Template para páginas sem header/footer nativos.
 * Ideal para páginas construídas inteiramente no Elementor.
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">
    <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
