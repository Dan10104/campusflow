import { CacheableResponsePlugin } from 'workbox-cacheable-response';
import { clientsClaim } from 'workbox-core';
import { ExpirationPlugin } from 'workbox-expiration';
import { cleanupOutdatedCaches, matchPrecache, precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst } from 'workbox-strategies';

const privatePathPrefixes = [
    '/api',
    '/api/blockchain',
    '/blockchain',
    '/login',
    '/logout',
    '/register',
    '/politicas',
    '/sanctum/csrf-cookie',
];

const privateExactPaths = [
    '/terminos-y-condiciones',
    '/politica-de-privacidad',
];

const isPrivatePath = (url) => {
    if (url.origin !== self.location.origin) {
        return false;
    }

    return privateExactPaths.includes(url.pathname)
        || privatePathPrefixes.some((path) => (
            url.pathname === path || url.pathname.startsWith(`${path}/`)
        ));
};

self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        event.waitUntil(Promise.resolve());
        return;
    }
});

clientsClaim();
precacheAndRoute(self.__WB_MANIFEST);
cleanupOutdatedCaches();

registerRoute(
    ({ request, url }) => request.method === 'GET'
        && request.mode === 'navigate'
        && url.origin === self.location.origin
        && !isPrivatePath(url),
    async ({ event }) => {
        try {
            return await fetch(event.request);
        } catch {
            return await matchPrecache('/offline.html')
                || new Response('CampusFlow requiere conexión para esta operación.', {
                    headers: { 'Content-Type': 'text/plain; charset=utf-8' },
                    status: 503,
                });
        }
    },
);

registerRoute(
    ({ request, url }) => {
        if (request.method !== 'GET') {
            return false;
        }

        if (url.origin !== self.location.origin) {
            return false;
        }

        if (isPrivatePath(url)) {
            return false;
        }

        if (!['script', 'style', 'font', 'image'].includes(request.destination)) {
            return false;
        }

        return url.pathname.startsWith('/build/assets/')
            || url.pathname.startsWith('/icons/')
            || url.pathname.startsWith('/assets/');
    },
    new CacheFirst({
        cacheName: 'campusflow-static-assets',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 90,
                maxAgeSeconds: 60 * 60 * 24 * 30,
            }),
            new CacheableResponsePlugin({
                statuses: [0, 200],
            }),
        ],
    }),
);
