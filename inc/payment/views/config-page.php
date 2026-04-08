<?php
/**
 * View: Página de Configurações de Pagamento
 * Variável disponível: $c = tema_aventuras_payment_config()
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;
?>
<div class="wrap ta-admin-wrap">
<h1>💳 <?php _e('Configurações de Pagamento — Mercado Pago','temaaventuras'); ?></h1>
<form method="post">
<?php wp_nonce_field('ta_salvar_pagamento','ta_payment_nonce'); ?>

<div class="ta-card">
    <h2>🔧 Ambiente</h2>
    <table class="form-table">
        <tr>
            <th>Modo Sandbox (Testes)</th>
            <td>
                <label><input type="checkbox" name="ta_mp_sandbox" value="1" <?php checked($c['sandbox']); ?>> Ativo — Desative em produção.</label>
                <p class="description">⚠️ Nunca processe pagamentos reais em sandbox!</p>
            </td>
        </tr>
    </table>
</div>

<div class="ta-card">
    <h2>🏭 Credenciais — Produção</h2>
    <p><a href="https://www.mercadopago.com.br/developers/panel/credentials" target="_blank">Obter credenciais →</a></p>
    <table class="form-table">
        <tr>
            <th><label for="mp_tok_prod">Access Token (Produção)</label></th>
            <td>
                <input type="password" id="mp_tok_prod" name="ta_mp_token_producao" value="<?php echo esc_attr($c['token_producao']); ?>" class="regular-text">
                <button type="button" class="button ta-vis" data-t="mp_tok_prod">👁</button>
            </td>
        </tr>
        <tr>
            <th><label for="mp_pub_prod">Public Key (Produção)</label></th>
            <td><input type="text" id="mp_pub_prod" name="ta_mp_pubkey_producao" value="<?php echo esc_attr($c['pubkey_producao']); ?>" class="regular-text"></td>
        </tr>
    </table>
</div>

<div class="ta-card">
    <h2>🧪 Credenciais — Sandbox (Testes)</h2>
    <table class="form-table">
        <tr>
            <th><label for="mp_tok_sb">Access Token (Sandbox)</label></th>
            <td>
                <input type="password" id="mp_tok_sb" name="ta_mp_token_sandbox" value="<?php echo esc_attr($c['token_sandbox']); ?>" class="regular-text">
                <button type="button" class="button ta-vis" data-t="mp_tok_sb">👁</button>
            </td>
        </tr>
        <tr>
            <th><label for="mp_pub_sb">Public Key (Sandbox)</label></th>
            <td><input type="text" id="mp_pub_sb" name="ta_mp_pubkey_sandbox" value="<?php echo esc_attr($c['pubkey_sandbox']); ?>" class="regular-text"></td>
        </tr>
    </table>
</div>

<div class="ta-card">
    <h2>⚙️ Configurações Gerais</h2>
    <table class="form-table">
        <tr>
            <th><label for="mp_notif">URL de Notificação (IPN)</label></th>
            <td>
                <input type="url" id="mp_notif" name="ta_mp_notificacao_url" value="<?php echo esc_url($c['notificacao_url']); ?>" class="regular-text">
                <p class="description">Configure esta URL em <strong>Mercado Pago → Suas Integrações → Notificações IPN</strong>.</p>
                <p class="description">Padrão: <code><?php echo esc_url(home_url('/?ta_pagamento_notificacao=1')); ?></code></p>
            </td>
        </tr>
        <tr>
            <th><label for="mp_email">E-mail do Administrador</label></th>
            <td><input type="email" id="mp_email" name="ta_mp_email_admin" value="<?php echo esc_attr($c['email_admin']); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="mp_parc">Máx. Parcelas (Cartão)</label></th>
            <td>
                <select id="mp_parc" name="ta_mp_parcelas_max">
                    <?php foreach ([1,2,3,4,5,6,8,10,12] as $p): ?>
                    <option value="<?php echo $p; ?>" <?php selected($c['parcelas_max'],$p); ?>><?php echo $p; ?>x</option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Juros do Parcelamento</th>
            <td>
                <label><input type="radio" name="ta_mp_juros" value="cliente" <?php checked($c['juros_por_conta'],'cliente'); ?>> Por conta do cliente</label><br>
                <label><input type="radio" name="ta_mp_juros" value="empresa" <?php checked($c['juros_por_conta'],'empresa'); ?>> Parcelado sem juros (empresa absorve)</label>
            </td>
        </tr>
    </table>
</div>

<div class="ta-card">
    <h2>🔌 Testar Conexão</h2>
    <button type="button" class="button" id="ta-testar-mp">🔌 Testar agora</button>
    <span id="ta-mp-res" style="margin-left:12px;"></span>
</div>

<?php submit_button('💾 Salvar Configurações'); ?>
</form>

<div class="ta-card">
    <h2>🧪 Cartões de Teste (Sandbox)</h2>
    <table class="widefat" style="max-width:700px">
        <thead><tr><th>Número</th><th>Bandeira</th><th>CVV</th><th>Validade</th><th>Resultado</th></tr></thead>
        <tbody>
            <tr><td><code>4013 5406 8274 6260</code></td><td>Visa</td><td>123</td><td>11/25</td><td>✅ Aprovado</td></tr>
            <tr><td><code>5031 4332 1540 6351</code></td><td>Mastercard</td><td>123</td><td>11/25</td><td>✅ Aprovado</td></tr>
            <tr><td><code>4000 0000 0000 0002</code></td><td>Visa</td><td>123</td><td>11/25</td><td>❌ Recusado</td></tr>
        </tbody>
    </table>
    <p><strong>CPF testes:</strong> <code>12345678909</code> | <strong>E-mail:</strong> qualquer @testuser.com</p>
</div>
</div>

<style>
.ta-admin-wrap{max-width:900px}
.ta-card{background:#fff;border:1px solid #ccd0d4;border-radius:4px;padding:20px;margin-bottom:20px}
.ta-card h2{margin-top:0;padding-bottom:10px;border-bottom:1px solid #eee}
</style>
<script>
document.querySelectorAll('.ta-vis').forEach(b=>b.addEventListener('click',()=>{const i=document.getElementById(b.dataset.t);i.type=i.type==='password'?'text':'password'}));
document.getElementById('ta-testar-mp')?.addEventListener('click',async()=>{
    const res=document.getElementById('ta-mp-res');
    res.textContent='⏳ Testando...';
    const fd=new FormData();
    fd.append('action','ta_testar_mp');
    fd.append('nonce','<?php echo wp_create_nonce("ta_testar_mp"); ?>');
    const r=await fetch(ajaxurl,{method:'POST',body:fd});
    const j=await r.json();
    res.innerHTML=j.success?'<span style="color:green">✅ OK — Ambiente: '+j.data.ambiente+'</span>':'<span style="color:red">❌ '+( j.data?.message||'Verifique as credenciais')+'</span>';
});
</script>
