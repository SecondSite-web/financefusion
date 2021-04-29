$(function () {
    // Validator download from https://jqueryvalidation.org/
    $(document).ready(function () {
        $("#add-user-form").validate({
            rules: {
                firstname: "required",
                lastname: "required",
                phone: "required",
                user_role: "required",
                user_group: "required",
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                firstname: "Please enter your firstname",
                lastname: "Please enter your lastname",
                phone: "Required",
                user_role: "Required",
                user_group: "Required",
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
        $('#add-user-form').on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                var url = document.location.origin + "/include/workers/add-user.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    success: function (data) {
                        var messageAlert = 'alert-' + data.type;
                        var messageText = data.message;
                        var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                        if (messageAlert && messageText) {
                            $('#add-user-form').find('.messages').html(alertBox);
                            $('#add-user-form')[0].reset();
                        }
                    }
                });
                return false;
            }
        })
    });
});