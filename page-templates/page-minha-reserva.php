<?php
/**
 * Template Name: Minha Reserva
 * Template Post Type: page
 *
 * Permite que o cliente consulte sua reserva por e-mail + código.
 *
 * @package TemaAventuras
 */
defined( 'ABSPATH' ) || exit;

get_header();

$reserva_id    = intval( $_GET['reserva'] ?? 0 );
$token         = sanitize_text_field( $_GET['token'] ?? '' );
$busca_email   = sanitize_email( $_POST['busca_email'] ?? '' );
$busca_codigo  = intval( $_POST['busca_codigo'] ?? 0 );
$reserva       = null;
$erro          = '';

// Validar acesso via link da confirmação (token + ID)
if ( $reserva_id && $token && $token === ta_token_reserva( $reserva_id ) ) {
    $reserva = get_post( $reserva_id );
    if ( ! $reserva || $reserva->post_type !== 'reserva' ) {
        $reserva = null;
        $erro    = 'Reserva não encontrada.';
    }
}

// Buscar via formulário (e-mail + código)
if ( ! $reserva && isset( $_POST['buscar_reserva'] ) && $busca_email && $busca_codigo ) {
    check_admin_referer( 'ta_buscar_reserva', 'ta_busca_nonce' );
    $candidata = get_post( $busca_codigo );
    if ( $candidata && $candidata->post_type === 'reserva' ) {
        $email_salvo = get_post_meta( $busca_codigo, '_reserva_cliente_email', true );
        if ( strtolower( $email_salvo ) === strtolower( $busca_email ) ) {
            $reserva = $candidata;
        } else {
            $erro = 'E-mail ou código incorretos. Verifique e tente novamente.';
        }
    } else {
        $erro = 'Nenhuma reserva encontrada com este código.';
    }
}

// Dados da reserva (se encontrada)
$m = $reserva ? fn( $k ) => get_post_meta( $reserva->ID, $k, true ) : null;
?>

