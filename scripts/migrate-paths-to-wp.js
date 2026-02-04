#!/usr/bin/env node

/**
 * Скрипт для миграции абсолютных путей на WordPress-совместимые
 * 
 * Использование:
 *   node scripts/migrate-paths-to-wp.js [--dry-run] [--css-only] [--html-only]
 * 
 * Опции:
 *   --dry-run    - только показать что будет изменено, не изменять файлы
 *   --css-only   - обработать только CSS файлы
 *   --html-only  - обработать только HTML файлы
 */

const fs = require('fs');
const path = require('path');

const DRY_RUN = process.argv.includes('--dry-run');
const CSS_ONLY = process.argv.includes('--css-only');
const HTML_ONLY = process.argv.includes('--html-only');

const ROOT = path.join(__dirname, '..');
const SRC_CSS = path.join(ROOT, 'src', 'css');
const SRC_JS = path.join(ROOT, 'src', 'js');

// Паттерны для замены
const REPLACEMENTS = {
  // HTML файлы: абсолютные пути в href/src
  html: [
    {
      pattern: /(href|src)=("|')(\/src\/(?:css|js)\/[^"'\\\s]+?)(\2)/g,
      replacement: (match, attr, quote, path, endQuote) => {
        return `${attr}=${quote}<?php echo get_template_directory_uri(); ?>${path}${endQuote}`;
      }
    },
    {
      pattern: /(href|src)=("|')(\/public\/[^"'\\\s]+?)(\2)/g,
      replacement: (match, attr, quote, path, endQuote) => {
        return `${attr}=${quote}<?php echo get_template_directory_uri(); ?>${path}${endQuote}`;
      }
    }
  ],
  
  // CSS файлы: абсолютные пути в url()
  css: [
    {
      pattern: /url\(("|')?\/public\/([^"')]+?)("|')?\)/g,
      replacement: 'url("../public/$2")'
    },
    {
      pattern: /url\(("|')?\/src\/([^"')]+?)("|')?\)/g,
      replacement: 'url("../src/$2")'
    }
  ],
  
  // JavaScript файлы: строковые пути
  js: [
    {
      pattern: /(['"`])(\/public\/[^'"`]+?)(\1)/g,
      replacement: (match, quote, path) => {
        // Если используется config.js, оставить как есть
        if (path.includes('OOR_PATHS') || path.includes('OOR_BASE_URL')) {
          return match;
        }
        return `${quote}<?php echo get_template_directory_uri(); ?>${path}${quote}`;
      }
    },
    {
      pattern: /(['"`])(\/src\/[^'"`]+?)(\1)/g,
      replacement: (match, quote, path) => {
        if (path.includes('OOR_PATHS') || path.includes('OOR_BASE_URL')) {
          return match;
        }
        return `${quote}<?php echo get_template_directory_uri(); ?>${path}${quote}`;
      }
    }
  ]
};

// Найти все файлы для обработки
function findFiles(dir, extensions, fileList = []) {
  const files = fs.readdirSync(dir);
  
  files.forEach(file => {
    const filePath = path.join(dir, file);
    const stat = fs.statSync(filePath);
    
    if (stat.isDirectory()) {
      // Пропускаем node_modules и другие служебные папки
      if (!['node_modules', '.git', 'vendor'].includes(file)) {
        findFiles(filePath, extensions, fileList);
      }
    } else if (extensions.some(ext => file.endsWith(ext))) {
      fileList.push(filePath);
    }
  });
  
  return fileList;
}

// Обработать файл
function processFile(filePath, type) {
  let content = fs.readFileSync(filePath, 'utf8');
  let modified = false;
  const changes = [];
  
  const replacements = REPLACEMENTS[type] || [];
  
  replacements.forEach(({ pattern, replacement }) => {
    const matches = content.match(pattern);
    if (matches) {
      const newContent = content.replace(pattern, replacement);
      if (newContent !== content) {
        content = newContent;
        modified = true;
        changes.push(`${matches.length} замен(ы) по паттерну: ${pattern}`);
      }
    }
  });
  
  if (modified && !DRY_RUN) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`✓ Обновлен: ${path.relative(ROOT, filePath)}`);
    changes.forEach(change => console.log(`  - ${change}`));
  } else if (modified && DRY_RUN) {
    console.log(`[DRY RUN] Будет обновлен: ${path.relative(ROOT, filePath)}`);
    changes.forEach(change => console.log(`  - ${change}`));
  }
  
  return modified;
}

// Главная функция
function main() {
  console.log('🚀 Миграция путей для WordPress\n');
  
  if (DRY_RUN) {
    console.log('⚠️  Режим DRY RUN - файлы не будут изменены\n');
  }
  
  let totalFiles = 0;
  let modifiedFiles = 0;
  
  // HTML файлы
  if (!CSS_ONLY) {
    console.log('📄 Обработка HTML файлов...');
    const htmlFiles = findFiles(ROOT, ['.html']);
    htmlFiles.forEach(file => {
      totalFiles++;
      if (processFile(file, 'html')) {
        modifiedFiles++;
      }
    });
    console.log(`   Обработано: ${htmlFiles.length} файлов\n`);
  }
  
  // CSS файлы
  if (!HTML_ONLY) {
    console.log('🎨 Обработка CSS файлов...');
    const cssFiles = findFiles(SRC_CSS, ['.css']);
    cssFiles.forEach(file => {
      totalFiles++;
      if (processFile(file, 'css')) {
        modifiedFiles++;
      }
    });
    console.log(`   Обработано: ${cssFiles.length} файлов\n`);
  }
  
  // JavaScript файлы (опционально, так как лучше использовать wp_localize_script)
  if (!HTML_ONLY && !CSS_ONLY) {
    console.log('⚠️  JavaScript файлы: рекомендуется использовать wp_localize_script()');
    console.log('   вместо автоматической замены путей в JS файлах\n');
  }
  
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log(`Всего файлов: ${totalFiles}`);
  console.log(`Изменено: ${modifiedFiles}`);
  
  if (DRY_RUN) {
    console.log('\n💡 Запустите без --dry-run для применения изменений');
  } else {
    console.log('\n✅ Миграция завершена!');
    console.log('\n⚠️  ВАЖНО:');
    console.log('   1. Проверьте все изменения вручную');
    console.log('   2. Для JavaScript используйте wp_localize_script()');
    console.log('   3. Протестируйте все страницы после миграции');
  }
}

main();
