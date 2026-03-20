<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$receita = (float) ($pnlResumo->receitas ?? 0);
$custo = (float) (($pnlResumo->custos_financeiros ?? 0) + ($pnlResumo->custos_diretos ?? 0));
$lucro = $receita - $custo;
$margem = $receita > 0 ? ($lucro / $receita) * 100 : 0;

$statusContrato = mb_strtolower((string) ($plano->status_contrato ?: $plano->status));
$statusContratoClass = 'pmoc-cl-tag-neutral';
if ($statusContrato === 'ativo') {
    $statusContratoClass = 'pmoc-cl-tag-success';
} elseif ($statusContrato === 'suspenso') {
    $statusContratoClass = 'pmoc-cl-tag-warning';
} elseif ($statusContrato === 'inativo') {
    $statusContratoClass = 'pmoc-cl-tag-danger';
}
?>

<style>
.pmoc-cl {
    --line: #dce4ee;
    --soft: #f4f8fc;
    --text: #243142;
    --muted: #5f6f82;
}
.pmoc-cl .pmoc-cl-head {
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 14px;
    background: #fff;
    margin-bottom: 12px;
}
.pmoc-cl .pmoc-cl-head h3 {
    margin: 0;
    color: var(--text);
}
.pmoc-cl .pmoc-cl-head p {
    margin: 3px 0 0;
    color: var(--muted);
}
.pmoc-cl .pmoc-cl-info {
    margin-top: 10px;
    display: grid;
    grid-template-columns: repeat(4, minmax(140px, 1fr));
    gap: 8px;
}
.pmoc-cl .pmoc-cl-chip {
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--soft);
    padding: 8px;
    font-size: 12px;
    color: var(--muted);
}
.pmoc-cl .pmoc-cl-chip b { color: var(--text); display: block; margin-top: 2px; font-size: 14px; }
.pmoc-cl .pmoc-cl-kpi {
    display: grid;
    grid-template-columns: repeat(5, minmax(110px, 1fr));
    gap: 8px;
    margin-bottom: 12px;
}
.pmoc-cl .pmoc-cl-kpi > div {
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 10px;
    background: #fff;
}
.pmoc-cl .pmoc-cl-kpi .n {
    font-size: 24px;
    font-weight: 700;
    color: var(--text);
    margin-top: 3px;
}
.pmoc-cl .pmoc-cl-tag {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid transparent;
}
.pmoc-cl .pmoc-cl-tag-success { background:#e7f7ef; color:#1f7f4f; border-color:#bee8cf; }
.pmoc-cl .pmoc-cl-tag-warning { background:#fff4df; color:#a56a00; border-color:#f5d9a0; }
.pmoc-cl .pmoc-cl-tag-danger { background:#fdeaea; color:#a13131; border-color:#f4b8b8; }
.pmoc-cl .pmoc-cl-tag-neutral { background:#edf2f7; color:#44586f; border-color:#d7e0ea; }
.pmoc-cl .pmoc-cl-box {
    border: 1px solid var(--line);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 12px;
}
.pmoc-cl .pmoc-cl-box h4 {
    margin: 0;
    padding: 12px 14px;
    border-bottom: 1px solid var(--line);
    color: var(--text);
}
.pmoc-cl .pmoc-cl-box .body { padding: 12px 14px; }
.pmoc-cl .pmoc-form { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin:0 0 10px; }
.pmoc-cl .pmoc-form input, .pmoc-cl .pmoc-form select { margin:0; }
.pmoc-cl .pmoc-table-wrap { overflow-x:auto; }
@media (max-width: 920px) {
    .pmoc-cl .pmoc-cl-info,
    .pmoc-cl .pmoc-cl-kpi {
        grid-template-columns: repeat(2, minmax(120px, 1fr));
    }
}
@media (max-width: 520px) {
    .pmoc-cl .pmoc-cl-info,
    .pmoc-cl .pmoc-cl-kpi {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="pmoc-cl">
    <div class="pmoc-cl-head">
        <h3>PMOC e Plano Mensal</h3>
        <p>Acompanhamento do seu contrato de manutencao.</p>
        <div class="pmoc-cl-info">
            <div class="pmoc-cl-chip">Plano <b><?= htmlspecialchars((string) ($plano->nome_plano ?: 'Plano PMOC')) ?></b></div>
            <div class="pmoc-cl-chip">Valor mensal <b>R$ <?= number_format((float) $plano->valor_mensal, 2, ',', '.') ?></b></div>
            <div class="pmoc-cl-chip">Frequencia <b><?= ucfirst((string) ($plano->frequencia_manutencao ?: '-')) ?></b></div>
            <div class="pmoc-cl-chip">Status <b><span class="pmoc-cl-tag <?= $statusContratoClass ?>"><?= ucfirst((string) ($statusContrato ?: '-')) ?></span></b></div>
        </div>
    </div>

    <div class="pmoc-cl-kpi">
        <div><div>Pendente</div><div class="n"><?= (int) ($resumo_os['pendente'] ?? 0) ?></div></div>
        <div><div>Agendado</div><div class="n"><?= (int) ($resumo_os['agendado'] ?? 0) ?></div></div>
        <div><div>Em execucao</div><div class="n"><?= (int) ($resumo_os['em_execucao'] ?? 0) ?></div></div>
        <div><div>Concluido</div><div class="n"><?= (int) ($resumo_os['concluido'] ?? 0) ?></div></div>
        <div><div>Atrasado</div><div class="n"><?= (int) ($resumo_os['atrasado'] ?? 0) ?></div></div>
    </div>

    <div class="pmoc-cl-box">
        <h4>Solicitar reparo</h4>
        <div class="body">
            <form action="<?= base_url('index.php/mine/solicitarReparoPmoc') ?>" method="post" class="pmoc-form">
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
                <input type="text" name="descricao" placeholder="Descricao da necessidade" style="min-width:260px; flex:1;">
                <button class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>

    <div class="pmoc-cl-box">
        <h4>Cronograma</h4>
        <div class="body">
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Data prevista</th><th>Status</th><th>Execucao</th></tr></thead>
                    <tbody>
                        <?php foreach ($cronograma as $item): ?>
                            <?php
                                $s = mb_strtolower((string) $item->status);
                                $sClass = 'pmoc-cl-tag-neutral';
                                if ($s === 'concluido') {
                                    $sClass = 'pmoc-cl-tag-success';
                                } elseif ($s === 'atrasado') {
                                    $sClass = 'pmoc-cl-tag-danger';
                                } elseif ($s === 'agendado') {
                                    $sClass = 'pmoc-cl-tag-warning';
                                }
                            ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($item->data_prevista)) ?></td>
                                <td><span class="pmoc-cl-tag <?= $sClass ?>"><?= ucfirst(str_replace('_', ' ', (string) $item->status)) ?></span></td>
                                <td><?= $item->data_execucao ? date('d/m/Y', strtotime($item->data_execucao)) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pmoc-cl-box">
        <h4>Relatorios</h4>
        <div class="body">
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Data</th><th>Unidade</th><th>Equipamento</th><th>Tecnico</th><th>Servico</th></tr></thead>
                    <tbody>
                        <?php if (empty($relatorios)): ?>
                            <tr><td colspan="5">Nenhum relatorio.</td></tr>
                        <?php else: ?>
                            <?php foreach ($relatorios as $r): ?>
                                <tr>
                                    <td><?= $r->data_verificacao ? date('d/m/Y H:i', strtotime($r->data_verificacao)) : '-' ?></td>
                                    <td><?= htmlspecialchars((string) ($r->unidade_nome ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->equipamento_descricao ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->tecnico_responsavel ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->tipo_servico ?: '-')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pmoc-cl-box">
        <h4>Painel financeiro (P&L)</h4>
        <div class="body">
            <form method="get" class="pmoc-form" style="margin-bottom:8px;">
                <select name="tipo_periodo" style="width:140px;">
                    <option value="mensal" <?= $tipoPeriodo === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                    <option value="trimestral" <?= $tipoPeriodo === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="anual" <?= $tipoPeriodo === 'anual' ? 'selected' : '' ?>>Anual</option>
                    <option value="personalizado" <?= $tipoPeriodo === 'personalizado' ? 'selected' : '' ?>>Personalizado</option>
                </select>
                <input type="text" name="periodo_referencia" value="<?= htmlspecialchars((string) $periodoReferencia) ?>" placeholder="YYYY-MM / YYYY / YYYY-QX">
                <input type="date" name="data_inicio" value="<?= htmlspecialchars((string) $dataInicio) ?>">
                <input type="date" name="data_fim" value="<?= htmlspecialchars((string) $dataFim) ?>">
                <button class="btn btn-small btn-primary">Filtrar</button>
            </form>
            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                <span><b>Receita:</b> R$ <?= number_format($receita, 2, ',', '.') ?></span>
                <span><b>Custo:</b> R$ <?= number_format($custo, 2, ',', '.') ?></span>
                <span><b>Lucro:</b> R$ <?= number_format($lucro, 2, ',', '.') ?></span>
                <span><b>Margem:</b> <?= number_format($margem, 2, ',', '.') ?>%</span>
            </div>

            <?php if (!empty($resumoUnidadesPnl)): ?>
                <hr>
                <h5>Detalhado por unidade</h5>
                <div class="pmoc-table-wrap">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Unidade</th><th>Receita</th><th>Custos</th><th>Lucro</th></tr></thead>
                        <tbody>
                        <?php foreach ($resumoUnidadesPnl as $ru): ?>
                            <?php $lucroUn = ((float) $ru->receitas) - ((float) $ru->custos_financeiros + (float) $ru->custos_diretos); ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $ru->nome) ?></td>
                                <td>R$ <?= number_format((float) $ru->receitas, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float) $ru->custos_financeiros + (float) $ru->custos_diretos, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($lucroUn, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
