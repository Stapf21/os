<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pmoc_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $this->db->select('pmoc_planos.*, clientes.nomeCliente, usuarios.nome as tecnico_nome, pmoc_planos.frequencia_manutencao as frequencia, pmoc_planos.numero_art as art_numero, pmoc_planos.validade_art as art_validade, pmoc_planos.local_instalacao as local, pmoc_planos.id_pmoc as id');
        $this->db->from('pmoc_planos');
        $this->db->join('clientes', 'clientes.idClientes = pmoc_planos.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = pmoc_planos.tecnico_responsavel');
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->select('pmoc_planos.*, clientes.nomeCliente, usuarios.nome as tecnico_nome, pmoc_planos.frequencia_manutencao as frequencia, pmoc_planos.numero_art as art_numero, pmoc_planos.validade_art as art_validade, pmoc_planos.local_instalacao as local, pmoc_planos.id_pmoc as id');
        $this->db->from('pmoc_planos');
        $this->db->join('clientes', 'clientes.idClientes = pmoc_planos.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = pmoc_planos.tecnico_responsavel');
        $this->db->where('pmoc_planos.id_pmoc', $id);
        return $this->db->get()->row();
    }

    public function getHistoricoEquipamento($equipamento_id)
    {
        $this->db->select('pmoc_checklists.*, os.*, pmoc_planos.*');
        $this->db->from('pmoc_checklists');
        $this->db->join('os', 'os.idOs = pmoc_checklists.os_id');
        $this->db->join('pmoc_planos', 'pmoc_planos.id = pmoc_checklists.plano_id');
        $this->db->where('pmoc_checklists.equipamento_id', $equipamento_id);
        $this->db->order_by('pmoc_checklists.data_verificacao', 'DESC');
        return $this->db->get()->result();
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
} 
