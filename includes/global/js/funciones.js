$(function() {
    //Se activa cuando el scroll supera los 100px
    $(window).scroll(function() {
        if ($(this).scrollTop() > 10) {
            $('a.scroll-top').fadeIn();
        } else {
            $('a.scroll-top').fadeOut();
        }
    });
    //Crea la animacion al dar clic sobre el boton
    $('a.scroll-top').click(function() {
        $("html, body").animate({scrollTop: 0}, 600);
        return false;
    });
});