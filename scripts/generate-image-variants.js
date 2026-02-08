#!/usr/bin/env node
/**
 * Генерация 1x из @2x.avif и всех форматов (avif, webp, png).
 * Приоритет: @2x.avif — источник истины. Качество 100%. Перезапись существующих.
 * Если @2x нет — создаём @2x из 1x (масштаб 200%).
 *
 * Область: wp-content/themes/oor-theme/public/assets/ (корень и artists/**)
 */

const fs = require('fs').promises;
const path = require('path');
const sharp = require('sharp');

const PROJECT_ROOT = path.join(__dirname, '..');
const ASSETS_ROOT = path.join(PROJECT_ROOT, 'wp-content', 'themes', 'oor-theme', 'public', 'assets');

const QUALITY = 100;
const AVIF_QUALITY = 100;
const WEBP_QUALITY = 100;

/** Собрать все файлы *@2x.avif рекурсивно */
async function find2xAvifFiles(dir, list = []) {
  const entries = await fs.readdir(dir, { withFileTypes: true }).catch(() => []);
  for (const e of entries) {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) {
      await find2xAvifFiles(full, list);
    } else if (e.isFile() && e.name.endsWith('@2x.avif')) {
      list.push(full);
    }
  }
  return list;
}

/** Для базового имени в dir: есть ли 1x без 2x (только 1x) */
async function find1xOnlyBases(dir, list = []) {
  const seen = new Set();
  const entries = await fs.readdir(dir, { withFileTypes: true }).catch(() => []);
  for (const e of entries) {
    if (!e.isFile()) continue;
    const name = e.name;
    const ext = path.extname(name).toLowerCase();
    if (!['.avif', '.webp', '.png', '.jpg', '.jpeg'].includes(ext)) continue;
    const base = name.replace(/@2x\.(avif|webp|png|jpg|jpeg)$/i, '').replace(/\.(avif|webp|png|jpg|jpeg)$/i, '');
    if (name.includes('@2x')) continue;
    const key = path.join(dir, base);
    if (seen.has(key)) continue;
    seen.add(key);
    const basePath = path.join(dir, base);
    const has2xAvif = await fs.access(basePath + '@2x.avif').then(() => true).catch(() => false);
    if (!has2xAvif) list.push({ dir, base });
  }
  return list;
}

/** Рекурсивно собрать пары (dir, base) для 1x-only во всех подпапках (без дублей по base) */
async function findAll1xOnly(startDir) {
  const seen = new Set();
  const list = [];
  async function walk(d) {
    const entries = await fs.readdir(d, { withFileTypes: true }).catch(() => []);
    const files = entries.filter(e => e.isFile());
    const dirs = entries.filter(e => e.isDirectory());
    for (const f of files) {
      const name = f.name;
      const ext = path.extname(name).toLowerCase();
      if (!['.avif', '.webp', '.png', '.jpg', '.jpeg'].includes(ext)) continue;
      if (name.includes('@2x')) continue;
      const base = path.basename(name, ext);
      const key = path.join(d, base);
      if (seen.has(key)) continue;
      const has2x = await fs.access(path.join(d, base + '@2x.avif')).then(() => true).catch(() => false);
      if (!has2x) {
        seen.add(key);
        list.push({ dir: d, base });
      }
    }
    for (const sub of dirs) await walk(path.join(d, sub.name));
  }
  await walk(startDir);
  return list;
}

/** Рекурсивно собрать пути к @2x.png и @2x.webp; для каждой базы оставить один источник: .png приоритетнее .webp */
async function find2xRasterSources(dir, map = new Map()) {
  const entries = await fs.readdir(dir, { withFileTypes: true }).catch(() => []);
  for (const e of entries) {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) {
      await find2xRasterSources(full, map);
    } else if (e.isFile()) {
      const m = e.name.match(/^(.+)@2x\.(png|webp)$/i);
      if (!m) continue;
      const baseKey = path.join(dir, m[1]);
      const ext = (m[2] || '').toLowerCase();
      const current = map.get(baseKey);
      if (!current || ext === 'png') map.set(baseKey, full);
    }
  }
  return map;
}

/** Из @2x.png или @2x.webp создать/перезаписать @2x.avif */
async function ensure2xAvifFromRaster(path2xRaster) {
  const avifPath = path2xRaster.replace(/@2x\.(png|webp)$/i, '@2x.avif');
  await sharp(path2xRaster)
    .avif({ quality: AVIF_QUALITY })
    .toFile(avifPath);
  console.log('  ✓', path.basename(avifPath), '(из', path.basename(path2xRaster) + ')');
}

