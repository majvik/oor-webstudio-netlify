const fs = require('fs');
const path = require('path');

const FULL_W = 5954;
const FULL_H = 850;
const SPLIT_X = 2810;

const gradientRects = [
  { x: 358, y: 227, w: 95, h: 90, type: 'win' },
  { x: 625, y: 172, w: 352, h: 90, type: 'win' },
  { x: 1173, y: 247, w: 328, h: 90, type: 'loss' },
  { x: 1734, y: 207, w: 291, h: 90, type: 'win' },
  { x: 2214, y: 170, w: 335, h: 90, type: 'win' },
  { x: 2459, y: 269, w: 91, h: 90, type: 'win' },
  { x: 446, y: 547, w: 269, h: 90, type: 'win' },
  { x: 869, y: 496, w: 369, h: 90, type: 'win' },
  { x: 1493, y: 556, w: 270, h: 90, type: 'win' },
  { x: 1925, y: 517, w: 99, h: 90, type: 'win' },
  { x: 2195, y: 585, w: 92, h: 90, type: 'loss' },
  { x: 2489, y: 500, w: 321, h: 90, type: 'win' },
  { x: 2746, y: 192, w: 328, h: 90, type: 'loss' },
  { x: 2974, y: 514, w: 360, h: 90, type: 'win' },
  { x: 3270, y: 237, w: 328, h: 119, type: 'win' },
  { x: 3538, y: 513, w: 59, h: 90, type: 'win' },
  { x: 3745, y: 585, w: 114, h: 90, type: 'win' },
  { x: 3813, y: 211, w: 46, h: 119, type: 'win' },
  { x: 4017, y: 251, w: 104, h: 119, type: 'win' },
  { x: 4040, y: 525, w: 81, h: 119, type: 'win' },
  { x: 4304, y: 574, w: 341, h: 119, type: 'win' },
  { x: 4324, y: 199, w: 59, h: 119, type: 'win' },
  { x: 4559, y: 261, w: 86, h: 90, type: 'win' },
  { x: 4807, y: 202, w: 100, h: 90, type: 'win' },
  { x: 4812, y: 519, w: 356, h: 90, type: 'loss' },
  { x: 5074, y: 267, w: 357, h: 90, type: 'win' },
  { x: 5371, y: 543, w: 60, h: 90, type: 'loss' },
  { x: 5593, y: 240, w: 361, h: 90, type: 'win' },
  { x: 5627, y: 588, w: 327, h: 90, type: 'loss' },
];

const verticalLines = [];
const lineXPositions = [190,452,714,976,1238,1500,1762,2024,2286,2548,2810];
const lineXPositions2 = [3072,3334,3596,3858,4120,4382,4644,4906,5168,5430,5692];

lineXPositions.forEach(x => {
  verticalLines.push({ x, y: 134, h: 582, color: '#ebebeb' });
});
lineXPositions2.forEach(x => {
  verticalLines.push({ x, y: 87, h: 676, color: '#ebebeb' });
});

const redLines = [
  { x: 190, y: 317, h: 231 },
  { x: 452, y: 262, h: 156 },
  { x: 714, y: 402, h: 94 },
  { x: 976, y: 345, h: 73 },
  { x: 1238, y: 411, h: 147 },
  { x: 1500, y: 294, h: 124 },
  { x: 1762, y: 409, h: 105 },
  { x: 2024, y: 263, h: 324 },
  { x: 2286, y: 362, h: 142 },
  { x: 2548, y: 282, h: 136 },
  { x: 2810, y: 402, h: 112 },
  { x: 3072, y: 356, h: 55 },
  { x: 3334, y: 402, h: 111 },
  { x: 3596, y: 330, h: 255 },
  { x: 3858, y: 370, h: 155 },
  { x: 4120, y: 318, h: 256 },
  { x: 4382, y: 360, h: 58 },
  { x: 4644, y: 292, h: 227 },
  { x: 4906, y: 357, h: 61 },
  { x: 5168, y: 402, h: 144 },
  { x: 5430, y: 330, h: 258 },
];

const axisSegments = [
  { x: 0, w: 190 },
  { x: 190, w: 262 }, { x: 452, w: 262 }, { x: 714, w: 262 }, { x: 976, w: 262 },
  { x: 1238, w: 262 }, { x: 1500, w: 262 }, { x: 1762, w: 262 }, { x: 2024, w: 262 },
  { x: 2286, w: 262 }, { x: 2548, w: 262 }, { x: 2810, w: 262 },
  { x: 3072, w: 262 }, { x: 3334, w: 262 }, { x: 3596, w: 262 }, { x: 3858, w: 262 },
  { x: 4120, w: 262 }, { x: 4382, w: 262 }, { x: 4644, w: 262 }, { x: 4906, w: 262 },
  { x: 5168, w: 262 }, { x: 5430, w: 262 }, { x: 5692, w: 262 },
];

