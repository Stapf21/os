<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<?php
$dataCriacao = $result->dataCriacao ? date('d/m/Y', strtotime($result->dataCriacao)) : '';
$validade = $result->validade ? date('d/m/Y', strtotime($result->validade)) : '';
$totalProdutos = 0;
foreach ($produtos as $p) {
    $totalProdutos += $p->subTotal;
}
$totalServicos = 0;
foreach ($servicos as $s) {
    $precoServico = $s->preco ?: $s->precoVenda;
    $quantidadeServico = $s->quantidade ?: 1;
    $totalServicos += $precoServico * $quantidadeServico;
}
$totalOrcamento = $totalProdutos + $totalServicos;
?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>Editar Orcamento</h5>
                <div class="buttons">
                    <a title="Visualizar Orcamento" class="button btn btn-mini btn-primary" href="<?php echo site_url() ?>/orcamentos/visualizar/<?php echo $result->idOrcamento; ?>">
                        <span class="button__icon"><i class="bx bx-show"></i></span><span class="button__text">Visualizar</span>
                    </a>
                    <a target="_blank" title="Imprimir Orcamento" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/orcamentos/imprimir/<?php echo $result->idOrcamento; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span><span class="button__text">Imprimir</span>
                    </a>
                    <a target="_blank" title="Impressao Termica" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/orcamentos/imprimirTermica/<?php echo $result->idOrcamento; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span><span class="button__text">Termica</span>
                    </a>
                    <a title="Enviar por E-mail" class="button btn btn-mini btn-warning" href="<?php echo site_url() ?>/orcamentos/enviar_email/<?php echo $result->idOrcamento; ?>">
                        <span class="button__icon"><i class="bx bx-envelope"></i></span> <span class="button__text">Via E-mail</span>
                    </a>
                    <?php if ($osGerada && $osGerada->idOs && $this->permission->checkPermission($this->session->userdata('permissao'), 'dOs')) { ?>
                        <form action="<?php echo site_url() ?>/os/excluir" method="post" style="display:inline" onsubmit="return confirm('Deseja realmente excluir esta OS?');">
                            <input type="hidden" name="id" value="<?php echo $osGerada->idOs; ?>" />
                            <button type="submit" title="Excluir OS" class="button btn btn-mini btn-danger">
                                <span class="button__icon"><i class="bx bx-trash-alt"></i></span><span class="button__text">Excluir OS</span>
                            </button>
                        </form>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) { ?>
                        <?php if (!($osGerada && $osGerada->idOs) && $result->status === 'Aprovado') { ?>
                            <a title="Gerar OS" class="button btn btn-mini btn-success" href="<?php echo site_url() ?>/orcamentos/gerarOs/<?php echo $result->idOrcamento; ?>">
                                <span class="button__icon"><i class="bx bx-transfer-alt"></i></span><span class="button__text">Gerar OS</span>
                            </a>
                        <?php } elseif ($osGerada && $osGerada->idOs) { ?>
                            <span class="button btn btn-mini btn-success disabled" title="OS ja gerada">
                                <span class="button__icon"><i class="bx bx-transfer-alt"></i></span><span class="button__text">OS Gerada</span>
                            </span>
                        <?php } else { ?>
                            <span class="button btn btn-mini btn-success disabled" title="Aprove o orcamento para gerar OS">
                                <span class="button__icon"><i class="bx bx-transfer-alt"></i></span><span class="button__text">Gerar OS</span>
                            </span>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes do Orcamento</a></li>
                        <li id="tabDesconto"><a href="#tab2" data-toggle="tab">Desconto</a></li>
                        <li id="tabProdutos"><a href="#tab3" data-toggle="tab">Produtos</a></li>
                        <li id="tabServicos"><a href="#tab4" data-toggle="tab">Servicos</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divEditarOrcamento">
                                <form action="<?php echo current_url(); ?>" method="post" id="formOrcamento">
                                    <?php echo form_hidden('idOrcamento', $result->idOrcamento) ?>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <h3>Orcamento: <?php echo $result->idOrcamento ?></h3>
                                        <div class="span2" style="margin-left: 0">
                                            <label for="dataCriacao">Data Criacao</label>
                                            <input id="dataCriacao" class="span12 datepicker" type="text" name="dataCriacao" value="<?php echo $dataCriacao; ?>" />
                                        </div>
                                        <div class="span2">
                                            <label for="validade">Validade</label>
                                            <input id="validade" class="span12 datepicker" type="text" name="validade" value="<?php echo $validade; ?>" />
                                        </div>
                                        <div class="span4">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id ?>" />
                                        </div>
                                        <div class="span4">
                                            <label for="tecnico">Responsavel<span class="required">*</span></label>
                                            <input id="tecnico" class="span12" type="text" name="tecnico" value="<?php echo $result->nome ?>" />
                                            <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id" value="<?php echo $result->usuarios_id ?>" />
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span3">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status">
                                                <option <?= $result->status == 'Rascunho' ? 'selected' : '' ?> value="Rascunho">Rascunho</option>
                                                <option <?= $result->status == 'Enviado ao cliente' ? 'selected' : '' ?> value="Enviado ao cliente">Enviado ao cliente</option>
                                                <option <?= $result->status == 'Aprovado' ? 'selected' : '' ?> value="Aprovado">Aprovado</option>
                                                <option <?= $result->status == 'Reprovado' ? 'selected' : '' ?> value="Reprovado">Reprovado</option>
                                                <option <?= $result->status == 'Cancelado' ? 'selected' : '' ?> value="Cancelado">Cancelado</option>
                                                <option <?= $result->status == 'Sem resposta' ? 'selected' : '' ?> value="Sem resposta">Sem resposta</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label for="observacoes"><h4>Observacoes</h4></label>
                                        <textarea class="span12 editor" name="observacoes" id="observacoes" cols="30" rows="5"><?php echo $result->observacoes ?></textarea>
                                    </div>
                                    <div class="span6" style="padding: 1%; margin-left: 0">
                                        <label for="condicoes"><h4>Condicoes</h4></label>
                                        <textarea class="span12 editor" name="condicoes" id="condicoes" cols="30" rows="5"><?php echo $result->condicoes ?></textarea>
                                    </div>
                                    <div class="span12" style="padding: 0; margin-left: 0">
                                        <div class="span6 offset3" style="display:flex;justify-content: center">
                                            <button class="button btn btn-primary" id="btnContinuar">
                                                <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span>
                                            </button>
                                            <a href="<?php echo base_url() ?>index.php/orcamentos" class="button btn btn-mini btn-warning">
                                                <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab2">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formDesconto" action="<?php echo base_url(); ?>index.php/orcamentos/adicionarDesconto" method="POST">
                                    <div id="divValorTotal">
                                        <div class="span3">
                                            <label for="">Valor Total do Orcamento:</label>
                                            <input class="span12 money" id="valorTotal" name="valorTotal" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." value="<?php echo number_format($totalOrcamento, 2, '.', ''); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <label for="">Tipo Desc.</label>
                                        <select style="width: 4em;" name="tipoDesconto" id="tipoDesconto">
                                            <option value="real">R$</option>
                                            <option value="porcento" <?= $result->tipo_desconto == "porcento" ? "selected" : "" ?>>%</option>
                                        </select>
                                    </div>
                                    <div class="span3">
                                        <input type="hidden" name="idOrcamento" id="idOrcamento" value="<?php echo $result->idOrcamento; ?>" />
                                        <label for="">Desconto</label>
                                        <input style="width: 4em;" id="desconto" name="desconto" type="text" maxlength="6" size="2" value="<?= $result->desconto ?>" />
                                        <strong><span style="color: red" id="errorAlert"></span></strong>
                                    </div>
                                    <div class="span2">
                                        <label for="">Total com Desconto</label>
                                        <input class="span12 money" id="resultado" type="text" data-affixes-stay="true" data-thousands="" data-decimal="." name="resultado" value="<?php echo $result->valor_desconto ?>" readonly />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarDesconto">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span> <span class="button__text2">Aplicar</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab3">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formProdutos" action="<?php echo base_url() ?>index.php/orcamentos/adicionarProduto" method="post">
                                    <div class="span6">
                                        <input type="hidden" name="idProduto" id="idProduto" />
                                        <input type="hidden" name="idOrcamentoProduto" id="idOrcamentoProduto" value="<?php echo $result->idOrcamento; ?>" />
                                        <input type="hidden" name="estoque" id="estoque" value="" />
                                        <label for="">Produto</label>
                                        <input type="text" class="span12" name="produto" id="produto" placeholder="Digite o nome do produto" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preco</label>
                                        <input type="text" placeholder="Preco" id="preco" name="preco" class="span12 money" data-affixes-stay="true" data-thousands="" data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade</label>
                                        <input type="text" placeholder="Quantidade" id="quantidade" name="quantidade" class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarProduto">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span> <span class="button__text2">Adicionar</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="span12" id="divProdutos" style="margin-left: 0">
                                <table class="table table-bordered" id="tblProdutos">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th width="8%">Quantidade</th>
                                            <th width="10%">Preco</th>
                                            <th width="6%">Acoes</th>
                                            <th width="10%">Sub-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos as $p) {
                                            $preco = $p->preco ?: $p->precoVenda;
                                            echo '<tr>';
                                            echo '<td>' . $p->descricao . '</td>';
                                            echo '<td><div align="center">' . $p->quantidade . '</div></td>';
                                            echo '<td><div align="center">R$ ' . number_format($preco, 2, ',', '.') . '</div></td>';
                                            echo '<td><div align="center"><a href="#" class="produto btn-nwe4" idAcao="' . $p->idOrcamentoProduto . '" title="Excluir Produto"><i class="bx bx-trash-alt"></i></a></div></td>';
                                            echo '<td><div align="center">R$ ' . number_format($p->subTotal, 2, ',', '.') . '</div></td>';
                                            echo '</tr>';
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="text-align: right"><strong>Total:</strong></td>
                                            <td>
                                                <div align="center"><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong></div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab4">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formServicos" action="<?php echo base_url() ?>index.php/orcamentos/adicionarServico" method="post">
                                    <div class="span6">
                                        <input type="hidden" name="idServico" id="idServico" />
                                        <input type="hidden" name="idOrcamentoServico" id="idOrcamentoServico" value="<?php echo $result->idOrcamento; ?>" />
                                        <label for="">Servico</label>
                                        <input type="text" class="span12" name="servico" id="servico" placeholder="Digite o nome do servico" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preco</label>
                                        <input type="text" placeholder="Preco" id="preco_servico" name="preco" class="span12 money" data-affixes-stay="true" data-thousands="" data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade</label>
                                        <input type="text" placeholder="Quantidade" id="quantidade_servico" name="quantidade" class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarServico">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span> <span class="button__text2">Adicionar</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="span12" id="divServicos" style="margin-left: 0">
                                <table class="table table-bordered" id="tblServicos">
                                    <thead>
                                        <tr>
                                            <th>Servico</th>
                                            <th width="8%">Quantidade</th>
                                            <th width="10%">Preco</th>
                                            <th width="6%">Acoes</th>
                                            <th width="10%">Sub-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($servicos as $s) {
                                            $preco = $s->preco ?: $s->precoVenda;
                                            $quantidade = $s->quantidade ?: 1;
                                            $subTotal = $preco * $quantidade;
                                            $nome = $s->nome ?: $s->servico;
                                            echo '<tr>';
                                            echo '<td>' . $nome . '</td>';
                                            echo '<td><div align="center">' . $quantidade . '</div></td>';
                                            echo '<td><div align="center">R$ ' . number_format($preco, 2, ',', '.') . '</div></td>';
                                            echo '<td><div align="center"><a href="#" class="servico btn-nwe4" idAcao="' . $s->idOrcamentoServico . '" title="Excluir Servico"><i class="bx bx-trash-alt"></i></a></div></td>';
                                            echo '<td><div align="center">R$ ' . number_format($subTotal, 2, ',', '.') . '</div></td>';
                                            echo '</tr>';
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="text-align: right"><strong>Total:</strong></td>
                                            <td>
                                                <div align="center"><strong>R$ <?php echo number_format($totalServicos, 2, ',', '.'); ?></strong></div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                &nbsp
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    function normalizarNumero(valor) {
        var texto = (valor || '').toString().trim();
        if (texto.indexOf(',') !== -1) {
            texto = texto.replace(/\./g, '');
            texto = texto.replace(',', '.');
        }
        return texto;
    }

    $("#quantidade, #quantidade_servico").keyup(function() {
        this.value = this.value.replace(/[^0-9.,]/g, '');
    });

    function calcDesconto(valor, desconto, tipoDesconto) {
        var resultado = 0;
        if (tipoDesconto == 'real') {
            resultado = valor - desconto;
        }
        if (tipoDesconto == 'porcento') {
            resultado = (valor - desconto * valor / 100).toFixed(2);
        }
        return resultado;
    }

    function validarDesconto(resultado, valor) {
        if (resultado == valor) {
            return resultado = "";
        }
        return resultado.toFixed(2);
    }

    var valorBackup = $("#valorTotal").val();

    $("#desconto").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#valorTotal").val() == null || $("#valorTotal").val() == '') {
            $('#errorAlert').text('Valor nao pode ser apagado.').css("display", "inline").fadeOut(5000);
            $('#desconto').val('');
            $('#resultado').val('');
            $("#valorTotal").val(valorBackup);
            $("#desconto").focus();
        } else if (Number($("#desconto").val()) >= 0) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val()), $("#tipoDesconto").val()));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        } else {
            $('#errorAlert').text('Erro desconhecido.').css("display", "inline").fadeOut(5000);
            $('#desconto').val('');
            $('#resultado').val('');
        }
    });

    $('#tipoDesconto').on('change', function() {
        if (Number($("#desconto").val()) >= 0) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val()), $("#tipoDesconto").val()));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });

    $("#formDesconto").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            beforeSend: function() {
                Swal.fire({
                    title: 'Processando',
                    text: 'Registrando desconto...',
                    icon: 'info',
                    showCloseButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            },
            success: function(response) {
                if (response.result) {
                    Swal.fire({
                        type: "success",
                        title: "Sucesso",
                        text: "Desconto aplicado com sucesso."
                    });
                } else {
                    Swal.fire({
                        type: "error",
                        title: "Atencao",
                        text: "Ocorreu um erro ao aplicar desconto."
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    type: "error",
                    title: "Atencao",
                    text: response.responseJSON.messages
                });
            }
        });
    });

    $(document).ready(function() {
        $(".money").maskMoney();

        $("#produto").autocomplete({
            source: "<?php echo base_url(); ?>index.php/orcamentos/autoCompleteProduto",
            minLength: 2,
            select: function(event, ui) {
                $("#idProduto").val(ui.item.id);
                $("#estoque").val(ui.item.estoque);
                $("#preco").val(ui.item.preco);
                $("#quantidade").focus();
            }
        });

        $("#servico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/orcamentos/autoCompleteServico",
            minLength: 2,
            select: function(event, ui) {
                $("#idServico").val(ui.item.id);
                $("#preco_servico").val(ui.item.preco);
                $("#quantidade_servico").focus();
            }
        });

        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/orcamentos/autoCompleteCliente",
            minLength: 2,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });

        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/orcamentos/autoCompleteUsuario",
            minLength: 2,
            select: function(event, ui) {
                $("#usuarios_id").val(ui.item.id);
            }
        });

        $("#formOrcamento").validate({
            rules: {
                cliente: {
                    required: true
                },
                tecnico: {
                    required: true
                },
                dataCriacao: {
                    required: true
                }
            },
            messages: {
                cliente: {
                    required: 'Campo Requerido.'
                },
                tecnico: {
                    required: 'Campo Requerido.'
                },
                dataCriacao: {
                    required: 'Campo Requerido.'
                }
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

        $("#formProdutos").validate({
            rules: {
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                }
            },
            messages: {
                preco: {
                    required: 'Insira o preco'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                }
            },
            submitHandler: function(form) {
                var quantidade = parseFloat(normalizarNumero($("#quantidade").val())) || 0;
                var estoque = parseFloat(normalizarNumero($("#estoque").val())) || 0;

                <?php if (! $configuration['control_estoque']) {
                    echo 'estoque = 1000000';
                } ?>

                if (estoque < quantidade) {
                    Swal.fire({
                        type: "warning",
                        title: "Atencao",
                        text: "Voce nao possui estoque suficiente."
                    });
                } else {
                    $("#quantidade").val(normalizarNumero($("#quantidade").val()));
                    var dados = $(form).serialize();
                    $("#divProdutos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/orcamentos/adicionarProduto",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data.result == true) {
                                $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                                $("#quantidade").val('');
                                $("#preco").val('');
                                $("#resultado").val('');
                                $("#desconto").val('');
                                $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                                $("#produto").val('').focus();
                            } else {
                                Swal.fire({
                                    type: "error",
                                    title: "Atencao",
                                    text: "Ocorreu um erro ao tentar adicionar produto."
                                });
                            }
                        }
                    });
                    return false;
                }
            }
        });

        $("#formServicos").validate({
            rules: {
                servico: {
                    required: true
                },
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                }
            },
            messages: {
                servico: {
                    required: 'Insira um servico'
                },
                preco: {
                    required: 'Insira o preco'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                }
            },
            submitHandler: function(form) {
                $("#quantidade_servico").val(normalizarNumero($("#quantidade_servico").val()));
                var dados = $(form).serialize();
                $("#divServicos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/orcamentos/adicionarServico",
                    data: dados,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divServicos").load("<?php echo current_url(); ?> #divServicos");
                            $("#quantidade_servico").val('');
                            $("#preco_servico").val('');
                            $("#resultado").val('');
                            $("#desconto").val('');
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#servico").val('').focus();
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atencao",
                                text: "Ocorreu um erro ao tentar adicionar servico."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(document).on('click', '.produto', function(event) {
            event.preventDefault();
            var idProduto = $(this).attr('idAcao');
            var idOrcamento = "<?php echo $result->idOrcamento ?>";
            if ((idProduto % 1) == 0) {
                $("#divProdutos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/orcamentos/excluirProduto",
                    data: "idProduto=" + idProduto + "&idOrcamento=" + idOrcamento,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#resultado").val('');
                            $("#desconto").val('');
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atencao",
                                text: "Ocorreu um erro ao tentar excluir produto."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(document).on('click', '.servico', function(event) {
            event.preventDefault();
            var idServico = $(this).attr('idAcao');
            var idOrcamento = "<?php echo $result->idOrcamento ?>";
            if ((idServico % 1) == 0) {
                $("#divServicos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/orcamentos/excluirServico",
                    data: "idServico=" + idServico + "&idOrcamento=" + idOrcamento,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            $("#divServicos").load("<?php echo current_url(); ?> #divServicos");
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#resultado").val('');
                            $("#desconto").val('');
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atencao",
                                text: "Ocorreu um erro ao tentar excluir servico."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
</script>
