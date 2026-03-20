<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$receita = (float) ($pnlResumo->receitas ?? 0);
$custo = (float) (($pnlResumo->custos_financeiros ?? 0) + ($pnlResumo->custos_diretos ?? 0));
$lucro = $receita - $custo;
$margem = $receita > 0 ? ($lucro / $receita) * 100 : 0;

$statusContrato = mb_strtolower((string) ($plano->status_contrato ?: $plano->status));
$statusContratoClass = 'pmoc-cl-tag-neutral';
if ($statusContrato === 'ativo') {
    $statusContratoClass = 'pmoc-cl-tag-success';
} elseif ($statusContrato === 'suspenso') {
    $statusContratoClass = 'pmoc-cl-tag-warning';
} elseif ($statusContrato === 'inativo') {
    $statusContratoClass = 'pmoc-cl-tag-danger';
}

$reparosResumo = [
    'aberto' => 0,
    'em_andamento' => 0,
    'concluido' => 0,
];
foreach (($reparos ?? []) as $rep) {
    $rs = mb_strtolower((string) ($rep->status ?? ''));
    if (isset($reparosResumo[$rs])) {
        $reparosResumo[$rs]++;
    }
}
?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pmoc-dashcode.css" />

<div class="pmoc-cl">
    <div class="pmoc-cl-head">
        <h3>PMOC e Plano Mensal</h3>
        <p>Acompanhamento do seu contrato de manutencao.</p>
        <div class="pmoc-cl-info">
            <div class="pmoc-cl-chip">Plano <b><?= htmlspecialchars((string) ($plano->nome_plano ?: 'Plano PMOC')) ?></b></div>
            <div class="pmoc-cl-chip">Valor mensal <b>R$ <?= number_format((float) $plano->valor_mensal, 2, ',', '.') ?></b></div>
            <div class="pmoc-cl-chip">Frequencia <b><?= ucfirst((string) ($plano->frequencia_manutencao ?: '-')) ?></b></div>
            <div class="pmoc-cl-chip">Status <b><span class="pmoc-cl-tag <?= $statusContratoClass ?>"><?= ucfirst((string) ($statusContrato ?: '-')) ?></span></b></div>
        </div>
    </div>

    <div class="pmoc-cl-nav">
        <a href="#cl-sec-solicitar" class="pmoc-nav-btn">Solicitar reparo</a>
        <a href="#cl-sec-cronograma" class="pmoc-nav-btn">Cronograma</a>
        <a href="#cl-sec-reparos" class="pmoc-nav-btn">Meus reparos</a>
        <a href="#cl-sec-relatorios" class="pmoc-nav-btn">Relatorios</a>
        <a href="#cl-sec-financeiro" class="pmoc-nav-btn">P&L</a>
    </div>

    <div class="pmoc-cl-kpi">
        <div><div>Pendente</div><div class="n"><?= (int) ($resumo_os['pendente'] ?? 0) ?></div></div>
        <div><div>Agendado</div><div class="n"><?= (int) ($resumo_os['agendado'] ?? 0) ?></div></div>
        <div><div>Em execucao</div><div class="n"><?= (int) ($resumo_os['em_execucao'] ?? 0) ?></div></div>
        <div><div>Concluido</div><div class="n"><?= (int) ($resumo_os['concluido'] ?? 0) ?></div></div>
        <div><div>Atrasado</div><div class="n"><?= (int) ($resumo_os['atrasado'] ?? 0) ?></div></div>
    </div>

    <div class="pmoc-cl-box pmoc-section" id="cl-sec-solicitar">
        <h4>Solicitar reparo</h4>
        <div class="body">
            <form action="<?= base_url('index.php/mine/solicitarReparoPmoc') ?>" method="post" class="pmoc-form">
                <input type="text" name="titulo" placeholder="Titulo" required>
                <select name="cliente_unidade_id">
                    <option value="">Unidade</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= (int) $u->idClienteUnidade ?>"><?= htmlspecialchars((string) $u->nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="equipamento_id">
                    <option value="">Equipamento</option>
                    <?php foreach ($equipamentos as $eq): ?>
                        <option value="<?= (int) $eq->idEquipamentos ?>"><?= htmlspecialchars((string) ($eq->descricao ?: $eq->equipamento)) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="descricao" placeholder="Descricao da necessidade" style="min-width:260px; flex:1;">
                <button class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>

    <div class="pmoc-cl-box pmoc-section" id="cl-sec-cronograma">
        <h4>Cronograma</h4>
        <div class="body">
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar por data ou status..." data-table-search="#cl-tb-cronograma">
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="cl-tb-cronograma">
                    <thead><tr><th>Data prevista</th><th>Status</th><th>Execucao</th></tr></thead>
                    <tbody>
                        <?php foreach ($cronograma as $item): ?>
                            <?php
                                $s = mb_strtolower((string) $item->status);
                                $sClass = 'pmoc-cl-tag-neutral';
                                if ($s === 'concluido') {
                                    $sClass = 'pmoc-cl-tag-success';
                                } elseif ($s === 'atrasado') {
                                    $sClass = 'pmoc-cl-tag-danger';
                                } elseif ($s === 'agendado') {
                                    $sClass = 'pmoc-cl-tag-warning';
                                }
                            ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($item->data_prevista)) ?></td>
                                <td><span class="pmoc-cl-tag <?= $sClass ?>"><?= ucfirst(str_replace('_', ' ', (string) $item->status)) ?></span></td>
                                <td><?= $item->data_execucao ? date('d/m/Y', strtotime($item->data_execucao)) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pmoc-cl-box pmoc-section" id="cl-sec-reparos">
        <h4>Minhas solicitacoes de reparo</h4>
        <div class="body">
            <div class="pmoc-reparo-resumo">
                <span class="pmoc-cl-tag pmoc-cl-tag-warning">Abertos: <?= (int) $reparosResumo['aberto'] ?></span>
                <span class="pmoc-cl-tag pmoc-cl-tag-neutral">Em andamento: <?= (int) $reparosResumo['em_andamento'] ?></span>
                <span class="pmoc-cl-tag pmoc-cl-tag-success">Concluidos: <?= (int) $reparosResumo['concluido'] ?></span>
            </div>
            <div class="pmoc-status-pills" data-status-filter="#cl-tb-reparos">
                <button type="button" class="pmoc-status-pill active" data-status="">Todos</button>
                <button type="button" class="pmoc-status-pill" data-status="aberto">Aberto</button>
                <button type="button" class="pmoc-status-pill" data-status="em_andamento">Em andamento</button>
                <button type="button" class="pmoc-status-pill" data-status="concluido">Concluido</button>
            </div>
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar por titulo, unidade ou equipamento..." data-table-search="#cl-tb-reparos">
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="cl-tb-reparos">
                    <thead><tr><th>Data</th><th>Titulo</th><th>Unidade</th><th>Equipamento</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (empty($reparos)): ?>
                            <tr><td colspan="5">Nenhuma solicitacao registrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reparos as $rep): ?>
                                <?php
                                    $repStatus = mb_strtolower((string) ($rep->status ?: ''));
                                    $repClass = 'pmoc-cl-tag-neutral';
                                    if ($repStatus === 'aberto') {
                                        $repClass = 'pmoc-cl-tag-warning';
                                    } elseif ($repStatus === 'concluido') {
                                        $repClass = 'pmoc-cl-tag-success';
                                    } elseif ($repStatus === 'cancelado') {
                                        $repClass = 'pmoc-cl-tag-danger';
                                    }
                                ?>
                                <tr data-status="<?= htmlspecialchars((string) $repStatus) ?>">
                                    <td><?= !empty($rep->data_solicitacao) ? date('d/m/Y H:i', strtotime($rep->data_solicitacao)) : '-' ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->titulo ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->unidade_nome ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rep->equipamento_descricao ?: '-')) ?></td>
                                    <td><span class="pmoc-cl-tag <?= $repClass ?>"><?= ucfirst(str_replace('_', ' ', (string) ($rep->status ?: '-'))) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pmoc-cl-box pmoc-section" id="cl-sec-relatorios">
        <h4>Relatorios</h4>
        <div class="body">
            <div class="pmoc-search">
                <input type="text" placeholder="Buscar por tecnico, servico ou equipamento..." data-table-search="#cl-tb-relatorios">
            </div>
            <div class="pmoc-table-wrap">
                <table class="table table-bordered table-striped pmoc-table-hover" id="cl-tb-relatorios">
                    <thead><tr><th>Data</th><th>Unidade</th><th>Equipamento</th><th>Tecnico</th><th>Servico</th></tr></thead>
                    <tbody>
                        <?php if (empty($relatorios)): ?>
                            <tr><td colspan="5">Nenhum relatorio.</td></tr>
                        <?php else: ?>
                            <?php foreach ($relatorios as $r): ?>
                                <tr>
                                    <td><?= $r->data_verificacao ? date('d/m/Y H:i', strtotime($r->data_verificacao)) : '-' ?></td>
                                    <td><?= htmlspecialchars((string) ($r->unidade_nome ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->equipamento_descricao ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->tecnico_responsavel ?: '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($r->tipo_servico ?: '-')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pmoc-cl-box pmoc-section" id="cl-sec-financeiro">
        <h4>Painel financeiro (P&L)</h4>
        <div class="body">
            <form method="get" class="pmoc-form" style="margin-bottom:8px;">
                <select name="tipo_periodo" style="width:140px;">
                    <option value="mensal" <?= $tipoPeriodo === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                    <option value="trimestral" <?= $tipoPeriodo === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="anual" <?= $tipoPeriodo === 'anual' ? 'selected' : '' ?>>Anual</option>
                    <option value="personalizado" <?= $tipoPeriodo === 'personalizado' ? 'selected' : '' ?>>Personalizado</option>
                </select>
                <input type="text" name="periodo_referencia" value="<?= htmlspecialchars((string) $periodoReferencia) ?>" placeholder="YYYY-MM / YYYY / YYYY-QX">
                <input type="date" name="data_inicio" value="<?= htmlspecialchars((string) $dataInicio) ?>">
                <input type="date" name="data_fim" value="<?= htmlspecialchars((string) $dataFim) ?>">
                <button class="btn btn-small btn-primary">Filtrar</button>
            </form>
            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                <span><b>Receita:</b> R$ <?= number_format($receita, 2, ',', '.') ?></span>
                <span><b>Custo:</b> R$ <?= number_format($custo, 2, ',', '.') ?></span>
                <span><b>Lucro:</b> R$ <?= number_format($lucro, 2, ',', '.') ?></span>
                <span><b>Margem:</b> <?= number_format($margem, 2, ',', '.') ?>%</span>
            </div>

            <?php if (!empty($resumoUnidadesPnl)): ?>
                <hr>
                <h5>Detalhado por unidade</h5>
                <div class="pmoc-table-wrap">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Unidade</th><th>Receita</th><th>Custos</th><th>Lucro</th></tr></thead>
                        <tbody>
                        <?php foreach ($resumoUnidadesPnl as $ru): ?>
                            <?php $lucroUn = ((float) $ru->receitas) - ((float) $ru->custos_financeiros + (float) $ru->custos_diretos); ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $ru->nome) ?></td>
                                <td>R$ <?= number_format((float) $ru->receitas, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float) $ru->custos_financeiros + (float) $ru->custos_diretos, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($lucroUn, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    function norm(v) {
        return (v || '').toString().toLowerCase();
    }

    document.querySelectorAll('.pmoc-cl .pmoc-nav-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var id = btn.getAttribute('href');
            if (!id || id.charAt(0) !== '#') return;
            var target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    document.querySelectorAll('.pmoc-cl .pmoc-section').forEach(function (section) {
        var head = section.querySelector('h4');
        var body = section.querySelector('.body');
        if (!head || !body) return;
        if (head.querySelector('.pmoc-toggle-btn')) return;
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pmoc-toggle-btn';
        btn.style.marginLeft = '8px';
        btn.textContent = 'Ocultar';
        btn.addEventListener('click', function () {
            var hidden = body.style.display === 'none';
            body.style.display = hidden ? '' : 'none';
            btn.textContent = hidden ? 'Ocultar' : 'Expandir';
        });
        head.appendChild(btn);
    });

    document.querySelectorAll('.pmoc-cl [data-table-search]').forEach(function (input) {
        var table = document.querySelector(input.getAttribute('data-table-search'));
        if (!table) return;
        var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
        input.addEventListener('input', function () {
            var q = norm(input.value);
            rows.forEach(function (tr) {
                var visible = norm(tr.textContent).indexOf(q) > -1;
                tr.style.display = visible ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('.pmoc-cl [data-status-filter]').forEach(function (wrap) {
        var table = document.querySelector(wrap.getAttribute('data-status-filter'));
        if (!table) return;
        var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr[data-status]'));
        wrap.querySelectorAll('.pmoc-status-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                wrap.querySelectorAll('.pmoc-status-pill').forEach(function (p) { p.classList.remove('active'); });
                pill.classList.add('active');
                var wanted = norm(pill.getAttribute('data-status'));
                rows.forEach(function (tr) {
                    var status = norm(tr.getAttribute('data-status'));
                    tr.style.display = (!wanted || status === wanted) ? '' : 'none';
                });
            });
        });
    });

    document.querySelectorAll('.pmoc-cl .n').forEach(function (node) {
        var raw = parseInt((node.textContent || '0').replace(/\D/g, ''), 10);
        if (!isFinite(raw) || raw <= 0) return;
        var i = 0;
        var steps = 18;
        var timer = setInterval(function () {
            i++;
            node.textContent = Math.round((raw * i) / steps);
            if (i >= steps) clearInterval(timer);
        }, 20);
    });
})();
</script>

