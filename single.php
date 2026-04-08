<?php
/**
 * single.php – Post singular (blog)
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

    <!-- Post Banner -->
    <div class="page-banner" style="min-height:400px;" role="banner">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'aventura-banner', [ 'class' => 'page-banner__img', 'loading' => 'eager', 'alt' => '' ] ); ?>
        <?php endif; ?>
        <div class="container page-banner__conteudo">
            <?php
            $cats = get_the_category();
            if ( $cats ) : ?>
            <div style="margin-bottom:var(--espaco-md);">
                <span class="badge badge--verde"><?php echo esc_html( $cats[0]->name ); ?></span>
            </div>
            <?php endif; ?>
            <h1 class="page-banner__titulo" style="font-size: clamp(1.8rem,4vw,3rem);"><?php the_title(); ?></h1>
            <div class="post-meta" style="margin-top:var(--espaco-md);">
                <span>📅 <?php echo get_the_date('d \d\e F \d\e Y'); ?></span>
                <span style="margin-inline:var(--espaco-md)">•</span>
                <span>✍️ <?php the_author(); ?></span>
                <span style="margin-inline:var(--espaco-md)">•</span>
                <span>⏱ <?php
                $palavras = str_word_count( strip_tags( get_the_content() ) );
                $minutos  = max(1, round( $palavras / 200 ));
                printf( _n('%d min de leitura', '%d min de leitura', $minutos, 'temaaventuras'), $minutos );
                ?></span>
            </div>
        </div>
    </div>

    <!-- Conteúdo do post -->
    <section class="section">
        <div class="container--estreito">

            <article id="post-<?php the_ID(); ?>" <?php post_class('entrada-post'); ?>>
                <div class="entry-content wp-content">
                    <?php the_content(); ?>
                </div>

                <!-- Tags -->
                <?php $tags = get_the_tags();
                if ( $tags ) : ?>
                <div class="post-tags" style="margin-top:var(--espaco-2xl); display:flex; gap:var(--espaco-sm); flex-wrap:wrap; align-items:center;">
                    <span style="font-size:var(--tamanho-pequeno);color:var(--texto-muted);">🏷 Tags:</span>
                    <?php foreach ( $tags as $tag ) : ?>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="badge badge--verde" style="text-decoration:none;">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Compartilhar -->
                <div class="post-share" style="margin-top:var(--espaco-2xl); padding:var(--espaco-xl); background:var(--fundo-card); border-radius:var(--raio-xl); border:1px solid var(--borda-glass);">
                    <p style="font-weight:var(--peso-negrito); margin-bottom:var(--espaco-md); color:var(--texto-primario);">Compartilhar esta aventura:</p>
                    <div style="display:flex; gap:var(--espaco-md); flex-wrap:wrap;">
                        <?php $url = urlencode(get_permalink()); $titulo = urlencode(get_the_title()); ?>
                        <a href="https://wa.me/?text=<?php echo $titulo . '+' . $url; ?>" class="btn btn--primario btn--pequeno" target="_blank" rel="noopener noreferrer">📲 WhatsApp</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" class="btn btn--ghost btn--pequeno" target="_blank" rel="noopener noreferrer">Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $titulo; ?>" class="btn btn--ghost btn--pequeno" target="_blank" rel="noopener noreferrer">Twitter/X</a>
                    </div>
                </div>
            </article>

            <!-- Posts relacionados -->
            <?php
            $relacionados = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'post__not_in'   => [get_the_ID()],
                'category__in'   => wp_get_post_categories(get_the_ID()),
                'orderby'        => 'rand',
            ]);
            if ( $relacionados->have_posts() ) : ?>
            <div style="margin-top:var(--espaco-4xl);">
                <h2 style="font-size:1.8rem; margin-bottom:var(--espaco-2xl);"><?php _e('Continue Explorando', 'temaaventuras'); ?></h2>
                <div class="grid grid--3">
                    <?php while ($relacionados->have_posts()) : $relacionados->the_post(); ?>
                    <article class="card card-post">
                        <?php if (has_post_thumbnail()) : ?>
                        <div style="aspect-ratio:16/9;overflow:hidden;">
                            <?php the_post_thumbnail('aventura-thumb',['class'=>'card-post__thumbnail','loading'=>'lazy']); ?>
                        </div>
                        <?php endif; ?>
                        <div class="card-post__corpo">
                            <h3 class="card-post__titulo" style="font-size:1rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <a href="<?php the_permalink(); ?>" class="card-post__lermais"><?php _e('Ler mais →','temaaventuras'); ?></a>
                        </div>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Comentários -->
            <?php if ( comments_open() || get_comments_number() ) : ?>
                <div style="margin-top:var(--espaco-4xl);">
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <?php endwhile; ?>

</main>

<?php get_footer(); ?>
