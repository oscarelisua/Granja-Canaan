// Service Worker para Granja de Rehabilitación Cristiana
// Permite funcionalidad offline y mejora la performance

const CACHE_NAME = 'granja-esperanza-v1.2';
const CRITICAL_CACHE = 'granja-critical-v1.2';
const RUNTIME_CACHE = 'granja-runtime-v1.2';

// Archivos críticos que siempre deben estar disponibles offline
const CRITICAL_ASSETS = [
    '/',
    '/index.html',
    '/styles.css',
    '/scripts.js',
    '/manifest.json',
    '/images/logo.png',
    '/images/hero-granja.jpg'
];

// Archivos menos críticos que se cachean bajo demanda
const RUNTIME_ASSETS = [
    '/images/',
    'https://fonts.googleapis.com/',
    'https://cdnjs.cloudflare.com/',
    'https://www.youtube.com/embed/',
    'https://maps.googleapis.com/'
];

// URLs que nunca deben cachearse (formularios, APIs dinámicas)
const NEVER_CACHE = [
    '/api/',
    'https://formspree.io/',
    'https://graph.instagram.com/',
    'chrome-extension://'
];

// Configuración de estrategias de cache
const CACHE_STRATEGIES = {
    // Para archivos críticos: Cache First (offline-first)
    critical: {
        cacheName: CRITICAL_CACHE,
        strategy: 'cache-first',
        maxAge: 7 * 24 * 60 * 60 * 1000, // 7 días
        maxEntries: 50
    },
    
    // Para imágenes: Cache First con fallback
    images: {
        cacheName: 'granja-images-v1',
        strategy: 'cache-first',
        maxAge: 30 * 24 * 60 * 60 * 1000, // 30 días
        maxEntries: 100
    },
    
    // Para APIs: Network First (datos frescos)
    api: {
        cacheName: 'granja-api-v1',
        strategy: 'network-first',
        maxAge: 5 * 60 * 1000, // 5 minutos
        maxEntries: 20
    },
    
    // Para contenido dinámico: Network First con cache como fallback
    dynamic: {
        cacheName: RUNTIME_CACHE,
        strategy: 'network-first',
        maxAge: 24 * 60 * 60 * 1000, // 24 horas
        maxEntries: 50
    }
};

/* ===================================
   EVENTOS DEL SERVICE WORKER
   =================================== */

// Instalación del Service Worker
self.addEventListener('install', (event) => {
    console.log('[SW] Instalando Service Worker...');
    
    event.waitUntil(
        (async () => {
            try {
                // Cache crítico
                const criticalCache = await caches.open(CRITICAL_CACHE);
                await criticalCache.addAll(CRITICAL_ASSETS);
                
                console.log('[SW] Archivos críticos cacheados correctamente');
                
                // Forzar activación inmediata
                self.skipWaiting();
                
            } catch (error) {
                console.error('[SW] Error durante la instalación:', error);
            }
        })()
    );
});

// Activación del Service Worker
self.addEventListener('activate', (event) => {
    console.log('[SW] Activando Service Worker...');
    
    event.waitUntil(
        (async () => {
            try {
                // Limpiar caches antiguos
                await cleanupOldCaches();
                
                // Tomar control de todas las pestañas
                await self.clients.claim();
                
                console.log('[SW] Service Worker activado correctamente');
                
            } catch (error) {
                console.error('[SW] Error durante la activación:', error);
            }
        })()
    );
});

// Interceptar todas las peticiones de red
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Ignorar peticiones no válidas
    if (!isValidRequest(request)) {
        return;
    }
    
    // Determinar estrategia según el tipo de recurso
    const strategy = determineStrategy(url, request);
    
    event.respondWith(
        handleRequest(request, strategy)
    );
});

// Manejar mensajes desde la aplicación principal
self.addEventListener('message', (event) => {
    const { type, payload } = event.data;
    
    switch (type) {
        case 'SKIP_WAITING':
            self.skipWaiting();
            break;
            
        case 'GET_VERSION':
            event.ports[0].postMessage({
                version: CACHE_NAME
            });
            break;
            
        case 'CLEAN_CACHE':
            cleanupCache(payload.cacheName);
            break;
            
        case 'PREFETCH_RESOURCES':
            prefetchResources(payload.urls);
            break;
            
        default:
            console.log('[SW] Mensaje no reconocido:', type);
    }
});

