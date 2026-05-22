import L from "leaflet";
import "leaflet/dist/leaflet.css";

function initMap(selector) {
    const mapDiv = document.querySelector(selector);
    if (!mapDiv || mapDiv.offsetWidth === 0) return;

    mapDiv.style.position = 'relative';

    const lat = parseFloat(mapDiv.dataset.lat);
    const lng = parseFloat(mapDiv.dataset.lng);
    const iconUrl = mapDiv.dataset.icon;

    const map = L.map(mapDiv).setView([lat, lng], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const customIcon = L.icon({
        iconUrl: iconUrl,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    L.marker([lat, lng], { icon: customIcon }).addTo(map);

    const fullscreenBtn = L.control({ position: 'topright' });
    fullscreenBtn.onAdd = function () {
        const btn = L.DomUtil.create('button', '');
        btn.innerHTML = '⛶';
        btn.title = 'Plein écran';
        btn.style.cssText = `
            background: white; border: 1px solid #ccc; border-radius: 6px;
            width: 34px; height: 34px; font-size: 18px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 1px 5px rgba(0,0,0,0.2); line-height: 1;
        `;
        L.DomEvent.disableClickPropagation(btn);
        btn.addEventListener('click', () => {
            const isFullscreen = mapDiv.classList.toggle('map-is-fullscreen');
            btn.innerHTML = isFullscreen ? '✕' : '⛶';
            map.invalidateSize();
        });
        return btn;
    };
    fullscreenBtn.addTo(map);
}

initMap(".cactus-map-show-desktop");
initMap(".cactus-map-show-mobile");
