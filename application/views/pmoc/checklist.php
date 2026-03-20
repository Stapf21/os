<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Estrutura dos itens do checklist PMOC
$itens_checklist_pmoc = array(
    'Limpeza de filtros' => array('Pendente', 'OK', 'Não Aplicável'),
    'Verificação da carga de gás' => array('Pendente', 'OK', 'Baixo'),
    'Condições do isolamento' => array('Pendente', 'OK'),
    'Estado da serpentina' => array('OK', 'Suja', 'Danificada'),
    'Bandeja de condensado' => array('OK', 'Com água', 'Suja'),
    'Fiação e conexões elétricas' => array('OK', 'Com falhas'),
    'Dreno (livre e funcional)' => array('OK', 'Entupido'),
    'Painel elétrico' => array('OK', 'Falha detectada'),
    'Grelhas e difusores' => array('OK', 'Sujos', 'Danificados'),
    'Ruídos anormais' => array('Sim', 'Não'),
    'Bomba de drenagem (se aplicável)' => array('OK', 'Inoperante'),
    'Controle/termostato' => array('Funcional', 'Defeituoso'),
    'Vazamentos identificados' => array('Sim', 'Não'),
    'Observações adicionais' => 'texto_livre',
    'Foto Antes / Depois' => 'upload'
);

$mapa_names = [
    'Limpeza de filtros' => 'limpeza_filtros',
    'Verificação da carga de gás' => 'carga_gas',
    'Condições do isolamento' => 'condicoes_isolamento',
    'Estado da serpentina' => 'estado_serpentina',
    'Bandeja de condensado' => 'bandeja_condensado',
    'Fiação e conexões elétricas' => 'fiacao_conexoes',
    'Dreno (livre e funcional)' => 'dreno',
    'Painel elétrico' => 'painel_eletrico',
    'Grelhas e difusores' => 'grelhas_difusores',
    'Ruídos anormais' => 'ruidos_anormais',
    'Bomba de drenagem (se aplicável)' => 'bomba_drenagem',
    'Controle/termostato' => 'controle_termostato',
    'Vazamentos identificados' => 'vazamentos_identificados',
    'Observações adicionais' => 'observacoes',
    'Foto Antes / Depois' => 'foto_antes_depois'
];

$osNumero = (int) ($os->idOsPmoc ?? $os->idOs ?? 0);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Checklist PMOC
            <small>Verificação de Equipamentos</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>pmoc">Planos PMOC</a></li>
            <li class="active">Checklist</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Checklist - OS #<?php echo $osNumero; ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="fas fa-clipboard-check"></i></span>
                                <h5>Checklist Técnico PMOC</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <form action="<?php echo base_url('pmoc/salvarChecklist'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                                    <input type="hidden" name="os_id" value="<?= $osNumero ?>">
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group">
                                                <label class="control-label">Equipamento</label>
                                                <div class="controls">
                                                    <select name="equipamento_id" class="span12" required>
                                                        <option value="">Selecione</option>
                                                        <?php foreach ($equipamentos as $eq): ?>
                                                            <?php
                                                                $desc = (string) ($eq->descricao ?? $eq->equipamento ?? 'Equipamento');
                                                                $modelo = (string) ($eq->modelo ?? '');
                                                            ?>
                                                            <option value="<?= (int) $eq->idEquipamentos ?>">
                                                                <?= htmlspecialchars($desc) ?><?= $modelo !== '' ? ' (' . htmlspecialchars($modelo) . ')' : '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <?php foreach ($itens_checklist_pmoc as $item => $tipo): ?>
                                        <div class="row-fluid">
                                            <div class="span12">
                                                <div class="control-group">
                                                    <label class="control-label" style="font-weight:600; color:#222e3c;"> <?= $item ?> </label>
                                                    <div class="controls">
                                                        <?php if (is_array($tipo)): ?>
                                                            <div style="display:flex; gap:10px; align-items:center;">
                                                                <select name="<?= $mapa_names[$item] ?>" class="span6" required>
                                                                    <option value="">Selecione</option>
                                                                    <?php foreach ($tipo as $op): ?>
                                                                        <option value="<?= $op ?>"><?= $op ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <div style="flex:1;">
                                                                    <input type="file" name="<?= $mapa_names[$item] ?>_fotos[]" accept="image/*" multiple class="span12" style="margin-top:0;">
                                                                    <small style="color:#666; font-size:11px;">Você pode selecionar várias fotos</small>
                                                                </div>
                                                            </div>
                                                        <?php elseif ($tipo === 'texto_livre'): ?>
                                                            <textarea name="<?= $mapa_names[$item] ?>" class="span8" rows="2" placeholder="Descreva observações, recomendações ou não conformidades..."></textarea>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <hr>
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group">
                                                <label class="control-label" style="font-weight:600; color:#222e3c;">Status da OS</label>
                                                <div class="controls">
                                                    <select name="status_os" class="span6" required>
                                                        <option value="">Selecione o status</option>
                                                        <option value="em andamento">Em andamento</option>
                                                        <option value="concluido">Concluído</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar Checklist</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 
