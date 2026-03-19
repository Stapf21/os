<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Relatórios PMOC
            <small>Relatórios de Planos de Manutenção</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Relatórios PMOC</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Relatórios PMOC</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Frequência</th>
                                        <th>Técnico</th>
                                        <th>ART</th>
                                        <th>Validade ART</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($planos as $p) { ?>
                                        <tr>
                                            <td><?php echo $p->nomeCliente; ?></td>
                                            <td><?php echo $p->frequencia; ?></td>
                                            <td><?php echo $p->tecnico_nome; ?></td>
                                            <td><?php echo $p->art_numero; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($p->art_validade)); ?></td>
                                            <td>
                                                <?php if ($p->status == 'ativo') { ?>
                                                    <span class="label label-success">Ativo</span>
                                                <?php } elseif ($p->status == 'vencido') { ?>
                                                    <span class="label label-danger">Vencido</span>
                                                <?php } else { ?>
                                                    <span class="label label-warning">Pendente</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo base_url() ?>pmoc/relatorio/<?php echo $p->id; ?>" class="btn btn-primary btn-xs" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i> Gerar Relatório
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