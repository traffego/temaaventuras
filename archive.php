<?php
/**
 * archive.php – Arquivos de categorias, tags, atividades, etc.
 *
 * @package TemaAventuras
 */

get_header();
?>

<main id="conteudo-principal" role="main">

    <!-- Banner do arquivo -->
    <div class="page-banner" style="min-height:300px;">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <div class="container page-banner__conteudo">
            <div class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">
                <?php
                if ( is_post_type_archive('atividade') ) echo '🌊 Aventuras';
                elseif ( is_post_type_archive('pacote') )    echo '💼 Pacotes';
                elseif ( is_category() )                     echo '📂 Categoria';
                elseif ( is_tag() )                          echo '🏷 Tag';
                else                                         echo '📋 Arquivo';
                ?>
            </div>
            <h1 class="page-banner__titulo">
                <?php
                if ( is_post_type_archive() ) post_type_archive_title();
                else                          the_archive_title();
                ?>
            </h1>
            <?php the_archive_description('<p style="color:rgba(255,255,255,0.7);max-width:600px;margin-top:var(--espaco-md);">', '</p>'); ?>
        </div>
    </div>

    <section class="section">
        <div class="container">

            <?php if ( have_posts() ) : ?>
                <div class="grid grid--auto-fit-sm">
                    <?php while ( have_posts() ) : the_post();

                        // CPT Atividade
                        if ( get_post_type() === 'atividade' ):
                            $nivel   = get_post_meta(get_the_ID(),'_atividade_nivel',true) ?: 'facil';
                            $duracao = get_post_meta(get_the_ID(),'_atividade_duracao',true);
                            $preco   = get_post_meta(get_the_ID(),'_atividade_preco',true);
                            
                            $at_data    = get_post_meta( get_the_ID(), '_atividade_data', true );
                            $vagas_info = ta_vagas_disponiveis( get_the_ID() );
                            $at_img_id  = (int) get_post_meta( get_the_ID(), '_atividade_imagem', true );
                    ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('card-atividade'); ?> style="aspect-ratio:3/4;" aria-label="<?php the_title_attribute(); ?>">
                            <?php 
                            if ($at_img_id): 
                                echo wp_get_attachment_image( $at_img_id, 'aventura-card', false, ['class'=>'card-atividade__img','loading'=>'lazy','alt'=>get_the_title()] );
                            elseif (has_post_thumbnail()): 
                                the_post_thumbnail('aventura-card',['class'=>'card-atividade__img','loading'=>'lazy','alt'=>get_the_title()]); 
                            else: ?>
                                <div class="card-atividade__img" style="background:var(--gradiente-hero);display:flex;align-items:center;justify-content:center;font-size:4rem;height:100%;">🌊</div>
                            <?php endif; ?>
                            
                            <div class="card-atividade__overlay">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;width:100%;">
                                    <div class="card-atividade__badge"><?php echo ta_nivel_badge($nivel); ?></div>
                                    <?php if ($at_data): ?>
                                        <div class="badge" style="background:rgba(0,0,0,0.5);font-size:0.7rem;backdrop-filter:blur(4px);">📅 <?php echo date_i18n('d/m', strtotime($at_data)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <h2 class="card-atividade__titulo"><?php the_title(); ?></h2>
                                <div class="card-atividade__meta">
                                    <?php if($at_data): 
                                        if ($vagas_info['livres'] > 0): ?>
                                            <span class="card-atividade__detalhe" style="color:#4ade80;">✅ <?php echo $vagas_info['livres']; ?> vagas</span>
                                        <?php else: ?>
                                            <span class="card-atividade__detalhe" style="color:#ef4444;">Lotado</span>
                                        <?php endif;
                                    elseif($duracao): ?>
                                        <span class="card-atividade__detalhe">⏱ <?php echo esc_html($duracao); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if($preco): ?><span class="card-atividade__detalhe" style="color:var(--cor-secundaria);font-weight:700;"><?php echo ta_preco($preco); ?> p/p</span><?php endif; ?>
                                    <a href="<?php the_permalink(); ?>" class="btn btn--secundario btn--pequeno" style="padding:0.4rem 0.8rem;font-size:0.8rem;">Ver Detalhes</a>
                                </div>
                            </div>
                        </article>

                    <?php else: // Post padrão ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('card card-post'); ?>>
                            <?php if (has_post_thumbnail()): ?>
                            <div style="aspect-ratio:16/9;overflow:hidden;"><?php the_post_thumbnail('aventura-thumb',['class'=>'card-post__thumbnail','loading'=>'lazy']); ?></div>
                            <?php endif; ?>
                            <div class="card-post__corpo">
                                <h2 class="card-post__titulo" style="font-size:1.2rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <p class="card-post__excerpt"><?php echo wp_trim_words(get_the_excerpt(),18); ?></p>
                                <div class="card-post__meta">
                                    <span>📅 <?php echo get_the_date('d M Y'); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="card-post__lermais"><?php _e('Ler mais','temaaventuras'); ?> →</a>
                                </div>
                            </div>
                        </article>
                    <?php endif; endwhile; ?>
                </div>

                <div style="margin-top:var(--espaco-3xl);">
                    <?php the_posts_pagination(['prev_text' => '← Anterior', 'next_text' => 'Próximo →']); ?>
                </div>

            <?php else : ?>
                <div class="texto-centro" style="padding:var(--espaco-4xl) 0;">
                    <p style="font-size:3rem;">🌿</p>
                    <h2><?php _e('Nenhum item encontrado.', 'temaaventuras'); ?></h2>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn--primario" style="margin-top:var(--espaco-xl);"><?php _e('Voltar ao Início', 'temaaventuras'); ?></a>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer(); ?>
