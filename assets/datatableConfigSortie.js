import DataTable from 'datatables.net-dt';

const $ = require("jquery");
let table = new DataTable('#sortie', {
    searching: false
});

$(document).ready( function () {
    $('#sortie').DataTable();
} );