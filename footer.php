<?php
/**
 * Footer do tema
 *
 * @package TemaAventuras
 */
?>

<?php
// Elementor Pro controla o footer? Senão usa o nativo
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :

    $empresa_nome    = ta_get( 'empresa_nome', get_bloginfo( 'name' ) );
    $empresa_slogan  = ta_get( 'empresa_slogan', get_bloginfo( 'description' ) );
    $empresa_tel     = ta_get( 'empresa_telefone', '' );
    $empresa_email   = ta_get( 'empresa_email', '' );
    $empresa_end     = ta_get( 'empresa_endereco', '' );
    $instagram       = ta_get( 'empresa_instagram', '' );
    $facebook        = ta_get( 'empresa_facebook', '' );
    $youtube         = ta_get( 'empresa_youtube', '' );
    $wa_link         = ta_whatsapp_link();
    $copy            = ta_get( 'footer_texto_copy', '&copy; ' . date( 'Y' ) . ' ' . $empresa_nome . '. Todos os direitos reservados.' );
    $footer_desc     = ta_get( 'footer_descricao', 'Especialistas em aventura. Segurança, adrenalina e natureza em cada experiência.' );
?>

<!-- =========================================
     FAIXA CTA ANTES DO FOOTER
     ========================================= -->
<section class="cta-strip section--pequena" id="contato" aria-labelledby="cta-titulo">
    <div class="container">
        <div class="cta-strip__inner flex-center" style="flex-direction:column; text-align:center; gap: var(--espaco-xl);">
            <div>
                <p class="section-header__eyebrow"><?php _e( 'Pronto para a aventura?', 'temaaventuras' ); ?></p>
                <h2 id="cta-titulo" style="font-size: clamp(2rem, 5vw, 3.5rem); margin-bottom: var(--espaco-md);">
                    <?php _e( 'Reserve Sua Aventura Hoje!', 'temaaventuras' ); ?>
                </h2>
                <p style="color: var(--texto-muted); max-width: 560px; margin-inline: auto;">
                    <?php _e( 'Entre em contato pelo WhatsApp e nossa equipe vai montar o pacote perfeito para você.', 'temaaventuras' ); ?>
                </p>
            </div>
            <div class="flex gap-md flex-wrap flex-center">
                <a href="<?php echo esc_url( $wa_link ); ?>"
                   class="btn btn--primario btn--grande pulsar"
                   target="_blank"
                   rel="noopener noreferrer"
                   id="footer-cta-whatsapp">
                    📲 <?php _e( 'Falar no WhatsApp', 'temaaventuras' ); ?>
                </a>
                <?php if ( $empresa_tel ) : ?>
                <a href="tel:<?php echo preg_replace( '/\D/', '', $empresa_tel ); ?>"
                   class="btn btn--ghost btn--grande"
                   id="footer-cta-telefone">
                    📞 <?php echo esc_html( $empresa_tel ); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- =========================================
     FOOTER PRINCIPAL
     ========================================= -->