// Manejo de sincronización en background
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    
    switch (event.tag) {
        case 'form-submission':
            event.waitUntil(syncFormSubmissions());
            break;
            
        case 'analytics-events':
            event.waitUntil(syncAnalyticsEvents());
            break;
    }
});

// Notificaciones push (para futuras implementaciones)
self.addEventListener('push', (event) => {
    if (!event.data) return;
    
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/images/logo.png',
        badge: '/images/badge.png',
        tag: data.tag || 'general',
        requireInteraction: data.urgent || false,
        actions: [
            {
                action: 'view',
                title: 'Ver mensaje'
            },
            {
                action: 'dismiss',
                title: 'Cerrar'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

/* ===================================
   FUNCIONES AUXILIARES
   =================================== */

// Validar si la petición debe ser manejada por el SW
function isValidRequest(request) {
    const url = new URL(request.url);
    
    // Ignorar extensiones del navegador
    if (url.protocol === 'chrome-extension:' || url.protocol === 'moz-extension:') {
        return false;
    }
    
    // Ignorar métodos que no sean GET
    if (request.method !== 'GET') {
        return false;
    }
    
    // Ignorar URLs que nunca deben cachearse
    if (NEVER_CACHE.some(pattern => url.href.includes(pattern))) {
        return false;
    }
    
    return true;
}

// Determinar la estrategia de cache según el recurso
function determineStrategy(url, request) {
    const pathname = url.pathname;
    const hostname = url.hostname;
    
    // Archivos críticos del sitio
    if (CRITICAL_ASSETS.some(asset => pathname === asset || pathname.endsWith(asset))) {
        return CACHE_STRATEGIES.critical;
    }
    
    // Imágenes
    if (request.destination === 'image' || pathname.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i)) {
        return CACHE_STRATEGIES.images;
    }
    
    // APIs y contenido dinámico
    if (pathname.includes('/api/') || hostname.includes('api.')) {
        return CACHE_STRATEGIES.api;
    }
    
    // Recursos de terceros
    if (hostname.includes('googleapis.com') || 
        hostname.includes('cdnjs.cloudflare.com') ||
        hostname.includes('fonts.googleapis.com')) {
        return CACHE_STRATEGIES.images; // Cache agresivo para recursos externos
    }
    
    // Default: contenido dinámico
    return CACHE_STRATEGIES.dynamic;
}

// Manejar petición según la estrategia
async function handleRequest(request, strategy) {
    try {
        switch (strategy.strategy) {
            case 'cache-first':
                return await cacheFirst(request, strategy);
                
            case 'network-first':
                return await networkFirst(request, strategy);
                
            case 'network-only':
                return await fetch(request);
                
            default:
                return await networkFirst(request, strategy);
        }
    } catch (error) {
        console.error('[SW] Error manejando petición:', error);
        return await getOfflineFallback(request);
    }
}

// Estrategia Cache First
async function cacheFirst(request, strategy) {
    const cache = await caches.open(strategy.cacheName);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
        // Actualizar cache en background si está expirado
        if (isExpired(cachedResponse, strategy.maxAge)) {
            updateCacheInBackground(request, cache);
        }
        return cachedResponse;
    }
    
    // No está en cache, buscar en red
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Guardar en cache para la próxima vez
            await cache.put(request, networkResponse.clone());
            await maintainCacheSize(cache, strategy.maxEntries);
        }
        
        return networkResponse;
    } catch (error) {
        // Red no disponible, buscar fallback
        return await getOfflineFallback(request);
    }
}

// Estrategia Network First
async function networkFirst(request, strategy) {
    const cache = await caches.open(strategy.cacheName);
    
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Actualizar cache con respuesta fresca
            await cache.put(request, networkResponse.clone());
            await maintainCacheSize(cache, strategy.maxEntries);
        }
        
        return networkResponse;
        
    } catch (error) {
        // Red no disponible, buscar en cache
        const cachedResponse = await cache.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // No hay cache, usar fallback
        return await getOfflineFallback(request);
    }
}

// Verificar si una respuesta cacheada está expirada
function isExpired(response, maxAge) {
    const dateHeader = response.headers.get('date');
    if (!dateHeader) return true;
    
    const responseDate = new Date(dateHeader).getTime();
    const now = new Date().getTime();
    
    return (now - responseDate) > maxAge;
}

// Actualizar cache en background
async function updateCacheInBackground(request, cache) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            await cache.put(request, networkResponse);
        }
    } catch (error) {
        // Silencioso, es un update en background
    }
}

