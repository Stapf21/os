<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .pnl-hero {
        background: linear-gradient(135deg, #0f3557 0%, #1c6a8c 52%, #d5a33a 100%);
        border-radius: 18px;
        color: #fff;
        padding: 28px 30px;
        margin-bottom: 18px;
        box-shadow: 0 12px 28px rgba(20, 44, 74, 0.18);
    }
    .pnl-hero h3 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
    }
    .pnl-hero p {
        margin: 0;
        font-size: 15px;
        opacity: 0.92;
    }
    .pnl-filters {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
        margin: 18px 0 22px;
    }
    .pnl-filter-box {
        min-width: 180px;
    }
    .pnl-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }
    .pnl-summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #e3e9ef;
        box-shadow: 0 8px 24px rgba(16, 37, 63, 0.06);
    }
    .pnl-summary-card small {
        display: block;
        color: #617389;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .pnl-summary-card strong {
        font-size: 26px;
        color: #17324d;
    }
    .pnl-profit-pos {
        color: #11824f;
    }
    .pnl-profit-neg {
        color: #c0392b;
    }
    .pnl-chip {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 999px;
        background: #edf4f8;
        color: #33516d;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<?php
$totalReceitas = 0;
$totalCustos = 0;
$totalLucro = 0;
foreach ($clientesResumo as $cliente) {
    $receitas = (float) $cliente->receitas;
    $custos = (float) $cliente->custos_financeiros + (float) $cliente->custos_diretos;
    $lucro = $receitas - $custos;
    $totalReceitas += $receitas;
    $totalCustos += $custos;
    $totalLucro += $lucro;
}
?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="pnl-hero">
            <h3>P&L por Cliente</h3>
            <p>Receitas, custos financeiros, custos especificos, unidades e ativos agrupados por cliente e periodo.</p>
        </div>

        <div class="pnl-summary-grid">
            <div class="pnl-summary-card">
                <small>Clientes no painel</small>
                <strong><?php echo count($clientesResumo); ?></strong>
            </div>
            <div class="pnl-summary-card">
                <small>Receitas consolidadas</small>
                <strong>R$ <?php echo number_format($totalReceitas, 2, ',', '.'); ?></strong>
            </div>
            <div class="pnl-summary-card">
                <small>Custos consolidados</small>
                <strong>R$ <?php echo number_format($totalCustos, 2, ',', '.'); ?></strong>
            </div>
            <div class="pnl-summary-card">
                <small>Lucro consolidado</small>
                <strong class="<?php echo $totalLucro >= 0 ? 'pnl-profit-pos' : 'pnl-profit-neg'; ?>">
                    R$ <?php echo number_format($totalLucro, 2, ',', '.'); ?>
                </strong>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-title">
                <h5>Painel Consolidado</h5>
            </div>
            <div class="widget-content" style="padding: 18px;">
                <form method="get" class="pnl-filters">
                    <div class="pnl-filter-box">
                        <label for="periodo">Periodo</label>
                        <input type="month" id="periodo" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>" class="span12">
                    </div>
                    <div class="pnl-filter-box">
                        <label for="grupo">Grupo empresarial</label>
                        <input type="text" id="grupo" name="grupo" value="<?php echo htmlspecialchars($grupo); ?>" class="span12" placeholder="Filtrar grupo">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 23px;">Aplicar filtros</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Grupo</th>
                                <th>Receitas</th>
                                <th>Custos</th>
                                <th>Lucro</th>
                                <th>Unidades</th>
                                <th>Ativos</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! $clientesResumo) { ?>
                                <tr>
                                    <td colspan="8">Nenhum cliente encontrado para o periodo informado.</td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($clientesResumo as $cliente) {
                                $custos = (float) $cliente->custos_financeiros + (float) $cliente->custos_diretos;
                                $lucro = (float) $cliente->receitas - $custos;
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($cliente->nomeCliente); ?></strong><br>
                                        <span class="pnl-chip"><?php echo htmlspecialchars($cliente->tipo_consolidacao_pnl ?: 'consolidado'); ?></span>
                                    </td>
                                    <td><?php echo $cliente->grupo_empresarial ? htmlspecialchars($cliente->grupo_empresarial) : '-'; ?></td>
                                    <td>R$ <?php echo number_format((float) $cliente->receitas, 2, ',', '.'); ?></td>
                                    <td>R$ <?php echo number_format($custos, 2, ',', '.'); ?></td>
                                    <td class="<?php echo $lucro >= 0 ? 'pnl-profit-pos' : 'pnl-profit-neg'; ?>">
                                        <strong>R$ <?php echo number_format($lucro, 2, ',', '.'); ?></strong>
                                    </td>
                                    <td><?php echo (int) $cliente->total_unidades; ?></td>
                                    <td><?php echo (int) $cliente->total_ativos; ?></td>
                                    <td>
                                        <a href="<?php echo base_url('pnl/cliente/' . $cliente->idClientes . '?periodo=' . urlencode($periodo)); ?>" class="btn btn-info btn-xs">
                                            Abrir painel
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
