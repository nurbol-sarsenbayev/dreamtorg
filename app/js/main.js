$(function() {

    var $wnd = $(window);
    var $top = $(".page-top");
    var $html = $("html, body");
    var $thanks = $("#thanks");
    var $tests = $(".test");
    var carouselLength = 6;    

    var utms = parseGET();

    if(utms && Object.keys(utms).length > 0) {
        window.sessionStorage.setItem('utms', JSON.stringify(utms));
    } else {
        utms = JSON.parse(window.sessionStorage.getItem('utms') || "[]");
    }    

    $wnd.scroll(function() { onscroll(); });
    $wnd.resize(function() { onresize(); });

    var onscroll = function() {
        if($wnd.scrollTop() > $wnd.height()) {
            $top.addClass('active');
        } else {
            $top.removeClass('active');
        }
    }

    onscroll();

    var onresize = function() {
        if($wnd.width() < 580) {
            carouselLength = 2;
        } else if($wnd.width() < 768) {
            carouselLength = 3;
        } else if($wnd.width() < 992) {
            carouselLength = 4
        } else if($wnd.width() < 1200) {
            carouselLength = 5
        } else {
            carouselLength = 6;
        }

        setTimeout(function() {
            $(".present").equalHeights();            
        }, 10);
    }

    onresize();

    $top.click(function() {
        $html.stop().animate({ scrollTop: 0 }, 'slow', 'swing');
    });

    $(".test .button").click(function() {
        var $test = $(this).closest('.test');
        var n = $test.find('input:checked').length;

        if($test.hasClass('has-form')) {
            var $requireds = $test.find(':required');
            var formValid = true;

            $requireds.each(function() {
                $elem = $(this);

                if(!$elem.val() || !checkInput($elem)) {
                    $elem.addClass('error');
                    formValid = false;
                }
            });

            if(formValid) {
                if($(this).hasClass("form-submit")) {
                    var $form = $(this).closest('form');
                    var data = $form.serialize();

                    if(Object.keys(utms).length > 0) {
                        for(var key in utms) {
                            data += '&' + key + '=' + utms[key];
                        }
                    } else {
                        data += '&utm=Прямой переход'
                    } 

                    $.ajax({
                        type: "POST",
                        url: "/mail.php",
                        data: data
                    }).done(function() {                
                    });

                    $requireds.removeClass('error');
                    $form[0].reset();
                } else {
                    $test.removeClass("active");
                    $test.next().addClass("active");            
                }    
            }
        }
        else if(n > 0) {
            $test.removeClass("has-error");
            $test.removeClass("active");
            $test.next().addClass("active");

            var index = $tests.index($test) + 1;
            
            if(index < 15) { 
                $(".present-" + index).removeClass("last-active");
                $(".present-" + (index + 1)).addClass("last-active");
                $(".present-" + (index + 1)).addClass("active");

                if(index % carouselLength == 0) {
                    owl.trigger("to.owl.carousel", [index / carouselLength, 200]);
                }
            }
        } else {
            $test.addClass("has-error");
        }

        return false;
    });
    

    var $remodalForm = $("#remodal-form");
    $("#remodal-form").submit(function() {
        if(Object.keys(utms).length > 0) {
            for(var key in utms) {
                $remodalForm.append("<input type='hidden' name='" + key + "' value='" + utms[key] + "'>");
            }
        } else {
            $remodalForm.prepend("<input type='hidden' name='utm' value='Прямой переход'>");
        } 
    });

    $(".phone").mask("+7 (999) 999 99 99", {
        completed: function() {
            $(this).removeClass('error');
        }
    });    

    $("input:required").keyup(function() {
        var $this = $(this);
        if(!$this.hasClass('phone')) {
            checkInput($this);
        }
    });    

    var owl = $(".present-carousel");
    owl.owlCarousel({
        items: 1,
        nav: false,
        dots: true,
        loop: false,
        smartSpeed: 500,
        margin: 0,
        navText: ['', ''],
        responsive: {
            0: { items: 2 },
            580: { items: 3 },
            768: { items: 4 },        
            992: { items: 5 },
            1200: { items: 6 },            
        },
    });        

    updateDate();

    $(".birthday-year, .birthday-month").change(function() {
        setDays();
    });

    $(".birthday-year, .birthday-day").change(function() {
        var $this = $(this);
        if($this.val()) {
            $this.removeClass("error");
        }
    });
});

var months = [
    'января',
    'февраля',
    'марта',
    'апреля',
    'мая',
    'июня',
    'июля',
    'августа',
    'сентября',
    'октября',
    'ноября',
    'декабря'
];

function updateDate() {
    var today = new Date();
    var year = today.getFullYear();

    today.setMonth(today.getMonth() + 1);
    $(".date-after-month").html(today.getDate() + " " + months[today.getMonth()]);

    var years = "<option value=''>Год</option>";
    for(var i = year; i > year - 100; i--) {
        years += "<option value='" + i + "'>" + i + "</option>";
    }

    $(".birthday-year").html(years);
    setDays();    
}

function setDays() {
    var year = +$(".birthday-year").val();
    var month = +$(".birthday-month").val();
    var day = +$(".birthday-day").val();

    var dayCount = year ? (new Date(year, month, 0)).getDate() : 30;
    var days = "<option value=''>День</option>";

    for(var i = 1; i <= dayCount; i++) {
        days += "<option value='" + i + "'" + (day === i ? " selected" : "") + ">" + i + "</option>";
    }

    $(".birthday-day").html(days);
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function checkInput($input) {
    if($input.val()) {
        if($input.attr('type') != 'email' || validateEmail($input.val())) {
            $input.removeClass('error');
            return true;
        }
    }
    return false;
}
    

function parseGET(url){
    var namekey = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    if(!url || url == '') url = decodeURI(document.location.search);
     
    if(url.indexOf('?') < 0) return Array(); 
    url = url.split('?'); 
    url = url[1]; 
    var GET = {}, params = [], key = []; 
     
    if(url.indexOf('#')!=-1){ 
        url = url.substr(0,url.indexOf('#')); 
    } 
    
    if(url.indexOf('&') > -1){ 
        params = url.split('&');
    } else {
        params[0] = url; 
    }
    
    for (var r=0; r < params.length; r++){
        for (var z=0; z < namekey.length; z++){ 
            if(params[r].indexOf(namekey[z]+'=') > -1){
                if(params[r].indexOf('=') > -1) {
                    key = params[r].split('=');
                    GET[key[0]]=key[1];
                }
            }
        }
    }

    return (GET);    
};