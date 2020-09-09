$(function() {
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 10) {
            $('.navbar').addClass('active');
            $('.navbarPost').addClass('active');
        } else {
            $('.navbar').removeClass('active');
            $('.navbarPost').removeClass('active');
        }
    });
});