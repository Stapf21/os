<div class="widget-box" style="margin-top: 0;">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-clipboard-list"></i></span>
        <h5>PMOC e Plano Mensal</h5>
    </div>
    <div class="widget-content" style="padding: 14px;">
        <div style="display:flex; gap:18px; flex-wrap:wrap; margin-bottom:12px;">
            <span><b>Plano:</b> <?= htmlspecialchars((string) ($plano->nome_plano ?: 'Plano PMOC')) ?></span>
            <span><b>Valor mensal:</b> R$ <?= number_format((float) $plano->valor_mensal, 2, ',', '.') ?></span>
            <span><b>Status:</b> <?= ucfirst((string) ($plano->status_contrato ?: $plano->status)) ?></span>
            <span><b>Frequencia:</b> <?= ucfirst((string) $plano->frequencia_manutencao) ?></span>
        </div>

        <div class="row-fluid" style="margin-bottom: 10px;">
            <div class="span3"><div class="well"><b>Pendente:</b> <?= (int) $resumo_os['pendente'] ?></div></div>
            <div class="span2"><div class="well"><b>Agendado:</b> <?= (int) $resumo_os['agendado'] ?></div></div>
            <div class="span2"><div class="well"><b>Em execucao:</b> <?= (int) $resumo_os['em_execucao'] ?></div></div>
            <div class="span2"><div class="well"><b>Concluido:</b> <?= (int) $resumo_os['concluido'] ?></div></div>
            <div class="span3"><div class="well"><b>Atrasado:</b> <?= (int) $resumo_os['atrasado'] ?></div></div>
        </div>

        <h5>Solicitar reparo</h5>
        <form action="<?= base_url('index.php/mine/solicitarReparoPmoc') ?>" method="post" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
            <input type="text" name="titulo" placeholder="Titulo" required>
            <select name="cliente_unidade_id">
                <option value="">Unidade</option>
                <?php foreach ($unidades as $u): ?>
                    <option value="<?= (int) $u->idClienteUnidade ?>"><?= htmlspecialchars((string) $u->nome) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="equipamento_id">
                <option value="">Equipamento</option>
                <?php foreach ($equipamentos as $eq): ?>
                    <option value="<?= (int) $eq->idEquipamentos ?>"><?= htmlspecialchars((string) ($eq->descricao ?: $eq->equipamento)) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="descricao" placeholder="Descricao da necessidade" style="min-width:260px;">
            <button class="btn btn-primary">Enviar</button>
        </form>

        <h5>Cronograma</h5>
        <table class="table table-bordered">
            <thead><tr><th>Data prevista</th><th>Status</th><th>Execucao</th></tr></thead>
            <tbody>
                <?php foreach ($cronograma as $item): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($item->data_prevista)) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $item->status)) ?></td>
                        <td><?= $item->data_execucao ? date('d/m/Y', strtotime($item->data_execucao)) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h5>Relatorios</h5>
        <table class="table table-bordered">
            <thead><tr><th>Data</th><th>Unidade</th><th>Equipamento</th><th>Tecnico</th><th>Servico</th></tr></thead>
            <tbody>
                <?php if (empty($relatorios)): ?>
                    <tr><td colspan="5">Nenhum relatorio.</td></tr>
                <?php else: ?>
                    <?php foreach ($relatorios as $r): ?>
                        <tr>
                            <td><?= $r->data_verificacao ? date('d/m/Y H:i', strtotime($r->data_verificacao)) : '-' ?></td>
                            <td><?= htmlspecialchars((string) $r->unidade_nome) ?></td>
                            <td><?= htmlspecialchars((string) $r->equipamento_descricao) ?></td>
                            <td><?= htmlspecialchars((string) $r->tecnico_responsavel) ?></td>
                            <td><?= htmlspecialchars((string) $r->tipo_servico) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h5>Painel financeiro (P&L)</h5>
        <?php
            $receita = (float) ($pnlResumo->receitas ?? 0);
            $custo = (float) (($pnlResumo->custos_financeiros ?? 0) + ($pnlResumo->custos_diretos ?? 0));
            $lucro = $receita - $custo;
            $margem = $receita > 0 ? ($lucro / $receita) * 100 : 0;
        ?>
        <form method="get" style="margin-bottom:8px;">
            <label>Periodo (YYYY-MM ou YYYY)</label>
            <input type="text" name="periodo" value="<?= htmlspecialchars((string) $periodo) ?>">
            <button class="btn btn-small">Filtrar</button>
        </form>
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <span><b>Receita:</b> R$ <?= number_format($receita, 2, ',', '.') ?></span>
            <span><b>Custo:</b> R$ <?= number_format($custo, 2, ',', '.') ?></span>
            <span><b>Lucro:</b> R$ <?= number_format($lucro, 2, ',', '.') ?></span>
            <span><b>Margem:</b> <?= number_format($margem, 2, ',', '.') ?>%</span>
        </div>
    </div>
</div>