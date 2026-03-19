<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_migrate_os_orcamentos extends CI_Migration
{
    public function up()
    {
        if (! $this->db->table_exists('orcamentos')) {
            return;
        }

        $orcamentosOs = $this->db->where_in('status', ['Orçamento', 'Orcamento'])->get('os')->result();
        foreach ($orcamentosOs as $os) {
            $data = [
                'dataCriacao' => $os->dataInicial,
                'validade' => $os->dataFinal,
                'status' => 'Rascunho',
                'clientes_id' => $os->clientes_id,
                'usuarios_id' => $os->usuarios_id,
                'observacoes' => $os->observacoes,
                'condicoes' => null,
                'desconto' => $os->desconto,
                'valor_desconto' => $os->valor_desconto,
                'tipo_desconto' => $os->tipo_desconto,
            ];
            $this->db->insert('orcamentos', $data);
            $orcamentoId = $this->db->insert_id();

            $this->db->where('idOs', $os->idOs);
            $this->db->update('os', ['orcamento_id' => $orcamentoId]);

            $produtos = $this->db->where('os_id', $os->idOs)->get('produtos_os')->result();
            foreach ($produtos as $produto) {
                $this->db->insert('orcamentos_produtos', [
                    'quantidade' => $produto->quantidade,
                    'descricao' => $produto->descricao,
                    'preco' => $produto->preco,
                    'orcamento_id' => $orcamentoId,
                    'produtos_id' => $produto->produtos_id,
                    'subTotal' => $produto->subTotal,
                ]);
            }

            $servicos = $this->db->where('os_id', $os->idOs)->get('servicos_os')->result();
            foreach ($servicos as $servico) {
                $this->db->insert('orcamentos_servicos', [
                    'servico' => $servico->servico,
                    'quantidade' => $servico->quantidade,
                    'preco' => $servico->preco,
                    'orcamento_id' => $orcamentoId,
                    'servicos_id' => $servico->servicos_id,
                    'subTotal' => $servico->subTotal,
                ]);
            }
        }
    }

    public function down()
    {
        // Sem rollback para migracao de dados
    }
}
