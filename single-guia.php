<?php
/**
 * single-guia.php – Perfil do guia
 *
 * @package TemaAventuras
 */

get_header();

while ( have_posts() ) : the_post();
    $foto_id   = (int) get_post_meta( get_the_ID(), '_guia_foto', true );
    $foto_url  = $foto_id ? wp_get_attachment_image_url( $foto_id, 'large' ) : '';
    $foto_full = $foto_id ? wp_get_attachment_image_url( $foto_id, 'full' ) : '';
    $subtitulo = get_post_meta( get_the_ID(), '_guia_subtitulo', true );
    $descricao = get_post_meta( get_the_ID(), '_guia_descricao', true );

    $p_guias = get_pages( [
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'page-templates/page-guias.php',
    ] )[0] ?? null;
?>

<main id="conteudo-principal" role="main">

    <!-- ── HERO DO GUIA ── -->
    <div class="guia-hero">
        <!-- Fundo desfocado com a foto -->
        <?php if ( $foto_url ) : ?>
        <div class="guia-hero__bg" style="background-image:url('<?php echo esc_url( $foto_full ?: $foto_url ); ?>')" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="guia-hero__overlay" aria-hidden="true"></div>

        <div class="container guia-hero__inner">

            <!-- Foto -->
            <div class="guia-hero__foto-col">
                <div class="guia-perfil__foto-wrap">
                    <?php if ( $foto_url ) : ?>
                        <img src="<?php echo esc_url( $foto_url ); ?>"
                             alt="<?php the_title_attribute(); ?>"
                             class="guia-perfil__foto" />
                    <?php else : ?>
                        <div class="guia-perfil__foto guia-perfil__foto--placeholder">🧭</div>
                    <?php endif; ?>
                    <div class="guia-perfil__foto-ring" aria-hidden="true"></div>
                </div>
            </div>

            <!-- Identidade -->
            <div class="guia-hero__id-col">
                <?php if ( $subtitulo ) : ?>
                    <span class="guia-hero__esp"><?php echo esc_html( $subtitulo ); ?></span>
                <?php endif; ?>
                <h1 class="guia-hero__nome"><?php the_title(); ?></h1>

                <?php if ( $p_guias ) : ?>
                <a href="<?php echo get_permalink( $p_guias ); ?>" class="guia-hero__voltar" aria-label="Voltar para lista de guias">
                    ← <?php _e( 'Ver todos os guias', 'temaaventuras' ); ?>
                </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- ── BIO ── -->
    <?php if ( $descricao ) : ?>
    <section class="section section--pequena">
        <div class="container--estreito">
            <div class="guia-bio">
                <span class="guia-bio__label"><?php _e( 'Sobre', 'temaaventuras' ); ?></span>
                <div class="guia-bio__texto">
                    <?php echo wpautop( esc_html( $descricao ) ); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
