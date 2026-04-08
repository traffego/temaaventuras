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

<style>
.page-banner {
    position: relative;
    min-height: 320px;
    display: flex;
    align-items: center;
    background: var(--gradiente-hero);
    overflow: hidden;
    margin-top: var(--altura-nav);
}

.page-banner__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.4;
}

.page-banner__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(0,39,118,0.8), rgba(0,156,59,0.6));
}

.page-banner__conteudo {
    position: relative;
    z-index: 2;
    padding-block: var(--espaco-3xl);
}

.page-banner__titulo {
    font-size: clamp(2rem,5vw,3.5rem);
    margin-bottom: var(--espaco-md);
}

.breadcrumb {
    font-size: var(--tamanho-pequeno);
    color: rgba(255,255,255,0.7);
}

.breadcrumb a {
    color: var(--cor-secundaria);
    text-decoration: none;
}

.breadcrumb a:hover { text-decoration: underline; }

.wp-content {
    font-size: 1.05rem;
    line-height: 1.9;
    color: var(--texto-secundario);
}

.wp-content h2, .wp-content h3, .wp-content h4 {
    color: var(--texto-primario);
    margin-top: var(--espaco-2xl);
    margin-bottom: var(--espaco-md);
}

.wp-content p { margin-bottom: var(--espaco-lg); }

.wp-content ul, .wp-content ol {
    list-style: initial;
    padding-left: var(--espaco-xl);
    margin-bottom: var(--espaco-lg);
    color: var(--texto-secundario);
}

.wp-content li { margin-bottom: var(--espaco-sm); }

.wp-content img {
    border-radius: var(--raio-lg);
    margin-block: var(--espaco-xl);
}

.wp-content blockquote {
    border-left: 4px solid var(--cor-primaria);
    padding-left: var(--espaco-xl);
    margin: var(--espaco-2xl) 0;
    font-style: italic;
    color: var(--texto-muted);
}

.wp-content a {
    color: var(--cor-primaria);
    text-decoration: underline;
}
</style>
