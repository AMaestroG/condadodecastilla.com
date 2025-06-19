window.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('top-nav');
  const btn = document.getElementById('mobile-menu-btn');
  const menu = document.getElementById('mobile-menu');
  const links = menu.querySelectorAll('a');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      nav.classList.add('bg-purple-900', 'bg-opacity-90', 'backdrop-blur');
      nav.classList.remove('bg-transparent');
    } else {
      nav.classList.remove('bg-purple-900', 'bg-opacity-90', 'backdrop-blur');
      nav.classList.add('bg-transparent');
    }
  });

  const openMenu = () => {
    menu.classList.add('open');
    menu.classList.remove('hidden');
    btn.setAttribute('aria-expanded', 'true');
    links.forEach((link, i) => {
      setTimeout(() => {
        link.classList.remove('-translate-y-5', 'opacity-0');
      }, i * 100);
    });
    links[0].focus();
  };

  const closeMenu = () => {
    menu.classList.remove('open');
    menu.classList.add('hidden');
    btn.setAttribute('aria-expanded', 'false');
    links.forEach(link => {
      link.classList.add('-translate-y-5', 'opacity-0');
    });
    btn.focus();
  };

  btn.addEventListener('click', () => {
    if (menu.classList.contains('hidden')) {
      openMenu();
    } else {
      closeMenu();
    }
  });

  menu.addEventListener('click', (e) => {
    if (e.target === menu || e.target.tagName === 'A') {
      closeMenu();
    }
  });
});
