<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/pmoc-dashcode.css?v=' . @filemtime(FCPATH . 'assets/css/pmoc-dashcode.css')) ?>" />

<?php
$isEdit = (($modo ?? 'criar') === 'editar');
$titulo = $isEdit ? 'Editar OS PMOC' : 'Nova OS PMOC';
$subtitulo = $isEdit ? 'Atualize os dados operacionais da ordem de servico.' : 'Crie uma ordem de servico vinculada ao contrato PMOC.';
$action = $isEdit
    ? base_url('pmoc/atualizar_os_pmoc/' . (int) $os_pmoc->idOsPmoc)
    : base_url('pmoc/salvar_os_pmoc/' . (int) $plano->id_pmoc);

$statusAtual = mb_strtolower((string) ($os_pmoc->status ?? 'agendado'));
$statusOptions = [
    'pendente' => 'Pendente',
    'agendado' => 'Agendado',
    'em andamento' => 'Em andamento',
    'concluido' => 'Concluido',
    'atrasado' => 'Atrasado',
];

$dataPrevista = ! empty($os_pmoc->data_prevista) ? date('Y-m-d', strtotime((string) $os_pmoc->data_prevista)) : date('Y-m-d');
$dataInicial = ! empty($os_pmoc->dataInicial) ? date('Y-m-d\TH:i', strtotime((string) $os_pmoc->dataInicial)) : date('Y-m-d\TH:i');
?>

<div class="new122 pmoc-dash pmoc-os">
    <div class="widget-box" style="margin-top:0;">
        <div class="pmoc-header">
            <div class="pmoc-title-wrap">
                <h3><?= $titulo ?></h3>
                <p><?= $subtitulo ?></p>
            </div>
            <div class="pmoc-top-actions">
                <a href="<?= base_url('pmoc/plano/' . (int) $plano->id_pmoc) ?>" class="btn btn-small"><i class="bx bx-arrow-back"></i> Voltar ao contrato</a>
                <?php if ($isEdit): ?>
                    <a href="<?= base_url('pmoc/os_pmoc/' . (int) $os_pmoc->idOsPmoc) ?>" class="btn btn-small"><i class="bx bx-show"></i> Visualizar OS</a>
                    <a href="<?= base_url('pmoc/checklist/' . (int) $os_pmoc->idOsPmoc) ?>" class="btn btn-primary btn-small"><i class="bx bx-clipboard"></i> Checklist</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="widget-content" style="padding:14px;">
            <div class="pmoc-chip-row pmoc-os-chip-row">
                <div class="pmoc-chip">Cliente <b><?= htmlspecialchars((string) $plano->nomeCliente) ?></b></div>
                <div class="pmoc-chip">Plano <b><?= htmlspecialchars((string) ($plano->nome_plano ?: 'Plano PMOC')) ?></b></div>
                <div class="pmoc-chip">OS <b><?= $isEdit ? ('#' . (int) $os_pmoc->idOsPmoc) : 'Nova' ?></b></div>
                <div class="pmoc-chip">Status atual <b><?= htmlspecialchars((string) ucfirst($statusAtual)) ?></b></div>
            </div>

            <form action="<?= $action ?>" method="post" class="pmoc-os-form" style="margin-top:12px;">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="pmoc-os-grid">
                    <label class="pmoc-os-field">
                        <span>Unidade</span>
                        <select name="cliente_unidade_id">
                            <option value="">Sem unidade especifica</option>
                            <?php foreach (($unidades ?? []) as $u): ?>
                                <option value="<?= (int) $u->idClienteUnidade ?>" <?= ((int) $u->idClienteUnidade === (int) ($os_pmoc->cliente_unidade_id ?? 0)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) $u->nome) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pmoc-os-field">
                        <span>Status</span>
                        <select name="status" required>
                            <?php foreach ($statusOptions as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($statusAtual === $key) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pmoc-os-field">
                        <span>Data prevista</span>
                        <input type="date" name="data_prevista" value="<?= $dataPrevista ?>" required>
                    </label>

                    <label class="pmoc-os-field">
                        <span>Data inicial</span>
                        <input type="datetime-local" name="data_inicial" value="<?= $dataInicial ?>" required>
                    </label>

                    <label class="pmoc-os-field pmoc-os-field-wide">
                        <span>Tipo de atendimento</span>
                        <input type="text" name="tipo_atendimento" value="<?= htmlspecialchars((string) ($os_pmoc->tipo_atendimento ?? '')) ?>" placeholder="Ex: manutencao preventiva">
                    </label>

                    <label class="pmoc-os-field pmoc-os-field-wide">
                        <span>Descricao</span>
                        <textarea name="descricao" rows="4" placeholder="Detalhe o escopo da ordem de servico"><?= htmlspecialchars((string) ($os_pmoc->descricao ?? '')) ?></textarea>
                    </label>
                </div>

                <div class="pmoc-os-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i>
                        <?= $isEdit ? 'Salvar alteracoes' : 'Criar OS PMOC' ?>
                    </button>
                    <a href="<?= base_url('pmoc/plano/' . (int) $plano->id_pmoc) ?>" class="btn">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
