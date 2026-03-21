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
    if (in_array(mb_strtolower((string) ($rep->status ?? '')), ['aberto', 'em_andamento'], true)) {
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

$paramsBase = [
    'tipo_periodo' => (string) $tipoPeriodo,
    'periodo_referencia' => (string) $periodoReferencia,
    'data_inicio' => (string) $dataInicio,
    'data_fim' => (string) $dataFim,
];
$unidadeSelecionada = null;
if (!empty($unidades) && (int) $unidadeId > 0) {
    foreach ($unidades as $uItem) {
        if ((int) $uItem->idClienteUnidade === (int) $unidadeId) {
            $unidadeSelecionada = $uItem;
            break;
        }
    }
}
?>

<link rel="stylesheet" href="<?= base_url('assets/css/pmoc-dashcode.css?v=' . @filemtime(FCPATH . 'assets/css/pmoc-dashcode.css')) ?>" />

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
            <?php if ($unidadeSelecionada): ?>
                <div class="pmoc-filter-banner">
                    Filtro ativo por unidade: <strong><?= htmlspecialchars((string) $unidadeSelecionada->nome) ?></strong>
                    <a href="<?= base_url('pmoc/plano/' . (int) $plano->id_pmoc . '?' . http_build_query($paramsBase + ['tab' => 'unidades'])) ?>">ver consolidado</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="pmoc-quick-nav" data-tab-nav>
        <button type="button" class="pmoc-nav-btn active" data-tab="unidades">Unidades</button>
        <button type="button" class="pmoc-nav-btn" data-tab="cronograma">Cronograma</button>
        <button type="button" class="pmoc-nav-btn" data-tab="equipamentos">Equipamentos</button>
        <button type="button" class="pmoc-nav-btn" data-tab="relatorios">Relatorios</button>
        <button type="button" class="pmoc-nav-btn" data-tab="reparos">Reparos</button>
        <button type="button" class="pmoc-nav-btn" data-tab="financeiro">P&L</button>
    </div>

    <div class="widget-box pmoc-section pmoc-tab-panel is-active" id="sec-unidades" data-tab-panel="unidades">
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
                <table class="table table-bordered table-striped pmoc-table-hover">
                    <thead><tr><th>Nome</th><th>Empresa</th><th>Cidade</th><th>UF</th><th>Status</th><th>Acoes</th></tr></thead>
                    <tbody>
                        <?php if (empty($unidades)): ?>
                            <tr><td colspan="6" class="pmoc-empty">Nenhuma unidade cadastrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($unidades as $u): ?>
                                <?php
                                    $baseUnidade = $paramsBase + ['unidade_id' => (int) $u->idClienteUnidade];
                                    $urlEquipamentos = base_url('pmoc/plano/' . (int) $plano->id_pmoc . '?' . http_build_query($baseUnidade + ['tab' => 'equipamentos']));
                                    $urlCronograma = base_url('pmoc/plano/' . (int) $plano->id_pmoc . '?' . http_build_query($baseUnidade + ['tab' => 'cronograma']));
                                    $urlRelatorios = base_url('pmoc/plano/' . (int) $plano->id_pmoc . '?' . http_build_query($baseUnidade + ['tab' => 'relatorios']));
                                ?>
                                <tr>
                                    <td><a href="<?= $urlEquipamentos ?>" class="pmoc-link-unit"><?= htmlspecialchars((string) $u->nome) ?></a></td>
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
                                    <td class="pmoc-unit-actions">
                                        <a class="btn btn-small" href="<?= $urlEquipamentos ?>">Equipamentos</a>
                                        <a class="btn btn-small" href="<?= $urlCronograma ?>">Cronograma</a>
                                        <a class="btn btn-small" href="<?= $urlRelatorios ?>">Relatorios</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="widget-box pmoc-section pmoc-tab-panel" id="sec-cronograma" data-tab-panel="cronograma">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom: 10px;">
                <h4 class="pmoc-section-title">Cronograma de manutencao (12 ciclos)</h4>
                <div class="pmoc-top-actions" style="gap:6px;">
                    <a href="<?= base_url('pmoc/nova_os_pmoc/' . (int) $plano->id_pmoc . '?data_prevista=' . date('Y-m-d') . '&unidade_id=' . (int) $unidadeId) ?>" class="btn btn-primary btn-small">Criar nova OS PMOC</a>
                </div>
            </div>
            <div class="pmoc-status-pills" data-status-filter="#tb-cronograma">
                <button type="button" class="pmoc-status-pill active" data-status="">Todos</button>
                <button type="button" class="pmoc-status-pill" data-status="agendado">Agendado</button>
                <button type="button" class="pmoc-status-pill" data-status="pendente">Pendente</button>
                <button type="button" class="pmoc-status-pill" data-status="concluido">Concluido</button>
                <button type="button" class="pmoc-status-pill" data-status="atrasado">Atrasado</button>
            </div>
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar por data, status ou OS..." data-table-search="#tb-cronograma">
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="tb-cronograma">
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
                            <tr data-status="<?= htmlspecialchars((string) $statusKey) ?>">
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
                                        <a href="<?= base_url('pmoc/nova_os_pmoc/' . (int) $plano->id_pmoc . '?data_prevista=' . htmlspecialchars((string) $item->data_prevista) . '&unidade_id=' . (int) $unidadeId) ?>" class="btn btn-small">Criar OS nesta data</a>
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

    <div class="widget-box pmoc-section pmoc-tab-panel" id="sec-equipamentos" data-tab-panel="equipamentos">
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
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar equipamento, tipo, local..." data-table-search="#tb-equipamentos">
            </div>

            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="tb-equipamentos">
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

    <div class="widget-box pmoc-section pmoc-tab-panel" id="sec-relatorios" data-tab-panel="relatorios">
        <div class="widget-content" style="padding: 14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Relatorios de manutencao</h4>
                <span class="pmoc-tag pmoc-tag-neutral">Total: <?= count($relatoriosPmoc ?? []) ?></span>
            </div>
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar por tecnico, servico, equipamento..." data-table-search="#tb-relatorios">
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="tb-relatorios">
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

    <div class="widget-box pmoc-section pmoc-tab-panel" id="sec-reparos" data-tab-panel="reparos">
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
            <div class="pmoc-status-pills" data-status-filter="#tb-reparos">
                <button type="button" class="pmoc-status-pill active" data-status="">Todos</button>
                <button type="button" class="pmoc-status-pill" data-status="aberto">Aberto</button>
                <button type="button" class="pmoc-status-pill" data-status="em_andamento">Em andamento</button>
                <button type="button" class="pmoc-status-pill" data-status="concluido">Concluido</button>
            </div>
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar reparo por titulo, unidade ou equipamento..." data-table-search="#tb-reparos">
            </div>

            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="tb-reparos">
                    <thead><tr><th>Data</th><th>Titulo</th><th>Unidade</th><th>Equipamento</th><th>Origem</th><th>Status</th><th>Acoes</th></tr></thead>
                    <tbody>
                        <?php if (empty($reparos)): ?>
                            <tr><td colspan="7" class="pmoc-empty">Sem solicitacoes registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reparos as $rep): ?>
                                <?php
                                    $repStatus = mb_strtolower((string) ($rep->status ?: ''));
                                    $repClass = 'pmoc-tag-neutral';
                                    if ($repStatus === 'aberto') {
                                        $repClass = 'pmoc-tag-warning';
                                    } elseif ($repStatus === 'em_andamento') {
                                        $repClass = 'pmoc-tag-warning';
                                    } elseif ($repStatus === 'concluido') {
                                        $repClass = 'pmoc-tag-success';
                                    } elseif ($repStatus === 'cancelado') {
                                        $repClass = 'pmoc-tag-danger';
                                    }
                                    $reparoId = (int) ($rep->id ?? $rep->idReparo ?? $rep->id_pmoc_reparo ?? $rep->idPmocReparo ?? 0);
                                ?>
                                <tr data-status="<?= htmlspecialchars((string) $repStatus) ?>">
                                    <td><?= date('d/m/Y H:i', strtotime($rep->data_solicitacao)) ?></td>
                                    <td><?= htmlspecialchars((string) $rep->titulo) ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->unidade_nome ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->equipamento_descricao ?: '-')) ?></td>
                                    <td><?= ucfirst((string) $rep->origem) ?></td>
                                    <td><span class="pmoc-tag <?= $repClass ?>"><?= ucfirst(str_replace('_', ' ', (string) ($rep->status ?: '-'))) ?></span></td>
                                    <td>
                                        <?php if ($reparoId > 0): ?>
                                            <form action="<?= base_url('pmoc/atualizar_status_reparo/' . $plano->id_pmoc . '/' . $reparoId) ?>" method="post" class="pmoc-status-form">
                                                <input type="hidden" name="redirect" value="<?= current_url() ?>">
                                                <select name="status">
                                                    <option value="aberto" <?= $repStatus === 'aberto' ? 'selected' : '' ?>>Aberto</option>
                                                    <option value="em_andamento" <?= $repStatus === 'em_andamento' ? 'selected' : '' ?>>Em andamento</option>
                                                    <option value="concluido" <?= $repStatus === 'concluido' ? 'selected' : '' ?>>Concluido</option>
                                                </select>
                                                <button type="submit" class="btn btn-small">Atualizar</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="pmoc-empty">-</span>
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

    <div class="widget-box pmoc-section pmoc-tab-panel" id="sec-financeiro" data-tab-panel="financeiro">
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

