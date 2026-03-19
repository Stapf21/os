<?php
class ChecklistPmoc_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->insert('checklist_os_pmoc', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function getByOs($os_pmoc_id)
    {
        return $this->db->where('os_pmoc_id', $os_pmoc_id)->get('checklist_os_pmoc')->result();
    }

    public function getByEquipamento($equipamento_id)
    {
        return $this->db->where('equipamento_id', $equipamento_id)
            ->order_by('data_verificacao', 'desc')
            ->get('checklist_os_pmoc')->result();
    }

    public function delete($idChecklist)
    {
        return $this->db->where('idChecklist', $idChecklist)->delete('checklist_os_pmoc');
    }

    public function getRelatorioByPlano($planoId)
    {
        $this->db->select('
            checklist_os_pmoc.*,
            os_pmoc.idOsPmoc,
            os_pmoc.dataInicial,
            os_pmoc.dataFinal,
            equipamentos.descricao as equipamento_descricao,
            equipamentos.modelo as equipamento_modelo,
            equipamentos.num_serie as equipamento_num_serie
        ');
        $this->db->from('checklist_os_pmoc');
        $this->db->join('os_pmoc', 'os_pmoc.idOsPmoc = checklist_os_pmoc.os_pmoc_id');
        $this->db->join('equipamentos', 'equipamentos.idEquipamentos = checklist_os_pmoc.equipamento_id', 'left');
        $this->db->where('os_pmoc.plano_id', $planoId);
        $this->db->order_by('checklist_os_pmoc.data_verificacao', 'desc');

        return $this->db->get()->result();
    }
} 
