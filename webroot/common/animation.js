$(function() {
    $('body').addClass('fadein');
    $(window).on("beforeunload", function () {
        $('body').removeClass('fadein');
    });
});