// Mantener tamaño del cache bajo control
async function maintainCacheSize(cache, maxEntries) {
    const keys = await cache.keys();
    
    if (keys.length > maxEntries) {
        // Eliminar las entradas más antiguas
        const entriesToDelete = keys.length - maxEntries;
        for (let i = 0; i < entriesToDelete; i++) {
            await cache.delete(keys[i]);
        }
    }
}

// Obtener respuesta de fallback offline
async function getOfflineFallback(request) {
    const url = new URL(request.url);
    
    // Para páginas HTML, mostrar página offline
    if (request.headers.get('accept')?.includes('text/html')) {
        const cache = await caches.open(CRITICAL_CACHE);
        const offlinePage = await cache.match('/') || await cache.match('/index.html');
        
        if (offlinePage) {
            return offlinePage;
        }
    }
    
    // Para imágenes, mostrar imagen placeholder
    if (request.destination === 'image') {
        return new Response(
            generateOfflineImageSVG(),
            {
                headers: {
                    'Content-Type': 'image/svg+xml',
                    'Cache-Control': 'no-cache'
                }
            }
        );
    }
    
    // Para otros recursos, respuesta 503
    return new Response(
        JSON.stringify({
            error: 'Contenido no disponible offline',
            message: 'Verifica tu conexión a internet'
        }),
        {
            status: 503,
            statusText: 'Service Unavailable',
            headers: {
                'Content-Type': 'application/json'
            }
        }
    );
}

// Generar imagen SVG placeholder para offline
function generateOfflineImageSVG() {
    return `
        <svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
            <rect width="400" height="300" fill="#f3f4f6"/>
            <text x="200" y="140" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#6b7280">
                Imagen no disponible
            </text>
            <text x="200" y="160" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#9ca3af">
                Verifica tu conexión
            </text>
        </svg>
    `;
}

// Limpiar caches antiguos
async function cleanupOldCaches() {
    const cacheNames = await caches.keys();
    const currentCaches = Object.values(CACHE_STRATEGIES).map(s => s.cacheName);
    currentCaches.push(CRITICAL_CACHE, RUNTIME_CACHE);
    
    const oldCaches = cacheNames.filter(name => !currentCaches.includes(name));
    
    await Promise.all(
        oldCaches.map(cacheName => {
            console.log('[SW] Eliminando cache antiguo:', cacheName);
            return caches.delete(cacheName);
        })
    );
}

// Limpiar cache específico
async function cleanupCache(cacheName) {
    if (await caches.has(cacheName)) {
        await caches.delete(cacheName);
        console.log('[SW] Cache eliminado:', cacheName);
    }
}

// Pre-cargar recursos específicos
async function prefetchResources(urls) {
    const cache = await caches.open(RUNTIME_CACHE);
    
    const prefetchPromises = urls.map(async (url) => {
        try {
            const response = await fetch(url);
            if (response.ok) {
                await cache.put(url, response);
            }
        } catch (error) {
            console.warn('[SW] Error prefetching:', url, error);
        }
    });
    
    await Promise.allSettled(prefetchPromises);
}

// Sincronizar envíos de formularios pendientes
async function syncFormSubmissions() {
    // Implementar lógica para reenviar formularios offline
    console.log('[SW] Sincronizando formularios...');
    
    // Por ahora solo logging, implementar según necesidades específicas
}

// Sincronizar eventos de analytics pendientes
async function syncAnalyticsEvents() {
    // Implementar lógica para enviar eventos de analytics offline
    console.log('[SW] Sincronizando analytics...');
    
    // Por ahora solo logging, implementar según necesidades específicas
}

/* ===================================
   UTILIDADES DE DEBUGGING
   =================================== */

// Log de estadísticas del cache
self.addEventListener('message', async (event) => {
    if (event.data.type === 'GET_CACHE_STATS') {
        const stats = await getCacheStats();
        event.ports[0].postMessage(stats);
    }
});

async function getCacheStats() {
    const cacheNames = await caches.keys();
    const stats = {};
    
    for (const cacheName of cacheNames) {
        const cache = await caches.open(cacheName);
        const keys = await cache.keys();
        stats[cacheName] = {
            entries: keys.length,
            urls: keys.map(req => req.url)
        };
    }
    
    return stats;
}

// Log de información del Service Worker
console.log('[SW] Service Worker cargado - Versión:', CACHE_NAME);
console.log('[SW] Estrategias de cache configuradas:', Object.keys(CACHE_STRATEGIES));
console.log('[SW] Archivos críticos:', CRITICAL_ASSETS.length);