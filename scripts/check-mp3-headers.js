#!/usr/bin/env node

/**
 * Скрипт для проверки HTTP заголовков MP3 файлов
 * Проверяет наличие Accept-Ranges: bytes и Content-Length
 */

const http = require('http');
const https = require('https');
const url = require('url');

const BASE_URL = process.argv[2] || 'http://localhost:8004';
const TEST_FILE = '/public/assets/artists/crylove/tracks/killer/audio.mp3';

function checkHeaders(fileUrl) {
  return new Promise((resolve, reject) => {
    const parsedUrl = url.parse(fileUrl);
    const client = parsedUrl.protocol === 'https:' ? https : http;
    
    const options = {
      hostname: parsedUrl.hostname,
      port: parsedUrl.port || (parsedUrl.protocol === 'https:' ? 443 : 80),
      path: parsedUrl.path,
      method: 'HEAD', // Используем HEAD для проверки заголовков без загрузки файла
      headers: {
        'Range': 'bytes=0-1023' // Запрашиваем первые 1024 байта для проверки range support
      }
    };
    
    const req = client.request(options, (res) => {
      const headers = res.headers;
      const statusCode = res.statusCode;
      
      resolve({
        statusCode,
        headers,
        hasAcceptRanges: headers['accept-ranges'] === 'bytes',
        hasContentLength: !!headers['content-length'],
        contentLength: headers['content-length'],
        supportsRange: statusCode === 206 || (statusCode === 200 && headers['accept-ranges'] === 'bytes')
      });
    });
    
    req.on('error', (error) => {
      reject(error);
    });
    
    req.setTimeout(5000, () => {
      req.destroy();
      reject(new Error('Request timeout'));
    });
    
    req.end();
  });
}

async function main() {
  console.log('🔍 Проверка HTTP заголовков MP3 файлов\n');
  console.log(`   Сервер: ${BASE_URL}`);
  console.log(`   Тестовый файл: ${TEST_FILE}\n`);
  
  const fileUrl = `${BASE_URL}${TEST_FILE}`;
  
  try {
    console.log('📡 Отправка HEAD запроса...');
    const result = await checkHeaders(fileUrl);
    
    console.log(`   Status Code: ${result.statusCode}`);
    console.log(`   Accept-Ranges: ${result.headers['accept-ranges'] || 'не установлен'}`);
    console.log(`   Content-Length: ${result.contentLength || 'не установлен'}`);
    console.log(`   Content-Type: ${result.headers['content-type'] || 'не установлен'}`);
    console.log('');
    
    if (result.hasAcceptRanges) {
      console.log('   ✅ Accept-Ranges: bytes - установлен');
    } else {
      console.log('   ❌ Accept-Ranges: bytes - НЕ установлен');
    }
    
    if (result.hasContentLength) {
      console.log(`   ✅ Content-Length: ${result.contentLength} - установлен`);
    } else {
      console.log('   ❌ Content-Length - НЕ установлен');
    }
    
    if (result.supportsRange) {
      console.log('   ✅ Поддержка Range requests - работает');
    } else {
      console.log('   ⚠️  Поддержка Range requests - может не работать');
    }
    
    console.log('');
    
    if (result.hasAcceptRanges && result.hasContentLength) {
      console.log('✅ Все необходимые заголовки установлены!');
      console.log('   Перемотка должна работать корректно.\n');
    } else {
      console.log('⚠️  Некоторые заголовки отсутствуют.');
      console.log('   Убедитесь, что веб-сервер правильно настроен для отдачи MP3 файлов.\n');
      console.log('   Для Netlify добавьте в netlify.toml:');
      console.log('   [[headers]]');
      console.log('     for = "/*.mp3"');
      console.log('     [headers.values]');
      console.log('       Accept-Ranges = "bytes"\n');
    }
    
  } catch (error) {
    console.error(`❌ Ошибка при проверке: ${error.message}`);
    console.error('');
    console.error('💡 Убедитесь, что веб-сервер запущен:');
    console.error(`   python3 -m http.server 8004`);
    console.error(`   или`);
    console.error(`   netlify dev\n`);
    process.exit(1);
  }
}

main();