<footer class="footer" role="contentinfo">
    <div class="container">
        <div class="footer__grid">

            <!-- Col 1: Marca -->
            <div class="footer__logo-area">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <div class="navbar__logo-texto" style="font-size: 2rem;">
                        <?php echo esc_html( $empresa_nome ); ?>
                    </div>
                <?php endif; ?>
                <p><?php echo esc_html( $footer_desc ); ?></p>

                <!-- Redes Sociais -->
                <div class="footer__social" role="list" aria-label="Redes Sociais">
                    <?php if ( $instagram ) : ?>
                    <a href="<?php echo esc_url( $instagram ); ?>"
                       class="footer__social-link"
                       target="_blank"
                       rel="noopener noreferrer"
                       role="listitem"
                       aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <?php if ( $facebook ) : ?>
                    <a href="<?php echo esc_url( $facebook ); ?>"
                       class="footer__social-link"
                       target="_blank"
                       rel="noopener noreferrer"
                       role="listitem"
                       aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <?php if ( $youtube ) : ?>
                    <a href="<?php echo esc_url( $youtube ); ?>"
                       class="footer__social-link"
                       target="_blank"
                       rel="noopener noreferrer"
                       role="listitem"
                       aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $wa_link ); ?>"
                       class="footer__social-link"
                       target="_blank"
                       rel="noopener noreferrer"
                       role="listitem"
                       aria-label="WhatsApp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Col 2: Links rápidos -->
            <div>
                <h3 class="footer__titulo"><?php _e( 'Links Rápidos', 'temaaventuras' ); ?></h3>
                <ul class="footer__links">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'menu-footer',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'fallback_cb'    => function() {
                            $links = [
                                'Início'      => home_url( '/' ),
                                'Atividades'  => home_url( '/atividades' ),
                                'Pacotes'     => home_url( '/pacotes' ),
                                'Blog'        => home_url( '/blog' ),
                                'Sobre Nós'   => home_url( '/sobre' ),
                                'Contato'     => home_url( '/contato' ),
                            ];
                            foreach ( $links as $label => $url ) {
                                echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
                            }
                        },
                    ] );
                    ?>
                </ul>
            </div>

            <!-- Col 3: Atividades -->
            <div>
                <h3 class="footer__titulo"><?php _e( 'Atividades', 'temaaventuras' ); ?></h3>
                <ul class="footer__links">
                    <?php
                    $atividades = ta_get_atividades( 6 );
                    if ( $atividades->have_posts() ) :
                        while ( $atividades->have_posts() ) : $atividades->the_post();
                            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                        endwhile;
                        wp_reset_postdata();
                    else :
                        $defaults = [ 'Rafting', 'Trilha', 'Tirolesa', 'Rapel', 'Boia Cross', 'Canionismo' ];
                        foreach ( $defaults as $a ) {
                            echo '<li><a href="' . esc_url( home_url( '/atividades' ) ) . '">' . esc_html( $a ) . '</a></li>';
                        }
                    endif;
                    ?>
                </ul>
            </div>

            <!-- Col 4: Contato -->
            <div>
                <h3 class="footer__titulo"><?php _e( 'Contato', 'temaaventuras' ); ?></h3>
                <ul class="footer__links">
                    <?php if ( $empresa_end ) : ?>
                    <li>📍 <?php echo esc_html( $empresa_end ); ?></li>
                    <?php endif; ?>
                    <?php if ( $empresa_tel ) : ?>
                    <li>📞 <a href="tel:<?php echo preg_replace( '/\D/', '', $empresa_tel ); ?>"><?php echo esc_html( $empresa_tel ); ?></a></li>
                    <?php endif; ?>
                    <?php if ( $empresa_email ) : ?>
                    <li>✉️ <a href="mailto:<?php echo antispambot( $empresa_email ); ?>"><?php echo antispambot( $empresa_email ); ?></a></li>
                    <?php endif; ?>
                </ul>

                <?php if ( is_active_sidebar( 'footer-col-3' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-col-3' ); ?>
                <?php endif; ?>
            </div>

        </div><!-- /.footer__grid -->

        <!-- Bottom Bar -->
        <div class="footer__bottom">
            <p class="footer__copy"><?php echo wp_kses_post( $copy ); ?></p>
            <p class="footer__copy" style="font-size:0.75rem;">
                <?php _e( 'Desenvolvido com', 'temaaventuras' ); ?> ❤️
                <?php _e( 'por', 'temaaventuras' ); ?>
                <a href="https://traffego.com.br" target="_blank" rel="noopener noreferrer">Traffego</a>
                &nbsp;|&nbsp;
                <a href="<?php echo esc_url( home_url( '/politica-de-privacidade' ) ); ?>">
                    <?php _e( 'Política de Privacidade', 'temaaventuras' ); ?>
                </a>
            </p>
        </div>

    </div><!-- /.container -->
</footer>

<?php endif; // end elementor footer check ?>

<!-- Botão flutuante WhatsApp -->
<a href="<?php echo esc_url( $wa_link ); ?>"
   class="whatsapp-float"
   id="whatsapp-float-btn"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="<?php _e( 'Falar no WhatsApp', 'temaaventuras' ); ?>">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
    </svg>
</a>

<style>
.whatsapp-float {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 60px;
    height: 60px;
    background: #25D366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
    z-index: var(--z-nav);
    transition: all 0.3s ease;
    text-decoration: none;
}
.whatsapp-float:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 30px rgba(37, 211, 102, 0.6);
    color: white;
}

.cta-strip {
    background: var(--gradiente-hero);
    position: relative;
    overflow: hidden;
}

.cta-strip::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}
</style>

<?php wp_footer(); ?>
</body>
</html>
