<?php
/**
 * CPT Reservas + CPT Sessões de Atividade
 * Gerenciamento de reservas e sessões agendadas
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

/* =========================================
   CPT: SESSÃO DE ATIVIDADE
   ========================================= */
function ta_cpt_sessoes() {
    register_post_type('sessao_atividade', [
        'labels'          => ['name' => 'Sessões', 'singular_name' => 'Sessão', 'add_new_item' => 'Nova Sessão', 'menu_name' => 'Sessões'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => true,
        'supports'        => ['title', 'custom-fields'],
        'menu_icon'       => 'dashicons-calendar-alt',
        'menu_position'   => 8,
        'show_in_menu'    => 'ta-aventuras',
    ]);
}
add_action('init', 'ta_cpt_sessoes');

/* =========================================
   CPT: RESERVA
   ========================================= */
function ta_cpt_reservas() {
    register_post_type('reserva', [
        'labels'          => ['name' => 'Reservas', 'singular_name' => 'Reserva', 'menu_name' => 'Reservas'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => false,
        'supports'        => ['title', 'custom-fields'],
        'menu_icon'       => 'dashicons-tickets-alt',
        'menu_position'   => 9,
        'show_in_menu'    => 'ta-aventuras',
        'capabilities'    => ['create_posts' => 'do_not_allow'],
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'ta_cpt_reservas');

/* =========================================
   META BOX: SESSÕES NA ATIVIDADE
   ========================================= */
add_action('add_meta_boxes', function() {
    add_meta_box('ta_sessoes_atividade', '📅 Sessões Agendadas', 'ta_render_sessoes_metabox', 'atividade', 'normal', 'high');
    add_meta_box('ta_reserva_detalhes', '📋 Detalhes da Reserva', 'ta_render_reserva_metabox', 'reserva', 'normal', 'high');
    add_meta_box('ta_reserva_inscritos', '👥 Inscritos', 'ta_render_inscritos_metabox', 'reserva', 'normal', 'default');
    add_meta_box('ta_participantes_atividade', '👥 Participantes Confirmados', 'ta_render_participantes_metabox', 'atividade', 'side', 'default');
});

/* =========================================
   META BOX: SESSÕES (render completo)
   ========================================= */
function ta_render_sessoes_metabox( $post ) {
    wp_nonce_field( 'ta_salvar_sessoes', 'ta_sessoes_nonce' );
    $sessoes    = ta_get_sessoes_atividade( $post->ID );
    $preco_base = (float) ( get_post_meta( $post->ID, '_atividade_preco', true ) ?: 0 );
    $preco_fmt  = number_format( $preco_base, 2, ',', '.' );
    ?>
    <style>
    #ta-sessoes-container .ta-sessao-row{background:#fff;border:1px solid #c3c4c7;border-left:4px solid #009C3B;border-radius:0 4px 4px 0;padding:14px 16px;margin-bottom:14px}
    #ta-sessoes-container .ta-sessao-row:hover{box-shadow:0 2px 6px rgba(0,156,59,.15)}
    .ta-row-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #f0f0f1}
    .ta-row-num{font-weight:700;color:#009C3B;font-size:12px;text-transform:uppercase;letter-spacing:.05em}
    .ta-campos-grid{display:grid;grid-template-columns:170px 120px 90px 150px;gap:12px}
    .ta-campo label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#50575e;margin-bottom:5px}
    .ta-campo input{width:100%;padding:6px 9px;border:1px solid #c3c4c7;border-radius:3px;font-size:13px;box-sizing:border-box}
    .ta-campo input:focus{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1;outline:none}
    .ta-preco-wrap{position:relative}
    .ta-preco-prefix{position:absolute;left:8px;top:50%;transform:translateY(-50%);font-size:12px;color:#50575e;pointer-events:none;font-weight:700}
    .ta-preco-wrap input{padding-left:28px!important}
    .ta-obs-row{margin-top:10px}
    .ta-obs-row label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#50575e;margin-bottom:5px}
    .ta-obs-row input{width:100%;padding:6px 9px;border:1px solid #c3c4c7;border-radius:3px;font-size:13px;box-sizing:border-box}
    .ta-remover-sessao{color:#b32d2e!important;border-color:#b32d2e!important}
    .ta-remover-sessao:hover{background:#b32d2e!important;color:#fff!important}
    @media(max-width:1024px){.ta-campos-grid{grid-template-columns:1fr 1fr 1fr}}
    @media(max-width:782px){.ta-campos-grid{grid-template-columns:1fr 1fr}}
    </style>

    <div style="margin-top:6px">
        <div id="ta-sessoes-container">

        <?php if ( empty( $sessoes ) ) : ?>
            <p id="ta-sem-sessoes" style="color:#50575e;font-style:italic;margin-bottom:10px;">
                Nenhuma sessão cadastrada. Clique em <strong>+ Nova Sessão</strong> para adicionar.
            </p>
        <?php else : ?>
            <p id="ta-sem-sessoes" style="display:none"></p>
        <?php endif; ?>

        <?php foreach ( $sessoes as $i => $s ) :
            $pf = ( $s['preco'] > 0 )
                ? number_format( (float) $s['preco'], 2, ',', '.' )
                : $preco_fmt;
        ?>
        <div class="ta-sessao-row">
            <div class="ta-row-header">
                <span class="ta-row-num">📅 Sessão <?php echo ( $i + 1 ); ?></span>
                <button type="button" class="button ta-remover-sessao">✕ Remover</button>
            </div>
            <div class="ta-campos-grid">
                <div class="ta-campo">
                    <label>📅 Data *</label>
                    <input type="date" name="sessao_data[]" value="<?php echo esc_attr( $s['data'] ); ?>" required>
                </div>
                <div class="ta-campo">
                    <label>⏰ Horário *</label>
                    <input type="time" name="sessao_hora[]" value="<?php echo esc_attr( $s['hora'] ); ?>" required>
                </div>
                <div class="ta-campo">
                    <label>👥 Vagas *</label>
                    <input type="number" name="sessao_vagas[]" value="<?php echo esc_attr( $s['vagas'] ); ?>" min="1" max="9999" placeholder="10">
                </div>
                <div class="ta-campo">
                    <label>💰 Preço / pessoa *</label>
                    <div class="ta-preco-wrap">
                        <span class="ta-preco-prefix">R$</span>
                        <input type="text" name="sessao_preco[]" class="ta-preco-input" value="<?php echo esc_attr( $pf ); ?>" placeholder="0,00" inputmode="decimal">
                    </div>
                </div>
            </div>
            <div class="ta-obs-row">
                <label>📝 Observações (opcional)</label>
                <input type="text" name="sessao_obs[]" value="<?php echo esc_attr( $s['obs'] ?? '' ); ?>" placeholder="Ex: Ponto de encontro, o que levar, informações extra...">
            </div>
            <input type="hidden" name="sessao_id[]" value="<?php echo esc_attr( $s['id'] ?? wp_generate_uuid4() ); ?>">
        </div>
        <?php endforeach; ?>

        </div><!-- #ta-sessoes-container -->

        <button type="button" id="ta-add-sessao" class="button button-primary" style="margin-top:4px">
            + Nova Sessão
        </button>
        <p class="description" style="margin-top:8px">
            💡 Cada sessão é uma ocorrência da atividade em data/hora específica com vagas limitadas.<br>
            O preço pode ser diferente para datas de feriado, alta temporada, etc.
        </p>
    </div>

    <script>
    (function(){
        'use strict';
        let n = <?php echo count( $sessoes ); ?>;
        const pBase = '<?php echo esc_js( $preco_fmt ); ?>';

        function mascaraPreco(el) {
            el.addEventListener('input', function() {
                let v = this.value.replace(/\D/g,'');
                if (!v) { this.value=''; return; }
                this.value = (parseInt(v,10)/100).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2});
            });
            el.addEventListener('focus', function(){ if(!this.value) this.value='0,00'; });
            el.addEventListener('blur', function(){ if(this.value==='0,00') this.value=''; });
        }

        document.querySelectorAll('.ta-preco-input').forEach(mascaraPreco);

        function bindRemove(){
            document.querySelectorAll('.ta-remover-sessao').forEach(b=>{
                b.onclick = function(){
                    if(!confirm('Remover esta sessão?\nAs reservas existentes não serão afetadas.')) return;
                    b.closest('.ta-sessao-row').remove();
                    renumerar();
                    toggleVazio();
                };
            });
        }

        function renumerar(){
            document.querySelectorAll('.ta-row-num').forEach((el,i)=>{
                el.textContent = '📅 Sessão '+(i+1);
            });
        }

        function toggleVazio(){
            const msg  = document.getElementById('ta-sem-sessoes');
            const rows = document.querySelectorAll('.ta-sessao-row');
            if(msg) msg.style.display = rows.length ? 'none' : 'block';
        }

        bindRemove();

        function gerarUUID(){
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,c=>{
                const r=Math.random()*16|0;
                return(c==='x'?r:(r&0x3|0x8)).toString(16);
            });
        }

        document.getElementById('ta-add-sessao').addEventListener('click',function(){
            n++;
            const row = document.createElement('div');
            row.className = 'ta-sessao-row';
            row.innerHTML = `
                <div class="ta-row-header">
                    <span class="ta-row-num">📅 Sessão ${n}</span>
                    <button type="button" class="button ta-remover-sessao">✕ Remover</button>
                </div>
                <div class="ta-campos-grid">
                    <div class="ta-campo">
                        <label>📅 Data *</label>
                        <input type="date" name="sessao_data[]" required>
                    </div>
                    <div class="ta-campo">
                        <label>⏰ Horário *</label>
                        <input type="time" name="sessao_hora[]" value="08:00" required>
                    </div>
                    <div class="ta-campo">
                        <label>👥 Vagas *</label>
                        <input type="number" name="sessao_vagas[]" value="10" min="1" max="9999" placeholder="10">
                    </div>
                    <div class="ta-campo">
                        <label>💰 Preço / pessoa *</label>
                        <div class="ta-preco-wrap">
                            <span class="ta-preco-prefix">R$</span>
                            <input type="text" name="sessao_preco[]" class="ta-preco-input" value="${pBase}" placeholder="0,00" inputmode="decimal">
                        </div>
                    </div>
                </div>
                <div class="ta-obs-row">
                    <label>📝 Observações (opcional)</label>
                    <input type="text" name="sessao_obs[]" placeholder="Ex: Ponto de encontro, o que levar, informações extra...">
                </div>
                <input type="hidden" name="sessao_id[]" value="${gerarUUID()}">
            `;
            document.getElementById('ta-sessoes-container').appendChild(row);
            mascaraPreco(row.querySelector('.ta-preco-input'));
            bindRemove();
            toggleVazio();
            row.querySelector('input[type="date"]').focus();
        });
    })();
    </script>
    <?php
}

/* =========================================
   SALVAR SESSÕES
   ========================================= */
add_action('save_post_atividade', function($post_id) {
    if (!isset($_POST['ta_sessoes_nonce']) || !wp_verify_nonce($_POST['ta_sessoes_nonce'], 'ta_salvar_sessoes')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $datas  = $_POST['sessao_data']  ?? [];
    $horas  = $_POST['sessao_hora']  ?? [];
    $vagas  = $_POST['sessao_vagas'] ?? [];
    $precos = $_POST['sessao_preco'] ?? [];
    $ids    = $_POST['sessao_id']    ?? [];
    $obs    = $_POST['sessao_obs']   ?? [];

    $sessoes = [];
    foreach ($datas as $i => $data) {
        if (empty($data)) continue;

        // Converter preço de formato BRL (1.234,56) para float
        $preco_raw = $precos[$i] ?? '0';
        $preco_raw = preg_replace('/[^\d,]/', '', $preco_raw); // Remove pontos
        $preco_raw = str_replace(',', '.', $preco_raw);        // Vírgula → ponto
        $preco     = floatval($preco_raw);

        $sessoes[] = [
            'id'    => sanitize_text_field($ids[$i] ?? wp_generate_uuid4()),
            'data'  => sanitize_text_field($data),
            'hora'  => sanitize_text_field($horas[$i] ?? '08:00'),
            'vagas' => absint($vagas[$i] ?? 10),
            'preco' => $preco,
            'obs'   => sanitize_text_field($obs[$i] ?? ''),
        ];
    }
    update_post_meta($post_id, '_atividade_sessoes', $sessoes);
});

/* =========================================
   HELPER: próxima sessão com vagas livres
   ========================================= */
function ta_proxima_sessao(int $atividade_id): ?array {
    $sessoes = ta_get_sessoes_atividade($atividade_id, true);
    foreach ($sessoes as $s) {
        $v = ta_vagas_disponiveis($atividade_id, $s['id']);
        if ($v['livres'] > 0) {
            return [
                'id'     => $s['id'],
                'data'   => $s['data'],
                'hora'   => $s['hora'],
                'livres' => $v['livres'],
                'obs'    => $s['obs'] ?? '',
            ];
        }
    }
    return null;
}

/* =========================================
   HELPER: URL da página de checkout
   ========================================= */
function ta_checkout_url(int $atividade_id, string $sessao_id = ''): string {
    $checkout_page = get_page_by_path('checkout');
    $base = $checkout_page ? get_permalink($checkout_page) : home_url('/checkout/');

    $params = ['atividade' => $atividade_id];
    if ($sessao_id) $params['sessao'] = $sessao_id;

    return add_query_arg($params, $base);
}

/* =========================================
   HELPER: obter sessões de uma atividade
   ========================================= */
function ta_get_sessoes_atividade(int $atividade_id, bool $apenas_futuras = false): array {
    $sessoes = get_post_meta($atividade_id, '_atividade_sessoes', true) ?: [];
    if ($apenas_futuras) {
        $hoje = date('Y-m-d');
        $sessoes = array_filter($sessoes, fn($s) => $s['data'] >= $hoje);
    }
    usort($sessoes, fn($a, $b) => strcmp($a['data'] . $a['hora'], $b['data'] . $b['hora']));
    return array_values($sessoes);
}

/* =========================================
   HELPER: vagas disponíveis de uma sessão
   ========================================= */
function ta_vagas_disponiveis(int $atividade_id, string $sessao_id): array {
    $sessoes = ta_get_sessoes_atividade($atividade_id);
    $sessao  = null;
    foreach ($sessoes as $s) { if ($s['id'] === $sessao_id) { $sessao = $s; break; } }
    if (!$sessao) return ['total' => 0, 'ocupadas' => 0, 'livres' => 0];

    // Contar inscritos confirmados
    $reservas = get_posts([
        'post_type'   => 'reserva',
        'numberposts' => -1,
        'meta_query'  => [
            ['key' => '_reserva_atividade_id', 'value' => $atividade_id],
            ['key' => '_reserva_sessao_id',    'value' => $sessao_id],
            ['key' => '_reserva_status',       'value' => 'aprovado'],
        ],
    ]);

    $ocupadas = 0;
    foreach ($reservas as $r) {
        $ocupadas += (int) get_post_meta($r->ID, '_reserva_total_inscritos', true);
    }

    return [
        'total'   => $sessao['vagas'],
        'ocupadas'=> $ocupadas,
        'livres'  => max(0, $sessao['vagas'] - $ocupadas),
        'sessao'  => $sessao,
    ];
}

/* =========================================
   META BOX: DETALHES DA RESERVA
   ========================================= */
function ta_render_reserva_metabox($post) {
    $m = fn($k) => get_post_meta($post->ID, $k, true);
    $atividade = get_post($m('_reserva_atividade_id'));
    $status    = $m('_reserva_status') ?: 'pendente';
    $cores     = ['pendente' => '#f0ad4e', 'aprovado' => '#5cb85c', 'rejeitado' => '#d9534f', 'cancelado' => '#777'];
    ?>
    <table class="form-table">
        <tr><th>Status</th><td><strong style="color:<?php echo $cores[$status] ?? '#333'; ?>"><?php echo strtoupper($status); ?></strong></td></tr>
        <tr><th>Atividade</th><td><?php echo $atividade ? esc_html($atividade->post_title) : '—'; ?></td></tr>
        <tr><th>Data/Hora</th><td><?php echo esc_html($m('_reserva_data_atividade') . ' ' . $m('_reserva_hora_atividade')); ?></td></tr>
        <tr><th>Responsável</th><td><?php echo esc_html($m('_reserva_cliente_nome')); ?> (<?php echo esc_html($m('_reserva_cliente_email')); ?>)</td></tr>
        <tr><th>Telefone</th><td><?php echo esc_html($m('_reserva_cliente_telefone')); ?></td></tr>
        <tr><th>CPF Responsável</th><td><?php echo esc_html($m('_reserva_cliente_cpf')); ?></td></tr>
        <tr><th>Total Inscritos</th><td><?php echo esc_html($m('_reserva_total_inscritos')); ?></td></tr>
        <tr><th>Valor Total</th><td><strong>R$ <?php echo number_format((float)$m('_reserva_valor_total'), 2, ',', '.'); ?></strong></td></tr>
        <tr><th>Método</th><td><?php echo strtoupper($m('_reserva_metodo') ?: '—'); ?></td></tr>
        <tr><th>MP Payment ID</th><td><code><?php echo esc_html($m('_reserva_mp_payment_id') ?: '—'); ?></code></td></tr>
        <tr>
            <th>Ação Manual</th>
            <td>
                <form method="post" style="display:inline">
                    <?php wp_nonce_field('ta_acao_reserva_' . $post->ID); ?>
                    <input type="hidden" name="ta_reserva_id" value="<?php echo $post->ID; ?>">
                    <input type="hidden" name="ta_reserva_acao" value="aprovar">
                    <button class="button button-primary" onclick="return confirm('Confirmar aprovação manual?')">✅ Aprovar</button>
                </form>
                <form method="post" style="display:inline;margin-left:8px">
                    <?php wp_nonce_field('ta_acao_reserva_' . $post->ID); ?>
                    <input type="hidden" name="ta_reserva_id" value="<?php echo $post->ID; ?>">
                    <input type="hidden" name="ta_reserva_acao" value="cancelar">
                    <button class="button" style="color:red" onclick="return confirm('Cancelar reserva?')">❌ Cancelar</button>
                </form>
            </td>
        </tr>
    </table>
    <?php
}

/* =========================================
   META BOX: INSCRITOS DA RESERVA
   ========================================= */
function ta_render_inscritos_metabox($post) {
    $total    = (int) get_post_meta($post->ID, '_reserva_total_inscritos', true);
    $inscritos = get_post_meta($post->ID, '_reserva_inscritos', true) ?: [];
    if (empty($inscritos)) { echo '<p>Sem inscritos cadastrados.</p>'; return; }
    echo '<table class="widefat"><thead><tr><th>#</th><th>Nome</th><th>CPF</th><th>Telefone</th></tr></thead><tbody>';
    foreach ($inscritos as $i => $p) {
        echo '<tr><td>' . ($i+1) . '</td><td>' . esc_html($p['nome']) . '</td><td>' . esc_html($p['cpf']) . '</td><td>' . esc_html($p['telefone']) . '</td></tr>';
    }
    echo '</tbody></table>';
}

/* =========================================
   META BOX: PARTICIPANTES DA ATIVIDADE
   ========================================= */
function ta_render_participantes_metabox($post) {
    echo '<p style="font-size:11px;color:#666">Participantes com pagamento confirmado:</p>';
    $reservas = get_posts([
        'post_type'   => 'reserva',
        'numberposts' => -1,
        'meta_query'  => [
            ['key' => '_reserva_atividade_id', 'value' => $post->ID],
            ['key' => '_reserva_status', 'value' => 'aprovado'],
        ],
    ]);
    if (empty($reservas)) { echo '<p>Nenhum participante confirmado ainda.</p>'; return; }
    $total = 0;
    foreach ($reservas as $r) {
        $data  = get_post_meta($r->ID, '_reserva_data_atividade', true);
        $hora  = get_post_meta($r->ID, '_reserva_hora_atividade', true);
        $n     = (int)get_post_meta($r->ID, '_reserva_total_inscritos', true);
        $total += $n;
        echo '<div style="border:1px solid #eee;padding:6px;margin-bottom:6px;border-radius:3px;font-size:12px">';
        echo '<strong>' . esc_html(get_post_meta($r->ID,'_reserva_cliente_nome',true)) . '</strong>';
        echo ' (' . $n . ' inscrito' . ($n>1?'s':'') . ')';
        echo '<br><span style="color:#888">📅 ' . esc_html($data . ' ' . $hora) . '</span>';
        echo ' <a href="' . get_edit_post_link($r->ID) . '" style="float:right">ver →</a>';
        echo '</div>';
    }
    echo '<p><strong>Total: ' . $total . ' participante(s)</strong></p>';
    $url = admin_url('admin.php?page=ta-reservas&atividade=' . $post->ID . '&status=aprovado');
    echo '<a href="' . esc_url($url) . '" class="button button-primary" style="width:100%;text-align:center;margin-top:6px">📋 Ver lista completa</a>';
}

/* =========================================
   PROCESSAR AÇÕES MANUAIS (aprovar/cancelar)
   ========================================= */
add_action('admin_init', function() {
    if (!isset($_POST['ta_reserva_acao']) || !isset($_POST['ta_reserva_id'])) return;
    $reserva_id = intval($_POST['ta_reserva_id']);
    if (!wp_verify_nonce($_POST['_wpnonce'], 'ta_acao_reserva_' . $reserva_id)) return;
    if (!current_user_can('manage_options')) return;

    $acao = sanitize_text_field($_POST['ta_reserva_acao']);
    if ($acao === 'aprovar') {
        ta_atualizar_status_reserva($reserva_id, 'aprovado');
    } elseif ($acao === 'cancelar') {
        ta_atualizar_status_reserva($reserva_id, 'cancelado');
    }
});

/* =========================================
   HELPER: criar reserva
   ========================================= */
function ta_criar_reserva(array $dados): int|WP_Error {
    $inscritos = $dados['inscritos'] ?? [];
    $total     = count($inscritos);

    $sessao_info = ta_vagas_disponiveis((int)$dados['atividade_id'], $dados['sessao_id']);
    if ($sessao_info['livres'] < $total) {
        return new WP_Error('sem_vagas', 'Não há vagas suficientes nesta sessão.');
    }

    $titulo = sprintf('Reserva – %s – %s', get_the_title($dados['atividade_id']), date('d/m/Y', strtotime($dados['data_atividade'])));
    $post_id = wp_insert_post(['post_type' => 'reserva', 'post_title' => $titulo, 'post_status' => 'publish']);
    if (is_wp_error($post_id)) return $post_id;

    $metas = [
        '_reserva_atividade_id'     => (int)$dados['atividade_id'],
        '_reserva_sessao_id'        => sanitize_text_field($dados['sessao_id']),
        '_reserva_data_atividade'   => sanitize_text_field($dados['data_atividade']),
        '_reserva_hora_atividade'   => sanitize_text_field($dados['hora_atividade'] ?? ''),
        '_reserva_cliente_nome'     => sanitize_text_field($dados['nome']),
        '_reserva_cliente_email'    => sanitize_email($dados['email']),
        '_reserva_cliente_telefone' => sanitize_text_field($dados['telefone']),
        '_reserva_cliente_cpf'      => preg_replace('/\D/', '', $dados['cpf']),
        '_reserva_total_inscritos'  => $total,
        '_reserva_valor_total'      => floatval($dados['valor_total']),
        '_reserva_metodo'           => sanitize_text_field($dados['metodo'] ?? ''),
        '_reserva_status'           => 'pendente',
        '_reserva_inscritos'        => array_map(fn($p) => [
            'nome'     => sanitize_text_field($p['nome']),
            'cpf'      => preg_replace('/\D/', '', $p['cpf']),
            'telefone' => sanitize_text_field($p['telefone']),
        ], $inscritos),
    ];

    foreach ($metas as $k => $v) update_post_meta($post_id, $k, $v);
    return $post_id;
}

/* =========================================
   HELPER: atualizar status da reserva
   ========================================= */
function ta_atualizar_status_reserva(int $reserva_id, string $status, string $payment_id = ''): void {
    $status_validos = ['pendente', 'aprovado', 'rejeitado', 'cancelado'];
    if (!in_array($status, $status_validos)) return;

    update_post_meta($reserva_id, '_reserva_status', $status);
    if ($payment_id) update_post_meta($reserva_id, '_reserva_mp_payment_id', $payment_id);

    if ($status === 'aprovado') {
        ta_enviar_email_confirmacao($reserva_id);
    }
}

/* =========================================
   PÁGINA ADMIN: LISTA DE RESERVAS
   ========================================= */
function ta_reservas_admin_page() {
    $status_filter    = sanitize_text_field($_GET['status'] ?? '');
    $atividade_filter = intval($_GET['atividade'] ?? 0);

    $meta_query = [];
    if ($status_filter) $meta_query[] = ['key' => '_reserva_status', 'value' => $status_filter];
    if ($atividade_filter) $meta_query[] = ['key' => '_reserva_atividade_id', 'value' => $atividade_filter];

    $reservas = get_posts(['post_type' => 'reserva', 'numberposts' => 50, 'meta_query' => $meta_query ?: null]);

    include TEMA_AVENTURAS_DIR . '/inc/payment/views/reservas-page.php';
}
