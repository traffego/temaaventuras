<?php
/**
 * search.php – Resultados de busca
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main" style="padding-top: var(--altura-nav);">
    <section class="section">
        <div class="container">

            <div class="section-header">
                <h1 class="section-header__titulo">
                    <?php
                    $term = get_search_query();
                    if ($term) printf(__('Resultados para: <span style="color:var(--cor-secundaria)">%s</span>', 'temaaventuras'), esc_html($term));
                    else _e('Pesquisa', 'temaaventuras');
                    ?>
                </h1>
                <p class="section-header__subtitulo" style="margin-top:var(--espaco-md);">
                    <?php printf(__('%d resultado(s) encontrado(s)', 'temaaventuras'), $wp_query->found_posts); ?>
                </p>
            </div>

            <!-- Nova busca -->
            <div style="max-width:500px; margin-inline:auto; margin-bottom:var(--espaco-3xl);">
                <?php get_search_form(); ?>
            </div>

            <?php if (have_posts()) : ?>
                <div class="grid grid--auto-fit-md">
                    <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('card card-post'); ?>>
                        <?php if (has_post_thumbnail()): ?>
                        <div style="aspect-ratio:16/9;overflow:hidden;">
                            <?php the_post_thumbnail('aventura-thumb',['class'=>'card-post__thumbnail','loading'=>'lazy']); ?>
                        </div>
                        <?php endif; ?>
                        <div class="card-post__corpo">
                            <span class="badge badge--verde" style="margin-bottom:var(--espaco-sm);">
                                <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? 'Post'); ?>
                            </span>
                            <h2 class="card-post__titulo"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="card-post__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            <a href="<?php the_permalink(); ?>" class="card-post__lermais"><?php _e('Ver mais →', 'temaaventuras'); ?></a>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>
                <div style="margin-top:var(--espaco-3xl);">
                    <?php the_posts_pagination(); ?>
                </div>
            <?php else : ?>
                <div class="texto-centro" style="padding:var(--espaco-4xl) 0;">
                    <p style="font-size:3rem;">🔍</p>
                    <h2><?php _e('Nenhum resultado encontrado.', 'temaaventuras'); ?></h2>
                    <p><?php _e('Tente buscar por rafting, trilha, tirolesa ou canionismo.', 'temaaventuras'); ?></p>
                    <a href="<?php echo home_url('/atividades'); ?>" class="btn btn--primario" style="margin-top:var(--espaco-xl);"><?php _e('Ver Nossas Atividades', 'temaaventuras'); ?></a>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>
