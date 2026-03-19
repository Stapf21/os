<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_pmoc_plano_mensal_module extends CI_Migration
{
    public function up()
    {
        $this->createClienteUnidadesIfMissing();
        $this->createOrUpdatePmocPlanos();
        $this->createOsPmoc();
        $this->createEquipamentosOsPmoc();
        $this->createChecklistOsPmoc();
        $this->createChecklistFotos();
        $this->createPmocReparos();
        $this->updateEquipamentosTable();
    }

    public function down()
    {
        if ($this->db->table_exists('pmoc_reparos')) {
            $this->dbforge->drop_table('pmoc_reparos', true);
        }

        if ($this->db->table_exists('checklist_fotos')) {
            $this->dbforge->drop_table('checklist_fotos', true);
        }

        if ($this->db->table_exists('checklist_os_pmoc')) {
            $this->dbforge->drop_table('checklist_os_pmoc', true);
        }

        if ($this->db->table_exists('equipamentos_os_pmoc')) {
            $this->dbforge->drop_table('equipamentos_os_pmoc', true);
        }

        if ($this->db->table_exists('os_pmoc')) {
            $this->dbforge->drop_table('os_pmoc', true);
        }

        if ($this->db->table_exists('pmoc_planos')) {
            $this->dbforge->drop_table('pmoc_planos', true);
        }
    }

    private function createClienteUnidadesIfMissing()
    {
        if ($this->db->table_exists('cliente_unidades')) {
            return;
        }

        $this->dbforge->add_field([
            'idClienteUnidade' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
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
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->dbforge->add_key('idClienteUnidade', true);
        $this->dbforge->create_table('cliente_unidades', true);
        $this->db->query('ALTER TABLE `cliente_unidades` ENGINE = InnoDB');
    }

    private function createOrUpdatePmocPlanos()
    {
        if (! $this->db->table_exists('pmoc_planos')) {
            $this->dbforge->add_field([
                'id_pmoc' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'clientes_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'tecnico_responsavel' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'nome_plano' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => true,
                ],
                'valor_mensal' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0,
                ],
                'data_inicio_contrato' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'vigencia_ate' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'forma_pagamento' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                ],
                'status_contrato' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'ativo',
                ],
                'frequencia_manutencao' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'mensal',
                ],
                'tipo_atendimento_padrao' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'null' => true,
                ],
                'numero_art' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                ],
                'validade_art' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'local_instalacao' => [
                    'type' => 'VARCHAR',
                    'constraint' => 180,
                    'null' => true,
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'ativo',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->dbforge->add_key('id_pmoc', true);
            $this->dbforge->create_table('pmoc_planos', true);
            $this->db->query('ALTER TABLE `pmoc_planos` ENGINE = InnoDB');
            return;
        }

        $columns = [
            'nome_plano' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'valor_mensal' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'data_inicio_contrato' => ['type' => 'DATE', 'null' => true],
            'vigencia_ate' => ['type' => 'DATE', 'null' => true],
            'forma_pagamento' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'status_contrato' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'ativo'],
            'tipo_atendimento_padrao' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        foreach ($columns as $name => $definition) {
            if (! $this->db->field_exists($name, 'pmoc_planos')) {
                $this->dbforge->add_column('pmoc_planos', [$name => $definition]);
            }
        }
    }

    private function createOsPmoc()
    {
        if ($this->db->table_exists('os_pmoc')) {
            $columns = [
                'cliente_unidade_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'data_prevista' => ['type' => 'DATE', 'null' => true],
                'tipo_atendimento' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ];
            foreach ($columns as $name => $definition) {
                if (! $this->db->field_exists($name, 'os_pmoc')) {
                    $this->dbforge->add_column('os_pmoc', [$name => $definition]);
                }
            }
            return;
        }

        $this->dbforge->add_field([
            'idOsPmoc' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'plano_id' => [
                'type' => 'INT',
                'constraint' => 11,
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
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pendente',
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tipo_atendimento' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'data_prevista' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'dataInicial' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'dataFinal' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->dbforge->add_key('idOsPmoc', true);
        $this->dbforge->create_table('os_pmoc', true);
        $this->db->query('ALTER TABLE `os_pmoc` ENGINE = InnoDB');
    }

    private function createEquipamentosOsPmoc()
    {
        if ($this->db->table_exists('equipamentos_os_pmoc')) {
            return;
        }

        $this->dbforge->add_field([
            'idEquipamentoOsPmoc' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'os_pmoc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'equipamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
        ]);
        $this->dbforge->add_key('idEquipamentoOsPmoc', true);
        $this->dbforge->create_table('equipamentos_os_pmoc', true);
        $this->db->query('ALTER TABLE `equipamentos_os_pmoc` ENGINE = InnoDB');
    }

    private function createChecklistOsPmoc()
    {
        if ($this->db->table_exists('checklist_os_pmoc')) {
            $columns = [
                'tecnico_responsavel' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'tipo_servico' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'checklist_json' => ['type' => 'LONGTEXT', 'null' => true],
            ];
            foreach ($columns as $name => $definition) {
                if (! $this->db->field_exists($name, 'checklist_os_pmoc')) {
                    $this->dbforge->add_column('checklist_os_pmoc', [$name => $definition]);
                }
            }
            return;
        }

        $this->dbforge->add_field([
            'idChecklist' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'os_pmoc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'equipamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'limpeza_filtros' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'carga_gas' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'condicoes_isolamento' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'estado_serpentina' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'bandeja_condensado' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'fiacao_conexoes' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'dreno' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'painel_eletrico' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'grelhas_difusores' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'ruidos_anormais' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'bomba_drenagem' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'controle_termostato' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'vazamentos_identificados' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'tecnico_responsavel' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'tipo_servico' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'checklist_json' => ['type' => 'LONGTEXT', 'null' => true],
            'data_verificacao' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->dbforge->add_key('idChecklist', true);
        $this->dbforge->create_table('checklist_os_pmoc', true);
        $this->db->query('ALTER TABLE `checklist_os_pmoc` ENGINE = InnoDB');
    }

    private function createChecklistFotos()
    {
        if ($this->db->table_exists('checklist_fotos')) {
            return;
        }

        $this->dbforge->add_field([
            'idChecklistFoto' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'checklist_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'campo' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => false,
            ],
            'nome_arquivo' => [
                'type' => 'VARCHAR',
                'constraint' => 180,
                'null' => false,
            ],
        ]);
        $this->dbforge->add_key('idChecklistFoto', true);
        $this->dbforge->create_table('checklist_fotos', true);
        $this->db->query('ALTER TABLE `checklist_fotos` ENGINE = InnoDB');
    }

    private function createPmocReparos()
    {
        if ($this->db->table_exists('pmoc_reparos')) {
            return;
        }

        $this->dbforge->add_field([
            'idReparo' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'plano_id' => [
                'type' => 'INT',
                'constraint' => 11,
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
            'equipamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => false,
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'aberto',
            ],
            'origem' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'cliente',
            ],
            'data_solicitacao' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'data_conclusao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->dbforge->add_key('idReparo', true);
        $this->dbforge->create_table('pmoc_reparos', true);
        $this->db->query('ALTER TABLE `pmoc_reparos` ENGINE = InnoDB');
    }

    private function updateEquipamentosTable()
    {
        if (! $this->db->table_exists('equipamentos')) {
            return;
        }

        $columns = [
            'marca' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'btu' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'local_instalacao' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'data_instalacao' => ['type' => 'DATE', 'null' => true],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'tipo_equipamento' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'cliente_unidade_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ];

        foreach ($columns as $name => $definition) {
            if (! $this->db->field_exists($name, 'equipamentos')) {
                $this->dbforge->add_column('equipamentos', [$name => $definition]);
            }
        }
    }
}