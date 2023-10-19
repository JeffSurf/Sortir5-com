import './styles/map-show.scss';

const $ = require("jquery");
const L = require("leaflet");

const selectorMap = "#map";
const {lat, lon} = $(selectorMap).data();

if(lat && lon)
{
    const map = L.map('map').setView([lat, lon], 16);
    const marker = L.marker();

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    marker.setLatLng([lat, lon]).addTo(map);
}
else
{
    $("<span>Aucune information renseignée</span>").prependTo(selectorMap);
    $("<i class=\"fa-solid fa-map-location-dot\"></i>").prependTo(selectorMap);
    $(selectorMap).addClass('map-false')
}
