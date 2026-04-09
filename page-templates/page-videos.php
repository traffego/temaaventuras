<?php
/**
 * Template Name: Página de Vídeos
 * Template Post Type: page
 *
 * @package TemaAventuras
 */

get_header();

$paged = get_query_var('paged') ?: 1;
$videos_query = new WP_Query( [
    'post_type'      => 'video',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
] );
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'aventura-banner', [ 'class' => 'page-banner__img', 'loading' => 'eager', 'alt' => '' ] ); ?>
        <div class="container page-banner__conteudo">
            <span class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">▶️ <?php _e( 'Em Ação', 'temaaventuras' ); ?></span>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php if ( get_the_excerpt() ) : ?>
                <p style="color:rgba(255,255,255,0.75);max-width:600px;margin-top:var(--espaco-md);"><?php the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid de Vídeos -->
    <section class="section section--pequena">
        <div class="container">
            <?php if ( $videos_query->have_posts() ) : ?>
            <div class="videos-grid videos-grid--pagina">
                <?php while ( $videos_query->have_posts() ) : $videos_query->the_post();
                    $url  = get_post_meta( get_the_ID(), '_video_url', true );
                    $desc = get_post_meta( get_the_ID(), '_video_descricao', true );
                    $vid  = $url ? ta_youtube_id( $url ) : '';
                    if ( ! $vid ) continue;
                    $thumb = "https://img.youtube.com/vi/{$vid}/hqdefault.jpg";
                    $embed = "https://www.youtube.com/embed/{$vid}?autoplay=1&rel=0";
                ?>
                <article class="video-card animar-entrada"
                         data-embed="<?php echo esc_attr( $embed ); ?>"
                         role="button"
                         tabindex="0"
                         aria-label="<?php the_title_attribute(); ?>">
                    <div class="video-card__thumb-wrap">
                        <img src="<?php echo esc_url( $thumb ); ?>"
                             alt="<?php the_title_attribute(); ?>"
                             class="video-card__thumb"
                             loading="lazy" />
                        <div class="video-card__overlay" aria-hidden="true">
                            <span class="video-card__play">▶</span>
                        </div>
                    </div>
                    <div class="video-card__info">
                        <h2 class="video-card__titulo"><?php the_title(); ?></h2>
                        <?php if ( $desc ) : ?>
                            <p class="video-card__desc"><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <!-- Paginação -->
            <?php if ( $videos_query->max_num_pages > 1 ) : ?>
            <div class="blog-paginacao" style="margin-top:var(--espaco-3xl);">
                <?php echo paginate_links( [
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $videos_query->max_num_pages,
                    'prev_text' => '← Anterior',
                    'next_text' => 'Próximo →',
                    'type'      => 'list',
                ] ); ?>
            </div>
            <?php endif; ?>

            <?php else : ?>
            <div class="texto-centro" style="padding:var(--espaco-4xl) 0;">
                <p style="font-size:3rem;">▶️</p>
                <h2><?php _e( 'Nenhum vídeo cadastrado ainda.', 'temaaventuras' ); ?></h2>
                <p><?php _e( 'Adicione vídeos no painel GESTÃO → Vídeos.', 'temaaventuras' ); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<!-- Modal -->
<div class="video-modal" id="video-modal" role="dialog" aria-modal="true" aria-label="Reproduzir vídeo">
    <button class="video-modal__fechar" id="video-modal-fechar" aria-label="Fechar vídeo">✕</button>
    <div class="video-modal__wrap">
        <iframe id="video-modal-iframe" src="" frameborder="0"
                allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>

<script>
(function(){
    var modal  = document.getElementById('video-modal');
    var iframe = document.getElementById('video-modal-iframe');
    var fechar = document.getElementById('video-modal-fechar');
    document.querySelectorAll('.video-card').forEach(function(card){
        function open() { iframe.src = card.dataset.embed; modal.classList.add('ativo'); document.body.style.overflow='hidden'; }
        card.addEventListener('click', open);
        card.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' ') open(); });
    });
    function closeModal() { modal.classList.remove('ativo'); iframe.src=''; document.body.style.overflow=''; }
    fechar.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });
})();
</script>

<?php get_footer(); ?>
