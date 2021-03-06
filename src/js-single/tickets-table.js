$(document).ready(function () {
    $('.reply-submit').submit(function (e) {
        e.preventDefault();
        var url = document.location.origin + "/include/workers/reply-ticket.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function (data) {
                var messageAlert = 'alert-' + data.type;
                var messageText = data.message;
                var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                var pageUrl = document.location.origin + "/dashboard/tickets/"
                if (messageAlert && messageText) {
                    $('.reply-submit').find('.messages').html(alertBox);
                }
                if (data.type === 'success') {
                    window.location.replace(pageUrl);
                }
            }
        });
    });
});
$(document).ready(function () {
    $('.confirm-close-submit').submit(function (e) {
        e.preventDefault();
        var url = document.location.origin + "/include/workers/confirm-close-ticket.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function (data) {
                var messageAlert = 'alert-' + data.type;
                var messageText = data.message;
                var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                var pageUrl = document.location.origin + "/dashboard/tickets/"
                if (messageAlert && messageText) {
                    $('.confirm-close-submit').find('.messages').html(alertBox);
                }
                if (data.type === 'success') {
                    window.location.replace(pageUrl);
                }
            }
        });
    });
});
$(document).ready(function () {
    $('.modify-submit').submit(function (e) {
        e.preventDefault();
        var url = document.location.origin + "/include/workers/modify-ticket.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function (data) {
                var messageAlert = 'alert-' + data.type;
                var messageText = data.message;
                var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + '</div>';
                var pageUrl = document.location.origin + "/dashboard/tickets/"
                if (messageAlert && messageText) {
                    $('.modify-submit').find('.messages').html(alertBox);
                }
                if (data.type === 'success') {
                    window.location.replace(pageUrl);
                }
            }
        });
    });
});
