<?php
/**
 * single-atividade.php – Template individual de Atividade (CPT)
 * Redesign: Hero + Accordions
 *
 * @package TemaAventuras
 */

get_header();

while (have_posts()):
    the_post();

    $nivel = get_post_meta(get_the_ID(), '_atividade_nivel', true) ?: 'facil';
    $duracao = get_post_meta(get_the_ID(), '_atividade_duracao', true);
    $preco = get_post_meta(get_the_ID(), '_atividade_preco', true);
    $pessoas = get_post_meta(get_the_ID(), '_atividade_pessoas', true);
    $wa_link = ta_whatsapp_link('Olá! Quero saber mais sobre: ' . get_the_title());

    $at_data = get_post_meta(get_the_ID(), '_atividade_data', true);
    $at_horario = get_post_meta(get_the_ID(), '_atividade_horario', true);
    $at_vagas = get_post_meta(get_the_ID(), '_atividade_vagas', true);
    $at_obs = get_post_meta(get_the_ID(), '_atividade_obs', true);

    $at_img_id = (int) get_post_meta(get_the_ID(), '_atividade_imagem', true);
    if (!$at_img_id) {
        $at_img_id = get_post_thumbnail_id();
    }

    $hero_bg = $at_img_id ? wp_get_attachment_image_url($at_img_id, 'full') : '';
    $tem_data = !empty($at_data);
    $vagas_info = ta_vagas_disponiveis(get_the_ID());
    ?>

    <main id="conteudo-principal" role="main">

        <!-- HERO DA ATIVIDADE -->
        <div class="atividade-hero" style="background-image: url('<?php echo esc_url($hero_bg); ?>');">
            <div class="atividade-hero__overlay"></div>
            <div class="container atividade-hero__conteudo">

                <!-- Breadcrumb oculto
            <nav class="breadcrumb" aria-label="Navegação" style="margin-bottom:var(--espaco-md);">
                <a href="<?php echo home_url('/'); ?>" style="color:#fff;"><?php _e('Início', 'temaaventuras'); ?></a>
                <span style="color:#aaa;"> / </span>
                <a href="<?php echo home_url('/atividades'); ?>" style="color:#fff;"><?php _e('Atividades', 'temaaventuras'); ?></a>
                <span style="color:#aaa;"> / </span>
                <span style="color:#ccc;"><?php the_title(); ?></span>
            </nav>
            -->

                <div class="atividade-hero__badges">
                    <?php echo ta_nivel_badge($nivel); ?>
                    <?php
                    $cats = get_the_terms(get_the_ID(), 'categoria_atividade');
                    if ($cats && !is_wp_error($cats)):
                        foreach ($cats as $cat)
                            echo '<span class="badge badge--azul">' . esc_html($cat->name) . '</span>';
                    endif;
                    ?>
                </div>

                <h1 class="atividade-hero__titulo"><?php the_title(); ?></h1>

                <!-- Botão de Reserva sobre o Hero -->
                <div class="atividade-hero__acoes">
                    <?php if ($tem_data && $vagas_info['livres'] > 0): ?>
                        <a href="<?php echo esc_url(ta_checkout_url(get_the_ID())); ?>"
                            class="btn btn--primario btn--grande pulsar">
                            🎟️ <?php _e('Reservar Atividade Agora', 'temaaventuras'); ?>
                        </a>
                    <?php elseif ($tem_data): ?>
                        <div class="btn" style="background:#555;color:#ccc;pointer-events:none;">
                            😔 <?php _e('Evento Lotado', 'temaaventuras'); ?>
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($wa_link); ?>" class="btn btn--ghost" target="_blank"
                        rel="noopener noreferrer">
                        📲 <?php _e('Falar no WhatsApp', 'temaaventuras'); ?>
                    </a>
                </div>

            </div>
        </div>

        <!-- CONTEÚDO EM ACORDEÃO -->
        <section class="section section--pequena">
            <div class="container container--estreito">

                <!-- Acordeão 1: Informações e Reserva -->
                <details class="ta-accordion" open>
                    <summary class="ta-accordion__resumo">
                        <h2>📅 Informações Técnicas & Valores</h2>
                        <span class="ta-accordion__icon">▼</span>
                    </summary>
                    <div class="ta-accordion__conteudo">
                        <div class="grid grid--2 gap-lg" style="margin-bottom:var(--espaco-xl);">

                            <div
                                style="background:var(--fundo-elevado);border:1px solid var(--borda-glass);padding:var(--espaco-xl);border-radius:var(--raio-lg);">
                                <h3 style="margin-bottom:var(--espaco-md);color:var(--cor-secundaria); font-size: 1.2rem;">
                                    Detalhes da Reserva</h3>
                                <?php if ($at_data): ?>
                                    <p><strong>Data:</strong> <?php echo date_i18n('d/m/Y', strtotime($at_data)); ?></p>
                                <?php endif; ?>
                                <?php if ($at_horario): ?>
                                    <p><strong>Horário:</strong> <?php echo esc_html($at_horario); ?></p>
                                <?php endif; ?>
                                <?php if ($tem_data): ?>
                                    <p><strong>Disponibilidade:</strong> <?php echo $vagas_info['livres']; ?> vagas de
                                        <?php echo intval($at_vagas); ?></p>
                                <?php endif; ?>
                                <?php if ($preco): ?>
                                    <p><strong>Investimento:</strong> <?php echo ta_preco($preco); ?> por pessoa</p>
                                <?php endif; ?>
                            </div>

                            <div
                                style="background:var(--fundo-elevado);border:1px solid var(--borda-glass);padding:var(--espaco-xl);border-radius:var(--raio-lg);">
                                <h3 style="margin-bottom:var(--espaco-md);color:var(--cor-secundaria); font-size: 1.2rem;">
                                    Ficha Técnica</h3>
                                <?php if ($duracao): ?>
                                    <p><strong>Duração:</strong> <?php echo esc_html($duracao); ?></p>
                                <?php endif; ?>
                                <?php if ($pessoas): ?>
                                    <p><strong>Mínimo:</strong> <?php echo intval($pessoas); ?> pessoas</p>
                                <?php endif; ?>
                                <p><strong>Nível:</strong> <?php echo strtoupper($nivel); ?></p>
                                <?php if ($at_obs): ?>
                                    <p style="font-size:0.85em;color:var(--texto-muted);margin-top:10px;"><em>📝 Obs:
                                            <?php echo esc_html($at_obs); ?></em></p>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </details>

                <!-- Acordeão 2: Descrição -->
                <details class="ta-accordion">
                    <summary class="ta-accordion__resumo">
                        <h2>📝 Descrição da Aventura</h2>
                        <span class="ta-accordion__icon">▼</span>
                    </summary>
                    <div class="ta-accordion__conteudo wp-content">
                        <?php the_content(); ?>
                    </div>
                </details>

            </div>
        </section>

        <!-- GALERIA -->
        <?php
        $galeria_ids = get_post_meta(get_the_ID(), '_atividade_galeria', true);
        if ($galeria_ids && is_array($galeria_ids)):
            ?>
            <section class="section section--sem-top">
                <div class="container">
                    <h2 style="font-size:1.8rem;margin-bottom:var(--espaco-xl);text-align:center;">
                        <?php _e('Galeria', 'temaaventuras'); ?></h2>
                    <div class="masonry">
                        <?php foreach ($galeria_ids as $img_id):
                            $src_full = wp_get_attachment_image_url($img_id, 'full');
                            $alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                            ?>
                            <div class="masonry__item galeria-item">
                                <a href="<?php echo esc_url($src_full); ?>" data-lightbox="<?php echo esc_attr($src_full); ?>"
                                    data-caption="<?php echo esc_attr($alt); ?>">
                                    <?php echo wp_get_attachment_image($img_id, 'aventura-galeria', false, ['class' => 'galeria-img', 'loading' => 'lazy']); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    </main>

