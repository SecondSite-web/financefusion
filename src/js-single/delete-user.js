$(function () {
    
    // when the form is submitted
    $('#force-delete-form').on('submit', function (e) {

        // if the validator does not prevent form submit
        if (!e.isDefaultPrevented()) {
            var url = document.location.origin + "/include/workers/delete-user.php";

            // POST values in the background the the script URL
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {
                    if (data.type === 'success') {
                        window.location.replace("/dashboard/users-table.php");
                    }
                }
            });
            return false;
        }
    })
});

