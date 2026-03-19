<?php

class Equipamentos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Buscar todos os equipamentos de um cliente
    public function getByClienteId($clientes_id)
    {
        $this->db->where('clientes_id', $clientes_id);
        return $this->db->get('equipamentos')->result();
    }

    // Buscar equipamentos com falha nos últimos X meses
    public function getComFalhaUltimosMeses($clientes_id, $meses = 6)
    {
        $this->db->select('equipamentos.*, COUNT(equipamentos_os.idEquipamentos_os) as total_falhas');
        $this->db->from('equipamentos');
        $this->db->join('equipamentos_os', 'equipamentos_os.equipamentos_id = equipamentos.idEquipamentos');
        $this->db->where('equipamentos.clientes_id', $clientes_id);
        $this->db->where('equipamentos_os.defeito_encontrado IS NOT NULL');
        $this->db->where('equipamentos_os.defeito_encontrado !=', '');
        $this->db->where('equipamentos_os.idEquipamentos_os IS NOT NULL');
        $this->db->where('equipamentos_os.os_id IS NOT NULL');
        $this->db->group_by('equipamentos.idEquipamentos');
        $query = $this->db->get();
        if ($query && is_object($query)) {
            return $query->result();
        }
        return [];
    }

    public function inserir($data)
    {
        return $this->db->insert('equipamentos', $data);
    }

    public function getById($id)
    {
        $this->db->where('idEquipamentos', $id);
        return $this->db->get('equipamentos')->row();
    }

    public function delete($id)
    {
        $this->db->where('idEquipamentos', $id);
        return $this->db->delete('equipamentos');
    }

    public function update($id, $data)
    {
        $this->db->where('idEquipamentos', $id);
        return $this->db->update('equipamentos', $data);
    }
} 