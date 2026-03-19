<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Ordens de Serviço PMOC em Aberto</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Início</a></li>
            <li><a href="<?php echo base_url(); ?>pmoc">Planos PMOC</a></li>
            <li class="active">OS PMOC em Aberto</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Lista de OS PMOC em Aberto</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plano</th>
                                    <th>Data Inicial</th>
                                    <th>Status</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($os_abertas)) { ?>
                                    <tr><td colspan="5" style="text-align:center; color:#7b8ca6;">Nenhuma OS PMOC em aberto encontrada.</td></tr>
                                <?php } else { foreach ($os_abertas as $os) { ?>
                                    <tr>
                                        <td><?php echo $os->idOsPmoc; ?></td>
                                        <td><?php echo $os->plano_id; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></td>
                                        <td><span class="label label-<?php echo ($os->status == 'aberta') ? 'success' : 'default'; ?>"><?php echo ucfirst($os->status); ?></span></td>
                                        <td><?php echo $os->descricao; ?></td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 