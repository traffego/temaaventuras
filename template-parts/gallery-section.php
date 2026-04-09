<?php
/**
 * Template Part: Seção de Vídeos (substitui galeria)
 *
 * @package TemaAventuras
 */

$videos = get_posts( [
    'post_type'      => 'video',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
] );

if ( empty( $videos ) ) return; // Sem vídeos = sem seção
?>

<!-- =========================================
     VÍDEOS
     ========================================= -->
<section class="section" id="videos" aria-labelledby="videos-titulo">
    <div class="container">

        <div class="section-header animar-entrada">
            <span class="section-header__eyebrow">▶️ <?php _e( 'Em Ação', 'temaaventuras' ); ?></span>
            <h2 id="videos-titulo" class="section-header__titulo">
                <?php _e( 'Veja Nossas Aventuras', 'temaaventuras' ); ?>
            </h2>
            <p class="section-header__subtitulo">
                <?php _e( 'Sinta a emoção antes mesmo de chegar aqui.', 'temaaventuras' ); ?>
            </p>
        </div>

        <div class="videos-grid">
            <?php foreach ( $videos as $v ) :
                $url  = get_post_meta( $v->ID, '_video_url', true );
                $desc = get_post_meta( $v->ID, '_video_descricao', true );
                $vid  = ta_youtube_id( $url );
                if ( ! $vid ) continue;
                $thumb  = "https://img.youtube.com/vi/{$vid}/hqdefault.jpg";
                $embed  = "https://www.youtube.com/embed/{$vid}?autoplay=1&rel=0";
            ?>
            <article class="video-card animar-entrada"
                     data-embed="<?php echo esc_attr( $embed ); ?>"
                     role="button"
                     tabindex="0"
                     aria-label="<?php echo esc_attr( $v->post_title ); ?>">
                <div class="video-card__thumb-wrap">
                    <img src="<?php echo esc_url( $thumb ); ?>"
                         alt="<?php echo esc_attr( $v->post_title ); ?>"
                         class="video-card__thumb"
                         loading="lazy" />
                    <div class="video-card__overlay" aria-hidden="true">
                        <span class="video-card__play">▶</span>
                    </div>
                </div>
                <div class="video-card__info">
                    <h3 class="video-card__titulo"><?php echo esc_html( $v->post_title ); ?></h3>
                    <?php if ( $desc ) : ?>
                        <p class="video-card__desc"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div><!-- /.videos-grid -->

        <?php
        $p_videos = get_pages( [
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'page-templates/page-videos.php',
        ] )[0] ?? null;

        if ( $p_videos ) : ?>
        <div style="text-align:center;margin-top:var(--espaco-3xl);">
            <a href="<?php echo esc_url( get_permalink( $p_videos ) ); ?>"
               class="btn btn--ghost btn--grande">
                ▶️ <?php _e( 'Ver todos os vídeos', 'temaaventuras' ); ?>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Modal de vídeo -->
<div class="video-modal" id="video-modal" role="dialog" aria-modal="true" aria-label="Reproduzir vídeo">
    <button class="video-modal__fechar" id="video-modal-fechar" aria-label="Fechar vídeo">✕</button>
    <div class="video-modal__wrap">
        <iframe id="video-modal-iframe" src="" frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen></iframe>
    </div>
</div>

<script>
(function(){
    var modal   = document.getElementById('video-modal');
    var iframe  = document.getElementById('video-modal-iframe');
    var fechar  = document.getElementById('video-modal-fechar');

    document.querySelectorAll('.video-card').forEach(function(card){
        function open() {
            iframe.src = card.dataset.embed;
            modal.classList.add('ativo');
            document.body.style.overflow = 'hidden';
        }
        card.addEventListener('click', open);
        card.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' ') open(); });
    });

    function closeModal() {
        modal.classList.remove('ativo');
        iframe.src = '';
        document.body.style.overflow = '';
    }

    fechar.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });
})();
</script>
