$(document).ready(function(){
    if ($(window).width() <= 1000) {
        if (document.querySelector('.subcategories') != null) {
            new Glider(document.querySelector('.subcategories'), {
                draggable: true,
                slidesToShow: 'auto',
                slidesToScroll:'auto',
                itemWidth: 250,
                arrows: {
                    prev: '.arrow_left',
                    next: '.arrow_right'
                }
            });
        }
    }
    $("#act nav").on("click", "a", function (event) {
        event.preventDefault();
        var id = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({ scrollTop: top }, 1000);
    });
});
