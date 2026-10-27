import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIconRetina from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { serviceLocations } from './service-locations';

function initServiceMap() {
    const container = document.getElementById('mapContainer');
    const selector = document.getElementById('serviceSelect');
    const status = document.getElementById('serviceMapStatus');
    if (!container || !selector || container.dataset.mapInitialized) return;

    const center = [14.2247, 120.9768];
    const map = L.map(container, { scrollWheelZoom: false }).setView(center, 13);
    container.dataset.mapInitialized = 'true';

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    const icon = L.icon({
        iconUrl: markerIcon,
        iconRetinaUrl: markerIconRetina,
        shadowUrl: markerShadow,
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41],
    });
    const markers = L.featureGroup().addTo(map);

    function updateLocations() {
        markers.clearLayers();
        const locations = serviceLocations[selector.value] ?? [];

        locations.forEach(location => {
            const popup = document.createElement('div');
            const title = document.createElement('strong');
            const address = document.createElement('p');
            title.textContent = location.name;
            address.textContent = location.address;
            popup.append(title, address);

            if (location.phone && location.phone !== 'N/A') {
                const phone = document.createElement('a');
                phone.href = `tel:${location.phone.replace(/[^\d+]/g, '')}`;
                phone.textContent = location.phone;
                popup.append(phone);
            }

            L.marker(location.coords, { icon }).bindPopup(popup).addTo(markers);
        });

        if (locations.length) {
            map.fitBounds(markers.getBounds(), { padding: [30, 30], maxZoom: 15 });
            status.textContent = `${locations.length} ${locations.length === 1 ? 'location' : 'locations'} shown. Select a marker for details.`;
        } else {
            map.setView(center, 13);
            status.textContent = 'Choose a service to show its locations on the map.';
        }
    }

    selector.addEventListener('change', updateLocations);
    updateLocations();

    // Keep tiles aligned when the contact layout changes on rotation or resize.
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(() => map.invalidateSize({ pan: false })).observe(container);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initServiceMap);
} else {
    initServiceMap();
}
