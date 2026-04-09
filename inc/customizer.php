<?php
/**
 * Customizer – Painel de Administração de Cores e Configurações
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

function tema_aventuras_customizer( $wp_customize ) {

    // =========================================
    // PAINEL: TEMA AVENTURAS
    // =========================================
    $wp_customize->add_panel( 'tema_aventuras_painel', [
        'title'       => __( '🌿 Tema Aventuras', 'temaaventuras' ),
        'description' => __( 'Personalize as cores, tipografia e conteúdo do tema.', 'temaaventuras' ),
        'priority'    => 10,
    ] );

    // =========================================
    // SEÇÃO: PALETA DE CORES
    // =========================================
    $wp_customize->add_section( 'tema_aventuras_cores', [
        'title'    => __( '🎨 Paleta de Cores', 'temaaventuras' ),
        'panel'    => 'tema_aventuras_painel',
        'priority' => 10,
    ] );

    // --- Cor Primária ---
    $wp_customize->add_setting( 'cor_primaria', [
        'default'           => '#009C3B',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control(
        $wp_customize,
        'cor_primaria',
        [
            'label'       => __( 'Cor Primária (Verde)', 'temaaventuras' ),
            'description' => __( 'Cor principal do tema. Padrão: Verde Brasil #009C3B', 'temaaventuras' ),
            'section'     => 'tema_aventuras_cores',
        ]
    ) );

    // --- Cor Secundária ---
    $wp_customize->add_setting( 'cor_secundaria', [
        'default'           => '#FFDF00',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control(
        $wp_customize,
        'cor_secundaria',
        [
            'label'       => __( 'Cor Secundária (Amarelo)', 'temaaventuras' ),
            'description' => __( 'Cor de destaque. Padrão: Amarelo Brasil #FFDF00', 'temaaventuras' ),
            'section'     => 'tema_aventuras_cores',
        ]
    ) );

    // --- Cor Terciária ---
    $wp_customize->add_setting( 'cor_terciaria', [
        'default'           => '#002776',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control(
        $wp_customize,
        'cor_terciaria',
        [
            'label'       => __( 'Cor Terciária (Azul)', 'temaaventuras' ),
            'description' => __( 'Color de acento. Padrão: Azul Brasil #002776', 'temaaventuras' ),
            'section'     => 'tema_aventuras_cores',
        ]
    ) );

    // --- Cor de Fundo ---
    $wp_customize->add_setting( 'cor_fundo', [
        'default'           => '#0A110D',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control(
        $wp_customize,
        'cor_fundo',
        [
            'label'       => __( 'Cor de Fundo Base', 'temaaventuras' ),
            'description' => __( 'Fundo escuro da página. Padrão: #0A110D', 'temaaventuras' ),
            'section'     => 'tema_aventuras_cores',
        ]
    ) );

    // --- Cor do Texto ---
    $wp_customize->add_setting( 'cor_texto', [
        'default'           => '#F4F9F5',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control(
        $wp_customize,
        'cor_texto',
        [
            'label'       => __( 'Cor do Texto Principal', 'temaaventuras' ),
            'description' => __( 'Texto principal sobre fundo escuro. Padrão: #F4F9F5', 'temaaventuras' ),
            'section'     => 'tema_aventuras_cores',
        ]
    ) );

    // =========================================
    // SEÇÃO: INFORMAÇÕES DA EMPRESA
    // =========================================
    $wp_customize->add_section( 'tema_aventuras_empresa', [
        'title'    => __( '🏢 Informações da Empresa', 'temaaventuras' ),
        'panel'    => 'tema_aventuras_painel',
        'priority' => 20,
    ] );

    $campos_empresa = [
        'empresa_nome'      => [ 'Aventura Extrema', 'Nome da Empresa', '', 'text' ],
        'empresa_slogan'    => [ 'Viva a Aventura, Sinta a Natureza!', 'Slogan / Tagline', '', 'text' ],
        'empresa_telefone'  => [ '(11) 99999-9999', 'Telefone / WhatsApp', '', 'text' ],
        'empresa_email'     => [ 'contato@aventuraextrema.com.br', 'E-mail de Contato', '', 'text' ],
        'empresa_endereco'  => [ 'Estrada das Cachoeiras, 1234 – Brotas, SP', 'Endereço', '', 'text' ],
        'empresa_instagram' => [ 'https://instagram.com/aventuraextrema', 'Instagram URL', '', 'url' ],
        'empresa_facebook'  => [ 'https://facebook.com/aventuraextrema', 'Facebook URL', '', 'url' ],
        'empresa_youtube'   => [ 'https://youtube.com/@aventuraextrema', 'YouTube URL', '', 'url' ],
        'empresa_whatsapp'  => [ '5511999999999', 'WhatsApp (apenas números com DDD e DDI)', '', 'text' ],
    ];

    foreach ( $campos_empresa as $id => $dados ) {
        $wp_customize->add_setting( $id, [
            'default'           => $dados[0],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => __( $dados[1], 'temaaventuras' ),
            'description' => $dados[2],
            'section'     => 'tema_aventuras_empresa',
            'type'        => $dados[3],
        ] );
    }

    // Checkboxes WhatsApp
    $wp_customize->add_setting( 'mostrar_wa_header', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ] );
    $wp_customize->add_control( 'mostrar_wa_header', [
        'label'   => __( 'Menu: Mostrar botão Falar no WhatsApp no topo', 'temaaventuras' ),
        'section' => 'tema_aventuras_empresa',
        'type'    => 'checkbox',
    ] );

    $wp_customize->add_setting( 'mostrar_wa_flutuante', [
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ] );
    $wp_customize->add_control( 'mostrar_wa_flutuante', [
        'label'   => __( 'Global: Mostrar ícone flutuante do WhatsApp no canto da tela', 'temaaventuras' ),
        'section' => 'tema_aventuras_empresa',
        'type'    => 'checkbox',
    ] );

    // =========================================
    // SEÇÃO: MÓDULOS & FUNCIONALIDADES
    // =========================================
    $wp_customize->add_section( 'tema_aventuras_modulos', [
        'title'    => __( '⚙️ Módulos & Funcionalidades', 'temaaventuras' ),
        'panel'    => 'tema_aventuras_painel',
        'priority' => 25,
    ] );

    $wp_customize->add_setting( 'mostrar_pacotes', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ] );
    $wp_customize->add_control( 'mostrar_pacotes', [
        'label'   => __( 'Ativar seção e sistema de Pacotes', 'temaaventuras' ),
        'section' => 'tema_aventuras_modulos',
        'type'    => 'checkbox',
    ] );

    $wp_customize->add_setting( 'mostrar_depoimentos', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ] );
    $wp_customize->add_control( 'mostrar_depoimentos', [
        'label'   => __( 'Ativar seção de Depoimentos', 'temaaventuras' ),
        'section' => 'tema_aventuras_modulos',
        'type'    => 'checkbox',
    ] );
    // SEÇÃO: HERO (PÁGINA INICIAL)
    // =========================================
    $wp_customize->add_section( 'tema_aventuras_hero', [
        'title'    => __( '🦅 Seção Hero', 'temaaventuras' ),
        'panel'    => 'tema_aventuras_painel',
        'priority' => 30,
    ] );

    // Imagem de fundo do hero
    $wp_customize->add_setting( 'hero_imagem', [
        'default'           => TEMA_AVENTURAS_URI . '/assets/images/hero-bg.jpg',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control(
        $wp_customize,
        'hero_imagem',
        [
            'label'   => __( 'Imagem de Fundo do Hero', 'temaaventuras' ),
            'section' => 'tema_aventuras_hero',
        ]
    ) );

    // Vídeo de fundo do hero (URL YouTube/Vimeo ou mp4)
    $wp_customize->add_setting( 'hero_video_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'hero_video_url', [
        'label'       => __( 'URL do Vídeo de Fundo (opcional, substitui imagem)', 'temaaventuras' ),
        'description' => __( 'Cole a URL de um arquivo .mp4 ou YouTube embed.', 'temaaventuras' ),
        'type'        => 'url',
        'section'     => 'tema_aventuras_hero',
    ] );

    $wp_customize->add_setting( 'hero_titulo', [
        'default'           => 'Sua Maior Aventura Começa Aqui',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'hero_titulo', [
        'label'   => __( 'Título Principal do Hero', 'temaaventuras' ),
        'type'    => 'text',
        'section' => 'tema_aventuras_hero',
    ] );

    $wp_customize->add_setting( 'hero_subtitulo', [
        'default'           => 'Rafting, trilhas, tirolesa e muito mais. Experiências radicais com segurança e profissionalismo.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'hero_subtitulo', [
        'label'   => __( 'Subtítulo do Hero', 'temaaventuras' ),
        'type'    => 'textarea',
        'section' => 'tema_aventuras_hero',
    ] );

    $wp_customize->add_setting( 'hero_cta_texto', [
        'default'           => 'Reserve Sua Aventura',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'hero_cta_texto', [
        'label'   => __( 'Texto do Botão CTA', 'temaaventuras' ),
        'type'    => 'text',
        'section' => 'tema_aventuras_hero',
    ] );

    $wp_customize->add_setting( 'hero_cta_url', [
        'default'           => '#atividades',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'hero_cta_url', [
        'label'   => __( 'URL do Botão CTA', 'temaaventuras' ),
        'type'    => 'url',
        'section' => 'tema_aventuras_hero',
    ] );

    // =========================================
    // SEÇÃO: CONTADORES (STATS)
    // =========================================
    $wp_customize->add_section( 'tema_aventuras_stats', [
        'title'    => __( '📊 Contadores / Stats', 'temaaventuras' ),
        'panel'    => 'tema_aventuras_painel',
        'priority' => 40,
    ] );

    for ( $i = 1; $i <= 4; $i++ ) {
        $defaults_num    = [ 1, '1200+', '8', '15' ];
        $defaults_label  = [ 'Anos de Experiência', 'Aventureiros Atendidos', 'Anos de Experiência', 'Destinos' ];

        $wp_customize->add_setting( "stat_{$i}_numero", [
            'default'           => $defaults_num[ $i - 1 ] ?? "{$i}00+",
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( "stat_{$i}_numero", [
            'label'   => __( "Stat #{$i} – Número", 'temaaventuras' ),
            'type'    => 'text',
            'section' => 'tema_aventuras_stats',
        ] );

        $wp_customize->add_setting( "stat_{$i}_label", [
            'default'           => $defaults_label[ $i - 1 ] ?? "Conquista #{$i}",
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( "stat_{$i}_label", [
            'label'   => __( "Stat #{$i} – Rótulo", 'temaaventuras' ),
            'type'    => 'text',
            'section' => 'tema_aventuras_stats',
        ] );
    }

    // =========================================
    // SEÇÃO: RODAPÉ
    // =========================================
    $wp_customize->add_section( 'tema_aventuras_footer', [
        'title'    => __( '🦶 Rodapé', 'temaaventuras' ),
        'panel'    => 'tema_aventuras_painel',
        'priority' => 80,
    ] );

    $wp_customize->add_setting( 'footer_texto_copy', [
        'default'           => '© ' . date( 'Y' ) . ' Aventura Extrema. Todos os direitos reservados.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'footer_texto_copy', [
        'label'   => __( 'Texto de Copyright', 'temaaventuras' ),
        'type'    => 'text',
        'section' => 'tema_aventuras_footer',
    ] );

    $wp_customize->add_setting( 'footer_descricao', [
        'default'           => 'Especialistas em aventura desde 2016. Segurança, adrenalina e natureza em cada experiência.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'footer_descricao', [
        'label'   => __( 'Descrição no Rodapé', 'temaaventuras' ),
        'type'    => 'textarea',
        'section' => 'tema_aventuras_footer',
    ] );

    // =========================================
    // POSTMESSAGE JS – Atualização em tempo real
    // =========================================
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
}
add_action( 'customize_register', 'tema_aventuras_customizer' );

// =========================================
// JS SELECTIVE REFRESH – Preview ao Vivo
// =========================================
function tema_aventuras_customizer_preview_js() {
    wp_enqueue_script(
        'tema-aventuras-customizer-preview',
        TEMA_AVENTURAS_URI . '/assets/js/customizer-preview.js',
        [ 'customize-preview' ],
        TEMA_AVENTURAS_VERSION,
        true
    );
}
add_action( 'customize_preview_init', 'tema_aventuras_customizer_preview_js' );
