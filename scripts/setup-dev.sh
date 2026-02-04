#!/bin/bash

# Скрипт для настройки окружения разработки

set -e

echo "🚀 Настройка окружения разработки OOR Webstudio"
echo ""

# Проверка Docker
if command -v docker &> /dev/null && command -v docker-compose &> /dev/null; then
    echo "✅ Docker найден"
    DOCKER_AVAILABLE=true
else
    echo "⚠️  Docker не найден"
    echo "   Установите Docker Desktop: https://www.docker.com/products/docker-desktop"
    DOCKER_AVAILABLE=false
fi

# Проверка Node.js
if command -v node &> /dev/null; then
    echo "✅ Node.js найден: $(node --version)"
    NODE_AVAILABLE=true
else
    echo "❌ Node.js не найден"
    echo "   Установите Node.js: https://nodejs.org/"
    NODE_AVAILABLE=false
fi

echo ""

# Создание директорий
echo "📁 Создание директорий..."
mkdir -p wp-content/themes/oor-theme
mkdir -p wordpress-uploads
echo "✅ Директории созданы"

echo ""

# Запуск Docker (если доступен)
if [ "$DOCKER_AVAILABLE" = true ]; then
    # Генерация SSL-сертификата для HTTPS (если ещё нет)
    if [ ! -f "ssl/cert.pem" ] || [ ! -f "ssl/key.pem" ]; then
        echo "🔐 Генерация SSL-сертификата для локального HTTPS..."
        bash scripts/generate-ssl-cert.sh
    fi
    echo "🐳 Запуск Docker контейнеров..."
    docker-compose up -d
    
    echo ""
    echo "⏳ Ожидание запуска контейнеров (10 секунд)..."
    sleep 10
    
    echo ""
    echo "📊 Статус контейнеров:"
    docker-compose ps
    
    echo ""
    echo "✅ Docker окружение запущено!"
    echo "   WordPress (HTTP):  http://localhost:8080"
    echo "   WordPress (HTTPS): https://localhost:8443"
    echo "   phpMyAdmin:        http://localhost:8081"
else
    echo "⚠️  Docker недоступен, пропускаем запуск контейнеров"
fi

echo ""

# Установка npm зависимостей (если нужно)
if [ "$NODE_AVAILABLE" = true ]; then
    if [ ! -d "node_modules" ]; then
        echo "📦 Установка npm зависимостей..."
        npm install
        echo "✅ Зависимости установлены"
    else
        echo "✅ npm зависимости уже установлены"
    fi
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Настройка завершена!"
echo ""
echo "Следующие шаги:"
echo ""

if [ "$DOCKER_AVAILABLE" = true ]; then
    echo "1. Откройте http://localhost:8080 для настройки WordPress"
    echo "2. Используйте данные БД из docker-compose.yml"
    echo "3. Скопируйте файлы темы в wp-content/themes/oor-theme/"
else
    echo "1. Установите Docker Desktop"
    echo "2. Запустите этот скрипт снова: ./scripts/setup-dev.sh"
    echo ""
    echo "Или используйте альтернативу:"
    echo "   - Local by Flywheel: https://localwp.com/"
    echo "   - MAMP: https://www.mamp.info/"
    echo "   - XAMPP: https://www.apachefriends.org/"
fi

echo ""
echo "Для статической версии запустите:"
echo "   python3 -m http.server 8040"
echo "   или: npx serve ."
echo ""
