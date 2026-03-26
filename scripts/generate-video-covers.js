#!/usr/bin/env node
/**
 * Генерация обложек для hero/manifest и talk-show (все из PNG + sharp).
 * Выход: width 1920, AVIF/WebP quality 70, PNG compression 9.
 *
 * Источники: scripts/sources/*.png или ~/.cursor/projects/.../assets (имена по умолчанию).
 * Переопределение: TALK_SHOW_COVER_1, TALK_SHOW_COVER_2 (абсолютные пути к PNG).
 *
 * Usage: node scripts/generate-video-covers.js
 */

const fs = require('fs').promises;
const path = require('path');
const sharp = require('sharp');

const PROJECT_ROOT = path.join(__dirname, '..');
const ASSETS_DIR = path.join(
  PROJECT_ROOT,
  'wp-content',
  'themes',
  'oor-theme',
  'public',
  'assets'
);
const SOURCES_DIR = path.join(PROJECT_ROOT, 'scripts', 'sources');

const WIDTH = 1920;
const AVIF_Q = 70;
const WEBP_Q = 70;
const PNG_COMPRESSION = 9;

const DEFAULT_CURSOR_ASSETS = path.join(
  process.env.HOME || '',
  '.cursor',
  'projects',
  'Users-vik-Projects-OOR-webstudio-netlify',
  'assets'
);

async function ensureSources() {
  const heroNamed = path.join(SOURCES_DIR, 'hero-video-cover-source.png');
  const manifestNamed = path.join(SOURCES_DIR, 'manifest-video-cover-source.png');
  try {
    await fs.access(heroNamed);
    await fs.access(manifestNamed);
    return { hero: heroNamed, manifest: manifestNamed };
  } catch {
    const heroGlob = path.join(
      DEFAULT_CURSOR_ASSETS,
      'hero-video-cover-da231e66-cea1-44fa-ab2a-8179f075c618.png'
    );
    const manGlob = path.join(
      DEFAULT_CURSOR_ASSETS,
      'manifest-video-cover-065a22d5-e3d7-4f7c-8709-7409b329313c.png'
    );
    try {
      await fs.access(heroGlob);
      await fs.access(manGlob);
      return { hero: heroGlob, manifest: manGlob };
    } catch (e) {
      console.error(
        'Missing sources. Add scripts/sources/hero-video-cover-source.png and manifest-video-cover-source.png'
      );
      throw e;
    }
  }
}

async function rasterToCovers(inputPath, baseName) {
  const pipeline = sharp(inputPath).resize({
    width: WIDTH,
    withoutEnlargement: true,
  });
  const buf = await pipeline.clone().toBuffer();

  const outBase = path.join(ASSETS_DIR, baseName);
  await sharp(buf).avif({ quality: AVIF_Q }).toFile(outBase + '.avif');
  await sharp(buf).webp({ quality: WEBP_Q }).toFile(outBase + '.webp');
  await sharp(buf).png({ compressionLevel: PNG_COMPRESSION }).toFile(outBase + '.png');
  console.log('  ✓', baseName + '.{avif,webp,png}');
}

async function ensureTalkShowSources() {
  const env1 = process.env.TALK_SHOW_COVER_1;
  const env2 = process.env.TALK_SHOW_COVER_2;
  if (env1 && env2) {
    await fs.access(env1);
    await fs.access(env2);
    return { talk1: env1, talk2: env2 };
  }
  const s1 = path.join(SOURCES_DIR, 'talk-show-hero-video-1-source.png');
  const s2 = path.join(SOURCES_DIR, 'talk-show-hero-video-2-source.png');
  try {
    await fs.access(s1);
    await fs.access(s2);
    return { talk1: s1, talk2: s2 };
  } catch {
    const c1 = path.join(
      DEFAULT_CURSOR_ASSETS,
      'talk-show-video-1-cover-df6bc99a-6ff3-43e6-aeba-f8cf40a43801.png'
    );
    const c2 = path.join(
      DEFAULT_CURSOR_ASSETS,
      'talk-show-video-2-cover-158ad30e-cb4a-49e5-b4e5-e1b1747f2af0.png'
    );
    await fs.access(c1);
    await fs.access(c2);
    return { talk1: c1, talk2: c2 };
  }
}

async function main() {
  await fs.mkdir(ASSETS_DIR, { recursive: true });
  await fs.mkdir(SOURCES_DIR, { recursive: true });

  console.log('Hero + manifest (from PNG)...');
  const src = await ensureSources();
  await rasterToCovers(src.hero, 'hero-video-cover');
  await rasterToCovers(src.manifest, 'manifest-video-cover');

  console.log('Talk-show covers (from PNG)...');
  const talk = await ensureTalkShowSources();
  console.log('  talk 1:', talk.talk1);
  console.log('  talk 2:', talk.talk2);
  await rasterToCovers(talk.talk1, 'talk-show-hero-video-1-cover');
  await rasterToCovers(talk.talk2, 'talk-show-hero-video-2-cover');

  console.log('Done.');
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
