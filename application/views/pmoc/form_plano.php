<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </span>
                <h5><?php echo isset($plano) ? 'Editar PMOC e Plano Mensal' : 'Novo PMOC e Plano Mensal'; ?></h5>
            </div>

            <div class="widget-content nopadding tab-content">
                <form action="<?php echo base_url() ?>pmoc/salvar" method="post" id="formPmoc" class="form-horizontal">
                    <?php if (isset($plano)) { ?>
                        <input type="hidden" name="id" value="<?php echo (int) $plano->id_pmoc; ?>">
                    <?php } ?>

                    <div class="span6">
                        <div class="control-group">
                            <label for="cliente" class="control-label">Cliente*</label>
                            <div class="controls">
                                <input id="cliente" type="text" name="cliente" value="<?php if (isset($plano)) echo htmlspecialchars((string) $plano->nomeCliente); ?>" required />
                                <input id="clientes_id" type="hidden" name="clientes_id" value="<?php if (isset($plano)) echo (int) $plano->clientes_id; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="nome_plano" class="control-label">Nome do plano*</label>
                            <div class="controls">
                                <input id="nome_plano" type="text" name="nome_plano" value="<?php echo isset($plano) ? htmlspecialchars((string) $plano->nome_plano) : ''; ?>" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="frequencia" class="control-label">Frequencia*</label>
                            <div class="controls">
                                <?php $freq = isset($plano) ? $plano->frequencia_manutencao : ''; ?>
                                <select id="frequencia" name="frequencia" required>
                                    <option value="">Selecione</option>
                                    <option value="mensal" <?= $freq == 'mensal' ? 'selected' : '' ?>>Mensal</option>
                                    <option value="bimestral" <?= $freq == 'bimestral' ? 'selected' : '' ?>>Bimestral</option>
                                    <option value="trimestral" <?= $freq == 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                                    <option value="quadrimestral" <?= $freq == 'quadrimestral' ? 'selected' : '' ?>>Quadrimestral</option>
                                    <option value="semestral" <?= $freq == 'semestral' ? 'selected' : '' ?>>Semestral</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="valor_mensal" class="control-label">Valor mensal*</label>
                            <div class="controls">
                                <input id="valor_mensal" type="text" name="valor_mensal" value="<?php echo isset($plano) ? number_format((float) $plano->valor_mensal, 2, ',', '.') : ''; ?>" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="data_inicio_contrato" class="control-label">Inicio*</label>
                            <div class="controls">
                                <input id="data_inicio_contrato" type="date" name="data_inicio_contrato" value="<?php echo isset($plano) ? $plano->data_inicio_contrato : ''; ?>" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="vigencia_ate" class="control-label">Vigencia</label>
                            <div class="controls">
                                <input id="vigencia_ate" type="date" name="vigencia_ate" value="<?php echo isset($plano) ? $plano->vigencia_ate : ''; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="forma_pagamento" class="control-label">Forma pagamento</label>
                            <div class="controls">
                                <input id="forma_pagamento" type="text" name="forma_pagamento" value="<?php echo isset($plano) ? htmlspecialchars((string) $plano->forma_pagamento) : ''; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="control-group">
                            <label for="tecnico" class="control-label">Tecnico responsavel</label>
                            <div class="controls">
                                <input id="tecnico" type="text" name="tecnico" value="<?php if (isset($plano)) echo htmlspecialchars((string) $plano->tecnico_nome); ?>" />
                                <input id="tecnico_id" type="hidden" name="tecnico_id" value="<?php if (isset($plano)) echo (int) $plano->tecnico_responsavel; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="status_contrato" class="control-label">Status*</label>
                            <?php $status = isset($plano) ? strtolower((string) ($plano->status_contrato ?: $plano->status)) : 'ativo'; ?>
                            <div class="controls">
                                <select id="status_contrato" name="status_contrato" required>
                                    <option value="ativo" <?= $status == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                    <option value="inativo" <?= $status == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                    <option value="suspenso" <?= $status == 'suspenso' ? 'selected' : '' ?>>Suspenso</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="tipo_atendimento_padrao" class="control-label">Tipo de atendimento padrao</label>
                            <div class="controls">
                                <input id="tipo_atendimento_padrao" type="text" name="tipo_atendimento_padrao" value="<?php echo isset($plano) ? htmlspecialchars((string) $plano->tipo_atendimento_padrao) : ''; ?>" placeholder="Ex: limpeza de filtros" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="art_numero" class="control-label">Numero da ART</label>
                            <div class="controls">
                                <input id="art_numero" type="text" name="art_numero" value="<?php echo isset($plano) ? htmlspecialchars((string) $plano->numero_art) : ''; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="art_validade" class="control-label">Validade ART</label>
                            <div class="controls">
                                <input id="art_validade" type="date" name="art_validade" value="<?php echo isset($plano) ? $plano->validade_art : ''; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="local" class="control-label">Local principal</label>
                            <div class="controls">
                                <input id="local" type="text" name="local" value="<?php echo isset($plano) ? htmlspecialchars((string) $plano->local_instalacao) : ''; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success">
                                    <span class="button__icon"><i class='bx bx-save'></i></span>
                                    <span class="button__text2">Salvar</span>
                                </button>
                                <a href="<?php echo base_url() ?>pmoc" class="button btn btn-warning">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span>
                                    <span class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#cliente').autocomplete({
        source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
        minLength: 1,
        select: function(event, ui) { $('#clientes_id').val(ui.item.id); }
    });

    $('#tecnico').autocomplete({
        source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
        minLength: 1,
        select: function(event, ui) { $('#tecnico_id').val(ui.item.id); }
    });
});
</script>
