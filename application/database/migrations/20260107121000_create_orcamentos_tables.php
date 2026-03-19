<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_orcamentos_tables extends CI_Migration
{
    public function up()
    {
        // Tabela orcamentos
        $this->dbforge->add_field([
            'idOrcamento' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'dataCriacao' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'validade' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false,
            ],
            'clientes_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'condicoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
            ],
            'valor_desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
            ],
            'tipo_desconto' => [
                'type' => 'VARCHAR',
                'constraint' => 8,
                'null' => true,
            ],
        ]);
        $this->dbforge->add_key('idOrcamento', true);
        $this->dbforge->create_table('orcamentos', true);
        $this->db->query('ALTER TABLE `orcamentos` ENGINE = InnoDB');

        // Tabela orcamentos_produtos
        $this->dbforge->add_field([
            'idOrcamentoProduto' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'quantidade' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => false,
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'preco' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
            ],
            'orcamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'produtos_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'subTotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
            ],
        ]);
        $this->dbforge->add_key('idOrcamentoProduto', true);
        $this->dbforge->create_table('orcamentos_produtos', true);
        $this->db->query('ALTER TABLE `orcamentos_produtos` ENGINE = InnoDB');

        // Tabela orcamentos_servicos
        $this->dbforge->add_field([
            'idOrcamentoServico' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'servico' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'quantidade' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'preco' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
            ],
            'orcamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'servicos_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'subTotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
            ],
        ]);
        $this->dbforge->add_key('idOrcamentoServico', true);
        $this->dbforge->create_table('orcamentos_servicos', true);
        $this->db->query('ALTER TABLE `orcamentos_servicos` ENGINE = InnoDB');

        // Adiciona referencia do orcamento em OS
        if (! $this->db->field_exists('orcamento_id', 'os')) {
            $this->dbforge->add_column('os', [
                'orcamento_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('orcamento_id', 'os')) {
            $this->dbforge->drop_column('os', 'orcamento_id');
        }
        $this->dbforge->drop_table('orcamentos_servicos', true);
        $this->dbforge->drop_table('orcamentos_produtos', true);
        $this->dbforge->drop_table('orcamentos', true);
    }
}
