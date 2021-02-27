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
            });
    }
}
else {
    console.log("[PWA Builder] Navigator not serviceWorker compatible");
    console.log(navigator);
}
