<?php
/**
 * Template Part: Gallery Section com Lightbox
 *
 * @package TemaAventuras
 */

// Busca imagens da galeria (post_type attachment tagged 'galeria')
$galeria = get_posts( [
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 9,
    'post_status'    => 'inherit',
    'orderby'        => 'rand',
] );

// Fallback: emojis representando fotos
$fallback_imgs = [
    [ 'emoji' => '🌊', 'label' => 'Rafting no Rio',    'cor' => 'linear-gradient(135deg,#002776,#0066cc)' ],
    [ 'emoji' => '🥾', 'label' => 'Trilha na Serra',   'cor' => 'linear-gradient(135deg,#1a4a0a,#2d8020)' ],
    [ 'emoji' => '🪂', 'label' => 'Tirolesa Extrema',  'cor' => 'linear-gradient(135deg,#4a1a00,#cc6600)' ],
    [ 'emoji' => '🧗', 'label' => 'Rapel em Rocha',    'cor' => 'linear-gradient(135deg,#2a0050,#6600cc)' ],
    [ 'emoji' => '🏊', 'label' => 'Boia Cross',        'cor' => 'linear-gradient(135deg,#004a4a,#006666)' ],
    [ 'emoji' => '🌅', 'label' => 'Pôr do Sol',        'cor' => 'linear-gradient(135deg,#4a2a00,#ff8800)' ],
    [ 'emoji' => '⛺', 'label' => 'Acampamento',       'cor' => 'linear-gradient(135deg,#0a3a0a,#009C3B)' ],
    [ 'emoji' => '🦜', 'label' => 'Fauna Local',       'cor' => 'linear-gradient(135deg,#003a1a,#007a35)' ],
    [ 'emoji' => '🌿', 'label' => 'Natureza Pura',     'cor' => 'linear-gradient(135deg,#1a3a00,#4a8a00)' ],
];
?>

<!-- =========================================
     GALERIA
     ========================================= -->
<section class="section" id="galeria" aria-labelledby="galeria-titulo">
    <div class="container">

        <div class="section-header animar-entrada">
            <span class="section-header__eyebrow">📸 <?php _e( 'Nossa Galeria', 'temaaventuras' ); ?></span>
            <h2 id="galeria-titulo" class="section-header__titulo">
                <?php _e( 'Momentos Inesquecíveis', 'temaaventuras' ); ?>
            </h2>
            <p class="section-header__subtitulo">
                <?php _e( 'Cada aventura gera memórias para toda a vida. Veja como são nossas experiências.', 'temaaventuras' ); ?>
            </p>
        </div>

    </div>

    <!-- MASONRY GRID -->
    <div class="container">
        <div class="masonry galeria-grid" role="list" id="galeria-container">

        <?php if ( ! empty( $galeria ) ) :
            foreach ( $galeria as $i => $img ) :
                $src_full  = wp_get_attachment_image_url( $img->ID, 'full' );
                $src_thumb = wp_get_attachment_image_url( $img->ID, 'aventura-galeria' );
                $alt       = get_post_meta( $img->ID, '_wp_attachment_image_alt', true ) ?: $img->post_title;
        ?>

            <div class="masonry__item galeria-item animar-entrada delay-<?php echo min( $i % 6 + 1, 6 ); ?>"
                 role="listitem">
                <a href="<?php echo esc_url( $src_full ); ?>"
                   class="galeria-link"
                   data-lightbox="<?php echo esc_attr( $src_full ); ?>"
                   data-caption="<?php echo esc_attr( $alt ); ?>"
                   aria-label="<?php echo esc_attr( $alt ); ?>">
                    <img src="<?php echo esc_url( $src_thumb ); ?>"
                         alt="<?php echo esc_attr( $alt ); ?>"
                         loading="lazy"
                         class="galeria-img">
                    <div class="galeria-overlay" aria-hidden="true">
                        <span class="galeria-zoom">🔍</span>
                    </div>
                </a>
            </div>

        <?php endforeach;

        else : // Fallback visual
            foreach ( $fallback_imgs as $i => $item ) : ?>

            <div class="masonry__item galeria-item animar-entrada delay-<?php echo min( $i % 6 + 1, 6 ); ?>"
                 style="aspect-ratio: <?php echo ( $i % 3 === 1 ) ? '4/5' : '4/3'; ?>"
                 role="listitem">
                <div class="galeria-placeholder"
                     style="background: <?php echo $item['cor']; ?>;"
                     role="img"
                     aria-label="<?php echo esc_attr( $item['label'] ); ?>">
                    <span class="galeria-placeholder__emoji" aria-hidden="true"><?php echo $item['emoji']; ?></span>
                    <span class="galeria-placeholder__label"><?php echo esc_html( $item['label'] ); ?></span>
                </div>
            </div>

        <?php endforeach; endif; ?>

        </div><!-- /.masonry -->
    </div>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Visualizar imagem">
        <button class="lightbox__fechar" id="lightbox-fechar" aria-label="Fechar galeria">✕</button>
        <img class="lightbox__img" id="lightbox-img" src="" alt="">
        <p class="lightbox__legenda" id="lightbox-legenda"></p>
    </div>

</section>

<style>
.galeria-item { position: relative; cursor: pointer; }

.galeria-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform var(--transicao-lenta);
}

.galeria-item:hover .galeria-img {
    transform: scale(1.04);
}

.galeria-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity var(--transicao-normal);
    border-radius: var(--raio-lg);
}

.galeria-item:hover .galeria-overlay { opacity: 1; }

.galeria-zoom { font-size: 2rem; }

.galeria-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: var(--espaco-sm);
    border-radius: var(--raio-lg);
    transition: transform var(--transicao-normal);
}

.galeria-item:hover .galeria-placeholder { transform: scale(1.02); }

.galeria-placeholder__emoji { font-size: 3rem; }
.galeria-placeholder__label { font-size: 0.85rem; color: rgba(255,255,255,0.8); font-weight: 600; text-align: center; }

.lightbox__legenda {
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
    text-align: center;
    margin-top: var(--espaco-md);
    position: absolute;
    bottom: var(--espaco-xl);
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
}
</style>
