<?php
/**
 * Custom Post Types: Atividades, Pacotes, Depoimentos
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

// =========================================
// CPT: ATIVIDADES
// =========================================
function tema_aventuras_cpt_atividades() {
    $labels = [
        'name'               => __( 'Atividades', 'temaaventuras' ),
        'singular_name'      => __( 'Atividade', 'temaaventuras' ),
        'add_new'            => __( 'Adicionar Atividade', 'temaaventuras' ),
        'add_new_item'       => __( 'Nova Atividade', 'temaaventuras' ),
        'edit_item'          => __( 'Editar Atividade', 'temaaventuras' ),
        'all_items'          => __( 'Todas as Atividades', 'temaaventuras' ),
        'search_items'       => __( 'Buscar Atividades', 'temaaventuras' ),
        'not_found'          => __( 'Nenhuma atividade encontrada.', 'temaaventuras' ),
        'menu_name'          => __( 'Atividades', 'temaaventuras' ),
    ];

    register_post_type( 'atividade', [
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'show_in_rest'        => true, // Suporte ao Gutenberg/Elementor
        'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'rewrite'             => [ 'slug' => 'atividades' ],
        'menu_icon'           => 'dashicons-palmtree',
        'menu_position'       => 5,
        'show_in_nav_menus'   => true,
        'taxonomies'          => [ 'categoria_atividade', 'nivel_dificuldade' ],
    ] );
}
add_action( 'init', 'tema_aventuras_cpt_atividades' );

// =========================================
// CPT: PACOTES
// =========================================
function tema_aventuras_cpt_pacotes() {
    $labels = [
        'name'          => __( 'Pacotes', 'temaaventuras' ),
        'singular_name' => __( 'Pacote', 'temaaventuras' ),
        'add_new'       => __( 'Adicionar Pacote', 'temaaventuras' ),
        'add_new_item'  => __( 'Novo Pacote', 'temaaventuras' ),
        'edit_item'     => __( 'Editar Pacote', 'temaaventuras' ),
        'all_items'     => __( 'Todos os Pacotes', 'temaaventuras' ),
        'menu_name'     => __( 'Pacotes', 'temaaventuras' ),
    ];

    register_post_type( 'pacote', [
        'labels'            => $labels,
        'public'            => true,
        'has_archive'       => true,
        'show_in_rest'      => true,
        'supports'          => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ],
        'rewrite'           => [ 'slug' => 'pacotes' ],
        'menu_icon'         => 'dashicons-tickets-alt',
        'menu_position'     => 6,
        'show_in_nav_menus' => true,
    ] );
}
add_action( 'init', 'tema_aventuras_cpt_pacotes' );

// =========================================
// CPT: DEPOIMENTOS
// =========================================
function tema_aventuras_cpt_depoimentos() {
    $labels = [
        'name'          => __( 'Depoimentos', 'temaaventuras' ),
        'singular_name' => __( 'Depoimento', 'temaaventuras' ),
        'add_new'       => __( 'Adicionar Depoimento', 'temaaventuras' ),
        'add_new_item'  => __( 'Novo Depoimento', 'temaaventuras' ),
        'edit_item'     => __( 'Editar Depoimento', 'temaaventuras' ),
        'all_items'     => __( 'Todos os Depoimentos', 'temaaventuras' ),
        'menu_name'     => __( 'Depoimentos', 'temaaventuras' ),
    ];

    register_post_type( 'depoimento', [
        'labels'        => $labels,
        'public'        => false, // Não tem arquivo, apenas usado internamente
        'show_ui'       => true,
        'show_in_rest'  => true,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
        'menu_icon'     => 'dashicons-format-quote',
        'menu_position' => 7,
    ] );
}
add_action( 'init', 'tema_aventuras_cpt_depoimentos' );

// =========================================
// TAXONOMIAS
// =========================================

// Categoria das atividades
function tema_aventuras_taxonomias() {
    // Categoria de Atividade
    register_taxonomy( 'categoria_atividade', [ 'atividade' ], [
        'labels'            => [
            'name'          => __( 'Categorias', 'temaaventuras' ),
            'singular_name' => __( 'Categoria', 'temaaventuras' ),
            'add_new_item'  => __( 'Nova Categoria', 'temaaventuras' ),
        ],
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'categoria-atividade' ],
    ] );

    // Nível de Dificuldade
    register_taxonomy( 'nivel_dificuldade', [ 'atividade' ], [
        'labels'            => [
            'name'          => __( 'Dificuldade', 'temaaventuras' ),
            'singular_name' => __( 'Nível', 'temaaventuras' ),
            'add_new_item'  => __( 'Novo Nível', 'temaaventuras' ),
        ],
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'dificuldade' ],
    ] );
}
add_action( 'init', 'tema_aventuras_taxonomias' );

// =========================================
// META BOXES SIMPLES (fallback sem ACF)
// Para campos extras nos CPTs
// =========================================
function tema_aventuras_add_meta_boxes() {
    // Atividade – detalhes
    add_meta_box(
        'atividade_detalhes',
        __( '⚡ Detalhes da Atividade', 'temaaventuras' ),
        'tema_aventuras_atividade_meta_box',
        'atividade',
        'normal',
        'high'
    );

    // Pacote – preço e detalhes
    add_meta_box(
        'pacote_detalhes',
        __( '💰 Detalhes do Pacote', 'temaaventuras' ),
        'tema_aventuras_pacote_meta_box',
        'pacote',
        'normal',
        'high'
    );

    // Depoimento – nota e autor
    add_meta_box(
        'depoimento_detalhes',
        __( '⭐ Detalhes do Depoimento', 'temaaventuras' ),
        'tema_aventuras_depoimento_meta_box',
        'depoimento',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tema_aventuras_add_meta_boxes' );

// Meta Box: Atividade
function tema_aventuras_atividade_meta_box( $post ) {
    wp_nonce_field( 'salvar_atividade_meta', 'atividade_meta_nonce' );
    $duracao = get_post_meta( $post->ID, '_atividade_duracao', true );
    $nivel   = get_post_meta( $post->ID, '_atividade_nivel', true );
    $preco   = get_post_meta( $post->ID, '_atividade_preco', true );
    $pessoas = get_post_meta( $post->ID, '_atividade_pessoas', true );
    ?>
    <table class="form-table">
        <tr>
            <th><?php _e( 'Duração', 'temaaventuras' ); ?></th>
            <td><input type="text" name="atividade_duracao" value="<?php echo esc_attr( $duracao ); ?>" placeholder="Ex: 3 horas" /></td>
        </tr>
        <tr>
            <th><?php _e( 'Nível de Dificuldade', 'temaaventuras' ); ?></th>
            <td>
                <select name="atividade_nivel">
                    <option value="facil"   <?php selected( $nivel, 'facil' ); ?>><?php _e( 'Fácil', 'temaaventuras' ); ?></option>
                    <option value="medio"   <?php selected( $nivel, 'medio' ); ?>><?php _e( 'Médio', 'temaaventuras' ); ?></option>
                    <option value="dificil" <?php selected( $nivel, 'dificil' ); ?>><?php _e( 'Difícil', 'temaaventuras' ); ?></option>
                    <option value="extremo" <?php selected( $nivel, 'extremo' ); ?>><?php _e( 'Extremo', 'temaaventuras' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Preço por Pessoa (R$)', 'temaaventuras' ); ?></th>
            <td><input type="number" name="atividade_preco" value="<?php echo esc_attr( $preco ); ?>" step="0.01" /></td>
        </tr>
        <tr>
            <th><?php _e( 'Mínimo de Pessoas', 'temaaventuras' ); ?></th>
            <td><input type="number" name="atividade_pessoas" value="<?php echo esc_attr( $pessoas ); ?>" min="1" /></td>
        </tr>
    </table>
    <?php
}

// Meta Box: Pacote
function tema_aventuras_pacote_meta_box( $post ) {
    wp_nonce_field( 'salvar_pacote_meta', 'pacote_meta_nonce' );
    $preco     = get_post_meta( $post->ID, '_pacote_preco', true );
    $periodo   = get_post_meta( $post->ID, '_pacote_periodo', true );
    $destaque  = get_post_meta( $post->ID, '_pacote_destaque', true );
    $inclui    = get_post_meta( $post->ID, '_pacote_inclui', true );
    ?>
    <table class="form-table">
        <tr>
            <th><?php _e( 'Preço (R$)', 'temaaventuras' ); ?></th>
            <td><input type="number" name="pacote_preco" value="<?php echo esc_attr( $preco ); ?>" step="0.01" /></td>
        </tr>
        <tr>
            <th><?php _e( 'Período / Label', 'temaaventuras' ); ?></th>
            <td><input type="text" name="pacote_periodo" value="<?php echo esc_attr( $periodo ); ?>" placeholder="Ex: por pessoa | por dia" /></td>
        </tr>
        <tr>
            <th><?php _e( 'Destacar como recomendado?', 'temaaventuras' ); ?></th>
            <td><input type="checkbox" name="pacote_destaque" value="1" <?php checked( $destaque, '1' ); ?> /></td>
        </tr>
        <tr>
            <th><?php _e( 'O que inclui (um por linha)', 'temaaventuras' ); ?></th>
            <td><textarea name="pacote_inclui" rows="6" style="width:100%"><?php echo esc_textarea( $inclui ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

// Meta Box: Depoimento
function tema_aventuras_depoimento_meta_box( $post ) {
    wp_nonce_field( 'salvar_depoimento_meta', 'depoimento_meta_nonce' );
    $nota      = get_post_meta( $post->ID, '_depoimento_nota', true );
    $atividade = get_post_meta( $post->ID, '_depoimento_atividade', true );
    $cidade    = get_post_meta( $post->ID, '_depoimento_cidade', true );
    ?>
    <table class="form-table">
        <tr>
            <th><?php _e( 'Nota (1-5)', 'temaaventuras' ); ?></th>
            <td><input type="number" name="depoimento_nota" value="<?php echo esc_attr( $nota ); ?>" min="1" max="5" /></td>
        </tr>
        <tr>
            <th><?php _e( 'Atividade Realizada', 'temaaventuras' ); ?></th>
            <td><input type="text" name="depoimento_atividade" value="<?php echo esc_attr( $atividade ); ?>" placeholder="Ex: Rafting no Rio Jacaré-Pepira" /></td>
        </tr>
        <tr>
            <th><?php _e( 'Cidade do Cliente', 'temaaventuras' ); ?></th>
            <td><input type="text" name="depoimento_cidade" value="<?php echo esc_attr( $cidade ); ?>" placeholder="Ex: São Paulo, SP" /></td>
        </tr>
    </table>
    <?php
}

// =========================================
// SALVAR META BOXES
// =========================================
function tema_aventuras_salvar_metas( $post_id ) {
    // Atividade
    if ( isset( $_POST['atividade_meta_nonce'] ) && wp_verify_nonce( $_POST['atividade_meta_nonce'], 'salvar_atividade_meta' ) ) {
        update_post_meta( $post_id, '_atividade_duracao', sanitize_text_field( $_POST['atividade_duracao'] ?? '' ) );
        update_post_meta( $post_id, '_atividade_nivel',   sanitize_text_field( $_POST['atividade_nivel'] ?? '' ) );
        update_post_meta( $post_id, '_atividade_preco',   floatval( $_POST['atividade_preco'] ?? 0 ) );
        update_post_meta( $post_id, '_atividade_pessoas', intval( $_POST['atividade_pessoas'] ?? 1 ) );
    }

    // Pacote
    if ( isset( $_POST['pacote_meta_nonce'] ) && wp_verify_nonce( $_POST['pacote_meta_nonce'], 'salvar_pacote_meta' ) ) {
        update_post_meta( $post_id, '_pacote_preco',    floatval( $_POST['pacote_preco'] ?? 0 ) );
        update_post_meta( $post_id, '_pacote_periodo',  sanitize_text_field( $_POST['pacote_periodo'] ?? '' ) );
        update_post_meta( $post_id, '_pacote_destaque', isset( $_POST['pacote_destaque'] ) ? '1' : '0' );
        update_post_meta( $post_id, '_pacote_inclui',   sanitize_textarea_field( $_POST['pacote_inclui'] ?? '' ) );
    }

    // Depoimento
    if ( isset( $_POST['depoimento_meta_nonce'] ) && wp_verify_nonce( $_POST['depoimento_meta_nonce'], 'salvar_depoimento_meta' ) ) {
        update_post_meta( $post_id, '_depoimento_nota',       intval( $_POST['depoimento_nota'] ?? 5 ) );
        update_post_meta( $post_id, '_depoimento_atividade',  sanitize_text_field( $_POST['depoimento_atividade'] ?? '' ) );
        update_post_meta( $post_id, '_depoimento_cidade',     sanitize_text_field( $_POST['depoimento_cidade'] ?? '' ) );
    }
}
add_action( 'save_post', 'tema_aventuras_salvar_metas' );
