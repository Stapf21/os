<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_orcamento_permissions extends CI_Migration
{
    public function up()
    {
        if (! $this->db->table_exists('permissoes')) {
            return;
        }

        $rows = $this->db->get('permissoes')->result();
        foreach ($rows as $row) {
            $permissoes = @unserialize($row->permissoes);
            if (! is_array($permissoes)) {
                continue;
            }

            if (! array_key_exists('vOrcamento', $permissoes)) {
                $permissoes['vOrcamento'] = $permissoes['vOs'] ?? 0;
            }
            if (! array_key_exists('aOrcamento', $permissoes)) {
                $permissoes['aOrcamento'] = $permissoes['aOs'] ?? 0;
            }
            if (! array_key_exists('eOrcamento', $permissoes)) {
                $permissoes['eOrcamento'] = $permissoes['eOs'] ?? 0;
            }
            if (! array_key_exists('dOrcamento', $permissoes)) {
                $permissoes['dOrcamento'] = $permissoes['dOs'] ?? 0;
            }

            $this->db->where('idPermissao', $row->idPermissao);
            $this->db->update('permissoes', ['permissoes' => serialize($permissoes)]);
        }
    }

    public function down()
    {
        if (! $this->db->table_exists('permissoes')) {
            return;
        }

        $rows = $this->db->get('permissoes')->result();
        foreach ($rows as $row) {
            $permissoes = @unserialize($row->permissoes);
            if (! is_array($permissoes)) {
                continue;
            }

            unset($permissoes['vOrcamento'], $permissoes['aOrcamento'], $permissoes['eOrcamento'], $permissoes['dOrcamento']);

            $this->db->where('idPermissao', $row->idPermissao);
            $this->db->update('permissoes', ['permissoes' => serialize($permissoes)]);
        }
    }
}
