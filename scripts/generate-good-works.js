#!/usr/bin/env node
/**
 * Генерация всех форматов изображения good-works из @2x версии
 * Создает: good-works.png, good-works.avif, good-works.webp
 *          good-works@2x.png, good-works@2x.avif, good-works@2x.webp
 */

const fs = require('fs').promises;
const path = require('path');
const sharp = require('sharp');

const PROJECT_ROOT = path.join(__dirname, '..');
const ASSETS_DIR = path.join(PROJECT_ROOT, 'public', 'assets');

// Ищем исходный файл good-works@2x.png
async function findSourceImage() {
  // Проверяем аргумент командной строки
  if (process.argv[2]) {
    const customPath = path.resolve(process.argv[2]);
    try {
      await fs.access(customPath);
      return customPath;
    } catch {
      console.warn(`⚠️  Указанный файл не найден: ${customPath}`);
    }
  }

  const possiblePaths = [
    path.join(PROJECT_ROOT, 'good-works@2x.png'),
    path.join(ASSETS_DIR, 'good-works@2x.png'),
    path.join(PROJECT_ROOT, 'public', 'good-works@2x.png'),
    path.join(PROJECT_ROOT, 'good-works@2x.PNG'),
    path.join(ASSETS_DIR, 'good-works@2x.PNG'),
  ];

  for (const filePath of possiblePaths) {
    try {
      await fs.access(filePath);
      return filePath;
    } catch {
      continue;
    }
  }

  return null;
}

// Генерация всех форматов
async function generateFormats(sourcePath) {
  console.log(`📸 Обработка изображения: ${sourcePath}\n`);

  const formats = [
    { ext: 'png', mime: 'image/png' },
    { ext: 'avif', mime: 'image/avif' },
    { ext: 'webp', mime: 'image/webp' }
  ];

  const sizes = [
    { suffix: '@2x', scale: 1 }, // Оригинальный размер
    { suffix: '', scale: 0.5 }    // Уменьшенный в 2 раза
  ];

  const image = sharp(sourcePath);
  const metadata = await image.metadata();

  console.log(`Исходное изображение: ${metadata.width}x${metadata.height}px\n`);

  for (const size of sizes) {
    const targetWidth = Math.round(metadata.width * size.scale);
    const targetHeight = Math.round(metadata.height * size.scale);

    console.log(`Генерация версии ${size.suffix || '1x'}: ${targetWidth}x${targetHeight}px`);

    for (const format of formats) {
      const outputPath = path.join(ASSETS_DIR, `good-works${size.suffix}.${format.ext}`);
      
      // Пропускаем создание @2x.png, если исходный файл уже является @2x.png
      if (size.suffix === '@2x' && format.ext === 'png' && sourcePath === outputPath) {
        console.log(`  → Пропущено: good-works${size.suffix}.${format.ext} (исходный файл)`);
        continue;
      }
      
      try {
        let processor = image.clone().resize(targetWidth, targetHeight, {
          fit: 'contain',
          background: { r: 255, g: 255, b: 255, alpha: 0 }
        });

        if (format.ext === 'avif') {
          processor = processor.toFormat('avif');
        } else if (format.ext === 'webp') {
          processor = processor.toFormat('webp');
        } else {
          processor = processor.toFormat('png');
        }

        await processor.toFile(outputPath);
        console.log(`  ✓ Создано: good-works${size.suffix}.${format.ext}`);
      } catch (error) {
        console.error(`  ✗ Ошибка создания good-works${size.suffix}.${format.ext}:`, error.message);
      }
    }
    console.log('');
  }

  console.log('✅ Генерация завершена!');
}

// Главная функция
async function main() {
  const sourcePath = await findSourceImage();

  if (!sourcePath) {
    console.error('❌ Файл good-works@2x.png не найден!');
    console.error('\nИщите в следующих местах:');
    console.error('  - Корень проекта: ./good-works@2x.png');
    console.error('  - Папка assets: ./public/assets/good-works@2x.png');
    console.error('  - Папка public: ./public/good-works@2x.png');
    console.error('\nИли укажите путь к файлу:');
    console.error('  node scripts/generate-good-works.js /path/to/good-works@2x.png');
    process.exit(1);
  }

  await generateFormats(sourcePath);
}

main().catch(error => {
  console.error('❌ Ошибка:', error);
  process.exit(1);
});
