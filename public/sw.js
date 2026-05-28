// public/sw.js
const CACHE_NAME = 'invenCoo-v1';

// 1. Se instala inmediatamente
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// 2. Toma el control
self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// 3. El truco: Cuando la app pide datos, el worker NO hace nada.
// Todo pasa directo a internet (tu servidor). Cero modo offline.
self.addEventListener('fetch', (event) => {
    return;
});
