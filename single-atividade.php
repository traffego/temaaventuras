<?php
/**
 * single-atividade.php – Template individual de Atividade (CPT)
 *
 * @package TemaAventuras
 */

get_header();

while ( have_posts() ) : the_post();

$nivel   = get_post_meta( get_the_ID(), '_atividade_nivel',   true ) ?: 'facil';
$duracao = get_post_meta( get_the_ID(), '_atividade_duracao', true );
$preco   = get_post_meta( get_the_ID(), '_atividade_preco',   true );
$pessoas = get_post_meta( get_the_ID(), '_atividade_pessoas', true );
$wa_msg  = urlencode( 'Olá! Quero reservar a atividade: ' . get_the_title() );
$wa_link = ta_whatsapp_link( 'Olá! Quero saber mais sobre: ' . get_the_title() );
?>

<main id="conteudo-principal" role="main">

    <!-- Hero da Atividade -->
    <div class="page-banner atividade-banner" style="min-height:70vh;">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'aventura-banner', [ 'class' => 'page-banner__img', 'loading' => 'eager', 'alt' => '' ] ); ?>
        <?php endif; ?>
        <div class="container page-banner__conteudo">
            <nav class="breadcrumb" aria-label="Navegação estrutural" style="margin-bottom:var(--espaco-md);">
                <a href="<?php echo home_url('/'); ?>"><?php _e('Início','temaaventuras'); ?></a>
                <span aria-hidden="true"> / </span>
                <a href="<?php echo home_url('/atividades'); ?>"><?php _e('Atividades','temaaventuras'); ?></a>
                <span aria-hidden="true"> / </span>
                <span aria-current="page"><?php the_title(); ?></span>
            </nav>

            <?php echo ta_nivel_badge($nivel); ?>&nbsp;
            <?php
            $cats = get_the_terms(get_the_ID(),'categoria_atividade');
            if ($cats && !is_wp_error($cats)):
                foreach ($cats as $cat) echo '<span class="badge badge--azul">' . esc_html($cat->name) . '</span>&nbsp;';
            endif;
            ?>

            <h1 class="page-banner__titulo" style="margin-top:var(--espaco-md);"><?php the_title(); ?></h1>

            <!-- Métricas rápidas -->
            <div class="atividade-metricas">
                <?php if ($duracao): ?>
                <div class="metrica-item"><span class="metrica-icon">⏱</span><span><?php echo esc_html($duracao); ?></span></div>
                <?php endif; ?>
                <?php if ($preco): ?>
                <div class="metrica-item"><span class="metrica-icon">💰</span><span><?php echo ta_preco($preco); ?> <?php _e('/pessoa','temaaventuras'); ?></span></div>
                <?php endif; ?>
                <?php if ($pessoas): ?>
                <div class="metrica-item"><span class="metrica-icon">👥</span><span><?php printf(__('Mín. %d pessoas','temaaventuras'), intval($pessoas)); ?></span></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Conteúdo + Sidebar -->
    <section class="section">
        <div class="container">
            <div class="atividade-layout">

                <!-- Conteúdo principal -->
                <div class="atividade-conteudo">
                    <div class="wp-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Galeria da atividade -->
                    <?php
                    $galeria_ids = get_post_meta(get_the_ID(), '_atividade_galeria', true);
                    if ($galeria_ids && is_array($galeria_ids)) :
                    ?>
                    <div style="margin-top:var(--espaco-3xl);">
                        <h2 style="font-size:1.8rem;margin-bottom:var(--espaco-xl);"><?php _e('Galeria','temaaventuras'); ?></h2>
                        <div class="masonry">
                            <?php foreach ($galeria_ids as $img_id):
                                $src_full = wp_get_attachment_image_url($img_id,'full');
                                $alt = get_post_meta($img_id,'_wp_attachment_image_alt',true);
                            ?>
                            <div class="masonry__item galeria-item">
                                <a href="<?php echo esc_url($src_full); ?>"
                                   data-lightbox="<?php echo esc_attr($src_full); ?>"
                                   data-caption="<?php echo esc_attr($alt); ?>">
                                    <?php echo wp_get_attachment_image($img_id,'aventura-galeria',false,['class'=>'galeria-img','loading'=>'lazy']); ?>
                                    <div class="galeria-overlay" aria-hidden="true"><span class="galeria-zoom">🔍</span></div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Atividades relacionadas -->
                    <?php
                    $relacionadas = new WP_Query([
                        'post_type'      => 'atividade',
                        'posts_per_page' => 3,
                        'post__not_in'   => [ get_the_ID() ],
                        'orderby'        => 'rand',
                    ]);
                    if ($relacionadas->have_posts()):
                    ?>
                    <div style="margin-top:var(--espaco-4xl);">
                        <h2 style="font-size:1.8rem;margin-bottom:var(--espaco-2xl);"><?php _e('Outras Aventuras','temaaventuras'); ?></h2>
                        <div class="grid grid--3">
                            <?php while ($relacionadas->have_posts()): $relacionadas->the_post();
                                $n = get_post_meta(get_the_ID(),'_atividade_nivel',true) ?: 'facil';
                                $p = get_post_meta(get_the_ID(),'_atividade_preco',true);
                            ?>
                            <article class="card-atividade" style="aspect-ratio:3/4;" aria-label="<?php the_title_attribute(); ?>">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('aventura-card',['class'=>'card-atividade__img','loading'=>'lazy','alt'=>get_the_title()]); ?>
                                <?php else: ?>
                                    <div class="card-atividade__img" style="background:var(--gradiente-hero);display:flex;align-items:center;justify-content:center;font-size:4rem;height:100%;">🌊</div>
                                <?php endif; ?>
                                <div class="card-atividade__overlay">
                                    <div class="card-atividade__badge"><?php echo ta_nivel_badge($n); ?></div>
                                    <h3 class="card-atividade__titulo"><?php the_title(); ?></h3>
                                    <div class="card-atividade__meta">
                                        <?php if($p): ?><span class="card-atividade__detalhe" style="color:var(--cor-secundaria);font-weight:700;"><?php echo ta_preco($p); ?>/pessoa</span><?php endif; ?>
                                        <a href="<?php the_permalink(); ?>" class="btn btn--secundario btn--pequeno"><?php _e('Ver','temaaventuras'); ?></a>
                                    </div>
                                </div>
                            </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar: Reserva -->
                <aside class="atividade-sidebar" aria-label="Reservar atividade">
                    <div class="sidebar-reserva">
                        <div class="sidebar-reserva__preco">
                            <?php if ($preco): ?>
                            <span class="sidebar-reserva__valor"><?php echo ta_preco($preco); ?></span>
                            <span class="sidebar-reserva__label"><?php _e('por pessoa','temaaventuras'); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="sidebar-reserva__meta">
                            <?php if ($duracao): ?><div class="sidebar-meta-item">⏱ <strong><?php _e('Duração:','temaaventuras'); ?></strong> <?php echo esc_html($duracao); ?></div><?php endif; ?>
                            <?php if ($pessoas): ?><div class="sidebar-meta-item">👥 <strong><?php _e('Mínimo:','temaaventuras'); ?></strong> <?php printf(_n('%d pessoa','%d pessoas',intval($pessoas),'temaaventuras'),intval($pessoas)); ?></div><?php endif; ?>
                            <div class="sidebar-meta-item"><?php echo ta_nivel_badge($nivel); ?> <strong><?php _e('Dificuldade','temaaventuras'); ?></strong></div>
                        </div>

                        <a href="<?php echo esc_url($wa_link); ?>"
                           class="btn btn--primario btn--grande pulsar"
                           style="width:100%;justify-content:center;margin-bottom:var(--espaco-md);"
                           target="_blank"
                           rel="noopener noreferrer"
                           id="atividade-reservar-btn">
                            📲 <?php _e('Reservar pelo WhatsApp','temaaventuras'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/contato')); ?>"
                           class="btn btn--ghost"
                           style="width:100%;justify-content:center;"
                           id="atividade-contato-btn">
                            ✉️ <?php _e('Outras formas de contato','temaaventuras'); ?>
                        </a>

                        <div class="sidebar-garantias">
                            <div class="garantia-item">✅ <?php _e('Segurança certificada','temaaventuras'); ?></div>
                            <div class="garantia-item">🏅 <?php _e('Guias especializados','temaaventuras'); ?></div>
                            <div class="garantia-item">🌿 <?php _e('Equipamentos homologados','temaaventuras'); ?></div>
                            <div class="garantia-item">💳 <?php _e('Parcelamento disponível','temaaventuras'); ?></div>
                        </div>
                    </div>
                </aside>

            </div><!-- /.atividade-layout -->
        </div>
    </section>

</main>

<?php endwhile;
get_footer(); ?>

<style>
.atividade-banner .page-banner__overlay {
    background: linear-gradient(135deg, rgba(0,39,118,0.75) 0%, rgba(0,0,0,0.7) 100%);
}

.atividade-metricas {
    display: flex;
    gap: var(--espaco-xl);
    flex-wrap: wrap;
    margin-top: var(--espaco-xl);
}

.metrica-item {
    display: flex;
    align-items: center;
    gap: var(--espaco-sm);
    background: var(--fundo-glass);
    border: 1px solid var(--borda-glass);
    backdrop-filter: blur(10px);
    padding: var(--espaco-sm) var(--espaco-md);
    border-radius: var(--raio-full);
    font-weight: var(--peso-medio);
    font-size: 0.9rem;
}

.metrica-icon { font-size: 1.2rem; }

.atividade-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: var(--espaco-3xl);
    align-items: start;
}

