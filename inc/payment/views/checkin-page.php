<?php
/**
 * View: Lista de Check-in Admin
 * Variável: $reservas, $atividade_filter
 *
 * @package TemaAventuras
 */
defined('ABSPATH') || exit;

$atividades = get_posts(['post_type' => 'atividade', 'numberposts' => -1, 'post_status' => 'publish']);
?>
<div class="wrap" style="max-width: 1000px;">
    <h1>✅ Controle de Presença (Check-in)</h1>
    <p>Selecione um evento para abrir a lista de passageiros e realizar o check-in na hora do embarque.</p>

    <!-- Filtro -->
    <div style="background:#fff; padding: 20px; border:1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
        <form method="get" style="display:flex; gap:12px; align-items:center;">
            <input type="hidden" name="page" value="ta-checkin">
            <label for="atividade" style="font-weight:bold;">Selecione a Atividade/Evento:</label>
            <select name="atividade" id="atividade" style="min-width:300px;">
                <option value="">-- Escolha um Evento --</option>
                <?php foreach ($atividades as $at): ?>
                <option value="<?php echo $at->ID; ?>" <?php selected($atividade_filter, $at->ID); ?>>
                    <?php echo esc_html($at->post_title); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button button-primary">Carregar Lista</button>
        </form>
    </div>

    <!-- Resultados -->
    <?php if ($atividade_filter > 0): ?>
        <?php if (empty($reservas)): ?>
            <div style="background:#fff; border:1px solid #ccd0d4; padding:30px; text-align:center; border-radius:4px;">
                <p style="font-size:2em;margin:0;">🤷</p>
                <p>Nenhuma reserva PAGA (aprovada) encontrada para esse evento.</p>
            </div>
        <?php else: ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Nº Reserva</th>
                        <th>Nome do Passageiro</th>
                        <th>Documento (CPF)</th>
                        <th>Telefone</th>
                        <th>Status do Pagamento</th>
                        <th style="text-align:right;">Ação de Check-in</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $nonce = wp_create_nonce('ta_admin_checkin');
                    $total_passageiros = 0;
                    $presentes = 0;

                    foreach ($reservas as $r): 
                        $inscritos = get_post_meta($r->ID, '_reserva_inscritos', true) ?: [];
                        $total_passageiros += count($inscritos);

                        foreach ($inscritos as $index => $pessoa):
                            $checkin = !empty($pessoa['checkin']);
                            if ($checkin) $presentes++;
                    ?>
                    <tr>
                        <td><strong>#<?php echo $r->ID; ?></strong></td>
                        <td style="font-size:1.1em;">
                            <?php echo esc_html($pessoa['nome']); ?>
                            <?php if ($index === 0): ?>
                                <span style="background:#eee;color:#666;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:4px;">Titular</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($pessoa['cpf']); ?></td>
                        <td><?php echo esc_html($pessoa['telefone']); ?></td>
                        <td><span style="color:green;font-weight:bold;">Aprovado</span></td>
                        <td style="text-align:right;">
                            <button type="button" 
                                    class="button btn-checkin <?php echo $checkin ? 'button-primary' : ''; ?>" 
                                    data-reserva="<?php echo $r->ID; ?>" 
                                    data-indice="<?php echo $index; ?>"
                                    data-checkin="<?php echo $checkin ? '1' : '0'; ?>"
                                    style="<?php echo $checkin ? 'background:#00a32a;border-color:#008a20;' : ''; ?>">
                                <?php echo $checkin ? '✔️ Presente' : 'Dar Check-in'; ?>
                            </button>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    endforeach; 
                    ?>
                </tbody>
            </table>

            <div style="margin-top:20px; font-size:1.2em; text-align:right;">
                <strong><span id="contador-presentes"><?php echo $presentes; ?></span> / <?php echo $total_passageiros; ?></strong> check-ins confirmados.
            </div>

            <script>
            document.querySelectorAll('.btn-checkin').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const reservaId = this.dataset.reserva;
                    const indice = this.dataset.indice;
                    const atual = this.dataset.checkin === '1';
                    const novoEstado = !atual;
                    
                    this.disabled = true;
                    this.textContent = '⏳ Processando...';

                    const fd = new URLSearchParams();
                    fd.append('action', 'ta_fazer_checkin');
                    fd.append('nonce', '<?php echo $nonce; ?>');
                    fd.append('reserva_id', reservaId);
                    fd.append('indice', indice);
                    fd.append('estado', novoEstado ? '1' : '0');

                    try {
                        const res = await fetch(ajaxurl, {
                            method: 'POST',
                            body: fd
                        });
                        const data = await res.json();

                        if (data.success) {
                            // Atualizar botão
                            this.dataset.checkin = novoEstado ? '1' : '0';
                            if (novoEstado) {
                                this.classList.add('button-primary');
                                this.style.background = '#00a32a';
                                this.style.borderColor = '#008a20';
                                this.textContent = '✔️ Presente';
                                // Atualizar painel local (+1)
                                document.getElementById('contador-presentes').innerText = parseInt(document.getElementById('contador-presentes').innerText) + 1;
                            } else {
                                this.classList.remove('button-primary');
                                this.style.background = '';
                                this.style.borderColor = '';
                                this.textContent = 'Dar Check-in';
                                // Atualizar painel local (-1)
                                document.getElementById('contador-presentes').innerText = parseInt(document.getElementById('contador-presentes').innerText) - 1;
                            }
                        } else {
                            alert(data.data || 'Erro ao comunicar com o servidor.');
                            this.textContent = atual ? '✔️ Presente' : 'Dar Check-in';
                        }
                    } catch (e) {
                        alert('Erro fatal. Verifique a internet.');
                        this.textContent = atual ? '✔️ Presente' : 'Dar Check-in';
                    } finally {
                        this.disabled = false;
                    }
                });
            });
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>
