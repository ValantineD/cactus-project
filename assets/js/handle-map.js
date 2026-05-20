import L from "leaflet";
import "leaflet/dist/leaflet.css";

function initMap(selector) {
    const mapDiv = document.querySelector(selector);
    if (!mapDiv || mapDiv.offsetWidth === 0) return;

    const map = L.map(mapDiv).setView([43.2965, 5.3698], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const activities = JSON.parse(mapDiv.dataset.activities || '[]');

    activities.forEach(activity => {
        const icon = L.icon({
            iconUrl: activity.icon,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        L.marker([activity.lat, activity.lng], { icon })
            .addTo(map)
            .bindPopup(activity.title);
    });
}

initMap(".cactus-map-desktop");
initMap(".cactus-map-mobile");
