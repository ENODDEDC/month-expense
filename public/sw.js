// Service Worker for Expense Tracker PWA
const CACHE_NAME = 'expense-tracker-v1.0.2';
const STATIC_CACHE = 'expense-tracker-static-v1.0.2';
const DYNAMIC_CACHE = 'expense-tracker-dynamic-v1.0.2';

// Files to cache for offline functionality
const STATIC_FILES = [
  '/manifest.json',
  '/dashboard',
  // Icons
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

// API endpoints that should work offline
const API_ENDPOINTS = [
  '/expenses',
  '/dashboard'
];

// Install event - cache static files
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => {
        console.log('Service Worker: Caching static files');
        return cache.addAll(STATIC_FILES);
      })
      .catch((error) => {
        console.log('Service Worker: Cache failed', error);
      })
  );
  
  // Force the waiting service worker to become the active service worker
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');
  
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
            console.log('Service Worker: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  
  // Take control of all pages immediately
  self.clients.claim();
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip service worker for authentication requests and external resources
  if (shouldSkipServiceWorker(request)) {
    return; // Let the browser handle these requests normally
  }
  
  // Handle API requests
  if (isApiRequest(request)) {
    event.respondWith(handleApiRequest(request));
    return;
  }
  
  // Handle dashboard and main page requests
  if (url.pathname === '/dashboard' || url.pathname === '/') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Cache successful dashboard responses
          if (response.ok) {
            const responseToCache = response.clone();
            caches.open(DYNAMIC_CACHE)
              .then((cache) => {
                cache.put(request, responseToCache);
              });
          }
          return response;
        })
        .catch(() => {
          // If network fails, serve cached dashboard
          return caches.match('/dashboard') || caches.match(request);
        })
    );
    return;
  }
  
  // Handle other static files and resources
  event.respondWith(
    caches.match(request)
      .then((cachedResponse) => {
        // Return cached version if available
        if (cachedResponse) {
          return cachedResponse;
        }
        
        // Fetch from network and cache dynamically
        return fetch(request)
          .then((response) => {
            // Don't cache non-successful responses
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }
            
            // Clone the response
            const responseToCache = response.clone();
            
            // Cache the response
            caches.open(DYNAMIC_CACHE)
              .then((cache) => {
                cache.put(request, responseToCache);
              });
            
            return response;
          })
          .catch(() => {
            // For non-critical resources, just fail gracefully
            return new Response('', { status: 404 });
          });
      })
  );
});

// Check if request is for API
function isApiRequest(request) {
  const url = new URL(request.url);
  return API_ENDPOINTS.some(endpoint => url.pathname.startsWith(endpoint)) ||
         request.headers.get('Content-Type') === 'application/json';
}

// Check if request is for authentication or external resources
function shouldSkipServiceWorker(request) {
  const url = new URL(request.url);
  
  // Skip external domains (like CDNs)
  if (url.origin !== self.location.origin) {
    return true;
  }
  
  // Skip authentication routes
  const authPaths = ['/login', '/register', '/logout', '/refresh-csrf'];
  if (authPaths.some(path => url.pathname === path)) {
    return true;
  }
  
  // Skip CSRF token requests
  if (url.pathname.includes('csrf') || url.pathname.includes('token')) {
    return true;
  }
  
  return false;
}

// Handle API requests with offline support
async function handleApiRequest(request) {
  const url = new URL(request.url);
  
  try {
    // Try network first
    const response = await fetch(request);
    
    // Cache successful GET requests
    if (request.method === 'GET' && response.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, response.clone());
    }
    
    // Handle POST requests (adding expenses) when online
    if (request.method === 'POST' && response.ok) {
      // Don't store successful POST data since it's already processed
      console.log('POST request successful');
    }
    
    return response;
  } catch (error) {
    console.log('API request failed, handling offline:', error);
    
    // Handle offline API requests
    if (request.method === 'GET') {
      // Try to serve from cache
      const cachedResponse = await caches.match(request);
      if (cachedResponse) {
        return cachedResponse;
      }
    }
    
    if (request.method === 'POST') {
      // Store POST data for later sync
      const data = await request.clone().json();
      await storeOfflineData('POST', url.pathname, data);
      
      // Return success response to keep UI working
      return new Response(JSON.stringify({
        id: Date.now(), // Temporary ID
        ...data,
        offline: true
      }), {
        status: 201,
        headers: { 'Content-Type': 'application/json' }
      });
    }
    
    // Return error for other methods
    return new Response(JSON.stringify({
      error: 'Offline - request will be synced when online'
    }), {
      status: 503,
      headers: { 'Content-Type': 'application/json' }
    });
  }
}

