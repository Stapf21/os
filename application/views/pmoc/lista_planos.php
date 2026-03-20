<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-cogs"></i></span>
        <h5>PMOC e Plano Mensal</h5>
    </div>

    <div class="span12" style="margin-left: 0">
        <div class="span3">
            <a href="<?php echo base_url(); ?>pmoc/novo" class="button btn btn-mini btn-success" style="max-width: 220px">
                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Novo Contrato PMOC</span>
            </a>
        </div>
    </div>

    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content" style="padding: 10px 12px; border-bottom: 1px solid #dfe5f0;">
            <form method="get" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin:0;">
                <input type="text" name="q" value="<?= htmlspecialchars((string) ($filtros['q'] ?? '')) ?>" placeholder="Buscar por cliente ou plano" style="min-width:260px;">
                <select name="status" style="width:180px;">
                    <option value="">Todos os status</option>
                    <option value="ativo" <?= (($filtros['status'] ?? '') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                    <option value="suspenso" <?= (($filtros['status'] ?? '') === 'suspenso') ? 'selected' : '' ?>>Suspenso</option>
                    <option value="inativo" <?= (($filtros['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                </select>
                <button class="btn btn-primary btn-small">Filtrar</button>
                <a href="<?= base_url('pmoc') ?>" class="btn btn-small">Limpar</a>
            </form>
        </div>
        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Plano</th>
                            <th>Valor Mensal</th>
                            <th>Frequencia</th>
                            <th>Unidades</th>
                            <th>Equip.</th>
                            <th>Reparos Abertos</th>
                            <th>Status</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($planos)): ?>
                            <tr><td colspan="10">Nenhum plano cadastrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($planos as $i => $p): ?>
                                <?php
                                    $status = strtolower((string) ($p->status_contrato ?: $p->status));
                                    $cor = '#808080';
                                    if ($status === 'ativo') {
                                        $cor = '#28a745';
                                    } elseif ($status === 'suspenso') {
                                        $cor = '#f39c12';
                                    } elseif ($status === 'inativo') {
                                        $cor = '#c0392b';
                                    }
                                ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars((string) $p->nomeCliente) ?></td>
                                    <td><?= htmlspecialchars((string) ($p->nome_plano ?: 'Plano PMOC')) ?></td>
                                    <td>R$ <?= number_format((float) $p->valor_mensal, 2, ',', '.') ?></td>
                                    <td><?= ucfirst((string) $p->frequencia_manutencao) ?></td>
                                    <td><?= (int) $p->total_unidades ?></td>
                                    <td><?= (int) $p->total_equipamentos ?></td>
                                    <td><?= (int) $p->total_reparos_abertos ?></td>
                                    <td><span class="badge" style="background-color: <?= $cor ?>; border-color: <?= $cor ?>"><?= ucfirst($status ?: 'ativo') ?></span></td>
                                    <td>
                                        <a href="<?= base_url('pmoc/editar/' . $p->id_pmoc) ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                                        <a href="<?= base_url('pmoc/plano/' . $p->id_pmoc) ?>" class="btn-nwe3" title="Painel"><i class="bx bx-bar-chart"></i></a>
                                        <a href="<?= base_url('pmoc/relatorio/' . $p->id_pmoc) ?>" class="btn-nwe6" title="Relatorio" target="_blank"><i class="bx bx-printer"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
