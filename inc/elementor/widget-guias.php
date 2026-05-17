<?php
/**
 * Widget Elementor: Grid de Guias
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

class TA_Widget_Guias extends \Elementor\Widget_Base {

    public function get_name()  { return 'ta_guias'; }
    public function get_title() { return __( 'Guias de Aventura', 'temaaventuras' ); }
    public function get_icon()  { return 'eicon-person'; }
    public function get_categories() { return [ 'tema-aventuras' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'secao_config', [
            'label' => __( 'Configurações', 'temaaventuras' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'titulo', [
            'label'   => __( 'Título da Seção', 'temaaventuras' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => __( 'Nossos Guias', 'temaaventuras' ),
        ] );

        $this->add_control( 'subtitulo', [
            'label'   => __( 'Subtítulo', 'temaaventuras' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => __( 'Profissionais apaixonados por natureza e aventura', 'temaaventuras' ),
        ] );

        $this->add_control( 'quantidade', [
            'label'   => __( 'Quantidade de guias', 'temaaventuras' ),
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => 4,
            'min'     => 1,
            'max'     => 20,
        ] );

        $this->add_control( 'colunas', [
            'label'   => __( 'Colunas', 'temaaventuras' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '4',
            'options' => [
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
        ] );

        $this->add_control( 'mostrar_descricao', [
            'label'   => __( 'Mostrar Descrição?', 'temaaventuras' ),
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $guias = new WP_Query( [
            'post_type'      => 'guia',
            'posts_per_page' => (int) $s['quantidade'],
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        if ( ! $guias->have_posts() ) {
            echo '<p>' . __( 'Nenhum guia encontrado.', 'temaaventuras' ) . '</p>';
            return;
        }
        ?>
        <div class="ta-guias-wrapper">
            <?php if ( $s['titulo'] ) : ?>
            <div class="section-header animar-entrada">
                <span class="section-header__eyebrow">🧭 <?php _e( 'Nossa Equipe', 'temaaventuras' ); ?></span>
                <h2 class="section-header__titulo"><?php echo esc_html( $s['titulo'] ); ?></h2>
                <?php if ( $s['subtitulo'] ) : ?>
                <p class="section-header__subtitulo"><?php echo esc_html( $s['subtitulo'] ); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="ta-guias-grid ta-guias-grid--<?php echo esc_attr( $s['colunas'] ); ?>">
                <?php $i = 1; while ( $guias->have_posts() ) : $guias->the_post();
                    $foto_id   = (int) get_post_meta( get_the_ID(), '_guia_foto', true );
                    $subtitulo = get_post_meta( get_the_ID(), '_guia_subtitulo', true );
                    $descricao = get_post_meta( get_the_ID(), '_guia_descricao', true );
                    $foto_url  = $foto_id
                        ? wp_get_attachment_image_url( $foto_id, 'medium' )
                        : '';
                ?>
                <div class="ta-guia-card animar-entrada delay-<?php echo $i++; ?>">
                    <div class="ta-guia-card__foto-wrap">
                        <?php if ( $foto_url ) : ?>
                            <img src="<?php echo esc_url( $foto_url ); ?>"
                                 alt="<?php the_title_attribute(); ?>"
                                 class="ta-guia-card__foto"
                                 loading="lazy" />
                        <?php else : ?>
                            <div class="ta-guia-card__foto-placeholder">🧗</div>
                        <?php endif; ?>
                    </div>
                    <div class="ta-guia-card__info">
                        <strong class="ta-guia-card__nome"><?php the_title(); ?></strong>
                        <?php if ( $subtitulo ) : ?>
                            <span class="ta-guia-card__cargo"><?php echo esc_html( $subtitulo ); ?></span>
                        <?php endif; ?>
                        <?php if ( $s['mostrar_descricao'] === 'yes' && $descricao ) : ?>
                            <p class="ta-guia-card__desc"><?php echo esc_html( $descricao ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>

        <style>
        .ta-guias-grid {
            display: grid;
            gap: var(--espaco-xl, 2rem);
        }
        .ta-guias-grid--2 { grid-template-columns: repeat(2, 1fr); }
        .ta-guias-grid--3 { grid-template-columns: repeat(3, 1fr); }
        .ta-guias-grid--4 { grid-template-columns: repeat(4, 1fr); }

        .ta-guia-card {
            background: var(--fundo-card, #1a2420);
            border: 1px solid var(--borda-glass, rgba(255,255,255,0.08));
            border-radius: var(--raio-xl, 16px);
            padding: var(--espaco-xl, 2rem);
            text-align: center;
            transition: all 0.3s ease;
        }
        .ta-guia-card:hover {
            transform: translateY(-6px);
            border-color: var(--cor-primaria, #009C3B);
            box-shadow: 0 12px 40px rgba(0,156,59,0.2);
        }
        .ta-guia-card__foto-wrap {
            width: 110px;
            height: 110px;
            margin: 0 auto var(--espaco-lg, 1.5rem);
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--cor-primaria, #009C3B);
        }
        .ta-guia-card__foto {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .ta-guia-card__foto-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            background: var(--gradiente-hero, linear-gradient(135deg,#002776,#009C3B));
        }
        .ta-guia-card__nome {
            display: block;
            font-family: var(--fonte-titulo, inherit);
            font-size: 1.15rem;
            color: var(--texto-primario, #fff);
            margin-bottom: 4px;
        }
        .ta-guia-card__cargo {
            display: block;
            font-size: 0.8rem;
            color: var(--cor-secundaria, #FFDF00);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: var(--espaco-sm, 0.5rem);
        }
        .ta-guia-card__desc {
            font-size: 0.85rem;
            color: var(--texto-muted, rgba(255,255,255,0.5));
            line-height: 1.6;
            margin: 0;
        }

        @media (max-width: 900px) {
            .ta-guias-grid--4 { grid-template-columns: repeat(2, 1fr); }
            .ta-guias-grid--3 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .ta-guias-grid--4,
            .ta-guias-grid--3,
            .ta-guias-grid--2 { grid-template-columns: 1fr; }
        }
        </style>
        <?php
    }
}
