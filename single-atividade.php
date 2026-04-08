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

// Campos adicionados na meta box
$at_data    = get_post_meta( get_the_ID(), '_atividade_data',    true );
$at_horario = get_post_meta( get_the_ID(), '_atividade_horario', true );
$at_vagas   = get_post_meta( get_the_ID(), '_atividade_vagas',   true );
$at_obs     = get_post_meta( get_the_ID(), '_atividade_obs',     true );
$at_img_id  = (int) get_post_meta( get_the_ID(), '_atividade_imagem', true );
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

                    <?php if ( $at_img_id ) :
                        $at_img_src = wp_get_attachment_image_url( $at_img_id, 'large' );
                        $at_img_alt = get_post_meta( $at_img_id, '_wp_attachment_image_alt', true ) ?: get_the_title();
                    ?>
                    <div class="atividade-imagem-destaque">
                        <img src="<?php echo esc_url( $at_img_src ); ?>"
                             alt="<?php echo esc_attr( $at_img_alt ); ?>"
                             loading="eager"
                             class="atividade-imagem-destaque__img" />
                    </div>
                    <?php endif; ?>

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
                            <?php if ( $at_data ) : ?>
                            <div class="sidebar-meta-item">📅 <strong><?php _e('Data:','temaaventuras'); ?></strong> <?php echo date_i18n('d/m/Y', strtotime($at_data)); ?></div>
                            <?php endif; ?>
                            <?php if ( $at_horario ) : ?>
                            <div class="sidebar-meta-item">⏰ <strong><?php _e('Horário:','temaaventuras'); ?></strong> <?php echo esc_html($at_horario); ?></div>
                            <?php endif; ?>
                            <?php if ( $at_vagas ) : ?>
                            <div class="sidebar-meta-item">👥 <strong><?php _e('Vagas:','temaaventuras'); ?></strong> <?php echo intval($at_vagas); ?></div>
                            <?php endif; ?>
                            <?php if ($preco): ?>
                            <div class="sidebar-meta-item">💰 <strong><?php _e('Preço/pessoa:','temaaventuras'); ?></strong> <?php echo ta_preco($preco); ?></div>
                            <?php endif; ?>
                            <?php if ($duracao): ?><div class="sidebar-meta-item">⏱ <strong><?php _e('Duração:','temaaventuras'); ?></strong> <?php echo esc_html($duracao); ?></div><?php endif; ?>
                            <?php if ($pessoas): ?><div class="sidebar-meta-item">👥 <strong><?php _e('Mínimo:','temaaventuras'); ?></strong> <?php printf(_n('%d pessoa','%d pessoas',intval($pessoas),'temaaventuras'),intval($pessoas)); ?></div><?php endif; ?>
                            <div class="sidebar-meta-item"><?php echo ta_nivel_badge($nivel); ?> <strong><?php _e('Dificuldade','temaaventuras'); ?></strong></div>
                            <?php if ( $at_obs ) : ?>
                            <div class="sidebar-meta-item sidebar-meta-obs">📝 <strong><?php _e('Obs:','temaaventuras'); ?></strong> <?php echo esc_html($at_obs); ?></div>
                            <?php endif; ?>
                        </div>

                        <?php
                        // Próximas sessões disponíveis
                        $proxima = ta_proxima_sessao( get_the_ID() );
                        $sessoes_futuras = ta_get_sessoes_atividade( get_the_ID(), true );
                        ?>

                        <?php if ($proxima): ?>
                        <!-- Próxima data disponível -->
                        <div style="background:var(--fundo-glass);border:1px solid var(--borda-glass);border-radius:var(--raio-lg);padding:var(--espaco-md);margin-bottom:var(--espaco-lg);">
                            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--texto-muted);margin-bottom:var(--espaco-sm);">📅 Próxima data</div>
                            <div style="font-weight:var(--peso-negrito);color:var(--texto-primario);">
                                <?php echo date_i18n('d/m/Y', strtotime($proxima['data'])); ?>
                                <?php echo ' às ' . esc_html($proxima['hora']); ?>
                            </div>
                            <div style="font-size:var(--tamanho-pequeno);color:var(--cor-primaria);margin-top:4px;">
                                ✅ <?php echo $proxima['livres']; ?> vaga<?php echo $proxima['livres'] > 1 ? 's' : ''; ?> disponível<?php echo $proxima['livres'] > 1 ? 'eis' : ''; ?>
                            </div>
                            <?php if (!empty($proxima['obs'])): ?>
                            <div style="font-size:0.75rem;color:var(--texto-muted);margin-top:var(--espaco-sm);border-top:1px solid var(--borda-glass);padding-top:var(--espaco-sm);">
                                📝 <?php echo esc_html($proxima['obs']); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <a href="<?php echo esc_url( ta_checkout_url( get_the_ID() ) ); ?>"
                           class="btn btn--primario btn--grande pulsar"
                           style="width:100%;justify-content:center;margin-bottom:var(--espaco-md);"
                           id="atividade-reservar-btn">
                            🎟️ <?php _e('Reservar Agora','temaaventuras'); ?>
                        </a>

                        <?php elseif (!empty($sessoes_futuras)): ?>
                        <!-- Sessões disponíveis mas todas lotadas -->
                        <div style="background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);border-radius:var(--raio-lg);padding:var(--espaco-md);margin-bottom:var(--espaco-lg);text-align:center;">
                            <div style="font-size:1.5rem;margin-bottom:4px;">😔</div>
                            <div style="color:var(--texto-muted);font-size:var(--tamanho-pequeno);">
                                Todas as sessões disponíveis estão lotadas.
                            </div>
                        </div>

                        <?php else: ?>
                        <!-- Sem sessões cadastradas -->
                        <div style="text-align:center;padding:var(--espaco-md);margin-bottom:var(--espaco-lg);">
                            <div style="font-size:0.85rem;color:var(--texto-muted);">
                                Entre em contato para verificar disponibilidade.
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Sempre mostrar WhatsApp como alternativa -->
                        <a href="<?php echo esc_url($wa_link); ?>"
                           class="btn btn--ghost"
                           style="width:100%;justify-content:center;"
                           target="_blank"
                           rel="noopener noreferrer"
                           id="atividade-whatsapp-btn">
                            📲 <?php _e('Reservar pelo WhatsApp','temaaventuras'); ?>
                        </a>

                        <!-- Mini-lista de sessões abertas -->
                        <?php if (count($sessoes_futuras) > 1): ?>
                        <details style="margin-top:var(--espaco-lg);">
                            <summary style="cursor:pointer;font-size:var(--tamanho-pequeno);color:var(--texto-muted);list-style:none;display:flex;justify-content:space-between;align-items:center;padding:var(--espaco-sm) 0;border-top:1px solid var(--borda-glass);">
                                <span>📅 Ver todas as datas (<?php echo count($sessoes_futuras); ?>)</span>
                                <span>▾</span>
                            </summary>
                            <div style="margin-top:var(--espaco-md);display:flex;flex-direction:column;gap:var(--espaco-sm);">
                                <?php foreach (array_slice($sessoes_futuras, 0, 5) as $s):
                                    $vi = ta_vagas_disponiveis(get_the_ID(), $s['id']);
                                    $cor_v = $vi['livres'] === 0 ? '#ef4444' : ($vi['livres'] <= 3 ? '#f59e0b' : '#22c55e');
                                ?>
                                <a href="<?php echo esc_url( ta_checkout_url( get_the_ID(), $s['id'] ) ); ?>"
                                   style="display:flex;justify-content:space-between;align-items:center;background:var(--fundo-glass);border:1px solid var(--borda-glass);border-radius:var(--raio-md);padding:var(--espaco-sm) var(--espaco-md);font-size:0.8rem;text-decoration:none;color:var(--texto-secundario);<?php echo $vi['livres'] === 0 ? 'opacity:.5;pointer-events:none;' : ''; ?>">
                                    <span><?php echo date_i18n('d/m', strtotime($s['data'])) . ' · ' . esc_html($s['hora']); ?></span>
                                    <span style="color:<?php echo $cor_v; ?>;font-weight:var(--peso-medio);">
                                        <?php echo $vi['livres'] === 0 ? 'Lotado' : $vi['livres'] . ' vaga' . ($vi['livres'] > 1 ? 's' : ''); ?>
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </details>
                        <?php endif; ?>

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

/* Imagem destacada da atividade (meta _atividade_imagem) */
.atividade-imagem-destaque {
    width: 100%;
    border-radius: var(--raio-xl);
    overflow: hidden;
    margin-bottom: var(--espaco-2xl);
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    aspect-ratio: 16/9;
}

.atividade-imagem-destaque__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .5s ease;
}

.atividade-imagem-destaque:hover .atividade-imagem-destaque__img {
    transform: scale(1.03);
}

/* Observações na sidebar */
.sidebar-meta-obs {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 2px;
    font-size: 0.78rem;
    line-height: 1.5;
    color: var(--texto-muted);
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
