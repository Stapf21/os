# Padrão de Implementação - Sistema de Ordem de Serviço

## 1. Estrutura do Sistema

### 1.1 Backend (PHP/CodeIgniter)

#### 1.1.1 Models
- Localização: `application/models/`
- Nomenclatura: `NomeModulo_model.php`
- Responsabilidades:
  - Lógica de negócio
  - Acesso ao banco de dados
  - Validações de dados
  - Relacionamentos entre tabelas

```php
class NovoModulo_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    // Métodos CRUD básicos
    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array') {
        // Implementação
    }
    
    public function getById($id) {
        // Implementação
    }
    
    public function add($table, $data) {
        // Implementação
    }
    
    public function edit($table, $data, $fieldID, $ID) {
        // Implementação
    }
    
    public function delete($table, $fieldID, $ID) {
        // Implementação
    }
}
```

#### 1.1.2 Controllers
- Localização: `application/controllers/`
- Nomenclatura: `NomeModulo.php`
- Responsabilidades:
  - Controle de fluxo
  - Processamento de requisições
  - Validação de permissões
  - Respostas HTTP

```php
class NovoModulo extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('novoModulo_model');
        $this->permission->checkPermission($this->session->userdata('permissao'), 'vNovoModulo');
    }
    
    public function index() {
        // Implementação
    }
    
    public function adicionar() {
        // Implementação
    }
    
    public function editar($id) {
        // Implementação
    }
    
    public function visualizar($id) {
        // Implementação
    }
}
```

#### 1.1.3 Views
- Localização: `application/views/nomeModulo/`
- Arquivos padrão:
  - `index.php` - Listagem
  - `adicionar.php` - Formulário de adição
  - `editar.php` - Formulário de edição
  - `visualizar.php` - Visualização detalhada

### 1.2 Frontend

#### 1.2.1 Assets
- CSS: `assets/css/nomeModulo.css`
- JavaScript: `assets/js/nomeModulo.js`
- Imagens: `assets/img/nomeModulo/`

#### 1.2.2 Estrutura JavaScript
```javascript
$(document).ready(function() {
    // Inicialização
    init();
    
    // Eventos
    bindEvents();
    
    // Funções
    function init() {
        // Inicialização do módulo
    }
    
    function bindEvents() {
        // Bind de eventos
    }
});
```

## 2. Padrões de Código

### 2.1 Backend

#### 2.1.1 Validações
```php
$this->load->library('form_validation');

$this->form_validation->set_rules('campo', 'Campo', 'required|trim');
$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

if ($this->form_validation->run() == false) {
    $this->data['custom_error'] = (validation_errors() ? true : false);
} else {
    // Processamento
}
```

#### 2.1.2 Respostas JSON
```php
$this->response([
    'status' => true,
    'message' => 'Operação realizada com sucesso!',
    'data' => $data
], REST_Controller::HTTP_OK);
```

#### 2.1.3 Logs
```php
log_info('Ação realizada. ID: ' . $id);
log_error('Erro ocorrido: ' . $error);
```

### 2.2 Frontend

#### 2.2.1 Requisições AJAX
```javascript
$.ajax({
    url: base_url + 'modulo/acao',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
        if (response.status) {
            Swal.fire('Sucesso!', response.message, 'success');
        } else {
            Swal.fire('Erro!', response.message, 'error');
        }
    },
    error: function(xhr, status, error) {
        Swal.fire('Erro!', 'Ocorreu um erro na requisição.', 'error');
    }
});
```

#### 2.2.2 Validações
```javascript
function validateForm() {
    let isValid = true;
    
    // Validações
    if (!campo) {
        isValid = false;
        showError('Campo é obrigatório');
    }
    
    return isValid;
}
```

## 3. Segurança

### 3.1 Backend
- Usar prepared statements
- Sanitizar inputs
- Validar permissões
- Implementar CSRF protection
- Registrar logs de auditoria

### 3.2 Frontend
- Validar dados antes do envio
- Sanitizar outputs
- Implementar rate limiting
- Usar HTTPS
- Proteger contra XSS

## 4. Integração de Novos Módulos

### 4.1 Passos
1. Criar tabela no banco de dados
2. Criar model
3. Criar controller
4. Criar views
5. Adicionar assets
6. Configurar permissões
7. Adicionar menu
8. Testar funcionalidades

### 4.2 Exemplo de Implementação

#### 4.2.1 Banco de Dados
```sql
CREATE TABLE IF NOT EXISTS `novo_modulo` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `campo` VARCHAR(255) NOT NULL,
    `data_criacao` DATETIME NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 4.2.2 Model
```php
class NovoModulo_model extends CI_Model {
    // Implementação
}
```

#### 4.2.3 Controller
```php
class NovoModulo extends CI_Controller {
    // Implementação
}
```

#### 4.2.4 View
```php
<div class="widget-box">
    <div class="widget-title">
        <h5>Novo Módulo</h5>
    </div>
    <div class="widget-content">
        <!-- Conteúdo -->
    </div>
</div>
```

## 5. Boas Práticas

### 5.1 Código
- Seguir PSR-12
- Documentar funções
- Usar nomes descritivos
- Manter código DRY
- Implementar testes

### 5.2 Performance
- Otimizar queries
- Usar cache quando possível
- Minificar assets
- Implementar lazy loading
- Otimizar imagens

### 5.3 Manutenção
- Manter documentação atualizada
- Versionar código
- Realizar code review
- Manter logs organizados
- Fazer backup regular

## 6. Checklist de Implementação

- [ ] Criar estrutura de banco de dados
- [ ] Implementar model
- [ ] Implementar controller
- [ ] Criar views
- [ ] Adicionar assets
- [ ] Configurar permissões
- [ ] Adicionar ao menu
- [ ] Implementar validações
- [ ] Adicionar logs
- [ ] Testar funcionalidades
- [ ] Documentar código
- [ ] Realizar code review
- [ ] Fazer deploy
- [ ] Monitorar erros 