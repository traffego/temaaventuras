<?php
/**
 * Template Name: Página do Blog
 * Template Post Type: page
 *
 * @package TemaAventuras
 */

get_header();

// Paginação
$paged = get_query_var('paged') ?: 1;

// Filtro de categoria
$cat_slug = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';

$args = [
    'post_type'      => 'post',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
];
if ($cat_slug) {
    $args['category_name'] = $cat_slug;
}

$posts_query = new WP_Query($args);

// Post mais recente destaque (sem filtro)
$destaque_query = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
$post_destaque = $destaque_query->have_posts() ? $destaque_query->posts[0] : null;
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if (has_post_thumbnail()) the_post_thumbnail('aventura-banner', ['class' => 'page-banner__img', 'loading' => 'eager', 'alt' => '']); ?>
        <div class="container page-banner__conteudo">
            <span class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">📝 <?php _e('Aventuras & Dicas', 'temaaventuras'); ?></span>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php if (get_the_excerpt()) : ?>
                <p style="color:rgba(255,255,255,0.75);max-width:600px;margin-top:var(--espaco-md);"><?php the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($post_destaque && !$cat_slug && $paged === 1) :
        $d_id       = $post_destaque->ID;
        $d_cat      = get_the_category($d_id);
        $d_img      = get_the_post_thumbnail_url($d_id, 'aventura-banner') ?: '';
        $d_data     = get_the_date('d \d\e F \d\e Y', $d_id);
        $d_excerpt  = wp_trim_words(get_the_excerpt($d_id), 25);
        $d_palavras = str_word_count(strip_tags(get_post_field('post_content', $d_id)));
        $d_min      = max(1, round($d_palavras / 200));
    ?>
    <!-- Post Destaque -->
    <section class="blog-destaque">
        <div class="container">
            <article class="blog-destaque__card">
                <?php if ($d_img) : ?>
                <div class="blog-destaque__img-wrap">
                    <img src="<?php echo esc_url($d_img); ?>" alt="<?php echo esc_attr(get_the_title($d_id)); ?>" class="blog-destaque__img" loading="lazy" />
                    <div class="blog-destaque__img-overlay" aria-hidden="true"></div>
                </div>
                <?php endif; ?>
                <div class="blog-destaque__conteudo">
                    <div class="blog-destaque__meta">
                        <?php if ($d_cat) : ?>
                            <span class="badge badge--verde"><?php echo esc_html($d_cat[0]->name); ?></span>
                        <?php endif; ?>
                        <span class="blog-meta-item">📅 <?php echo $d_data; ?></span>
                        <span class="blog-meta-item">⏱ <?php echo $d_min; ?> min</span>
                    </div>
                    <h2 class="blog-destaque__titulo">
                        <a href="<?php echo get_permalink($d_id); ?>"><?php echo get_the_title($d_id); ?></a>
                    </h2>
                    <?php if ($d_excerpt) : ?>
                        <p class="blog-destaque__excerpt"><?php echo $d_excerpt; ?></p>
                    <?php endif; ?>
                    <a href="<?php echo get_permalink($d_id); ?>" class="btn btn--primario">
                        <?php _e('Ler artigo completo', 'temaaventuras'); ?> →
                    </a>
                </div>
            </article>
        </div>
    </section>
    <?php endif; ?>

    <!-- Filtro por categoria -->
    <?php
    $categorias = get_categories(['hide_empty' => true]);
    if (!empty($categorias)) :
    ?>
    <section class="blog-filtros">
        <div class="container">
            <div class="blog-filtros__lista" role="group" aria-label="Filtrar por categoria">
                <a href="<?php echo get_permalink(); ?>"
                   class="btn btn--pequeno <?php echo !$cat_slug ? 'btn--primario' : 'btn--ghost'; ?>">
                    <?php _e('Todos', 'temaaventuras'); ?>
                </a>
                <?php foreach ($categorias as $cat) : ?>
                <a href="?categoria=<?php echo esc_attr($cat->slug); ?>"
                   class="btn btn--pequeno <?php echo ($cat_slug === $cat->slug) ? 'btn--primario' : 'btn--ghost'; ?>">
                    <?php echo esc_html($cat->name); ?>
                    <span class="blog-filtros__count"><?php echo $cat->count; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Grid de Posts -->
    <section class="section section--pequena">
        <div class="container">
            <?php if ($posts_query->have_posts()) : ?>
            <div class="blog-grid">
                <?php while ($posts_query->have_posts()) : $posts_query->the_post();
                    $b_cats    = get_the_category();
                    $b_img     = get_the_post_thumbnail_url(get_the_ID(), 'aventura-thumb');
                    $b_palavras = str_word_count(strip_tags(get_the_content()));
                    $b_min      = max(1, round($b_palavras / 200));
                ?>
                <article class="card-post animar-entrada" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <a href="<?php the_permalink(); ?>" class="card-post__img-link" tabindex="-1" aria-hidden="true">
                        <?php if ($b_img) : ?>
                            <div class="card-post__img-wrap">
                                <img src="<?php echo esc_url($b_img); ?>" alt="<?php the_title_attribute(); ?>" class="card-post__img" loading="lazy" />
                            </div>
                        <?php else : ?>
                            <div class="card-post__img-wrap card-post__img-wrap--placeholder">📝</div>
                        <?php endif; ?>
                    </a>
                    <div class="card-post__corpo">
                        <div class="card-post__meta">
                            <?php if ($b_cats) : ?>
                                <a href="<?php echo get_category_link($b_cats[0]->term_id); ?>" class="badge badge--verde card-post__cat">
                                    <?php echo esc_html($b_cats[0]->name); ?>
                                </a>
                            <?php endif; ?>
                            <span class="blog-meta-item">⏱ <?php echo $b_min; ?> min</span>
                        </div>
                        <h2 class="card-post__titulo">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="card-post__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
                        <div class="card-post__rodape">
                            <span class="blog-meta-item">📅 <?php echo get_the_date('d M Y'); ?></span>
                            <a href="<?php the_permalink(); ?>" class="card-post__lermais">
                                <?php _e('Ler mais', 'temaaventuras'); ?> →
                            </a>
                        </div>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <!-- Paginação -->
            <div class="blog-paginacao">
                <?php
                echo paginate_links([
                    'base'      => add_query_arg('paged', '%#%'),
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $posts_query->max_num_pages,
                    'prev_text' => '← Anterior',
                    'next_text' => 'Próximo →',
                    'type'      => 'list',
                ]);
                ?>
            </div>

            <?php else : ?>
            <div class="texto-centro" style="padding:var(--espaco-4xl) 0;">
                <p style="font-size:3rem;">📝</p>
                <h2><?php _e('Nenhum post encontrado.', 'temaaventuras'); ?></h2>
                <?php if ($cat_slug) : ?>
                    <a href="<?php echo get_permalink(); ?>" class="btn btn--primario" style="margin-top:var(--espaco-xl);">
                        <?php _e('Ver todos os posts', 'temaaventuras'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
