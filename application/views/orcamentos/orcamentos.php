<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<style>
    select {
        width: 70px;
    }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice"></i>
        </span>
        <h5>Orcamentos</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/orcamentos/gerenciar">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aOrcamento')) { ?>
                <div class="span3">
                    <a href="<?php echo base_url(); ?>index.php/orcamentos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Novo Orcamento</span>
                    </a>
                </div>
            <?php } ?>
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do cliente a pesquisar" class="span12" value="<?= $this->input->get('pesquisa') ?>">
            </div>
            <div class="span2">
                <select name="status" class="span12">
                    <option value="">Selecione status</option>
                    <option value="Rascunho" <?= $this->input->get('status') == 'Rascunho' ? 'selected' : '' ?>>Rascunho</option>
                    <option value="Enviado ao cliente" <?= $this->input->get('status') == 'Enviado ao cliente' ? 'selected' : '' ?>>Enviado ao cliente</option>
                    <option value="Aprovado" <?= $this->input->get('status') == 'Aprovado' ? 'selected' : '' ?>>Aprovado</option>
                    <option value="Reprovado" <?= $this->input->get('status') == 'Reprovado' ? 'selected' : '' ?>>Reprovado</option>
                    <option value="Cancelado" <?= $this->input->get('status') == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="Sem resposta" <?= $this->input->get('status') == 'Sem resposta' ? 'selected' : '' ?>>Sem resposta</option>
                </select>
            </div>
            <div class="span3">
                <input type="date" name="data" id="data" placeholder="De" class="span6 datepicker" autocomplete="off" value="<?= $this->input->get('data') ?>">
                <input type="date" name="data2" id="data2" placeholder="Ate" class="span6 datepicker" autocomplete="off" value="<?= $this->input->get('data2') ?>">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                </button>
            </div>
        </form>
    </div>

    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Cliente</th>
                            <th>Responsavel</th>
                            <th>Data Criacao</th>
                            <th>Validade</th>
                            <th>Total</th>
                            <th>Desconto</th>
                            <th>Total c/ Desconto</th>
                            <th>Status</th>
                            <th>OS</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! $results) {
                            echo '<tr>
                                    <td colspan="11">Nenhum Orcamento Cadastrado</td>
                                </tr>';
                        }
                        foreach ($results as $r) {
                            $dataCriacao = $r->dataCriacao ? date('d/m/Y', strtotime($r->dataCriacao)) : '';
                            $validade = $r->validade ? date('d/m/Y', strtotime($r->validade)) : '';
                            $total = $r->totalProdutos + $r->totalServicos;
                            $totalComDesconto = $total;
                            if (floatval($r->valor_desconto) > 0) {
                                $totalComDesconto = $r->valor_desconto;
                            }

                            $descontoLabel = 'R$ ' . number_format(floatval($r->desconto), 2, ',', '.');
                            if ($r->tipo_desconto === 'porcento') {
                                $descontoLabel = number_format(floatval($r->desconto), 2, ',', '.') . ' %';
                            }

                            switch ($r->status) {
                                case 'Rascunho':
                                    $cor = '#808080';
                                    break;
                                case 'Enviado ao cliente':
                                    $cor = '#436eee';
                                    break;
                                case 'Aprovado':
                                    $cor = '#00cd00';
                                    break;
                                case 'Reprovado':
                                    $cor = '#CD0000';
                                    break;
                                case 'Cancelado':
                                    $cor = '#B266FF';
                                    break;
                                case 'Sem resposta':
                                    $cor = '#FF8C00';
                                    break;
                                default:
                                    $cor = '#E0E4CC';
                            }

                            echo '<tr>';
                            echo '<td>' . $r->idOrcamento . '</td>';
                            echo '<td><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->clientes_id . '">' . $r->nomeCliente . '</a></td>';
                            echo '<td>' . $r->nome . '</td>';
                            echo '<td>' . $dataCriacao . '</td>';
                            echo '<td>' . $validade . '</td>';
                            echo '<td>R$ ' . number_format($total, 2, ',', '.') . '</td>';
                            echo '<td>' . $descontoLabel . '</td>';
                            echo '<td>R$ ' . number_format($totalComDesconto, 2, ',', '.') . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $r->status . '</span></td>';
                            echo '<td>' . ($r->os_id ? $r->os_id : '-') . '</td>';
                            echo '<td>';

                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOrcamento')) {
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/orcamentos/visualizar/' . $r->idOrcamento . '" class="btn-nwe" title="Visualizar Orcamento"><i class="bx bx-show"></i></a>';
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/orcamentos/imprimir/' . $r->idOrcamento . '" target="_blank" class="btn-nwe6" title="Imprimir A4"><i class="bx bx-printer bx-xs"></i></a>';
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/orcamentos/imprimirTermica/' . $r->idOrcamento . '" target="_blank" class="btn-nwe6" title="Imprimir Termica"><i class="bx bx-printer bx-xs"></i></a>';
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/orcamentos/enviar_email/' . $r->idOrcamento . '" class="btn-nwe6" title="Enviar Email"><i class="bx bx-envelope bx-xs"></i></a>';
                            }

                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOrcamento')) {
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/orcamentos/editar/' . $r->idOrcamento . '" class="btn-nwe3" title="Editar Orcamento"><i class="bx bx-edit"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dOrcamento')) {
                                echo '<a href="#modal-excluir" role="button" data-toggle="modal" data-orcamento="' . $r->idOrcamento . '" class="btn-nwe4 orcamento-excluir" title="Excluir Orcamento"><i class="bx bx-trash-alt"></i></a>';
                            }

                            if ($r->os_id) {
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/os/editar/' . $r->os_id . '" class="btn-nwe" title="Ver OS"><i class="bx bx-file"></i></a>';
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dOs')) {
                                    echo '<a style="margin-right: 1%" href="#modal-excluir-os" role="button" data-toggle="modal" data-os="' . $r->os_id . '" class="btn-nwe4 os-excluir" title="Excluir OS"><i class="bx bx-trash-alt"></i></a>';
                                }
                            } elseif ($this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) {
                                if ($r->status === 'Aprovado') {
                                    echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/orcamentos/gerarOs/' . $r->idOrcamento . '" class="btn-nwe6" title="Gerar OS"><i class="bx bx-transfer-alt"></i></a>';
                                } else {
                                    echo '<span style="margin-right: 1%; opacity: 0.5; cursor: not-allowed;" class="btn-nwe6" title="Aprove o orcamento para gerar OS"><i class="bx bx-transfer-alt"></i></span>';
                                }
                            }

                            echo '</td>';
                            echo '</tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php echo $this->pagination->create_links(); ?>
</div>

<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/orcamentos/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5 id="modalExcluirLabel">Excluir Orcamento</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idOrcamento" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este orcamento?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger">
                <span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span>
            </button>
        </div>
    </form>
</div>

<div id="modal-excluir-os" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalExcluirOsLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/os/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5 id="modalExcluirOsLabel">Excluir OS</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idOs" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta OS?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger">
                <span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span>
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.orcamento-excluir', function() {
            $('#idOrcamento').val($(this).data('orcamento'));
        });
        $(document).on('click', '.os-excluir', function() {
            $('#idOs').val($(this).data('os'));
        });
    });
</script>
