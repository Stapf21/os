<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Orcamentos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('orcamentos_model');
        $this->data['menuOrcamentos'] = 'Orcamentos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para visualizar orcamentos.');
            redirect(base_url());
        }

        $this->load->library('pagination');
        $this->orcamentos_model->atualizarSemResposta();

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $de = $this->input->get('data');
        $ate = $this->input->get('data2');

        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($status) {
            $where_array['status'] = [$status];
        }
        if ($de) {
            $where_array['de'] = $de;
        }
        if ($ate) {
            $where_array['ate'] = $ate;
        }

        $this->data['configuration']['base_url'] = site_url('orcamentos/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->orcamentos_model->count('orcamentos');
        if (count($where_array) > 0) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
            $this->data['configuration']['first_url'] = base_url("index.php/orcamentos/gerenciar") . "?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->orcamentos_model->getOrcamentos($where_array, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'orcamentos/orcamentos';

        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para adicionar orcamentos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('orcamentos') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $dataCriacao = $this->input->post('dataCriacao');
            $validadeInput = $this->input->post('validade');
            $status = $this->input->post('status');
            $validade = $validadeInput;

            try {
                $dataCriacao = explode('/', $dataCriacao);
                $dataCriacao = $dataCriacao[2] . '-' . $dataCriacao[1] . '-' . $dataCriacao[0];

                if ($validade) {
                    $validade = explode('/', $validade);
                    $validade = $validade[2] . '-' . $validade[1] . '-' . $validade[0];
                } else {
                    $validade = null;
                }
            } catch (Exception $e) {
                $dataCriacao = date('Y-m-d');
                $validade = null;
            }

            if ($status === 'Enviado ao cliente' && empty($validadeInput)) {
                $validade = date('Y-m-d', strtotime('+7 days'));
            }

            $data = [
                'dataCriacao' => $dataCriacao,
                'validade' => $validade,
                'clientes_id' => $this->input->post('clientes_id'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'status' => $status,
                'observacoes' => $this->input->post('observacoes'),
                'condicoes' => $this->input->post('condicoes'),
                'desconto' => 0,
                'valor_desconto' => 0,
                'tipo_desconto' => null,
            ];

            if (is_numeric($id = $this->orcamentos_model->add('orcamentos', $data, true))) {
                $this->session->set_flashdata('success', 'Orcamento criado com sucesso.');
                log_info('Adicionou um orcamento. ID: ' . $id);
                redirect(site_url('orcamentos/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
            }
        }

        $this->data['view'] = 'orcamentos/adicionarOrcamento';

        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item nao encontrado.');
            redirect('orcamentos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para editar orcamentos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('orcamentos') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataCriacao = $this->input->post('dataCriacao');
            $validadeInput = $this->input->post('validade');
            $status = $this->input->post('status');
            $validade = $validadeInput;

            try {
                $dataCriacao = explode('/', $dataCriacao);
                $dataCriacao = $dataCriacao[2] . '-' . $dataCriacao[1] . '-' . $dataCriacao[0];

                if ($validade) {
                    $validade = explode('/', $validade);
                    $validade = $validade[2] . '-' . $validade[1] . '-' . $validade[0];
                } else {
                    $validade = null;
                }
            } catch (Exception $e) {
                $dataCriacao = date('Y-m-d');
                $validade = null;
            }

            if ($status === 'Enviado ao cliente' && empty($validadeInput)) {
                $validade = date('Y-m-d', strtotime('+7 days'));
            }

            $data = [
                'dataCriacao' => $dataCriacao,
                'validade' => $validade,
                'clientes_id' => $this->input->post('clientes_id'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'status' => $status,
                'observacoes' => $this->input->post('observacoes'),
                'condicoes' => $this->input->post('condicoes'),
            ];

            if ($this->orcamentos_model->edit('orcamentos', $data, 'idOrcamento', $this->input->post('idOrcamento')) == true) {
                $this->session->set_flashdata('success', 'Orcamento atualizado com sucesso!');
                log_info('Atualizou um orcamento. ID: ' . $this->input->post('idOrcamento'));
                redirect(site_url('orcamentos/editar/') . $this->input->post('idOrcamento'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['result'] = $this->orcamentos_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->orcamentos_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->orcamentos_model->getServicos($this->uri->segment(3));
        $this->data['osGerada'] = $this->orcamentos_model->getOsByOrcamento($this->uri->segment(3));

        $this->data['view'] = 'orcamentos/editarOrcamento';

        return $this->layout();
    }

    public function visualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item nao encontrado.');
            redirect('orcamentos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para visualizar orcamentos.');
            redirect(base_url());
        }

        $this->load->model('mapos_model');
        $id = $this->uri->segment(3);
        $this->data['result'] = $this->orcamentos_model->getById($id);
        $this->data['produtos'] = $this->orcamentos_model->getProdutos($id);
        $this->data['servicos'] = $this->orcamentos_model->getServicos($id);
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['osGerada'] = $this->orcamentos_model->getOsByOrcamento($id);

        $this->data['view'] = 'orcamentos/visualizarOrcamento';

        return $this->layout();
    }

    public function imprimir()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item nao encontrado.');
            redirect('orcamentos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para visualizar orcamentos.');
            redirect(base_url());
        }

        $this->load->model('mapos_model');
        $id = $this->uri->segment(3);
        $this->data['result'] = $this->orcamentos_model->getById($id);
        $this->data['produtos'] = $this->orcamentos_model->getProdutos($id);
        $this->data['servicos'] = $this->orcamentos_model->getServicos($id);
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        $this->load->view('orcamentos/imprimirOrcamento', $this->data);
    }

    public function imprimirTermica()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item nao encontrado.');
            redirect('orcamentos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para visualizar orcamentos.');
            redirect(base_url());
        }

        $this->load->model('mapos_model');
        $id = $this->uri->segment(3);
        $this->data['result'] = $this->orcamentos_model->getById($id);
        $this->data['produtos'] = $this->orcamentos_model->getProdutos($id);
        $this->data['servicos'] = $this->orcamentos_model->getServicos($id);
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        $this->load->view('orcamentos/imprimirOrcamentoTermica', $this->data);
    }

    public function enviar_email()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item nao encontrado.');
            redirect('orcamentos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para enviar orcamento por e-mail.');
            redirect(base_url());
        }

        $this->load->model('mapos_model');
        $this->load->model('usuarios_model');
        $id = $this->uri->segment(3);
        $this->data['result'] = $this->orcamentos_model->getById($id);
        if (! isset($this->data['result']->email) || empty($this->data['result']->email)) {
            $this->session->set_flashdata('error', 'O cliente nao tem e-mail cadastrado.');
            redirect(site_url('orcamentos'));
        }

        $this->data['produtos'] = $this->orcamentos_model->getProdutos($id);
        $this->data['servicos'] = $this->orcamentos_model->getServicos($id);
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        if (! isset($this->data['emitente']->email)) {
            $this->session->set_flashdata('error', 'Efetue o cadastro dos dados de emitente.');
            redirect(site_url('orcamentos'));
        }

        $emitente = $this->data['emitente'];
        $responsavel = $this->usuarios_model->getById($this->data['result']->usuarios_id);

        $remetentes = [];
        $remetentes[] = $this->data['result']->email;
        if ($responsavel && $responsavel->email) {
            $remetentes[] = $responsavel->email;
        }
        if ($emitente->email) {
            $remetentes[] = $emitente->email;
        }

        $enviouEmail = $this->enviarOrcamentoPorEmail($id, $remetentes, 'Orcamento');
        if ($enviouEmail) {
            $this->session->set_flashdata('success', 'O email esta sendo processado e sera enviado em breve.');
            log_info('Enviou e-mail de orcamento para o cliente: ' . $this->data['result']->nomeCliente . '. E-mail: ' . $this->data['result']->email);
            redirect(site_url('orcamentos'));
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao enviar e-mail.');
        redirect(site_url('orcamentos'));
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dOrcamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para excluir orcamento.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if (! $id || ! is_numeric($id)) {
            $this->session->set_flashdata('error', 'Orcamento nao encontrado.');
            redirect(site_url('orcamentos/gerenciar/'));
        }

        $orcamento = $this->orcamentos_model->getById($id);
        if (! $orcamento) {
            $this->session->set_flashdata('error', 'Orcamento nao encontrado.');
            redirect(site_url('orcamentos/gerenciar/'));
        }

        $os = $this->orcamentos_model->getOsByOrcamento($id);
        if ($os && $os->idOs) {
            $this->session->set_flashdata('error', 'Este orcamento possui OS vinculada.');
            redirect(site_url('orcamentos/gerenciar/'));
        }

        $this->orcamentos_model->delete('orcamentos_produtos', 'orcamento_id', $id);
        $this->orcamentos_model->delete('orcamentos_servicos', 'orcamento_id', $id);
        $this->orcamentos_model->delete('orcamentos', 'idOrcamento', $id);

        log_info('Removeu um orcamento. ID: ' . $id);
        $this->session->set_flashdata('success', 'Orcamento excluido com sucesso.');
        redirect(site_url('orcamentos/gerenciar/'));
    }

    public function adicionarProduto()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_produto_orcamento') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $preco = $this->normalizeDecimal($this->input->post('preco'));
        $quantidade = $this->normalizeDecimal($this->input->post('quantidade'));
        $data = [
            'produtos_id' => $this->input->post('idProduto'),
            'quantidade' => $quantidade,
            'preco' => $preco,
            'orcamento_id' => $this->input->post('idOrcamentoProduto'),
            'subTotal' => $preco * $quantidade,
        ];

        if ($this->orcamentos_model->add('orcamentos_produtos', $data) == true) {
            log_info('Adicionou produto em orcamento. ID: ' . $this->input->post('idOrcamentoProduto'));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(['result' => false]));
    }

    public function excluirProduto()
    {
        $id = $this->input->post('idProduto');
        $orcamentoId = $this->input->post('idOrcamento');

        if ($this->orcamentos_model->delete('orcamentos_produtos', 'idOrcamentoProduto', $id) == true) {
            log_info('Removeu produto do orcamento. ID: ' . $orcamentoId);
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function adicionarServico()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_servico_orcamento') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $preco = $this->normalizeDecimal($this->input->post('preco'));
        $quantidade = $this->normalizeDecimal($this->input->post('quantidade'));
        $idServico = $this->input->post('idServico');
        $orcamentoId = $this->input->post('idOrcamentoServico');
        $data = [
            'servicos_id' => $idServico,
            'quantidade' => $quantidade,
            'preco' => $preco,
            'orcamento_id' => $orcamentoId,
            'subTotal' => $preco * $quantidade,
        ];

        if ($this->orcamentos_model->add('orcamentos_servicos', $data) == true) {
            log_info('Adicionou servico em orcamento. ID: ' . $this->input->post('idOrcamentoServico'));

            $this->load->model('servicos_model');
            $itens = $this->servicos_model->getItens($idServico);
            if ($itens) {
                $quantidadeServico = $quantidade ? (float) $quantidade : 1;
                foreach ($itens as $item) {
                    $qtdItem = (float) $item->quantidade * $quantidadeServico;
                    $precoItem = $item->precoVenda;
                    $descricao = $item->descricao;
                    $detalhes = [];
                    if (! empty($item->observacao)) {
                        $detalhes[] = $item->observacao;
                    }
                    if (! empty($item->unidade)) {
                        $detalhes[] = $item->unidade;
                    }
                    if ($detalhes) {
                        $descricao .= ' (' . implode(' - ', $detalhes) . ')';
                    }

                    $this->orcamentos_model->add('orcamentos_produtos', [
                        'produtos_id' => $item->produtos_id,
                        'quantidade' => $qtdItem,
                        'preco' => $precoItem,
                        'orcamento_id' => $orcamentoId,
                        'subTotal' => $precoItem * $qtdItem,
                        'descricao' => $descricao,
                    ]);
                }
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(['result' => false]));
    }

    public function excluirServico()
    {
        $id = $this->input->post('idServico');
        $orcamentoId = $this->input->post('idOrcamento');

        if ($this->orcamentos_model->delete('orcamentos_servicos', 'idOrcamentoServico', $id) == true) {
            log_info('Removeu servico do orcamento. ID: ' . $orcamentoId);
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function adicionarDesconto()
    {
        if ($this->input->post('desconto') == '') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['messages' => 'Campo desconto vazio']));
        }

        $idOrcamento = $this->input->post('idOrcamento');
        $data = [
            'desconto' => $this->input->post('desconto'),
            'tipo_desconto' => $this->input->post('tipoDesconto'),
            'valor_desconto' => $this->input->post('resultado'),
        ];

        if ($this->orcamentos_model->edit('orcamentos', $data, 'idOrcamento', $idOrcamento) == true) {
            log_info('Adicionou desconto no orcamento. ID: ' . $idOrcamento);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(201)
                ->set_output(json_encode(['result' => true]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode(['result' => false]));
    }

    public function gerarOs($id)
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para gerar OS.');
            redirect(base_url());
        }

        $orcamento = $this->orcamentos_model->getById($id);
        if (! $orcamento) {
            $this->session->set_flashdata('error', 'Orcamento nao encontrado.');
            redirect(site_url('orcamentos'));
        }

        if ($orcamento->status != 'Aprovado') {
            $this->session->set_flashdata('error', 'Orcamento precisa estar aprovado.');
            redirect(site_url('orcamentos/editar/') . $id);
        }

        $osExistente = $this->orcamentos_model->getOsByOrcamento($id);
        if ($osExistente && $osExistente->idOs) {
            $this->session->set_flashdata('error', 'OS ja gerada para este orcamento.');
            redirect(site_url('orcamentos/editar/') . $id);
        }

        $data = [
            'dataInicial' => date('Y-m-d'),
            'dataFinal' => date('Y-m-d'),
            'clientes_id' => $orcamento->clientes_id,
            'usuarios_id' => $orcamento->usuarios_id,
            'status' => 'Aberto',
            'observacoes' => $orcamento->observacoes,
            'descricaoProduto' => null,
            'defeito' => null,
            'laudoTecnico' => null,
            'garantia' => 0,
            'faturado' => 0,
            'orcamento_id' => $orcamento->idOrcamento,
        ];

        $this->load->model('os_model');
        $osId = $this->os_model->add('os', $data, true);
        if (! is_numeric($osId)) {
            $this->session->set_flashdata('error', 'Erro ao gerar OS.');
            redirect(site_url('orcamentos/editar/') . $id);
        }

        $produtos = $this->orcamentos_model->getProdutos($id);
        foreach ($produtos as $produto) {
            $this->os_model->add('produtos_os', [
                'quantidade' => $produto->quantidade,
                'descricao' => $produto->descricao,
                'preco' => $produto->preco,
                'os_id' => $osId,
                'produtos_id' => $produto->produtos_id,
                'subTotal' => $produto->subTotal,
            ]);
        }

        $servicos = $this->orcamentos_model->getServicos($id);
        foreach ($servicos as $servico) {
            $this->os_model->add('servicos_os', [
                'servico' => $servico->servico,
                'quantidade' => $servico->quantidade ?: 1,
                'preco' => $servico->preco,
                'os_id' => $osId,
                'servicos_id' => $servico->servicos_id,
                'subTotal' => $servico->subTotal,
            ]);
        }

        $this->session->set_flashdata('success', 'OS gerada com sucesso.');
        redirect(site_url('os/editar/') . $osId);
    }

    public function autoCompleteCliente()
    {
        $q = $this->input->get('term');
        $this->load->model('os_model');
        $this->os_model->autoCompleteCliente($q);
    }

    public function autoCompleteUsuario()
    {
        $q = $this->input->get('term');
        $this->load->model('os_model');
        $this->os_model->autoCompleteUsuario($q);
    }

    public function autoCompleteProduto()
    {
        $q = $this->input->get('term');
        $this->load->model('os_model');
        $this->os_model->autoCompleteProduto($q);
    }

    public function autoCompleteServico()
    {
        $q = $this->input->get('term');
        $this->load->model('os_model');
        $this->os_model->autoCompleteServico($q);
    }

    private function enviarOrcamentoPorEmail($idOrcamento, $remetentes, $assunto)
    {
        $dados = [];

        $this->load->model('mapos_model');
        $dados['result'] = $this->orcamentos_model->getById($idOrcamento);
        if (! isset($dados['result']->email)) {
            return false;
        }

        $dados['produtos'] = $this->orcamentos_model->getProdutos($idOrcamento);
        $dados['servicos'] = $this->orcamentos_model->getServicos($idOrcamento);
        $dados['emitente'] = $this->mapos_model->getEmitente();
        $emitente = $dados['emitente'];
        if (! isset($emitente->email)) {
            return false;
        }

        $html = $this->load->view('orcamentos/emails/orcamento', $dados, true);

        $this->load->model('email_model');

        $remetentes = array_unique($remetentes);
        foreach ($remetentes as $remetente) {
            if ($remetente) {
                $headers = ['From' => $emitente->email, 'Subject' => $assunto, 'Return-Path' => ''];
                $email = [
                    'to' => $remetente,
                    'message' => $html,
                    'status' => 'pending',
                    'date' => date('Y-m-d H:i:s'),
                    'headers' => serialize($headers),
                ];
                $this->email_model->add('email_queue', $email);
            }
        }

        return true;
    }
}
