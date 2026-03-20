<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/pmoc-dashcode.css?v=' . @filemtime(FCPATH . 'assets/css/pmoc-dashcode.css')) ?>" />

<?php
$osNumero = (int) ($os->idOsPmoc ?? $os->idOs ?? 0);
$statusAtual = mb_strtolower((string) ($os->status ?? 'agendado'));

$statusMap = [
    'pendente' => ['Pendente', 'pmoc-tag-neutral'],
    'agendado' => ['Agendado', 'pmoc-tag-warning'],
    'em andamento' => ['Em andamento', 'pmoc-tag-warning'],
    'em_execucao' => ['Em execucao', 'pmoc-tag-warning'],
    'concluido' => ['Concluido', 'pmoc-tag-success'],
    'atrasado' => ['Atrasado', 'pmoc-tag-danger'],
];
$statusLabel = $statusMap[$statusAtual][0] ?? ucfirst($statusAtual ?: 'Agendado');
$statusClass = $statusMap[$statusAtual][1] ?? 'pmoc-tag-neutral';

$equipamentosQtd = is_array($equipamentos) ? count($equipamentos) : 0;

$itensChecklist = [
    'limpeza_filtros' => ['label' => 'Limpeza de filtros', 'opcoes' => ['Pendente', 'OK', 'Nao Aplicavel']],
    'carga_gas' => ['label' => 'Verificacao da carga de gas', 'opcoes' => ['Pendente', 'OK', 'Baixo']],
    'condicoes_isolamento' => ['label' => 'Condicoes do isolamento', 'opcoes' => ['Pendente', 'OK']],
    'estado_serpentina' => ['label' => 'Estado da serpentina', 'opcoes' => ['OK', 'Suja', 'Danificada']],
    'bandeja_condensado' => ['label' => 'Bandeja de condensado', 'opcoes' => ['OK', 'Com agua', 'Suja']],
    'fiacao_conexoes' => ['label' => 'Fiacao e conexoes eletricas', 'opcoes' => ['OK', 'Com falhas']],
    'dreno' => ['label' => 'Dreno (livre e funcional)', 'opcoes' => ['OK', 'Entupido']],
    'painel_eletrico' => ['label' => 'Painel eletrico', 'opcoes' => ['OK', 'Falha detectada']],
    'grelhas_difusores' => ['label' => 'Grelhas e difusores', 'opcoes' => ['OK', 'Sujos', 'Danificados']],
    'ruidos_anormais' => ['label' => 'Ruidos anormais', 'opcoes' => ['Sim', 'Nao']],
    'bomba_drenagem' => ['label' => 'Bomba de drenagem', 'opcoes' => ['OK', 'Inoperante']],
    'controle_termostato' => ['label' => 'Controle/termostato', 'opcoes' => ['Funcional', 'Defeituoso']],
    'vazamentos_identificados' => ['label' => 'Vazamentos identificados', 'opcoes' => ['Sim', 'Nao']],
];

$action = base_url('pmoc/salvarChecklist');
?>

