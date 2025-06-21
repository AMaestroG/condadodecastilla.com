const CACHE_NAME = 'assets-cache-v1';
const PAGE_CACHE = 'pages-cache-v1';
const PAGE_URLS = ['/index.php', '/foro/index.php', '/tailwind_index.php'];
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(PAGE_CACHE).then(cache => cache.addAll(PAGE_URLS))
  );
});
self.addEventListener('activate', event => {
  event.waitUntil(clients.claim());
});
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/assets/')) {
    event.respondWith(
      caches.match(event.request).then(resp => {
        if (resp) return resp;
        return fetch(event.request).then(networkResp => {
          const copy = networkResp.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
          return networkResp;
        });
      })
    );
  } else if (event.request.mode === 'navigate' || PAGE_URLS.includes(url.pathname)) {
    event.respondWith(
      caches.open(PAGE_CACHE).then(cache =>
        cache.match(event.request).then(cached => {
          const fetchPromise = fetch(event.request).then(networkResp => {
            cache.put(event.request, networkResp.clone());
            return networkResp;
          }).catch(() => cached);
          return cached || fetchPromise;
        })
      )
    );
  }
});
