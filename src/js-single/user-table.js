$(document).ready(function () {
    $('#dataTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'print'
        ]
    });
});
$('.status').change(userStatus);
function userStatus(e)
{
    e.preventDefault();
    var id=$(this).parent("form").attr('id');
    $.ajax({
        type: 'POST',
        url: document.location.origin + '/include/workers/change-userstatus.php',
        data: $('#'+ id).serialize(),
        success: function (data) {
            window.location.replace("/dashboard/users-table/");
        }
    });
}
$('.userRoleSelect').change(userRole);
function userRole(e)
{
    e.preventDefault();
    var id=$(this).parent("form").attr('id');
    $.ajax({
        type: 'POST',
        url: document.location.origin + '/include/workers/change-userrole.php',
        data: $('#'+ id).serialize(),
        success: function (data) {
            window.location.replace("/dashboard/users-table/");
        }
    });
}
$('.userGroupSelect').change(userGroup);
function userGroup(e)
{
    e.preventDefault();
    var id=$(this).parent("form").attr('id');
    $.ajax({
        type: 'POST',
        url: document.location.origin + '/include/workers/change-usergroup.php',
        data: $('#'+ id).serialize(),
    });
}
$(document).ready(function () {
    $('.user-edit').on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            var base = document.location.origin;
            var url = document.location.origin + "/include/workers/user-edit.php";
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {
                    var messageAlert = 'alert-' + data.type;
                    var messageText = data.message;
                    var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                    if (messageAlert && messageText) {
                        $('.user-edit').find('.messages').html(alertBox);
                        $('.user-edit')[0].reset();
                    }
                    if (data.type === 'success') {
                        window.location.replace(base + "/dashboard/users-table.php");
                    }
                }
            });
            return false;
        }
    })
})