<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pmoc extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pmoc_model');
        $this->load->model('mapos_model');
        $this->load->model('clientes_model');
        $this->load->model('usuarios_model');
        $this->data['menuPmoc'] = 'pmoc';

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vPmoc')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para acessar o PMOC.');
            redirect(base_url());
        }
    }

    public function index()
    {
        $this->data['planos'] = $this->pmoc_model->getAll();
        $this->data['view'] = 'pmoc/lista_planos';
        return $this->layout();
    }

    public function novo()
    {
        $this->data['clientes'] = $this->clientes_model->get('clientes', '*', '', 0, 0, false);
        $this->data['tecnicos'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'pmoc/form_plano';
        return $this->layout();
    }

    public function editar($id)
    {
        $this->data['plano'] = $this->pmoc_model->getById($id);
        $this->data['clientes'] = $this->clientes_model->get('clientes', '*', '', 0, 0, false);
        $this->data['tecnicos'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'pmoc/form_plano';
        return $this->layout();
    }

    public function salvar()
    {
        $id = $this->input->post('id');
        $data = [
            'clientes_id' => $this->input->post('clientes_id'),
            'frequencia_manutencao' => $this->input->post('frequencia'),
            'tecnico_responsavel' => $this->input->post('tecnico_id'),
            'numero_art' => $this->input->post('art_numero'),
            'validade_art' => $this->input->post('art_validade'),
            'local_instalacao' => $this->input->post('local'),
            'status' => 'ativo'
        ];

        if ($id) {
            $resultado = $this->pmoc_model->edit('pmoc_planos', $data, 'id_pmoc', $id);
            $mensagem = 'Plano PMOC atualizado com sucesso!';
        } else {
            $resultado = $this->pmoc_model->add('pmoc_planos', $data);
            $mensagem = 'Plano PMOC adicionado com sucesso!';
        }

        if ($resultado) {
            $this->session->set_flashdata('success', $mensagem);
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar o plano PMOC.');
        }
        redirect('pmoc');
    }

    public function checklist($os_pmoc_id = null)
    {
        if (!$os_pmoc_id) {
            $this->session->set_flashdata('error', 'ID da OS PMOC não informado.');
            redirect('pmoc');
        }

        $this->load->model('OsPmoc_model');
        $os_pmoc = $this->OsPmoc_model->getById($os_pmoc_id);
        if (!$os_pmoc) {
            $this->session->set_flashdata('error', 'OS PMOC não encontrada.');
            redirect('pmoc');
        }

        $this->load->model('equipamentos_model');
        $equipamentos = $this->equipamentos_model->getByClienteId($os_pmoc->clientes_id);
        $this->load->model('ChecklistPmoc_model');
        $checklist = $this->ChecklistPmoc_model->getByOs($os_pmoc_id);

        $this->data['os'] = $os_pmoc;
        $this->data['equipamentos'] = $equipamentos;
        $this->data['checklist'] = $checklist;
        $this->data['view'] = 'pmoc/checklist';
        return $this->layout();
    }

    public function salvarChecklist()
    {
        $os_pmoc_id = $this->input->post('os_id');
        $equipamento_id = $this->input->post('equipamento_id');

        // Array com os campos que podem ter fotos
        $campos_com_fotos = [
            'limpeza_filtros',
            'carga_gas',
            'condicoes_isolamento',
            'estado_serpentina',
            'bandeja_condensado',
            'fiacao_conexoes',
            'dreno',
            'painel_eletrico',
            'grelhas_difusores',
            'ruidos_anormais',
            'bomba_drenagem',
            'controle_termostato',
            'vazamentos_identificados'
        ];

        $data = [
            'os_pmoc_id' => $os_pmoc_id,
            'equipamento_id' => $equipamento_id,
            'limpeza_filtros' => $this->input->post('limpeza_filtros'),
            'carga_gas' => $this->input->post('carga_gas'),
            'condicoes_isolamento' => $this->input->post('condicoes_isolamento'),
            'estado_serpentina' => $this->input->post('estado_serpentina'),
            'bandeja_condensado' => $this->input->post('bandeja_condensado'),
            'fiacao_conexoes' => $this->input->post('fiacao_conexoes'),
            'dreno' => $this->input->post('dreno'),
            'painel_eletrico' => $this->input->post('painel_eletrico'),
            'grelhas_difusores' => $this->input->post('grelhas_difusores'),
            'ruidos_anormais' => $this->input->post('ruidos_anormais'),
            'bomba_drenagem' => $this->input->post('bomba_drenagem'),
            'controle_termostato' => $this->input->post('controle_termostato'),
            'vazamentos_identificados' => $this->input->post('vazamentos_identificados'),
            'observacoes' => is_array($this->input->post('observacoes')) ? implode(' ', $this->input->post('observacoes')) : $this->input->post('observacoes'),
            'data_verificacao' => date('Y-m-d H:i:s')
        ];

        $this->load->model('ChecklistPmoc_model');
        $checklist_id = $this->ChecklistPmoc_model->add($data);

        // Atualizar status da OS PMOC, se enviado
        $status_os = $this->input->post('status_os');
        if ($status_os && in_array($status_os, ['em andamento', 'concluido'])) {
            $this->load->model('OsPmoc_model');
            $update_data = ['status' => $status_os];
            if ($status_os == 'concluido') {
                $update_data['dataFinal'] = date('Y-m-d H:i:s');
            }
            $this->OsPmoc_model->update($os_pmoc_id, $update_data);
        }

        if ($checklist_id) {
            // Processar fotos para cada campo
            foreach ($campos_com_fotos as $campo) {
                if (isset($_FILES[$campo . '_fotos']) && is_array($_FILES[$campo . '_fotos']['name'])) {
                    $total_fotos = count($_FILES[$campo . '_fotos']['name']);
                    
                    for ($i = 0; $i < $total_fotos; $i++) {
                        if ($_FILES[$campo . '_fotos']['error'][$i] == 0) {
                            $ext = pathinfo($_FILES[$campo . '_fotos']['name'][$i], PATHINFO_EXTENSION);
                            $permitidas = ['jpg', 'jpeg', 'png'];
                            
                            if (!in_array(strtolower($ext), $permitidas)) {
                                continue; // Pula se não for imagem permitida
                            }
                            
                            if ($_FILES[$campo . '_fotos']['size'][$i] > 5 * 1024 * 1024) {
                                continue; // Pula se for maior que 5MB
                            }
                            
                            $foto_nome = $campo . '_' . time() . '_' . $i . '.' . $ext;
                            $destino = FCPATH . 'uploads/pmoc/';
                            
                            if (!is_dir($destino)) {
                                mkdir($destino, 0777, true);
                            }
                            
                            if (move_uploaded_file($_FILES[$campo . '_fotos']['tmp_name'][$i], $destino . $foto_nome)) {
                                // Salvar referência da foto no banco
                                $foto_data = [
                                    'checklist_id' => $checklist_id,
                                    'campo' => $campo,
                                    'nome_arquivo' => $foto_nome
                                ];
                                $this->db->insert('checklist_fotos', $foto_data);
                            }
                        }
                    }
                }
            }
            
            $this->session->set_flashdata('success', 'Checklist salvo com sucesso!');
        } else {
            echo '<pre>ERRO DO BANCO:</pre>';
            var_dump($this->db->error());
            echo '<pre>DATA ENVIADA:</pre>';
            var_dump($data);
            exit;
        }
        
        redirect('pmoc/historico/' . $equipamento_id);
    }

    public function historico($equipamento_id)
    {
        $this->data['equipamento'] = $this->mapos_model->getEquipamento($equipamento_id);
        $this->load->model('ChecklistPmoc_model');
        $this->data['historico'] = $this->ChecklistPmoc_model->getByEquipamento($equipamento_id);
        $this->data['view'] = 'pmoc/historico_equipamento';
        return $this->layout();
    }

    public function relatorio($plano_id)
    {
        $this->data['plano'] = $this->pmoc_model->getById($plano_id);
        $this->load->model('ChecklistPmoc_model');
        $this->data['checklists'] = $this->ChecklistPmoc_model->getRelatorioByPlano($plano_id);
        $this->data['view'] = 'pmoc/relatorio_pdf';
        return $this->layout();
    }

    public function plano($id)
    {
        // Buscar dados do plano PMOC
        $plano = $this->pmoc_model->getById($id);
        if (!$plano) {
            show_404();
        }
        // Buscar equipamentos do cliente
        $this->load->model('equipamentos_model');
        $equipamentos = $this->equipamentos_model->getByClienteId($plano->clientes_id);
        // Quantidade de aparelhos
        $qtd_aparelhos = count($equipamentos);
        // Buscar última OS PMOC
        $this->load->model('os_model');
        $ultima_os = $this->os_model->getUltimaOsPmoc($plano->clientes_id);
        // Calcular próxima manutenção
        $proxima_manutencao = null;
        if ($ultima_os && $plano->frequencia_manutencao && isset($ultima_os->dataFinal)) {
            $freq = $plano->frequencia_manutencao;
            $data_ultima = $ultima_os->dataFinal;
            if ($freq == 'mensal') {
                $proxima_manutencao = date('d/m/Y', strtotime("$data_ultima +1 month"));
            } elseif ($freq == 'trimestral') {
                $proxima_manutencao = date('d/m/Y', strtotime("$data_ultima +3 months"));
            } elseif ($freq == 'semestral') {
                $proxima_manutencao = date('d/m/Y', strtotime("$data_ultima +6 months"));
            } elseif ($freq == 'anual') {
                $proxima_manutencao = date('d/m/Y', strtotime("$data_ultima +1 year"));
            }
        }
        // Equipamentos com falhas nos últimos 6 meses
        $equipamentos_falha = $this->equipamentos_model->getComFalhaUltimosMeses($plano->clientes_id, 6);
        // Histórico de OS PMOC
        $historico = $this->os_model->getHistoricoPmoc($plano->clientes_id);
        $this->data['plano'] = $plano;
        $this->data['equipamentos'] = $equipamentos;
        $this->data['qtd_aparelhos'] = $qtd_aparelhos;
        $this->data['ultima_os'] = $ultima_os;
        $this->data['proxima_manutencao'] = $proxima_manutencao;
        $this->data['equipamentos_falha'] = $equipamentos_falha;
        $this->data['historico'] = $historico;
        $this->data['view'] = 'pmoc/dashboard_cliente';
        return $this->layout();
    }

    public function criar_os_pmoc($plano_id)
    {
        // Buscar dados do plano
        $plano = $this->pmoc_model->getById($plano_id);
        if (!$plano) {
            show_404();
        }
        // Criar nova OS PMOC
        $this->load->model('OsPmoc_model');
        $os_data = [
            'plano_id' => $plano_id,
            'clientes_id' => $plano->clientes_id,
            'usuarios_id' => $this->session->userdata('id_admin'),
            'status' => 'aberta',
            'descricao' => 'Execução de manutenção preventiva conforme plano PMOC',
            'dataInicial' => date('Y-m-d'),
            'dataFinal' => null
        ];
        $id_os_pmoc = $this->OsPmoc_model->add($os_data);
        if (!$id_os_pmoc) {
            $this->session->set_flashdata('error', 'Erro ao criar OS PMOC. Verifique se todos os campos obrigatórios estão preenchidos.');
            redirect('pmoc/plano/' . $plano_id);
        }
        // Associar equipamentos do cliente à nova OS PMOC
        $this->load->model('equipamentos_model');
        $equipamentos = $this->equipamentos_model->getByClienteId($plano->clientes_id);
        if ($equipamentos) {
            foreach ($equipamentos as $eq) {
                $this->OsPmoc_model->vincularEquipamento($id_os_pmoc, $eq->idEquipamentos);
            }
        } else {
            $this->session->set_flashdata('error', 'Nenhum equipamento cadastrado para este cliente. Cadastre equipamentos antes de criar a OS PMOC.');
            redirect('pmoc/plano/' . $plano_id);
        }
        // Redirecionar para checklist técnico (ajuste a rota se necessário)
        redirect('pmoc/checklist/' . $id_os_pmoc);
    }

    // Nova função: listar OS PMOC em aberto do cliente
    public function os_pmoc_abertas($cliente_id)
    {
        $this->load->model('OsPmoc_model');
        $os_abertas = $this->OsPmoc_model->getAbertasByCliente($cliente_id);
        $this->data['os_abertas'] = $os_abertas;
        $this->data['view'] = 'pmoc/os_pmoc_abertas';
        return $this->layout();
    }

    // Visualização de uma OS PMOC
    public function os_pmoc($id)
    {
        $this->load->model('OsPmoc_model');
        $os = $this->OsPmoc_model->getById($id);
        if (!$os) {
            show_404();
        }
        $this->load->model('equipamentos_model');
        // Buscar os vínculos
        $vinculos = $this->db->where('os_pmoc_id', $id)->get('equipamentos_os_pmoc')->result();
        $equipamentos = [];
        foreach ($vinculos as $v) {
            $equipamento = $this->equipamentos_model->getById($v->equipamento_id);
            if ($equipamento) {
                $equipamentos[] = $equipamento;
            }
        }
        $this->load->model('ChecklistPmoc_model');
        $checklists = $this->ChecklistPmoc_model->getByOs($id);
        $this->data['os_pmoc'] = $os;
        $this->data['equipamentos'] = $equipamentos;
        $this->data['checklists'] = $checklists;
        $this->data['view'] = 'pmoc/visualizar_os_pmoc';
        return $this->layout();
    }

    public function excluir_os_pmoc($id)
    {
        $this->load->model('OsPmoc_model');
        if ($this->OsPmoc_model->delete($id)) {
            $this->session->set_flashdata('success', 'OS PMOC excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir OS PMOC.');
        }
        redirect('pmoc');
    }

    public function excluir_checklist($idChecklist, $equipamento_id)
    {
        $this->load->model('ChecklistPmoc_model');
        if ($this->ChecklistPmoc_model->delete($idChecklist)) {
            $this->session->set_flashdata('success', 'Checklist excluído com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir checklist.');
        }
        redirect('pmoc/historico/' . $equipamento_id);
    }
} 
