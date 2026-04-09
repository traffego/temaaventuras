<?php
/**
 * Custom Post Types: Atividades, Pacotes, Depoimentos, Guias
 *
 * @package TemaAventuras
 */

defined( 'ABSPATH' ) || exit;

// =========================================
// MENU PRINCIPAL: GESTÃO
// =========================================
function tema_aventuras_menu_gestao() {
    add_menu_page(
        __( 'Gestão', 'temaaventuras' ),
        __( 'GESTÃO', 'temaaventuras' ),
        'edit_posts',
        'gestao-aventuras',
        '__return_null',
        'dashicons-admin-tools',
        4
    );

    // Submenus explícitos (os CPTs serão adicionados automaticamente via show_in_menu)
    add_submenu_page( 'gestao-aventuras', __( 'Atividades', 'temaaventuras' ),   __( 'Atividades', 'temaaventuras' ),   'edit_posts', 'edit.php?post_type=atividade',  '' );
    add_submenu_page( 'gestao-aventuras', __( 'Pacotes', 'temaaventuras' ),      __( 'Pacotes', 'temaaventuras' ),      'edit_posts', 'edit.php?post_type=pacote',     '' );
    add_submenu_page( 'gestao-aventuras', __( 'Depoimentos', 'temaaventuras' ),  __( 'Depoimentos', 'temaaventuras' ),  'edit_posts', 'edit.php?post_type=depoimento', '' );
    add_submenu_page( 'gestao-aventuras', __( 'Guias', 'temaaventuras' ),        __( 'Guias', 'temaaventuras' ),        'edit_posts', 'edit.php?post_type=guia',       '' );

    // Remove o item duplicado que o WP cria automaticamente
    remove_submenu_page( 'gestao-aventuras', 'gestao-aventuras' );
}
add_action( 'admin_menu', 'tema_aventuras_menu_gestao', 20 );

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
        'show_in_rest'        => true,
        'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'rewrite'             => [ 'slug' => 'atividades' ],
        'show_in_menu'        => 'gestao-aventuras',
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
        'show_in_menu'      => 'gestao-aventuras',
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
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
        'show_in_menu'  => 'gestao-aventuras',
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

    // Campos existentes
    $duracao  = get_post_meta( $post->ID, '_atividade_duracao',  true );
    $nivel    = get_post_meta( $post->ID, '_atividade_nivel',    true );
    $preco    = get_post_meta( $post->ID, '_atividade_preco',    true );
    $pessoas  = get_post_meta( $post->ID, '_atividade_pessoas',  true );

    // Novos campos
    $data     = get_post_meta( $post->ID, '_atividade_data',     true );
    $horario  = get_post_meta( $post->ID, '_atividade_horario',  true );
    $vagas    = get_post_meta( $post->ID, '_atividade_vagas',    true );
    $obs      = get_post_meta( $post->ID, '_atividade_obs',      true );
    $img_id   = (int) get_post_meta( $post->ID, '_atividade_imagem', true );
    $img_url  = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';

    // Enfileira media uploader
    wp_enqueue_media();
    ?>
    <style>
        #atividade-imagem-preview { max-width:200px; max-height:140px; display:block; border-radius:6px; margin-bottom:8px; }
        .ta-img-wrap { display:flex; align-items:flex-start; gap:12px; flex-wrap:wrap; }
        .ta-img-btns { display:flex; flex-direction:column; gap:4px; }
    </style>

    <table class="form-table">

        <!-- ── IMAGEM ──────────────────────────────── -->
        <tr>
            <th><?php _e( '🖼️ Imagem da Atividade', 'temaaventuras' ); ?></th>
            <td>
                <div class="ta-img-wrap">
                    <?php if ( $img_url ) : ?>
                        <img id="atividade-imagem-preview" src="<?php echo esc_url( $img_url ); ?>" alt="" />
                    <?php else : ?>
                        <img id="atividade-imagem-preview" src="" alt="" style="display:none;" />
                    <?php endif; ?>
                    <div class="ta-img-btns">
                        <button type="button" id="atividade-imagem-btn" class="button">
                            📷 <?php _e( 'Selecionar Imagem', 'temaaventuras' ); ?>
                        </button>
                        <button type="button" id="atividade-imagem-remover" class="button" style="color:red;<?php echo $img_id ? '' : 'display:none;'; ?>">
                            ✕ <?php _e( 'Remover', 'temaaventuras' ); ?>
                        </button>
                    </div>
                </div>
                <input type="hidden" id="atividade-imagem-id" name="atividade_imagem" value="<?php echo esc_attr( $img_id ); ?>" />
                <script>
                (function(){
                    var frame;
                    document.getElementById('atividade-imagem-btn').addEventListener('click', function(e){
                        e.preventDefault();
                        if (frame) { frame.open(); return; }
                        frame = wp.media({ title: 'Selecionar imagem da atividade', button: { text: 'Usar esta imagem' }, multiple: false });
                        frame.on('select', function(){
                            var att = frame.state().get('selection').first().toJSON();
                            document.getElementById('atividade-imagem-id').value = att.id;
                            var prev = document.getElementById('atividade-imagem-preview');
                            prev.src = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
                            prev.style.display = 'block';
                            document.getElementById('atividade-imagem-remover').style.display = 'inline-block';
                        });
                        frame.open();
                    });
                    document.getElementById('atividade-imagem-remover').addEventListener('click', function(e){
                        e.preventDefault();
                        document.getElementById('atividade-imagem-id').value = '';
                        var prev = document.getElementById('atividade-imagem-preview');
                        prev.src = ''; prev.style.display = 'none';
                        this.style.display = 'none';
                    });
                })();
                </script>
            </td>
        </tr>

        <!-- ── DATA E HORÁRIO ───────────────────────── -->
        <tr>
            <th><?php _e( '📅 Data', 'temaaventuras' ); ?></th>
            <td>
                <input type="date" name="atividade_data" value="<?php echo esc_attr( $data ); ?>" />
                <p class="description"><?php _e( 'Data principal da atividade', 'temaaventuras' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><?php _e( '⏰ Horário', 'temaaventuras' ); ?></th>
            <td>
                <input type="time" name="atividade_horario" value="<?php echo esc_attr( $horario ); ?>" />
            </td>
        </tr>

        <!-- ── VAGAS E PREÇO ────────────────────────── -->
        <tr>
            <th><?php _e( '👥 Vagas', 'temaaventuras' ); ?></th>
            <td>
                <input type="number" name="atividade_vagas" value="<?php echo esc_attr( $vagas ); ?>" min="1" placeholder="Ex: 10" />
            </td>
        </tr>
        <tr>
            <th><?php _e( '💰 Preço / pessoa (R$)', 'temaaventuras' ); ?></th>
            <td>
                <input type="number" name="atividade_preco" value="<?php echo esc_attr( $preco ); ?>" step="0.01" min="0" placeholder="150.00" />
            </td>
        </tr>

        <!-- ── OBSERVAÇÕES ──────────────────────────── -->
        <tr>
            <th><?php _e( '📝 Observações', 'temaaventuras' ); ?></th>
            <td>
                <textarea name="atividade_obs" rows="3" style="width:100%" placeholder="Ex: Levar protetor solar e repelente"><?php echo esc_textarea( $obs ); ?></textarea>
            </td>
        </tr>

        <!-- ── CAMPOS GERAIS ────────────────────────── -->
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
        update_post_meta( $post_id, '_atividade_duracao',  sanitize_text_field( $_POST['atividade_duracao']  ?? '' ) );
        update_post_meta( $post_id, '_atividade_nivel',    sanitize_text_field( $_POST['atividade_nivel']    ?? '' ) );
        update_post_meta( $post_id, '_atividade_preco',    floatval( $_POST['atividade_preco']   ?? 0 ) );
        update_post_meta( $post_id, '_atividade_pessoas',  intval(   $_POST['atividade_pessoas']  ?? 1 ) );
        // Novos campos
        update_post_meta( $post_id, '_atividade_data',     sanitize_text_field( $_POST['atividade_data']     ?? '' ) );
        update_post_meta( $post_id, '_atividade_horario',  sanitize_text_field( $_POST['atividade_horario']  ?? '' ) );
        update_post_meta( $post_id, '_atividade_vagas',    absint( $_POST['atividade_vagas']     ?? 0 ) );
        update_post_meta( $post_id, '_atividade_obs',      sanitize_textarea_field( $_POST['atividade_obs']  ?? '' ) );
        update_post_meta( $post_id, '_atividade_imagem',   absint( $_POST['atividade_imagem']    ?? 0 ) );
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

// =========================================
// CPT: GUIAS
// =========================================
function tema_aventuras_cpt_guias() {
    $labels = [
        'name'          => __( 'Guias', 'temaaventuras' ),
        'singular_name' => __( 'Guia', 'temaaventuras' ),
        'add_new'       => __( 'Adicionar Guia', 'temaaventuras' ),
        'add_new_item'  => __( 'Novo Guia', 'temaaventuras' ),
        'edit_item'     => __( 'Editar Guia', 'temaaventuras' ),
        'all_items'     => __( 'Todos os Guias', 'temaaventuras' ),
        'menu_name'     => __( 'Guias', 'temaaventuras' ),
    ];

    register_post_type( 'guia', [
        'labels'        => $labels,
        'public'        => true,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'supports'      => [ 'title', 'custom-fields' ],
        'rewrite'       => [ 'slug' => 'guias' ],
        'show_in_menu'  => 'gestao-aventuras',
        'has_archive'   => false,
    ] );
}
add_action( 'init', 'tema_aventuras_cpt_guias' );

// Meta Box: Guia
function tema_aventuras_guia_meta_box_register() {
    add_meta_box(
        'guia_detalhes',
        __( '🧭 Detalhes do Guia', 'temaaventuras' ),
        'tema_aventuras_guia_meta_box',
        'guia',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tema_aventuras_guia_meta_box_register' );

function tema_aventuras_guia_meta_box( $post ) {
    wp_nonce_field( 'salvar_guia_meta', 'guia_meta_nonce' );
    wp_enqueue_media();

    $subtitulo = get_post_meta( $post->ID, '_guia_subtitulo',  true );
    $descricao = get_post_meta( $post->ID, '_guia_descricao',  true );
    $img_id    = (int) get_post_meta( $post->ID, '_guia_foto', true );
    $img_url   = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';
    ?>
    <style>
        #guia-foto-preview { max-width:180px; max-height:180px; border-radius:50%; object-fit:cover; display:block; margin-bottom:8px; }
        .ta-guia-img-wrap { display:flex; align-items:flex-start; gap:12px; flex-wrap:wrap; }
        .ta-guia-img-btns { display:flex; flex-direction:column; gap:4px; }
    </style>
    <table class="form-table">

        <!-- FOTO -->
        <tr>
            <th><?php _e( '📷 Foto do Guia', 'temaaventuras' ); ?></th>
            <td>
                <div class="ta-guia-img-wrap">
                    <?php if ( $img_url ) : ?>
                        <img id="guia-foto-preview" src="<?php echo esc_url( $img_url ); ?>" alt="" />
                    <?php else : ?>
                        <img id="guia-foto-preview" src="" alt="" style="display:none;" />
                    <?php endif; ?>
                    <div class="ta-guia-img-btns">
                        <button type="button" id="guia-foto-btn" class="button">
                            📷 <?php _e( 'Selecionar Foto', 'temaaventuras' ); ?>
                        </button>
                        <button type="button" id="guia-foto-remover" class="button" style="color:red;<?php echo $img_id ? '' : 'display:none;'; ?>">
                            ✕ <?php _e( 'Remover', 'temaaventuras' ); ?>
                        </button>
                    </div>
                </div>
                <input type="hidden" id="guia-foto-id" name="guia_foto" value="<?php echo esc_attr( $img_id ); ?>" />
                <script>
                (function(){
                    var frame;
                    document.getElementById('guia-foto-btn').addEventListener('click', function(e){
                        e.preventDefault();
                        if (frame) { frame.open(); return; }
                        frame = wp.media({ title: 'Selecionar foto do guia', button: { text: 'Usar esta foto' }, multiple: false });
                        frame.on('select', function(){
                            var att = frame.state().get('selection').first().toJSON();
                            document.getElementById('guia-foto-id').value = att.id;
                            var prev = document.getElementById('guia-foto-preview');
                            prev.src = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
                            prev.style.display = 'block';
                            document.getElementById('guia-foto-remover').style.display = 'inline-block';
                        });
                        frame.open();
                    });
                    document.getElementById('guia-foto-remover').addEventListener('click', function(e){
                        e.preventDefault();
                        document.getElementById('guia-foto-id').value = '';
                        var prev = document.getElementById('guia-foto-preview');
                        prev.src = ''; prev.style.display = 'none';
                        this.style.display = 'none';
                    });
                })();
                </script>
            </td>
        </tr>

        <!-- SUBTÍTULO -->
        <tr>
            <th><?php _e( '🏷️ Subtítulo / Especialidade', 'temaaventuras' ); ?></th>
            <td>
                <input type="text" name="guia_subtitulo" value="<?php echo esc_attr( $subtitulo ); ?>" style="width:100%;" placeholder="Ex: Guia de Rafting e Trilhas" />
                <p class="description"><?php _e( 'Aparece abaixo do nome na listagem.', 'temaaventuras' ); ?></p>
            </td>
        </tr>

        <!-- DESCRIÇÃO -->
        <tr>
            <th><?php _e( '📝 Descrição', 'temaaventuras' ); ?></th>
            <td>
                <textarea name="guia_descricao" rows="5" style="width:100%;" placeholder="Breve bio do guia, experiências, certificações..."><?php echo esc_textarea( $descricao ); ?></textarea>
            </td>
        </tr>

    </table>
    <?php
}

// Salvar metas do Guia
function tema_aventuras_salvar_guia_metas( $post_id ) {
    if ( ! isset( $_POST['guia_meta_nonce'] ) || ! wp_verify_nonce( $_POST['guia_meta_nonce'], 'salvar_guia_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, '_guia_subtitulo', sanitize_text_field( $_POST['guia_subtitulo'] ?? '' ) );
    update_post_meta( $post_id, '_guia_descricao', sanitize_textarea_field( $_POST['guia_descricao'] ?? '' ) );
    update_post_meta( $post_id, '_guia_foto',      absint( $_POST['guia_foto'] ?? 0 ) );
}
add_action( 'save_post_guia', 'tema_aventuras_salvar_guia_metas' );
