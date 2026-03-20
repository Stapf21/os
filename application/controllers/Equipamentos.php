<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipamentos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipamentos_model');
        $this->load->model('pmoc_model');
        $this->load->helper(['form', 'url']);
    }

    public function novo()
    {
        $clienteId = (int) $this->input->get('cliente_id');
        $unidadeId = (int) $this->input->get('unidade_id');

        $this->data['cliente_id'] = $clienteId;
        $this->data['unidade_id'] = $unidadeId ?: null;
        $this->data['unidades'] = $clienteId ? $this->pmoc_model->getUnidades($clienteId) : [];
        $this->data['view'] = 'pmoc/form_equipamento';
        return $this->layout();
    }

    public function salvar()
    {
        $clienteId = (int) $this->input->post('cliente_id');
        $fotoNome = $this->uploadFotoEquipamento();
        if ($fotoNome === false) {
            $this->redirectPlanoCliente($clienteId);
            return;
        }

        $data = [
            'equipamento' => $this->input->post('descricao'),
            'descricao' => $this->input->post('descricao'),
            'marca' => $this->input->post('marca'),
            'modelo' => $this->input->post('modelo'),
            'num_serie' => $this->input->post('num_serie'),
            'btu' => $this->input->post('btu'),
            'tensao' => $this->input->post('tensao'),
            'potencia' => $this->input->post('potencia'),
            'local_instalacao' => $this->input->post('local_instalacao'),
            'data_instalacao' => $this->input->post('data_instalacao') ?: null,
            'clientes_id' => $clienteId,
            'foto' => $fotoNome,
        ];

        $this->adicionarCamposPmoc($data);

        if ($this->equipamentos_model->inserir($data)) {
            $this->session->set_flashdata('success', 'Equipamento cadastrado com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Nao foi possivel cadastrar o equipamento.');
        }

        $this->redirectPlanoCliente($clienteId);
    }

    public function excluir($id = null)
    {
        if (! $id) {
            show_404();
        }

        $equipamento = $this->equipamentos_model->getById($id);
        if (! $equipamento) {
            show_404();
        }

        $this->equipamentos_model->delete($id);
        $this->session->set_flashdata('success', 'Equipamento excluido com sucesso.');
        $this->redirectPlanoCliente((int) $equipamento->clientes_id);
    }

    public function editar($id = null)
    {
        if (! $id) {
            show_404();
        }

        $equipamento = $this->equipamentos_model->getById($id);
        if (! $equipamento) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $fotoNome = $this->uploadFotoEquipamento($equipamento->foto);
            if ($fotoNome === false) {
                $this->redirectPlanoCliente((int) $equipamento->clientes_id);
                return;
            }

            $data = [
                'equipamento' => $this->input->post('descricao'),
                'descricao' => $this->input->post('descricao'),
                'marca' => $this->input->post('marca'),
                'modelo' => $this->input->post('modelo'),
                'num_serie' => $this->input->post('num_serie'),
                'btu' => $this->input->post('btu'),
                'tensao' => $this->input->post('tensao'),
                'potencia' => $this->input->post('potencia'),
                'local_instalacao' => $this->input->post('local_instalacao'),
                'data_instalacao' => $this->input->post('data_instalacao') ?: null,
                'foto' => $fotoNome,
            ];

            $this->adicionarCamposPmoc($data);

            if ($this->equipamentos_model->update($id, $data)) {
                $this->session->set_flashdata('success', 'Equipamento atualizado com sucesso.');
            } else {
                $this->session->set_flashdata('error', 'Nao foi possivel atualizar o equipamento.');
            }

            $this->redirectPlanoCliente((int) $equipamento->clientes_id);
            return;
        }

        $this->data['equipamento'] = $equipamento;
        $this->data['cliente_id'] = $equipamento->clientes_id;
        $this->data['unidades'] = $this->pmoc_model->getUnidades($equipamento->clientes_id);
        $this->data['view'] = 'pmoc/form_equipamento_editar';
        return $this->layout();
    }

    private function uploadFotoEquipamento($fotoAtual = null)
    {
        if (! isset($_FILES['foto']) || (int) $_FILES['foto']['error'] !== 0) {
            return $fotoAtual;
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $this->session->set_flashdata('error', 'Apenas arquivos JPG ou PNG sao permitidos.');
            return false;
        }

        if ((int) $_FILES['foto']['size'] > (5 * 1024 * 1024)) {
            $this->session->set_flashdata('error', 'O tamanho maximo permitido e 5MB.');
            return false;
        }

        $nome = 'equipamento_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $destino = FCPATH . 'assets/uploads/equipamentos/';
        if (! is_dir($destino)) {
            mkdir($destino, 0777, true);
        }

        if (! move_uploaded_file($_FILES['foto']['tmp_name'], $destino . $nome)) {
            $this->session->set_flashdata('error', 'Nao foi possivel enviar a foto do equipamento.');
            return false;
        }

        return $nome;
    }

    private function adicionarCamposPmoc(&$data)
    {
        if ($this->db->field_exists('tipo_equipamento', 'equipamentos')) {
            $data['tipo_equipamento'] = $this->input->post('tipo_equipamento') ?: null;
        }

        if ($this->db->field_exists('cliente_unidade_id', 'equipamentos')) {
            $data['cliente_unidade_id'] = $this->input->post('cliente_unidade_id') ?: null;
        }
    }

    private function redirectPlanoCliente($clienteId)
    {
        $plano = $this->pmoc_model->getByClienteId($clienteId);
        if ($plano) {
            redirect('pmoc/plano/' . $plano->id_pmoc);
            return;
        }

        redirect('pmoc');
    }
}
