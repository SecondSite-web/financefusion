$(function () {
    // Validator download from https://jqueryvalidation.org/
    $(document).ready(function () {
        $("#account-update-form").validate({
            rules: {
                firstname: "required",
                lastname: "required",
                phone: "required",
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                firstname: "Please enter your firstname",
                lastname: "Please enter your lastname",
                phone: "Please enter a valid phone number",
                email: "Please enter a valid email address"
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
    });
    $('#account-update-form').on('submit', function (e) {

        if (!e.isDefaultPrevented()) {
            var url = document.location.origin + "/include/workers/account-update.php";

            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {

                    var messageAlert = 'alert-' + data.type;
                    var messageText = data.message;
                    var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                    if (messageAlert && messageText) {
                        $('#account-update-form').find('.messages').html(alertBox);
                        $('#account-update-form')[0].reset();
                    }

                }
            });
            return false;
        }
    })
});