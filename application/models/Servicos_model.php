<?php

class Servicos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idServicos', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->like('nome', $where);
            $this->db->or_like('descricao', $where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->where('idServicos', $id);
        $this->db->limit(1);

        return $this->db->get('servicos')->row();
    }

    public function getItens($servicoId)
    {
        $this->db->select('servicos_itens.*, produtos.descricao, produtos.unidade as unidade_produto, produtos.precoVenda');
        $this->db->from('servicos_itens');
        $this->db->join('produtos', 'produtos.idProdutos = servicos_itens.produtos_id');
        $this->db->where('servicos_itens.servicos_id', $servicoId);
        $this->db->order_by('servicos_itens.idServicoItem', 'asc');

        return $this->db->get()->result();
    }

    public function autoCompleteProduto($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('codDeBarra', $q);
        $this->db->or_like('descricao', $q);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['descricao'] . ' | Preco: R$ ' . $row['precoVenda'] . ' | Estoque: ' . $row['estoque'],
                    'estoque' => $row['estoque'],
                    'id' => $row['idProdutos'],
                    'preco' => $row['precoVenda'],
                    'unidade' => $row['unidade'],
                ];
            }
            echo json_encode($row_set);
        }
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}
