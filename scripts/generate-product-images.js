#!/usr/bin/env node
/**
 * Генерация всех форматов изображений продуктов из @2x версий
 * Создает: product-N.png, product-N.avif, product-N.webp
 *          product-N@2x.png, product-N@2x.avif, product-N@2x.webp
 * для N = 1, 2, 3, 4
 */

const fs = require('fs').promises;
const path = require('path');
const sharp = require('sharp');

const PROJECT_ROOT = path.join(__dirname, '..');
const SOURCE_DIR = path.join(PROJECT_ROOT, 'public', 'assets', 'products');
const ASSETS_DIR = path.join(PROJECT_ROOT, 'public', 'assets');

// Генерация всех форматов для одного продукта
async function generateProductFormats(productNumber) {
  const sourcePath = path.join(SOURCE_DIR, `product-${productNumber}@2x.png`);
  
  try {
    await fs.access(sourcePath);
  } catch {
    console.error(`❌ Файл не найден: ${sourcePath}`);
    return false;
  }

  console.log(`📸 Обработка product-${productNumber}@2x.png\n`);

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
      const outputPath = path.join(ASSETS_DIR, `product-${productNumber}${size.suffix}.${format.ext}`);
      
      // Пропускаем создание @2x.png, если исходный файл уже является @2x.png
      if (size.suffix === '@2x' && format.ext === 'png') {
        // Копируем исходный файл
        try {
          await fs.copyFile(sourcePath, outputPath);
          console.log(`  → Скопировано: product-${productNumber}${size.suffix}.${format.ext}`);
        } catch (error) {
          console.error(`  ✗ Ошибка копирования product-${productNumber}${size.suffix}.${format.ext}:`, error.message);
        }
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
        console.log(`  ✓ Создано: product-${productNumber}${size.suffix}.${format.ext}`);
      } catch (error) {
        console.error(`  ✗ Ошибка создания product-${productNumber}${size.suffix}.${format.ext}:`, error.message);
      }
    }
    console.log('');
  }

  return true;
}

// Главная функция
async function main() {
  console.log('🔄 Генерация форматов для продуктов...\n');

  for (let i = 1; i <= 4; i++) {
    await generateProductFormats(i);
  }

  console.log('✅ Генерация завершена!');
}

main().catch(error => {
  console.error('❌ Ошибка:', error);
  process.exit(1);
});
