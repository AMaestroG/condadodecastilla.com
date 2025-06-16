const {JSDOM} = require('jsdom');
const {window} = new JSDOM(`<!DOCTYPE html><body>
  <button id="left-btn" data-menu-target="menu-left"></button>
  <button id="right-btn" data-menu-target="menu-right"></button>
  <nav id="menu-left" class="slide-menu left"></nav>
  <nav id="menu-right" class="slide-menu right"></nav>
</body>`, {url: 'http://localhost'});

global.window = window;
global.document = window.document;
const body = window.document.body;

// Mock getComputedStyle width for menus
Object.defineProperty(window.HTMLElement.prototype, 'offsetWidth', {get(){return 260;}});

require('../../js/sliding-menu.js');
window.document.dispatchEvent(new window.Event('DOMContentLoaded'));

function click(el){
  const evt = new window.Event('click', {bubbles:true});
  el.dispatchEvent(evt);
}

function check(condition, msg){
  if(!condition){
    console.error('Test failed:', msg);
    process.exit(1);
  }
}

const leftBtn = document.getElementById('left-btn');
const rightBtn = document.getElementById('right-btn');
const leftMenu = document.getElementById('menu-left');
const rightMenu = document.getElementById('menu-right');

// Open left
click(leftBtn);
check(leftMenu.classList.contains('open'), 'Left menu should open');
check(body.classList.contains('menu-open-left'), 'Body shifted left');

// Close left
click(leftBtn);
check(!leftMenu.classList.contains('open'), 'Left menu should close');
check(!body.classList.contains('menu-open-left'), 'Body reset after closing left');

// Open right
click(rightBtn);
check(rightMenu.classList.contains('open'), 'Right menu should open');
check(body.classList.contains('menu-open-right'), 'Body shifted right');

// Close right
click(rightBtn);
check(!rightMenu.classList.contains('open'), 'Right menu should close');
check(!body.classList.contains('menu-open-right'), 'Body reset after closing right');

console.log('sliding-menu tests passed');
