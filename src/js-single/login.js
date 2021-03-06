$(function () {
    // Validator download from https://jqueryvalidation.org/ //
    $(document).ready(function () {
        $("#login-form").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 8
                },
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                password: {
                    required: "Please provide a password"
                },
                email: "Please enter a valid email address"
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
        $('#login-form').on('submit', function (e) {

            if (!e.isDefaultPrevented()) {
                var url = document.location.origin + "/include/workers/login.php";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    success: function (data) {

                        if (data.type === 'danger') {
                            var messageAlert = 'alert-' + data.type;
                            var messageText = data.message;
                            var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';

                            if (messageAlert && messageText) {
                                $('#login-form').find('.messages').html(alertBox);
                                $('#login-form')[0].reset();
                            }
                        }
                        if (data.type === 'success') {
                            window.location.replace("/dashboard/");
                        }
                    }
                });
                return false;
            }
        })

    });

});

