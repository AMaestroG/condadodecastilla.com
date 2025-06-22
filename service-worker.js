const CACHE_NAME = 'assets-cache-v4';
const CORE_ROUTES = [
  '/index.php',
  '/foro/index.php',
  '/museo/museo.php',
  '/lugares/lugares.php'
];
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(CORE_ROUTES))
  );
  self.skipWaiting();
});
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(name => {
          if (name !== CACHE_NAME) {
            return caches.delete(name);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  if (CORE_ROUTES.includes(url.pathname)) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => {
        return cache.match(event.request).then(cached => {
          const fetchPromise = fetch(event.request).then(networkResp => {
            cache.put(event.request, networkResp.clone());
            return networkResp;
          });
          return cached || fetchPromise;
        });
      })
    );
    return;
  }
  if (url.pathname.startsWith('/assets/') ||
      url.pathname.startsWith('/museo/') ||
      url.pathname.startsWith('/lugares/')) {
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
  }
});
