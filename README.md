# Comissão API

API RESTful desenvolvida com Laravel 10 (PHP 8.2+) para simular e gerenciar comissões de vendas em uma plataforma digital. A aplicação segue boas práticas de arquitetura, organização de código e validação de dados, com persistência em arquivo JSON (sem uso de banco de dados relacional).

---

## 🚀 Tecnologias utilizadas

* PHP 8.2+
* Laravel 10
* JSON File como armazenamento local
* PHPUnit para testes automatizados

---

## 📋 Desafio

Desenvolver uma API que simule o cálculo de comissões sobre vendas diretas e afiliadas:

* **Vendas Diretas**: plataforma (10%), produtor (90%)
* **Vendas Afiliadas**: plataforma (10%), produtor (60%), afiliado (30%)
* As comissões são sempre calculadas sobre o `valor_total` informado.

### 🎯 Objetivos do desafio:

* Calcular comissões com base no tipo de venda
* Registrar, listar, atualizar e remover simulações de vendas
* Persistência em memória (via arquivo JSON)
* Rota de health check

---

## 📐 Arquitetura e Organização

A API está dividida em camadas, cada uma com uma responsabilidade clara:

```
app/
├── Http/Controllers/
│   ├── SaleController.php          # Controla as rotas RESTful das vendas
│   └── HealthController.php       # Rota /health para verificação do sistema
├── Services/
│   └── SaleService.php            # Regras de negócio e orquestração de vendas
├── Repositories/
│   └── SaleRepository.php         # Manipulação do arquivo JSON de vendas
├── Rules/
│   └── CommissionRules.php        # Calcula comissões com base no JSON externo
routes/
└── api.php                        # Define todas as rotas da API

storage/app/sales.json             # Persistência dos dados de vendas
```

### Explicação de cada arquivo

#### `SaleController.php`

* Controlador principal da API.
* Gerencia criação (`store`), listagem (`index`), exibição (`show`), atualização (`update`) e exclusão (`destroy`) de vendas.

#### `HealthController.php`

* Controlador responsável pela rota `/api/health`.
* Verifica se o arquivo `sales.json` existe e tem permissões corretas.

#### `SaleService.php`

* Contém a lógica de negócio para vendas.
* Coordena chamadas para o repositório e para a lógica de comissão.
* Trata simulação, atualização e exclusão com consistência.

#### `SaleRepository.php`

* Camada de persistência.
* Lê e grava no arquivo `storage/app/sales.json`.
* Responsável por `create`, `find`, `update`, `delete`, `exists`.

#### `CommissionRules.php`

* Aplica as porcentagens fixa com base no `valor_total` e `tipo_venda`.
* Verifica se os percentuais estão corretamente definidos e lança exceções em caso de erro ou tipo inexistente.

---

## 📲 Endpoints RESTful

| Método | Rota            | Ação                      |
| ------ | --------------- | ------------------------- |
| GET    | /api/health     | Verificar status da API   |
| GET    | /api/sales      | Listar todas as vendas    |
| GET    | /api/sales/{id} | Ver detalhes de uma venda |
| POST   | /api/sales      | Criar nova simulação      |
| PUT    | /api/sales/{id} | Atualizar uma venda       |
| DELETE | /api/sales/{id} | Remover uma venda         |

---

## 🔎 Validações e Regras de negócio

### Campos obrigatórios:

* `valor_total`: numérico, mínimo de 0.01
* `tipo_venda`: "direta" ou "afiliada"

### Composição de Comissões:

| Tipo     | Plataforma | Produtor | Afiliado |
| -------- | ---------- | -------- | -------- |
| Direta   | 10%        | 90%      | -        |
| Afiliada | 10%        | 60%      | 30%      |


---

## ✅ Exemplo de requisição

### POST `/api/sales`

```json
{
  "valor_total": 1000,
  "tipo_venda": "afiliada"
}
```

### Callback

```json
{
  "id": "uuid",
  "valor_total": 1000,
  "tipo_venda": "afiliada",
  "comissoes": {
    "plataforma": 100,
    "produtor": 600,
    "afiliado": 300
  }
}
```

---

## ⚙️ Instalação do projeto

```bash
git clone https://github.com/wesleysnapss/laravel-comisso-api
cd laravel-comisso-api
composer install
cp .env.example .env
php artisan key:generate

# Criar arquivos e pastas necessárias
mkdir -p storage/app/config
mkdir -p storage/app

# Criar arquivos vazios
touch storage/app/sales.json


php artisan serve
```

---

## 🔐 Segurança

* Validação rigorosa com `Request::validate()`
* Verificação de existência de ID antes de update/delete
* Manipulação segura de arquivos com Laravel `Storage`
* Sem execução de dados arbitrários

---

## 🧪 Testes automatizados

Local: `tests/Feature/SaleTest.php`

```bash
php artisan test
```

Testes cobrem:

* Criação de venda válida
* Validação de erros
* Listagem de vendas
* Atualização de venda com recálculo
* Remoção de venda
* Health check da API

---

## 📌 Decisões técnicas

* Utilizado `apiResource` para manter RESTful padrão
* Persistência em arquivo foi mantida para simular banco
* Configuração de comissão externa via JSON
* Health check retorna status e permissões do JSON

---

## 👨‍💻 Autor

**Wesley Snap** - Tech Lead

---

> Este projeto foi desenvolvido como parte de um desafio técnico. A solução proposta preza por clareza, boas práticas.
