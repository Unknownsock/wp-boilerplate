// Scoped JS for the map block - renders a Leaflet map (OpenStreetMap tiles,
// no API key) pinned to the lat/lng the PHP template already geocoded.
//
// Also pulls in this block's own stylesheet (in addition to Leaflet's) -
// both land in this entry's manifest `css` array and are now enqueued
// together by enqueue_block_assets() in includes/asset-management.php.
// Leaflet's CSS was already being imported here but had no way to reach
// the page before that change, so its default controls/markers were
// likely rendering unstyled - worth a visual check after deploying.
import './_map.scss';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Leaflet's default marker icon references image URLs relative to its own
// CSS file, which doesn't survive Vite's bundling - point it at the
// bundled asset URLs explicitly instead.
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

function initMap(el) {
    const lat = parseFloat(el.dataset.lat);
    const lng = parseFloat(el.dataset.lng);
    const address = el.dataset.address || '';

    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

    const map = L.map(el, {
        center: [lat, lng],
        zoom: 16,
        scrollWheelZoom: false,
    });

    // CARTO's "Positron" basemap instead of the standard OSM raster tiles -
    // same underlying OpenStreetMap data, but a light, low-saturation,
    // minimal-label style closer to Google Maps' default look than the
    // busier, more saturated stock OSM tiles. Free, no API key/signup.
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    L.marker([lat, lng]).addTo(map).bindPopup(address);
}

document.querySelectorAll('.b-map__embed').forEach(initMap);
