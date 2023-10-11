const $ = require("jquery");
$("#btn-csv").on('click', () => {
    $("#input-csv").click();
})

$("#input-csv").on("change", (e) => {
    const file = e.currentTarget.files[0];

    if(file)
    {
        const reader = new FileReader();
        reader.onload = e => {
            const content = e.target.result;
            $.post("http://localhost:8000/participant/ajouter/csv", content);
        };
        reader.readAsBinaryString(file);
    }
})