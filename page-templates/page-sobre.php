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
        <div class="container">
            <div class="sobre-layout">

                <!-- Col principal -->
                <div class="sobre-conteudo">
                    <?php while (have_posts()) : the_post(); ?>
                    <div class="wp-content">
                        <?php the_content(); ?>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Col lateral: diferenciais -->
                <aside>
                    <div style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-2xl);">
                        <h2 style="font-size:1.6rem;margin-bottom:var(--espaco-xl);"><?php _e('Por que nos escolher?','temaaventuras'); ?></h2>
                        <?php
                        $diferenciais = [
                            ['🏅', __('Equipe Certificada','temaaventuras'),       __('Guias com certificação internacional e anos de experiência em campo.','temaaventuras')],
                            ['🛡️', __('Segurança em Primeiro Lugar','temaaventuras'), __('Todos os equipamentos homologados e constantemente revisados.','temaaventuras')],
                            ['🌿', __('Compromisso Ambiental','temaaventuras'),     __('Praticamos turismo responsável e apoiamos a preservação da natureza.','temaaventuras')],
                            ['⭐', __('Atendimento 5 Estrelas','temaaventuras'),    __('Do planejamento ao pós-aventura, suporte completo para você.','temaaventuras')],
                            ['📸', __('Memórias Garantidas','temaaventuras'),       __('Registro fotográfico em todas as atividades para eternizar momentos.','temaaventuras')],
                        ];
                        foreach ($diferenciais as $d) :
                        ?>
                        <div style="display:flex;gap:var(--espaco-md);align-items:flex-start;padding:var(--espaco-md) 0;border-bottom:1px solid var(--borda-glass);">
                            <span style="font-size:1.8rem;flex-shrink:0;"><?php echo $d[0]; ?></span>
                            <div>
                                <strong style="color:var(--texto-primario);display:block;margin-bottom:4px;"><?php echo esc_html($d[1]); ?></strong>
                                <span style="font-size:var(--tamanho-pequeno);color:var(--texto-muted);"><?php echo esc_html($d[2]); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <a href="<?php echo esc_url($wa_link); ?>"
                           class="btn btn--primario btn--grande"
                           style="width:100%;justify-content:center;margin-top:var(--espaco-xl);"
                           target="_blank" rel="noopener noreferrer"
                           id="sobre-whatsapp-btn">
                            📲 <?php _e('Falar Conosco','temaaventuras'); ?>
                        </a>
                    </div>
                </aside>

            </div>
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

    <!-- Equipe (fallback visual) -->
    <section class="section section--escura" aria-labelledby="equipe-titulo">
        <div class="container">
            <div class="section-header animar-entrada">
                <span class="section-header__eyebrow">👥 <?php _e('Nossa Equipe','temaaventuras'); ?></span>
                <h2 id="equipe-titulo" class="section-header__titulo"><?php _e('Quem Faz a Aventura Acontecer','temaaventuras'); ?></h2>
                <p class="section-header__subtitulo"><?php _e('Profissionais apaixonados por natureza e adrenalina, prontos para guiar cada aventura com segurança.','temaaventuras'); ?></p>
            </div>

            <?php
            // Se existir CPT de equipe, exibe aqui
            // Caso contrário exibe cards placeholder
            $membros = [
                ['nome' => 'Guide 1', 'cargo' => __('Guia Sênior – Rafting','temaaventuras'),  'emoji' => '🏄', 'cor' => 'linear-gradient(135deg,#002776,#0066cc)'],
                ['nome' => 'Guide 2', 'cargo' => __('Guia – Trilha & Rapel','temaaventuras'), 'emoji' => '🧗', 'cor' => 'linear-gradient(135deg,#009C3B,#00cc55)'],
                ['nome' => 'Guide 3', 'cargo' => __('Guia – Canionismo','temaaventuras'),      'emoji' => '⛰️', 'cor' => 'linear-gradient(135deg,#4a2a00,#cc6600)'],
                ['nome' => 'Guide 4', 'cargo' => __('Instrutora – Tirolesa','temaaventuras'),  'emoji' => '🪂', 'cor' => 'linear-gradient(135deg,#3a003a,#990099)'],
            ];
            ?>
            <div class="grid grid--4">
                <?php foreach ($membros as $i => $m): ?>
                <div class="card animar-entrada delay-<?php echo $i+1; ?>" style="text-align:center;padding:var(--espaco-xl);">
                    <div style="width:100px;height:100px;border-radius:50%;background:<?php echo $m['cor']; ?>;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto var(--espaco-md);border:3px solid var(--cor-primaria);">
                        <?php echo $m['emoji']; ?>
                    </div>
                    <strong style="color:var(--texto-primario);display:block;"><?php echo esc_html($m['nome']); ?></strong>
                    <span style="font-size:var(--tamanho-pequeno);color:var(--texto-muted);"><?php echo esc_html($m['cargo']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Depoimentos rápidos -->
    <?php get_template_part('template-parts/testimonials'); ?>

</main>

<?php get_footer(); ?>

<style>
.sobre-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: var(--espaco-3xl);
    align-items: start;
}
@media (max-width: 1024px) {
    .sobre-layout { grid-template-columns: 1fr; }
}
</style>
