const $ = require("jquery");
const {msg} = require("@babel/core/lib/config/validation/option-assertions");

const handleCsvMessage = (data, type = "success") => {
    const bodyHtml = $("body");
    const idMsgCsv = "#msg-csv";
    const msgCsv = !$(idMsgCsv).length ? $("<div id='msg-csv'/>") : $(idMsgCsv);

    msgCsv.attr("class", `alert alert-${type}` );
    msgCsv.empty();
    msgCsv.append(data);

    bodyHtml.prepend(msgCsv);
}

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
            $.post("http://localhost:8000/admin/participant/ajouter/csv", content, (data) => handleCsvMessage(data))
                .fail((err) => handleCsvMessage(err?.responseText, "danger"));
        };
        reader.readAsBinaryString(file);
    }
})