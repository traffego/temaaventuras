<?php
/**
 * Template Part: Blog Preview – Últimos posts
 *
 * @package TemaAventuras
 */

$posts_recentes = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
] );

if ( ! $posts_recentes->have_posts() ) return; // Não exibe se não há posts
?>

<!-- =========================================
     BLOG PREVIEW
     ========================================= -->
<section class="section" id="blog" aria-labelledby="blog-titulo">
    <div class="container">

        <div class="section-header animar-entrada flex-between">
            <div>
                <span class="section-header__eyebrow">✍️ <?php _e( 'Blog', 'temaaventuras' ); ?></span>
                <h2 id="blog-titulo" class="section-header__titulo" style="margin-bottom:0; text-align:left;">
                    <?php _e( 'Dicas e Aventuras', 'temaaventuras' ); ?>
                </h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn--ghost" id="ver-todos-posts">
                <?php _e( 'Ver Todos', 'temaaventuras' ); ?> →
            </a>
        </div>

        <div class="grid grid--3">
            <?php $delay = 1; while ( $posts_recentes->have_posts() ) : $posts_recentes->the_post(); ?>

            <article class="card card-post animar-entrada delay-<?php echo $delay++; ?>"
                     aria-label="<?php the_title_attribute(); ?>">

                <!-- Imagem -->
                <a href="<?php the_permalink(); ?>" class="card-post__img-link" tabindex="-1" aria-hidden="true">
                    <div class="card-post__img">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'aventura-thumb', [ 'class' => 'card-post__thumbnail', 'loading' => 'lazy', 'alt' => get_the_title() ] ); ?>
                        <?php else : ?>
                            <div class="card-post__placeholder" aria-hidden="true">✍️</div>
                        <?php endif; ?>
                    </div>
                </a>

                <div class="card-post__corpo">
                    <!-- Categoria -->
                    <?php
                    $cats = get_the_category();
                    if ( $cats ) : ?>
                    <span class="badge badge--verde card-post__cat">
                        <?php echo esc_html( $cats[0]->name ); ?>
                    </span>
                    <?php endif; ?>

                    <!-- Título -->
                    <h3 class="card-post__titulo">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>

                    <!-- Excerpt -->
                    <p class="card-post__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18 ); ?></p>

                    <!-- Meta -->
                    <div class="card-post__meta">
                        <span>📅 <?php echo get_the_date( 'd M Y' ); ?></span>
                        <a href="<?php the_permalink(); ?>" class="card-post__lermais">
                            <?php _e( 'Ler mais', 'temaaventuras' ); ?> →
                        </a>
                    </div>
                </div>

            </article>

            <?php endwhile; wp_reset_postdata(); ?>
        </div>

    </div>
</section>

<style>
.card-post__img {
    aspect-ratio: 16/9;
    overflow: hidden;
    background: var(--fundo-elevado);
}

.card-post__thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transicao-lenta);
}

.card-post:hover .card-post__thumbnail { transform: scale(1.05); }

.card-post__placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    background: var(--gradiente-hero);
}

.card-post__corpo {
    padding: var(--espaco-xl);
    display: flex;
    flex-direction: column;
    gap: var(--espaco-sm);
}

.card-post__cat { margin-bottom: var(--espaco-sm); }

.card-post__titulo {
    font-size: 1.2rem;
    line-height: 1.3;
}

.card-post__titulo a {
    color: var(--texto-primario);
    text-decoration: none;
    transition: color var(--transicao-rapida);
}

.card-post__titulo a:hover { color: var(--cor-secundaria); }

.card-post__excerpt {
    font-size: var(--tamanho-pequeno);
    color: var(--texto-muted);
    line-height: 1.6;
    margin: 0;
}

.card-post__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--texto-muted);
    margin-top: var(--espaco-sm);
    padding-top: var(--espaco-sm);
    border-top: 1px solid var(--borda-glass);
}

.card-post__lermais {
    color: var(--cor-primaria);
    font-weight: var(--peso-negrito);
    text-decoration: none;
    transition: color var(--transicao-rapida);
}

.card-post__lermais:hover { color: var(--cor-secundaria); }
</style>
