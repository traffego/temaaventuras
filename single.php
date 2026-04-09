<?php
/**
 * single.php – Post singular (blog)
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">
<?php while ( have_posts() ) : the_post();
    $cats     = get_the_category();
    $tags     = get_the_tags();
    $palavras = str_word_count( strip_tags( get_the_content() ) );
    $minutos  = max( 1, round( $palavras / 200 ) );
    $url_enc  = urlencode( get_permalink() );
    $tit_enc  = urlencode( get_the_title() );
?>

<?php
$_hero_img = get_the_post_thumbnail_url( get_the_ID(), 'full' )
           ?: wp_get_attachment_image_url( 78, 'full' );
?>
<!-- ══ HERO ══════════════════════════════════════════════════ -->
<div class="post-hero" role="banner">
    <?php if ( $_hero_img ) : ?>
    <div class="post-hero__bg">
        <img src="<?php echo esc_url( $_hero_img ); ?>" class="post-hero__img" loading="eager" alt="" />
    </div>
    <?php endif; ?>
    <div class="post-hero__overlay" aria-hidden="true"></div>

    <div class="container post-hero__inner">

        <!-- Categoria -->
        <?php if ( $cats ) : ?>
        <a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"
           class="post-hero__cat">
            <?php echo esc_html( $cats[0]->name ); ?>
        </a>
        <?php endif; ?>

        <!-- Título -->
        <h1 class="post-hero__titulo"><?php the_title(); ?></h1>

        <!-- Meta -->
        <div class="post-hero__meta">
            <span>
                <?php if ( get_avatar_url( get_the_author_meta('ID') ) ) : ?>
                <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta('ID'), ['size'=>32] ) ); ?>"
                     alt="<?php the_author(); ?>" class="post-hero__avatar" />
                <?php endif; ?>
                <?php the_author(); ?>
            </span>
            <span class="post-hero__sep" aria-hidden="true">·</span>
            <span><?php echo get_the_date('d M Y'); ?></span>
            <span class="post-hero__sep" aria-hidden="true">·</span>
            <span>⏱ <?php printf( _n('%d min', '%d min de leitura', $minutos, 'temaaventuras'), $minutos ); ?></span>
        </div>

    </div>
</div>

<!-- ══ BODY ══════════════════════════════════════════════════ -->
<div class="post-layout">

    <!-- Coluna principal -->
    <div class="post-main">
        <article id="post-<?php the_ID(); ?>" <?php post_class('entrada-post'); ?>>

            <!-- Imagem destaque -->
            <?php
            $_featured = get_the_post_thumbnail_url( get_the_ID(), 'aventura-banner' )
                      ?: wp_get_attachment_image_url( 78, 'aventura-banner' );
            if ( $_featured ) : ?>
            <figure class="post-featured-img">
                <img src="<?php echo esc_url( $_featured ); ?>"
                     alt="<?php the_title_attribute(); ?>"
                     class="post-featured-img__img"
                     loading="eager" />
                <?php if ( get_the_post_thumbnail_caption() ) : ?>
                <figcaption><?php echo get_the_post_thumbnail_caption(); ?></figcaption>
                <?php endif; ?>
            </figure>
            <?php endif; ?>

            <!-- Conteúdo -->
            <div class="entry-content wp-content">
                <?php the_content(); ?>
            </div>

            <!-- Tags -->
            <?php if ( $tags ) : ?>
            <div class="post-tags">
                <span class="post-tags__label">🏷</span>
                <?php foreach ( $tags as $tag ) : ?>
                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                   class="badge badge--verde"><?php echo esc_html( $tag->name ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </article>

        <!-- Compartilhar -->
        <div class="post-share">
            <span class="post-share__label"><?php _e( 'Compartilhar', 'temaaventuras' ); ?></span>
            <div class="post-share__btns">
                <a href="https://wa.me/?text=<?php echo $tit_enc . '+' . $url_enc; ?>"
                   class="post-share__btn post-share__btn--wa"
                   target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url_enc; ?>"
                   class="post-share__btn post-share__btn--fb"
                   target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo $url_enc; ?>&text=<?php echo $tit_enc; ?>"
                   class="post-share__btn post-share__btn--x"
                   target="_blank" rel="noopener noreferrer" aria-label="Twitter / X">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.626L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                    X
                </a>
            </div>
        </div>

        <!-- Posts relacionados -->
        <?php
        $relacionados = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post__not_in'   => [ get_the_ID() ],
            'category__in'   => wp_get_post_categories( get_the_ID() ),
            'orderby'        => 'rand',
        ] );
        if ( $relacionados->have_posts() ) : ?>
        <section class="post-relacionados" aria-label="<?php _e('Continue lendo','temaaventuras'); ?>">
            <h2 class="post-relacionados__titulo"><?php _e( 'Continue Explorando', 'temaaventuras' ); ?></h2>
            <div class="post-relacionados__grid">
                <?php while ( $relacionados->have_posts() ) : $relacionados->the_post(); ?>
                <article class="post-rel-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" class="post-rel-card__img-wrap" tabindex="-1" aria-hidden="true">
                        <?php the_post_thumbnail( 'aventura-thumb', [ 'class' => 'post-rel-card__img', 'loading' => 'lazy' ] ); ?>
                    </a>
                    <?php endif; ?>
                    <div class="post-rel-card__body">
                        <?php $rc = get_the_category(); if ($rc): ?>
                        <span class="post-rel-card__cat"><?php echo esc_html($rc[0]->name); ?></span>
                        <?php endif; ?>
                        <h3 class="post-rel-card__titulo">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <span class="post-rel-card__data"><?php echo get_the_date('d M Y'); ?></span>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Comentários -->
        <?php if ( comments_open() || get_comments_number() ) : ?>
        <div class="post-comentarios">
            <?php comments_template(); ?>
        </div>
        <?php endif; ?>

    </div><!-- /.post-main -->

</div><!-- /.post-layout -->

<?php endwhile; ?>
</main>

<style>
/* ── HERO ── */
.post-hero {
    position: relative;
    min-height: 70vh;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
}

