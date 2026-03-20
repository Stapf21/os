<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/pmoc-dashcode.css?v=' . @filemtime(FCPATH . 'assets/css/pmoc-dashcode.css')) ?>" />

<?php
$statusKey = mb_strtolower((string) ($os_pmoc->status ?? 'agendado'));
$statusMap = [
    'pendente' => ['Pendente', 'pmoc-tag-neutral'],
    'agendado' => ['Agendado', 'pmoc-tag-warning'],
    'em andamento' => ['Em andamento', 'pmoc-tag-warning'],
    'em_execucao' => ['Em execucao', 'pmoc-tag-warning'],
    'concluido' => ['Concluido', 'pmoc-tag-success'],
    'atrasado' => ['Atrasado', 'pmoc-tag-danger'],
];
$statusLabel = $statusMap[$statusKey][0] ?? ucfirst($statusKey ?: 'Agendado');
$statusClass = $statusMap[$statusKey][1] ?? 'pmoc-tag-neutral';

$equipamentoPrincipal = ! empty($equipamentos) ? $equipamentos[0] : null;
$qtdEquipamentos = is_array($equipamentos) ? count($equipamentos) : 0;

$itensChecklist = [
    'limpeza_filtros' => 'Limpeza de filtros',
    'carga_gas' => 'Verificacao da carga de gas',
    'condicoes_isolamento' => 'Condicoes do isolamento',
    'estado_serpentina' => 'Estado da serpentina',
    'bandeja_condensado' => 'Bandeja de condensado',
    'fiacao_conexoes' => 'Fiacao e conexoes eletricas',
    'dreno' => 'Dreno (livre e funcional)',
    'painel_eletrico' => 'Painel eletrico',
    'grelhas_difusores' => 'Grelhas e difusores',
    'ruidos_anormais' => 'Ruidos anormais',
    'bomba_drenagem' => 'Bomba de drenagem',
    'controle_termostato' => 'Controle/termostato',
    'vazamentos_identificados' => 'Vazamentos identificados',
];
?>

