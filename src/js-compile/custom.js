jQuery(document).ready(function(e) {
// get current url
    var location = window.location.href;

// remove active class from all
    $(".nav-item a").removeClass('active');

// add active class to div that matches active url
    $(".nav-item a[href='"+location+"']").addClass('active');
});

$(document).ready(function() {

    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.move-top').fadeIn();
        } else {
            $('.move-top').fadeOut();
        }
    });

    $('.move-top').click(function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

});
