<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-cogs"></i>
                </span>
                <h5>Editar Equipamento</h5>
            </div>
            <?php if (isset($custom_error) && $custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="" method="post" enctype="multipart/form-data" id="formEquipamento" class="form-horizontal">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="cliente_id" value="<?= $cliente_id ?? '' ?>">
                <div class="widget-content nopadding tab-content">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="control-label">Descrição*</label>
                                <div class="controls">
                                    <input type="text" name="descricao" class="span12" required value="<?= htmlspecialchars($equipamento->descricao) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Marca*</label>
                                <div class="controls">
                                    <input type="text" name="marca" class="span12" required value="<?= htmlspecialchars($equipamento->marca) ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Modelo*</label>
                                <div class="controls">
                                    <input type="text" name="modelo" class="span12" required value="<?= htmlspecialchars($equipamento->modelo) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Nº de Série*</label>
                                <div class="controls">
                                    <input type="text" name="num_serie" class="span12" required value="<?= htmlspecialchars($equipamento->num_serie) ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">BTU*</label>
                                <div class="controls">
                                    <input type="number" name="btu" class="span12" required value="<?= htmlspecialchars($equipamento->btu) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Tensão*</label>
                                <div class="controls">
                                    <input type="text" name="tensao" class="span12" required value="<?= htmlspecialchars($equipamento->tensao) ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Potência*</label>
                                <div class="controls">
                                    <input type="text" name="potencia" class="span12" required value="<?= htmlspecialchars($equipamento->potencia) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Data de Instalação*</label>
                                <div class="controls">
                                    <input type="date" name="data_instalacao" class="span12" required value="<?= htmlspecialchars($equipamento->data_instalacao) ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Local de Instalação*</label>
                                <div class="controls">
                                    <input type="text" name="local_instalacao" class="span12" required value="<?= htmlspecialchars($equipamento->local_instalacao) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="control-label">Foto do Equipamento*</label>
                                <div class="controls">
                                    <input type="file" name="foto" id="foto" class="span12" accept="image/jpeg,image/png" onchange="previewImagem(event)" />
                                </div>
                                <?php if (!empty($equipamento->foto)) { ?>
                                    <img id="imgPreview" src="<?= base_url('assets/uploads/equipamentos/' . $equipamento->foto) ?>" style="max-width:120px; max-height:80px; margin-top:8px; border-radius:6px; border:1px solid #e0e7ef;" />
                                <?php } else { ?>
                                    <img id="imgPreview" style="display:none; max-width:120px; max-height:80px; margin-top:8px; border-radius:6px; border:1px solid #e0e7ef;" />
                                <?php } ?>
                                <div id="erroFoto" style="color:#e74c3c;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-mini btn-success"><span class="button__icon"><i class='bx bx-save'></i></span> <span class="button__text2">Salvar Alterações</span></button>
                            <a href="<?php echo base_url('pmoc'); ?>" class="button btn btn-warning" style="margin-left: 8px;"><span class="button__icon"><i class="bx bx-arrow-back"></i></span> <span class="button__text2">Voltar</span></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function previewImagem(event) {
  const file = event.target.files[0];
  const erro = document.getElementById('erroFoto');
  erro.textContent = '';
  if (file) {
    if (!['image/jpeg','image/png'].includes(file.type)) {
      erro.textContent = 'Apenas arquivos JPG ou PNG são permitidos.';
      event.target.value = '';
      document.getElementById('imgPreview').style.display = 'none';
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      erro.textContent = 'O tamanho máximo permitido é 5MB.';
      event.target.value = '';
      document.getElementById('imgPreview').style.display = 'none';
      return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = document.getElementById('imgPreview');
      img.src = e.target.result;
      img.style.display = 'block';
    }
    reader.readAsDataURL(file);
  }
}
</script>
<style>
.label-equip {
    font-weight: 500;
    font-size: 15px;
    margin-bottom: 4px;
    color: #222e3c;
}
.input-equip {
    border-radius: 7px !important;
    padding: 10px 12px !important;
    font-size: 15px !important;
    box-shadow: 0 1px 2px #e0e7ef33;
    border: 1px solid #dbe3ef !important;
    background: #f9fafb !important;
}
.btn-equip {
    font-size: 16px !important;
    border-radius: 7px !important;
    padding: 10px 28px !important;
    box-shadow: 0 2px 8px #e0e7ef33;
}
.btn-outline-warning {
    background: #fff !important;
    color: #f59e42 !important;
    border: 1.5px solid #f59e42 !important;
}
.btn-outline-warning:hover {
    background: #f59e42 !important;
    color: #fff !important;
}
@media (max-width: 900px) {
    .row-fluid .span4, .row-fluid .span6, .row-fluid .span2 {
        width: 100% !important;
        margin-bottom: 12px;
    }
    .form-actions {
        flex-direction: column;
        gap: 10px;
    }
}
.form-horizontal .span6:last-child .controls input,
.form-horizontal .span6:last-child .controls select,
.form-horizontal .span6:last-child .controls textarea {
    margin-right: 12px;
}
@media (max-width: 900px) {
    .form-horizontal .span6:last-child .controls input {
        margin-right: 0;
    }
}
</style> 