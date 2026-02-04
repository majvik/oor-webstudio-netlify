const fs = require('fs').promises;
const path = require('path');
const sharp = require('sharp');

// Конфигурация
const PROJECT_ROOT = path.join(__dirname, '..');
const ASSETS_DIR = path.join(PROJECT_ROOT, 'public', 'assets');
const ARTISTS_DIR = path.join(ASSETS_DIR, 'artists');

// Размеры для слайдера на главной странице
// slide-media: width: min(380px, 100%), aspect-ratio: 3 / 4
const SLIDER_WIDTH = 380;
const SLIDER_HEIGHT = Math.round(SLIDER_WIDTH * 4 / 3); // 507px для 3:4
const SLIDER_WIDTH_2X = SLIDER_WIDTH * 2; // 760px
const SLIDER_HEIGHT_2X = SLIDER_HEIGHT * 2; // 1014px

// Список артистов для главной страницы (в порядке отображения)
const ARTISTS = [
  { slug: 'crylove', name: 'CRYLOVE' },
  { slug: 'dimma-urih', name: 'Dimma Urih' },
  { slug: 'dsprite', name: 'DSPRITE' },
  { slug: 'nxn', name: 'NXN' },
  { slug: 'net-vremeni-ob-yasnyat', name: 'Нет Времени Объяснять' }
];

// Подготовка изображения артиста для слайдера
async function prepareSliderImage(artistSlug, artistName, index) {
  console.log(`\n📸 Подготовка изображения для ${artistName} (${artistSlug})`);
  
  // Ищем исходное изображение (используем PNG как исходник)
  const sourceImage = path.join(ARTISTS_DIR, artistSlug, 'main.png');
  
  try {
    await fs.access(sourceImage);
  } catch {
    console.error(`  ❌ Исходное изображение не найдено: ${sourceImage}`);
    return null;
  }
  
  const formats = [
    { ext: 'avif', mime: 'image/avif' },
    { ext: 'webp', mime: 'image/webp' },
    { ext: 'jpg', mime: 'image/jpeg' }
  ];
  
  const sizes = [
    { suffix: '', width: SLIDER_WIDTH, height: SLIDER_HEIGHT },
    { suffix: '@2x', width: SLIDER_WIDTH_2X, height: SLIDER_HEIGHT_2X }
  ];
  
  const results = {};
  
  for (const size of sizes) {
    for (const format of formats) {
      const outputPath = path.join(ASSETS_DIR, `img${index + 1}${size.suffix}.${format.ext}`);
      
      try {
        await sharp(sourceImage)
          .resize(size.width, size.height, {
            fit: 'cover',
            position: 'top' // Кроп по верхнему краю и центру
          })
          .toFormat(format.ext === 'avif' ? 'avif' : format.ext === 'webp' ? 'webp' : 'jpeg', {
            quality: format.ext === 'jpg' ? 90 : undefined
          })
          .toFile(outputPath);
        
        console.log(`  ✓ Создано: img${index + 1}${size.suffix}.${format.ext}`);
        
        if (!results[format.ext]) {
          results[format.ext] = {};
        }
        results[format.ext][size.suffix ? '2x' : '1x'] = `/public/assets/img${index + 1}${size.suffix}.${format.ext}`;
      } catch (error) {
        console.error(`  ✗ Ошибка создания img${index + 1}${size.suffix}.${format.ext}:`, error.message);
      }
    }
  }
  
  return {
    name: artistName,
    slug: artistSlug,
    images: results
  };
}

// Главная функция
async function main() {
  console.log('🚀 Подготовка изображений артистов для главной страницы...\n');
  console.log(`Размеры: ${SLIDER_WIDTH}x${SLIDER_HEIGHT} (1x), ${SLIDER_WIDTH_2X}x${SLIDER_HEIGHT_2X} (2x)`);
  console.log(`Кроп: top center (3:4 aspect ratio)\n`);
  
  const results = [];
  
  for (let i = 0; i < ARTISTS.length; i++) {
    const artist = ARTISTS[i];
    const result = await prepareSliderImage(artist.slug, artist.name, i);
    if (result) {
      results.push(result);
    }
  }
  
  console.log(`\n✅ Подготовка завершена! Обработано артистов: ${results.length}`);
  console.log('\n📋 Данные для обновления index.html:');
  console.log(JSON.stringify(results, null, 2));
  
  return results;
}

// Запуск
if (require.main === module) {
  main().catch(console.error);
}

module.exports = { main, ARTISTS };

