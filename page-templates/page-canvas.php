<?php
/**
 * Template Name: Canvas (Elementor)
 * Template Post Type: page
 *
 * Template absolutamente vazio – sem header, footer ou navbar.
 * Usado pelo Elementor Canvas para landing pages totalmente customizadas.
 *
 * @package TemaAventuras
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('elementor-page elementor-page-canvas'); ?>>
<?php wp_body_open(); ?>

<main id="conteudo-principal" role="main">
    <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
