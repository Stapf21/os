<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_servicos_itens_table extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('servicos_itens')) {
            return;
        }

        $this->dbforge->add_field([
            'idServicoItem' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'servicos_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'produtos_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'quantidade' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => false,
            ],
            'unidade' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'observacao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'editavel' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
            ],
        ]);
        $this->dbforge->add_key('idServicoItem', true);
        $this->dbforge->add_key('servicos_id');
        $this->dbforge->add_key('produtos_id');
        $this->dbforge->create_table('servicos_itens', true);
        $this->db->query('ALTER TABLE `servicos_itens` ENGINE = InnoDB');
    }

    public function down()
    {
        $this->dbforge->drop_table('servicos_itens', true);
    }
}
