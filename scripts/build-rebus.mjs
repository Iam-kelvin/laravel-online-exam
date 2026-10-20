import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const destination = fileURLToPath(new URL('../public/puzzles/rebus/', import.meta.url));
fs.mkdirSync(destination, { recursive: true });
const escape = value => String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
const text = (value, x = 300, y = 165, size = 52) => `<text x="${x}" y="${y}" text-anchor="middle" font-family="Arial, sans-serif" font-size="${size}" font-weight="700" fill="#302346">${escape(value)}</text>`;
const line = (x1, y1, x2, y2) => `<path d="M${x1} ${y1} L${x2} ${y2}" stroke="#7a60a5" stroke-width="4" fill="none"/>`;
const box = (x = 200, y = 70, width = 200, height = 160) => `<rect x="${x}" y="${y}" width="${width}" height="${height}" rx="3" fill="none" stroke="#7a60a5" stroke-width="4"/>`;
const stacked = (top, bottom) => text(top, 300, 125) + text(bottom, 300, 205);
const puzzles = [
    text('M1Y L1I1F1E', 300, 175, 58),
    stacked('HEAD', 'HEELS'),
    line(130, 100, 470, 100) + text('READING', 300, 168) + line(130, 195, 470, 195),
    stacked('MAN', 'BOARD'),
    stacked('STAND', 'I'),
    stacked('MIND', 'MATTER'),
    text('CYCLE', 115, 165, 38) + text('CYCLE', 300, 165, 38) + text('CYCLE', 485, 165, 38),
    text('LIFE', 185, 165) + text('LIFE', 415, 165),
    text('TIME', 175, 165) + text('TIME', 425, 165),
    text('T', 95, 165, 60) + text('I', 155, 165, 60) + text('JUST', 300, 162, 40) + text('M', 435, 165, 60) + text('E', 505, 165, 60),
    text('HE', 215, 167, 60) + text('ART', 405, 167, 60) + '<path d="M305 80 L283 110 L308 140 L285 170 L308 200 L288 230" fill="none" stroke="#c6617c" stroke-width="4"/>',
    text('SEC', 200, 165, 60) + text('OND', 430, 165, 60) + line(312, 85, 312, 220),
    text('A B C D E F G H I J K L M', 300, 130, 28) + text('N O P Q R S T V W X Y Z', 300, 190, 28),
    text('ECNALG', 300, 168, 60),
    box(75, 70, 180, 160) + text('TOUCH', 415, 165, 45),
    box(50, 70, 160, 160) + text('THINKING', 415, 165, 39),
    box(100, 50, 400, 220) + text('SECRET', 300, 100, 45),
    box(100, 40, 400, 220) + text('LINE', 300, 243, 45),
    line(120, 105, 480, 105) + text('GROUND', 300, 195),
    text('BRIDGE', 300, 105, 48) + line(135, 135, 465, 135) + text('WATER', 300, 210, 48),
    text('LE', 200, 125, 60) + text('VEL', 385, 205, 60),
    line(80, 80, 520, 80) + text('FEET   FEET   FEET', 300, 155, 33) + text('FEET   FEET   FEET', 300, 225, 33),
    text('NOON', 300, 90, 54) + line(140, 245, 460, 245),
    text('WORLD', 95, 65, 28) + text('WORLD', 505, 65, 28) + text('WORLD', 95, 255, 28) + text('WORLD', 505, 255, 28),
];
puzzles.forEach((puzzle, index) => {
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 300" role="img"><title>Picture puzzle</title><rect width="600" height="300" rx="12" fill="#f2ecff"/>${puzzle}</svg>\n`;
    fs.writeFileSync(path.join(destination, `puzzle-${String(index + 1).padStart(2, '0')}.svg`), svg);
});
console.log(`Built ${puzzles.length} rebus images.`);
