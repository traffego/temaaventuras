<?php
/**
 * Template Part: Packages / Pricing
 *
 * @package TemaAventuras
 */

$pacotes  = ta_get_pacotes( 3 );
$wa_link  = ta_whatsapp_link( 'Olá! Tenho interesse em um pacote de aventura.' );

$fallback_pacotes = [
    [
        'nome'     => 'Aventureiro',
        'preco'    => '299',
        'periodo'  => 'por pessoa / final de semana',
        'destaque' => false,
        'inclui'   => [ 'Rafting nível básico', 'Trilha guiada', 'Café da manhã', 'Seguro básico', 'Kit aventureiro' ],
    ],
    [
        'nome'     => 'Explorador',
        'preco'    => '549',
        'periodo'  => 'por pessoa / final de semana',
        'destaque' => true,
        'inclui'   => [ 'Rafting + Boia Cross', 'Trilha + Tirolesa', 'Café + Almoço', 'Seguro completo', 'Kit aventureiro premium', 'Fotos e vídeos', 'Transporte incluso' ],
    ],
    [
        'nome'     => 'Extremo',
        'preco'    => '899',
        'periodo'  => 'por pessoa / final de semana',
        'destaque' => false,
        'inclui'   => [ 'Todas as atividades', 'Rapel + Canionismo', 'Hospedagem inclusa', 'Seguro premium', 'Kit exclusivo', 'Fotógrafo dedicado', 'Transfer aeroporto', 'Alimentação completa' ],
    ],
];
?>

<!-- =========================================
     PACOTES / PREÇOS
     ========================================= -->
<section class="section section--escura" id="pacotes" aria-labelledby="pacotes-titulo">
    <div class="container">

        <div class="section-header animar-entrada">
            <span class="section-header__eyebrow">💼 <?php _e( 'Nossos Pacotes', 'temaaventuras' ); ?></span>
            <h2 id="pacotes-titulo" class="section-header__titulo">
                <?php _e( 'Escolha Sua Aventura', 'temaaventuras' ); ?>
            </h2>
            <p class="section-header__subtitulo">
                <?php _e( 'Pacotes completos para todos os perfis. Grupos, famílias ou individuais.', 'temaaventuras' ); ?>
            </p>
        </div>

        <div class="grid grid--3">

        <?php if ( $pacotes->have_posts() ) :
            $delay = 1;
            while ( $pacotes->have_posts() ) : $pacotes->the_post();
                $preco    = get_post_meta( get_the_ID(), '_pacote_preco', true );
                $periodo  = get_post_meta( get_the_ID(), '_pacote_periodo', true );
                $destaque = get_post_meta( get_the_ID(), '_pacote_destaque', true );
                $inclui   = get_post_meta( get_the_ID(), '_pacote_inclui', true );
                $itens    = array_filter( array_map( 'trim', explode( "\n", $inclui ) ) );
        ?>

            <div class="card-pacote animar-entrada delay-<?php echo $delay++; ?> <?php echo $destaque ? 'card-pacote--destaque' : ''; ?>">
                <?php if ( $destaque ) : ?>
                <div class="badge badge--amarelo" style="margin-bottom:var(--espaco-md);">
                    ⭐ <?php _e( 'Mais Popular', 'temaaventuras' ); ?>
                </div>
                <?php endif; ?>

                <h3 class="card-pacote__nome"><?php the_title(); ?></h3>
                <div class="card-pacote__preco"><?php echo ta_preco( $preco, 'R$' ); ?></div>
                <div class="card-pacote__preco-label"><?php echo esc_html( $periodo ); ?></div>

                <?php if ( $itens ) : ?>
                <ul class="card-pacote__lista" role="list">
                    <?php foreach ( $itens as $item ) : ?>
                    <li class="card-pacote__item" role="listitem"><?php echo esc_html( $item ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <a href="<?php echo esc_url( $wa_link ); ?>"
                   class="btn <?php echo $destaque ? 'btn--primario' : 'btn--secundario'; ?>"
                   style="width:100%"
                   target="_blank"
                   rel="noopener noreferrer"
                   id="pacote-btn-<?php the_ID(); ?>">
                    📲 <?php _e( 'Quero Este Pacote', 'temaaventuras' ); ?>
                </a>
            </div>

        <?php endwhile; wp_reset_postdata();

        else : // Fallback
            foreach ( $fallback_pacotes as $i => $p ) : ?>

            <div class="card-pacote animar-entrada delay-<?php echo $i + 1; ?> <?php echo $p['destaque'] ? 'card-pacote--destaque' : ''; ?>">
                <?php if ( $p['destaque'] ) : ?>
                <div class="badge badge--amarelo" style="margin-bottom:var(--espaco-md);">
                    ⭐ <?php _e( 'Mais Popular', 'temaaventuras' ); ?>
                </div>
                <?php endif; ?>

                <h3 class="card-pacote__nome"><?php echo esc_html( $p['nome'] ); ?></h3>
                <div class="card-pacote__preco">R$<?php echo esc_html( $p['preco'] ); ?></div>
                <div class="card-pacote__preco-label"><?php echo esc_html( $p['periodo'] ); ?></div>

                <ul class="card-pacote__lista" role="list">
                    <?php foreach ( $p['inclui'] as $item ) : ?>
                    <li class="card-pacote__item" role="listitem"><?php echo esc_html( $item ); ?></li>
                    <?php endforeach; ?>
                </ul>

                <a href="<?php echo esc_url( $wa_link ); ?>"
                   class="btn <?php echo $p['destaque'] ? 'btn--primario' : 'btn--secundario'; ?>"
                   style="width:100%"
                   target="_blank"
                   rel="noopener noreferrer"
                   id="pacote-fallback-<?php echo $i; ?>">
                    📲 <?php _e( 'Quero Este Pacote', 'temaaventuras' ); ?>
                </a>
            </div>

        <?php endforeach; endif; ?>

        </div><!-- /.grid -->

        <p class="texto-centro" style="margin-top:var(--espaco-xl); color:var(--texto-muted); font-size:var(--tamanho-pequeno);">
            <?php _e( '✅ Grupos a partir de 6 pessoas ganham desconto especial. Entre em contato!', 'temaaventuras' ); ?>
        </p>

    </div>
</section>
