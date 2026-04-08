<?php
/**
 * Template Part: Stats Counter – Contadores animados
 *
 * @package TemaAventuras
 */

$stats = [];
for ( $i = 1; $i <= 4; $i++ ) {
    $stats[] = [
        'numero' => ta_get( "stat_{$i}_numero", [ '8+', '1200+', '15', '100%' ][ $i - 1 ] ),
        'label'  => ta_get( "stat_{$i}_label",  [ 'Anos de Experiência', 'Aventureiros Atendidos', 'Destinos', 'Satisfação' ][ $i - 1 ] ),
    ];
}
?>

<!-- =========================================
     STATS COUNTER
     ========================================= -->
<section class="section section--pequena stats-section" aria-label="Números que nos orgulham">

    <div class="container">
        <div class="stats-grid" role="list">
            <?php foreach ( $stats as $i => $stat ) : ?>
            <div class="stat-item animar-entrada delay-<?php echo $i + 1; ?>" role="listitem">
                <span class="stat-item__numero" data-contador="<?php echo esc_attr( $stat['numero'] ); ?>">
                    <?php echo esc_html( $stat['numero'] ); ?>
                </span>
                <span class="stat-item__label"><?php echo esc_html( $stat['label'] ); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Linha decorativa -->
    <div style="position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--borda-glass),transparent)" aria-hidden="true"></div>

</section>

<style>
.stats-section {
    background: var(--fundo-card);
    padding-block: var(--espaco-3xl);
    position: relative;
    overflow: hidden;
}

.stats-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(0,156,59,0.05) 0%, transparent 70%);
    pointer-events: none;
}

.stat-item {
    position: relative;
    padding: var(--espaco-xl);
    text-align: center;
}

.stat-item::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 1px;
    height: 60%;
    background: var(--borda-glass);
}

.stat-item:last-child::after { display: none; }

@media (max-width: 768px) {
    .stat-item::after { display: none; }
    .stats-grid { gap: var(--espaco-xl); }
}
</style>
