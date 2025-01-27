jQuery(function($){
    $('.filter .tags').slick({
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        swipeToSlide: true,
        arrows: false,
        variableWidth: true,
        mobileFirst: true,
        responsive: [
           {
              breakpoint: 768,
              settings: "unslick"
           }
        ]
    });
});