/* Sidebar de reserva */
.sidebar-reserva {
    background: var(--fundo-card);
    border: 1px solid var(--borda-glass);
    border-radius: var(--raio-xl);
    padding: var(--espaco-xl);
    position: sticky;
    top: calc(var(--altura-nav) + var(--espaco-lg));
}

.sidebar-reserva__preco {
    text-align: center;
    padding-bottom: var(--espaco-xl);
    border-bottom: 1px solid var(--borda-glass);
    margin-bottom: var(--espaco-xl);
}

.sidebar-reserva__valor {
    display: block;
    font-family: var(--fonte-titulo);
    font-size: 3.5rem;
    color: var(--cor-secundaria);
    line-height: 1;
}

.sidebar-reserva__label {
    font-size: var(--tamanho-pequeno);
    color: var(--texto-muted);
}

.sidebar-reserva__meta {
    display: flex;
    flex-direction: column;
    gap: var(--espaco-sm);
    margin-bottom: var(--espaco-xl);
}

.sidebar-meta-item {
    font-size: var(--tamanho-pequeno);
    color: var(--texto-secundario);
    display: flex;
    align-items: center;
    gap: var(--espaco-sm);
    padding: var(--espaco-sm) 0;
    border-bottom: 1px solid var(--borda-glass);
}

.sidebar-garantias {
    display: flex;
    flex-direction: column;
    gap: var(--espaco-sm);
    margin-top: var(--espaco-xl);
    padding-top: var(--espaco-xl);
    border-top: 1px solid var(--borda-glass);
}

.garantia-item {
    font-size: 0.8rem;
    color: var(--texto-muted);
}

@media (max-width: 1024px) {
    .atividade-layout {
        grid-template-columns: 1fr;
    }
    .sidebar-reserva { position: static; }
    .atividade-sidebar { order: -1; }
}
</style>