.post-hero__bg {
    position: absolute;
    inset: 0;
}

.post-hero__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
}

.post-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(10,17,13,1) 0%,
        rgba(10,17,13,0.55) 45%,
        rgba(10,17,13,0.15) 100%
    );
}

.post-hero__inner {
    position: relative;
    padding-block: var(--espaco-4xl);
    max-width: 800px;
}

.post-hero__cat {
    display: inline-block;
    font-size: .75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--cor-primaria);
    background: rgba(0,156,59,.14);
    border: 1px solid rgba(0,156,59,.35);
    border-radius: 999px;
    padding: 4px 14px;
    text-decoration: none;
    margin-bottom: var(--espaco-lg);
    transition: background .2s;
}
.post-hero__cat:hover { background: rgba(0,156,59,.28); }

.post-hero__titulo {
    font-size: clamp(2rem, 5vw, 3.8rem);
    line-height: 1.15;
    margin: 0 0 var(--espaco-xl);
    color: #fff;
}

.post-hero__meta {
    display: flex;
    align-items: center;
    gap: var(--espaco-md);
    font-size: .9rem;
    color: rgba(255,255,255,.65);
    flex-wrap: wrap;
}

.post-hero__avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    vertical-align: middle;
    margin-right: 6px;
    border: 1px solid rgba(255,255,255,.3);
}

.post-hero__sep { opacity: .4; }

/* ── LAYOUT ── */
.post-layout {
    max-width: 800px;
    margin: 0 auto;
    padding: var(--espaco-3xl) var(--espaco-xl) var(--espaco-4xl);
}

/* ── IMAGEM DESTAQUE ── */
.post-featured-img {
    margin: 0 0 var(--espaco-3xl);
    border-radius: var(--raio-2xl);
    overflow: hidden;
}
.post-featured-img__img {
    width: 100%;
    height: auto;
    display: block;
}
.post-featured-img figcaption {
    font-size: .8rem;
    color: var(--texto-muted);
    text-align: center;
    padding: var(--espaco-sm) var(--espaco-md);
}

/* ── CONTEÚDO ── */
.entry-content.wp-content {
    font-size: 1.1rem;
    line-height: 1.9;
    color: var(--texto-secundario);
}

