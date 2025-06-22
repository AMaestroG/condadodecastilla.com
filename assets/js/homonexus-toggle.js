document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('homonexus-toggle');
  if (!toggle) return;

  const updateState = (active) => {
    document.body.classList.toggle('homonexus-active', active);
    toggle.setAttribute('aria-pressed', active);
    document.cookie = `homonexus=${active ? 'on' : 'off'};path=/;max-age=31536000`;
  };

  // Set initial aria-pressed from body class
  toggle.setAttribute('aria-pressed', document.body.classList.contains('homonexus-active'));

  toggle.addEventListener('click', () => {
    const active = !document.body.classList.contains('homonexus-active');
    updateState(active);
  });
});
