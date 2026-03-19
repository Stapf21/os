<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatorio PMOC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1f2d3d;
        }
        .header {
            margin-bottom: 24px;
            border-bottom: 3px solid #1f5e7a;
            padding-bottom: 12px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #506273;
        }
        .block {
            margin-bottom: 24px;
        }
        .block h3 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #1f5e7a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border: 1px solid #d7dee6;
            padding: 8px;
            vertical-align: top;
        }
        th {
            background: #f3f7fa;
            text-align: left;
        }
        .status-ok {
            color: #0b8d58;
            font-weight: bold;
        }
        .status-alerta {
            color: #c0392b;
            font-weight: bold;
        }
        .muted {
            color: #687a8c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatorio PMOC</h1>
        <p>Gerado em <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <div class="block">
        <h3>Plano</h3>
        <table>
            <tr>
                <th>Cliente</th>
                <td><?php echo htmlspecialchars($plano->nomeCliente); ?></td>
                <th>Frequencia</th>
                <td><?php echo htmlspecialchars(ucfirst($plano->frequencia)); ?></td>
            </tr>
            <tr>
                <th>Tecnico Responsavel</th>
                <td><?php echo htmlspecialchars($plano->tecnico_nome); ?></td>
                <th>ART</th>
                <td><?php echo htmlspecialchars($plano->art_numero); ?></td>
            </tr>
            <tr>
                <th>Validade ART</th>
                <td><?php echo !empty($plano->art_validade) ? date('d/m/Y', strtotime($plano->art_validade)) : '-'; ?></td>
                <th>Local</th>
                <td><?php echo htmlspecialchars($plano->local); ?></td>
            </tr>
        </table>
    </div>

    <div class="block">
        <h3>Execucoes / Checklists</h3>
        <?php if (empty($checklists)) { ?>
            <p class="muted">Nenhuma execucao registrada para este plano.</p>
        <?php } ?>

        <?php
        $itens = [
            'Limpeza de filtros' => 'limpeza_filtros',
            'Carga de gas' => 'carga_gas',
            'Condicoes do isolamento' => 'condicoes_isolamento',
            'Estado da serpentina' => 'estado_serpentina',
            'Bandeja de condensado' => 'bandeja_condensado',
            'Fiacao e conexoes' => 'fiacao_conexoes',
            'Dreno' => 'dreno',
            'Painel eletrico' => 'painel_eletrico',
            'Grelhas e difusores' => 'grelhas_difusores',
            'Ruidos anormais' => 'ruidos_anormais',
            'Bomba de drenagem' => 'bomba_drenagem',
            'Controle / termostato' => 'controle_termostato',
            'Vazamentos identificados' => 'vazamentos_identificados',
        ];
        ?>

        <?php foreach ($checklists as $checklist) { ?>
            <table>
                <tr>
                    <th colspan="4">
                        OS PMOC #<?php echo $checklist->idOsPmoc; ?>
                        | Equipamento: <?php echo htmlspecialchars($checklist->equipamento_descricao ?: 'Nao identificado'); ?>
                        | Data: <?php echo date('d/m/Y H:i', strtotime($checklist->data_verificacao)); ?>
                    </th>
                </tr>
                <tr>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Modelo</th>
                    <th>Numero de serie</th>
                </tr>
                <?php foreach ($itens as $label => $campo) {
                    $valor = $checklist->$campo ?? '';
                    $classe = in_array(strtolower((string) $valor), ['ok', 'conforme', 'sim', 'bom']) ? 'status-ok' : 'status-alerta';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($label); ?></td>
                        <td class="<?php echo $valor ? $classe : 'muted'; ?>"><?php echo $valor ? htmlspecialchars($valor) : 'Nao informado'; ?></td>
                        <td><?php echo htmlspecialchars($checklist->equipamento_modelo ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($checklist->equipamento_num_serie ?: '-'); ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>Observacoes</th>
                    <td colspan="3"><?php echo !empty($checklist->observacoes) ? nl2br(htmlspecialchars($checklist->observacoes)) : '<span class="muted">Sem observacoes.</span>'; ?></td>
                </tr>
            </table>
        <?php } ?>
    </div>
</body>
</html>
