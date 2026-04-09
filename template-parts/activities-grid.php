<?php
/**
 * Template Part: Activities Grid
 *
 * @package TemaAventuras
 */

$atividades = ta_get_atividades( 6 );

// Atividades fictícias para fallback visual
$fallback = [
    [ 'titulo' => 'Rafting Radical',    'nivel' => 'dificil', 'duracao' => '4h',  'preco' => '180', 'emoji' => '🌊' ],
    [ 'titulo' => 'Trilha na Mata',     'nivel' => 'facil',   'duracao' => '3h',  'preco' => '90',  'emoji' => '🥾' ],
    [ 'titulo' => 'Tirolesa Extrema',   'nivel' => 'medio',   'duracao' => '2h',  'preco' => '120', 'emoji' => '🪂' ],
    [ 'titulo' => 'Rapel em Cachoeira', 'nivel' => 'dificil', 'duracao' => '3h',  'preco' => '150', 'emoji' => '🧗' ],
    [ 'titulo' => 'Boia Cross',         'nivel' => 'medio',   'duracao' => '2h',  'preco' => '100', 'emoji' => '🏊' ],
    [ 'titulo' => 'Canionismo',         'nivel' => 'extremo', 'duracao' => '6h',  'preco' => '220', 'emoji' => '⛰️' ],
];
?>

<!-- =========================================
     ATIVIDADES GRID
     ========================================= -->
<section class="section" id="atividades" aria-labelledby="atividades-titulo">
    <div class="container">

        <div class="section-header animar-entrada">
            <span class="section-header__eyebrow">🌿 <?php _e( 'O Que Oferecemos', 'temaaventuras' ); ?></span>
            <h2 id="atividades-titulo" class="section-header__titulo">
                <?php _e( 'Atividades de Aventura', 'temaaventuras' ); ?>
            </h2>
            <p class="section-header__subtitulo">
                <?php _e( 'Experiências únicas para todos os níveis. Da trilha relaxante ao rafting mais intenso.', 'temaaventuras' ); ?>
            </p>
        </div>

        <div class="grid grid--4" role="list">

        <?php if ( $atividades->have_posts() ) :
            $delay = 1;
            while ( $atividades->have_posts() ) : $atividades->the_post();
                $nivel   = get_post_meta( get_the_ID(), '_atividade_nivel', true ) ?: 'facil';
                $duracao = get_post_meta( get_the_ID(), '_atividade_duracao', true );
                $preco   = get_post_meta( get_the_ID(), '_atividade_preco', true );
        ?>

            <article class="card-atividade animar-entrada delay-<?php echo min( $delay++, 6 ); ?>"
                     role="listitem"
                     aria-label="<?php the_title_attribute(); ?>">

                <!-- Imagem adaptada para usar metadata e evitar quebra de layout -->
                <?php
                $img_id = (int) get_post_meta( get_the_ID(), '_atividade_imagem', true );
                ?>
                <div class="card-atividade__img-wrapper" style="height: 100%;">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'aventura-card', [ 'class' => 'card-atividade__img', 'loading' => 'lazy', 'alt' => get_the_title() ] ); ?>
                <?php elseif ( $img_id ) : ?>
                    <?php echo wp_get_attachment_image( $img_id, 'aventura-card', false, [ 'class' => 'card-atividade__img', 'loading' => 'lazy', 'alt' => get_the_title() ] ); ?>
                <?php else : ?>
                    <div class="card-atividade__img" style="background: var(--gradiente-hero); display:flex; align-items:center; justify-content:center; font-size:4rem; height: 100%;">
                        🌊
                    </div>
                <?php endif; ?>
                </div>

                <div class="card-atividade__overlay">
                    <div class="card-atividade__badge">
                        <?php echo ta_nivel_badge( $nivel ); ?>
                    </div>

                    <h3 class="card-atividade__titulo"><?php the_title(); ?></h3>

                    <div class="card-atividade__meta">
                        <?php if ( $duracao ) : ?>
                        <span class="card-atividade__detalhe">⏱ <?php echo esc_html( $duracao ); ?></span>
                        <?php endif; ?>
                        <?php if ( $preco ) : ?>
                        <span class="card-atividade__detalhe" style="color:var(--cor-secundaria); font-weight:700;">
                            <?php echo ta_preco( $preco ); ?> <?php _e( '/pessoa', 'temaaventuras' ); ?>
                        </span>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( ta_checkout_url( get_the_ID() ) ); ?>"
                           class="btn btn--primario btn--pequeno"
                           aria-label="<?php printf( __( 'Reservar Agora: %s', 'temaaventuras' ), get_the_title() ); ?>">
                            <?php _e( 'Reservar Agora', 'temaaventuras' ); ?>
                        </a>
                    </div>
                </div>

            </article>

        <?php endwhile; wp_reset_postdata();

        else : // Fallback sem CPTs cadastrados
            foreach ( $fallback as $i => $item ) : ?>

            <article class="card-atividade animar-entrada delay-<?php echo min( $i + 1, 6 ); ?>"
                     role="listitem"
                     aria-label="<?php echo esc_attr( $item['titulo'] ); ?>">

                <div class="card-atividade__img atividade-emoji-bg" aria-hidden="true"
                     style="background: var(--gradiente-hero); display:flex;align-items:center;justify-content:center;font-size:5rem;height:100%;">
                    <?php echo $item['emoji']; ?>
                </div>

                <div class="card-atividade__overlay">
                    <div class="card-atividade__badge">
                        <?php echo ta_nivel_badge( $item['nivel'] ); ?>
                    </div>
                    <h3 class="card-atividade__titulo"><?php echo esc_html( $item['titulo'] ); ?></h3>
                    <div class="card-atividade__meta">
                        <span class="card-atividade__detalhe">⏱ <?php echo esc_html( $item['duracao'] ); ?></span>
                        <span class="card-atividade__detalhe" style="color:var(--cor-secundaria);font-weight:700;">
                            R$ <?php echo esc_html( $item['preco'] ); ?>/pessoa
                        </span>
                        <a href="<?php echo esc_url( home_url( '/atividades' ) ); ?>" class="btn btn--secundario btn--pequeno">
                            <?php _e( 'Saiba Mais', 'temaaventuras' ); ?>
                        </a>
                    </div>
                </div>

            </article>

        <?php endforeach; endif; ?>

        </div><!-- /.grid -->

        <div class="texto-centro" style="margin-top: var(--espaco-2xl);">
            <a href="<?php echo esc_url( home_url( '/atividades' ) ); ?>"
               class="btn btn--secundario btn--grande"
               id="ver-todas-atividades">
                <?php _e( 'Ver Todas as Atividades', 'temaaventuras' ); ?> →
            </a>
        </div>

    </div>
</section>
