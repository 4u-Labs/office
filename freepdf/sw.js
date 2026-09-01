const CACHE_NAME = 'freepdf-stirling-v3.0';

self.addEventListener('install', (e) => {
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  if (e.request.method !== 'GET') return;
  e.respondWith(
    fetch(e.request)
      .then((response) => {
        return response || new Response('', { status: 200, statusText: 'OK' });
      })
      .catch(() => {
        return caches.match(e.request).then((cached) => {
          return cached || new Response('', { status: 200, statusText: 'OK' });
        });
      })
  );
});
