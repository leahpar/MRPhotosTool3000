// PWA
// https://www.pwabuilder.com
// This is the "Offline page" service worker

console.log("Hi! I'm MRPT!");

if ("serviceWorker" in navigator) {
    if (navigator.serviceWorker.controller) {
        console.log("[PWA Builder] active service worker found, no need to register");
    } else {
        // Register the service worker
        navigator.serviceWorker
            .register("/pwabuilder-sw.js", {
                scope: "/"
            })
            .then(function (reg) {
                console.log("[PWA Builder] Service worker has been registered for scope: " + reg.scope);
//                    reg.periodicSync.register('content-sync', {
//                        // An interval of one day.
//                        minInterval: 5 * 60 * 1000,
//                    });
//                    console.log("register('content-sync')");
                }
            );

//        self.addEventListener('periodicsync', (event) => {
//            if (event.tag === 'content-sync') {
//                console.log("event('content-sync')");
//                // See the "Think before you sync" section for
//                // checks you could perform before syncing.
//                event.waitUntil(
//                    navigator.setAppBadge(Math.floor(Math.random()*100))
//                );
//            }
//        });

    }
}
else {
    console.log("[PWA Builder] Navigator not serviceWorker compatible");
    console.log(navigator);
}
