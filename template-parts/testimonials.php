<?php
/**
 * Template Part: Testimonials – Carrossel de Depoimentos
 *
 * @package TemaAventuras
 */

$depoimentos = ta_get_depoimentos( 6 );

$fallback_deps = [
    [ 'nome' => 'Carlos Mendes',   'cidade' => 'São Paulo, SP',      'nota' => 5, 'atividade' => 'Rafting Radical',    'texto' => 'Experiência incrível! Os guias foram super profissionais e a emoção do rafting foi algo que jamais vou esquecer. Recomendo demais!' ],
    [ 'nome' => 'Ana Paula',       'cidade' => 'Campinas, SP',       'nota' => 5, 'atividade' => 'Trilha + Tirolesa',  'texto' => 'A trilha foi linda e a tirolesa foi de tirar o fôlego. Minha família adorou cada momento. Com certeza voltaremos!' ],
    [ 'nome' => 'Roberto Silva',   'cidade' => 'Belo Horizonte, MG', 'nota' => 5, 'atividade' => 'Pacote Explorador',  'texto' => 'O pacote explorador vale cada centavo. Fotos, transporte, alimentação... tudo perfeito. Equipe nota 10!' ],
    [ 'nome' => 'Fernanda Costa',  'cidade' => 'Rio de Janeiro, RJ', 'nota' => 5, 'atividade' => 'Canionismo',         'texto' => 'Nunca pensei que conseguiria fazer canionismo, mas os instrutores me deram todo o apoio. Foi a melhor aventura da minha vida!' ],
    [ 'nome' => 'Marcos Oliveira', 'cidade' => 'Brasília, DF',       'nota' => 5, 'atividade' => 'Rapel',              'texto' => 'Fiz o rapel em cachoeira e foi simplesmente épico. A paisagem é deslumbrante e o atendimento é top.' ],
    [ 'nome' => 'Juliana Nunes',   'cidade' => 'Curitiba, PR',       'nota' => 5, 'atividade' => 'Boia Cross',         'texto' => 'Boia cross é pura diversão! Levei meu grupo de amigos e todo mundo amou. Já estamos planejando a próxima visita.' ],
];
?>

<!-- =========================================
     DEPOIMENTOS
     ========================================= -->
<section class="section section--elevada" id="depoimentos" aria-labelledby="depoimentos-titulo">
    <div class="container">

        <div class="section-header animar-entrada">
            <span class="section-header__eyebrow">⭐ <?php _e( 'O Que Dizem', 'temaaventuras' ); ?></span>
            <h2 id="depoimentos-titulo" class="section-header__titulo">
                <?php _e( 'Aventureiros Satisfeitos', 'temaaventuras' ); ?>
            </h2>
            <p class="section-header__subtitulo">
                <?php _e( 'Mais de 1.200 aventureiros já viveram experiências únicas conosco.', 'temaaventuras' ); ?>
            </p>
        </div>

        <!-- CARROSSEL -->
        <div class="tc-wrapper" role="region" aria-label="Depoimentos de clientes">
            <div class="tc-track" id="tc-track">

            <?php if ( $depoimentos->have_posts() ) :
                while ( $depoimentos->have_posts() ) : $depoimentos->the_post();
                    $nota      = get_post_meta( get_the_ID(), '_depoimento_nota', true ) ?: 5;
                    $atividade = get_post_meta( get_the_ID(), '_depoimento_atividade', true );
                    $cidade    = get_post_meta( get_the_ID(), '_depoimento_cidade', true );
            ?>
                <div class="card-depoimento tc-slide">
                    <?php echo ta_estrelas( $nota ); ?>
                    <p class="card-depoimento__texto"><?php echo wp_kses_post( get_the_content() ?: get_the_excerpt() ); ?></p>
                    <div class="card-depoimento__autor">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'thumbnail', [ 'class' => 'card-depoimento__foto', 'loading' => 'lazy' ] ); ?>
                        <?php else : ?>
                            <div class="card-depoimento__foto" style="background:var(--gradiente-cta);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                                <?php echo mb_substr( get_the_title(), 0, 1 ); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="card-depoimento__nome"><?php the_title(); ?></div>
                            <?php if ( $atividade ) : ?>
                            <div class="card-depoimento__atividade"><?php echo esc_html( $atividade ); ?></div>
                            <?php endif; ?>
                            <?php if ( $cidade ) : ?>
                            <div style="font-size:0.75rem;color:var(--texto-muted);">📍 <?php echo esc_html( $cidade ); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endwhile; wp_reset_postdata();
            else :
                foreach ( $fallback_deps as $dep ) : ?>

                <div class="card-depoimento tc-slide">
                    <?php echo ta_estrelas( $dep['nota'] ); ?>
                    <p class="card-depoimento__texto"><?php echo esc_html( $dep['texto'] ); ?></p>
                    <div class="card-depoimento__autor">
                        <div class="card-depoimento__foto" aria-hidden="true"
                             style="background:var(--gradiente-cta);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;font-weight:bold;">
                            <?php echo mb_substr( $dep['nome'], 0, 1 ); ?>
                        </div>
                        <div>
                            <div class="card-depoimento__nome"><?php echo esc_html( $dep['nome'] ); ?></div>
                            <div class="card-depoimento__atividade"><?php echo esc_html( $dep['atividade'] ); ?></div>
                            <div style="font-size:0.75rem;color:var(--texto-muted);">📍 <?php echo esc_html( $dep['cidade'] ); ?></div>
                        </div>
                    </div>
                </div>

            <?php endforeach; endif; ?>

            </div><!-- /.tc-track -->

            <!-- Navegação -->
            <div class="tc-nav">
                <button class="testimonials-btn" id="tc-prev" aria-label="Depoimento anterior">←</button>
                <div class="tc-dots" id="tc-dots" role="tablist"></div>
                <button class="testimonials-btn" id="tc-next" aria-label="Próximo depoimento">→</button>
            </div>
        </div>

    </div>