// Store data for offline sync
async function storeOfflineData(method, endpoint, data) {
  const offlineData = {
    id: Date.now(),
    method,
    endpoint,
    data,
    timestamp: new Date().toISOString()
  };
  
  // Get existing offline data
  const existingData = await getOfflineData();
  existingData.push(offlineData);
  
  // Store in IndexedDB or localStorage
  localStorage.setItem('expense_tracker_offline_data', JSON.stringify(existingData));
}

// Get offline data
async function getOfflineData() {
  const data = localStorage.getItem('expense_tracker_offline_data');
  return data ? JSON.parse(data) : [];
}

// Sync offline data when back online
self.addEventListener('online', async () => {
  console.log('Back online - syncing offline data');
  await syncOfflineData();
});

// Background sync for offline data
self.addEventListener('sync', (event) => {
  if (event.tag === 'expense-sync') {
    event.waitUntil(syncOfflineData());
  }
});

// Sync offline data with server
async function syncOfflineData() {
  const offlineData = await getOfflineData();
  
  if (offlineData.length === 0) return;
  
  console.log('Syncing', offlineData.length, 'offline items');
  
  const syncedItems = [];
  
  for (const item of offlineData) {
    try {
      const response = await fetch(item.endpoint, {
        method: item.method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(item.data)
      });
      
      if (response.ok) {
        syncedItems.push(item.id);
        console.log('Synced offline item:', item.id);
      }
    } catch (error) {
      console.log('Failed to sync item:', item.id, error);
    }
  }
  
  // Remove synced items
  if (syncedItems.length > 0) {
    const remainingData = offlineData.filter(item => !syncedItems.includes(item.id));
    localStorage.setItem('expense_tracker_offline_data', JSON.stringify(remainingData));
    
    // Notify the main thread about successful sync
    self.clients.matchAll().then(clients => {
      clients.forEach(client => {
        client.postMessage({
          type: 'SYNC_COMPLETE',
          syncedCount: syncedItems.length
        });
      });
    });
  }
}

// Create offline response
function createOfflineResponse() {
  return new Response(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Offline - Expense Tracker</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f3f4f6; }
        .offline-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .offline-icon { font-size: 64px; margin-bottom: 20px; }
        h1 { color: #374151; margin-bottom: 10px; }
        p { color: #6b7280; margin-bottom: 20px; }
        .retry-btn { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .retry-btn:hover { background: #2563eb; }
      </style>
    </head>
    <body>
      <div class="offline-container">
        <div class="offline-icon">📱</div>
        <h1>You're Offline</h1>
        <p>Don't worry! Your expense tracker still works offline. Any changes you make will be synced when you're back online.</p>
        <button class="retry-btn" onclick="window.location.reload()">Try Again</button>
      </div>
    </body>
    </html>
  `, {
    headers: { 'Content-Type': 'text/html' }
  });
}

// Push notification handling
self.addEventListener('push', (event) => {
  if (!event.data) return;
  
  const data = event.data.json();
  const options = {
    body: data.body || 'New notification from Expense Tracker',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    vibrate: [200, 100, 200],
    data: data.data || {},
    actions: [
      {
        action: 'view',
        title: 'View',
        icon: '/icons/view.png'
      },
      {
        action: 'dismiss',
        title: 'Dismiss',
        icon: '/icons/dismiss.png'
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title || 'Expense Tracker', options)
  );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  
  if (event.action === 'view') {
    event.waitUntil(
      clients.openWindow('/dashboard')
    );
  }
});