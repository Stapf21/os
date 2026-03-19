<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pnl_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getClientesResumo($dataInicio, $dataFim, $grupo = null)
    {
        $hasGrupo = $this->hasField('clientes', 'grupo_empresarial');
        $hasTipoConsolidacao = $this->hasField('clientes', 'tipo_consolidacao_pnl');
        $hasClienteCustos = $this->hasTable('cliente_custos');
        $hasClienteUnidades = $this->hasTable('cliente_unidades');
        $hasClienteAtivos = $this->hasTable('cliente_ativos');

        $params = [$dataInicio, $dataFim];
        $sql = "SELECT
                    c.idClientes,
                    c.nomeCliente,
                    " . ($hasGrupo ? "c.grupo_empresarial" : "''") . " AS grupo_empresarial,
                    " . ($hasTipoConsolidacao ? "c.tipo_consolidacao_pnl" : "'consolidado'") . " AS tipo_consolidacao_pnl,
                    COALESCE(fin.receitas, 0) AS receitas,
                    COALESCE(fin.custos_financeiros, 0) AS custos_financeiros,
                    COALESCE(cc.custos_diretos, 0) AS custos_diretos,
                    COALESCE(u.total_unidades, 0) AS total_unidades,
                    COALESCE(a.total_ativos, 0) AS total_ativos
                FROM clientes c
                LEFT JOIN (
                    SELECT
                        clientes_id,
                        SUM(CASE WHEN tipo = 'receita' THEN COALESCE(NULLIF(valor_desconto, 0), valor) ELSE 0 END) AS receitas,
                        SUM(CASE WHEN tipo = 'despesa' THEN COALESCE(NULLIF(valor_desconto, 0), valor) ELSE 0 END) AS custos_financeiros
                    FROM lancamentos
                    WHERE clientes_id IS NOT NULL
                      AND DATE(COALESCE(NULLIF(data_pagamento, '0000-00-00'), data_vencimento)) BETWEEN ? AND ?
                    GROUP BY clientes_id
                ) fin ON fin.clientes_id = c.idClientes";

        if ($hasClienteCustos) {
            $sql .= "
                LEFT JOIN (
                    SELECT clientes_id, SUM(valor) AS custos_diretos
                    FROM cliente_custos
                    WHERE data_referencia BETWEEN ? AND ?
                    GROUP BY clientes_id
                ) cc ON cc.clientes_id = c.idClientes";
            $params[] = $dataInicio;
            $params[] = $dataFim;
        } else {
            $sql .= " LEFT JOIN (SELECT NULL AS clientes_id, 0 AS custos_diretos) cc ON cc.clientes_id = c.idClientes";
        }

        if ($hasClienteUnidades) {
            $sql .= "
                LEFT JOIN (
                    SELECT clientes_id, COUNT(*) AS total_unidades
                    FROM cliente_unidades
                    GROUP BY clientes_id
                ) u ON u.clientes_id = c.idClientes";
        } else {
            $sql .= " LEFT JOIN (SELECT NULL AS clientes_id, 0 AS total_unidades) u ON u.clientes_id = c.idClientes";
        }

        if ($hasClienteAtivos) {
            $sql .= "
                LEFT JOIN (
                    SELECT clientes_id, COUNT(*) AS total_ativos
                    FROM cliente_ativos
                    GROUP BY clientes_id
                ) a ON a.clientes_id = c.idClientes";
        } else {
            $sql .= " LEFT JOIN (SELECT NULL AS clientes_id, 0 AS total_ativos) a ON a.clientes_id = c.idClientes";
        }

        if ($grupo && $hasGrupo) {
            $sql .= " WHERE c.grupo_empresarial LIKE ?";
            $params[] = '%' . $grupo . '%';
        }

        $sql .= " ORDER BY (COALESCE(fin.receitas, 0) - COALESCE(fin.custos_financeiros, 0) - COALESCE(cc.custos_diretos, 0)) DESC, c.nomeCliente ASC";

        $query = $this->db->query($sql, $params);
        if (! $query) {
            log_message('error', 'Pnl_model::getClientesResumo SQL error: ' . $this->db->error()['message']);
            return [];
        }
        return $query->result();
    }

    public function getCliente($clienteId)
    {
        return $this->db->where('idClientes', $clienteId)->get('clientes')->row();
    }

    public function getResumoCliente($clienteId, $dataInicio, $dataFim, $unidadeId = null)
    {
        $hasClienteUnidadeLancamentos = $this->hasField('lancamentos', 'cliente_unidade_id');
        $hasClienteCustos = $this->hasTable('cliente_custos');

        $this->db->select("
            SUM(CASE WHEN tipo = 'receita' THEN COALESCE(NULLIF(valor_desconto, 0), valor) ELSE 0 END) AS receitas,
            SUM(CASE WHEN tipo = 'despesa' THEN COALESCE(NULLIF(valor_desconto, 0), valor) ELSE 0 END) AS custos_financeiros
        ", false);
        $this->db->from('lancamentos');
        $this->db->where('clientes_id', $clienteId);
        $this->db->where("DATE(COALESCE(NULLIF(data_pagamento, '0000-00-00'), data_vencimento)) >= " . $this->db->escape($dataInicio), null, false);
        $this->db->where("DATE(COALESCE(NULLIF(data_pagamento, '0000-00-00'), data_vencimento)) <= " . $this->db->escape($dataFim), null, false);

        if ($unidadeId && $hasClienteUnidadeLancamentos) {
            $this->db->where('cliente_unidade_id', $unidadeId);
        }

        $financeiro = $this->db->get()->row();
        $custosDiretos = (object) ['valor' => 0];

        if ($hasClienteCustos) {
            $this->db->select_sum('valor');
            $this->db->from('cliente_custos');
            $this->db->where('clientes_id', $clienteId);
            $this->db->where('data_referencia >=', $dataInicio);
            $this->db->where('data_referencia <=', $dataFim);
            if ($unidadeId && $this->hasField('cliente_custos', 'cliente_unidade_id')) {
                $this->db->where('cliente_unidade_id', $unidadeId);
            }
            $queryCustos = $this->db->get();
            if ($queryCustos) {
                $custosDiretos = $queryCustos->row();
            }
        }

        return (object) [
            'receitas' => (float) ($financeiro->receitas ?? 0),
            'custos_financeiros' => (float) ($financeiro->custos_financeiros ?? 0),
            'custos_diretos' => (float) ($custosDiretos->valor ?? 0),
        ];
    }

    public function getUnidades($clienteId)
    {
        if (! $this->hasTable('cliente_unidades')) {
            return [];
        }
        return $this->db
            ->where('clientes_id', $clienteId)
            ->order_by('nome', 'asc')
            ->get('cliente_unidades')
            ->result();
    }

    public function getAtivos($clienteId)
    {
        if (! $this->hasTable('cliente_ativos')) {
            return [];
        }

        $this->db->select('cliente_ativos.*, cliente_unidades.nome as unidade_nome');
        $this->db->from('cliente_ativos');
        if ($this->hasTable('cliente_unidades')) {
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = cliente_ativos.cliente_unidade_id', 'left');
        }
        $this->db->where('cliente_ativos.clientes_id', $clienteId);
        $this->db->order_by('cliente_ativos.nome', 'asc');

        return $this->db->get()->result();
    }

    public function getCustos($clienteId, $dataInicio, $dataFim, $unidadeId = null)
    {
        if (! $this->hasTable('cliente_custos')) {
            return [];
        }

        $this->db->select('cliente_custos.*, cliente_unidades.nome as unidade_nome, cliente_ativos.nome as ativo_nome');
        $this->db->from('cliente_custos');
        if ($this->hasTable('cliente_unidades')) {
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = cliente_custos.cliente_unidade_id', 'left');
        }
        if ($this->hasTable('cliente_ativos')) {
            $this->db->join('cliente_ativos', 'cliente_ativos.idClienteAtivo = cliente_custos.cliente_ativo_id', 'left');
        }
        $this->db->where('cliente_custos.clientes_id', $clienteId);
        $this->db->where('cliente_custos.data_referencia >=', $dataInicio);
        $this->db->where('cliente_custos.data_referencia <=', $dataFim);

        if ($unidadeId && $this->hasField('cliente_custos', 'cliente_unidade_id')) {
            $this->db->where('cliente_custos.cliente_unidade_id', $unidadeId);
        }

        $this->db->order_by('cliente_custos.data_referencia', 'desc');

        return $this->db->get()->result();
    }

    public function getLancamentosCliente($clienteId, $dataInicio, $dataFim)
    {
        return $this->db
            ->where('clientes_id', $clienteId)
            ->where("DATE(COALESCE(NULLIF(data_pagamento, '0000-00-00'), data_vencimento)) >= " . $this->db->escape($dataInicio), null, false)
            ->where("DATE(COALESCE(NULLIF(data_pagamento, '0000-00-00'), data_vencimento)) <= " . $this->db->escape($dataFim), null, false)
            ->order_by('data_vencimento', 'desc')
            ->get('lancamentos')
            ->result();
    }

    public function getResumoPorUnidade($clienteId, $dataInicio, $dataFim)
    {
        if (! $this->hasTable('cliente_unidades')) {
            return [];
        }

        $hasClienteCustos = $this->hasTable('cliente_custos');
        $hasClienteUnidadeLancamentos = $this->hasField('lancamentos', 'cliente_unidade_id');

        if (! $hasClienteUnidadeLancamentos) {
            return $this->db
                ->select("idClienteUnidade, nome, empresa, 0 AS receitas, 0 AS custos_financeiros, 0 AS custos_diretos", false)
                ->where('clientes_id', $clienteId)
                ->order_by('nome', 'asc')
                ->get('cliente_unidades')
                ->result();
        }

        $sql = "SELECT
                    u.idClienteUnidade,
                    u.nome,
                    u.empresa,
                    COALESCE(fin.receitas, 0) AS receitas,
                    COALESCE(fin.custos_financeiros, 0) AS custos_financeiros,
                    COALESCE(cc.custos_diretos, 0) AS custos_diretos
                FROM cliente_unidades u
                LEFT JOIN (
                    SELECT
                        cliente_unidade_id,
                        SUM(CASE WHEN tipo = 'receita' THEN COALESCE(NULLIF(valor_desconto, 0), valor) ELSE 0 END) AS receitas,
                        SUM(CASE WHEN tipo = 'despesa' THEN COALESCE(NULLIF(valor_desconto, 0), valor) ELSE 0 END) AS custos_financeiros
                    FROM lancamentos
                    WHERE clientes_id = ?
                      AND cliente_unidade_id IS NOT NULL
                      AND DATE(COALESCE(NULLIF(data_pagamento, '0000-00-00'), data_vencimento)) BETWEEN ? AND ?
                    GROUP BY cliente_unidade_id
                ) fin ON fin.cliente_unidade_id = u.idClienteUnidade";

        $params = [
            $clienteId,
            $dataInicio,
            $dataFim,
        ];

        if ($hasClienteCustos) {
            $sql .= "
                LEFT JOIN (
                    SELECT cliente_unidade_id, SUM(valor) AS custos_diretos
                    FROM cliente_custos
                    WHERE clientes_id = ?
                      AND data_referencia BETWEEN ? AND ?
                    GROUP BY cliente_unidade_id
                ) cc ON cc.cliente_unidade_id = u.idClienteUnidade";

            $params[] = $clienteId;
            $params[] = $dataInicio;
            $params[] = $dataFim;
        } else {
            $sql .= " LEFT JOIN (SELECT NULL AS cliente_unidade_id, 0 AS custos_diretos) cc ON cc.cliente_unidade_id = u.idClienteUnidade";
        }

        $sql .= "
                WHERE u.clientes_id = ?
                ORDER BY u.nome ASC";
        $params[] = $clienteId;

        $query = $this->db->query($sql, $params);
        if (! $query) {
            log_message('error', 'Pnl_model::getResumoPorUnidade SQL error: ' . $this->db->error()['message']);
            return [];
        }
        return $query->result();
    }

    public function addUnidade($data)
    {
        if (! $this->hasTable('cliente_unidades')) {
            return false;
        }
        return $this->db->insert('cliente_unidades', $data);
    }

    public function addAtivo($data)
    {
        if (! $this->hasTable('cliente_ativos')) {
            return false;
        }
        return $this->db->insert('cliente_ativos', $data);
    }

    public function addCusto($data)
    {
        if (! $this->hasTable('cliente_custos')) {
            return false;
        }
        return $this->db->insert('cliente_custos', $data);
    }

    public function updateLancamento($lancamentoId, $data)
    {
        if (! $this->hasField('lancamentos', 'cliente_unidade_id')) {
            unset($data['cliente_unidade_id']);
        }
        if (! $this->hasField('lancamentos', 'categoria_pnl')) {
            unset($data['categoria_pnl']);
        }
        if (! $this->hasField('lancamentos', 'origem_pnl')) {
            unset($data['origem_pnl']);
        }
        return $this->db->where('idLancamentos', $lancamentoId)->update('lancamentos', $data);
    }

    private function hasTable($table)
    {
        return $this->db->table_exists($table);
    }

    private function hasField($table, $field)
    {
        if (! $this->hasTable($table)) {
            return false;
        }
        return $this->db->field_exists($field, $table);
    }
}
