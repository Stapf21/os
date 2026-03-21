<?php
class OsPmoc_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->insert('os_pmoc', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function getById($id)
    {
        return $this->db->where('idOsPmoc', $id)->get('os_pmoc')->row();
    }

    public function getByPlano($plano_id)
    {
        return $this->db->where('plano_id', $plano_id)->get('os_pmoc')->result();
    }

    public function vincularEquipamento($os_pmoc_id, $equipamento_id)
    {
        $data = [
            'os_pmoc_id' => $os_pmoc_id,
            'equipamento_id' => $equipamento_id
        ];
        return $this->db->insert('equipamentos_os_pmoc', $data);
    }

    public function getAbertasByCliente($cliente_id)
    {
        return $this->db->where('clientes_id', $cliente_id)
            ->where('status', 'aberta')
            ->order_by('dataInicial', 'desc')
            ->get('os_pmoc')->result();
    }

    public function getEquipamentos($os_pmoc_id)
    {
        // 1) Fluxo principal: equipamentos vinculados diretamente na OS PMOC.
        $this->db->select('equipamentos.*');
        $this->db->from('equipamentos_os_pmoc');
        $this->db->join('equipamentos', 'equipamentos.idEquipamentos = equipamentos_os_pmoc.equipamento_id');
        $this->db->where('equipamentos_os_pmoc.os_pmoc_id', (int) $os_pmoc_id);
        $vinculados = $this->db->get()->result();
        if (!empty($vinculados)) {
            return $vinculados;
        }

        // 2) Fallback para OS antigas/sem vínculo: buscar por cliente/unidade.
        $os = $this->getById((int) $os_pmoc_id);
        if (!$os) {
            return [];
        }

        $this->db->select('equipamentos.*');
        $this->db->from('equipamentos');
        $this->db->where('equipamentos.clientes_id', (int) $os->clientes_id);

        if (!empty($os->cliente_unidade_id) && $this->db->field_exists('cliente_unidade_id', 'equipamentos')) {
            $this->db->where('equipamentos.cliente_unidade_id', (int) $os->cliente_unidade_id);
        }

        $this->db->order_by('equipamentos.descricao', 'asc');
        $fallback = $this->db->get()->result();

        // 3) Auto-vincular para manter consistência nas próximas consultas.
        if (!empty($fallback) && $this->db->table_exists('equipamentos_os_pmoc')) {
            foreach ($fallback as $eq) {
                $jaVinculado = $this->db
                    ->where('os_pmoc_id', (int) $os_pmoc_id)
                    ->where('equipamento_id', (int) $eq->idEquipamentos)
                    ->count_all_results('equipamentos_os_pmoc');

                if ((int) $jaVinculado === 0) {
                    $this->db->insert('equipamentos_os_pmoc', [
                        'os_pmoc_id' => (int) $os_pmoc_id,
                        'equipamento_id' => (int) $eq->idEquipamentos,
                    ]);
                }
            }
        }

        return $fallback;
    }

    public function update($id, $data)
    {
        $this->db->where('idOsPmoc', $id);
        return $this->db->update('os_pmoc', $data);
    }

    public function delete($id)
    {
        $this->db->where('os_pmoc_id', $id)->delete('equipamentos_os_pmoc');
        $this->db->where('os_pmoc_id', $id)->delete('checklist_os_pmoc');
        $this->db->where('idOsPmoc', $id);
        return $this->db->delete('os_pmoc');
    }
} 
