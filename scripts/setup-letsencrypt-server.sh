#!/bin/bash
# Настройка Let's Encrypt на сервере.
# Запуск на сервере из корня проекта: sudo bash scripts/setup-letsencrypt-server.sh
# Требуется: порты 80 и 443 открыты, домен 45.141.102.187.nip.io указывает на этот сервер.

set -e
DOMAIN="${1:-45.141.102.187.nip.io}"
EMAIL="${2:-vik.mayorov@gmail.com}"
PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$PROJECT_ROOT"

echo "Domain: $DOMAIN"
echo "Email: $EMAIL"
echo ""

# 1. Установка certbot
if ! command -v certbot &>/dev/null; then
    echo "Installing certbot..."
    apt-get update -qq
    apt-get install -y certbot
fi

# 2. Каталог для ACME challenge
mkdir -p certbot-webroot
chmod 755 certbot-webroot

# 3. Перезапуск nginx с поддержкой certbot webroot (если ещё не перезапущен)
docker compose restart nginx 2>/dev/null || true
sleep 2

# 4. Получение сертификата
echo "Requesting certificate from Let's Encrypt..."
certbot certonly --webroot \
  -w "$PROJECT_ROOT/certbot-webroot" \
  -d "$DOMAIN" \
  --email "$EMAIL" \
  --agree-tos \
  --non-interactive \
  --force-renewal

echo ""
echo "Certificate obtained. Configuring nginx to use it..."

# 5. Переключаем nginx на конфиг с Let's Encrypt и монтируем сертификаты
# Создаём override для docker-compose: другой volume для ssl и другой конфиг nginx
NGINX_CONF="$PROJECT_ROOT/nginx-letsencrypt.conf"
SSL_DIR="/etc/letsencrypt/live/$DOMAIN"
if [ ! -d "$SSL_DIR" ]; then
    echo "Error: $SSL_DIR not found."
    exit 1
fi

# Заменяем конфиг и перезапускаем с монтированием Let's Encrypt
cp "$NGINX_CONF" /tmp/nginx-letsencrypt.conf
docker compose stop nginx 2>/dev/null || true
docker run --rm -v "$PROJECT_ROOT/nginx-letsencrypt.conf:/etc/nginx/conf.d/default.conf:ro" \
  -v "oor-webstudio-netlify_wordpress-data:/var/www/html:ro" \
  -v "$PROJECT_ROOT/wp-content:/var/www/html/wp-content:ro" \
  -v "$PROJECT_ROOT/wordpress-uploads:/var/www/html/wp-content/uploads:ro" \
  -v "$SSL_DIR:/etc/nginx/ssl:ro" \
  -v "$PROJECT_ROOT/certbot-webroot:/var/www/certbot:ro" \
  -p 80:80 -p 443:443 \
  --network oor-webstudio-netlify_oor-network \
  nginx:alpine nginx -t 2>/dev/null || true

# docker-compose.override.yml: подмена конфига nginx и монтирование сертификатов Let's Encrypt
OVERRIDE="$PROJECT_ROOT/docker-compose.override.yml"
cat > "$OVERRIDE" << EOF
# Let's Encrypt (сгенерировано setup-letsencrypt-server.sh)
services:
  nginx:
    volumes:
      - wordpress-data:/var/www/html:ro
      - ./wp-content:/var/www/html/wp-content:ro
      - ./wordpress-uploads:/var/www/html/wp-content/uploads:ro
      - ./nginx-letsencrypt.conf:/etc/nginx/conf.d/default.conf:ro
      - /etc/letsencrypt:/etc/letsencrypt:ro
      - ./certbot-webroot:/var/www/certbot:ro
EOF

docker compose up -d nginx
echo ""
echo "Done. HTTPS: https://$DOMAIN"
echo "Renewal: certbot renew (добавьте в cron: 0 3 * * * certbot renew --quiet)"
