#!/bin/bash
# Генерация самоподписанного SSL-сертификата для локальной разработки

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SSL_DIR="$(dirname "$SCRIPT_DIR")/ssl"
mkdir -p "$SSL_DIR"
cd "$SSL_DIR"

if [ -f "cert.pem" ] && [ -f "key.pem" ]; then
    echo "Сертификат уже есть в $SSL_DIR"
    exit 0
fi

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout key.pem -out cert.pem \
    -config openssl.cnf -extensions v3_req

echo "Сертификат создан: $SSL_DIR/cert.pem, $SSL_DIR/key.pem"
echo "Доступ по HTTPS: https://localhost:8443"
