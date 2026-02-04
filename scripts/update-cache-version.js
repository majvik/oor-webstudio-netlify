#!/usr/bin/env node
/**
 * Simple cache-busting helper.
 * Adds/updates ?v=<timestamp> query params for /src/css and /src/js assets
 * inside static HTML files so browsers fetch fresh bundles without manually
 * clearing caches.
 * 
 * Automatically finds all HTML files in the project that contain /src/css or /src/js references.
 */

const { readFileSync, writeFileSync, readdirSync, statSync } = require('fs');
const { resolve, join, extname } = require('path');

const ROOT = resolve(__dirname, '..');
const VERSION = new Date().toISOString().replace(/[-:.TZ]/g, '').slice(0, 14);

// Находит /src/css или /src/js пути с опциональным ?v= параметром
// Поддерживает одинарные и двойные кавычки, а также отсутствие кавычек
const ASSET_REGEX =
  /(href|src)=("|\')(\/src\/(?:css|js)\/[^"\']+?)(?:\?v=[^"\']*)?(\2)/g;

/**
 * Рекурсивно находит все HTML файлы в директории
 */
function findHtmlFiles(dir, fileList = []) {
  const files = readdirSync(dir);
  
  files.forEach(file => {
    const filePath = join(dir, file);
    const stat = statSync(filePath);
    
    // Пропускаем node_modules, .git и другие служебные папки
    if (stat.isDirectory()) {
      if (!file.startsWith('.') && file !== 'node_modules' && file !== 'public' && file !== 'src' && file !== 'scripts') {
        findHtmlFiles(filePath, fileList);
      }
    } else if (stat.isFile() && extname(file) === '.html') {
      fileList.push(filePath);
    }
  });
  
  return fileList;
}

/**
 * Проверяет, содержит ли файл ссылки на /src/css или /src/js
 */
function hasAssetReferences(filePath) {
  try {
    const content = readFileSync(filePath, 'utf8');
    return /\/src\/(?:css|js)\//.test(content);
  } catch (error) {
    console.warn(`Warning: Could not read ${filePath}:`, error.message);
    return false;
  }
}

/**
 * Обновляет версии кэша в файле
 */
function updateFile(filePath) {
  try {
    let content = readFileSync(filePath, 'utf8');
    const originalContent = content;
    let updatedCount = 0;
    
    // Заменяем все совпадения
    content = content.replace(ASSET_REGEX, (match, attr, quote, path) => {
      updatedCount++;
      return `${attr}=${quote}${path}?v=${VERSION}${quote}`;
    });

    if (content !== originalContent && updatedCount > 0) {
      writeFileSync(filePath, content, 'utf8');
      console.log(`✓ Updated ${updatedCount} asset(s) in ${filePath.replace(ROOT, '.')}`);
      return true;
    } else if (updatedCount === 0) {
      console.log(`⚠ No matching assets found in ${filePath.replace(ROOT, '.')}`);
      return false;
    } else {
      console.log(`→ Cache version already up to date in ${filePath.replace(ROOT, '.')}`);
      return false;
    }
  } catch (error) {
    console.error(`✗ Error updating ${filePath}:`, error.message);
    return false;
  }
}

// Находим все HTML файлы
console.log('🔍 Searching for HTML files...');
const allHtmlFiles = findHtmlFiles(ROOT);

// Фильтруем только те, что содержат ссылки на ассеты
const targetFiles = allHtmlFiles.filter(hasAssetReferences);

if (targetFiles.length === 0) {
  console.log('⚠ No HTML files with /src/css or /src/js references found.');
  process.exit(0);
}

console.log(`📄 Found ${targetFiles.length} HTML file(s) with asset references\n`);
console.log(`🔄 Applying cache version ${VERSION}\n`);

// Обновляем все найденные файлы
let updatedCount = 0;
targetFiles.forEach(filePath => {
  if (updateFile(filePath)) {
    updatedCount++;
  }
});

console.log(`\n✅ Done! Updated ${updatedCount} of ${targetFiles.length} file(s).`);


