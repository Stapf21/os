<?php

class Orcamentos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrcamentos($where = [], $perpage = 0, $start = 0)
    {
        $this->db->select(
            'orcamentos.*, clientes.nomeCliente, usuarios.nome, os.idOs as os_id,' .
            ' COALESCE((SELECT SUM(orcamentos_produtos.preco * orcamentos_produtos.quantidade) FROM orcamentos_produtos WHERE orcamentos_produtos.orcamento_id = orcamentos.idOrcamento), 0) totalProdutos,' .
            ' COALESCE((SELECT SUM(orcamentos_servicos.preco * orcamentos_servicos.quantidade) FROM orcamentos_servicos WHERE orcamentos_servicos.orcamento_id = orcamentos.idOrcamento), 0) totalServicos'
        );
        $this->db->from('orcamentos');
        $this->db->join('clientes', 'clientes.idClientes = orcamentos.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = orcamentos.usuarios_id');
        $this->db->join('os', 'os.orcamento_id = orcamentos.idOrcamento', 'left');

        if (array_key_exists('status', $where)) {
            $this->db->where_in('orcamentos.status', $where['status']);
        }
        if (array_key_exists('pesquisa', $where)) {
            $this->db->like('clientes.nomeCliente', $where['pesquisa']);
            $this->db->or_like('clientes.documento', $where['pesquisa']);
        }
        if (array_key_exists('de', $where)) {
            $this->db->where('dataCriacao >=', $where['de']);
        }
        if (array_key_exists('ate', $where)) {
            $this->db->where('dataCriacao <=', $where['ate']);
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('orcamentos.dataCriacao', 'desc');
        $this->db->order_by('orcamentos.idOrcamento', 'desc');
        $this->db->group_by('orcamentos.idOrcamento');

        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->select('orcamentos.*, clientes.nomeCliente, clientes.documento, clientes.email, clientes.telefone, clientes.celular, clientes.rua, clientes.numero, clientes.bairro, clientes.cidade, clientes.estado, clientes.cep, clientes.complemento, clientes.contato, usuarios.nome, usuarios.email as email_usuario');
        $this->db->from('orcamentos');
        $this->db->join('clientes', 'clientes.idClientes = orcamentos.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = orcamentos.usuarios_id');
        $this->db->where('orcamentos.idOrcamento', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getProdutos($orcamento_id)
    {
        $this->db->select('orcamentos_produtos.idOrcamentoProduto, orcamentos_produtos.quantidade, orcamentos_produtos.preco, orcamentos_produtos.orcamento_id, orcamentos_produtos.produtos_id, orcamentos_produtos.subTotal, COALESCE(orcamentos_produtos.descricao, produtos.descricao) as descricao, produtos.precoVenda');
        $this->db->from('orcamentos_produtos');
        $this->db->join('produtos', 'produtos.idProdutos = orcamentos_produtos.produtos_id');
        $this->db->where('orcamento_id', $orcamento_id);

        return $this->db->get()->result();
    }

    public function getServicos($orcamento_id)
    {
        $this->db->select('orcamentos_servicos.*, servicos.nome, servicos.preco as precoVenda');
        $this->db->from('orcamentos_servicos');
        $this->db->join('servicos', 'servicos.idServicos = orcamentos_servicos.servicos_id');
        $this->db->where('orcamento_id', $orcamento_id);

        return $this->db->get()->result();
    }

    public function getOsByOrcamento($orcamento_id)
    {
        return $this->db->select('idOs')->where('orcamento_id', $orcamento_id)->get('os')->row();
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $returnId ? $this->db->insert_id() : true;
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

    public function atualizarSemResposta()
    {
        $this->db->set('validade', 'DATE_ADD(dataCriacao, INTERVAL 7 DAY)', false);
        $this->db->where('status', 'Enviado ao cliente');
        $this->db->group_start();
        $this->db->where('validade', null);
        $this->db->or_where('validade', '0000-00-00');
        $this->db->group_end();
        $this->db->update('orcamentos');

        $this->db->set('status', 'Sem resposta');
        $this->db->where('status', 'Enviado ao cliente');
        $this->db->where('validade <=', date('Y-m-d'));
        $this->db->update('orcamentos');
    }
}
