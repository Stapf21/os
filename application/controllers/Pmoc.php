<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pmoc extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pmoc_model');
        $this->load->model('clientes_model');
        $this->load->model('usuarios_model');
        $this->load->model('equipamentos_model');
        $this->load->model('ChecklistPmoc_model');
        $this->load->model('OsPmoc_model');
        $this->load->model('pnl_model');

        $this->data['menuPmoc'] = 'pmoc';

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vPmoc')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para acessar o PMOC.');
            redirect(base_url());
        }
    }

    public function index()
    {
        $filtros = [
            'status' => $this->input->get('status') ?: null,
            'q' => $this->input->get('q') ?: null,
        ];

        $this->data['filtros'] = $filtros;
        $this->data['planos'] = $this->pmoc_model->getAll($filtros);
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
        $plano = $this->pmoc_model->getById($id);
        if (! $plano) {
            show_404();
        }

        $this->data['plano'] = $plano;
        $this->data['clientes'] = $this->clientes_model->get('clientes', '*', '', 0, 0, false);
        $this->data['tecnicos'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'pmoc/form_plano';
        return $this->layout();
    }

    public function salvar()
    {
        $id = (int) $this->input->post('id');

        $data = [
            'clientes_id' => (int) $this->input->post('clientes_id'),
            'frequencia_manutencao' => $this->input->post('frequencia'),
            'tecnico_responsavel' => $this->input->post('tecnico_id') ?: null,
            'nome_plano' => $this->input->post('nome_plano'),
            'valor_mensal' => $this->parseCurrency($this->input->post('valor_mensal')),
            'data_inicio_contrato' => $this->input->post('data_inicio_contrato') ?: null,
            'vigencia_ate' => $this->input->post('vigencia_ate') ?: null,
            'forma_pagamento' => $this->input->post('forma_pagamento'),
            'status_contrato' => $this->input->post('status_contrato') ?: 'ativo',
            'tipo_atendimento_padrao' => $this->input->post('tipo_atendimento_padrao'),
            'numero_art' => $this->input->post('art_numero'),
            'validade_art' => $this->input->post('art_validade') ?: null,
            'local_instalacao' => $this->input->post('local'),
            'status' => $this->input->post('status_contrato') ?: 'ativo',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $resultado = $this->pmoc_model->edit('pmoc_planos', $data, 'id_pmoc', $id);
            $mensagem = 'Plano PMOC e Plano Mensal atualizado com sucesso!';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $resultado = $this->pmoc_model->add('pmoc_planos', $data);
            $mensagem = 'Plano PMOC e Plano Mensal criado com sucesso!';
        }

        if ($resultado) {
            $this->session->set_flashdata('success', $mensagem);
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar o plano.');
        }

        redirect('pmoc');
    }

    public function plano($id)
    {
        $plano = $this->pmoc_model->getById($id);
        if (! $plano) {
            show_404();
        }

        $unidadeId = (int) $this->input->get('unidade_id');
        $tipoPeriodo = $this->input->get('tipo_periodo') ?: 'mensal';
        $periodoReferencia = $this->input->get('periodo_referencia') ?: date('Y-m');
        $dataInicioCustom = $this->input->get('data_inicio');
        $dataFimCustom = $this->input->get('data_fim');

        [$dataInicio, $dataFim] = $this->resolverPeriodo($tipoPeriodo, $periodoReferencia, $dataInicioCustom, $dataFimCustom);

        $this->data['plano'] = $plano;
        $this->data['unidadeId'] = $unidadeId ?: null;
        $this->data['tipoPeriodo'] = $tipoPeriodo;
        $this->data['periodoReferencia'] = $periodoReferencia;
        $this->data['dataInicio'] = $dataInicio;
        $this->data['dataFim'] = $dataFim;
        $this->data['equipamentos'] = $this->pmoc_model->getEquipamentos($plano->clientes_id, $unidadeId ?: null);
        $this->data['unidades'] = $this->pmoc_model->getUnidades($plano->clientes_id);
        $this->data['cronograma'] = $this->pmoc_model->getCronograma($plano, 12);
        $this->data['resumoOs'] = $this->pmoc_model->getResumoOsByPlano($plano->id_pmoc);
        $this->data['relatoriosPmoc'] = $this->pmoc_model->getRelatoriosByPlano($plano->id_pmoc);
        $this->data['reparos'] = $this->pmoc_model->getReparos($plano->id_pmoc);
        $this->data['pnlResumo'] = $this->pnl_model->getResumoCliente($plano->clientes_id, $dataInicio, $dataFim, $unidadeId ?: null);
        $this->data['resumoUnidadesPnl'] = $this->pnl_model->getResumoPorUnidade($plano->clientes_id, $dataInicio, $dataFim);
        $this->data['view'] = 'pmoc/dashboard_cliente';
        return $this->layout();
    }

    public function adicionar_unidade($plano_id)
    {
        $plano = $this->pmoc_model->getById($plano_id);
        if (! $plano) {
            show_404();
        }

        $nome = trim((string) $this->input->post('nome'));
        if ($nome === '') {
            $this->session->set_flashdata('error', 'Informe o nome da unidade.');
            redirect('pmoc/plano/' . $plano_id);
            return;
        }

        $data = [
            'clientes_id' => $plano->clientes_id,
            'nome' => $nome,
            'empresa' => $this->input->post('empresa'),
            'codigo' => $this->input->post('codigo'),
            'cidade' => $this->input->post('cidade'),
            'estado' => mb_strtoupper(substr(trim((string) $this->input->post('estado')), 0, 2)),
            'ativa' => $this->input->post('ativa') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->pnl_model->addUnidade($data)) {
            $this->session->set_flashdata('success', 'Unidade adicionada ao contrato com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Nao foi possivel adicionar a unidade.');
        }

        redirect('pmoc/plano/' . $plano_id);
    }

    public function criar_os_pmoc($plano_id)
    {
        $plano = $this->pmoc_model->getById($plano_id);
        if (! $plano) {
            show_404();
        }

        $dataPrevista = $this->input->get('data_prevista') ?: date('Y-m-d');
        $clienteUnidadeId = $this->input->get('cliente_unidade_id') ?: null;
        $status = $this->input->get('status') ?: 'agendado';

        $os_data = [
            'plano_id' => $plano_id,
            'clientes_id' => $plano->clientes_id,
            'cliente_unidade_id' => $clienteUnidadeId,
            'usuarios_id' => $this->session->userdata('id_admin'),
            'status' => $status,
            'descricao' => 'Execucao de manutencao preventiva conforme contrato PMOC.',
            'tipo_atendimento' => $plano->tipo_atendimento_padrao,
            'data_prevista' => $dataPrevista,
            'dataInicial' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id_os_pmoc = $this->OsPmoc_model->add($os_data);
        if (! $id_os_pmoc) {
            $this->session->set_flashdata('error', 'Erro ao criar OS PMOC.');
            redirect('pmoc/plano/' . $plano_id);
            return;
        }

        $equipamentos = $this->equipamentos_model->getByClienteId($plano->clientes_id, $clienteUnidadeId);
        foreach ($equipamentos as $eq) {
            $this->OsPmoc_model->vincularEquipamento($id_os_pmoc, $eq->idEquipamentos);
        }

        $this->session->set_flashdata('success', 'OS PMOC criada e vinculada aos equipamentos.');
        redirect('pmoc/checklist/' . $id_os_pmoc);
    }

    public function checklist($os_pmoc_id = null)
    {
        if (! $os_pmoc_id) {
            $this->session->set_flashdata('error', 'ID da OS PMOC nao informado.');
            redirect('pmoc');
            return;
        }

        $os_pmoc = $this->OsPmoc_model->getById($os_pmoc_id);
        if (! $os_pmoc) {
            $this->session->set_flashdata('error', 'OS PMOC nao encontrada.');
            redirect('pmoc');
            return;
        }

        $this->data['os'] = $os_pmoc;
        $this->data['equipamentos'] = $this->OsPmoc_model->getEquipamentos($os_pmoc_id);
        $this->data['checklist'] = $this->ChecklistPmoc_model->getByOs($os_pmoc_id);
        $this->data['view'] = 'pmoc/checklist';
        return $this->layout();
    }

    public function salvarChecklist()
    {
        $os_pmoc_id = (int) $this->input->post('os_id');
        $equipamento_id = (int) $this->input->post('equipamento_id');

        $campos_com_fotos = ['limpeza_filtros', 'carga_gas', 'condicoes_isolamento', 'estado_serpentina', 'bandeja_condensado', 'fiacao_conexoes', 'dreno', 'painel_eletrico', 'grelhas_difusores', 'ruidos_anormais', 'bomba_drenagem', 'controle_termostato', 'vazamentos_identificados'];

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
            'tecnico_responsavel' => $this->input->post('tecnico_responsavel'),
            'tipo_servico' => $this->input->post('tipo_servico'),
            'data_verificacao' => date('Y-m-d H:i:s'),
        ];

        $checklist_id = $this->ChecklistPmoc_model->add($data);
        if (! $checklist_id) {
            $this->session->set_flashdata('error', 'Erro ao salvar checklist.');
            redirect('pmoc/checklist/' . $os_pmoc_id);
            return;
        }

        $status_os = $this->input->post('status_os');
        if ($status_os) {
            $update = ['status' => $status_os];
            if ($status_os === 'concluido') {
                $update['dataFinal'] = date('Y-m-d H:i:s');
            }
            $this->OsPmoc_model->update($os_pmoc_id, $update);
        }

        foreach ($campos_com_fotos as $campo) {
            if (! isset($_FILES[$campo . '_fotos']) || ! is_array($_FILES[$campo . '_fotos']['name'])) {
                continue;
            }

            $total = count($_FILES[$campo . '_fotos']['name']);
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES[$campo . '_fotos']['error'][$i] != 0) {
                    continue;
                }

                $ext = strtolower(pathinfo($_FILES[$campo . '_fotos']['name'][$i], PATHINFO_EXTENSION));
                if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                    continue;
                }

                if ($_FILES[$campo . '_fotos']['size'][$i] > 5 * 1024 * 1024) {
                    continue;
                }

                $nome = $campo . '_' . time() . '_' . $i . '.' . $ext;
                $destino = FCPATH . 'uploads/pmoc/';
                if (! is_dir($destino)) {
                    mkdir($destino, 0777, true);
                }

                if (move_uploaded_file($_FILES[$campo . '_fotos']['tmp_name'][$i], $destino . $nome)) {
                    $this->db->insert('checklist_fotos', [
                        'checklist_id' => $checklist_id,
                        'campo' => $campo,
                        'nome_arquivo' => $nome,
                    ]);
                }
            }
        }

        $this->session->set_flashdata('success', 'Checklist salvo com sucesso!');
        redirect('pmoc/historico/' . $equipamento_id);
    }

    public function historico($equipamento_id)
    {
        $this->data['equipamento'] = $this->equipamentos_model->getById($equipamento_id);
        $this->data['historico'] = $this->ChecklistPmoc_model->getByEquipamento($equipamento_id);
        $this->data['view'] = 'pmoc/historico_equipamento';
        return $this->layout();
    }

    public function relatorio($plano_id)
    {
        $this->data['plano'] = $this->pmoc_model->getById($plano_id);
        $this->data['checklists'] = $this->ChecklistPmoc_model->getRelatorioByPlano($plano_id);
        $this->data['view'] = 'pmoc/relatorio_pdf';
        return $this->layout();
    }

    public function os_pmoc_abertas($cliente_id)
    {
        $this->data['os_abertas'] = $this->OsPmoc_model->getAbertasByCliente($cliente_id);
        $this->data['view'] = 'pmoc/os_pmoc_abertas';
        return $this->layout();
    }

    public function os_pmoc($id)
    {
        $os = $this->OsPmoc_model->getById($id);
        if (! $os) {
            show_404();
        }

        $this->data['os_pmoc'] = $os;
        $this->data['equipamentos'] = $this->OsPmoc_model->getEquipamentos($id);
        $this->data['checklists'] = $this->ChecklistPmoc_model->getByOs($id);
        $this->data['view'] = 'pmoc/visualizar_os_pmoc';
        return $this->layout();
    }

    public function excluir_os_pmoc($id)
    {
        if ($this->OsPmoc_model->delete($id)) {
            $this->session->set_flashdata('success', 'OS PMOC excluida com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir OS PMOC.');
        }
        redirect('pmoc');
    }

    public function excluir_checklist($idChecklist, $equipamento_id)
    {
        if ($this->ChecklistPmoc_model->delete($idChecklist)) {
            $this->session->set_flashdata('success', 'Checklist excluido com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir checklist.');
        }
        redirect('pmoc/historico/' . $equipamento_id);
    }

    public function solicitar_reparo($plano_id)
    {
        $plano = $this->pmoc_model->getById($plano_id);
        if (! $plano) {
            show_404();
        }

        $data = [
            'plano_id' => $plano_id,
            'clientes_id' => $plano->clientes_id,
            'cliente_unidade_id' => $this->input->post('cliente_unidade_id') ?: null,
            'equipamento_id' => $this->input->post('equipamento_id') ?: null,
            'titulo' => $this->input->post('titulo') ?: 'Solicitacao de reparo',
            'descricao' => $this->input->post('descricao'),
            'status' => 'aberto',
            'origem' => $this->input->post('origem') ?: 'interno',
            'data_solicitacao' => date('Y-m-d H:i:s'),
        ];

        if ($this->pmoc_model->addReparo($data)) {
            $this->session->set_flashdata('success', 'Solicitacao de reparo registrada com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Nao foi possivel registrar a solicitacao de reparo.');
        }

        $redirect = $this->input->post('redirect') ?: 'pmoc/plano/' . $plano_id;
        redirect($redirect);
    }

    public function atualizar_status_reparo($plano_id, $reparo_id)
    {
        $plano = $this->pmoc_model->getById((int) $plano_id);
        if (! $plano) {
            show_404();
        }

        $reparo = $this->pmoc_model->getReparoById((int) $reparo_id);
        if (! $reparo || (int) $reparo->plano_id !== (int) $plano->id_pmoc) {
            $this->session->set_flashdata('error', 'Solicitacao de reparo nao encontrada para este contrato.');
            redirect('pmoc/plano/' . (int) $plano->id_pmoc);
            return;
        }

        $novoStatus = mb_strtolower(trim((string) $this->input->post('status')));
        if (! in_array($novoStatus, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->session->set_flashdata('error', 'Status de reparo invalido.');
            redirect('pmoc/plano/' . (int) $plano->id_pmoc);
            return;
        }

        if ($this->pmoc_model->updateReparoStatus((int) $reparo_id, $novoStatus)) {
            $labels = [
                'aberto' => 'Aberto',
                'em_andamento' => 'Em andamento',
                'concluido' => 'Concluido',
            ];
            $this->session->set_flashdata('success', 'Status do reparo atualizado para ' . $labels[$novoStatus] . '.');
        } else {
            $this->session->set_flashdata('error', 'Nao foi possivel atualizar o status do reparo.');
        }

        $redirect = $this->input->post('redirect') ?: 'pmoc/plano/' . (int) $plano->id_pmoc;
        redirect($redirect);
    }

    private function resolverPeriodo($tipoPeriodo, $periodoReferencia, $dataInicioCustom, $dataFimCustom)
    {
        if ($tipoPeriodo === 'anual') {
            $ano = preg_match('/^\d{4}$/', (string) $periodoReferencia) ? $periodoReferencia : date('Y');
            return [$ano . '-01-01', $ano . '-12-31'];
        }

        if ($tipoPeriodo === 'trimestral') {
            if (preg_match('/^(\d{4})\-Q([1-4])$/', (string) $periodoReferencia, $m)) {
                $ano = (int) $m[1];
                $q = (int) $m[2];
                $mesInicio = (($q - 1) * 3) + 1;
                $inicio = sprintf('%04d-%02d-01', $ano, $mesInicio);
                $fim = date('Y-m-t', strtotime($inicio . ' +2 month'));
                return [$inicio, $fim];
            }
            $inicio = date('Y-m-01');
            return [$inicio, date('Y-m-t', strtotime($inicio . ' +2 month'))];
        }

        if ($tipoPeriodo === 'personalizado' && $dataInicioCustom && $dataFimCustom) {
            return [$dataInicioCustom, $dataFimCustom];
        }

        if (preg_match('/^\d{4}\-\d{2}$/', (string) $periodoReferencia)) {
            $inicio = $periodoReferencia . '-01';
            return [$inicio, date('Y-m-t', strtotime($inicio))];
        }

        return [date('Y-m-01'), date('Y-m-t')];
    }

    private function parseCurrency($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $value = str_replace('.', '', (string) $value);
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }
}
