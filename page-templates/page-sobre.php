<?php
/**
 * Template Name: Página Sobre Nós
 * Template Post Type: page
 *
 * @package TemaAventuras
 */

get_header();

$empresa_nome   = ta_get( 'empresa_nome', get_bloginfo('name') );
$empresa_slogan = ta_get( 'empresa_slogan', '' );
$wa_link        = ta_whatsapp_link( 'Olá! Quero conhecer mais sobre a ' . $empresa_nome );

// Stats para a seção Sobre
$stats = [];
for ( $i = 1; $i <= 4; $i++ ) {
    $stats[] = [
        'numero' => ta_get( "stat_{$i}_numero", ['8+','1200+','15','100%'][$i-1] ),
        'label'  => ta_get( "stat_{$i}_label",  ['Anos de Experiência','Aventureiros','Destinos','Satisfação'][$i-1] ),
    ];
}
?>

<main id="conteudo-principal" role="main">

    <!-- Banner -->
    <div class="page-banner" style="min-height:380px;">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <?php if (has_post_thumbnail()) the_post_thumbnail('aventura-banner',['class'=>'page-banner__img','loading'=>'eager','alt'=>'']); ?>
        <div class="container page-banner__conteudo">
            <span class="section-header__eyebrow" style="margin-bottom:var(--espaco-md);">🌿 <?php _e('Nossa História','temaaventuras'); ?></span>
            <h1 class="page-banner__titulo"><?php the_title(); ?></h1>
            <?php if ($empresa_slogan): ?>
            <p style="color:rgba(255,255,255,0.8);font-size:1.2rem;margin-top:var(--espaco-md);font-style:italic;">
                "<?php echo esc_html($empresa_slogan); ?>"
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Conteúdo da página -->
    <section class="section">
        <div class="container container--estreito">
            <?php while (have_posts()) : the_post(); ?>
            <div class="wp-content">
                <?php the_content(); ?>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Números -->
    <section class="section section--pequena stats-section" aria-label="Nossos números">
        <div class="container">
            <div class="stats-grid" role="list">
                <?php foreach ($stats as $i => $stat): ?>
                <div class="stat-item animar-entrada delay-<?php echo $i+1; ?>" role="listitem">
                    <span class="stat-item__numero" data-contador="<?php echo esc_attr($stat['numero']); ?>">
                        <?php echo esc_html($stat['numero']); ?>
                    </span>
                    <span class="stat-item__label"><?php echo esc_html($stat['label']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>



    <!-- Depoimentos rápidos -->
    <?php get_template_part('template-parts/testimonials'); ?>

</main>

<?php get_footer(); ?>


