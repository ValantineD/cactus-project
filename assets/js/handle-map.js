import L from "leaflet";
import "leaflet/dist/leaflet.css";

const mapDiv = document.querySelector(".cactus-map-desktop");

const map = L.map(mapDiv).setView([43.2965, 5.3698], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
