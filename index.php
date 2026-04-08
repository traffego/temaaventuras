<?php
/**
 * index.php – Template fallback principal
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" class="section container" role="main" style="padding-top: calc(var(--altura-nav) + var(--espaco-3xl));">

    <?php if ( have_posts() ) : ?>

        <div class="section-header texto-centro">
            <h1 class="section-header__titulo"><?php _e( 'Blog & Notícias', 'temaaventuras' ); ?></h1>
        </div>

        <div class="grid grid--auto-fit-md">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('card card-post'); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                        <div style="aspect-ratio:16/9;overflow:hidden;">
                            <?php the_post_thumbnail('aventura-thumb', ['class'=>'card-post__thumbnail','loading'=>'lazy']); ?>
                        </div>
                    </a>
                    <?php endif; ?>
                    <div class="card-post__corpo">
                        <h2 class="card-post__titulo" style="font-size:1.3rem;">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="card-post__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        <div class="card-post__meta">
                            <span>📅 <?php echo get_the_date('d M Y'); ?></span>
                            <a href="<?php the_permalink(); ?>" class="card-post__lermais"><?php _e('Ler mais', 'temaaventuras'); ?> →</a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div style="margin-top:var(--espaco-3xl);">
            <?php the_posts_pagination(['prev_text' => '← Anterior', 'next_text' => 'Próximo →']); ?>
        </div>

    <?php else : ?>
        <div class="texto-centro" style="padding:var(--espaco-4xl) 0;">
            <p style="font-size:4rem;">🌿</p>
            <h1><?php _e('Nenhum conteúdo encontrado', 'temaaventuras'); ?></h1>
            <p><?php _e('Ainda não há posts publicados. Volte em breve!', 'temaaventuras'); ?></p>
            <a href="<?php echo home_url('/'); ?>" class="btn btn--primario" style="margin-top:var(--espaco-xl);"><?php _e('Ir para o Início', 'temaaventuras'); ?></a>
        </div>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
