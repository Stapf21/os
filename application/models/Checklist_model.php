<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Checklist_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByOs($os_id)
    {
        $this->db->select('pmoc_checklists.*, equipamentos.*');
        $this->db->from('pmoc_checklists');
        $this->db->join('equipamentos', 'equipamentos.id = pmoc_checklists.equipamento_id');
        $this->db->where('pmoc_checklists.os_id', $os_id);
        return $this->db->get()->result();
    }

    public function getByPlano($plano_id)
    {
        $this->db->select('pmoc_checklists.*, equipamentos.*, os.*');
        $this->db->from('pmoc_checklists');
        $this->db->join('equipamentos', 'equipamentos.id = pmoc_checklists.equipamento_id');
        $this->db->join('os', 'os.idOs = pmoc_checklists.os_id');
        $this->db->where('pmoc_checklists.plano_id', $plano_id);
        $query = $this->db->get();
        if (!$query) {
            die('Erro na query: ' . $this->db->last_query() . ' - ' . print_r($this->db->error(), true));
        }
        return $query->result();
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
} 