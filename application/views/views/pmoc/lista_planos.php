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
            <h5>Planos PMOC</h5>
        </div>
    <div class="span12" style="margin-left: 0">
        <div class="span3">
            <a href="<?php echo base_url(); ?>pmoc/novo" class="button btn btn-mini btn-success" style="max-width: 160px">
                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Novo Plano</span></a>
        </div>
    </div>
    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>N°</th>
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
                        <?php if (!$planos) {
                            echo '<tr><td colspan="8">Nenhum Plano PMOC cadastrado</td></tr>';
                        }
                        $i = 1;
                        foreach ($planos as $p) {
                            $corStatus = '';
                            switch ($p->status) {
                                case 'ativo':
                                    $corStatus = '#00cd00';
                                    break;
                                case 'vencido':
                                    $corStatus = '#CD0000';
                                    break;
                                default:
                                    $corStatus = '#E0E4CC';
                                    break;
                            }
                            echo '<tr>';
                            echo '<td>' . $i++ . '</td>';
                            echo '<td>' . $p->nomeCliente . '</td>';
                            echo '<td>' . $p->frequencia . '</td>';
                            echo '<td>' . $p->tecnico_nome . '</td>';
                            echo '<td>' . $p->art_numero . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($p->art_validade)) . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '">' . ucfirst($p->status) . '</span></td>';
                            echo '<td>';
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'pmoc/editar/' . $p->id_pmoc . '" class="btn-nwe3" title="Editar Plano"><i class="bx bx-edit"></i></a>';
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'pmoc/relatorio/' . $p->id_pmoc . '" class="btn-nwe6" title="Gerar Relatório" target="_blank"><i class="bx bx-printer bx-xs"></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 