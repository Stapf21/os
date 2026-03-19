<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipamentos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipamentos_model');
        $this->load->helper(array('form', 'url'));
    }

    // Exibe o formulário de cadastro
    public function novo()
    {
        $cliente_id = $this->input->get('cliente_id');
        $this->data['cliente_id'] = $cliente_id;
        $this->data['view'] = 'pmoc/form_equipamento';
        return $this->layout();
    }

    // Salva o equipamento
    public function salvar()
    {
        $foto_nome = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $permitidas = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($ext), $permitidas)) {
                $this->session->set_flashdata('error', 'Apenas arquivos JPG ou PNG são permitidos.');
                redirect($this->agent->referrer());
                return;
            }
            if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
                $this->session->set_flashdata('error', 'O tamanho máximo permitido é 5MB.');
                redirect($this->agent->referrer());
                return;
            }
            $foto_nome = 'equipamento_' . time() . '.' . $ext;
            $destino = FCPATH . 'assets/uploads/equipamentos/';
            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }
            move_uploaded_file($_FILES['foto']['tmp_name'], $destino . $foto_nome);
        }
        $data = [
            'equipamento' => $this->input->post('descricao'),
            'descricao' => $this->input->post('descricao'),
            'modelo' => $this->input->post('modelo'),
            'num_serie' => $this->input->post('num_serie'),
            'tensao' => $this->input->post('tensao'),
            'potencia' => $this->input->post('potencia'),
            'clientes_id' => $this->input->post('cliente_id'),
            'foto' => $foto_nome,
        ];
        $this->equipamentos_model->inserir($data);
        $this->session->set_flashdata('success', 'Equipamento cadastrado com sucesso!');
        // Redireciona para o dashboard do cliente PMOC, se cliente_id informado
        $cliente_id = $this->input->post('cliente_id');
        $this->load->model('pmoc_model');
        $plano = $this->pmoc_model->getByClienteId($cliente_id);
        if ($plano) {
            redirect('pmoc/plano/' . $plano->id_pmoc); // ajuste o campo se necessário
        } else {
            redirect('/');
        }
    }

    public function excluir($id = null)
    {
        if (!$id) {
            show_404();
        }
        $this->load->model('equipamentos_model');
        $equipamento = $this->equipamentos_model->getById($id);
        if (!$equipamento) {
            show_404();
        }
        $this->equipamentos_model->delete($id);
        $this->session->set_flashdata('success', 'Equipamento excluído com sucesso!');
        // Redireciona para o dashboard do cliente PMOC
        if ($equipamento->clientes_id) {
            $this->load->model('pmoc_model');
            $plano = $this->pmoc_model->getByClienteId($equipamento->clientes_id);
            if ($plano) {
                redirect('pmoc/plano/' . $plano->id_pmoc);
            }
        }
        redirect('/');
    }

    public function editar($id = null)
    {
        if (!$id) {
            show_404();
        }
        $this->load->model('equipamentos_model');
        $equipamento = $this->equipamentos_model->getById($id);
        if (!$equipamento) {
            show_404();
        }
        if ($this->input->method() === 'post') {
            $foto_nome = $equipamento->foto;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $permitidas = ['jpg', 'jpeg', 'png'];
                if (in_array(strtolower($ext), $permitidas) && $_FILES['foto']['size'] <= 5 * 1024 * 1024) {
                    $foto_nome = 'equipamento_' . time() . '.' . $ext;
                    $destino = FCPATH . 'assets/uploads/equipamentos/';
                    if (!is_dir($destino)) {
                        mkdir($destino, 0777, true);
                    }
                    move_uploaded_file($_FILES['foto']['tmp_name'], $destino . $foto_nome);
                }
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
                'data_instalacao' => $this->input->post('data_instalacao'),
                'foto' => $foto_nome,
            ];
            $this->equipamentos_model->update($id, $data);
            $this->session->set_flashdata('success', 'Equipamento atualizado com sucesso!');
            // Redireciona para o dashboard do cliente PMOC
            if ($equipamento->clientes_id) {
                $this->load->model('pmoc_model');
                $plano = $this->pmoc_model->getByClienteId($equipamento->clientes_id);
                if ($plano) {
                    redirect('pmoc/plano/' . $plano->id_pmoc);
                }
            }
            redirect('/');
        } else {
            $this->data['equipamento'] = $equipamento;
            $this->data['cliente_id'] = $equipamento->clientes_id;
            $this->data['view'] = 'pmoc/form_equipamento_editar';
            return $this->layout();
        }
    }
} 