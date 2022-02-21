require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        setTimeout(function(){ 
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
       }, 1000);
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        setTimeout(function(){ 
        $('.sampleslider').owlCarousel({
            loop: true,
            margin: 10,
            center: true,
            responsiveClass: true,
            pagination: true,
            autoplay: true,
            stopOnHover: true,
            navigation: false,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 6000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                769: {
                    items: 2,
                    nav: true
                },
                1000: {
                    items: 3,
                    nav: true,
                    loop: true
                }
            }
        });
       }, 1000);
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
		if(!$('body').hasClass('cms-home-page-v15')) { 
   $('.pcustom.head').remove();
  }
	});
});

require(["jquery"], function ($)
 {
var checkExistt = setInterval(function() {
        if (jQuery('input[name=telephone]').length) {
            // jQuery("input[name=telephone]").val("+1");     
            jQuery("input[name=telephone]").trigger('change'); 
            jQuery("input[name=telephone]").before('<img id="theImg" src="https://aquam2v1.aquariumspecialty.com/pub/media/usflag.png" />'); 
            jQuery("input[name=telephone]").before("<span id=uscode>+1</span>");
            jQuery("input[name=telephone]").after("<span id=min-length></span>");
            jQuery("input[name=telephone]").attr("maxlength", "10");
                clearInterval(checkExistt);
        }
        }, 100);    
 jQuery("body").delegate("input[name=telephone]","keypress",function (e) {    
    
    var charCode = (e.which) ? e.which : event.keyCode    
    jQuery("input[name=telephone]").val(jQuery("input[name=telephone]").val()); 
    jQuery("input[name=telephone]").trigger('change'); 
    if (String.fromCharCode(charCode).match(/[^0-9]/g))    

        return false;                        
        jQuery("input[name=telephone]").val(jQuery("input[name=telephone]").val()); 
        jQuery("input[name=telephone]").trigger('change'); 
}); 
}); 
// require(["jquery"], function ($)
//  {
// jQuery("body").delegate("input[name=telephone]","keydown",function(event){
// console.log(this.selectionStart);
// console.log(event);
// if(event.keyCode == 8){
// this.selectionStart--;
// }
// if(this.selectionStart < 2){
// this.selectionStart = 2;
// console.log(this.selectionStart);
// event.preventDefault();
// }
// jQuery("input[name=telephone]").val(jQuery("input[name=telephone]").val()); 
// jQuery("input[name=telephone]").trigger('change'); 
// });
// });
require(["jquery"], function ($)
 {
    var checkExistt = setInterval(function() {
        if (jQuery('input[name=telephone]').length) {
            jQuery('input[name=telephone]').bind('copy paste cut',function(e) {
                e.preventDefault(); //disable cut,copy,paste
              });
                clearInterval(checkExistt);
        }
        }, 100);
});
// require(["jquery"], function ($)
//  {
//   jQuery("input[name=telephone]").after("<span id=min-length></span>");
// }); 
require(["jquery"], function ($)
 {
    var checkExistt = setInterval(function() {
        if (jQuery('input[name=telephone]').length) {
            var minLength = 10; 
        jQuery('input[name=telephone]').on('keydown keyup change', function(){
        var char = jQuery(this).val();
        var charLength = jQuery(this).val().length;
        if(charLength < 10){
            jQuery('#min-length').text('Length is short, minimum '+minLength+' required.');
        }
        else{
            jQuery('#min-length').text('');
        }
    });
                clearInterval(checkExistt);
        }
        }, 100);
});