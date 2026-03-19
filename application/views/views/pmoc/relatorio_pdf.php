<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório PMOC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 200px;
        }
        .info-block {
            margin-bottom: 20px;
        }
        .info-block h3 {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo base_url('assets/uploads/logo.png'); ?>" alt="Logo">
        <h1>Relatório de Manutenção PMOC</h1>
    </div>

    <div class="info-block">
        <h3>Informações do Plano</h3>
        <table>
            <tr>
                <th>Cliente</th>
                <td><?php echo $plano->nomeCliente; ?></td>
                <th>Frequência</th>
                <td><?php echo $plano->frequencia; ?></td>
            </tr>
            <tr>
                <th>Técnico Responsável</th>
                <td><?php echo $plano->tecnico_nome; ?></td>
                <th>ART</th>
                <td><?php echo $plano->art_numero; ?></td>
            </tr>
            <tr>
                <th>Validade ART</th>
                <td><?php echo date('d/m/Y', strtotime($plano->art_validade)); ?></td>
                <th>Local</th>
                <td><?php echo $plano->local; ?></td>
            </tr>
        </table>
    </div>

    <div class="info-block">
        <h3>Checklists Realizados</h3>
        <?php foreach ($checklists as $checklist) { ?>
            <h4>OS #<?php echo $checklist->idOs; ?> - <?php echo date('d/m/Y', strtotime($checklist->data_verificacao)); ?></h4>
            <table>
                <tr>
                    <th>Equipamento</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Observações</th>
                </tr>
                <?php 
                $items = json_decode($checklist->items, true);
                $status = json_decode($checklist->status, true);
                $observacoes = json_decode($checklist->observacoes, true);
                
                foreach ($items as $item => $value) {
                    echo "<tr>";
                    echo "<td>{$checklist->nome}</td>";
                    echo "<td>$item</td>";
                    echo "<td>{$status[$item]}</td>";
                    echo "<td>{$observacoes[$item]}</td>";
                    echo "</tr>";
                }
                ?>
            </table>

            <?php if (!empty($checklist->foto_antes) || !empty($checklist->foto_depois)) { ?>
                <div style="margin: 20px 0;">
                    <?php if (!empty($checklist->foto_antes)) { ?>
                        <div style="display: inline-block; margin-right: 20px;">
                            <p><strong>Foto Antes:</strong></p>
                            <img src="<?php echo base_url('assets/uploads/pmoc/' . $checklist->foto_antes); ?>" style="max-width: 300px;">
                        </div>
                    <?php } ?>
                    <?php if (!empty($checklist->foto_depois)) { ?>
                        <div style="display: inline-block;">
                            <p><strong>Foto Depois:</strong></p>
                            <img src="<?php echo base_url('assets/uploads/pmoc/' . $checklist->foto_depois); ?>" style="max-width: 300px;">
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema em <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Documento válido sem assinatura digital</p>
    </div>
</body>
</html> 