/** Из @2x.avif сгенерировать 1x (avif, webp, png) и @2x.webp, @2x.png */
async function generateFrom2xAvif(path2xAvif) {
  const dir = path.dirname(path2xAvif);
  const filename = path.basename(path2xAvif);
  const baseName = filename.replace('@2x.avif', '');
  const basePath = path.join(dir, baseName);

  const img = sharp(path2xAvif);
  const meta = await img.metadata();
  const w = meta.width || 0;
  const h = meta.height || 0;
  if (!w || !h) {
    console.warn('  ⚠ Пропуск (нет размеров):', path2xAvif);
    return;
  }
  const w1 = Math.round(w / 2);
  const h1 = Math.round(h / 2);

  const pipeline2x = img.clone();
  const pipeline1x = img.clone().resize(w1, h1, { fit: 'fill' });

  // 1x
  await pipeline1x.clone().avif({ quality: AVIF_QUALITY }).toFile(basePath + '.avif');
  console.log('  ✓', baseName + '.avif');
  await pipeline1x.clone().webp({ quality: WEBP_QUALITY }).toFile(basePath + '.webp');
  console.log('  ✓', baseName + '.webp');
  await pipeline1x.clone().png({ compressionLevel: 9 }).toFile(basePath + '.png');
  console.log('  ✓', baseName + '.png');

  // @2x webp и png (из @2x.avif того же размера)
  await pipeline2x.clone().webp({ quality: WEBP_QUALITY }).toFile(path.join(dir, baseName + '@2x.webp'));
  console.log('  ✓', baseName + '@2x.webp');
  await pipeline2x.clone().png({ compressionLevel: 9 }).toFile(path.join(dir, baseName + '@2x.png'));
  console.log('  ✓', baseName + '@2x.png');
}

/** Выбрать лучший 1x файл для базы (avif > webp > png > jpg) */
async function get1xSourcePath(dir, base) {
  const exts = ['.avif', '.webp', '.png', '.jpg', '.jpeg'];
  for (const ext of exts) {
    const p = path.join(dir, base + ext);
    try {
      await fs.access(p);
      return p;
    } catch (_) {}
  }
  return null;
}

/** Создать @2x из 1x (масштаб 200%), затем пересобрать 1x из нового @2x */
async function generate2xFrom1x(dir, base) {
  const src = await get1xSourcePath(dir, base);
  if (!src) {
    console.warn('  ⚠ Нет 1x файла для', path.join(dir, base));
    return;
  }
  const img = sharp(src);
  const meta = await img.metadata();
  const w = meta.width || 0;
  const h = meta.height || 0;
  if (!w || !h) {
    console.warn('  ⚠ Нет размеров:', src);
    return;
  }
  const w2 = w * 2;
  const h2 = h * 2;
  const pipeline = img.clone().resize(w2, h2, { fit: 'fill' });
  const outBase = path.join(dir, base + '@2x');
  await pipeline.clone().avif({ quality: AVIF_QUALITY }).toFile(outBase + '.avif');
  console.log('  ✓', base + '@2x.avif');
  await pipeline.clone().webp({ quality: WEBP_QUALITY }).toFile(outBase + '.webp');
  console.log('  ✓', base + '@2x.webp');
  await pipeline.clone().png({ compressionLevel: 9 }).toFile(outBase + '.png');
  console.log('  ✓', base + '@2x.png');
  // Пересобрать 1x из нового @2x (чтобы 1x = 50% от 2x)
  await generateFrom2xAvif(outBase + '.avif');
}

async function main() {
  console.log('📁 Assets root:', ASSETS_ROOT);
  if (await fs.access(ASSETS_ROOT).then(() => true).catch(() => false) === false) {
    console.error('Папка assets не найдена.');
    process.exit(1);
  }

  // Шаг 0: из каждого @2x.png (приоритет) или @2x.webp создать/перезаписать @2x.avif
  const rasterSources = await find2xRasterSources(ASSETS_ROOT);
  const listRaster = Array.from(rasterSources.values());
  if (listRaster.length > 0) {
    console.log('\n🔄 Шаг 0: создание @2x.avif из', listRaster.length, 'источников (@2x.png / @2x.webp)\n');
    for (const file of listRaster) {
      const rel = path.relative(ASSETS_ROOT, file);
      console.log('📸', rel);
      try {
        await ensure2xAvifFromRaster(file);
      } catch (err) {
        console.error('  ✗', err.message);
      }
    }
  }

  // Шаг 1: все @2x.avif → 1x + @2x webp/png
  const list2x = await find2xAvifFiles(ASSETS_ROOT);
  console.log('\n🔄 Шаг 1: генерация из', list2x.length, 'файлов @2x.avif\n');
  for (const file of list2x) {
    const rel = path.relative(ASSETS_ROOT, file);
    console.log('📸', rel);
    try {
      await generateFrom2xAvif(file);
    } catch (err) {
      console.error('  ✗', err.message);
    }
  }

  // Шаг 2: где нет @2x — создать @2x из 1x
  const list1xOnly = await findAll1xOnly(ASSETS_ROOT);
  console.log('\n🔄 Шаг 2: создание @2x из 1x для', list1xOnly.length, 'баз\n');
  for (const { dir, base } of list1xOnly) {
    const rel = path.relative(ASSETS_ROOT, path.join(dir, base));
    console.log('📸', rel);
    try {
      await generate2xFrom1x(dir, base);
    } catch (err) {
      console.error('  ✗', err.message);
    }
  }

  console.log('\n✅ Генерация завершена.');
}

main().catch((err) => {
  console.error('❌', err);
  process.exit(1);
});
