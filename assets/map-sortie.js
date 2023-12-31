import './styles/map-sortie.scss';

const BASE_URL_GEOCODE = "https://geocode.maps.co";
const L = require("leaflet");
const $ = require("jquery");

const map = L.map('map').setView([46.323716, -0.464777], 6);
const marker = L.marker();

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

const nom_input = $("#lieu_nom");
const adresse_input = $("#lieu_adresse");
const ville_input = $("#lieu_ville");
const lieu_lat = $("#lieu_latitude");
const lieu_lng = $("#lieu_longitude");

const handleChangeGeocode = () => {
    const nom = nom_input.val();
    const adresse = adresse_input.val()
    const ville = ville_input.find(`option[value=${ville_input.val()}]`).text();

    if(nom && ville)
    {
        const q = `${nom} ${adresse} ${ville}`;
        $.get(BASE_URL_GEOCODE + "/search", { q }, (data) => {

            const msg = $("#msg-map");
            msg.empty();

            const resulats = data.length;

            if(resulats > 0)
            {
                const point = data[0];

                lieu_lat.val(Math.round(point.lat * 10000)/10000);
                lieu_lng.val(Math.round(point.lon * 10000) /10000);

                marker.setLatLng([point.lat, point.lon]).addTo(map);
                map.setView([point.lat, point.lon], 16)

                const selectResults = $("<select/>");

            }
            else
            {
                msg.append("Pas de résultat");
            }

        });
    }
}

nom_input.on('change', handleChangeGeocode);
adresse_input.on('change', handleChangeGeocode);
ville_input.on('change', handleChangeGeocode);

//Permet d'afficher la map correctement
$("#show-lieu").on('click', () => setTimeout(() => map.invalidateSize(), 200));

const handleClickLieu = () => {
    const bodyReq = {
        [nom_input.attr("name")]: nom_input.val(),
        [adresse_input.attr("name")]: adresse_input.val(),
        [ville_input.attr("name")]: ville_input.val(),
        [lieu_lat.attr("name")]: lieu_lat.val(),
        [lieu_lng.attr("name")]: lieu_lng.val(),
    }
    $.post("/lieu", bodyReq, (data) => {
        const addedLieu = JSON.parse(data);

        $("#ville").val(ville_input.val()).change();

        const selectorLieuListe = "#liste_lieu";
        $(selectorLieuListe).append($(`<option value="${addedLieu.id}">${addedLieu.nom}</option>`));

        setTimeout(() => $(selectorLieuListe).val(addedLieu.id).change(), 800);

        $(`.modal-body [name]`).removeClass("is-invalid");

        $("#btn-close-modal").trigger("click");
    })
        .fail((err) => {
            const errors = err.responseJSON;
            for(const key in errors) {
                const selectorInput = "#lieu_" + key;
                $(selectorInput).addClass("is-invalid");
                $(selectorInput).next().text(errors[key]);
            }
        })
}

$("#btn-add-lieu").on("click", handleClickLieu);