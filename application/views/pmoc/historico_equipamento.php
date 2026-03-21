<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/pmoc-dashcode.css?v=' . @filemtime(FCPATH . 'assets/css/pmoc-dashcode.css')) ?>" />

<?php
$equipNome = (string) ($equipamento->nome ?? $equipamento->descricao ?? 'Equipamento');
$equipModelo = (string) ($equipamento->modelo ?? '');
$equipSerie = (string) ($equipamento->num_serie ?? '');

$total = is_array($historico ?? null) ? count($historico) : 0;
$concluidos = 0;
$emAndamento = 0;
$ultimaData = null;

foreach (($historico ?? []) as $h) {
    $statusRaw = mb_strtolower(trim((string) ($h->status_os ?? '')));
    if ($statusRaw === 'concluido' || $statusRaw === 'concluído' || $statusRaw === 'finalizado') {
        $concluidos++;
    }
    if ($statusRaw === 'em andamento' || $statusRaw === 'em_execucao' || $statusRaw === 'em execução') {
        $emAndamento++;
    }
    if (!empty($h->data_verificacao)) {
        $dt = strtotime((string) $h->data_verificacao);
        if ($dt && ($ultimaData === null || $dt > $ultimaData)) {
            $ultimaData = $dt;
        }
    }
}
?>

<div class="new122 pmoc-dash pmoc-history">
    <div class="widget-box" style="margin-top:0;">
        <div class="pmoc-header">
            <div class="pmoc-title-wrap">
                <h3>Historico do Equipamento</h3>
                <p>Registro consolidado das manutencoes realizadas no ativo.</p>
            </div>
            <div class="pmoc-top-actions">
                <a href="<?= base_url('pmoc') ?>" class="btn btn-small"><i class="bx bx-arrow-back"></i> Voltar PMOC</a>
                <a href="<?= base_url('pmoc/plano/' . (int) ($equipamento->plano_id ?? 0)) ?>" class="btn btn-small" style="display:none;">Contrato</a>
            </div>
        </div>

        <div class="widget-content" style="padding:14px;">
            <div class="pmoc-chip-row pmoc-history-chip-row">
                <div class="pmoc-chip">Equipamento <b><?= htmlspecialchars($equipNome) ?></b></div>
                <div class="pmoc-chip">Modelo <b><?= htmlspecialchars($equipModelo !== '' ? $equipModelo : '-') ?></b></div>
                <div class="pmoc-chip">Serie <b><?= htmlspecialchars($equipSerie !== '' ? $equipSerie : '-') ?></b></div>
                <div class="pmoc-chip">Ultima manutencao <b><?= $ultimaData ? date('d/m/Y H:i', $ultimaData) : '-' ?></b></div>
            </div>

            <div class="pmoc-kpi-grid pmoc-history-kpi-grid" style="margin-top:10px;">
                <div class="pmoc-kpi"><div class="pmoc-kpi-label">Total de registros</div><div class="pmoc-kpi-value"><?= (int) $total ?></div></div>
                <div class="pmoc-kpi pmoc-kpi-success"><div class="pmoc-kpi-label">Concluidos</div><div class="pmoc-kpi-value"><?= (int) $concluidos ?></div></div>
                <div class="pmoc-kpi pmoc-kpi-warning"><div class="pmoc-kpi-label">Em andamento</div><div class="pmoc-kpi-value"><?= (int) $emAndamento ?></div></div>
            </div>
        </div>
    </div>

    <div class="widget-box pmoc-section" style="margin-top:12px;">
        <div class="widget-content" style="padding:14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Registros de manutencao</h4>
                <span class="pmoc-tag pmoc-tag-neutral">Total: <?= (int) $total ?></span>
            </div>

            <div class="pmoc-search">
                <input type="text" placeholder="Buscar por OS, status, observacoes..." data-table-search="#tb-historico-equipamento">
            </div>

            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="tb-historico-equipamento">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>OS</th>
                            <th>Status</th>
                            <th>Observacoes</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historico)): ?>
                            <tr>
                                <td colspan="5" class="pmoc-empty" style="text-align:center; padding:26px 10px;">Nenhum registro de manutencao encontrado para este equipamento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historico as $h): ?>
                                <?php
                                    $statusRaw = mb_strtolower(trim((string) ($h->status_os ?? '')));
                                    $statusLabel = ucfirst(str_replace('_', ' ', $statusRaw ?: 'pendente'));
                                    $statusClass = 'pmoc-tag-neutral';
                                    if (in_array($statusRaw, ['concluido', 'concluído', 'finalizado'], true)) {
                                        $statusClass = 'pmoc-tag-success';
                                    } elseif (in_array($statusRaw, ['em andamento', 'em_execucao', 'em execução'], true)) {
                                        $statusClass = 'pmoc-tag-warning';
                                    } elseif ($statusRaw === 'atrasado') {
                                        $statusClass = 'pmoc-tag-danger';
                                    }

                                    $obsHtml = '';
                                    $obsRaw = (string) ($h->observacoes ?? '');
                                    $obsJson = json_decode($obsRaw, true);
                                    if (is_array($obsJson)) {
                                        $chunks = [];
                                        foreach ($obsJson as $k => $v) {
                                            if (trim((string) $v) === '') {
                                                continue;
                                            }
                                            $chunks[] = '<div><b>' . htmlspecialchars((string) $k) . ':</b> ' . htmlspecialchars((string) $v) . '</div>';
                                        }
                                        $obsHtml = !empty($chunks) ? implode('', $chunks) : '<span class="pmoc-empty">Sem observacoes.</span>';
                                    } else {
                                        $obsHtml = trim($obsRaw) !== ''
                                            ? nl2br(htmlspecialchars($obsRaw))
                                            : '<span class="pmoc-empty">Sem observacoes.</span>';
                                    }
                                ?>
                                <tr>
                                    <td><?= !empty($h->data_verificacao) ? date('d/m/Y H:i', strtotime((string) $h->data_verificacao)) : '-' ?></td>
                                    <td>
                                        <a href="<?= base_url('pmoc/os_pmoc/' . (int) ($h->os_pmoc_id ?? 0)) ?>">#<?= (int) ($h->os_pmoc_id ?? $h->idChecklist ?? 0) ?></a>
                                    </td>
                                    <td><span class="pmoc-tag <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
                                    <td class="pmoc-history-obs"><?= $obsHtml ?></td>
                                    <td class="pmoc-history-actions">
                                        <a href="<?= base_url('pmoc/os_pmoc/' . (int) ($h->os_pmoc_id ?? 0)) ?>" class="btn btn-small btn-success">
                                            <i class="bx bx-show"></i> Visualizar
                                        </a>
                                        <a href="<?= base_url('pmoc/excluir_checklist/' . (int) $h->idChecklist . '/' . (int) $equipamento->idEquipamentos) ?>" class="btn btn-small btn-danger" onclick="return confirm('Tem certeza que deseja excluir este checklist?');">
                                            <i class="bx bx-trash"></i> Excluir
                                        </a>
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

<script>
(function () {
    function norm(v) { return (v || '').toString().toLowerCase(); }

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
})();
</script>