<div class="new122 pmoc-dash pmoc-os">
    <div class="widget-box" style="margin-top:0;">
        <div class="pmoc-header">
            <div class="pmoc-title-wrap">
                <h3>OS PMOC #<?= (int) $os_pmoc->idOsPmoc ?></h3>
                <p>Detalhes operacionais, equipamentos vinculados e historico de checklist.</p>
            </div>
            <div class="pmoc-top-actions">
                <a href="<?= base_url('pmoc/plano/' . (int) $os_pmoc->plano_id) ?>" class="btn btn-small"><i class="bx bx-arrow-back"></i> Contrato</a>
                <a href="<?= base_url('pmoc/editar_os_pmoc/' . (int) $os_pmoc->idOsPmoc) ?>" class="btn btn-small"><i class="bx bx-edit"></i> Editar OS</a>
                <a href="<?= base_url('pmoc/checklist/' . (int) $os_pmoc->idOsPmoc) ?>" class="btn btn-primary btn-small"><i class="bx bx-clipboard"></i> Atualizar OS</a>
            </div>
        </div>

        <div class="widget-content" style="padding:14px;">
            <div class="pmoc-chip-row pmoc-os-chip-row">
                <div class="pmoc-chip">Status <b><span class="pmoc-tag <?= $statusClass ?>"><?= $statusLabel ?></span></b></div>
                <div class="pmoc-chip">Data prevista <b><?= ! empty($os_pmoc->data_prevista) ? date('d/m/Y', strtotime((string) $os_pmoc->data_prevista)) : '-' ?></b></div>
                <div class="pmoc-chip">Data inicial <b><?= ! empty($os_pmoc->dataInicial) ? date('d/m/Y H:i', strtotime((string) $os_pmoc->dataInicial)) : '-' ?></b></div>
                <div class="pmoc-chip">Data final <b><?= ! empty($os_pmoc->dataFinal) ? date('d/m/Y H:i', strtotime((string) $os_pmoc->dataFinal)) : '-' ?></b></div>
                <div class="pmoc-chip">Equipamentos <b><?= (int) $qtdEquipamentos ?></b></div>
            </div>

            <div class="pmoc-os-summary-grid" style="margin-top:12px;">
                <div class="pmoc-os-summary-card">
                    <span>Equipamento principal</span>
                    <strong><?= $equipamentoPrincipal ? htmlspecialchars((string) ($equipamentoPrincipal->descricao ?: '-')) : '-' ?></strong>
                    <small>
                        <?php if ($equipamentoPrincipal): ?>
                            Modelo: <?= htmlspecialchars((string) ($equipamentoPrincipal->modelo ?: '-')) ?>
                            | Serie: <?= htmlspecialchars((string) ($equipamentoPrincipal->num_serie ?: '-')) ?>
                        <?php else: ?>
                            Nenhum equipamento vinculado.
                        <?php endif; ?>
                    </small>
                </div>
                <div class="pmoc-os-summary-card">
                    <span>Tipo de atendimento</span>
                    <strong><?= htmlspecialchars((string) ($os_pmoc->tipo_atendimento ?: '-')) ?></strong>
                    <small>Definido no contrato e ajustavel por OS.</small>
                </div>
                <div class="pmoc-os-summary-card pmoc-os-summary-card-wide">
                    <span>Descricao da OS</span>
                    <strong><?= nl2br(htmlspecialchars((string) ($os_pmoc->descricao ?: '-'))) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-box pmoc-section" style="margin-top:12px;">
        <div class="widget-content" style="padding:14px;">
            <div class="pmoc-section-head" style="margin-bottom:10px;">
                <h4 class="pmoc-section-title">Checklists realizados</h4>
                <span class="pmoc-tag pmoc-tag-neutral">Total: <?= is_array($checklists) ? count($checklists) : 0 ?></span>
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tecnico</th>
                            <th>Itens OK</th>
                            <th>Observacoes</th>
                            <th>Fotos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($checklists)): ?>
                            <tr><td colspan="5" class="pmoc-empty" style="text-align:center;">Nenhum checklist realizado.</td></tr>
                        <?php else: ?>
                            <?php usort($checklists, function ($a, $b) { return strtotime((string) $b->data_verificacao) - strtotime((string) $a->data_verificacao); }); ?>
                            <?php foreach ($checklists as $c): ?>
                                <?php
                                    $ok = 0;
                                    foreach (array_keys($itensChecklist) as $col) {
                                        $valor = mb_strtolower(trim((string) ($c->$col ?? '')));
                                        if (in_array($valor, ['ok', 'sim', 'conforme', 'realizado'], true)) {
                                            $ok++;
                                        }
                                    }
                                    $fotosChecklist = $this->db->where('checklist_id', (int) $c->idChecklist)->get('checklist_fotos')->result();
                                    $imgUrls = [];
                                    foreach ($fotosChecklist as $foto) {
                                        $imgUrls[] = base_url('uploads/pmoc/' . $foto->nome_arquivo);
                                    }
                                ?>
                                <tr>
                                    <td><?= ! empty($c->data_verificacao) ? date('d/m/Y H:i', strtotime((string) $c->data_verificacao)) : '-' ?></td>
                                    <td><?= htmlspecialchars((string) ($c->tecnico_responsavel ?: '-')) ?></td>
                                    <td><span class="pmoc-tag pmoc-tag-success"><?= (int) $ok ?>/<?= count($itensChecklist) ?></span></td>
                                    <td style="max-width:420px;"><?= ! empty($c->observacoes) ? nl2br(htmlspecialchars((string) $c->observacoes)) : '<span class="pmoc-empty">Sem observacoes.</span>' ?></td>
                                    <td>
                                        <?php if (! empty($imgUrls)): ?>
                                            <button type="button" class="btn btn-small pmoc-open-gallery" data-images='<?= htmlspecialchars(json_encode($imgUrls), ENT_QUOTES, 'UTF-8') ?>'>
                                                Ver fotos (<?= count($imgUrls) ?>)
                                            </button>
                                        <?php else: ?>
                                            <span class="pmoc-empty">Sem fotos</span>
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
</div>

<div id="pmoc-gallery-modal" class="pmoc-gallery-modal" aria-hidden="true">
    <div class="pmoc-gallery-content">
        <button type="button" class="pmoc-gallery-close" aria-label="Fechar">&times;</button>
        <div class="pmoc-gallery-grid"></div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('pmoc-gallery-modal');
    var grid = modal ? modal.querySelector('.pmoc-gallery-grid') : null;
    var closeBtn = modal ? modal.querySelector('.pmoc-gallery-close') : null;

    function closeModal() {
        if (!modal || !grid) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        grid.innerHTML = '';
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('.pmoc-open-gallery');
        if (!trigger || !modal || !grid) return;

        var raw = trigger.getAttribute('data-images') || '[]';
        var images = [];
        try { images = JSON.parse(raw); } catch (e) { images = []; }

        if (!images.length) return;

        grid.innerHTML = images.map(function (url) {
            return '<img src="' + url + '" alt="Foto checklist">';
        }).join('');

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal();
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });
})();
</script>
