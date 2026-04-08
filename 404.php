<?php
/**
 * 404.php – Página não encontrada
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">
    <section class="section hero" style="min-height:90vh;">
        <div class="hero__overlay" aria-hidden="true" style="background:linear-gradient(135deg,rgba(0,39,118,0.85),rgba(0,156,59,0.7));"></div>
        <div class="hero__conteudo">
            <div class="container texto-centro">
                <div style="font-size:8rem; line-height:1; margin-bottom:var(--espaco-lg); animation: float 4s ease-in-out infinite;" aria-hidden="true">
                    🏕️
                </div>
                <h1 style="font-size:clamp(4rem,12vw,10rem); color:var(--cor-secundaria); line-height:1; margin-bottom:0;">404</h1>
                <h2 style="font-size:clamp(1.5rem,3vw,2.5rem); margin-bottom:var(--espaco-lg);">
                    <?php _e('Você se perdeu na trilha!', 'temaaventuras'); ?>
                </h2>
                <p style="color:rgba(255,255,255,0.75); max-width:500px; margin-inline:auto; margin-bottom:var(--espaco-2xl);">
                    <?php _e('Não se preocupe — até os melhores aventureiros erram o caminho às vezes. Vamos te guiar de volta.', 'temaaventuras'); ?>
                </p>
                <div style="display:flex; gap:var(--espaco-md); justify-content:center; flex-wrap:wrap;">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primario btn--grande" id="404-home-btn">
                        🏠 <?php _e('Voltar ao Início', 'temaaventuras'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/atividades')); ?>" class="btn btn--ghost btn--grande" id="404-atividades-btn">
                        🌊 <?php _e('Ver Atividades', 'temaaventuras'); ?>
                    </a>
                </div>
                <!-- Busca rápida -->
                <div style="margin-top:var(--espaco-3xl); max-width:400px; margin-inline:auto;">
                    <p style="font-size:var(--tamanho-pequeno); color:rgba(255,255,255,0.5); margin-bottom:var(--espaco-md);">
                        <?php _e('Ou busque o que procura:', 'temaaventuras'); ?>
                    </p>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
