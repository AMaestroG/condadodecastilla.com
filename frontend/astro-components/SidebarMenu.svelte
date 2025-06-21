<script>
  import { onMount, tick } from 'svelte';
  export let menu = [];
  let isOpen = false;
  let items = [];

  function toggle() {
    isOpen = !isOpen;
    if (isOpen) {
      tick().then(() => {
        items[0]?.focus();
      });
    }
  }

  function handleKeydown(event) {
    if (!isOpen) return;
    const index = items.indexOf(document.activeElement);
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      const next = (index + 1) % items.length;
      items[next]?.focus();
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      const prev = (index - 1 + items.length) % items.length;
      items[prev]?.focus();
    } else if (event.key === 'Escape') {
      isOpen = false;
    }
  }
</script>

<button
  class="toggle-button"
  aria-controls="sidebar-menu"
  aria-expanded={isOpen}
  on:click={toggle}
>
  ☰ Menú
</button>
<nav
  id="sidebar-menu"
  role="navigation"
  class:is-open={isOpen}
  on:keydown={handleKeydown}
  aria-label="Menú principal"
>
  <ul>
    {#each menu as item, i}
      <li>
        <a href={item.url} bind:this={el => (items[i] = el)}>{item.label}</a>
      </li>
    {/each}
  </ul>
</nav>

<style>
  nav {
    position: fixed;
    top: var(--menu-top-offset, 0);
    left: 0;
    height: 100%;
    width: 250px;
    transform: translateX(-100%);
    transition: transform var(--global-transition-speed, 0.3s);
    background: var(--epic-alabaster-bg);
    box-shadow: var(--global-box-shadow-medium);
    z-index: 1000;
    padding: 1rem;
  }
  nav.is-open {
    transform: translateX(0);
  }
  button.toggle-button {
    background: var(--epic-purple-emperor);
    color: var(--epic-gold-main);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--global-border-radius);
    cursor: pointer;
  }
  button.toggle-button:focus {
    outline: 2px solid var(--epic-gold-main);
  }
  ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  li a {
    display: block;
    padding: 0.5rem 0;
    color: var(--epic-purple-emperor);
    background-image: linear-gradient(to right, var(--epic-purple-emperor), var(--epic-gold-main));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-decoration: none;
  }
  li a:focus {
    outline: 2px solid var(--epic-gold-main);
  }
</style>
