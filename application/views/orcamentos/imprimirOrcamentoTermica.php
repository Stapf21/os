<?php
$totalProdutos = 0;
$totalServicos = 0;
$dataCriacao = $result->dataCriacao ? date('d/m/Y', strtotime($result->dataCriacao)) : '';
$validade = $result->validade ? date('d/m/Y', strtotime($result->validade)) : '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Orcamento_<?php echo $result->idOrcamento ?>_<?php echo $result->nomeCliente ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">
    <style>
        .table {
            width: 72mm;
            margin: auto;
        }

        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 80mm;
            min-height: 30cm;
            padding: 2mm;
            margin: 1mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        @page {
            size: auto;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 80mm;
                height: 30cm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid page">
        <table class="table table-condensed">
            <tbody>
                <?php if ($emitente == null) { ?>
                    <tr>
                        <td class="alert" style="font-size: 11px;">
                            Voce precisa configurar os dados do emitente.
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td style="text-align: center; font-size: 11px;">
                            <img src="<?php echo $emitente->url_logo; ?>" style="max-height: 80px"><br>
                            <span style="font-size: 12px; text-transform: uppercase"><b><?php echo $emitente->nome; ?></b></span><br>
                            <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?>
                                <span><?php echo $emitente->cnpj; ?></span><br>
                            <?php } ?>
                            <span><?php echo $emitente->rua . ', ' . $emitente->numero . '</br>' . $emitente->bairro . ', ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span><br>
                            <span><?php echo $emitente->email; ?> - <?php echo $emitente->telefone; ?></span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <table class="table table-condensed">
            <tbody>
                <tr>
                    <td style="font-size: 11px;">
                        <b>CLIENTE</b><br>
                        <?php echo $result->nomeCliente ?><br>
                        <?php if (!empty($result->contato)) : ?>
                            <span><?= $result->contato ?> </span>
                        <?php endif; ?>
                        <?= !empty($result->telefone) ? $result->telefone : "" ?>
                        <?= !empty($result->celular) ? ' / ' . $result->celular : "" ?><br>
                        <?php if (!empty($result->email)) : ?>
                            <span><?php echo $result->email ?></span><br>
                        <?php endif; ?>
                        <?php
                        $retorno_end = array_filter([$result->rua, $result->numero, $result->complemento, $result->bairro]);
                        $endereco = implode(', ', $retorno_end);
                        if (!empty($endereco)) {
                            echo $endereco . '<br>';
                        }
                        if (!empty($result->cidade) || !empty($result->estado) || !empty($result->cep)) {
                            echo "<span>{$result->cidade} - {$result->estado}, {$result->cep}</span><br>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; font-size: 12px;">
                        <b>Orcamento:</b> <?php echo $result->idOrcamento ?>
                        <span style="padding-left: 5%;"><b>Status:</b> <?php echo $result->status ?></span><br>
                        <b>Emissao:</b> <?php echo date('d/m/Y H:i:s') ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-condensed" style="font-size: 11px;">
            <tbody>
                <tr>
                    <td><b>Data Criacao:</b> <?php echo $dataCriacao; ?></td>
                    <td><b>Validade:</b> <?php echo $validade; ?></td>
                </tr>
                <?php if (!empty($result->observacoes)) { ?>
                    <tr>
                        <td colspan="2"><b>Observacoes:</b> <?php echo htmlspecialchars_decode($result->observacoes) ?></td>
                    </tr>
                <?php } ?>
                <?php if (!empty($result->condicoes)) { ?>
                    <tr>
                        <td colspan="2"><b>Condicoes:</b> <?php echo htmlspecialchars_decode($result->condicoes) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if ($produtos) { ?>
            <table style='font-size: 11px;' class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Qtd</th>
                        <th>Produto</th>
                        <th>Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p) {
                        $preco = $p->preco ?: $p->precoVenda;
                        $subTotal = $p->subTotal ?: ($preco * $p->quantidade);
                        $totalProdutos += $subTotal;
                        echo '<tr>';
                        echo '<td>' . $p->quantidade . '</td>';
                        echo '<td>' . $p->descricao . '</td>';
                        echo '<td>R$ ' . number_format($preco, 2, ',', '.') . '</td>';
                        echo '<td>R$ ' . number_format($subTotal, 2, ',', '.') . '</td>';
                        echo '</tr>';
                    } ?>
                    <tr>
                        <td colspan="3" style="text-align: right"><strong>Total:</strong></td>
                        <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>

        <?php if ($servicos) { ?>
            <table style='font-size: 11px;' class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Qtd</th>
                        <th>Servico</th>
                        <th>Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $s) {
                        $preco = $s->preco ?: $s->precoVenda;
                        $quantidade = $s->quantidade ?: 1;
                        $subTotal = $s->subTotal ?: ($preco * $quantidade);
                        $totalServicos += $subTotal;
                        $nomeServico = $s->nome ?: $s->servico;
                        echo '<tr>';
                        echo '<td>' . $quantidade . '</td>';
                        echo '<td>' . $nomeServico . '</td>';
                        echo '<td>R$ ' . number_format($preco, 2, ',', '.') . '</td>';
                        echo '<td>R$ ' . number_format($subTotal, 2, ',', '.') . '</td>';
                        echo '</tr>';
                    } ?>
                    <tr>
                        <td colspan="3" style="text-align: right"><strong>Total:</strong></td>
                        <td><strong>R$ <?php echo number_format($totalServicos, 2, ',', '.'); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>

        <table class="table table-bordered table-condensed">
            <tbody>
                <tr>
                    <td colspan="4">
                        <?php
                        if ($totalProdutos != 0 || $totalServicos != 0) {
                            if ($result->valor_desconto != 0) {
                                echo "<h4 style='text-align: right; font-size: 13px;'>Subtotal: R$ " . number_format($totalProdutos + $totalServicos, 2, ',', '.') . "</h4>";
                                echo "<h4 style='text-align: right; font-size: 13px;'>Desconto: R$ " . number_format($result->valor_desconto - ($totalProdutos + $totalServicos), 2, ',', '.') . "</h4>";
                                echo "<h4 style='text-align: right; font-size: 13px;'>Total: R$ " . number_format($result->valor_desconto, 2, ',', '.') . "</h4>";
                            } else {
                                echo "<h4 style='text-align: right; font-size: 13px;'>Total: R$ " . number_format($totalProdutos + $totalServicos, 2, ',', '.') . "</h4>";
                            }
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered table-condensed" style="font-size: 15px">
            <tbody>
                <tr>
                    <td colspan="4">
                        <b>
                            <p class="text-center">Assinatura do Cliente</p>
                        </b><br />
                        <hr>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
