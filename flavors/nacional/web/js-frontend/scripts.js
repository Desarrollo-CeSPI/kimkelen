$(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
});




jQuery(document).ready(function () {
    window.fbAsyncInit = function () {
        FB.init({appId: 1817942881803388, status: false, cookie: true, xfbml: true});
        FB.Event.subscribe('auth.login', function (response) {
            window.location = "<?php echo url_for('default', array('module' => 'sfGuardAuth', 'action' => 'facebookLogin')) ?>";
        });
        FB.Event.subscribe('auth.logout', function (response) {
            window.location = "<?php echo url_for('@homepage') ?>";
        });
    };
    //(function () {
    //var e = document.createElement('script');
    //e.type = 'text/javascript';
    //e.src = 'http://connect.facebook.net/es_LA/all.js';
    //e.async = true;
    //document.getElementById('fb-root').appendChild(e);
    //}());



    // JS SDK - this will be loaded asynchronously
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/es_LA/all.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));



});








$(document).ready(function() {
    $('.navbar a.dropdown-toggle').on('click', function(e) {
        var $el = $(this);
        var $parent = $(this).offsetParent(".dropdown-menu");
        $(this).parent("li").toggleClass('open');

        if(!$parent.parent().hasClass('nav')) {
            $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
        }

        $('.nav li.open').not($(this).parents("li")).removeClass("open");

        return false;
    });

    $(".nav-tabs a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("active");
        $(this).parent().siblings().removeClass("active");
        var tab = $(this).attr("href");
        $(".tab-pane").not(tab).css("display", "none");
        $(tab).fadeIn();
    });

    $('#menu li').click(function(){
        $(this).find('ul').slideToggle('slow');
    });

    $('#nav_mobile').click(function(){
        $('#oculto').slideToggle('slow');
    });
});



