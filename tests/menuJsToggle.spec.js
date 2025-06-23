import { describe, it, expect } from 'vitest'
import { JSDOM } from 'jsdom'
import fs from 'fs'

const menuScript = fs.readFileSync('nuevaweb/src/js/menu.js', 'utf8')

describe('menu.js toggle behavior', () => {
  it('toggles aria-expanded and open class', () => {
    const dom = new JSDOM('<button id="menu-toggle"></button><div id="menu"></div>', { runScripts: 'dangerously' })
    const { window } = dom

    const scriptEl = window.document.createElement('script')
    scriptEl.textContent = menuScript
    window.document.body.appendChild(scriptEl)

    // Trigger DOMContentLoaded so the script initializes
    window.document.dispatchEvent(new window.Event('DOMContentLoaded'))

    const btn = window.document.getElementById('menu-toggle')
    const menu = window.document.getElementById('menu')

    btn.click()
    expect(menu.classList.contains('open')).toBe(true)
    expect(window.document.body.classList.length).toBe(0)
    expect(btn.getAttribute('aria-expanded')).toBe('true')

    btn.click()
    expect(menu.classList.contains('open')).toBe(false)
    expect(window.document.body.classList.length).toBe(0)
    expect(btn.getAttribute('aria-expanded')).toBe('false')
  })
})
