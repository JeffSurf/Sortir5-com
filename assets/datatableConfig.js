import DataTable from 'datatables.net-dt';

const $ = require("jquery");
let table = new DataTable('#participant');

$(document).ready( function () {
    $('#participant').DataTable();
} );

table.on('click', 'tbody tr', function (e) {
    e.currentTarget.classList.toggle('selected');
});

if ( table.rows( '.selected' ).any()) {
    alert('attention');
    $('#delete-button').removeClass('nodisplay');
    $('#disable-button').removeClass('nodisplay');
} else {
    $('#delete-button').addClass('nodisplay');
    $('#disable-button').addClass('nodisplay');
    console.log('test');
}

document.querySelector('#button').addEventListener('click', function () {
    table.row('.selected').remove().draw(false);
    alert(table.rows('.selected').data().length + ' row(s) selected');
});