const dateLabels = [
  { x: 196, text: '21 Ноября' }, { x: 458, text: '16 Ноября' }, { x: 720, text: '15 Ноября' },
  { x: 982, text: '9 Ноября' }, { x: 1244, text: '3 Ноября' }, { x: 1506, text: '1 Ноября' },
  { x: 1768, text: '26 Октября' }, { x: 2030, text: '18 Октября' }, { x: 2292, text: '12 Октября' },
  { x: 2554, text: '4 Октября' }, { x: 2816, text: '28 сентября' },
  { x: 3078, text: '28 сентября' }, { x: 3340, text: '6 сентября' }, { x: 3602, text: '6 сентября' },
  { x: 3864, text: '23 августа' }, { x: 4126, text: '23 августа' }, { x: 4388, text: '10 августа' },
  { x: 4650, text: '10 августа' }, { x: 4912, text: '7 августа' }, { x: 5174, text: '7 августа' },
  { x: 5436, text: '3 августа' },
];

const topCards = [
  { x: 191, y: 227, opponent: 'DAWGS vs HOOPS', score: '53:38 - W', win: true },
  { x: 453, y: 172, opponent: 'DAWGS vs Sky Clyb', score: '29:19 - W', win: true },
  { x: 977, y: 247, opponent: 'DAWGS vs Blatoshpera', score: '20:23 - L', win: false },
  { x: 1501, y: 207, opponent: 'DAWGS vs MDK Basket Club', score: '27:16 - W', win: true },
  { x: 2025, y: 170, opponent: 'DAWGS vs Pena Team', score: '29:19 - W', win: true },
  { x: 2287, y: 269, opponent: 'DAWGS vs T-Squad', score: '27:16 - W', win: true },
  { x: 2549, y: 192, opponent: 'DAWGS vs Players Club', score: '20:12 - L', win: false },
  { x: 4383, y: 261, opponent: 'DAWGS vs SkyCLub', score: '19:10 - W', win: true },
];

const bottomCards = [
  { x: 191, y: 547, opponent: 'DAWGS vs Underground Bizne$', score: '34:22 - W', win: true },
  { x: 715, y: 496, opponent: 'DAWGS vs AUF', score: '45:44 - W', win: true },
  { x: 1239, y: 556, opponent: 'DAWGS vs Sayonara Boys Club', score: '23:22 - W', win: true },
  { x: 1763, y: 517, opponent: 'DAWGS vs GOATS', score: '27:16 - W', win: true },
  { x: 2287, y: 501, opponent: 'DAWGS vs Alikson Team', score: '29:19 - W', win: true },
  { x: 2025, y: 585, opponent: 'DAWGS vs Sky Clyb', score: '18:21 - L', win: false },
];

const topCardsS2 = [
  { x: 2811, y: 514, opponent: 'DAWGS vs Lugang', score: '32:28 - W', win: true },
  { x: 3073, y: 237, opponent: 'DAWGS vs Players Club', score: '20:19 - W', win: true, stage: 'Финал' },
  { x: 3597, y: 211, opponent: 'DAWGS vs ЦОП Bad Boys', score: '16:15 - W', win: true, stage: 'Финал' },
  { x: 4121, y: 199, opponent: 'DAWGS vs Rocket Team', score: '? - W', win: true, stage: 'Финал' },
  { x: 4645, y: 202, opponent: 'DAWGS vs GOATS', score: '? - W', win: true },
  { x: 4907, y: 267, opponent: 'DAWGS vs HOOPS', score: '34:33 - W', win: true },
  { x: 5431, y: 240, opponent: 'DAWGS vs GOATS', score: '28:18 - W', win: true },
];

