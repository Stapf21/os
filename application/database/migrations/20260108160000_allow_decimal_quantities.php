<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_allow_decimal_quantities extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `produtos` MODIFY `estoque` DECIMAL(10,3) NOT NULL');
        $this->db->query('ALTER TABLE `produtos` MODIFY `estoqueMinimo` DECIMAL(10,3) NULL');
        $this->db->query('ALTER TABLE `produtos_os` MODIFY `quantidade` DECIMAL(10,3) NOT NULL');
        $this->db->query('ALTER TABLE `orcamentos_produtos` MODIFY `quantidade` DECIMAL(10,3) NOT NULL');
        $this->db->query('ALTER TABLE `itens_de_vendas` MODIFY `quantidade` DECIMAL(10,3) NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `produtos` MODIFY `estoque` INT(11) NOT NULL');
        $this->db->query('ALTER TABLE `produtos` MODIFY `estoqueMinimo` INT(11) NULL');
        $this->db->query('ALTER TABLE `produtos_os` MODIFY `quantidade` INT(11) NOT NULL');
        $this->db->query('ALTER TABLE `orcamentos_produtos` MODIFY `quantidade` INT(11) NOT NULL');
        $this->db->query('ALTER TABLE `itens_de_vendas` MODIFY `quantidade` INT(11) NULL');
    }
}