<script>
(function () {
    function norm(v) {
        return (v || '').toString().toLowerCase();
    }

    function abrirAba(tab) {
        if (!tab) return;
        document.querySelectorAll('[data-tab-nav] .pmoc-nav-btn').forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-tab') === tab);
        });
        document.querySelectorAll('.pmoc-tab-panel').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-tab-panel') === tab);
        });
    }

    function tabFromUrl() {
        try {
            var params = new URLSearchParams(window.location.search || '');
            return params.get('tab') || '';
        } catch (e) {
            return '';
        }
    }

    document.querySelectorAll('[data-tab-nav] .pmoc-nav-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-tab');
            abrirAba(tab);
            if (window.history && window.history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url.toString());
            }
        });
    });

    var initialTab = tabFromUrl();
    abrirAba(initialTab || 'unidades');

    document.querySelectorAll('[data-table-search]').forEach(function (input) {
        var table = document.querySelector(input.getAttribute('data-table-search'));
        if (!table) return;
        var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
        input.addEventListener('input', function () {
            var q = norm(input.value);
            rows.forEach(function (tr) {
                var visible = norm(tr.textContent).indexOf(q) > -1;
                tr.style.display = visible ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('[data-status-filter]').forEach(function (wrap) {
        var table = document.querySelector(wrap.getAttribute('data-status-filter'));
        if (!table) return;
        var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr[data-status]'));
        wrap.querySelectorAll('.pmoc-status-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                wrap.querySelectorAll('.pmoc-status-pill').forEach(function (p) { p.classList.remove('active'); });
                pill.classList.add('active');
                var wanted = norm(pill.getAttribute('data-status'));
                rows.forEach(function (tr) {
                    var status = norm(tr.getAttribute('data-status'));
                    var visible = !wanted || status === wanted;
                    tr.style.display = visible ? '' : 'none';
                });
            });
        });
    });

    document.querySelectorAll('.pmoc-kpi-value').forEach(function (node) {
        var raw = parseInt((node.textContent || '0').replace(/\D/g, ''), 10);
        if (!isFinite(raw) || raw <= 0) return;
        var start = 0;
        var steps = 18;
        var cur = 0;
        var timer = setInterval(function () {
            cur++;
            node.textContent = Math.round((raw * cur) / steps);
            if (cur >= steps) clearInterval(timer);
        }, 20);
    });
})();
</script>

