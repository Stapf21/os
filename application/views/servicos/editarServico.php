<div class="row-fluid" style="margin-top:0">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-wrench"></i>
                </span>
                <h5>Editar Serviço</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formServico" method="post" class="form-horizontal">
                    <?php echo form_hidden('idServicos', $result->idServicos) ?>
                    <div class="control-group">
                        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo $result->nome ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="preco" class="control-label"><span class="required">Preço*</span></label>
                        <div class="controls">
                            <input id="preco" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="preco" value="<?php echo $result->preco ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="descricao" class="control-label">Descrição</label>
                        <div class="controls">
                            <input id="descricao" type="text" name="descricao" value="<?php echo $result->descricao ?>" />
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary" style="max-width: 160px"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/servicos" id="btnAdicionar" class="button btn btn-mini btn-warning" style="max-width: 160px">
                                  <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="widget-box" style="margin-top: 12px;">
        <div class="widget-title">
            <span class="icon">
                <i class="fas fa-boxes"></i>
            </span>
            <h5>Itens padrao do servico</h5>
        </div>
        <div class="widget-content">
            <form id="formItensServico" action="<?php echo base_url(); ?>index.php/servicos/adicionarItem" method="post">
                <input type="hidden" name="idServico" id="idServico" value="<?php echo $result->idServicos; ?>" />
                <input type="hidden" name="idProduto" id="idProduto" />
                <div class="span4">
                    <label for="produto_item">Produto</label>
                    <input type="text" class="span12" name="produto" id="produto_item" placeholder="Digite o nome do produto" />
                </div>
                <div class="span2">
                    <label for="quantidade_item">Quantidade</label>
                    <input type="text" class="span12" name="quantidade" id="quantidade_item" placeholder="0,000" />
                </div>
                <div class="span2">
                    <label for="unidade_item">Unidade</label>
                    <select id="unidade_item" name="unidade" class="span12"></select>
                </div>
                <div class="span3">
                    <label for="observacao_item">Observacao</label>
                    <input type="text" class="span12" name="observacao" id="observacao_item" placeholder="Ex.: bitola 1/4" />
                </div>
                <div class="span1">
                    <label for="editavel_item">Editar</label>
                    <input type="checkbox" id="editavel_item" name="editavel" value="1" checked />
                </div>
                <div class="span12" style="margin-top: 8px;">
                    <button class="button btn btn-success" id="btnAdicionarItem">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span> <span class="button__text2">Adicionar item</span>
                    </button>
                </div>
            </form>
            <div class="span12" id="divItensServico" style="margin-left: 0; margin-top: 10px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th width="10%">Qtd</th>
                            <th width="10%">Unidade</th>
                            <th>Observacao</th>
                            <th width="8%">Editavel</th>
                            <th width="6%">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($itens) && $itens) { foreach ($itens as $item) {
                            $unidade = $item->unidade ?: $item->unidade_produto;
                            echo '<tr>';
                            echo '<td>' . $item->descricao . '</td>';
                            echo '<td><div align="center">' . number_format((float) $item->quantidade, 3, ',', '.') . '</div></td>';
                            echo '<td><div align="center">' . $unidade . '</div></td>';
                            echo '<td>' . $item->observacao . '</td>';
                            echo '<td><div align="center">' . ($item->editavel ? 'Sim' : 'Nao') . '</div></td>';
                            echo '<td><div align="center"><a href="#" class="item-servico btn-nwe4" data-id="' . $item->idServicoItem . '" title="Excluir item"><i class="bx bx-trash-alt"></i></a></div></td>';
                            echo '</tr>';
                        }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
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

    $(document).ready(function() {
        $(".money").maskMoney();
        $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
            for (i in data.medidas) {
                $('#unidade_item').append(new Option(data.medidas[i].descricao, data.medidas[i].sigla));
            }
        });

        $("#produto_item").autocomplete({
            source: "<?php echo base_url(); ?>index.php/servicos/autoCompleteProduto",
            minLength: 2,
            select: function(event, ui) {
                $("#idProduto").val(ui.item.id);
                if (ui.item.unidade) {
                    $("#unidade_item").val(ui.item.unidade);
                }
                $("#quantidade_item").focus();
            }
        });

        $("#quantidade_item").keyup(function() {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });

        $("#formItensServico").submit(function(e) {
            e.preventDefault();
            $("#quantidade_item").val(normalizarNumero($("#quantidade_item").val()));
            var dados = $(this).serialize();
            $("#divItensServico").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/servicos/adicionarItem",
                data: dados,
                dataType: 'json',
                success: function(data) {
                    if (data.result == true) {
                        $("#divItensServico").load("<?php echo current_url(); ?> #divItensServico");
                        $("#produto_item").val('');
                        $("#idProduto").val('');
                        $("#quantidade_item").val('');
                        $("#observacao_item").val('');
                        $("#produto_item").focus();
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atencao",
                            text: "Ocorreu um erro ao adicionar item."
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        type: "error",
                        title: "Atencao",
                        text: "Verifique os dados do item."
                    });
                }
            });
        });

        $(document).on('click', '.item-servico', function(event) {
            event.preventDefault();
            var idItem = $(this).data('id');
            if (!idItem) {
                return;
            }
            $("#divItensServico").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/servicos/excluirItem",
                data: "idItem=" + idItem,
                dataType: 'json',
                success: function(data) {
                    if (data.result == true) {
                        $("#divItensServico").load("<?php echo current_url(); ?> #divItensServico");
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atencao",
                            text: "Ocorreu um erro ao excluir item."
                        });
                    }
                }
            });
        });

        $('#formServico').validate({
            rules: {
                nome: {
                    required: true
                },
                preco: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: 'Campo Requerido.'
                },
                preco: {
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
    });
</script>
