<?php
$totalProdutos = 0;
$totalServicos = 0;
$dataCriacao = $result->dataCriacao ? date('d/m/Y', strtotime($result->dataCriacao)) : '';
$validade = $result->validade ? date('d/m/Y', strtotime($result->validade)) : '';
$enderecoLinha = implode(', ', array_filter([$result->rua ?? '', $result->numero ?? '', $result->bairro ?? '']));
$localidadeLinha = implode(' - ', array_filter([$result->cidade ?? '', $result->estado ?? '']));
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?> - Orcamento <?= $result->idOrcamento ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap5.3.2.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/imprimir.css">
</head>

<body>
    <div class="main-page">
        <div class="sub-page">
            <header>
                <?php if ($emitente == null) : ?>
                    <div class="alert alert-danger" role="alert">
                        Voce precisa configurar os dados do emitente. >>> <a href="<?= base_url() ?>index.php/mapos/emitente">Configurar</a>
                    </div>
                <?php else : ?>
                    <div class="imgLogo align-middle">
                        <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                    </div>
                    <div class="emitente">
                        <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                        <?php if ($emitente->cnpj != "00.000.000/0000-00") : ?>
                            <span class="align-middle">CNPJ: <?= $emitente->cnpj ?></span></br>
                        <?php endif; ?>
                        <span class="align-middle">
                            <?= $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro ?><br>
                            <?= $emitente->cidade . ' - ' . $emitente->uf . ' - ' . $emitente->cep ?>
                        </span>
                    </div>
                    <div class="contatoEmitente">
                        <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                        <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
                        <span style="word-break: break-word;">Responsavel: <b><?= $result->nome ?></b></span>
                    </div>
                <?php endif; ?>
            </header>
            <section>
                <div class="title">
                    ORCAMENTO #<?= str_pad($result->idOrcamento, 4, 0, STR_PAD_LEFT) ?>
                    <span class="emissao">Emissao: <?= date('d/m/Y H:i:s') ?></span>
                </div>

                <div class="tabela">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="table-secondary">
                                <th class="text-center">STATUS</th>
                                <th class="text-center">DATA CRIACAO</th>
                                <th class="text-center">VALIDADE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center"><?= $result->status ?></td>
                                <td class="text-center"><?= $dataCriacao ?></td>
                                <td class="text-center"><?= $validade ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="subtitle">DADOS DO CLIENTE</div>
                <div class="dados">
                    <div>
                        <span><b><?= $result->nomeCliente ?></b></span><br />
                        <?php if (!empty($result->documento)) : ?>
                            <span>CPF/CNPJ: <?= $result->documento ?></span><br />
                        <?php endif; ?>
                        <?php if (!empty($result->contato) || !empty($result->telefone) || !empty($result->celular)) : ?>
                            <span><?= trim($result->contato . ' ' . $result->telefone) ?><?= !empty($result->telefone) && !empty($result->celular) ? ' / ' : '' ?><?= $result->celular ?></span><br />
                        <?php endif; ?>
                        <?php if (!empty($result->email)) : ?>
                            <span><?= $result->email ?></span><br />
                        <?php endif; ?>
                    </div>
                    <div style="text-align: right;">
                        <?php if (!empty($enderecoLinha)) : ?>
                            <span><?= $enderecoLinha ?></span><br />
                        <?php endif; ?>
                        <?php if (!empty($localidadeLinha)) : ?>
                            <span><?= $localidadeLinha ?></span><br />
                        <?php endif; ?>
                        <?php if (!empty($result->cep)) : ?>
                            <span>CEP: <?= $result->cep ?></span><br />
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($result->observacoes)) : ?>
                    <div class="subtitle">OBSERVACOES</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= $result->observacoes ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($result->condicoes)) : ?>
                    <div class="subtitle">CONDICOES</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= $result->condicoes ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($produtos) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th>PRODUTO(S)</th>
                                    <th class="text-center" width="10%">QTD</th>
                                    <th class="text-center" width="10%">UNT</th>
                                    <th class="text-end" width="15%">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $p) :
                                    $preco = $p->preco ?: $p->precoVenda;
                                    $subTotal = $p->subTotal ?: ($preco * $p->quantidade);
                                    $totalProdutos += $subTotal;
                                    echo '<tr>';
                                    echo '  <td>' . $p->descricao . '</td>';
                                    echo '  <td class="text-center">' . $p->quantidade . '</td>';
                                    echo '  <td class="text-center">' . number_format($preco, 2, ',', '.') . '</td>';
                                    echo '  <td class="text-end">R$ ' . number_format($subTotal, 2, ',', '.') . '</td>';
                                    echo '</tr>';
                                endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><b>TOTAL PRODUTOS:</b></td>
                                    <td class="text-end"><b>R$ <?= number_format($totalProdutos, 2, ',', '.') ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($servicos) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th>SERVICO(S)</th>
                                    <th class="text-center" width="10%">QTD</th>
                                    <th class="text-center" width="10%">UNT</th>
                                    <th class="text-end" width="15%">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($servicos as $s) :
                                    $preco = $s->preco ?: $s->precoVenda;
                                    $quantidade = $s->quantidade ?: 1;
                                    $subTotal = $s->subTotal ?: ($preco * $quantidade);
                                    $totalServicos += $subTotal;
                                    $nomeServico = $s->nome ?: $s->servico;
                                    echo '<tr>';
                                    echo '  <td>' . $nomeServico . '</td>';
                                    echo '  <td class="text-center">' . $quantidade . '</td>';
                                    echo '  <td class="text-center">' . number_format($preco, 2, ',', '.') . '</td>';
                                    echo '  <td class="text-end">R$ ' . number_format($subTotal, 2, ',', '.') . '</td>';
                                    echo '</tr>';
                                endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><b>TOTAL SERVICOS:</b></td>
                                    <td class="text-end"><b>R$ <?= number_format($totalServicos, 2, ',', '.') ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($totalProdutos != 0 || $totalServicos != 0) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th colspan="2">RESUMO DOS VALORES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->valor_desconto != 0) : ?>
                                    <tr>
                                        <td width="65%">SUBTOTAL</td>
                                        <td>R$ <b><?= number_format($totalProdutos + $totalServicos, 2, ',', '.') ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>DESCONTO</td>
                                        <td>R$ <b><?= number_format($result->valor_desconto - ($totalProdutos + $totalServicos), 2, ',', '.') ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL</td>
                                        <td>R$ <?= number_format($result->valor_desconto, 2, ',', '.') ?></td>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <td style="width:290px">TOTAL</td>
                                        <td>R$ <?= number_format($totalProdutos + $totalServicos, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
