require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.homeSlider').owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            pagination: true,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 1,
                    nav: true
                },
                1000: {
                    items: 1,
                    nav: true,
                    loop: true
                }
            }
        });
        $( ".owl-prev").html('<span class="custom-prev"></span>');
        $( ".owl-next").html('<span class="custom-next"></span>');
    });
});

require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.featured-slider').owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 2,
                    nav: true
                },
                1000: {
                    items: 5,
                    nav: true,
                    loop: true
                }
            }
        });
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $("#is_subscribed").click(function(){
      $(".area_interested").toggle();
    });
    });