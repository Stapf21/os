<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />

<?php
$receita = (float) ($pnlResumo->receitas ?? 0);
$custo = (float) (($pnlResumo->custos_financeiros ?? 0) + ($pnlResumo->custos_diretos ?? 0));
$lucro = $receita - $custo;
$margem = $receita > 0 ? ($lucro / $receita) * 100 : 0;

$statusContrato = mb_strtolower((string) ($plano->status_contrato ?: $plano->status));
$statusContratoLabel = ucfirst($statusContrato ?: 'ativo');
$statusContratoClass = 'pmoc-tag-neutral';
if ($statusContrato === 'ativo') {
    $statusContratoClass = 'pmoc-tag-success';
} elseif ($statusContrato === 'suspenso') {
    $statusContratoClass = 'pmoc-tag-warning';
} elseif ($statusContrato === 'inativo') {
    $statusContratoClass = 'pmoc-tag-danger';
}

$totalReparosAbertos = 0;
foreach (($reparos ?? []) as $rep) {
    if (mb_strtolower((string) ($rep->status ?? '')) === 'aberto') {
        $totalReparosAbertos++;
    }
}

$statusLabels = [
    'pendente' => 'Pendente',
    'agendado' => 'Agendado',
    'em_execucao' => 'Em execucao',
    'concluido' => 'Concluido',
    'atrasado' => 'Atrasado',
];
?>