</section>

<style>
.tc-wrapper {
    position: relative;
    overflow: hidden;
}

.tc-track {
    display: flex;
    gap: var(--espaco-xl);
    transition: transform 0.5s ease;
    will-change: transform;
}

.tc-slide {
    flex: 0 0 calc((100% - var(--espaco-xl) * 2) / 3);
    min-width: 0;
}

@media (max-width: 640px) {
    .tc-slide { flex: 0 0 100%; }
}

.tc-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--espaco-lg);
    margin-top: var(--espaco-2xl);
}

.testimonials-btn {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: var(--fundo-elevado);
    border: 1px solid var(--borda-glass);
    color: var(--texto-primario);
    font-size: 1.2rem;
    cursor: pointer;
    transition: all var(--transicao-normal);
    display: flex; align-items: center; justify-content: center;
}
.testimonials-btn:hover {
    background: var(--cor-primaria);
    border-color: var(--cor-primaria);
    transform: scale(1.1);
}

.tc-dots { display: flex; gap: var(--espaco-sm); }

.dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--borda-glass);
    cursor: pointer;
    transition: all var(--transicao-normal);
    border: none;
}
.dot.ativo {
    background: var(--cor-secundaria);
    width: 24px;
    border-radius: var(--raio-full);
}
</style>

<script>
(function () {
    var track   = document.getElementById('tc-track');
    var dotsEl  = document.getElementById('tc-dots');
    var btnPrev = document.getElementById('tc-prev');
    var btnNext = document.getElementById('tc-next');
    if (!track) return;

    var slides  = track.querySelectorAll('.tc-slide');
    var total   = slides.length;
    var current = 0;

    function perPage() {
        return window.innerWidth <= 640 ? 1 : 3;
    }

    function pages() {
        return Math.ceil(total / perPage());
    }

    function buildDots() {
        dotsEl.innerHTML = '';
        var n = pages();
        for (var i = 0; i < n; i++) {
            var d = document.createElement('button');
            d.className = 'dot' + (i === current ? ' ativo' : '');
            d.setAttribute('aria-label', 'Página ' + (i + 1));
            d.dataset.i = i;
            d.addEventListener('click', function () { goTo(+this.dataset.i); });
            dotsEl.appendChild(d);
        }
    }

    function goTo(page) {
        var pp = perPage();
        current = Math.max(0, Math.min(page, pages() - 1));
        var gap = parseFloat(getComputedStyle(track).gap) || 0;
        var slideW = slides[0].offsetWidth + gap;
        track.style.transform = 'translateX(-' + (current * pp * slideW) + 'px)';
        dotsEl.querySelectorAll('.dot').forEach(function (d, i) {
            d.classList.toggle('ativo', i === current);
        });
    }

    btnPrev.addEventListener('click', function () { goTo(current - 1); });
    btnNext.addEventListener('click', function () { goTo(current + 1); });

    // Auto-play
    var timer = setInterval(function () {
        goTo(current + 1 < pages() ? current + 1 : 0);
    }, 5000);

    track.closest('.tc-wrapper').addEventListener('mouseenter', function () { clearInterval(timer); });

    // Rebuild on resize
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearInterval(resizeTimer);
        resizeTimer = setTimeout(function () {
            current = 0;
            buildDots();
            goTo(0);
        }, 200);
    });

    buildDots();
    goTo(0);
})();
</script>
