<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Checklist PMOC
            <small>Verificação de Equipamentos</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>pmoc">Planos PMOC</a></li>
            <li class="active">Checklist</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Checklist - OS #<?php echo $os->idOs; ?></h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo base_url() ?>pmoc/salvarChecklist" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="os_id" value="<?php echo $os->idOs; ?>">
                            
                            <?php foreach ($equipamentos as $equipamento) { ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4><?php echo $equipamento->nome; ?> - <?php echo $equipamento->marca; ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <input type="hidden" name="equipamento_id[]" value="<?php echo $equipamento->id; ?>">
                                        
                                        <div class="form-group">
                                            <label>Verificação de Nível de Óleo</label>
                                            <select class="form-control" name="status[<?php echo $equipamento->id; ?>][oleo]" required>
                                                <option value="ok">OK</option>
                                                <option value="nok">NOK</option>
                                                <option value="na">N/A</option>
                                            </select>
                                            <textarea class="form-control" name="observacoes[<?php echo $equipamento->id; ?>][oleo]" placeholder="Observações"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Verificação de Filtros</label>
                                            <select class="form-control" name="status[<?php echo $equipamento->id; ?>][filtros]" required>
                                                <option value="ok">OK</option>
                                                <option value="nok">NOK</option>
                                                <option value="na">N/A</option>
                                            </select>
                                            <textarea class="form-control" name="observacoes[<?php echo $equipamento->id; ?>][filtros]" placeholder="Observações"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Verificação de Correias</label>
                                            <select class="form-control" name="status[<?php echo $equipamento->id; ?>][correias]" required>
                                                <option value="ok">OK</option>
                                                <option value="nok">NOK</option>
                                                <option value="na">N/A</option>
                                            </select>
                                            <textarea class="form-control" name="observacoes[<?php echo $equipamento->id; ?>][correias]" placeholder="Observações"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Verificação de Conexões</label>
                                            <select class="form-control" name="status[<?php echo $equipamento->id; ?>][conexoes]" required>
                                                <option value="ok">OK</option>
                                                <option value="nok">NOK</option>
                                                <option value="na">N/A</option>
                                            </select>
                                            <textarea class="form-control" name="observacoes[<?php echo $equipamento->id; ?>][conexoes]" placeholder="Observações"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Foto Antes</label>
                                            <input type="file" class="form-control" name="foto_antes[<?php echo $equipamento->id; ?>]" accept="image/*">
                                        </div>

                                        <div class="form-group">
                                            <label>Foto Depois</label>
                                            <input type="file" class="form-control" name="foto_depois[<?php echo $equipamento->id; ?>]" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Salvar Checklist</button>
                                <a href="<?php echo base_url() ?>pmoc" class="btn btn-default">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 