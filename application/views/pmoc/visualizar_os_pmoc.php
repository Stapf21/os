<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1 style="font-size:2.2rem; font-weight:800; margin-bottom:8px;">
            Detalhes da OS - <?php echo !empty($equipamentos) ? htmlspecialchars($equipamentos[0]->descricao) : 'Equipamento'; ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Início</a></li>
            <li><a href="<?php echo base_url(); ?>pmoc">Planos PMOC</a></li>
            <li class="active">OS PMOC</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Informações da OS PMOC</h3>
                    </div>
                    <div class="box-body">
                        <div style="margin-bottom:18px;">
                            <?php if (!empty($equipamentos)) {
                                $eq = $equipamentos[0];
                                echo '<div style="font-size:1.1rem; margin-bottom:8px;"><b>Equipamento:</b> ' . htmlspecialchars($eq->descricao) . ' <span style="color:#7b8ca6;">| Modelo: ' . htmlspecialchars($eq->modelo) . ' | Nº Série: ' . htmlspecialchars($eq->num_serie) . '</span></div>';
                            } ?>
                            <table class="table table-condensed" style="margin-bottom:0;">
                                <tr>
                                    <td><b>Status:</b> <span class="label label-<?php echo ($os_pmoc->status == 'concluido') ? 'success' : (($os_pmoc->status == 'em andamento') ? 'warning' : 'default'); ?>"><?php echo ucfirst($os_pmoc->status); ?></span></td>
                                    <td><b>Data Inicial:</b> <?php echo date('d/m/Y', strtotime($os_pmoc->dataInicial)); ?></td>
                                    <td><b>Data Final:</b> <?php echo $os_pmoc->dataFinal ? date('d/m/Y', strtotime($os_pmoc->dataFinal)) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b>Descrição:</b> <?php echo $os_pmoc->descricao; ?></td>
                                </tr>
                            </table>
                        </div>
                        <h4>Checklists Realizados</h4>
                        <a href="<?php echo base_url('pmoc/checklist/' . $os_pmoc->idOsPmoc); ?>" class="btn btn-primary" style="margin-bottom:18px; font-weight:600; border-radius:7px;">Atualizar OS</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Itens Verificados</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($checklists)) { ?>
                                    <tr><td colspan="3" style="text-align:center; color:#7b8ca6;">Nenhum checklist realizado.</td></tr>
                                <?php } else { 
                                    // Ordenar checklists por data_verificacao decrescente
                                    usort($checklists, function($a, $b) {
                                        return strtotime($b->data_verificacao) - strtotime($a->data_verificacao);
                                    });
                                    foreach ($checklists as $c) {
                                ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($c->data_verificacao)); ?> <span style="color:#888; font-size:13px;"><?php echo date('H:i', strtotime($c->data_verificacao)); ?></span></td>
                                        <td>
                                            <?php 
                                            $itens_checklist_pmoc = [
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
                                                'Vazamentos identificados' => 'vazamentos_identificados'
                                            ];
                                            echo '<ul style="list-style:none; padding:0; margin:0;">';
                                            foreach ($itens_checklist_pmoc as $label => $col) {
                                                $status_item = isset($c->$col) && $c->$col !== null ? $c->$col : 'Não verificado';
                                                echo '<li style="margin-bottom:12px; display:flex; align-items:center; gap:10px;">';
                                                echo '<strong>' . htmlspecialchars($label) . ':</strong> ' . htmlspecialchars($status_item);
                                                // Buscar fotos deste item
                                                $fotos = $this->db->where('checklist_id', $c->idChecklist)
                                                                 ->where('campo', $col)
                                                                 ->get('checklist_fotos')
                                                                 ->result();
                                                if (!empty($fotos)) {
                                                    echo '<button type="button" class="btn btn-info btn-xs visualizar-imagem" data-imagens="';
                                                    $img_urls = [];
                                                    foreach ($fotos as $foto) {
                                                        $img_urls[] = base_url('uploads/pmoc/' . $foto->nome_arquivo);
                                                    }
                                                    echo htmlspecialchars(json_encode($img_urls), ENT_QUOTES, 'UTF-8');
                                                    echo '">Visualizar Imagem</button>';
                                                }
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                            ?>
                                        </td>
                                        <td style="vertical-align:top; max-width:320px; white-space:pre-line; color:#222e3c; font-size:15px;">
                                            <?php echo !empty($c->observacoes) ? nl2br(htmlspecialchars($c->observacoes)) : '<span style="color:#aaa;">Nenhuma observação.</span>'; ?>
                                        </td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                        $(function(){
                            $(document).on('click', '.visualizar-imagem', function(e){
                                e.preventDefault();
                                var imagens = $(this).data('imagens');
                                if (typeof imagens === 'string') {
                                    imagens = JSON.parse(imagens);
                                }
                                var html = '';
                                for (var i=0; i<imagens.length; i++) {
                                    html += '<img src="'+imagens[i]+'" style="max-width:220px; max-height:180px; display:block; margin-bottom:8px; border-radius:6px; border:1px solid #e0e7ef;">';
                                }
                                var $btn = $(this);
                                $('.popover-imagem').remove();
                                var offset = $btn.offset();
                                var popover = $('<div class="popover-imagem" style="position:absolute; z-index:9999; background:#fff; border:1.5px solid #dbe3ef; box-shadow:0 2px 12px #e0e7ef66; border-radius:8px; padding:12px;">'+html+'</div>');
                                $('body').append(popover);
                                var left = offset.left + $btn.outerWidth() + 10;
                                var top = offset.top - 10;
                                popover.css({left:left, top:top});
                                $(document).on('mousedown.popover', function(ev){
                                    if (!$(ev.target).closest('.popover-imagem, .visualizar-imagem').length) {
                                        $('.popover-imagem').remove();
                                        $(document).off('mousedown.popover');
                                    }
                                });
                            });
                        });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 