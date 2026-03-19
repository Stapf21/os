<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_servicos_descricao_length extends CI_Migration
{
    public function up()
    {
        $this->dbforge->modify_column('servicos', array(
            'descricao' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
    }

    public function down()
    {
        $this->dbforge->modify_column('servicos', array(
            'descricao' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ),
        ));
    }
}
