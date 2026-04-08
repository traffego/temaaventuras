<?php
/**
 * Template Name: Página de Contato
 * Template Post Type: page
 *
 * @package TemaAventuras
 */

get_header();

$empresa_tel   = ta_get('empresa_telefone', '(11) 99999-9999');
$empresa_email = ta_get('empresa_email', 'contato@aventuraextrema.com.br');
$empresa_end   = ta_get('empresa_endereco', 'Brotas, SP');
$wa_link       = ta_whatsapp_link('Olá! Gostaria de mais informações sobre as atividades.');
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner" style="min-height:300px;">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <div class="container page-banner__conteudo">
            <span class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">📞 <?php _e('Fale Conosco','temaaventuras'); ?></span>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="grid grid--2 gap-xl" style="align-items:start;">

                <!-- Formulário -->
                <div>
                    <h2 style="font-size:2rem;margin-bottom:var(--espaco-xl);"><?php _e('Envie sua mensagem','temaaventuras'); ?></h2>

                    <?php
                    // Exibe conteúdo da página (shortcode do CF7 ou WPForms)
                    while (have_posts()) : the_post();
                        if (get_the_content()) {
                            the_content();
                        } else {
                            // Formulário nativo HTML de fallback
                    ?>
                    <form class="form-contato" id="form-contato" method="post" action="">
                        <div class="grid grid--2" style="gap:var(--espaco-md);">
                            <div class="form-grupo">
                                <label for="contato-nome"><?php _e('Nome completo','temaaventuras'); ?> *</label>
                                <input type="text" id="contato-nome" name="nome" required placeholder="Seu nome">
                            </div>
                            <div class="form-grupo">
                                <label for="contato-tel"><?php _e('Telefone / WhatsApp','temaaventuras'); ?></label>
                                <input type="tel" id="contato-tel" name="telefone" placeholder="(11) 99999-9999">
                            </div>
                        </div>
                        <div class="form-grupo">
                            <label for="contato-email"><?php _e('E-mail','temaaventuras'); ?> *</label>
                            <input type="email" id="contato-email" name="email" required placeholder="seu@email.com">
                        </div>
                        <div class="form-grupo">
                            <label for="contato-atividade"><?php _e('Atividade de interesse','temaaventuras'); ?></label>
                            <select id="contato-atividade" name="atividade">
                                <option value=""><?php _e('Selecione...','temaaventuras'); ?></option>
                                <option>Rafting</option>
                                <option>Trilha</option>
                                <option>Tirolesa</option>
                                <option>Rapel</option>
                                <option>Boia Cross</option>
                                <option>Canionismo</option>
                                <option><?php _e('Pacote completo','temaaventuras'); ?></option>
                                <option><?php _e('Outro','temaaventuras'); ?></option>
                            </select>
                        </div>
                        <div class="form-grupo">
                            <label for="contato-mensagem"><?php _e('Mensagem','temaaventuras'); ?> *</label>
                            <textarea id="contato-mensagem" name="mensagem" required placeholder="<?php _e('Conte-nos mais sobre o que você procura...','temaaventuras'); ?>"></textarea>
                        </div>
                        <?php wp_nonce_field('contato_form_nonce','contato_nonce'); ?>
                        <button type="submit" class="btn btn--primario btn--grande" style="width:100%">
                            ✉️ <?php _e('Enviar Mensagem','temaaventuras'); ?>
                        </button>
                    </form>
                    <?php } endwhile; ?>
                </div>

                <!-- Informações de contato -->
                <div>
                    <h2 style="font-size:2rem;margin-bottom:var(--espaco-xl);"><?php _e('Informações','temaaventuras'); ?></h2>

                    <div style="display:flex;flex-direction:column;gap:var(--espaco-xl);">

                        <?php if ($empresa_tel): ?>
                        <div class="contato-info-item">
                            <div class="contato-info-icon">📞</div>
                            <div>
                                <strong><?php _e('Telefone / WhatsApp','temaaventuras'); ?></strong>
                                <p><a href="tel:<?php echo preg_replace('/\D/','',$empresa_tel); ?>"><?php echo esc_html($empresa_tel); ?></a></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($empresa_email): ?>
                        <div class="contato-info-item">
                            <div class="contato-info-icon">✉️</div>
                            <div>
                                <strong><?php _e('E-mail','temaaventuras'); ?></strong>
                                <p><a href="mailto:<?php echo antispambot($empresa_email); ?>"><?php echo antispambot($empresa_email); ?></a></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($empresa_end): ?>
                        <div class="contato-info-item">
                            <div class="contato-info-icon">📍</div>
                            <div>
                                <strong><?php _e('Endereço','temaaventuras'); ?></strong>
                                <p><?php echo esc_html($empresa_end); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- WhatsApp CTA -->
                        <a href="<?php echo esc_url($wa_link); ?>"
                           class="btn btn--primario btn--grande"
                           target="_blank"
                           rel="noopener noreferrer"
                           id="contato-whatsapp-cta"
                           style="margin-top:var(--espaco-md);">
                            📲 <?php _e('Falar pelo WhatsApp','temaaventuras'); ?>
                        </a>

                    </div>

                    <!-- Mapa (placeholder) -->
                    <div style="margin-top:var(--espaco-2xl); border-radius:var(--raio-xl); overflow:hidden; border:1px solid var(--borda-glass); aspect-ratio:16/10; background:var(--fundo-elevado); display:flex; align-items:center; justify-content:center; flex-direction:column; gap:var(--espaco-md);">
                        <span style="font-size:3rem;">🗺️</span>
                        <p style="color:var(--texto-muted); font-size:var(--tamanho-pequeno); text-align:center;">
                            <?php _e('Substitua por um iframe do Google Maps','temaaventuras'); ?>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>

<style>
.contato-info-item {
    display: flex;
    gap: var(--espaco-lg);
    align-items: flex-start;
}
.contato-info-icon {
    font-size: 1.8rem;
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    background: var(--fundo-glass);
    border: 1px solid var(--borda-glass);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.contato-info-item strong {
    display: block;
    color: var(--texto-primario);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 4px;
}
.contato-info-item a {
    color: var(--cor-primaria);
}
</style>
