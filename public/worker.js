self.addEventListener('install', event => {
    event.waitUntil(
        caches
            .open('mrpt')
            .then(cache =>
                cache.addAll([
                    '/',
                    '/favicon.png',
                    '/css/style.css',
                ])
            )
    )
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      if (response) {
        //we found an entry in the cache!
        return response
      }
      return fetch(event.request)
    })
  )
});

/*
self.addEventListener('fetch', event => {
    event.respondWith(fetch(event.request));
});
*/
