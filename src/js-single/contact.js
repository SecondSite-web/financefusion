$(document).ready(function () {
    $("#contact-form").validate({
        rules: {
            firstname: "required",
            lastname: "required",
            email: {
                required: true,
                email: true
            },
            telephone:{
                required: false
            },
            message: "required"
        },
        messages: {
            firstname: "Please enter your firstname",
            lastname: "Please enter your lastname",
            email: "Please enter a valid email address",
            telephone: "Please leave us a telephone number",
            message: "Please leave us a short message"
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

    $('#contact-form').on('submit', function (e) {

        // if the validator does not prevent form submit
        if (!e.isDefaultPrevented()) {
            var url = document.location.origin + "/include/workers/contact-form.php";

            // POST values in the background the the script URL
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {
                    $('#cf-wrapper').addClass('gone');
                    $('#success-wrapper').removeClass('gone');
                    $('#contact-form')[0].reset();
                }
            });
            return false;
        }
    });
});
