<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_pnl_cliente_structure extends CI_Migration
{
    public function up()
    {
        if (! $this->db->field_exists('grupo_empresarial', 'clientes')) {
            $this->dbforge->add_column('clientes', [
                'grupo_empresarial' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => true,
                ],
            ]);
        }

        if (! $this->db->field_exists('tipo_consolidacao_pnl', 'clientes')) {
            $this->dbforge->add_column('clientes', [
                'tipo_consolidacao_pnl' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => false,
                    'default' => 'consolidado',
                ],
            ]);
        }

        if (! $this->db->field_exists('cliente_unidade_id', 'lancamentos')) {
            $this->dbforge->add_column('lancamentos', [
                'cliente_unidade_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
            ]);
        }

        if (! $this->db->field_exists('categoria_pnl', 'lancamentos')) {
            $this->dbforge->add_column('lancamentos', [
                'categoria_pnl' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                ],
            ]);
        }

        if (! $this->db->field_exists('origem_pnl', 'lancamentos')) {
            $this->dbforge->add_column('lancamentos', [
                'origem_pnl' => [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => false,
                    'default' => 'financeiro',
                ],
            ]);
        }

        if (! $this->db->table_exists('cliente_unidades')) {
            $this->dbforge->add_field([
                'idClienteUnidade' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                    'null' => false,
                ],
                'clientes_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => false,
                ],
                'empresa' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => true,
                ],
                'codigo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                ],
                'cidade' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'null' => true,
                ],
                'estado' => [
                    'type' => 'VARCHAR',
                    'constraint' => 2,
                    'null' => true,
                ],
                'ativa' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'null' => false,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);
            $this->dbforge->add_key('idClienteUnidade', true);
            $this->dbforge->create_table('cliente_unidades', true);
            $this->db->query('ALTER TABLE `cliente_unidades` ENGINE = InnoDB');
        }

        if (! $this->db->table_exists('cliente_ativos')) {
            $this->dbforge->add_field([
                'idClienteAtivo' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                    'null' => false,
                ],
                'clientes_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'cliente_unidade_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => false,
                ],
                'tipo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                ],
                'identificador' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'null' => true,
                ],
                'descricao' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'custo_mensal_estimado' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => false,
                    'default' => 0,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);
            $this->dbforge->add_key('idClienteAtivo', true);
            $this->dbforge->create_table('cliente_ativos', true);
            $this->db->query('ALTER TABLE `cliente_ativos` ENGINE = InnoDB');
        }

        if (! $this->db->table_exists('cliente_custos')) {
            $this->dbforge->add_field([
                'idClienteCusto' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                    'null' => false,
                ],
                'clientes_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'cliente_unidade_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'cliente_ativo_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'categoria' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => false,
                ],
                'descricao' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
                'tipo_custo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => false,
                    'default' => 'outros',
                ],
                'valor' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => false,
                    'default' => 0,
                ],
                'data_referencia' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
                'observacoes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);
            $this->dbforge->add_key('idClienteCusto', true);
            $this->dbforge->create_table('cliente_custos', true);
            $this->db->query('ALTER TABLE `cliente_custos` ENGINE = InnoDB');
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('cliente_custos', true);
        $this->dbforge->drop_table('cliente_ativos', true);
        $this->dbforge->drop_table('cliente_unidades', true);

        if ($this->db->field_exists('origem_pnl', 'lancamentos')) {
            $this->dbforge->drop_column('lancamentos', 'origem_pnl');
        }
        if ($this->db->field_exists('categoria_pnl', 'lancamentos')) {
            $this->dbforge->drop_column('lancamentos', 'categoria_pnl');
        }
        if ($this->db->field_exists('cliente_unidade_id', 'lancamentos')) {
            $this->dbforge->drop_column('lancamentos', 'cliente_unidade_id');
        }
        if ($this->db->field_exists('tipo_consolidacao_pnl', 'clientes')) {
            $this->dbforge->drop_column('clientes', 'tipo_consolidacao_pnl');
        }
        if ($this->db->field_exists('grupo_empresarial', 'clientes')) {
            $this->dbforge->drop_column('clientes', 'grupo_empresarial');
        }
    }
}
