#!/bin/bash
# Генерация самоподписанного SSL для локального HTTPS (certs/).
# Запуск: из корня проекта — bash scripts/generate-local-certs.sh

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CERTS_DIR="$(dirname "$SCRIPT_DIR")/certs"
mkdir -p "$CERTS_DIR"
cd "$CERTS_DIR"

if [ -f "local.crt" ] && [ -f "local.key" ]; then
  echo "Сертификаты уже есть в $CERTS_DIR"
  exit 0
fi

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout local.key -out local.crt \
  -subj "/CN=localhost"

echo "Сертификаты созданы: $CERTS_DIR/local.crt, $CERTS_DIR/local.key"
echo "Локальный HTTPS: https://localhost:8443"
