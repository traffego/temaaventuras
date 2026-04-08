<?php
/**
 * single-pacote.php – Página individual de Pacote (CPT)
 *
 * @package TemaAventuras
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) : the_post();

$preco       = get_post_meta( get_the_ID(), '_pacote_preco', true );
$periodo     = get_post_meta( get_the_ID(), '_pacote_periodo', true );
$destaque    = get_post_meta( get_the_ID(), '_pacote_destaque', true );
$inclui_raw  = get_post_meta( get_the_ID(), '_pacote_inclui', true );
$inclui      = $inclui_raw ? array_filter( array_map( 'trim', explode( "\n", $inclui_raw ) ) ) : [];
$wa_link     = ta_whatsapp_link( 'Olá! Tenho interesse no pacote: ' . get_the_title() );
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner" style="min-height:60vh;">
        <div class="page-banner__overlay" aria-hidden="true"
             style="background:linear-gradient(135deg,rgba(0,39,118,.8),rgba(0,156,59,.6));"></div>
        <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'aventura-banner', [ 'class' => 'page-banner__img', 'loading' => 'eager', 'alt' => '' ] ); ?>
        <div class="container page-banner__conteudo">
            <nav class="breadcrumb" aria-label="Navegação estrutural" style="margin-bottom:var(--espaco-md);">
                <a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Início', 'temaaventuras' ); ?></a>
                <span aria-hidden="true"> / </span>
                <a href="<?php echo home_url( '/pacotes' ); ?>"><?php _e( 'Pacotes', 'temaaventuras' ); ?></a>
                <span aria-hidden="true"> / </span>
                <span aria-current="page"><?php the_title(); ?></span>
            </nav>
            <?php if ( $destaque ) : ?>
            <span class="badge badge--amarelo" style="margin-bottom:var(--espaco-md);">⭐ Mais Popular</span>
            <?php endif; ?>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php if ( $preco ) : ?>
            <div style="margin-top:var(--espaco-lg);display:flex;align-items:baseline;gap:var(--espaco-sm);">
                <span style="font-family:var(--fonte-titulo);font-size:3.5rem;color:var(--cor-secundaria);line-height:1;">
                    <?php echo ta_preco( $preco ); ?>
                </span>
                <?php if ( $periodo ) : ?>
                <span style="color:rgba(255,255,255,.7);">/<?php echo esc_html( $periodo ); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Conteúdo + Sidebar -->
    <section class="section">
        <div class="container">
            <div style="display:grid;grid-template-columns:1fr 350px;gap:var(--espaco-3xl);align-items:start;" class="pacote-layout">

                <!-- Conteúdo principal -->
                <div>
                    <?php if ( get_the_content() ) : ?>
                    <div class="wp-content">
                        <?php the_content(); ?>
                    </div>
                    <?php endif; ?>

                    <!-- O que está incluído -->
                    <?php if ( ! empty( $inclui ) ) : ?>
                    <div style="margin-top:var(--espaco-3xl);">
                        <h2 style="font-size:1.6rem;margin-bottom:var(--espaco-xl);">✅ O que está incluído</h2>
                        <ul style="list-style:none;display:flex;flex-direction:column;gap:var(--espaco-md);">
                            <?php foreach ( $inclui as $item ) : ?>
                            <li style="display:flex;align-items:flex-start;gap:var(--espaco-md);padding:var(--espaco-md) var(--espaco-lg);
                                       background:var(--fundo-glass);border:1px solid var(--borda-glass);border-radius:var(--raio-lg);">
                                <span style="color:var(--cor-primaria);font-size:1.2rem;flex-shrink:0;">✓</span>
                                <span style="color:var(--texto-secundario);"><?php echo esc_html( $item ); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Atividades incluídas -->
                    <?php
                    $atividades_pacote = get_posts( [
                        'post_type'   => 'atividade',
                        'numberposts' => 6,
                        'post_status' => 'publish',
                        'orderby'     => 'menu_order',
                    ] );
                    if ( $atividades_pacote ) :
                    ?>
                    <div style="margin-top:var(--espaco-3xl);">
                        <h2 style="font-size:1.6rem;margin-bottom:var(--espaco-xl);">🌊 Atividades do Pacote</h2>
                        <div class="grid grid--3">
                            <?php foreach ( $atividades_pacote as $at ) :
                                $nivel = get_post_meta( $at->ID, '_atividade_nivel', true ) ?: 'facil';
                            ?>
                            <div class="card animar-entrada" style="padding:var(--espaco-lg);text-align:center;">
                                <?php if ( has_post_thumbnail( $at->ID ) ) : ?>
                                <div style="border-radius:var(--raio-lg);overflow:hidden;margin-bottom:var(--espaco-md);aspect-ratio:4/3;">
                                    <?php echo get_the_post_thumbnail( $at->ID, 'aventura-thumb', [ 'loading' => 'lazy' ] ); ?>
                                </div>
                                <?php else : ?>
                                <div style="font-size:2.5rem;margin-bottom:var(--espaco-md);">🌊</div>
                                <?php endif; ?>
                                <h3 style="font-size:1rem;margin-bottom:var(--espaco-sm);">
                                    <a href="<?php echo get_permalink( $at->ID ); ?>"><?php echo esc_html( $at->post_title ); ?></a>
                                </h3>
                                <?php echo ta_nivel_badge( $nivel ); ?>
                            </div>
                            <?php endforeach; wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar: CTA de contratação -->
                <aside>
                    <div style="background:var(--fundo-card);border:<?php echo $destaque ? '2px solid var(--cor-secundaria)' : '1px solid var(--borda-glass)'; ?>;
                                border-radius:var(--raio-xl);padding:var(--espaco-2xl);position:sticky;top:calc(var(--altura-nav) + var(--espaco-lg));">

                        <?php if ( $destaque ) : ?>
                        <div style="background:var(--cor-secundaria);color:#000;text-align:center;padding:var(--espaco-sm);
                                    border-radius:var(--raio-md);margin-bottom:var(--espaco-xl);font-weight:var(--peso-negrito);font-size:var(--tamanho-pequeno);">
                            ⭐ PACOTE MAIS POPULAR
                        </div>
                        <?php endif; ?>

                        <?php if ( $preco ) : ?>
                        <div style="text-align:center;padding-bottom:var(--espaco-xl);border-bottom:1px solid var(--borda-glass);margin-bottom:var(--espaco-xl);">
                            <span style="font-family:var(--fonte-titulo);font-size:4rem;color:var(--cor-secundaria);line-height:1;display:block;">
                                <?php echo ta_preco( $preco ); ?>
                            </span>
                            <?php if ( $periodo ) : ?>
                            <span style="font-size:var(--tamanho-pequeno);color:var(--texto-muted);">
                                por <?php echo esc_html( $periodo ); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <a href="<?php echo esc_url( $wa_link ); ?>"
                           id="pacote-contratar-btn"
                           class="btn btn--primario btn--grande pulsar"
                           style="width:100%;justify-content:center;margin-bottom:var(--espaco-md);"
                           target="_blank" rel="noopener noreferrer">
                            📲 Contratar pelo WhatsApp
                        </a>
                        <a href="<?php echo esc_url( home_url( '/contato' ) ); ?>"
                           class="btn btn--ghost"
                           style="width:100%;justify-content:center;">
                            ✉️ Outras formas de contato
                        </a>

                        <div style="margin-top:var(--espaco-xl);padding-top:var(--espaco-xl);border-top:1px solid var(--borda-glass);
                                    display:flex;flex-direction:column;gap:var(--espaco-sm);">
                            <div style="font-size:0.8rem;color:var(--texto-muted);">✅ Guias especializados</div>
                            <div style="font-size:0.8rem;color:var(--texto-muted);">🛡️ Equipamentos homologados</div>
                            <div style="font-size:0.8rem;color:var(--texto-muted);">💳 Parcelamento disponível</div>
                            <div style="font-size:0.8rem;color:var(--texto-muted);">🌿 Turismo responsável</div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

</main>

<?php endwhile;
get_footer(); ?>

<style>
@media (max-width:1024px) {
    .pacote-layout { grid-template-columns: 1fr !important; }
}
</style>
