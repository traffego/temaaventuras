<?php
/**
 * Template Part: Hero – Seção principal da home
 *
 * @package TemaAventuras
 */

$hero_titulo   = ta_get( 'hero_titulo',    'Sua Maior Aventura Começa Aqui' );
$hero_subtitulo = ta_get( 'hero_subtitulo', 'Rafting, trilhas, tirolesa e muito mais. Experiências radicais com segurança e profissionalismo.' );
$hero_cta_texto = ta_get( 'hero_cta_texto', 'Reserve Sua Aventura' );
$hero_cta_url   = ta_get( 'hero_cta_url',  '#atividades' );
$hero_imagem    = ta_get( 'hero_imagem',   get_template_directory_uri() . '/assets/images/hero-bg.jpg' );
$hero_video     = ta_get( 'hero_video_url', '' );
$wa_link        = ta_whatsapp_link( 'Olá! Gostaria de reservar uma aventura.' );
?>

<!-- =========================================
     HERO
     ========================================= -->
<section class="hero" id="inicio" aria-labelledby="hero-titulo">

    <!-- FUNDO: Vídeo ou Imagem -->
    <?php if ( $hero_video ) : ?>
        <div class="hero__fundo-container">
            <?php if ( ta_is_youtube_url( $hero_video ) ) : ?>
                <iframe class="hero__fundo hero__fundo--youtube"
                        src="<?php echo esc_url( ta_get_youtube_embed_url( $hero_video ) ); ?>"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture; encrypted-media; gyroscope"
                        title="Background Video"></iframe>
            <?php else : ?>
                <video class="hero__fundo"
                       autoplay muted loop playsinline
                       aria-hidden="true"
                       poster="<?php echo esc_url( $hero_imagem ); ?>">
                    <source src="<?php echo esc_url( $hero_video ); ?>" type="video/mp4">
                </video>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <img class="hero__fundo"
             src="<?php echo esc_url( $hero_imagem ); ?>"
             alt=""
             aria-hidden="true"
             fetchpriority="high">
    <?php endif; ?>

    <!-- Overlay gradiente -->
    <div class="hero__overlay" aria-hidden="true"></div>

    <!-- Partículas decorativas -->
    <div class="hero__particulas" aria-hidden="true">
        <span class="particula" style="--x:10%;--y:20%;--s:8px;--d:0s"></span>
        <span class="particula" style="--x:85%;--y:15%;--s:5px;--d:1s"></span>
        <span class="particula" style="--x:30%;--y:70%;--s:6px;--d:2s"></span>
        <span class="particula" style="--x:70%;--y:60%;--s:4px;--d:0.5s"></span>
        <span class="particula" style="--x:50%;--y:40%;--s:7px;--d:1.5s"></span>
    </div>

    <!-- CONTEÚDO -->
    <div class="hero__conteudo">
        <div class="container">
            <div class="hero__texto">

                <!-- Eyebrow -->
                <span class="hero__eyebrow animar-entrada" aria-label="Tag destacada">
                    🌿 <?php _e( 'Aventura &amp; Natureza', 'temaaventuras' ); ?>
                </span>

                <!-- Título -->
                <h1 id="hero-titulo" class="hero-titulo">
                    <?php echo wp_kses_post( $hero_titulo ); ?>
                </h1>

                <!-- Subtítulo -->
                <p class="hero__subtitulo hero-subtitulo">
                    <?php echo esc_html( $hero_subtitulo ); ?>
                </p>

                <!-- Botões CTA -->
                <div class="hero-botoes hero__botoes">
                    <a href="<?php echo esc_url( $hero_cta_url ); ?>"
                       class="btn btn--primario btn--grande"
                       id="hero-cta-principal">
                        ⚡ <?php echo esc_html( $hero_cta_texto ); ?>
                    </a>
                    <a href="<?php echo esc_url( $wa_link ); ?>"
                       class="btn btn--ghost btn--grande"
                       id="hero-cta-whatsapp"
                       target="_blank"
                       rel="noopener noreferrer">
                        📲 <?php _e( 'Falar com Especialista', 'temaaventuras' ); ?>
                    </a>
                </div>

                <!-- Badges de confiança -->
                <div class="hero__trust animar-entrada delay-5">
                    <span class="trust-item">✅ <?php _e( 'Segurança certificada', 'temaaventuras' ); ?></span>
                    <span class="trust-item">🏅 <?php _e( 'Guias experientes', 'temaaventuras' ); ?></span>
                    <span class="trust-item">⭐ <?php _e( '5 estrelas no Google', 'temaaventuras' ); ?></span>
                </div>

            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="scroll-indicador" aria-hidden="true">
        <span><?php _e( 'Explorar', 'temaaventuras' ); ?></span>
        <div class="scroll-indicador__seta"></div>
    </div>

</section>

<style>
/* Estilos exclusivos do Hero */
.hero__texto {
    max-width: 760px;
}

.hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--fundo-glass);
    border: 1px solid var(--borda-glass);
    backdrop-filter: blur(10px);
    padding: 8px 20px;
    border-radius: var(--raio-full);
    font-size: 0.8rem;
    font-weight: var(--peso-negrito);
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--cor-secundaria);
    margin-bottom: var(--espaco-lg);
}

.hero-titulo {
    line-height: 0.95;
    margin-bottom: var(--espaco-lg);
    text-shadow: 0 2px 20px rgba(0,0,0,0.5);
}

.hero__subtitulo {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: rgba(255,255,255,0.8);
    max-width: 560px;
    line-height: 1.7;
    margin-bottom: var(--espaco-2xl);
}

/* Fundo de vídeo/imagem */
.hero__fundo-container {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.hero__fundo {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    min-width: 100%;
    min-height: 100%;
    transform: translate(-50%, -50%);
    object-fit: cover;
    z-index: 0;
}

/* O iframe do youtube não aceita object-fit nativamente tão bem,
   precisa forçar a proporção 16:9 (100vw x 56.25vw ou 177.77vh x 100vh) */
.hero__fundo--youtube {
    width: 100vw;
    height: 56.25vw; /* Proporção 16:9 */
    min-height: 100vh;
    min-width: 177.77vh; /* Proporção 16:9 invertida */
    margin: 0;
    pointer-events: none;
}


.hero__botoes {
    display: flex;
    gap: var(--espaco-md);
    flex-wrap: wrap;
    margin-bottom: var(--espaco-2xl);
}

.hero__trust {
    display: flex;
    gap: var(--espaco-lg);
    flex-wrap: wrap;
}

.trust-item {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.7);
}

/* Partículas flutuantes */
.hero__particulas {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 1;
}
.particula {
    position: absolute;
    left: var(--x);
    top: var(--y);
    width: var(--s);
    height: var(--s);
    background: var(--cor-secundaria);
    border-radius: 50%;
    opacity: 0.4;
    animation: float 6s var(--d) ease-in-out infinite;
}

@media (max-width: 768px) {
    .hero__botoes { flex-direction: column; align-items: flex-start; }
    .hero__trust  { gap: var(--espaco-md); }
}
</style>
