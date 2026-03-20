<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />

<?php
$receita = (float) ($pnlResumo->receitas ?? 0);
$custo = (float) (($pnlResumo->custos_financeiros ?? 0) + ($pnlResumo->custos_diretos ?? 0));
$lucro = $receita - $custo;
$margem = $receita > 0 ? ($lucro / $receita) * 100 : 0;
?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-cogs"></i></span>
        <h5>Painel do Contrato PMOC e Plano Mensal</h5>
    </div>

    <div class="widget-box" style="margin-top: 10px;">
        <div class="widget-content" style="padding: 14px 18px;">
            <div style="display:flex; flex-wrap:wrap; gap:24px;">
                <span><b>Cliente:</b> <?= htmlspecialchars((string) $plano->nomeCliente) ?></span>
                <span><b>Plano:</b> <?= htmlspecialchars((string) ($plano->nome_plano ?: 'Plano PMOC')) ?></span>
                <span><b>Valor mensal:</b> R$ <?= number_format((float) $plano->valor_mensal, 2, ',', '.') ?></span>
                <span><b>Inicio:</b> <?= $plano->data_inicio_contrato ? date('d/m/Y', strtotime($plano->data_inicio_contrato)) : '-' ?></span>
                <span><b>Status:</b> <?= ucfirst((string) ($plano->status_contrato ?: $plano->status)) ?></span>
            </div>
        </div>
    </div>

    <div class="row-fluid" style="margin-top:12px;">
        <div class="span3"><div class="well"><b>Pendente:</b> <?= (int) $resumoOs['pendente'] ?></div></div>
        <div class="span2"><div class="well"><b>Agendado:</b> <?= (int) $resumoOs['agendado'] ?></div></div>
        <div class="span2"><div class="well"><b>Em execucao:</b> <?= (int) $resumoOs['em_execucao'] ?></div></div>
        <div class="span2"><div class="well"><b>Concluido:</b> <?= (int) $resumoOs['concluido'] ?></div></div>
        <div class="span3"><div class="well"><b>Atrasado:</b> <?= (int) $resumoOs['atrasado'] ?></div></div>
    </div>

    <div class="widget-box">
        <div class="widget-title"><h5>Unidades do cliente</h5></div>
        <div class="widget-content" style="padding:10px;">
            <form action="<?= base_url('pmoc/adicionar_unidade/' . $plano->id_pmoc) ?>" method="post" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px; align-items:center;">
                <input type="text" name="nome" placeholder="Nome da unidade" required>
                <input type="text" name="empresa" placeholder="Empresa/Filial">
                <input type="text" name="codigo" placeholder="Codigo">
                <input type="text" name="cidade" placeholder="Cidade">
                <input type="text" name="estado" placeholder="UF" style="width:70px;">
                <label style="margin:0;"><input type="checkbox" name="ativa" value="1" checked> Ativa</label>
                <button class="btn btn-success">Adicionar unidade</button>
            </form>

            <table class="table table-bordered">
                <thead><tr><th>Nome</th><th>Empresa</th><th>Cidade</th><th>UF</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if (empty($unidades)): ?>
                        <tr><td colspan="5">Nenhuma unidade cadastrada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($unidades as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $u->nome) ?></td>
                                <td><?= htmlspecialchars((string) $u->empresa) ?></td>
                                <td><?= htmlspecialchars((string) $u->cidade) ?></td>
                                <td><?= htmlspecialchars((string) $u->estado) ?></td>
                                <td><?= ((int) $u->ativa === 1) ? 'Ativa' : 'Inativa' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"><h5>Cronograma de manutencao (12 ciclos)</h5></div>
        <div class="widget-content nopadding">
            <table class="table table-bordered">
                <thead><tr><th>Data prevista</th><th>Status</th><th>OS</th><th>Data conclusao</th></tr></thead>
                <tbody>
                    <?php foreach ($cronograma as $item): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($item->data_prevista)) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $item->status)) ?></td>
                            <td>
                                <?php if ($item->os_pmoc_id): ?>
                                    <a href="<?= base_url('pmoc/os_pmoc/' . $item->os_pmoc_id) ?>">#<?= (int) $item->os_pmoc_id ?></a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= $item->data_execucao ? date('d/m/Y', strtotime($item->data_execucao)) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="padding: 0 10px 10px; display:flex; gap:8px; flex-wrap:wrap;">
                <a href="<?= base_url('pmoc/criar_os_pmoc/' . $plano->id_pmoc) ?>" class="btn btn-primary">Criar nova OS PMOC</a>
                <a href="<?= base_url('equipamentos/novo?cliente_id=' . $plano->clientes_id . '&unidade_id=' . (int) $unidadeId) ?>" class="btn btn-success">Adicionar equipamento</a>
            </div>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"><h5>Equipamentos (filtro por unidade)</h5></div>
        <div class="widget-content nopadding">
            <div style="padding:10px;">
                <form method="get" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <input type="hidden" name="tipo_periodo" value="<?= htmlspecialchars((string) $tipoPeriodo) ?>">
                    <input type="hidden" name="periodo_referencia" value="<?= htmlspecialchars((string) $periodoReferencia) ?>">
                    <input type="hidden" name="data_inicio" value="<?= htmlspecialchars((string) $dataInicio) ?>">
                    <input type="hidden" name="data_fim" value="<?= htmlspecialchars((string) $dataFim) ?>">
                    <label style="margin:0;">Unidade</label>
                    <select name="unidade_id">
                        <option value="">Todas</option>
                        <?php foreach ($unidades as $u): ?>
                            <option value="<?= (int) $u->idClienteUnidade ?>" <?= ((int) $unidadeId === (int) $u->idClienteUnidade) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $u->nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-small">Filtrar</button>
                </form>
            </div>

            <table class="table table-bordered">
                <thead><tr><th>Unidade</th><th>Equipamento</th><th>Tipo</th><th>BTUs</th><th>Local</th><th>Acoes</th></tr></thead>
                <tbody>
                    <?php if (empty($equipamentos)): ?>
                        <tr><td colspan="6">Nenhum equipamento cadastrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($equipamentos as $eq): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) ($eq->unidade_nome ?: 'Sem unidade')) ?></td>
                                <td><?= htmlspecialchars((string) ($eq->descricao ?: $eq->equipamento)) ?></td>
                                <td><?= htmlspecialchars((string) $eq->tipo_equipamento) ?></td>
                                <td><?= htmlspecialchars((string) $eq->btu) ?></td>
                                <td><?= htmlspecialchars((string) $eq->local_instalacao) ?></td>
                                <td>
                                    <a href="<?= base_url('pmoc/historico/' . $eq->idEquipamentos) ?>" class="btn-nwe3"><i class="bx bx-search"></i></a>
                                    <a href="<?= base_url('equipamentos/editar/' . $eq->idEquipamentos) ?>" class="btn-nwe3"><i class="bx bx-edit"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"><h5>Relatorios de manutencao</h5></div>
        <div class="widget-content nopadding">
            <table class="table table-bordered">
                <thead><tr><th>Data</th><th>Unidade</th><th>Equipamento</th><th>Tecnico</th><th>Servico</th><th>OS</th></tr></thead>
                <tbody>
                    <?php if (empty($relatoriosPmoc)): ?>
                        <tr><td colspan="6">Nenhum relatorio registrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($relatoriosPmoc as $r): ?>
                            <tr>
                                <td><?= $r->data_verificacao ? date('d/m/Y H:i', strtotime($r->data_verificacao)) : '-' ?></td>
                                <td><?= htmlspecialchars((string) $r->unidade_nome) ?></td>
                                <td><?= htmlspecialchars((string) $r->equipamento_descricao) ?></td>
                                <td><?= htmlspecialchars((string) $r->tecnico_responsavel) ?></td>
                                <td><?= htmlspecialchars((string) $r->tipo_servico) ?></td>
                                <td><a href="<?= base_url('pmoc/os_pmoc/' . $r->os_pmoc_id) ?>">#<?= (int) $r->os_pmoc_id ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"><h5>Solicitacoes de reparo</h5></div>
        <div class="widget-content" style="padding:10px;">
            <form action="<?= base_url('pmoc/solicitar_reparo/' . $plano->id_pmoc) ?>" method="post" class="form-inline" style="margin-bottom:10px; display:flex; gap:8px; flex-wrap:wrap;">
                <input type="hidden" name="redirect" value="<?= current_url() ?>">
                <input type="hidden" name="origem" value="interno">
                <input type="text" name="titulo" class="span3" placeholder="Titulo" required>
                <select name="cliente_unidade_id" class="span3">
                    <option value="">Unidade</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= (int) $u->idClienteUnidade ?>"><?= htmlspecialchars((string) $u->nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="equipamento_id" class="span3">
                    <option value="">Equipamento</option>
                    <?php foreach ($equipamentos as $eq): ?>
                        <option value="<?= (int) $eq->idEquipamentos ?>"><?= htmlspecialchars((string) ($eq->descricao ?: $eq->equipamento)) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="descricao" class="span8" placeholder="Descricao da solicitacao">
                <button type="submit" class="btn btn-primary">Abrir solicitacao</button>
            </form>

            <table class="table table-bordered">
                <thead><tr><th>Data</th><th>Titulo</th><th>Unidade</th><th>Equipamento</th><th>Origem</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if (empty($reparos)): ?>
                        <tr><td colspan="6">Sem solicitacoes registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reparos as $rep): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($rep->data_solicitacao)) ?></td>
                                <td><?= htmlspecialchars((string) $rep->titulo) ?></td>
                                <td><?= htmlspecialchars((string) $rep->unidade_nome) ?></td>
                                <td><?= htmlspecialchars((string) $rep->equipamento_descricao) ?></td>
                                <td><?= ucfirst((string) $rep->origem) ?></td>
                                <td><?= ucfirst((string) $rep->status) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"><h5>Painel financeiro do contrato (P&L)</h5></div>
        <div class="widget-content" style="padding: 12px 16px;">
            <form method="get" style="display:flex; gap:8px; align-items:center; margin-bottom:10px; flex-wrap:wrap;">
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
                <button class="btn btn-small">Filtrar</button>
            </form>

            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div><b>Receita total:</b> R$ <?= number_format($receita, 2, ',', '.') ?></div>
                <div><b>Custo total:</b> R$ <?= number_format($custo, 2, ',', '.') ?></div>
                <div><b>Lucro:</b> R$ <?= number_format($lucro, 2, ',', '.') ?></div>
                <div><b>Margem:</b> <?= number_format($margem, 2, ',', '.') ?>%</div>
            </div>

            <?php if (!empty($resumoUnidadesPnl)): ?>
                <hr>
                <h5>Detalhado por unidade</h5>
                <table class="table table-bordered">
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
            <?php endif; ?>
        </div>
    </div>
</div>
