<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<style>
  select {
    width: 70px;
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-cogs"></i>
        </span>
        <h5><?php echo isset($plano) ? 'Editar Plano PMOC' : 'Novo Plano PMOC'; ?></h5>
    </div>
    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content nopadding tab-content">
            <form action="<?php echo base_url() ?>pmoc/salvar" method="post" class="form-horizontal">
                <?php if (isset($plano)) { ?>
                    <input type="hidden" name="id" value="<?php echo $plano->id_pmoc; ?>">
                <?php } ?>
                <div class="span12" style="margin-left: 0">
                    <div class="span6">
                        <label for="cliente_id">Cliente</label>
                        <select class="span12" name="cliente_id" id="cliente_id" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $c) { ?>
                                <option value="<?php echo $c->idClientes; ?>" <?php echo isset($plano) && $plano->clientes_id == $c->idClientes ? 'selected' : ''; ?>>
                                    <?php echo $c->nomeCliente; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="span3">
                        <label for="frequencia">Frequência</label>
                        <select class="span12" name="frequencia" id="frequencia" required>
                            <option value="">Selecione a frequência</option>
                            <option value="mensal" <?php echo isset($plano) && $plano->frequencia == 'mensal' ? 'selected' : ''; ?>>Mensal</option>
                            <option value="trimestral" <?php echo isset($plano) && $plano->frequencia == 'trimestral' ? 'selected' : ''; ?>>Trimestral</option>
                            <option value="semestral" <?php echo isset($plano) && $plano->frequencia == 'semestral' ? 'selected' : ''; ?>>Semestral</option>
                            <option value="anual" <?php echo isset($plano) && $plano->frequencia == 'anual' ? 'selected' : ''; ?>>Anual</option>
                        </select>
                    </div>
                    <div class="span3">
                        <label for="tecnico_id">Técnico Responsável</label>
                        <select class="span12" name="tecnico_id" id="tecnico_id" required>
                            <option value="">Selecione um técnico</option>
                            <?php foreach ($tecnicos as $t) { ?>
                                <option value="<?php echo $t->idUsuarios; ?>" <?php echo isset($plano) && $plano->tecnico_responsavel == $t->idUsuarios ? 'selected' : ''; ?>>
                                    <?php echo $t->nome; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="span12" style="margin-left: 0">
                    <div class="span4">
                        <label for="art_numero">Número da ART</label>
                        <input type="text" class="span12" name="art_numero" id="art_numero" value="<?php echo isset($plano) ? $plano->numero_art : ''; ?>" required>
                    </div>
                    <div class="span4">
                        <label for="art_validade">Validade da ART</label>
                        <input type="date" class="span12" name="art_validade" id="art_validade" value="<?php echo isset($plano) ? $plano->validade_art : ''; ?>" required>
                    </div>
                    <div class="span4">
                        <label for="local">Local</label>
                        <input type="text" class="span12" name="local" id="local" value="<?php echo isset($plano) ? $plano->local_instalacao : ''; ?>" required>
                    </div>
                </div>
                <div class="span12" style="margin-left: 0; margin-top: 20px;">
                    <button type="submit" class="button btn btn-mini btn-primary"><span class="button__icon"><i class='bx bx-save'></i></span> Salvar</button>
                    <a href="<?php echo base_url() ?>pmoc" class="button btn btn-mini btn-default"><span class="button__icon"><i class='bx bx-arrow-back'></i></span> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div> 