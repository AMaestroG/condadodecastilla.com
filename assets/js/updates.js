document.addEventListener('DOMContentLoaded', () => {
  fetch('/api_articles.php')
    .then(r => r.json())
    .then(items => {
      const list = document.getElementById('latest-articles');
      if (list) {
        list.innerHTML = '';
        items.forEach(a => {
          const li = document.createElement('li');
          li.innerHTML = `<a href="${a.url}" class="underline text-old-gold hover:text-purple">${a.title}</a>`;
          list.appendChild(li);
        });
      }
    }).catch(() => {});

  fetch('/api_visits.php')
    .then(r => r.json())
    .then(items => {
      const list = document.getElementById('upcoming-visits');
      if (list) {
        list.innerHTML = '';
        items.forEach(v => {
          const li = document.createElement('li');
          li.textContent = `${v.date} - ${v.name}: ${v.description}`;
          list.appendChild(li);
        });
      }
    }).catch(() => {});
});

