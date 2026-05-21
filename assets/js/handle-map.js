import L from "leaflet";
import "leaflet/dist/leaflet.css";

function makeIcon(iconUrl, selected = false) {
    if (selected) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width: 44px; height: 44px;
                background: var(--tertiary-color);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            ">
                <img src="${iconUrl}" style="width:24px;height:24px;filter:brightness(0) invert(1);" />
            </div>`,
            iconSize: [44, 44],
            iconAnchor: [22, 44],
            popupAnchor: [0, -44]
        });
    }
    return L.icon({
        iconUrl,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });
}

function initMap(selector) {
    const mapDiv = document.querySelector(selector);
    if (!mapDiv || mapDiv.offsetWidth === 0) return;

    mapDiv.style.position = 'relative';
    mapDiv.style.overflow = 'hidden';

    const map = L.map(mapDiv).setView([43.2965, 5.3698], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

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

    const allCards = mapDiv.querySelectorAll('.map-popup-card');
    let activeCard = null;
    let activeMarker = null;

    function showCard(card) {
        if (activeCard && activeCard !== card) hideCard();
        card.style.display = 'block';
        card.style.transition = 'none';
        card.style.bottom = '-160px';
        card.style.opacity = '0';
        requestAnimationFrame(() => {
            card.style.transition = 'bottom 0.3s cubic-bezier(.4,0,.2,1), opacity 0.3s ease';
            card.style.bottom = '16px';
            card.style.opacity = '1';
        });
        activeCard = card;
    }

    function hideCard() {
        if (!activeCard) return;
        const card = activeCard;
        card.style.bottom = '-160px';
        card.style.opacity = '0';
        setTimeout(() => { card.style.display = 'none'; }, 300);
        activeCard = null;

        if (activeMarker) {
            activeMarker.marker.setIcon(makeIcon(activeMarker.icon));
            activeMarker = null;
        }
    }

    map.on('click', hideCard);

    const activities = JSON.parse(mapDiv.dataset.activities || '[]');

    activities.forEach(activity => {
        const marker = L.marker([activity.lat, activity.lng], {
            icon: makeIcon(activity.icon)
        }).addTo(map);

        const card = mapDiv.querySelector(
            `.map-popup-card[data-lat="${activity.lat}"][data-lng="${activity.lng}"]`
        );

        if (card) {
            marker.on('click', (e) => {
                L.DomEvent.stopPropagation(e);
                if (activeCard === card) {
                    marker.setIcon(makeIcon(activity.icon));
                    activeMarker = null;
                    hideCard();
                } else {
                    if (activeMarker) activeMarker.marker.setIcon(makeIcon(activeMarker.icon));
                    marker.setIcon(makeIcon(activity.icon, true));
                    activeMarker = { marker, icon: activity.icon };
                    showCard(card);
                }
            });
        }
    });

    const localisation = mapDiv.dataset.localisation;
    if (localisation) {
        fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(localisation)}&limit=1`,
            { headers: { 'Accept-Language': 'fr' } }
        )
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    map.flyTo([parseFloat(data[0].lat), parseFloat(data[0].lon)], 13, {
                        animate: true,
                        duration: 1.2
                    });
                }
            })
            .catch(err => console.error('Geocoding error:', err));
    }
}

initMap(".cactus-map-desktop");
initMap(".cactus-map-mobile");
