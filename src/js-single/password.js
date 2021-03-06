$(function () {
    // Validator download from https://jqueryvalidation.org/
    $(document).ready(function () {
        $("#password-form").validate({
            rules: {

                currpass: {
                    required: true
                },
                newpass: {
                    required: true,
                    minlength: 8
                },
                repeatnewpass: {
                    required: true,
                    minlength: 8,
                    equalTo: "#newpass"
                },
            },
            messages: {
                currpass: {
                    required: "Please provide a password"
                },
                newpass: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 8 characters long"
                },
                repeatnewpass: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 8 characters long",
                    equalTo: "Your Passwords do not match"
                },
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                // Add the `invalid-feedback` class to the error element
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

    // when the form is submitted
    $('#password-form').on('submit', function (e) {

        // if the validator does not prevent form submit
        if (!e.isDefaultPrevented()) {
            var url = document.location.origin + "/include/workers/password.php";

            // POST values in the background the the script URL
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {

                    var messageAlert = 'alert-' + data.type;
                    var messageText = data.message;
                    var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';

                    if (messageAlert && messageText) {
                        $('#password-form').find('.messages').html(alertBox);
                        $('#password-form')[0].reset();
                    }
                }
            });
            return false;
        }
    })
});