<?php
/**
 * page.php – Template genérico de páginas
 * Detecta se o Elementor está no controle antes de renderizar o layout nativo.
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">

<?php if ( ta_is_elementor_page() ) : ?>
    <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
<?php else : ?>

    <!-- Banner da página -->
    <div class="page-banner" role="banner" aria-label="<?php the_title_attribute(); ?>">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'aventura-banner', [ 'class' => 'page-banner__img', 'loading' => 'eager', 'alt' => '' ] ); ?>
        <?php endif; ?>
        <div class="container page-banner__conteudo">
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php
            // Breadcrumbs simples
            echo '<nav class="breadcrumb" aria-label="Navegação estrutural">';
            echo '<a href="' . esc_url( home_url('/') ) . '">' . __('Início','temaaventuras') . '</a>';
            echo '<span aria-hidden="true"> / </span>';
            echo '<span aria-current="page">' . get_the_title() . '</span>';
            echo '</nav>';
            ?>
        </div>
    </div>

    <!-- Conteúdo -->
    <section class="section">
        <div class="container--estreito">
            <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('pagina-conteudo'); ?>>
                <div class="entry-content wp-content">
                    <?php the_content(); ?>
                </div>
                <?php
                wp_link_pages([
                    'before' => '<div class="page-links">' . __('Páginas:', 'temaaventuras'),
                    'after'  => '</div>',
                ]);
                ?>
            </article>
            <?php endwhile; ?>
        </div>
    </section>

<?php endif; ?>

</main>

<?php get_footer(); ?>


