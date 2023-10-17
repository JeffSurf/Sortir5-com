const $ = require("jquery");
const selectorVille = "#ville";
const selectorLieu = '#liste_lieu';

function getLieuParVille() {
    const id = $(selectorVille).val();

    if(!id) {
        $(selectorLieu).empty();
        return;
    }

    $.get(`/ville/${id}/lieu`, response => {
        const lieuSelect = $(selectorLieu);
        lieuSelect.empty();

        const optionDisabled =$(`<option selected disabled>Choisir un lieu</option>`);
        lieuSelect.append(optionDisabled);

        response.forEach(lieu => {
            const option = $("<option/>");
            option.attr("value", lieu.id);
            option.text(lieu.nom);
            lieuSelect.append(option);
        });
    });
}

$(selectorVille).on("change", getLieuParVille);

$(document).ready(() => $(selectorVille).trigger("change"));