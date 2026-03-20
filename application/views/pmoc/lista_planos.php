<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pmoc-dashcode.css" />

<?php $stats = $statsPmoc ?? []; ?>

<div class="new122 pmoc-list">
    <div class="pmoc-list-header widget-box">
        <div class="pmoc-list-header-top">
            <div>
                <h3>PMOC e Plano Mensal</h3>
                <p>Gestao de contratos recorrentes, execucao tecnica e controle financeiro.</p>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>pmoc/novo" class="button btn btn-mini btn-success pmoc-list-cta">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                    <span class="button__text2">Novo Contrato PMOC</span>
                </a>
            </div>
        </div>

        <div class="pmoc-list-stats">
            <div class="pmoc-list-stat-card">
                <span>Total de contratos</span>
                <strong><?= (int) ($stats['total_planos'] ?? 0) ?></strong>
            </div>
            <div class="pmoc-list-stat-card">
                <span>Ativos</span>
                <strong><?= (int) ($stats['ativos'] ?? 0) ?></strong>
            </div>
            <div class="pmoc-list-stat-card">
                <span>Suspensos</span>
                <strong><?= (int) ($stats['suspensos'] ?? 0) ?></strong>
            </div>
            <div class="pmoc-list-stat-card">
                <span>Inativos</span>
                <strong><?= (int) ($stats['inativos'] ?? 0) ?></strong>
            </div>
            <div class="pmoc-list-stat-card">
                <span>Receita mensal</span>
                <strong>R$ <?= number_format((float) ($stats['receita_mensal'] ?? 0), 2, ',', '.') ?></strong>
            </div>
            <div class="pmoc-list-stat-card">
                <span>Reparos abertos</span>
                <strong><?= (int) ($stats['reparos_abertos'] ?? 0) ?></strong>
            </div>
        </div>
    </div>

    <div class="widget-box" style="margin-top: 10px;">
        <div class="widget-content" style="padding: 12px 14px; border-bottom: 1px solid #dfe5f0;">
            <form method="get" class="pmoc-list-filter" style="margin:0;">
                <input type="text" name="q" value="<?= htmlspecialchars((string) ($filtros['q'] ?? '')) ?>" placeholder="Buscar por cliente ou plano">
                <select name="status">
                    <option value="">Todos os status</option>
                    <option value="ativo" <?= (($filtros['status'] ?? '') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                    <option value="suspenso" <?= (($filtros['status'] ?? '') === 'suspenso') ? 'selected' : '' ?>>Suspenso</option>
                    <option value="inativo" <?= (($filtros['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                </select>
                <button class="btn btn-primary btn-small" type="submit">Filtrar</button>
                <a href="<?= base_url('pmoc') ?>" class="btn btn-small">Limpar</a>
            </form>
        </div>

        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered pmoc-list-table">
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
                                    $status = mb_strtolower((string) ($p->status_contrato ?: $p->status));
                                    $statusClass = 'pmoc-tag-neutral';
                                    if ($status === 'ativo') {
                                        $statusClass = 'pmoc-tag-success';
                                    } elseif ($status === 'suspenso') {
                                        $statusClass = 'pmoc-tag-warning';
                                    } elseif ($status === 'inativo') {
                                        $statusClass = 'pmoc-tag-danger';
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
                                    <td><span class="pmoc-tag <?= $statusClass ?>"><?= ucfirst($status ?: 'ativo') ?></span></td>
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
