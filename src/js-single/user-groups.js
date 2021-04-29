$(function () {
    // Validator download from https://jqueryvalidation.org/
    $('#dataTable').DataTable();
    $(document).ready(function () {
        $("#add-group-form").validate({
            rules: {
                groupName: "required",
            },
            messages: {
                groupName: "Please enter your firstname",
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                error.addClass("invalid-feedback");

                if ( element.prop("type") === "checkbox" ) {
                    error.insertAfter(element.next("label"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function ( element, errorClass, validClass ) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            }
        });
        $('#add-group-form').on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                var url = document.location.origin + "/include/workers/add-group.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    success: function (data) {
                        var messageAlert = 'alert-' + data.type;
                        var messageText = data.message;
                        var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                        if (messageAlert && messageText) {
                            $('#add-group-form').find('.messages').html(alertBox);
                            $('#add-group-form')[0].reset();
                        }
                        if (data.type === 'success') {
                            window.location.replace("/dashboard/user-groups.php");
                        }
                    }
                });
                return false;
            }
        })
    });
    $('.delete-group-forms').on('submit', function (e) {

        // if the validator does not prevent form submit
        if (!e.isDefaultPrevented()) {
            var url = document.location.origin + "/include/workers/delete-group.php";

            // POST values in the background the the script URL
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {
                    if (data.type === 'success') {
                        window.location.replace("/dashboard/user-groups.php");
                    }
                }
            });
            return false;
        }
    })
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
                            window.location.replace(base + "/dashboard/user-groups.php");
                        }
                    }
                });
                return false;
            }
        })
    })
});


