<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pmoc_model extends CI_Model
{
    private $frequenciasMeses = [
        'mensal' => 1,
        'bimestral' => 2,
        'trimestral' => 3,
        'quadrimestral' => 4,
        'semestral' => 6,
        'anual' => 12,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll($filters = [])
    {
        $this->db->select('pmoc_planos.*, clientes.nomeCliente, usuarios.nome as tecnico_nome');
        $this->db->from('pmoc_planos');
        $this->db->join('clientes', 'clientes.idClientes = pmoc_planos.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = pmoc_planos.tecnico_responsavel', 'left');

        if (!empty($filters['status'])) {
            $status = mb_strtolower(trim((string) $filters['status']));
            $this->db->group_start();
            if ($this->db->field_exists('status_contrato', 'pmoc_planos')) {
                $this->db->where('LOWER(pmoc_planos.status_contrato)', $status);
            }
            $this->db->or_where('LOWER(pmoc_planos.status)', $status);
            $this->db->group_end();
        }

        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $this->db->group_start();
            $this->db->like('clientes.nomeCliente', $q);
            if ($this->db->field_exists('nome_plano', 'pmoc_planos')) {
                $this->db->or_like('pmoc_planos.nome_plano', $q);
            }
            $this->db->group_end();
        }

        $this->db->order_by('pmoc_planos.id_pmoc', 'desc');
        $planos = $this->db->get()->result();

        foreach ($planos as $plano) {
            $plano->total_unidades = count($this->getUnidades($plano->clientes_id));
            $plano->total_equipamentos = count($this->getEquipamentos($plano->clientes_id));
            $plano->total_reparos_abertos = count($this->getReparos($plano->id_pmoc, 'aberto'));
        }

        return $planos;
    }

    public function getById($id)
    {
        $this->db->select('pmoc_planos.*, clientes.nomeCliente, usuarios.nome as tecnico_nome');
        $this->db->from('pmoc_planos');
        $this->db->join('clientes', 'clientes.idClientes = pmoc_planos.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = pmoc_planos.tecnico_responsavel', 'left');
        $this->db->where('pmoc_planos.id_pmoc', $id);
        return $this->db->get()->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        return $this->db->update($table, $data);
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        return $this->db->delete($table);
    }

    public function getByClienteId($cliente_id)
    {
        return $this->db->where('clientes_id', $cliente_id)->get('pmoc_planos')->row();
    }

    public function getUnidades($clienteId)
    {
        if (! $this->db->table_exists('cliente_unidades')) {
            return [];
        }

        return $this->db
            ->where('clientes_id', $clienteId)
            ->order_by('nome', 'asc')
            ->get('cliente_unidades')
            ->result();
    }

    public function getEquipamentos($clienteId, $unidadeId = null)
    {
        $this->db->select('equipamentos.*');
        if ($this->db->table_exists('cliente_unidades')) {
            $this->db->select('cliente_unidades.nome as unidade_nome');
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = equipamentos.cliente_unidade_id', 'left');
        }
        $this->db->from('equipamentos');
        $this->db->where('equipamentos.clientes_id', $clienteId);
        if ($unidadeId && $this->db->field_exists('cliente_unidade_id', 'equipamentos')) {
            $this->db->where('equipamentos.cliente_unidade_id', $unidadeId);
        }
        $this->db->order_by('equipamentos.descricao', 'asc');

        return $this->db->get()->result();
    }

    public function getResumoOsByPlano($planoId)
    {
        if (! $this->db->table_exists('os_pmoc')) {
            return ['pendente' => 0, 'agendado' => 0, 'em_execucao' => 0, 'concluido' => 0, 'atrasado' => 0];
        }

        $result = ['pendente' => 0, 'agendado' => 0, 'em_execucao' => 0, 'concluido' => 0, 'atrasado' => 0];

        $rows = $this->db->select('status, data_prevista')->where('plano_id', $planoId)->get('os_pmoc')->result();
        foreach ($rows as $row) {
            $status = $this->normalizarStatus($row->status, $row->data_prevista);
            if (isset($result[$status])) {
                $result[$status]++;
            }
        }

        return $result;
    }

    public function getCronograma($plano, $meses = 12)
    {
        $cronograma = [];
        $inicio = ! empty($plano->data_inicio_contrato) ? $plano->data_inicio_contrato : date('Y-m-d');
        $intervalo = $this->frequenciasMeses[$plano->frequencia_manutencao ?? 'mensal'] ?? 1;
        $mapaOs = [];

        if ($this->db->table_exists('os_pmoc')) {
            $os = $this->db->where('plano_id', $plano->id_pmoc)->order_by('data_prevista', 'asc')->get('os_pmoc')->result();
            foreach ($os as $item) {
                $chave = $item->data_prevista ?: date('Y-m-d', strtotime($item->dataInicial ?: 'now'));
                $mapaOs[$chave][] = $item;
            }
        }

        $dataAtual = date('Y-m-d');
        for ($i = 0; $i < $meses; $i++) {
            $prevista = date('Y-m-d', strtotime($inicio . ' +' . ($intervalo * $i) . ' month'));
            $status = 'pendente';
            $osId = null;
            $dataExecucao = null;

            if (! empty($mapaOs[$prevista])) {
                $registro = end($mapaOs[$prevista]);
                $status = $this->normalizarStatus($registro->status, $registro->data_prevista);
                $osId = $registro->idOsPmoc;
                $dataExecucao = $registro->dataFinal;
            } elseif ($prevista < $dataAtual) {
                $status = 'atrasado';
            }

            $cronograma[] = (object) [
                'data_prevista' => $prevista,
                'status' => $status,
                'os_pmoc_id' => $osId,
                'data_execucao' => $dataExecucao,
            ];
        }

        return $cronograma;
    }

    public function getRelatoriosByPlano($planoId)
    {
        if (! $this->db->table_exists('checklist_os_pmoc') || ! $this->db->table_exists('os_pmoc')) {
            return [];
        }

        $this->db->select('checklist_os_pmoc.*, os_pmoc.dataInicial, os_pmoc.dataFinal, os_pmoc.status as status_os, equipamentos.descricao as equipamento_descricao, equipamentos.modelo as equipamento_modelo, cliente_unidades.nome as unidade_nome');
        $this->db->from('checklist_os_pmoc');
        $this->db->join('os_pmoc', 'os_pmoc.idOsPmoc = checklist_os_pmoc.os_pmoc_id');
        $this->db->join('equipamentos', 'equipamentos.idEquipamentos = checklist_os_pmoc.equipamento_id', 'left');
        if ($this->db->table_exists('cliente_unidades')) {
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = equipamentos.cliente_unidade_id', 'left');
        }
        $this->db->where('os_pmoc.plano_id', $planoId);
        $this->db->order_by('checklist_os_pmoc.data_verificacao', 'desc');

        return $this->db->get()->result();
    }

    public function addReparo($data)
    {
        if (! $this->db->table_exists('pmoc_reparos')) {
            return false;
        }
        $this->db->insert('pmoc_reparos', $data);
        return $this->db->insert_id();
    }

    public function getReparoById($reparoId)
    {
        if (! $this->db->table_exists('pmoc_reparos')) {
            return null;
        }

        $pk = $this->getPkReparo();
        return $this->db->where($pk, (int) $reparoId)->get('pmoc_reparos')->row();
    }

    public function updateReparoStatus($reparoId, $status)
    {
        if (! $this->db->table_exists('pmoc_reparos')) {
            return false;
        }

        $status = mb_strtolower(trim((string) $status));
        if (! in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            return false;
        }

        $data = [
            'status' => $status,
            'data_atualizacao' => date('Y-m-d H:i:s'),
        ];

        $this->db->where($this->getPkReparo(), (int) $reparoId);
        return $this->db->update('pmoc_reparos', $data);
    }

    private function getPkReparo()
    {
        foreach (['id', 'idReparo', 'id_pmoc_reparo', 'idPmocReparo'] as $campo) {
            if ($this->db->field_exists($campo, 'pmoc_reparos')) {
                return $campo;
            }
        }
        return 'id';
    }

    public function getReparos($planoId, $status = null)
    {
        if (! $this->db->table_exists('pmoc_reparos')) {
            return [];
        }

        $this->db->select('pmoc_reparos.*, equipamentos.descricao as equipamento_descricao, cliente_unidades.nome as unidade_nome');
        $this->db->from('pmoc_reparos');
        $this->db->join('equipamentos', 'equipamentos.idEquipamentos = pmoc_reparos.equipamento_id', 'left');
        if ($this->db->table_exists('cliente_unidades')) {
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = pmoc_reparos.cliente_unidade_id', 'left');
        }
        $this->db->where('pmoc_reparos.plano_id', $planoId);
        if ($status) {
            $this->db->where('pmoc_reparos.status', $status);
        }
        $this->db->order_by('pmoc_reparos.data_solicitacao', 'desc');

        return $this->db->get()->result();
    }

    public function getPlanoCompletoCliente($clienteId)
    {
        $plano = $this->getByClienteId($clienteId);
        if (! $plano) {
            return null;
        }

        return (object) [
            'plano' => $plano,
            'unidades' => $this->getUnidades($clienteId),
            'equipamentos' => $this->getEquipamentos($clienteId),
            'cronograma' => $this->getCronograma($plano),
            'relatorios' => $this->getRelatoriosByPlano($plano->id_pmoc),
            'reparos' => $this->getReparos($plano->id_pmoc),
            'resumo_os' => $this->getResumoOsByPlano($plano->id_pmoc),
        ];
    }

    private function normalizarStatus($status, $dataPrevista = null)
    {
        $status = mb_strtolower(trim((string) $status));

        if (in_array($status, ['pendente', 'aberta', 'aberto'], true)) {
            if ($dataPrevista && strtotime($dataPrevista) < strtotime(date('Y-m-d'))) {
                return 'atrasado';
            }
            return 'pendente';
        }

        if (in_array($status, ['agendado'], true)) {
            return 'agendado';
        }

        if (in_array($status, ['em execucao', 'em execução', 'em andamento'], true)) {
            return 'em_execucao';
        }

        if (in_array($status, ['concluido', 'concluído', 'finalizado'], true)) {
            return 'concluido';
        }

        if (in_array($status, ['atrasado'], true)) {
            return 'atrasado';
        }

        return 'pendente';
    }
}

