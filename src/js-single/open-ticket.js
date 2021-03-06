$(function () {
    // Validator download from https://jqueryvalidation.org/
    $(document).ready(function () {
        $("#open-ticket-form").validate({
            rules: {
                dest_group: "required",
                subject: "required",
                message: "required",
                priority: "required",
                status: "required"
            },
            messages: {
                dest_group: "Please select the user group",
                subject: "Please state the subject for your ticket",
                message: "Please leave a detailed message",
                priority: "Required",
                status: "Required"
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
        $('#open-ticket-form').on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                var url = document.location.origin + "/include/workers/open-ticket.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    success: function (data) {
                        var messageAlert = 'alert-' + data.type;
                        var messageText = data.message;
                        var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                        if (messageAlert && messageText) {
                            $('#open-ticket-form').find('.messages').html(alertBox);
                            $('#open-ticket-form')[0].reset();
                        }
                    }
                });
                return false;
            }
        })
    });
});