<style>
.pmoc-dash {
    --pmoc-bg-soft: #f4f8fc;
    --pmoc-line: #dce4ee;
    --pmoc-text: #243142;
    --pmoc-muted: #5f6f82;
    --pmoc-brand: #0f7ea8;
    --pmoc-brand-soft: #e7f4fb;
    --pmoc-danger-soft: #fdeaea;
    --pmoc-success-soft: #e8f6ef;
    --pmoc-warning-soft: #fff4df;
}
.pmoc-dash .pmoc-header {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding: 14px 16px;
}
.pmoc-dash .pmoc-title-wrap h3 {
    margin: 0;
    font-size: 26px;
    line-height: 1.2;
    color: var(--pmoc-text);
}
.pmoc-dash .pmoc-title-wrap p {
    margin: 4px 0 0;
    color: var(--pmoc-muted);
}
.pmoc-dash .pmoc-top-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.pmoc-dash .pmoc-chip-row {
    display: grid;
    grid-template-columns: repeat(5, minmax(140px, 1fr));
    gap: 8px;
    margin-top: 10px;
}
.pmoc-dash .pmoc-chip {
    background: var(--pmoc-bg-soft);
    border: 1px solid var(--pmoc-line);
    border-radius: 8px;
    padding: 8px 10px;
    color: var(--pmoc-muted);
    font-size: 12px;
}
.pmoc-dash .pmoc-chip b {
    display: block;
    color: var(--pmoc-text);
    font-size: 14px;
    margin-top: 3px;
}
.pmoc-dash .pmoc-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(130px, 1fr));
    gap: 10px;
    margin-top: 12px;
}
.pmoc-dash .pmoc-kpi {
    border: 1px solid var(--pmoc-line);
    border-radius: 10px;
    padding: 10px 12px;
    background: #fff;
}
.pmoc-dash .pmoc-kpi .pmoc-kpi-label {
    color: var(--pmoc-muted);
    font-size: 12px;
}
.pmoc-dash .pmoc-kpi .pmoc-kpi-value {
    margin-top: 4px;
    font-size: 24px;
    font-weight: 700;
    color: var(--pmoc-text);
}
.pmoc-dash .pmoc-kpi.pmoc-kpi-danger {
    background: var(--pmoc-danger-soft);
}
.pmoc-dash .pmoc-kpi.pmoc-kpi-success {
    background: var(--pmoc-success-soft);
}
.pmoc-dash .pmoc-kpi.pmoc-kpi-warning {
    background: var(--pmoc-warning-soft);
}
.pmoc-dash .pmoc-tag {
    display: inline-block;
    border-radius: 999px;
    padding: 3px 9px;
    font-size: 12px;
    line-height: 1.3;
    border: 1px solid transparent;
    font-weight: 600;
}
.pmoc-dash .pmoc-tag-success { background: #e7f7ef; color: #1f7f4f; border-color: #bee8cf; }
.pmoc-dash .pmoc-tag-warning { background: #fff4df; color: #a56a00; border-color: #f5d9a0; }
.pmoc-dash .pmoc-tag-danger { background: #fdeaea; color: #a13131; border-color: #f4b8b8; }
.pmoc-dash .pmoc-tag-neutral { background: #edf2f7; color: #44586f; border-color: #d7e0ea; }
.pmoc-dash .pmoc-section-title {
    margin: 0;
    font-size: 20px;
    color: var(--pmoc-text);
}
.pmoc-dash .pmoc-section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.pmoc-dash .pmoc-inline-form {
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.pmoc-dash .pmoc-inline-form input,
.pmoc-dash .pmoc-inline-form select {
    margin: 0;
}
.pmoc-dash .pmoc-inline-form .pmoc-input-grow {
    min-width: 220px;
    flex: 1;
}
.pmoc-dash .pmoc-table-wrap {
    overflow-x: auto;
}
.pmoc-dash .pmoc-money-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}
.pmoc-dash .pmoc-money-card {
    border: 1px solid var(--pmoc-line);
    border-radius: 8px;
    background: #fff;
    padding: 10px;
}
.pmoc-dash .pmoc-money-card .label {
    color: var(--pmoc-muted);
    font-size: 12px;
    margin: 0;
}
.pmoc-dash .pmoc-money-card .value {
    margin: 3px 0 0;
    font-size: 20px;
    font-weight: 700;
    color: var(--pmoc-text);
}
.pmoc-dash .pmoc-empty {
    color: var(--pmoc-muted);
}
@media (max-width: 1280px) {
    .pmoc-dash .pmoc-chip-row,
    .pmoc-dash .pmoc-kpi-grid {
        grid-template-columns: repeat(3, minmax(130px, 1fr));
    }
}
@media (max-width: 860px) {
    .pmoc-dash .pmoc-chip-row,
    .pmoc-dash .pmoc-kpi-grid,
    .pmoc-dash .pmoc-money-grid {
        grid-template-columns: repeat(2, minmax(130px, 1fr));
    }
}
@media (max-width: 560px) {
    .pmoc-dash .pmoc-chip-row,
    .pmoc-dash .pmoc-kpi-grid,
    .pmoc-dash .pmoc-money-grid {
        grid-template-columns: 1fr;
    }
    .pmoc-dash .pmoc-title-wrap h3 {
        font-size: 22px;
    }
}
</style>

<div class="new122 pmoc-dash">
    <div class="widget-box" style="margin-top: 0;">
        <div class="pmoc-header">
            <div class="pmoc-title-wrap">
                <h3>Painel do Contrato PMOC e Plano Mensal</h3>
                <p>Visao tecnica, operacional e financeira do contrato recorrente.</p>
            </div>
            <div class="pmoc-top-actions">
                <a href="<?= base_url('pmoc/editar/' . $plano->id_pmoc) ?>" class="btn btn-small"><i class="bx bx-edit"></i> Editar contrato</a>
                <a href="<?= base_url('pmoc/relatorio/' . $plano->id_pmoc) ?>" target="_blank" class="btn btn-small"><i class="bx bx-printer"></i> Relatorio</a>
                <a href="<?= base_url('equipamentos/novo?cliente_id=' . $plano->clientes_id . '&unidade_id=' . (int) $unidadeId) ?>" class="btn btn-success btn-small"><i class="bx bx-plus"></i> Equipamento</a>
            </div>
        </div>

        <div class="widget-content" style="padding: 0 16px 14px;">
            <div class="pmoc-chip-row">
                <div class="pmoc-chip">Cliente <b><?= htmlspecialchars((string) $plano->nomeCliente) ?></b></div>
                <div class="pmoc-chip">Plano <b><?= htmlspecialchars((string) ($plano->nome_plano ?: 'Plano PMOC')) ?></b></div>
                <div class="pmoc-chip">Valor mensal <b>R$ <?= number_format((float) $plano->valor_mensal, 2, ',', '.') ?></b></div>
                <div class="pmoc-chip">Inicio <b><?= $plano->data_inicio_contrato ? date('d/m/Y', strtotime($plano->data_inicio_contrato)) : '-' ?></b></div>
                <div class="pmoc-chip">Status <b><span class="pmoc-tag <?= $statusContratoClass ?>"><?= $statusContratoLabel ?></span></b></div>
            </div>

            <div class="pmoc-kpi-grid">
                <div class="pmoc-kpi"><div class="pmoc-kpi-label">Pendente</div><div class="pmoc-kpi-value"><?= (int) $resumoOs['pendente'] ?></div></div>
                <div class="pmoc-kpi pmoc-kpi-warning"><div class="pmoc-kpi-label">Agendado</div><div class="pmoc-kpi-value"><?= (int) $resumoOs['agendado'] ?></div></div>
                <div class="pmoc-kpi"><div class="pmoc-kpi-label">Em execucao</div><div class="pmoc-kpi-value"><?= (int) $resumoOs['em_execucao'] ?></div></div>
                <div class="pmoc-kpi pmoc-kpi-success"><div class="pmoc-kpi-label">Concluido</div><div class="pmoc-kpi-value"><?= (int) $resumoOs['concluido'] ?></div></div>
                <div class="pmoc-kpi pmoc-kpi-danger"><div class="pmoc-kpi-label">Atrasado</div><div class="pmoc-kpi-value"><?= (int) $resumoOs['atrasado'] ?></div></div>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Unidades do cliente</h4>
                <span class="pmoc-tag pmoc-tag-neutral">Total: <?= count($unidades ?? []) ?></span>
            </div>
            <form action="<?= base_url('pmoc/adicionar_unidade/' . $plano->id_pmoc) ?>" method="post" class="pmoc-inline-form" style="margin-bottom:12px;">
                <input type="text" name="nome" placeholder="Nome da unidade" required>
                <input type="text" name="empresa" placeholder="Empresa/Filial">
                <input type="text" name="codigo" placeholder="Codigo" style="width:120px;">
                <input type="text" name="cidade" placeholder="Cidade" style="width:180px;">
                <input type="text" name="estado" placeholder="UF" maxlength="2" style="width:70px; text-transform:uppercase;">
                <label style="margin:0;"><input type="checkbox" name="ativa" value="1" checked> Ativa</label>
                <button class="btn btn-success">Adicionar unidade</button>
            </form>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Nome</th><th>Empresa</th><th>Cidade</th><th>UF</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (empty($unidades)): ?>
                            <tr><td colspan="5" class="pmoc-empty">Nenhuma unidade cadastrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($unidades as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) $u->nome) ?></td>
                                    <td><?= htmlspecialchars((string) $u->empresa) ?></td>
                                    <td><?= htmlspecialchars((string) $u->cidade) ?></td>
                                    <td><?= htmlspecialchars((string) $u->estado) ?></td>
                                    <td>
                                        <?php if ((int) $u->ativa === 1): ?>
                                            <span class="pmoc-tag pmoc-tag-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="pmoc-tag pmoc-tag-neutral">Inativa</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom: 10px;">
                <h4 class="pmoc-section-title">Cronograma de manutencao (12 ciclos)</h4>
                <div class="pmoc-top-actions" style="gap:6px;">
                    <a href="<?= base_url('pmoc/criar_os_pmoc/' . $plano->id_pmoc) ?>" class="btn btn-primary btn-small">Criar nova OS PMOC</a>
                </div>
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Data prevista</th><th>Status</th><th>OS</th><th>Data conclusao</th><th>Acao</th></tr></thead>
                    <tbody>
                        <?php foreach ($cronograma as $item): ?>
                            <?php
                                $statusKey = (string) $item->status;
                                $statusLabel = $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
                                $statusClass = 'pmoc-tag-neutral';
                                if ($statusKey === 'concluido') {
                                    $statusClass = 'pmoc-tag-success';
                                } elseif ($statusKey === 'atrasado') {
                                    $statusClass = 'pmoc-tag-danger';
                                } elseif ($statusKey === 'agendado') {
                                    $statusClass = 'pmoc-tag-warning';
                                }
                            ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($item->data_prevista)) ?></td>
                                <td><span class="pmoc-tag <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                <td>
                                    <?php if ($item->os_pmoc_id): ?>
                                        <a href="<?= base_url('pmoc/os_pmoc/' . $item->os_pmoc_id) ?>">#<?= (int) $item->os_pmoc_id ?></a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= $item->data_execucao ? date('d/m/Y', strtotime($item->data_execucao)) : '-' ?></td>
                                <td>
                                    <?php if (!$item->os_pmoc_id): ?>
                                        <?php
                                            $queryOs = ['data_prevista' => $item->data_prevista, 'status' => 'agendado'];
                                            if ((int) $unidadeId > 0) {
                                                $queryOs['cliente_unidade_id'] = (int) $unidadeId;
                                            }
                                        ?>
                                        <a href="<?= base_url('pmoc/criar_os_pmoc/' . $plano->id_pmoc . '?' . http_build_query($queryOs)) ?>" class="btn btn-small">Criar OS nesta data</a>
                                    <?php else: ?>
                                        <span class="pmoc-empty">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom: 10px;">
                <h4 class="pmoc-section-title">Equipamentos</h4>
                <span class="pmoc-tag pmoc-tag-neutral">Total: <?= count($equipamentos ?? []) ?></span>
            </div>
            <form method="get" class="pmoc-inline-form" style="margin-bottom:10px;">
                <input type="hidden" name="tipo_periodo" value="<?= htmlspecialchars((string) $tipoPeriodo) ?>">
                <input type="hidden" name="periodo_referencia" value="<?= htmlspecialchars((string) $periodoReferencia) ?>">
                <input type="hidden" name="data_inicio" value="<?= htmlspecialchars((string) $dataInicio) ?>">
                <input type="hidden" name="data_fim" value="<?= htmlspecialchars((string) $dataFim) ?>">
                <label style="margin:0;">Unidade</label>
                <select name="unidade_id" style="width:240px;">
                    <option value="">Todas</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= (int) $u->idClienteUnidade ?>" <?= ((int) $unidadeId === (int) $u->idClienteUnidade) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $u->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-small btn-primary">Filtrar</button>
                <a href="<?= base_url('equipamentos/novo?cliente_id=' . $plano->clientes_id . '&unidade_id=' . (int) $unidadeId) ?>" class="btn btn-success btn-small">Adicionar equipamento</a>
            </form>

            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Unidade</th><th>Equipamento</th><th>Tipo</th><th>BTUs</th><th>Local</th><th>Acoes</th></tr></thead>
                    <tbody>
                        <?php if (empty($equipamentos)): ?>
                            <tr><td colspan="6" class="pmoc-empty">Nenhum equipamento cadastrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($equipamentos as $eq): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) ($eq->unidade_nome ?: 'Sem unidade')) ?></td>
                                    <td><?= htmlspecialchars((string) ($eq->descricao ?: $eq->equipamento)) ?></td>
                                    <td><?= htmlspecialchars((string) ($eq->tipo_equipamento ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($eq->btu ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($eq->local_instalacao ?: '-')) ?></td>
                                    <td>
                                        <a href="<?= base_url('pmoc/historico/' . $eq->idEquipamentos) ?>" class="btn-nwe3" title="Historico"><i class="bx bx-search"></i></a>
                                        <a href="<?= base_url('equipamentos/editar/' . $eq->idEquipamentos) ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Relatorios de manutencao</h4>
                <span class="pmoc-tag pmoc-tag-neutral">Total: <?= count($relatoriosPmoc ?? []) ?></span>
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Data</th><th>Unidade</th><th>Equipamento</th><th>Tecnico</th><th>Servico</th><th>OS</th></tr></thead>
                    <tbody>
                        <?php if (empty($relatoriosPmoc)): ?>
                            <tr><td colspan="6" class="pmoc-empty">Nenhum relatorio registrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($relatoriosPmoc as $r): ?>
                                <tr>
                                    <td><?= $r->data_verificacao ? date('d/m/Y H:i', strtotime($r->data_verificacao)) : '-' ?></td>
                                    <td><?= htmlspecialchars((string) ($r->unidade_nome ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->equipamento_descricao ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->tecnico_responsavel ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->tipo_servico ?: '-')) ?></td>
                                    <td><a href="<?= base_url('pmoc/os_pmoc/' . $r->os_pmoc_id) ?>">#<?= (int) $r->os_pmoc_id ?></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Solicitacoes de reparo</h4>
                <span class="pmoc-tag pmoc-tag-warning">Abertos: <?= (int) $totalReparosAbertos ?></span>
            </div>
            <form action="<?= base_url('pmoc/solicitar_reparo/' . $plano->id_pmoc) ?>" method="post" class="pmoc-inline-form" style="margin-bottom:10px;">
                <input type="hidden" name="redirect" value="<?= current_url() ?>">
                <input type="hidden" name="origem" value="interno">
                <input type="text" name="titulo" placeholder="Titulo" required style="min-width:220px;">
                <select name="cliente_unidade_id" style="width:220px;">
                    <option value="">Unidade</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= (int) $u->idClienteUnidade ?>" <?= ((int) $unidadeId === (int) $u->idClienteUnidade) ? 'selected' : '' ?>><?= htmlspecialchars((string) $u->nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="equipamento_id" style="width:240px;">
                    <option value="">Equipamento</option>
                    <?php foreach ($equipamentos as $eq): ?>
                        <option value="<?= (int) $eq->idEquipamentos ?>"><?= htmlspecialchars((string) ($eq->descricao ?: $eq->equipamento)) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="descricao" class="pmoc-input-grow" placeholder="Descricao da solicitacao">
                <button type="submit" class="btn btn-primary">Abrir solicitacao</button>
            </form>

            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Data</th><th>Titulo</th><th>Unidade</th><th>Equipamento</th><th>Origem</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (empty($reparos)): ?>
                            <tr><td colspan="6" class="pmoc-empty">Sem solicitacoes registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reparos as $rep): ?>
                                <?php
                                    $repStatus = mb_strtolower((string) ($rep->status ?: ''));
                                    $repClass = 'pmoc-tag-neutral';
                                    if ($repStatus === 'aberto') {
                                        $repClass = 'pmoc-tag-warning';
                                    } elseif ($repStatus === 'concluido') {
                                        $repClass = 'pmoc-tag-success';
                                    } elseif ($repStatus === 'cancelado') {
                                        $repClass = 'pmoc-tag-danger';
                                    }
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($rep->data_solicitacao)) ?></td>
                                    <td><?= htmlspecialchars((string) $rep->titulo) ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->unidade_nome ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->equipamento_descricao ?: '-')) ?></td>
                                    <td><?= ucfirst((string) $rep->origem) ?></td>
                                    <td><span class="pmoc-tag <?= $repClass ?>"><?= ucfirst((string) ($rep->status ?: '-')) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Painel financeiro do contrato (P&L)</h4>
            </div>

            <form method="get" class="pmoc-inline-form">
                <input type="hidden" name="unidade_id" value="<?= htmlspecialchars((string) $unidadeId) ?>">
                <label style="margin:0;">Periodo</label>
                <select name="tipo_periodo" style="width:140px;">
                    <option value="mensal" <?= $tipoPeriodo === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                    <option value="trimestral" <?= $tipoPeriodo === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="anual" <?= $tipoPeriodo === 'anual' ? 'selected' : '' ?>>Anual</option>
                    <option value="personalizado" <?= $tipoPeriodo === 'personalizado' ? 'selected' : '' ?>>Personalizado</option>
                </select>
                <input type="text" name="periodo_referencia" value="<?= htmlspecialchars((string) $periodoReferencia) ?>" placeholder="YYYY-MM / YYYY / YYYY-QX" style="width:170px;">
                <input type="date" name="data_inicio" value="<?= htmlspecialchars((string) $dataInicio) ?>">
                <input type="date" name="data_fim" value="<?= htmlspecialchars((string) $dataFim) ?>">
                <button class="btn btn-small btn-primary">Filtrar</button>
            </form>

            <div class="pmoc-money-grid">
                <div class="pmoc-money-card">
                    <p class="label">Receita total</p>
                    <p class="value">R$ <?= number_format($receita, 2, ',', '.') ?></p>
                </div>
                <div class="pmoc-money-card">
                    <p class="label">Custo total</p>
                    <p class="value">R$ <?= number_format($custo, 2, ',', '.') ?></p>
                </div>
                <div class="pmoc-money-card">
                    <p class="label">Lucro</p>
                    <p class="value">R$ <?= number_format($lucro, 2, ',', '.') ?></p>
                </div>
                <div class="pmoc-money-card">
                    <p class="label">Margem</p>
                    <p class="value"><?= number_format($margem, 2, ',', '.') ?>%</p>
                </div>
            </div>

            <?php if (!empty($resumoUnidadesPnl)): ?>
                <hr>
                <h5 style="margin-top:0;">Detalhado por unidade</h5>
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