<div class="new122 pmoc-dash pmoc-checklist">
    <div class="widget-box" style="margin-top:0;">
        <div class="pmoc-header">
            <div class="pmoc-title-wrap">
                <h3>Checklist PMOC - OS #<?= $osNumero ?></h3>
                <p>Preencha os itens tecnicos, registre evidencias e conclua a ordem de servico.</p>
            </div>
            <div class="pmoc-top-actions">
                <a href="<?= base_url('pmoc/os_pmoc/' . $osNumero) ?>" class="btn btn-small"><i class="bx bx-arrow-back"></i> Voltar para OS</a>
                <a href="<?= base_url('pmoc/editar_os_pmoc/' . $osNumero) ?>" class="btn btn-small"><i class="bx bx-edit"></i> Editar OS</a>
            </div>
        </div>

        <div class="widget-content" style="padding:14px;">
            <div class="pmoc-chip-row pmoc-checklist-chip-row">
                <div class="pmoc-chip">OS <b>#<?= $osNumero ?></b></div>
                <div class="pmoc-chip">Status atual <b><span class="pmoc-tag <?= $statusClass ?>"><?= $statusLabel ?></span></b></div>
                <div class="pmoc-chip">Equipamentos vinculados <b><?= (int) $equipamentosQtd ?></b></div>
                <div class="pmoc-chip">Data inicial <b><?= ! empty($os->dataInicial) ? date('d/m/Y H:i', strtotime((string) $os->dataInicial)) : '-' ?></b></div>
            </div>

            <form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="pmoc-checklist-form" style="margin-top:12px;">
                <input type="hidden" name="os_id" value="<?= $osNumero ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="pmoc-checklist-top-grid">
                    <label class="pmoc-checklist-field">
                        <span>Equipamento</span>
                        <select name="equipamento_id" required>
                            <option value="">Selecione</option>
                            <?php foreach (($equipamentos ?? []) as $eq): ?>
                                <?php
                                    $desc = (string) ($eq->descricao ?? $eq->equipamento ?? 'Equipamento');
                                    $modelo = (string) ($eq->modelo ?? '');
                                ?>
                                <option value="<?= (int) $eq->idEquipamentos ?>">
                                    <?= htmlspecialchars($desc) ?><?= $modelo !== '' ? ' (' . htmlspecialchars($modelo) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pmoc-checklist-field">
                        <span>Status da OS apos checklist</span>
                        <select name="status_os" required>
                            <option value="">Selecione</option>
                            <option value="em andamento">Em andamento</option>
                            <option value="concluido">Concluido</option>
                        </select>
                    </label>

                    <label class="pmoc-checklist-field">
                        <span>Tecnico responsavel</span>
                        <input type="text" name="tecnico_responsavel" placeholder="Nome do tecnico" required>
                    </label>

                    <label class="pmoc-checklist-field">
                        <span>Tipo de servico</span>
                        <input type="text" name="tipo_servico" placeholder="Ex: manutencao preventiva" required>
                    </label>
                </div>

                <div class="pmoc-checklist-progress">
                    <div class="pmoc-checklist-progress-label">Preenchimento dos itens tecnicos</div>
                    <div class="pmoc-checklist-progress-track"><span id="pmoc-checklist-progress-bar"></span></div>
                    <div class="pmoc-checklist-progress-text"><strong id="pmoc-checklist-progress-value">0%</strong> dos itens selecionados</div>
                </div>

                <div class="pmoc-checklist-grid">
                    <?php foreach ($itensChecklist as $campo => $item): ?>
                        <article class="pmoc-checklist-card">
                            <h4><?= htmlspecialchars((string) $item['label']) ?></h4>
                            <div class="pmoc-checklist-card-body">
                                <label class="pmoc-checklist-field" style="margin:0;">
                                    <span>Status do item</span>
                                    <select name="<?= $campo ?>" required data-item-select>
                                        <option value="">Selecione</option>
                                        <?php foreach ($item['opcoes'] as $op): ?>
                                            <option value="<?= htmlspecialchars((string) $op) ?>"><?= htmlspecialchars((string) $op) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label class="pmoc-checklist-field" style="margin:0;">
                                    <span>Fotos de evidencia (opcional)</span>
                                    <input type="file" name="<?= $campo ?>_fotos[]" accept="image/*" multiple class="pmoc-file-input" data-file-input>
                                    <small class="pmoc-file-count">Nenhum arquivo selecionado.</small>
                                </label>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="pmoc-checklist-notes">
                    <label class="pmoc-checklist-field" style="margin:0;">
                        <span>Observacoes tecnicas</span>
                        <textarea name="observacoes" rows="4" placeholder="Descreva nao conformidades, orientacoes e recomendacoes."></textarea>
                    </label>
                </div>

                <div class="pmoc-os-actions">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Salvar checklist</button>
                    <a href="<?= base_url('pmoc/os_pmoc/' . $osNumero) ?>" class="btn">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var selects = Array.prototype.slice.call(document.querySelectorAll('[data-item-select]'));
    var bar = document.getElementById('pmoc-checklist-progress-bar');
    var value = document.getElementById('pmoc-checklist-progress-value');

    function updateProgress() {
        if (!selects.length || !bar || !value) return;
        var filled = selects.filter(function (s) { return (s.value || '').trim() !== ''; }).length;
        var percent = Math.round((filled / selects.length) * 100);
        bar.style.width = percent + '%';
        value.textContent = percent + '%';
    }

    selects.forEach(function (s) {
        s.addEventListener('change', updateProgress);
    });

    var fileInputs = Array.prototype.slice.call(document.querySelectorAll('[data-file-input]'));
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            var small = input.parentElement ? input.parentElement.querySelector('.pmoc-file-count') : null;
            if (!small) return;
            var total = input.files ? input.files.length : 0;
            small.textContent = total > 0 ? (total + ' arquivo(s) selecionado(s).') : 'Nenhum arquivo selecionado.';
        });
    });

    updateProgress();
})();
</script>