const bottomCardsS2 = [
  { x: 3335, y: 516, opponent: 'DAWGS vs Антихрупкие', score: '21:12 - W', win: true },
  { x: 3597, y: 588, opponent: 'DAWGS vs Zizzi', score: '21:13 - W', win: true },
  { x: 3859, y: 251, opponent: 'DAWGS vs AUF', score: '22:19 - W', win: true, stage: 'Группа' },
  { x: 3859, y: 525, opponent: 'DAWGS vs СиндЕкат', score: '16:11 - W', win: true, stage: 'Группа' },
  { x: 4121, y: 574, opponent: 'DAWGS vs AS Basket', score: '22:13 - W', win: true, stage: 'Группа' },
  { x: 4645, y: 522, opponent: 'DAWGS vs HOOPS', score: '? - L', win: false },
  { x: 5169, y: 546, opponent: 'DAWGS vs Alikson Team', score: '27:33 - L', win: false },
  { x: 5431, y: 591, opponent: 'DAWGS vs Players Club', score: '23:25 - L', win: false },
];

const titles = [
  { x: 48, y: 42, text: 'TIMELINE - MAIN', size: 40 },
  { x: 2815, y: 42, text: 'TIMELINE - МЕЖСЕЗОНЬЕ', size: 40 },
];

const events = [
  { x: 2815, y: 115, text: 'MEDIA BASKET SPB (Чемпионы)' },
  { x: 3341, y: 115, text: 'СтритБаскет Пикник (Чемпионы)' },
  { x: 3863, y: 115, text: 'СтритБаскет Пикник (Чемпионы)' },
  { x: 4387, y: 115, text: 'HOOPS DAY' },
  { x: 4913, y: 115, text: 'СтритБаскет Медиаостановка' },
  { x: 5437, y: 115, text: 'FONBASE' },
];

