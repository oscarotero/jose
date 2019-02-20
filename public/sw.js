const version = 'v1';

self.addEventListener('fetch', function(event) {
    if (event.request.method !== 'GET') {
        return;
    }

    const destination = event.request.destination;

    switch (destination) {
        case 'style':
        case 'script':
            event.respondWith(assets(event.request));
            return;

        default:
            event.respondWith(pages(event.request));
            return;
    }
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(key => !key.startsWith(version))
                    .map(key => caches.delete(key))
            );
        })
    );
});

//Assets - cache first strategy
function assets(request) {
    return caches.match(request).then(cached => {
        if (cached !== undefined) {
            return cached;
        }

        return fetch(request)
            .catch(errorResponse)
            .then(saveCache(request, 'assets'));
    });
}

//Html pages - network first strategy
function pages(request) {
    return fetch(request)
        .then(saveCache(request, 'pages'))
        .catch(() =>
            caches.match(request).then(cached => {
                if (cached !== undefined) {
                    return cached;
                }

                new Response('No internet :(', {
                    status: 503,
                    statusText: 'Service Unavailable'
                })
            })
        );
}

function saveCache(request, type) {
    return function(response) {
        const clonedResponse = response.clone();

        caches.open(`${version}-${type}`).then(function(cache) {
            cache.put(request, clonedResponse);
        });

        return response;
    };
}