<main id="conteudo-principal" role="main" style="padding-top:var(--altura-nav);">
<section class="section">
<div class="container--estreito">

    <div class="texto-centro" style="margin-bottom:var(--espaco-3xl);">
        <span class="section-header__eyebrow">🎟️ Consulta de Reserva</span>
        <h1 style="font-size:clamp(2rem,4vw,3rem);margin-top:var(--espaco-md);">Minha Reserva</h1>
        <p style="color:var(--texto-muted);">Consulte o status e os detalhes da sua reserva.</p>
    </div>

    <?php if ( ! $reserva ) : ?>
    <!-- FORMULÁRIO DE BUSCA -->
    <div style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-2xl);">
        <h2 style="font-size:1.4rem;margin-bottom:var(--espaco-xl);">🔍 Encontrar minha reserva</h2>

        <?php if ( $erro ) : ?>
        <div style="background:rgba(220,38,38,.12);border:1px solid rgba(220,38,38,.3);border-radius:var(--raio-md);padding:var(--espaco-md) var(--espaco-lg);color:#f87171;margin-bottom:var(--espaco-xl);">
            ⚠️ <?php echo esc_html( $erro ); ?>
        </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field( 'ta_buscar_reserva', 'ta_busca_nonce' ); ?>
            <input type="hidden" name="buscar_reserva" value="1">

            <div class="grid grid--2" style="margin-bottom:var(--espaco-lg);">
                <div class="form-grupo">
                    <label for="busca-codigo">Código da Reserva *</label>
                    <input type="number" id="busca-codigo" name="busca_codigo"
                           value="<?php echo esc_attr( $busca_codigo ?: '' ); ?>"
                           required placeholder="Ex: 1234"
                           min="1">
                    <span style="font-size:0.75rem;color:var(--texto-muted);margin-top:4px;display:block;">
                        Enviado no e-mail de confirmação.
                    </span>
                </div>
                <div class="form-grupo">
                    <label for="busca-email">E-mail usado na reserva *</label>
                    <input type="email" id="busca-email" name="busca_email"
                           value="<?php echo esc_attr( $busca_email ); ?>"
                           required placeholder="seu@email.com">
                </div>
            </div>
            <button type="submit" class="btn btn--primario btn--grande" style="width:100%">
                🔍 Buscar reserva
            </button>
        </form>
    </div>

    <div style="margin-top:var(--espaco-2xl);text-align:center;">
        <p style="color:var(--texto-muted);font-size:var(--tamanho-pequeno);">
            Não encontrou? Entre em contato:
            <a href="<?php echo esc_url( ta_whatsapp_link( 'Olá, preciso de ajuda com minha reserva.' ) ); ?>"
               target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--pequeno" style="margin-left:8px">
                📲 WhatsApp
            </a>
        </p>
    </div>

    <?php else :
        $status     = $m( '_reserva_status' ) ?: 'pendente';
        $nome       = $m( '_reserva_cliente_nome' );
        $email      = $m( '_reserva_cliente_email' );
        $atividade  = get_the_title( $m( '_reserva_atividade_id' ) );
        $data_atv   = date( 'd/m/Y', strtotime( $m( '_reserva_data_atividade' ) ) );
        $hora_atv   = $m( '_reserva_hora_atividade' );
        $inscritos  = $m( '_reserva_inscritos' ) ?: [];
        $valor      = number_format( (float) $m( '_reserva_valor_total' ), 2, ',', '.' );
        $metodo     = strtoupper( $m( '_reserva_metodo' ) ?: '' );
        $atividade_id = (int) $m( '_reserva_atividade_id' );
        $cores  = [ 'pendente' => '#f0ad4e', 'aprovado' => '#22c55e', 'rejeitado' => '#ef4444', 'cancelado' => '#6b7280' ];
        $icones = [ 'pendente' => '⏳', 'aprovado' => '✅', 'rejeitado' => '❌', 'cancelado' => '🚫' ];
        $cor    = $cores[ $status ] ?? '#6b7280';
        $icone  = $icones[ $status ] ?? '❔';
    ?>

    <!-- STATUS BADGE -->
    <div style="text-align:center;margin-bottom:var(--espaco-2xl);">
        <span style="background:<?php echo $cor; ?>22;border:2px solid <?php echo $cor; ?>;color:<?php echo $cor; ?>;
              padding:var(--espaco-md) var(--espaco-2xl);border-radius:var(--raio-full);
              font-weight:var(--peso-negrito);font-size:1.1rem;display:inline-block;">
            <?php echo $icone; ?> <?php echo ucfirst( $status ); ?>
        </span>
        <p style="margin-top:var(--espaco-md);color:var(--texto-muted);font-size:var(--tamanho-pequeno);">
            Reserva #<?php echo $reserva->ID; ?> • Olá, <?php echo esc_html( $nome ); ?>!
        </p>
    </div>

    <!-- DETALHES GERAIS -->
    <div style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-2xl);margin-bottom:var(--espaco-xl);">
        <h2 style="font-size:1.2rem;margin-bottom:var(--espaco-xl);padding-bottom:var(--espaco-md);border-bottom:1px solid var(--borda-glass);">
            📋 Detalhes do Evento
        </h2>
        <div class="grid grid--2" style="gap:var(--espaco-lg);">
            <?php
            $itens = [
                [ '🌊', 'Atividade',    $atividade ],
                [ '📅', 'Data',         $data_atv ],
                [ '⏰', 'Horário',      $hora_atv ],
                [ '👥', 'Participantes', count( $inscritos ) . ' pessoa(s)' ],
                [ '💰', 'Valor Total',  'R$ ' . $valor ],
                [ '💳', 'Pagamento',    $metodo ],
            ];
            foreach ( $itens as $item ) :
            ?>
            <div style="display:flex;gap:var(--espaco-md);align-items:flex-start;">
                <span style="font-size:1.4rem;flex-shrink:0;width:36px;text-align:center;"><?php echo $item[0]; ?></span>
                <div>
                    <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--texto-muted);"><?php echo esc_html( $item[1] ); ?></div>
                    <div style="font-weight:var(--peso-medio);color:var(--texto-primario);"><?php echo esc_html( $item[2] ); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- LISTA DE INSCRITOS -->
    <?php if ( ! empty( $inscritos ) ) : ?>
    <div style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-2xl);margin-bottom:var(--espaco-xl);">
        <h2 style="font-size:1.2rem;margin-bottom:var(--espaco-lg);">👥 Inscritos</h2>
        <table style="width:100%;border-collapse:collapse;font-size:var(--tamanho-pequeno);">
            <thead>
                <tr style="background:var(--fundo-glass);">
                    <th style="padding:10px 12px;text-align:left;">#</th>
                    <th style="padding:10px 12px;text-align:left;">Nome</th>
                    <th style="padding:10px 12px;text-align:left;">CPF</th>
                    <th style="padding:10px 12px;text-align:left;">Telefone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $inscritos as $i => $p ) :
                    // Mascarar CPF: 123.***.**-45
                    $cpf = $p['cpf'];
                    if ( strlen( $cpf ) === 11 ) {
                        $cpf = substr( $cpf, 0, 3 ) . '.***.***-' . substr( $cpf, -2 );
                    }
                ?>
                <tr style="border-bottom:1px solid var(--borda-glass);">
                    <td style="padding:10px 12px;"><?php echo ( $i + 1 ); ?></td>
                    <td style="padding:10px 12px;"><?php echo esc_html( $p['nome'] ); ?></td>
                    <td style="padding:10px 12px;"><?php echo esc_html( $cpf ); ?></td>
                    <td style="padding:10px 12px;"><?php echo esc_html( $p['telefone'] ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- INFORMAÇÕES IMPORTANTES (somente aprovado) -->
    <?php if ( $status === 'aprovado' ) : ?>
    <div style="background:rgba(255,223,0,.07);border:1px solid rgba(255,223,0,.2);border-radius:var(--raio-xl);padding:var(--espaco-xl);margin-bottom:var(--espaco-xl);">
        <h3 style="font-size:1rem;color:var(--cor-secundaria);margin-bottom:var(--espaco-md);">⚠️ Informações Importantes</h3>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:var(--espaco-sm);">
            <li style="font-size:var(--tamanho-pequeno);color:var(--texto-secundario);">📍 Chegue com 30 minutos de antecedência</li>
            <li style="font-size:var(--tamanho-pequeno);color:var(--texto-secundario);">👕 Use roupas adequadas para atividades ao ar livre</li>
            <li style="font-size:var(--tamanho-pequeno);color:var(--texto-secundario);">🪪 Todos os inscritos devem portar documento com foto</li>
            <li style="font-size:var(--tamanho-pequeno);color:var(--texto-secundario);">🌧️ Em caso de mau tempo, aguarde contato da equipe</li>
        </ul>
    </div>
    <?php endif; ?>

    <!-- AÇÕES -->
    <div style="display:flex;gap:var(--espaco-md);flex-wrap:wrap;justify-content:center;margin-top:var(--espaco-xl);">
        <?php if ( $status === 'aprovado' ) : ?>
        <button onclick="window.print()" class="btn btn--secundario">🖨️ Imprimir comprovante</button>
        <?php endif; ?>
        <a href="<?php echo esc_url( ta_whatsapp_link( 'Olá, tenho dúvidas sobre minha reserva #' . $reserva->ID ) ); ?>"
           class="btn btn--primario" target="_blank" rel="noopener noreferrer">
            📲 Falar com a equipe
        </a>
        <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn--ghost">
            🔄 Consultar outra reserva
        </a>
    </div>

    <?php endif; // fim if !$reserva ?>

</div>
</section>
</main>

<style>
@media print {
    .navbar,.footer,.whatsapp-float,.btn{display:none!important}
    body{background:#fff;color:#000}
    [style*="background:var"]{background:#fff!important;color:#000!important;border-color:#ccc!important}
}
</style>

<?php get_footer(); ?>
