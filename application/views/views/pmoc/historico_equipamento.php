<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Histórico do Equipamento
            <small>Registro de Manutenções</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>pmoc">Planos PMOC</a></li>
            <li class="active">Histórico</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Histórico - <?php echo $equipamento->nome; ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>OS</th>
                                        <th>Cliente</th>
                                        <th>Status</th>
                                        <th>Observações</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historico as $h) { ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($h->data_verificacao)); ?></td>
                                            <td>#<?php echo $h->idOs; ?></td>
                                            <td><?php echo $h->nomeCliente; ?></td>
                                            <td>
                                                <?php 
                                                $status = json_decode($h->status, true);
                                                $total = count($status);
                                                $ok = count(array_filter($status, function($s) { return $s == 'ok'; }));
                                                $percent = ($ok / $total) * 100;
                                                ?>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" style="width: <?php echo $percent; ?>%">
                                                        <?php echo number_format($percent, 0); ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                $observacoes = json_decode($h->observacoes, true);
                                                foreach ($observacoes as $item => $obs) {
                                                    if (!empty($obs)) {
                                                        echo "<strong>$item:</strong> $obs<br>";
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo base_url() ?>pmoc/relatorio/<?php echo $h->plano_id; ?>" class="btn btn-primary btn-xs">
                                                    <i class="fa fa-file-pdf-o"></i> Relatório
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 