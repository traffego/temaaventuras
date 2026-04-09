<?php
/**
 * single-guia.php – Página de detalhes do guia
 *
 * @package TemaAventuras
 */

get_header();

while ( have_posts() ) : the_post();
    $foto_id   = (int) get_post_meta( get_the_ID(), '_guia_foto', true );
    $foto_url  = $foto_id ? wp_get_attachment_image_url( $foto_id, 'large' ) : '';
    $subtitulo = get_post_meta( get_the_ID(), '_guia_subtitulo', true );
    $descricao = get_post_meta( get_the_ID(), '_guia_descricao', true );
    $wa_link   = ta_whatsapp_link( sprintf( __( 'Olá! Gostaria de saber mais sobre as atividades com o guia %s.', 'temaaventuras' ), get_the_title() ) );
?>

<main id="conteudo-principal" role="main">

    <!-- Banner compacto -->
    <div class="page-banner" style="min-height:220px;">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <div class="container page-banner__conteudo" style="padding-block:var(--espaco-2xl);">
            <nav class="breadcrumb" aria-label="Navegação">
                <a href="<?php echo home_url('/'); ?>"><?php _e('Início','temaaventuras'); ?></a> /
                <?php
                $p_guias = get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-guias.php'])[0] ?? null;
                if ($p_guias): ?>
                    <a href="<?php echo get_permalink($p_guias); ?>"><?php _e('Guias','temaaventuras'); ?></a> /
                <?php endif; ?>
                <span><?php the_title(); ?></span>
            </nav>
        </div>
    </div>

    <!-- Perfil -->
    <section class="section">
        <div class="container--estreito">
            <div class="guia-perfil">

                <!-- Foto -->
                <div class="guia-perfil__foto-col">
                    <?php if ( $foto_url ) : ?>
                        <div class="guia-perfil__foto-wrap">
                            <img src="<?php echo esc_url( $foto_url ); ?>"
                                 alt="<?php the_title_attribute(); ?>"
                                 class="guia-perfil__foto" />
                            <div class="guia-perfil__foto-ring" aria-hidden="true"></div>
                        </div>
                    <?php else : ?>
                        <div class="guia-perfil__foto-wrap">
                            <div class="guia-perfil__foto guia-perfil__foto--placeholder">🧭</div>
                        </div>
                    <?php endif; ?>

                    <!-- CTA -->
                    <div class="guia-perfil__ctas">
                        <a href="<?php echo esc_url( $wa_link ); ?>"
                           class="btn btn--primario"
                           target="_blank" rel="noopener noreferrer">
                            📲 <?php _e('Falar no WhatsApp','temaaventuras'); ?>
                        </a>
                        <?php
                        $p_guias = get_pages(['meta_key'=>'_wp_page_template','meta_value'=>'page-templates/page-guias.php'])[0] ?? null;
                        if ($p_guias): ?>
                        <a href="<?php echo get_permalink($p_guias); ?>" class="btn btn--ghost">
                            ← <?php _e('Ver todos os guias','temaaventuras'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Infos -->
                <div class="guia-perfil__info-col">
                    <?php if ( $subtitulo ) : ?>
                        <span class="guia-perfil__especialidade"><?php echo esc_html( $subtitulo ); ?></span>
                    <?php endif; ?>

                    <h1 class="guia-perfil__nome"><?php the_title(); ?></h1>

                    <?php if ( $descricao ) : ?>
                    <div class="guia-perfil__bio">
                        <?php echo wpautop( esc_html( $descricao ) ); ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
