<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pnl extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pnl_model');
        $this->load->model('clientes_model');
        $this->load->library('form_validation');

        $this->data['menuPnl'] = 'pnl';

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para visualizar o painel de P&L.');
            redirect(base_url());
        }
    }

    public function index()
    {
        $periodo = $this->input->get('periodo') ?: date('Y-m');
        $grupo = trim((string) $this->input->get('grupo'));
        [$dataInicio, $dataFim] = $this->resolverPeriodo($periodo);

        $this->data['periodo'] = $periodo;
        $this->data['grupo'] = $grupo;
        $this->data['dataInicio'] = $dataInicio;
        $this->data['dataFim'] = $dataFim;
        $this->data['clientesResumo'] = $this->pnl_model->getClientesResumo($dataInicio, $dataFim, $grupo ?: null);
        $this->data['view'] = 'pnl/index';

        return $this->layout();
    }

    public function cliente($clienteId)
    {
        $cliente = $this->clientes_model->getById($clienteId);
        if (! $cliente) {
            show_404();
        }

        $periodo = $this->input->get('periodo') ?: date('Y-m');
        $unidadeId = $this->input->get('unidade_id') ?: null;
        [$dataInicio, $dataFim] = $this->resolverPeriodo($periodo);

        $resumo = $this->pnl_model->getResumoCliente($clienteId, $dataInicio, $dataFim, $unidadeId);
        $this->data['cliente'] = $cliente;
        $this->data['periodo'] = $periodo;
        $this->data['dataInicio'] = $dataInicio;
        $this->data['dataFim'] = $dataFim;
        $this->data['unidadeId'] = $unidadeId;
        $this->data['resumo'] = $resumo;
        $this->data['lucro'] = $resumo->receitas - $resumo->custos_financeiros - $resumo->custos_diretos;
        $this->data['unidades'] = $this->pnl_model->getUnidades($clienteId);
        $this->data['ativos'] = $this->pnl_model->getAtivos($clienteId);
        $this->data['custos'] = $this->pnl_model->getCustos($clienteId, $dataInicio, $dataFim, $unidadeId);
        $this->data['lancamentos'] = $this->pnl_model->getLancamentosCliente($clienteId, $dataInicio, $dataFim);
        $this->data['resumoUnidades'] = $this->pnl_model->getResumoPorUnidade($clienteId, $dataInicio, $dataFim);
        $this->data['view'] = 'pnl/cliente';

        return $this->layout();
    }

    public function adicionarUnidade($clienteId)
    {
        $cliente = $this->clientes_model->getById($clienteId);
        if (! $cliente) {
            show_404();
        }

        $data = [
            'clientes_id' => $clienteId,
            'nome' => $this->input->post('nome'),
            'empresa' => $this->input->post('empresa'),
            'codigo' => $this->input->post('codigo'),
            'cidade' => $this->input->post('cidade'),
            'estado' => $this->input->post('estado'),
            'ativa' => $this->input->post('ativa') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($data['nome']) {
            $this->pnl_model->addUnidade($data);
            $this->session->set_flashdata('success', 'Unidade vinculada ao cliente com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Informe o nome da unidade.');
        }

        redirect('pnl/cliente/' . $clienteId);
    }

    public function adicionarAtivo($clienteId)
    {
        $cliente = $this->clientes_model->getById($clienteId);
        if (! $cliente) {
            show_404();
        }

        $valor = (float) str_replace(',', '.', str_replace('.', '', $this->input->post('custo_mensal_estimado')));
        if (! $valor && $this->input->post('custo_mensal_estimado')) {
            $valor = (float) str_replace(',', '.', $this->input->post('custo_mensal_estimado'));
        }

        $data = [
            'clientes_id' => $clienteId,
            'cliente_unidade_id' => $this->input->post('cliente_unidade_id') ?: null,
            'nome' => $this->input->post('nome'),
            'tipo' => $this->input->post('tipo'),
            'identificador' => $this->input->post('identificador'),
            'descricao' => $this->input->post('descricao'),
            'custo_mensal_estimado' => $valor,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($data['nome']) {
            $this->pnl_model->addAtivo($data);
            $this->session->set_flashdata('success', 'Ativo associado ao cliente com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Informe o nome do ativo.');
        }

        redirect('pnl/cliente/' . $clienteId);
    }

    public function adicionarCusto($clienteId)
    {
        $cliente = $this->clientes_model->getById($clienteId);
        if (! $cliente) {
            show_404();
        }

        $valor = (float) str_replace(',', '.', str_replace('.', '', $this->input->post('valor')));
        if (! $valor && $this->input->post('valor')) {
            $valor = (float) str_replace(',', '.', $this->input->post('valor'));
        }

        $data = [
            'clientes_id' => $clienteId,
            'cliente_unidade_id' => $this->input->post('cliente_unidade_id') ?: null,
            'cliente_ativo_id' => $this->input->post('cliente_ativo_id') ?: null,
            'categoria' => $this->input->post('categoria'),
            'descricao' => $this->input->post('descricao'),
            'tipo_custo' => $this->input->post('tipo_custo'),
            'valor' => $valor,
            'data_referencia' => $this->input->post('data_referencia') ?: date('Y-m-d'),
            'observacoes' => $this->input->post('observacoes'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($data['descricao'] && $data['categoria']) {
            $this->pnl_model->addCusto($data);
            $this->session->set_flashdata('success', 'Custo especifico registrado com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Informe a categoria e a descricao do custo.');
        }

        redirect('pnl/cliente/' . $clienteId . '?periodo=' . urlencode($this->input->post('periodo') ?: date('Y-m')));
    }

    public function atualizarLancamento($clienteId, $lancamentoId)
    {
        $cliente = $this->clientes_model->getById($clienteId);
        if (! $cliente) {
            show_404();
        }

        $this->pnl_model->updateLancamento($lancamentoId, [
            'cliente_unidade_id' => $this->input->post('cliente_unidade_id') ?: null,
            'categoria_pnl' => $this->input->post('categoria_pnl') ?: null,
            'origem_pnl' => 'financeiro',
        ]);

        $this->session->set_flashdata('success', 'Lancamento categorizado com sucesso.');
        redirect('pnl/cliente/' . $clienteId . '?periodo=' . urlencode($this->input->post('periodo') ?: date('Y-m')));
    }

    public function exportar($clienteId)
    {
        $cliente = $this->clientes_model->getById($clienteId);
        if (! $cliente) {
            show_404();
        }

        $periodo = $this->input->get('periodo') ?: date('Y-m');
        [$dataInicio, $dataFim] = $this->resolverPeriodo($periodo);
        $resumo = $this->pnl_model->getResumoCliente($clienteId, $dataInicio, $dataFim);
        $custos = $this->pnl_model->getCustos($clienteId, $dataInicio, $dataFim);
        $lancamentos = $this->pnl_model->getLancamentosCliente($clienteId, $dataInicio, $dataFim);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="pnl_cliente_' . $clienteId . '_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Cliente', $cliente->nomeCliente]);
        fputcsv($output, ['Periodo', $dataInicio . ' ate ' . $dataFim]);
        fputcsv($output, []);
        fputcsv($output, ['Resumo', 'Valor']);
        fputcsv($output, ['Receitas', number_format($resumo->receitas, 2, '.', '')]);
        fputcsv($output, ['Custos financeiros', number_format($resumo->custos_financeiros, 2, '.', '')]);
        fputcsv($output, ['Custos diretos', number_format($resumo->custos_diretos, 2, '.', '')]);
        fputcsv($output, ['Lucro', number_format($resumo->receitas - $resumo->custos_financeiros - $resumo->custos_diretos, 2, '.', '')]);
        fputcsv($output, []);
        fputcsv($output, ['Lancamentos']);
        fputcsv($output, ['Tipo', 'Descricao', 'Data', 'Valor', 'Categoria']);
        foreach ($lancamentos as $lancamento) {
            fputcsv($output, [
                $lancamento->tipo,
                $lancamento->descricao,
                $lancamento->data_vencimento,
                number_format((float) ($lancamento->valor_desconto ?: $lancamento->valor), 2, '.', ''),
                $lancamento->categoria_pnl,
            ]);
        }
        fputcsv($output, []);
        fputcsv($output, ['Custos especificos']);
        fputcsv($output, ['Categoria', 'Descricao', 'Data', 'Valor']);
        foreach ($custos as $custo) {
            fputcsv($output, [
                $custo->categoria,
                $custo->descricao,
                $custo->data_referencia,
                number_format((float) $custo->valor, 2, '.', ''),
            ]);
        }
        fclose($output);
        exit;
    }

    private function resolverPeriodo($periodo)
    {
        if (preg_match('/^\d{4}$/', $periodo)) {
            return [$periodo . '-01-01', $periodo . '-12-31'];
        }

        if (preg_match('/^\d{4}\-\d{2}$/', $periodo)) {
            $inicio = $periodo . '-01';
            return [$inicio, date('Y-m-t', strtotime($inicio))];
        }

        return [date('Y-m-01'), date('Y-m-t')];
    }
}
