<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .pnl-shell {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .pnl-banner {
        background: linear-gradient(135deg, #12385a, #225c78 55%, #f0c05e);
        border-radius: 18px;
        color: #fff;
        padding: 26px 28px;
        box-shadow: 0 12px 28px rgba(10, 31, 51, 0.16);
    }
    .pnl-banner h3 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 700;
    }
    .pnl-banner-meta {
        display: flex;
        gap: 22px;
        flex-wrap: wrap;
        font-size: 14px;
    }
    .pnl-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }
    .pnl-card {
        background: #fff;
        border: 1px solid #e3eaf0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 8px 24px rgba(11, 35, 58, 0.06);
    }
    .pnl-card small {
        display: block;
        color: #667a91;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }
    .pnl-card strong {
        font-size: 26px;
        color: #17324d;
    }
    .pnl-pos {
        color: #0d8d53;
    }
    .pnl-neg {
        color: #c0392b;
    }
    .pnl-toolbar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
        margin-bottom: 14px;
    }
    .pnl-stack {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 16px;
    }
    .pnl-mini-form .control-group {
        margin-bottom: 10px;
    }
    @media (max-width: 980px) {
        .pnl-stack {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12 pnl-shell">
        <div class="pnl-banner">
            <h3><?php echo htmlspecialchars($cliente->nomeCliente); ?></h3>
            <div class="pnl-banner-meta">
                <span>Grupo: <?php echo $cliente->grupo_empresarial ? htmlspecialchars($cliente->grupo_empresarial) : 'Nao informado'; ?></span>
                <span>Consolidacao: <?php echo htmlspecialchars($cliente->tipo_consolidacao_pnl ?: 'consolidado'); ?></span>
                <span>Periodo: <?php echo date('d/m/Y', strtotime($dataInicio)); ?> ate <?php echo date('d/m/Y', strtotime($dataFim)); ?></span>
            </div>
        </div>

        <div class="pnl-grid">
            <div class="pnl-card">
                <small>Receitas</small>
                <strong>R$ <?php echo number_format($resumo->receitas, 2, ',', '.'); ?></strong>
            </div>
            <div class="pnl-card">
                <small>Custos financeiros</small>
                <strong>R$ <?php echo number_format($resumo->custos_financeiros, 2, ',', '.'); ?></strong>
            </div>
            <div class="pnl-card">
                <small>Custos diretos</small>
                <strong>R$ <?php echo number_format($resumo->custos_diretos, 2, ',', '.'); ?></strong>
            </div>
            <div class="pnl-card">
                <small>Lucro / Prejuizo</small>
                <strong class="<?php echo $lucro >= 0 ? 'pnl-pos' : 'pnl-neg'; ?>">
                    R$ <?php echo number_format($lucro, 2, ',', '.'); ?>
                </strong>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-title">
                <h5>Filtros e Exportacao</h5>
            </div>
            <div class="widget-content" style="padding:18px;">
                <form method="get" class="pnl-toolbar">
                    <div>
                        <label for="periodo">Periodo</label>
                        <input type="month" id="periodo" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>">
                    </div>
                    <div>
                        <label for="unidade_id">Unidade</label>
                        <select id="unidade_id" name="unidade_id">
                            <option value="">Todas</option>
                            <?php foreach ($unidades as $unidade) { ?>
                                <option value="<?php echo $unidade->idClienteUnidade; ?>" <?php echo (string) $unidadeId === (string) $unidade->idClienteUnidade ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unidade->nome); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div style="margin-top: 22px;">
                        <button type="submit" class="btn btn-primary">Atualizar painel</button>
                        <a href="<?php echo base_url('pnl/exportar/' . $cliente->idClientes . '?periodo=' . urlencode($periodo)); ?>" class="btn btn-success">Exportar CSV</a>
                        <a href="<?php echo base_url('clientes/visualizar/' . $cliente->idClientes); ?>" class="btn btn-warning">Voltar ao cliente</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="pnl-stack">
            <div>
                <div class="widget-box">
                    <div class="widget-title">
                        <h5>Resumo por Unidade</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Unidade</th>
                                    <th>Receitas</th>
                                    <th>Custos</th>
                                    <th>Lucro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (! $resumoUnidades) { ?>
                                    <tr><td colspan="4">Nenhuma unidade cadastrada para este cliente.</td></tr>
                                <?php } ?>
                                <?php foreach ($resumoUnidades as $item) {
                                    $custosTotal = (float) $item->custos_financeiros + (float) $item->custos_diretos;
                                    $lucroUnidade = (float) $item->receitas - $custosTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item->nome); ?></strong><br>
                                            <small><?php echo htmlspecialchars($item->empresa ?: 'Sem empresa'); ?></small>
                                        </td>
                                        <td>R$ <?php echo number_format((float) $item->receitas, 2, ',', '.'); ?></td>
                                        <td>R$ <?php echo number_format($custosTotal, 2, ',', '.'); ?></td>
                                        <td class="<?php echo $lucroUnidade >= 0 ? 'pnl-pos' : 'pnl-neg'; ?>">
                                            R$ <?php echo number_format($lucroUnidade, 2, ',', '.'); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="widget-box">
                    <div class="widget-title">
                        <h5>Lancamentos Financeiros do Cliente</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descricao</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Classificacao</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (! $lancamentos) { ?>
                                    <tr><td colspan="5">Nenhum lancamento para o periodo filtrado.</td></tr>
                                <?php } ?>
                                <?php foreach ($lancamentos as $lancamento) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(ucfirst($lancamento->tipo)); ?></td>
                                        <td><?php echo htmlspecialchars($lancamento->descricao); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($lancamento->data_vencimento)); ?></td>
                                        <td>R$ <?php echo number_format((float) ($lancamento->valor_desconto ?: $lancamento->valor), 2, ',', '.'); ?></td>
                                        <td style="min-width: 260px;">
                                            <form action="<?php echo base_url('pnl/atualizarLancamento/' . $cliente->idClientes . '/' . $lancamento->idLancamentos); ?>" method="post" style="margin:0; display:flex; gap:8px; flex-wrap:wrap;">
                                                <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>">
                                                <select name="cliente_unidade_id" style="max-width: 120px;">
                                                    <option value="">Sem unidade</option>
                                                    <?php foreach ($unidades as $unidade) { ?>
                                                        <option value="<?php echo $unidade->idClienteUnidade; ?>" <?php echo (string) $lancamento->cliente_unidade_id === (string) $unidade->idClienteUnidade ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($unidade->nome); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <input type="text" name="categoria_pnl" value="<?php echo htmlspecialchars($lancamento->categoria_pnl); ?>" placeholder="Categoria" style="max-width: 110px;">
                                                <button type="submit" class="btn btn-info btn-xs">Salvar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="widget-box">
                    <div class="widget-title">
                        <h5>Estrutura do Cliente</h5>
                    </div>
                    <div class="widget-content" style="padding:18px;">
                        <div class="pnl-mini-form">
                            <form action="<?php echo base_url('pnl/adicionarUnidade/' . $cliente->idClientes); ?>" method="post">
                                <h4>Nova unidade</h4>
                                <div class="control-group">
                                    <label>Nome da unidade</label>
                                    <input type="text" name="nome" class="span12">
                                </div>
                                <div class="control-group">
                                    <label>Empresa</label>
                                    <input type="text" name="empresa" class="span12">
                                </div>
                                <div class="control-group">
                                    <label>Codigo</label>
                                    <input type="text" name="codigo" class="span12">
                                </div>
                                <div class="control-group">
                                    <label>Cidade / Estado</label>
                                    <div style="display:flex; gap:8px;">
                                        <input type="text" name="cidade" class="span9">
                                        <input type="text" name="estado" class="span3" maxlength="2">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Adicionar unidade</button>
                            </form>
                            <hr>
                            <form action="<?php echo base_url('pnl/adicionarAtivo/' . $cliente->idClientes); ?>" method="post">
                                <h4>Novo ativo</h4>
                                <div class="control-group">
                                    <label>Nome</label>
                                    <input type="text" name="nome" class="span12">
                                </div>
                                <div class="control-group">
                                    <label>Tipo</label>
                                    <input type="text" name="tipo" class="span12" placeholder="Maquina, loja, contrato">
                                </div>
                                <div class="control-group">
                                    <label>Identificador</label>
                                    <input type="text" name="identificador" class="span12">
                                </div>
                                <div class="control-group">
                                    <label>Unidade</label>
                                    <select name="cliente_unidade_id" class="span12">
                                        <option value="">Sem unidade</option>
                                        <?php foreach ($unidades as $unidade) { ?>
                                            <option value="<?php echo $unidade->idClienteUnidade; ?>"><?php echo htmlspecialchars($unidade->nome); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="control-group">
                                    <label>Custo mensal estimado</label>
                                    <input type="text" name="custo_mensal_estimado" class="span12" placeholder="0,00">
                                </div>
                                <div class="control-group">
                                    <label>Descricao</label>
                                    <textarea name="descricao" class="span12"></textarea>
                                </div>
                                <button type="submit" class="btn btn-info">Adicionar ativo</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="widget-box">
                    <div class="widget-title">
                        <h5>Custos Especificos</h5>
                    </div>
                    <div class="widget-content" style="padding:18px;">
                        <form action="<?php echo base_url('pnl/adicionarCusto/' . $cliente->idClientes); ?>" method="post" class="pnl-mini-form">
                            <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>">
                            <div class="control-group">
                                <label>Categoria</label>
                                <input type="text" name="categoria" class="span12" placeholder="Insumos, servicos, manutencao">
                            </div>
                            <div class="control-group">
                                <label>Descricao</label>
                                <input type="text" name="descricao" class="span12">
                            </div>
                            <div class="control-group">
                                <label>Tipo de custo</label>
                                <select name="tipo_custo" class="span12">
                                    <option value="insumos">Insumos</option>
                                    <option value="servicos">Servicos</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </div>
                            <div class="control-group">
                                <label>Unidade</label>
                                <select name="cliente_unidade_id" class="span12">
                                    <option value="">Sem unidade</option>
                                    <?php foreach ($unidades as $unidade) { ?>
                                        <option value="<?php echo $unidade->idClienteUnidade; ?>"><?php echo htmlspecialchars($unidade->nome); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="control-group">
                                <label>Ativo</label>
                                <select name="cliente_ativo_id" class="span12">
                                    <option value="">Sem ativo</option>
                                    <?php foreach ($ativos as $ativo) { ?>
                                        <option value="<?php echo $ativo->idClienteAtivo; ?>"><?php echo htmlspecialchars($ativo->nome); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="control-group">
                                <label>Data de referencia</label>
                                <input type="date" name="data_referencia" class="span12" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="control-group">
                                <label>Valor</label>
                                <input type="text" name="valor" class="span12" placeholder="0,00">
                            </div>
                            <div class="control-group">
                                <label>Observacoes</label>
                                <textarea name="observacoes" class="span12"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Registrar custo</button>
                        </form>
                        <hr>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Categoria</th>
                                    <th>Descricao</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (! $custos) { ?>
                                    <tr><td colspan="4">Nenhum custo especifico no periodo.</td></tr>
                                <?php } ?>
                                <?php foreach ($custos as $custo) { ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($custo->data_referencia)); ?></td>
                                        <td><?php echo htmlspecialchars($custo->categoria); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($custo->descricao); ?><br>
                                            <small><?php echo htmlspecialchars($custo->unidade_nome ?: $custo->ativo_nome ?: 'Sem vinculacao adicional'); ?></small>
                                        </td>
                                        <td>R$ <?php echo number_format((float) $custo->valor, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="widget-box">
                    <div class="widget-title">
                        <h5>Ativos Associados</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Unidade</th>
                                    <th>Custo estimado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (! $ativos) { ?>
                                    <tr><td colspan="4">Nenhum ativo associado.</td></tr>
                                <?php } ?>
                                <?php foreach ($ativos as $ativo) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ativo->nome); ?></td>
                                        <td><?php echo htmlspecialchars($ativo->tipo ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($ativo->unidade_nome ?: '-'); ?></td>
                                        <td>R$ <?php echo number_format((float) $ativo->custo_mensal_estimado, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