.entry-content h2 { font-size: 1.9rem; margin: var(--espaco-3xl) 0 var(--espaco-lg); color: var(--texto-primario); }
.entry-content h3 { font-size: 1.5rem; margin: var(--espaco-2xl) 0 var(--espaco-md); color: var(--texto-primario); }
.entry-content p  { margin-bottom: var(--espaco-xl); }
.entry-content a  { color: var(--cor-primaria); text-decoration: underline; }
.entry-content blockquote {
    border-left: 4px solid var(--cor-primaria);
    margin: var(--espaco-2xl) 0;
    padding: var(--espaco-lg) var(--espaco-xl);
    background: var(--fundo-elevado);
    border-radius: 0 var(--raio-lg) var(--raio-lg) 0;
    font-style: italic;
    font-size: 1.15rem;
    color: var(--texto-primario);
}
.entry-content img {
    max-width: 100%;
    border-radius: var(--raio-lg);
    margin-block: var(--espaco-xl);
}

/* divisor */
.entry-content hr {
    border: none;
    height: 1px;
    background: var(--borda-glass);
    margin: var(--espaco-3xl) 0;
}

/* ── TAGS ── */
.post-tags {
    display: flex;
    align-items: center;
    gap: var(--espaco-sm);
    flex-wrap: wrap;
    margin-top: var(--espaco-3xl);
    padding-top: var(--espaco-xl);
    border-top: 1px solid var(--borda-glass);
}
.post-tags__label { font-size: 1rem; }

/* ── SHARE ── */
.post-share {
    display: flex;
    align-items: center;
    gap: var(--espaco-lg);
    flex-wrap: wrap;
    margin-top: var(--espaco-2xl);
    padding: var(--espaco-xl) var(--espaco-2xl);
    background: var(--fundo-card);
    border: 1px solid var(--borda-glass);
    border-radius: var(--raio-2xl);
}

.post-share__label {
    font-size: .8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--texto-muted);
    white-space: nowrap;
}

.post-share__btns { display: flex; gap: var(--espaco-md); flex-wrap: wrap; }

.post-share__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 999px;
    font-size: .85rem;
    font-weight: 700;
    text-decoration: none;
    transition: transform .2s, opacity .2s;
}
.post-share__btn:hover { transform: translateY(-2px); opacity: .9; }

.post-share__btn--wa { background: #25D366; color: #fff; }
.post-share__btn--fb { background: #1877F2; color: #fff; }
.post-share__btn--x  { background: #000; color: #fff; }

/* ── RELACIONADOS ── */
.post-relacionados { margin-top: var(--espaco-4xl); }

.post-relacionados__titulo {
    font-size: 1.6rem;
    margin-bottom: var(--espaco-2xl);
    padding-bottom: var(--espaco-lg);
    border-bottom: 1px solid var(--borda-glass);
}

.post-relacionados__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--espaco-xl);
}

.post-rel-card {
    background: var(--fundo-card);
    border: 1px solid var(--borda-glass);
    border-radius: var(--raio-xl);
    overflow: hidden;
    transition: transform .3s, border-color .3s;
}
.post-rel-card:hover { transform: translateY(-4px); border-color: var(--cor-primaria); }

.post-rel-card__img-wrap { display: block; aspect-ratio: 16/9; overflow: hidden; }
.post-rel-card__img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
.post-rel-card:hover .post-rel-card__img { transform: scale(1.05); }

.post-rel-card__body { padding: var(--espaco-lg); display: flex; flex-direction: column; gap: 6px; }

.post-rel-card__cat {
    font-size: .7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--cor-primaria);
}

.post-rel-card__titulo {
    font-size: .95rem;
    line-height: 1.45;
    margin: 0;
}
.post-rel-card__titulo a { color: var(--texto-primario); text-decoration: none; }
.post-rel-card__titulo a:hover { color: var(--cor-primaria); }

.post-rel-card__data { font-size: .75rem; color: var(--texto-muted); }

/* ── COMENTÁRIOS ── */
.post-comentarios {
    margin-top: var(--espaco-4xl);
    padding-top: var(--espaco-3xl);
    border-top: 1px solid var(--borda-glass);
}

/* ── RESPONSIVO ── */
@media (max-width: 768px) {
    .post-hero { min-height: 55vh; }
    .post-layout { padding-inline: var(--espaco-md); }
    .post-relacionados__grid { grid-template-columns: 1fr; }
    .post-share { flex-direction: column; align-items: flex-start; }
}
</style>

<?php get_footer(); ?>
