import L from "leaflet";
import "leaflet/dist/leaflet.css";

function initMap(selector) {
    const mapDiv = document.querySelector(selector);
    if (!mapDiv || mapDiv.offsetWidth === 0) return;

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
}

initMap(".cactus-map-show-desktop");
initMap(".cactus-map-show-mobile");
