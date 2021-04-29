$(document).ready(function () {
    $('#dataTable').DataTable({
        "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'print'
        ]
    });
});
$('select').change(processForm);
function processForm(e)
{
    e.preventDefault();
    var id=$(this).parent("form").attr('id');
    $.ajax({
        type: 'POST',
        url: document.location.origin + '/include/workers/contact-form-status.php',
        data: $('#'+ id).serialize(),
        success: function (data) {

        }
    });
}