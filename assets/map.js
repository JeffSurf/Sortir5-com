import './styles/map.scss';

const BASE_URL_GEOCODE = "https://geocode.maps.co/search";

const L = require("leaflet");
const $ = require("jquery");

const map = L.map('map').setView([46.323716, -0.464777], 6);
const marker = L.marker();

const nom_input = $("#lieu_nom");
const adresse_input = $("#lieu_adresse");
const ville_input = $("#lieu_ville");

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);


function handleChangeGeocode() {
    const nom = nom_input.val();
    const adresse = adresse_input.val()
    const ville = ville_input.find(`option[value=${ville_input.val()}]`).text();

    console.log(nom, adresse, ville);

    if(nom && ville)
    {
        const q = `${nom} ${adresse} ${ville}`;
        $.get(BASE_URL_GEOCODE, { q }, (data) => {

            const resulats = data.length;

            if(resulats > 0)
            {
                const point = data[0];

                const lieu_lat = $("#lieu_latitude");
                const lieu_lng = $("#lieu_longitude");

                lieu_lat.val(Math.round(point.lat * 10000)/10000);
                lieu_lng.val(Math.round(point.lon * 10000) /10000);

                marker.setLatLng([point.lat, point.lon]).addTo(map);
                map.setView([point.lat, point.lon], 16)
            }

            const msg = $("#msg-map");
            msg.empty();
            msg.append(resulats ? `${resulats} résultat(s)` : "Aucun résultat");
        });
    }
}

nom_input.on('change', handleChangeGeocode);
adresse_input.on('change', handleChangeGeocode);
ville_input.on('change', handleChangeGeocode);
