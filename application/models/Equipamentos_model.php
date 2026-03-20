<?php

class Equipamentos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Buscar todos os equipamentos de um cliente
    public function getByClienteId($clientes_id, $cliente_unidade_id = null)
    {
        $this->db->select('equipamentos.*');
        if ($this->db->table_exists('cliente_unidades') && $this->db->field_exists('cliente_unidade_id', 'equipamentos')) {
            $this->db->select('cliente_unidades.nome as unidade_nome');
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = equipamentos.cliente_unidade_id', 'left');
        }
        $this->db->where('clientes_id', $clientes_id);
        if ($cliente_unidade_id && $this->db->field_exists('cliente_unidade_id', 'equipamentos')) {
            $this->db->where('cliente_unidade_id', $cliente_unidade_id);
        }
        $this->db->order_by('descricao', 'asc');
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
        $this->db->select('equipamentos.*');
        if ($this->db->table_exists('cliente_unidades') && $this->db->field_exists('cliente_unidade_id', 'equipamentos')) {
            $this->db->select('cliente_unidades.nome as unidade_nome');
            $this->db->join('cliente_unidades', 'cliente_unidades.idClienteUnidade = equipamentos.cliente_unidade_id', 'left');
        }
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
