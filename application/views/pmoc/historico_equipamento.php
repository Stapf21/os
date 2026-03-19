<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper" style="background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; align-items: center;">
    <section class="content-header" style="margin-bottom: 18px; width: 100%; max-width: 1100px;">
        <h1 style="font-size: 2.4rem; font-weight: 800; color: #222e3c; margin-bottom: 2px; letter-spacing: -1px;">
            Histórico do Equipamento
            <small style="font-size: 1.15rem; color: #7b8ca6; font-weight: 400; margin-left: 6px;">Registro de Manutenções</small>
        </h1>
        <ol class="breadcrumb" style="background: none; padding-left: 0; margin-bottom: 0; font-size: 0.98rem;">
            <li><a href="<?php echo base_url(); ?>" style="color: #7b8ca6;"><i class="fa fa-dashboard"></i> Início</a></li>
            <li><a href="<?php echo base_url(); ?>pmoc" style="color: #7b8ca6;">Pmoc</a></li>
            <li class="active" style="color: #f59e42;">Histórico</li>
        </ol>
    </section>
    <section class="content" style="width: 100%; display: flex; justify-content: center;">
        <div class="box" style="border-radius: 16px; box-shadow: 0 4px 24px #e0e7ef55; border: none; background: #fff; width: 100%; max-width: 1100px; margin-bottom: 32px;">
            <div class="box-header" style="border-bottom: 1.5px solid #e0e7ef; padding: 24px 32px 10px 32px;">
                <h3 class="box-title" style="font-size: 1.35rem; font-weight: 700; color: #222e3c; margin:0;">Histórico - <?php echo $equipamento->nome ?? ($equipamento->descricao ?? ''); ?></h3>
                    </div>
            <div class="box-body" style="padding: 24px 32px;">
                        <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="background: #fff; border-radius: 8px; overflow: hidden; margin-bottom:0;">
                        <thead style="background: #f1f5fa; position: sticky; top: 0; z-index: 2;">
                                    <tr>
                                <th style="min-width: 90px;">Data</th>
                                <th style="min-width: 60px;">OS</th>
                                <th style="min-width: 120px;">Status</th>
                                <th style="min-width: 180px;">Observações</th>
                                <th style="min-width: 90px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php if (empty($historico)) { ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; color:#7b8ca6; font-size:1.15rem; padding: 48px 0; background: #f8fafb;">
                                        <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                                            <i class="fa fa-history" style="font-size:2.5rem; color:#e0e7ef;"></i>
                                            Nenhum registro de manutenção encontrado para este equipamento.
                                        </div>
                                    </td>
                                </tr>
                            <?php } else { 
                                $CI = &get_instance();
                                $CI->load->model('OsPmoc_model');
                                foreach ($historico as $h) { 
                                    $os_pmoc = $CI->OsPmoc_model->getById($h->os_pmoc_id);
                                    $status_os = $os_pmoc ? ucfirst($os_pmoc->status) : '-';
                            ?>
                                <tr style="background: <?php echo ($h === reset($historico) || $h === end($historico)) ? '#f9fafb' : '#fff'; ?>;">
                                    <td><?php echo date('d/m/Y', strtotime($h->data_verificacao)); ?></td>
                                    <td><a href="<?php echo base_url('pmoc/os_pmoc/' . $h->os_pmoc_id); ?>" style="color:#2980b9; font-weight:600;">#<?php echo $h->os_pmoc_id ?? $h->idChecklist; ?></a></td>
                                    <td>
                                        <span class="label label-<?php echo ($status_os == 'Concluido') ? 'success' : (($status_os == 'Em andamento') ? 'warning' : 'default'); ?>">
                                            <?php echo $status_os; ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 1rem; color: #222e3c;">
                                        <?php 
                                        $observacoes = json_decode($h->observacoes, true);
                                        if ($observacoes && is_array($observacoes)) {
                                            foreach ($observacoes as $item => $obs) {
                                                if (!empty($obs)) {
                                                    echo "<div style='margin-bottom:2px;'><strong style='color:#2980b9;'>$item:</strong> $obs</div>";
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url('pmoc/os_pmoc/' . $h->os_pmoc_id); ?>" class="btn btn-success btn-xs" style="border-radius:6px; font-size:14px; padding:7px 16px; font-weight:600; box-shadow:0 1px 4px #e0e7ef33;" title="Visualizar OS PMOC">
                                            <i class="fa fa-eye"></i> Visualizar
                                        </a>
                                        <a href="<?php echo base_url('pmoc/excluir_checklist/' . $h->idChecklist . '/' . $equipamento->idEquipamentos); ?>" class="btn btn-danger btn-xs" style="border-radius:6px; font-size:14px; padding:7px 16px; font-weight:600; margin-left:6px; box-shadow:0 1px 4px #e0e7ef33;" title="Excluir Checklist" onclick="return confirm('Tem certeza que deseja excluir este checklist?');">
                                            <i class="fa fa-trash"></i> Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php }} ?>
                                </tbody>
                            </table>
                </div>
            </div>
        </div>
    </section>
</div> 
<style>
@media (max-width: 1100px) {
    .content-wrapper, .content-header, .box, .box-body {
        max-width: 100vw !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}
@media (max-width: 900px) {
    .content-wrapper .box, .content-wrapper .box-body, .content-wrapper .table-responsive {
        padding: 0 !important;
    }
    .content-wrapper .table {
        font-size: 13px;
    }
    .content-header h1 {
        font-size: 1.3rem !important;
    }
}
</style> 