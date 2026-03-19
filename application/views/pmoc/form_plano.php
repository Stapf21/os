<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <h5><?php echo isset($plano) ? 'Editar Plano PMOC' : 'Novo Plano PMOC'; ?></h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" style="margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">Detalhes do Plano PMOC</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12">
                                <form action="<?php echo base_url() ?>pmoc/salvar" method="post" id="formPmoc">
                                    <?php if (isset($plano)) { ?>
                                        <input type="hidden" name="id" value="<?php echo $plano->id_pmoc; ?>">
                                    <?php } ?>
                                    <div class="span12" style="padding: 1%">
                                        <div class="span6">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php if(isset($plano)) echo $plano->nomeCliente; ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php if(isset($plano)) echo $plano->clientes_id; ?>" />
                                        </div>
                                        <div class="span6">
                                            <label for="tecnico">Técnico Responsável<span class="required">*</span></label>
                                            <input id="tecnico" class="span12" type="text" name="tecnico" value="<?php if(isset($plano)) echo $plano->tecnico_nome; ?>" />
                                            <input id="tecnico_id" class="span12" type="hidden" name="tecnico_id" value="<?php if(isset($plano)) echo $plano->tecnico_responsavel; ?>" />
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span3">
                                            <label for="frequencia">Frequência<span class="required">*</span></label>
                                            <select class="span12" name="frequencia" id="frequencia" required>
                                                <option value="">Selecione a frequência</option>
                                                <option value="mensal" <?php echo isset($plano) && $plano->frequencia == 'mensal' ? 'selected' : ''; ?>>Mensal</option>
                                                <option value="trimestral" <?php echo isset($plano) && $plano->frequencia == 'trimestral' ? 'selected' : ''; ?>>Trimestral</option>
                                                <option value="semestral" <?php echo isset($plano) && $plano->frequencia == 'semestral' ? 'selected' : ''; ?>>Semestral</option>
                                                <option value="anual" <?php echo isset($plano) && $plano->frequencia == 'anual' ? 'selected' : ''; ?>>Anual</option>
                                            </select>
                                        </div>
                                        <div class="span3">
                                            <label for="art_numero">Número da ART<span class="required">*</span></label>
                                            <input type="text" class="span12" name="art_numero" id="art_numero" value="<?php echo isset($plano) ? $plano->numero_art : ''; ?>" required>
                                        </div>
                                        <div class="span3">
                                            <label for="art_validade">Validade da ART<span class="required">*</span></label>
                                            <input type="date" class="span12" name="art_validade" id="art_validade" value="<?php echo isset($plano) ? $plano->validade_art : ''; ?>" required>
                                        </div>
                                        <div class="span3">
                                            <label for="local">Local<span class="required">*</span></label>
                                            <input type="text" class="span12" name="local" id="local" value="<?php echo isset($plano) ? $plano->local_instalacao : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="display:flex">
                                            <button class="button btn btn-success" id="btnSalvar">
                                              <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                                            <a href="<?php echo base_url() ?>pmoc" class="button btn btn-mini btn-warning" style="max-width: 160px">
                                              <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
            minLength: 1,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });
        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 1,
            select: function(event, ui) {
                $("#tecnico_id").val(ui.item.id);
            }
        });
        $("#formPmoc").validate({
            rules: {
                cliente: { required: true },
                tecnico: { required: true },
                frequencia: { required: true },
                art_numero: { required: true },
                art_validade: { required: true },
                local: { required: true }
            },
            messages: {
                cliente: { required: 'Campo Requerido.' },
                tecnico: { required: 'Campo Requerido.' },
                frequencia: { required: 'Campo Requerido.' },
                art_numero: { required: 'Campo Requerido.' },
                art_validade: { required: 'Campo Requerido.' },
                local: { required: 'Campo Requerido.' }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script> 