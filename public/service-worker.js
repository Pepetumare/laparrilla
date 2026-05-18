const CACHE_NAME = 'la-parrilla-v1';

const urlsToCache = [
    '/',
    '/login',
    '/manifest.json',
    '/images/logo-la-parrilla.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});