<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_pmoc_cronograma_excecoes extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('pmoc_cronograma_excecoes')) {
            return;
        }

        $this->dbforge->add_field([
            'idPmocCronogramaExcecao' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'plano_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'cliente_unidade_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'data_prevista' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tipo' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'excluir',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->dbforge->add_key('idPmocCronogramaExcecao', true);
        $this->dbforge->create_table('pmoc_cronograma_excecoes', true);
        $this->db->query('ALTER TABLE `pmoc_cronograma_excecoes` ENGINE = InnoDB');
        $this->db->query('CREATE UNIQUE INDEX `idx_pmoc_crono_excecao_unique` ON `pmoc_cronograma_excecoes` (`plano_id`, `cliente_unidade_id`, `data_prevista`, `tipo`)');
    }

    public function down()
    {
        if ($this->db->table_exists('pmoc_cronograma_excecoes')) {
            $this->dbforge->drop_table('pmoc_cronograma_excecoes', true);
        }
    }
}

