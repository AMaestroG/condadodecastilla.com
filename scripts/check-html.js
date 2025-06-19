#!/usr/bin/env node
"use strict";
const fs = require('fs');
const { JSDOM } = require('jsdom');

const filePath = process.argv[2];
if (!filePath) {
  console.error('Usage: node scripts/check-html.js <file>');
  process.exit(1);
}

let html;
try {
  html = fs.readFileSync(filePath, 'utf8');
} catch (err) {
  console.error(`Cannot read file: ${filePath}`);
  process.exit(1);
}

const dom = new JSDOM(html);
const { document } = dom.window;
let exitCode = 0;

if (!document.documentElement.hasAttribute('lang')) {
  console.error('<html> element missing lang attribute');
  exitCode = 1;
}

const h1s = document.getElementsByTagName('h1');
if (h1s.length !== 1) {
  console.error(`Expected 1 <h1> element, found ${h1s.length}`);
  exitCode = 1;
}

if (exitCode === 0) {
  console.log('HTML validation passed');
}
process.exit(exitCode);
