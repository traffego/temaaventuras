<?php
/**
 * Template Name: Página de Guias
 * Template Post Type: page
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if (has_post_thumbnail()) the_post_thumbnail('aventura-banner',['class'=>'page-banner__img','loading'=>'eager','alt'=>'']); ?>
        <div class="container page-banner__conteudo">
            <span class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">🧭 <?php _e('Nossa Equipe','temaaventuras'); ?></span>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php if (get_the_excerpt()) : ?>
                <p style="color:rgba(255,255,255,0.75);max-width:600px;margin-top:var(--espaco-md);"><?php the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid de Guias -->
    <section class="section section--pequena">
        <div class="container">
            <?php
            $guias = new WP_Query([
                'post_type'      => 'guia',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order title',
                'order'          => 'ASC',
            ]);

            if ($guias->have_posts()) :
            ?>
            <div class="guias-grid">
                <?php while ($guias->have_posts()) : $guias->the_post();
                    $subtitulo = get_post_meta(get_the_ID(), '_guia_subtitulo', true);
                    $descricao = get_post_meta(get_the_ID(), '_guia_descricao', true);
                    $foto_id   = (int) get_post_meta(get_the_ID(), '_guia_foto', true);
                    $foto_url  = $foto_id ? wp_get_attachment_image_url($foto_id, 'medium') : '';
                ?>
                <article class="guia-card animar-entrada" aria-label="<?php the_title_attribute(); ?>">
                    <div class="guia-card__foto-wrap">
                        <?php if ($foto_url) : ?>
                            <img src="<?php echo esc_url($foto_url); ?>" alt="<?php the_title_attribute(); ?>" class="guia-card__foto" loading="lazy" />
                        <?php else : ?>
                            <div class="guia-card__foto guia-card__foto--placeholder">🧭</div>
                        <?php endif; ?>
                        <div class="guia-card__foto-ring" aria-hidden="true"></div>
                    </div>
                    <div class="guia-card__info">
                        <h2 class="guia-card__nome"><?php the_title(); ?></h2>
                        <?php if ($subtitulo) : ?>
                            <p class="guia-card__subtitulo"><?php echo esc_html($subtitulo); ?></p>
                        <?php endif; ?>
                        <?php if ($descricao) : ?>
                            <p class="guia-card__descricao"><?php echo esc_html($descricao); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <?php else: ?>
                <div class="texto-centro" style="padding:var(--espaco-4xl) 0;">
                    <p style="font-size:3rem;">🧭</p>
                    <h2><?php _e('Nenhum guia cadastrado ainda.','temaaventuras'); ?></h2>
                    <p><?php _e('Adicione guias no painel do WordPress para exibi-los aqui.','temaaventuras'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
