import DataTable from 'datatables.net-dt';

const $ = require("jquery");
let table = new DataTable('#participant');

$(document).ready( function () {
    $('#participant').DataTable();
} );

table.on('click', 'tbody tr', function (e) {
    e.currentTarget.classList.toggle('selec');
});

if (table.rows('.selec').data().length > 1) {
    $('#delete-button').removeClass('nodisplay');
    $('#disable-button').removeClass('nodisplay');
} else {
    $('#delete-button').addClass('nodisplay');
    $('#disable-button').addClass('nodisplay');
    console.log('test');
}

document.querySelector('#button').addEventListener('click', function () {

    alert(table.rows('.selec').data().length + ' row(s) selected');
});