<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<style>
  .pmoc-cards { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 24px; }
  .pmoc-card {
    border-radius: 16px;
    padding: 28px 18px 22px 28px;
    min-width: 220px;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    box-shadow: 0 2px 12px #e0e0e0;
    font-family: 'Roboto', Arial, sans-serif;
    position: relative;
    border: none;
    transition: box-shadow 0.2s;
  }
  .pmoc-card .icon {
    font-size: 2.8em;
    opacity: 0.18;
    position: absolute;
    top: 18px;
    right: 24px;
    pointer-events: none;
  }
  .pmoc-card h3 {
    margin: 0 0 10px 0;
    font-size: 1.1em;
    font-weight: 600;
    letter-spacing: 0.01em;
    color: #F5F5F5;
  }
  .pmoc-card .value {
    font-size: 2.2em;
    font-weight: bold;
    margin-bottom: 0;
    letter-spacing: 0.01em;
    color: #F5F5F5;
  }
  .pmoc-card.blue {
    background: linear-gradient(120deg, #1B3C74 80%, #2C5FAA 100%);
    color: #F5F5F5;
  }
  .pmoc-card.lightblue {
    background: linear-gradient(120deg, #50B7E8 80%, #7EC9ED 100%);
    color: #212121;
  }
  .pmoc-card.turquoise {
    background: linear-gradient(120deg, #50B7E8 80%, #2C5FAA 100%);
    color: #F5F5F5;
  }
  .pmoc-card.red {
    background: linear-gradient(120deg, #E57373 80%, #e53935 100%);
    color: #fff;
  }

  .actions-bar {
    margin-bottom: 24px;
    display: flex;
    gap: 18px;
  }
  .pmoc-btn {
    display: flex;
    align-items: center;
    background: #2C5FAA;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 28px;
    font-size: 1.08em;
    font-weight: 500;
    box-shadow: 0 2px 8px #e0e7ef;
    transition: box-shadow 0.2s, background 0.2s, color 0.2s;
    cursor: pointer;
    text-decoration: none;
    gap: 10px;
  }
  .pmoc-btn .button__icon {
    font-size: 1.3em;
    margin-right: 6px;
    color: #fff;
  }
  .pmoc-btn:hover {
    background: #7EC9ED;
    color: #1B3C74;
    box-shadow: 0 6px 18px #50B7E8;
  }
  .widget-title h5 { font-size: 1.4em; }
  .badge { padding: 5px 15px; font-size: 1em; border-radius: 12px; color: #fff; }
  .pmoc-next-actions {
    border: 2px solid #7EC9ED;
    border-radius: 14px;
    background: #fff;
    margin-top: 22px;
    margin-bottom: 18px;
    box-shadow: 0 2px 10px #f0f4fa;
    padding: 28px 32px 32px 32px;
  }
  .pmoc-next-actions-title {
    font-size: 1.45em;
    font-weight: 700;
    color: #1B3C74;
    margin-bottom: 18px;
    letter-spacing: 0.01em;
  }
  .pmoc-next-actions-info {
    font-size: 1.08em;
    color: #50B7E8;
    margin-bottom: 22px;
    font-weight: 500;
  }
  .pmoc-next-actions-btn {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    width: fit-content;
    min-width: 220px;
    margin-top: 0;
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-cogs"></i>
        </span>
        <h5>Dashboard do Cliente PMOC</h5>
    </div>
    <!-- Identificação do Plano -->
    <div class="widget-box" style="margin-top: 15px;">
        <div class="widget-title">
            <h5>Identificação do Plano</h5>
        </div>
        <div class="widget-content" style="padding: 15px 20px;">
            <div style="display: flex; flex-wrap: wrap; gap: 32px; align-items: center;">
                <span><b>Cliente:</b> <?php echo $plano->nomeCliente; ?></span>
                <span><b>Técnico Responsável:</b> <?php echo $plano->tecnico_nome; ?></span>
                <span><b>Frequência:</b> <?php echo ucfirst($plano->frequencia_manutencao); ?></span>
                <span><b>Nº ART:</b> <?php echo $plano->numero_art; ?> <b>| Validade:</b> <?php echo date('d/m/Y', strtotime($plano->validade_art)); ?></span>
                <span><b>Status:</b> <span class="badge" style="background-color: <?php echo $plano->status == 'ativo' ? '#00cd00' : '#CD0000'; ?>; border-color: <?php echo $plano->status == 'ativo' ? '#00cd00' : '#CD0000'; ?>"><?php echo ucfirst($plano->status); ?></span></span>
            </div>
        </div>
    </div>
    <!-- Cards/Resumo dos Equipamentos -->
    <div class="pmoc-cards">
        <div class="pmoc-card blue">
            <span class="icon"><i class='bx bx-cube'></i></span>
            <h3>Quantidade de Aparelhos</h3>
            <div class="value"><?php echo $qtd_aparelhos; ?></div>
        </div>
        <div class="pmoc-card lightblue">
            <span class="icon"><i class='bx bx-calendar-check'></i></span>
            <h3>Última OS Realizada</h3>
            <div class="value"><?php echo $ultima_os ? date('d/m/Y', strtotime($ultima_os->dataFinal)) : '-'; ?></div>
        </div>
        <div class="pmoc-card turquoise">
            <span class="icon"><i class='bx bx-calendar-star'></i></span>
            <h3>Próxima Manutenção</h3>
            <div class="value"><?php echo $proxima_manutencao ? $proxima_manutencao : '-'; ?></div>
        </div>
        <div class="pmoc-card red">
            <span class="icon"><i class='bx bx-error'></i></span>
            <h3>Equipamentos com Falhas (6 meses)</h3>
            <div class="value"><?php echo $equipamentos_falha ? count($equipamentos_falha) : 0; ?></div>
        </div>
    </div>
    <!-- Botão cadastrar novo equipamento -->
    <div class="actions-bar">
        <a href="<?php echo base_url('equipamentos/novo?cliente_id=' . $plano->clientes_id); ?>" class="pmoc-btn">
            <span class="button__icon"><i class='bx bx-plus-circle'></i></span> Cadastrar Novo Equipamento
        </a>
        <a href="<?php echo base_url('pmoc/relatorio/' . $plano->id_pmoc); ?>" class="pmoc-btn" target="_blank">
            <span class="button__icon"><i class='bx bx-printer'></i></span> Gerar Relatório PMOC
        </a>
    </div>
    <!-- Tabela de Equipamentos -->
    <div class="widget-box">
        <div class="widget-title">
            <h5>Equipamentos Cadastrados</h5>
        </div>
        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Nº de Série</th>
                            <th>Local de Instalação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$equipamentos) {
                            echo '<tr><td colspan="6">Nenhum equipamento cadastrado</td></tr>';
                        } else {
                            foreach ($equipamentos as $eq) {
                                echo '<tr>';
                                echo '<td>' . ($eq->descricao ?? $eq->equipamento) . '</td>';
                                echo '<td>' . ($eq->marca ?? '-') . '</td>';
                                echo '<td>' . ($eq->modelo ?? '-') . '</td>';
                                echo '<td>' . ($eq->num_serie ?? '-') . '</td>';
                                echo '<td>' . ($eq->local_instalacao ?? '-') . '</td>';
                                echo '<td>';
                                echo '<a href="' . base_url('pmoc/historico/' . $eq->idEquipamentos) . '" class="btn-nwe3" title="Ver histórico"><i class="bx bx-search"></i></a> ';
                                echo '<a href="' . base_url('equipamentos/editar/' . $eq->idEquipamentos) . '" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a> ';
                                echo '<a href="' . base_url('equipamentos/excluir/' . $eq->idEquipamentos) . '" class="btn-nwe6" title="Excluir" onclick="return confirm(\'Deseja realmente excluir este equipamento?\')"><i class="bx bx-trash"></i></a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Próximas Ações -->
    <?php if ($plano->status == 'ativo') { ?>
    <div class="pmoc-next-actions">
        <div class="pmoc-next-actions-title">Próximas Ações</div>
        <div class="pmoc-next-actions-info">
            Próxima OS prevista: <b><?php echo $proxima_manutencao ? $proxima_manutencao : '-'; ?></b>
        </div>
        <div class="pmoc-next-actions-btn">
            <a href="<?php echo base_url('pmoc/criar_os_pmoc/' . $plano->id_pmoc); ?>" class="pmoc-btn">
                <span class="button__icon"><i class='bx bx-cog'></i></span> Criar OS PMOC agora
            </a>
        </div>
    </div>
    <?php } ?>
</div> 