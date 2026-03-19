<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pmoc extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pmoc_model');
        $this->load->model('checklist_model');
        $this->load->model('mapos_model');
    }

    public function index()
    {
        $this->data['planos'] = $this->pmoc_model->getAll();
        $this->data['view'] = 'pmoc/lista_planos';
        return $this->layout();
    }

    public function novo()
    {
        $this->data['clientes'] = $this->mapos_model->getClientes();
        $this->data['tecnicos'] = $this->mapos_model->getUsuarios();
        $this->data['view'] = 'pmoc/form_plano';
        return $this->layout();
    }

    public function editar($id)
    {
        $this->data['plano'] = $this->pmoc_model->getById($id);
        $this->data['clientes'] = $this->mapos_model->getClientes();
        $this->data['tecnicos'] = $this->mapos_model->getUsuarios();
        $this->data['view'] = 'pmoc/form_plano';
        return $this->layout();
    }

    public function salvar()
    {
        $data = [
            'cliente_id' => $this->input->post('cliente_id'),
            'frequencia' => $this->input->post('frequencia'),
            'tecnico_id' => $this->input->post('tecnico_id'),
            'art_numero' => $this->input->post('art_numero'),
            'art_validade' => $this->input->post('art_validade'),
            'local' => $this->input->post('local'),
            'status' => 'ativo',
            'data_criacao' => date('Y-m-d H:i:s')
        ];

        if ($this->pmoc_model->add('pmoc_planos', $data)) {
            $this->session->set_flashdata('success', 'Plano PMOC adicionado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao adicionar plano PMOC.');
        }
        redirect('pmoc');
    }

    public function checklist($os_id)
    {
        $this->data['os'] = $this->mapos_model->getById($os_id);
        $this->data['equipamentos'] = $this->mapos_model->getEquipamentosOs($os_id);
        $this->data['checklist'] = $this->checklist_model->getByOs($os_id);
        $this->data['view'] = 'pmoc/checklist';
        return $this->layout();
    }

    public function salvarChecklist()
    {
        $os_id = $this->input->post('os_id');
        $equipamento_id = $this->input->post('equipamento_id');
        $items = $this->input->post('items');
        $status = $this->input->post('status');
        $observacoes = $this->input->post('observacoes');

        $data = [
            'os_id' => $os_id,
            'equipamento_id' => $equipamento_id,
            'items' => json_encode($items),
            'status' => json_encode($status),
            'observacoes' => json_encode($observacoes),
            'data_verificacao' => date('Y-m-d H:i:s')
        ];

        if ($this->checklist_model->add('pmoc_checklists', $data)) {
            $this->session->set_flashdata('success', 'Checklist salvo com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar checklist.');
        }
        redirect('pmoc/checklist/' . $os_id);
    }

    public function historico($equipamento_id)
    {
        $this->data['equipamento'] = $this->mapos_model->getEquipamento($equipamento_id);
        $this->data['historico'] = $this->pmoc_model->getHistoricoEquipamento($equipamento_id);
        $this->data['view'] = 'pmoc/historico_equipamento';
        return $this->layout();
    }

    public function relatorio($plano_id)
    {
        $this->data['plano'] = $this->pmoc_model->getById($plano_id);
        $this->data['checklists'] = $this->checklist_model->getByPlano($plano_id);
        $this->data['view'] = 'pmoc/relatorio_pdf';
        return $this->layout();
    }
} 