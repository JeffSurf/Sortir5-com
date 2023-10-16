import './styles/map-show.scss';

const $ = require("jquery");
const L = require("leaflet");

const {lat, lon} = $("#map").data();

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
    $("#map").text("Pas de coordonnées fournies");
}