<?php endwhile;
get_footer(); ?>

<style>
    /* CSS do Novo Hero */
    .atividade-hero {
        position: relative;
        min-height: 85vh;
        display: flex;
        align-items: flex-end;
        /* Conteúdo agrupado embaixo no Hero */
        background-size: cover;
        background-position: center;
        padding-bottom: var(--espaco-4xl);
    }

    .atividade-hero__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(10, 17, 13, 1) 0%, rgba(10, 17, 13, 0.4) 60%, transparent 100%);
        z-index: 1;
    }

    .atividade-hero__conteudo {
        position: relative;
        z-index: 2;
        width: 100%;
    }

    .atividade-hero__titulo {
        font-size: clamp(3rem, 6vw, 5rem);
        line-height: 1.1;
        margin-bottom: var(--espaco-lg);
        color: #fff;
    }

    .atividade-hero__badges {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .atividade-hero__acoes {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* CSS do Accordion Nativo */
    .ta-accordion {
        background: var(--fundo-card);
        border: 1px solid var(--borda-glass);
        border-radius: var(--raio-lg);
        margin-bottom: var(--espaco-md);
        overflow: hidden;
    }

    .ta-accordion__resumo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--espaco-lg) var(--espaco-xl);
        cursor: pointer;
        user-select: none;
        list-style: none;
        /* remove seta padrão iOS/Chrome */
        background: var(--fundo-elevado);
        transition: background var(--transicao-rapida);
    }

    .ta-accordion__resumo::-webkit-details-marker {
        display: none;
        /* Safari */
    }

    .ta-accordion__resumo:hover {
        background: var(--fundo-glass);
    }

    .ta-accordion__resumo h2 {
        margin: 0;
        font-size: 1.4rem;
        color: var(--texto-primario);
    }

    .ta-accordion__icon {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
        color: var(--cor-secundaria);
    }

    .ta-accordion[open] .ta-accordion__icon {
        transform: rotate(-180deg);
    }

    .ta-accordion__conteudo {
        padding: var(--espaco-xl);
        border-top: 1px solid var(--borda-glass);
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .atividade-hero {
            min-height: 90vh;
            /* Mais alto no mobile para caber os botões empilhados */
        }

        .atividade-hero__acoes {
            flex-direction: column;
            align-items: stretch;
        }

        .atividade-hero__acoes .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>