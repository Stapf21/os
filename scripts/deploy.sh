#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

LOCK_FILE="${DEPLOY_LOCK_FILE:-application/cache/deploy.lock}"
LOG_FILE="${DEPLOY_LOG_FILE:-application/logs/deploy.log}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
RUN_FRONTEND_BUILD="${RUN_FRONTEND_BUILD:-0}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-1}"

if [ -n "$(git status --porcelain)" ]; then
    echo "ERRO: Repositorio com alteracoes locais. Abortando."
    exit 1
fi

mkdir -p "$(dirname "$LOCK_FILE")"
mkdir -p "$(dirname "$LOG_FILE")"
touch "$LOG_FILE"

if [ -f "$LOCK_FILE" ]; then
    OLD_PID="$(cat "$LOCK_FILE" 2>/dev/null || true)"
    if [ -n "$OLD_PID" ] && kill -0 "$OLD_PID" 2>/dev/null; then
        echo "Deploy ja em execucao (PID $OLD_PID)."
        exit 0
    fi
    rm -f "$LOCK_FILE"
fi

echo "$$" > "$LOCK_FILE"
trap 'rm -f "$LOCK_FILE"' EXIT
exec >>"$LOG_FILE" 2>&1

echo "================================================================"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Inicio do deploy"
echo "Branch: $DEPLOY_BRANCH"

git fetch --prune origin
git checkout "$DEPLOY_BRANCH"
git pull --ff-only origin "$DEPLOY_BRANCH"

"$COMPOSER_BIN" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

if [ "$RUN_MIGRATIONS" = "1" ]; then
    "$PHP_BIN" index.php tools migrate
fi

if [ "$RUN_FRONTEND_BUILD" = "1" ] && [ -f "package.json" ]; then
    if command -v "$NPM_BIN" >/dev/null 2>&1; then
        "$NPM_BIN" ci
        "$NPM_BIN" run build
    else
        echo "Aviso: npm nao encontrado, build de frontend ignorado."
    fi
fi

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deploy finalizado com sucesso"
echo "================================================================"
