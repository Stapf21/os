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
$totalComDesconto = floatval($result->valor_desconto) > 0 ? $result->valor_desconto : $totalOrcamento;
$descontoLabel = 'R$ ' . number_format(floatval($result->desconto), 2, ',', '.');
if ($result->tipo_desconto === 'porcento') {
    $descontoLabel = number_format(floatval($result->desconto), 2, ',', '.') . ' %';
}
?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>Visualizar Orcamento</h5>
                <div class="buttons">
                    <a title="Editar Orcamento" class="button btn btn-mini btn-primary" href="<?php echo site_url() ?>/orcamentos/editar/<?php echo $result->idOrcamento; ?>">
                        <span class="button__icon"><i class="bx bx-edit"></i></span><span class="button__text">Editar</span>
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
            <div class="widget-content">
                <div class="span12" style="padding: 1%; margin-left: 0">
                    <h3>Orcamento: <?php echo $result->idOrcamento ?></h3>
                    <div class="span2" style="margin-left: 0">
                        <label>Data Criacao</label>
                        <input class="span12" type="text" readonly value="<?php echo $dataCriacao; ?>" />
                    </div>
                    <div class="span2">
                        <label>Validade</label>
                        <input class="span12" type="text" readonly value="<?php echo $validade; ?>" />
                    </div>
                    <div class="span4">
                        <label>Cliente</label>
                        <input class="span12" type="text" readonly value="<?php echo $result->nomeCliente; ?>" />
                    </div>
                    <div class="span4">
                        <label>Responsavel</label>
                        <input class="span12" type="text" readonly value="<?php echo $result->nome; ?>" />
                    </div>
                </div>
                <div class="span12" style="padding: 1%; margin-left: 0">
                    <div class="span3">
                        <label>Status</label>
                        <input class="span12" type="text" readonly value="<?php echo $result->status; ?>" />
                    </div>
                </div>
                <div class="span6" style="padding: 1%; margin-left: 0">
                    <label><h4>Observacoes</h4></label>
                    <textarea class="span12" rows="6" readonly><?php echo $result->observacoes; ?></textarea>
                </div>
                <div class="span6" style="padding: 1%; margin-left: 0">
                    <label><h4>Condicoes</h4></label>
                    <textarea class="span12" rows="6" readonly><?php echo $result->condicoes; ?></textarea>
                </div>
                <div class="span12" style="padding: 1%; margin-left: 0">
                    <div class="span6" style="margin-left: 0">
                        <h4>Produtos</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th width="10%">Qtd</th>
                                    <th width="15%">Preco</th>
                                    <th width="15%">Sub-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $p) {
                                    $preco = $p->preco ?: $p->precoVenda;
                                    echo '<tr>';
                                    echo '<td>' . $p->descricao . '</td>';
                                    echo '<td>' . $p->quantidade . '</td>';
                                    echo '<td>R$ ' . number_format($preco, 2, ',', '.') . '</td>';
                                    echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                    echo '</tr>';
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="span6">
                        <h4>Servicos</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Servico</th>
                                    <th width="10%">Qtd</th>
                                    <th width="15%">Preco</th>
                                    <th width="15%">Sub-total</th>
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
                                    echo '<td>' . $quantidade . '</td>';
                                    echo '<td>R$ ' . number_format($preco, 2, ',', '.') . '</td>';
                                    echo '<td>R$ ' . number_format($subTotal, 2, ',', '.') . '</td>';
                                    echo '</tr>';
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="span12" style="padding: 1%; margin-left: 0">
                    <div class="span3">
                        <label>Total Produtos</label>
                        <input class="span12" type="text" readonly value="R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?>" />
                    </div>
                    <div class="span3">
                        <label>Total Servicos</label>
                        <input class="span12" type="text" readonly value="R$ <?php echo number_format($totalServicos, 2, ',', '.'); ?>" />
                    </div>
                    <div class="span3">
                        <label>Desconto</label>
                        <input class="span12" type="text" readonly value="<?php echo $descontoLabel; ?>" />
                    </div>
                    <div class="span3">
                        <label>Total com Desconto</label>
                        <input class="span12" type="text" readonly value="R$ <?php echo number_format($totalComDesconto, 2, ',', '.'); ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
