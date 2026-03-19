# Sistema de Atualizacao - Hostinger (CodeIgniter)

Este documento e o padrao deste projeto para deploy em hospedagem compartilhada da Hostinger.
Nao depende de VPS dedicada.

## 1. Objetivo

Implantar e operar:

- script idempotente `scripts/deploy.sh`
- painel web em `/configuracoes/atualizacoes`
- deploy automatico por GitHub Actions via SSH

## 2. Estrutura ja usada neste projeto

- Config: `application/config/deploy.php`
- Script: `scripts/deploy.sh`
- Rotas:
  - `GET /configuracoes/atualizacoes`
  - `POST /configuracoes/atualizacoes/executar`
- Controller: `application/controllers/Mapos.php`
  - `atualizacoes()`
  - `executarAtualizacao()`
- View: `application/views/mapos/atualizacoes.php`
- Workflow: `.github/workflows/deploy.yml`

## 3. Variaveis no `application/.env`

Use estas chaves no servidor:

```env
ALLOW_WEB_DEPLOY=true
DEPLOY_BRANCH=main
DEPLOY_SCRIPT_PATH=scripts/deploy.sh
DEPLOY_LOG_FILE=application/logs/deploy.log
DEPLOY_LOCK_FILE=application/cache/deploy.lock
DEPLOY_LOG_TAIL_LINES=120
```

Referencia de exemplo: `application/.env.example`.

## 4. Preparacao da Hostinger (SSH)

No terminal SSH da hospedagem:

```bash
cd /home/SEU_USUARIO/domains/SEU_DOMINIO/public_html/os
mkdir -p application/logs application/cache scripts
chmod -R 775 application/logs application/cache
```

Validar binarios:

```bash
php -v
composer -V
git --version
```

Se `npm` nao existir, manter `RUN_FRONTEND_BUILD=0` no deploy (padrao deste projeto).

## 5. Script de deploy (comportamento esperado)

`scripts/deploy.sh` deve:

- aplicar lock (`application/cache/deploy.lock`)
- escrever log (`application/logs/deploy.log`)
- abortar se `git status` estiver sujo
- executar:
  - `git fetch --prune origin`
  - `git checkout <branch>`
  - `git pull --ff-only origin <branch>`
  - `composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction`
  - `php index.php tools migrate` (se `RUN_MIGRATIONS=1`)
- build frontend somente se habilitado e com `npm` disponivel

## 6. Workflow GitHub Actions

Arquivo: `.github/workflows/deploy.yml`.

Secrets recomendados (Hostinger):

- `HOSTINGER_HOST`
- `HOSTINGER_USER`
- `HOSTINGER_PORT`
- `HOSTINGER_APP_PATH`
- `HOSTINGER_SSH_KEY`

Compatibilidade:

- o workflow aceita tambem `VPS_*` como fallback.

## 7. Chave SSH para Actions

1. Gerar chave sem passphrase.
2. Adicionar chave publica no `~/.ssh/authorized_keys` do usuario da Hostinger.
3. Salvar chave privada em `HOSTINGER_SSH_KEY`.

## 8. Operacao via painel

URL: `/configuracoes/atualizacoes`

A tela mostra:

- branch
- commit atual/remoto
- status do lock
- ultimas linhas do log

Botao `Executar atualizacao`:

- exige permissao `cSistema`
- respeita `ALLOW_WEB_DEPLOY`
- executa em background com `nohup`

## 9. Checklist de validacao

Antes:

- `php -l application/controllers/Mapos.php`
- `php -l application/config/deploy.php`

Depois do deploy:

- GitHub Actions verde
- `tail -n 100 application/logs/deploy.log`
- abrir `/configuracoes/atualizacoes`
- acionar `Executar atualizacao`

## 10. Problemas comuns

- Chave SSH errada ou com passphrase no secret
- Usuario SSH diferente do dono do `authorized_keys`
- Repositorio remoto com alteracao local (`git status` sujo)
- Permissao insuficiente em `application/logs` e `application/cache`
- `scripts/deploy.sh` sem permissao de execucao
- `composer` indisponivel no PATH do usuario