function escapeXml(s) {
  return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function renderCard(card, isBottom) {
  const cardH = card.stage ? 119 : 90;
  const cardW = Math.max(160, card.opponent.length * 8 + 32);
  let parts = [];

  const bgColor = card.win ? 'rgba(50,60,50,0.92)' : 'rgba(80,70,70,0.92)';
  const dotColor = card.win ? '#befd58' : '#f11a1a';

  if (isBottom) {
    parts.push(`<circle cx="${card.x + 3}" cy="${card.y}" r="3" fill="${dotColor}"/>`);
    parts.push(`<rect x="${card.x + 3}" y="${card.y + 3}" width="${cardW}" height="${cardH}" rx="0" fill="${bgColor}"/>`);
    if (card.stage) {
      parts.push(`<text x="${card.x + 19}" y="${card.y + 19}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="12" font-weight="300" fill="white">${escapeXml(card.stage)}</text>`);
      parts.push(`<line x1="${card.x + 19}" y1="${card.y + 32}" x2="${card.x + cardW - 16}" y2="${card.y + 32}" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>`);
      parts.push(`<text x="${card.x + 19}" y="${card.y + 51}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="14" font-weight="300" fill="white">${escapeXml(card.opponent)}</text>`);
      parts.push(`<text x="${card.x + 19}" y="${card.y + 78}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="28" font-weight="700" fill="white">${escapeXml(card.score)}</text>`);
    } else {
      parts.push(`<text x="${card.x + 19}" y="${card.y + 22}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="14" font-weight="300" fill="white">${escapeXml(card.opponent)}</text>`);
      parts.push(`<text x="${card.x + 19}" y="${card.y + 55}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="28" font-weight="700" fill="white">${escapeXml(card.score)}</text>`);
    }
  } else {
    parts.push(`<rect x="${card.x + 3}" y="${card.y}" width="${cardW}" height="${cardH}" rx="0" fill="${bgColor}"/>`);
    if (card.stage) {
      parts.push(`<text x="${card.x + 19}" y="${card.y + 16}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="12" font-weight="300" fill="white">${escapeXml(card.stage)}</text>`);
      parts.push(`<line x1="${card.x + 19}" y1="${card.y + 29}" x2="${card.x + cardW - 16}" y2="${card.y + 29}" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>`);
      parts.push(`<text x="${card.x + 19}" y="${card.y + 48}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="14" font-weight="300" fill="white">${escapeXml(card.opponent)}</text>`);
      parts.push(`<text x="${card.x + 19}" y="${card.y + 75}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="28" font-weight="700" fill="white">${escapeXml(card.score)}</text>`);
    } else {
      parts.push(`<text x="${card.x + 19}" y="${card.y + 19}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="14" font-weight="300" fill="white">${escapeXml(card.opponent)}</text>`);
      parts.push(`<text x="${card.x + 19}" y="${card.y + 52}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="28" font-weight="700" fill="white">${escapeXml(card.score)}</text>`);
    }
    parts.push(`<circle cx="${card.x + 3}" cy="${card.y + cardH + 6}" r="3" fill="${dotColor}"/>`);
  }
  return parts.join('\n    ');
}

function generateSVG(xOffset, width, seasonIndex) {
  let svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${xOffset} 0 ${width} ${FULL_H}" width="${width}" height="${FULL_H}">
  <defs>
    <linearGradient id="grad-win-${seasonIndex}" x1="100%" y1="0%" x2="0%" y2="0%">
      <stop offset="5.8%" stop-color="rgba(255,255,255,0.43)"/>
      <stop offset="100%" stop-color="rgba(190,253,88,0.43)"/>
    </linearGradient>
    <linearGradient id="grad-loss-${seasonIndex}" x1="100%" y1="0%" x2="0%" y2="0%">
      <stop offset="5.8%" stop-color="rgba(255,255,255,0.43)"/>
      <stop offset="100%" stop-color="rgba(166,176,174,0.43)"/>
    </linearGradient>
  </defs>
  <rect x="${xOffset}" y="0" width="${width}" height="${FULL_H}" fill="white"/>
`;

  gradientRects.filter(r => r.x >= xOffset && r.x < xOffset + width).forEach(r => {
    const grad = r.type === 'win' ? `grad-win-${seasonIndex}` : `grad-loss-${seasonIndex}`;
    svg += `  <rect x="${r.x}" y="${r.y}" width="${r.w}" height="${r.h}" fill="url(#${grad})"/>\n`;
  });

  verticalLines.filter(l => l.x >= xOffset && l.x < xOffset + width).forEach(l => {
    svg += `  <rect x="${l.x}" y="${l.y}" width="2" height="${l.h}" fill="${l.color}"/>\n`;
  });

  redLines.filter(l => l.x >= xOffset && l.x < xOffset + width).forEach(l => {
    svg += `  <rect x="${l.x}" y="${l.y}" width="2" height="${l.h}" fill="#f11a1a"/>\n`;
  });

  const AXIS_Y = 402;
  axisSegments.filter(s => s.x >= xOffset && (s.x + s.w) <= (xOffset + width + 10)).forEach(s => {
    svg += `  <rect x="${s.x}" y="${AXIS_Y}" width="2" height="16" fill="black"/>\n`;
    svg += `  <rect x="${s.x + 2}" y="${AXIS_Y + 7}" width="${s.w - 2}" height="2" fill="black"/>\n`;
  });

  dateLabels.filter(d => d.x >= xOffset && d.x < xOffset + width).forEach(d => {
    svg += `  <text x="${d.x}" y="${AXIS_Y + 39}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="14" font-weight="300" fill="black" letter-spacing="-0.28">${escapeXml(d.text)}</text>\n`;
  });

  titles.filter(t => t.x >= xOffset && t.x < xOffset + width).forEach(t => {
    svg += `  <text x="${t.x}" y="${t.y + 40}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="${t.size}" font-weight="700" fill="black" text-transform="uppercase">${escapeXml(t.text)}</text>\n`;
  });

  events.filter(e => e.x >= xOffset && e.x < xOffset + width).forEach(e => {
    svg += `  <text x="${e.x}" y="${e.y + 16}" font-family="'Pragmatica Extended','Helvetica Neue',Arial,sans-serif" font-size="16" font-weight="300" fill="black">${escapeXml(e.text)}</text>\n`;
  });

  const allTopCards = [...topCards, ...topCardsS2];
  const allBottomCards = [...bottomCards, ...bottomCardsS2];

  allTopCards.filter(c => c.x >= xOffset && c.x < xOffset + width).forEach(c => {
    svg += `    ${renderCard(c, false)}\n`;
  });

  allBottomCards.filter(c => c.x >= xOffset && c.x < xOffset + width).forEach(c => {
    svg += `    ${renderCard(c, true)}\n`;
  });

  svg += '</svg>';
  return svg;
}

const outDir = path.join(__dirname, '..', 'wp-content', 'themes', 'oor-theme', 'public', 'assets');

const svg1 = generateSVG(0, SPLIT_X, 1);
fs.writeFileSync(path.join(outDir, 'dawgs-timeline-1.svg'), svg1);
console.log('Generated dawgs-timeline-1.svg (Season 1: Main)');

const svg2 = generateSVG(SPLIT_X, FULL_W - SPLIT_X, 2);
fs.writeFileSync(path.join(outDir, 'dawgs-timeline-2.svg'), svg2);
console.log('Generated dawgs-timeline-2.svg (Season 2: Межсезонье)');
