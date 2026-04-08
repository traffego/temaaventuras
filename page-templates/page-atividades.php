<?php
/**
 * Template Name: Página de Atividades
 * Template Post Type: page
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner" style="min-height:340px;">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if (has_post_thumbnail()) the_post_thumbnail('aventura-banner',['class'=>'page-banner__img','loading'=>'eager','alt'=>'']); ?>
        <div class="container page-banner__conteudo">
            <span class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">🌊 <?php _e('Nossas Atividades','temaaventuras'); ?></span>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php if (get_the_excerpt()) : ?>
                <p style="color:rgba(255,255,255,0.75);max-width:600px;margin-top:var(--espaco-md);"><?php the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtro por categoria -->
    <?php
    $categorias = get_terms(['taxonomy' => 'categoria_atividade', 'hide_empty' => true]);
    $niveis     = get_terms(['taxonomy' => 'nivel_dificuldade',   'hide_empty' => true]);
    if (!is_wp_error($categorias) && !empty($categorias)) :
    ?>
    <section style="background:var(--fundo-card);padding:var(--espaco-xl) 0;border-bottom:1px solid var(--borda-glass);">
        <div class="container">
            <div class="flex flex-wrap gap-md" role="group" aria-label="Filtrar atividades">
                <a href="<?php echo get_permalink(); ?>"
                   class="btn btn--pequeno <?php echo !isset($_GET['categoria']) && !isset($_GET['nivel']) ? 'btn--primario' : 'btn--ghost'; ?>">
                   <?php _e('Todas','temaaventuras'); ?>
                </a>
                <?php foreach($categorias as $cat): ?>
                <a href="?categoria=<?php echo esc_attr($cat->slug); ?>"
                   class="btn btn--pequeno <?php echo (isset($_GET['categoria']) && $_GET['categoria'] === $cat->slug) ? 'btn--primario' : 'btn--ghost'; ?>">
                   <?php echo esc_html($cat->name); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Grid de Atividades -->
    <section class="section">
        <div class="container">
            <?php
            $args = [
                'post_type'      => 'atividade',
                'posts_per_page' => 12,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ];

            if (!empty($_GET['categoria'])) {
                $args['tax_query'] = [['taxonomy'=>'categoria_atividade','field'=>'slug','terms'=>sanitize_text_field($_GET['categoria'])]];
            }
            if (!empty($_GET['nivel'])) {
                $args['meta_query'] = [['key'=>'_atividade_nivel','value'=>sanitize_text_field($_GET['nivel'])]];
            }

            $atividades = new WP_Query($args);

            if ($atividades->have_posts()) :
            ?>
            <div class="grid grid--auto-fit-sm">
                <?php while($atividades->have_posts()) : $atividades->the_post();
                    $nivel   = get_post_meta(get_the_ID(),'_atividade_nivel',true) ?: 'facil';
                    $duracao = get_post_meta(get_the_ID(),'_atividade_duracao',true);
                    $preco   = get_post_meta(get_the_ID(),'_atividade_preco',true);
                    $pessoas = get_post_meta(get_the_ID(),'_atividade_pessoas',true);
                ?>
                <article class="card-atividade animar-entrada" style="aspect-ratio:3/4;" aria-label="<?php the_title_attribute(); ?>">
                    <?php
                    $img_id  = (int) get_post_meta( get_the_ID(), '_atividade_imagem', true );
                    $img_url = '';
                    if ( $img_id > 0 ) {
                        $img_url = wp_get_attachment_image_url( $img_id, 'aventura-card' )
                                 ?: wp_get_attachment_image_url( $img_id, 'large' )
                                 ?: wp_get_attachment_image_url( $img_id, 'full' );
                    }
                    if ( ! $img_url && has_post_thumbnail() ) {
                        $img_url = get_the_post_thumbnail_url( get_the_ID(), 'aventura-card' )
                                 ?: get_the_post_thumbnail_url( get_the_ID(), 'full' );
                    }
                    ?>
                    <?php if ( $img_url ) : ?>
                        <div class="card-atividade__img-wrapper">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php the_title_attribute(); ?>" class="card-atividade__img" loading="lazy">
                        </div>
                    <?php else : ?>
                        <div class="card-atividade__img" style="background:var(--gradiente-hero);display:flex;align-items:center;justify-content:center;font-size:5rem;height:100%;">🌊</div>
                    <?php endif; ?>
                    <div class="card-atividade__overlay">
                        <div class="card-atividade__badge"><?php echo ta_nivel_badge($nivel); ?></div>
                        <h2 class="card-atividade__titulo"><?php the_title(); ?></h2>
                        <div class="card-atividade__meta">
                            <?php if($duracao): ?><span class="card-atividade__detalhe">⏱ <?php echo esc_html($duracao); ?></span><?php endif; ?>
                            <?php if($preco): ?><span class="card-atividade__detalhe" style="color:var(--cor-secundaria);font-weight:700;"><?php echo ta_preco($preco); ?>/pessoa</span><?php endif; ?>
                            <?php if($pessoas): ?><span class="card-atividade__detalhe">👥 Min <?php echo esc_html($pessoas); ?> pessoas</span><?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="btn btn--secundario btn--pequeno"><?php _e('Ver Detalhes','temaaventuras'); ?></a>
                        </div>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <?php else: ?>
                <div class="texto-centro" style="padding: var(--espaco-4xl) 0;">
                    <p style="font-size:3rem;">🌿</p>
                    <h2><?php _e('Nenhuma atividade encontrada.','temaaventuras'); ?></h2>
                    <p><?php _e('Adicione atividades no painel do WordPress para exibi-las aqui.','temaaventuras'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
