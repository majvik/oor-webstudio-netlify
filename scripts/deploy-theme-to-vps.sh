#!/bin/bash
# Синхронизация темы oor-theme на VPS.
# При каждом деплое в footer подставляется комментарий с временем и версией (см. SAFETY-SYNC.md).
# Использование:
#   DEPLOY_TARGET="root@45.141.102.187" DEPLOY_PASSWORD="yourpass" bash scripts/deploy-theme-to-vps.sh
# Или без пароля (по ключу): DEPLOY_TARGET="root@45.141.102.187" bash scripts/deploy-theme-to-vps.sh
set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

DEPLOY_TARGET="${DEPLOY_TARGET:-root@45.141.102.187}"
REMOTE_PATH="${REMOTE_PATH:-/opt/oor-webstudio}"
THEME_DIR="wp-content/themes/oor-theme"
FOOTER_FILE="$PROJECT_ROOT/$THEME_DIR/footer.php"

# Время деплоя (ISO 8601) и версия (короткий хеш коммита)
DEPLOY_TIMESTAMP="${DEPLOY_TIMESTAMP:-$(date -Iseconds 2>/dev/null || date +%Y-%m-%dT%H:%M:%S%z 2>/dev/null)}"
DEPLOY_VERSION="${DEPLOY_VERSION:-$(git rev-parse --short HEAD 2>/dev/null || echo 'n/a')}"
echo "→ Деплой: $DEPLOY_TIMESTAMP | версия: $DEPLOY_VERSION"

# Подставить в footer комментарий с временем и версией (на сервере будет актуальное значение)
if [ -f "$FOOTER_FILE" ]; then
  (sed -i.bak "s|{{DEPLOY_TIMESTAMP}}|$DEPLOY_TIMESTAMP|g; s|{{DEPLOY_VERSION}}|$DEPLOY_VERSION|g" "$FOOTER_FILE" 2>/dev/null && rm -f "${FOOTER_FILE}.bak") || \
  sed -i '' "s|{{DEPLOY_TIMESTAMP}}|$DEPLOY_TIMESTAMP|g; s|{{DEPLOY_VERSION}}|$DEPLOY_VERSION|g" "$FOOTER_FILE" 2>/dev/null || true
fi

if [ -n "${DEPLOY_PASSWORD:-}" ]; then
  echo "→ Деплой темы на $DEPLOY_TARGET (с паролем)..."
  export DEPLOY_PASSWORD
  expect -f - "$DEPLOY_TARGET" "$REMOTE_PATH" "$THEME_DIR" "$PROJECT_ROOT" <<'EXPECT_SCRIPT'
set timeout 120
set target [lindex $argv 0]
set remote_path [lindex $argv 1]
set theme_dir [lindex $argv 2]
set project_root [lindex $argv 3]
spawn rsync -avz -e "ssh -o StrictHostKeyChecking=accept-new" --delete "$project_root/$theme_dir/" "$target:$remote_path/$theme_dir/"
expect {
  -re "password:" { send "$env(DEPLOY_PASSWORD)\r"; exp_continue }
  -re "yes/no" { send "yes\r"; exp_continue }
  eof { catch wait result; exit [lindex $result 3] }
  timeout { puts "Timeout"; exit 1 }
}
EXPECT_SCRIPT
else
  echo "→ Деплой темы на $DEPLOY_TARGET (по SSH-ключу)..."
  rsync -avz -e "ssh -o StrictHostKeyChecking=accept-new" --delete \
    "$PROJECT_ROOT/$THEME_DIR/" \
    "$DEPLOY_TARGET:$REMOTE_PATH/$THEME_DIR/"
fi

# Вернуть в репозитории плейсхолдер (в репо хранятся {{...}}, на сервере — фактические значение)
git checkout -- "$THEME_DIR/footer.php" 2>/dev/null || true
echo "✅ Готово. В HTML футера на проде: <!-- deploy: $DEPLOY_TIMESTAMP $DEPLOY_VERSION -->"
