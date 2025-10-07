const root = document.querySelector('.site-shell');
const menus = {
  left: document.getElementById('menu-left'),
  right: document.getElementById('menu-right')
};
const toggles = document.querySelectorAll('.menu-toggle');
const heroLead = document.querySelector('.hero-lead');
const agentGrid = document.querySelector('[data-role="agents-grid"]');

const state = {
  open: null
};

function setMenu(side, open) {
  const menu = menus[side];
  if (!menu) return;

  if (open) {
    Object.keys(menus).forEach((key) => {
      if (key !== side) {
        menus[key]?.classList.remove('open');
        menus[key]?.setAttribute('aria-hidden', 'true');
        root?.classList.remove(`menu-open-${key}`);
      }
    });
    menu.classList.add('open');
    menu.setAttribute('aria-hidden', 'false');
    root?.classList.add(`menu-open-${side}`);
    state.open = side;
  } else {
    menu.classList.remove('open');
    menu.setAttribute('aria-hidden', 'true');
    root?.classList.remove(`menu-open-${side}`);
    if (state.open === side) {
      state.open = null;
    }
  }
}

function toggleMenu(side) {
  if (state.open === side) {
    setMenu(side, false);
  } else {
    setMenu(side, true);
  }
}

toggles.forEach((toggle) => {
  const side = toggle.dataset.menu;
  if (!side) return;
  toggle.addEventListener('click', (event) => {
    event.stopPropagation();
    toggleMenu(side);
  });
});

document.addEventListener('click', (event) => {
  if (!state.open) return;
  const menu = menus[state.open];
  if (!menu) return;
  const target = event.target;
  if (menu.contains(target) || (target instanceof Element && target.closest('.menu-toggle'))) {
    return;
  }
  setMenu(state.open, false);
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && state.open) {
    setMenu(state.open, false);
  }
});

async function refreshMission() {
  const endpoint = root?.dataset.missionEndpoint;
  if (!endpoint || !heroLead) return;
  try {
    const response = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
    if (!response.ok) return;
    const data = await response.json();
    if (data?.mission) {
      heroLead.textContent = data.mission;
    }
  } catch (error) {
    console.warn('No se pudo actualizar la misión', error);
  }
}

function createAgentCard(key, agent) {
  const article = document.createElement('article');
  article.className = 'agent-card';
  article.dataset.agent = key;

  const badge = document.createElement('span');
  badge.className = 'agent-badge';
  badge.innerHTML = `<i class="${agent.role_icon ?? 'fas fa-star'}"></i> ${agent.name ?? 'Agente'}`;

  const title = document.createElement('h4');
  title.textContent = agent.expertise ?? '';

  const bio = document.createElement('p');
  bio.textContent = agent.bio ?? '';

  article.append(badge, title, bio);

  if (agent.vision) {
    const vision = document.createElement('p');
    const emphasis = document.createElement('em');
    emphasis.textContent = agent.vision;
    vision.append(emphasis);
    article.append(vision);
  }

  return article;
}

function diffAgents(seed, incoming) {
  const merged = { ...seed };
  Object.entries(incoming).forEach(([key, value]) => {
    if (typeof value === 'object' && value) {
      merged[key] = { ...(merged[key] ?? {}), ...value };
    }
  });
  return merged;
}

async function hydrateAgents() {
  if (!agentGrid || !root) return;
  let seed = {};
  try {
    seed = JSON.parse(root.dataset.agentSeed || '{}');
  } catch (error) {
    console.warn('Semilla de agentes inválida', error);
  }

  const endpoint = root.dataset.agentsEndpoint;
  if (endpoint) {
    try {
      const response = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
      if (response.ok) {
        const data = await response.json();
        if (data && typeof data === 'object') {
          seed = diffAgents(seed, data);
        }
      }
    } catch (error) {
      console.warn('No se pudo cargar agentes dinámicos', error);
    }
  }

  agentGrid.replaceChildren();
  Object.entries(seed).forEach(([key, agent]) => {
    agentGrid.appendChild(createAgentCard(key, agent));
  });
}

let gsapAttempts = 0;

function initGsap() {
  if (!window.gsap) {
    if (gsapAttempts < 10) {
      gsapAttempts += 1;
      setTimeout(initGsap, 120);
    }
    return;
  }
  const timeline = window.gsap.timeline({ defaults: { duration: 0.8, ease: 'power2.out' } });
  timeline
    .from('.gradient-display', { y: 40, opacity: 0 })
    .from('.hero-lead', { y: 20, opacity: 0 }, '-=0.4')
    .from('.hero-cta a', { y: 25, opacity: 0, stagger: 0.15 }, '-=0.5');
}

refreshMission();
hydrateAgents();
initGsap();

export {};
