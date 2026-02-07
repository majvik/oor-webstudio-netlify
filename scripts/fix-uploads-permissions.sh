#!/bin/bash
# Восстанавливает права на wp-content и wordpress-uploads для работы WordPress в Docker.
# Запуск на сервере из корня проекта: bash scripts/fix-uploads-permissions.sh
# Или по SSH: ssh root@SERVER "cd /opt/oor-webstudio && bash scripts/fix-uploads-permissions.sh"

set -e
cd "$(dirname "$0")/.."

echo "Fixing permissions for www-data (33:33)..."
mkdir -p wordpress-uploads/2026/02
mkdir -p wp-content/uploads/2026/02
chown -R 33:33 wp-content wordpress-uploads
chmod -R 775 wp-content wordpress-uploads
echo "Done. wp-content and wordpress-uploads are writable by the WordPress container."
