var $document = $(document);
var $window = $(window);
var $page = 0;
let $loadTimeout = null;

$document.ready(function() {
    $.showMain();
});

$window.scroll(function() {
    $.buttonCheckScroll();
});

$.enableLightTheme = function(enabled) {
    if (enabled)
        $('link[href="/client/css/black.css"]').attr('href','/client/css/light.css');
    else
        $('link[href="/client/css/light.css"]').attr('href','/client/css/black.css');

    if (!enabled) {
        $("nav #hover").attr("style", "color: #FFF !important");
    }
    else {
        $("nav #hover").attr("style", "color: #000 !important");
    }

    $.ajax({
        type: 'POST',
        url: '/index.php',
        data: 'ajax=true&action=enable-light-theme&enabled=' + enabled,
        success: function(data) {
            console.log(data);
        },
        error: function () {
        }
    });
};

$.showMain = function() {
    M.AutoInit();
    M.updateTextFields();

    //$('.button-collapse').sideNav();
    //$('.carousel.carousel-slider').carousel({fullWidth: true});
    $('.tooltipped').tooltip({delay: 50});
    $.buttonScroll();

    $('.collapsible').collapsible();
    $('.modal').modal();
    //$('select').material_select();

    //$('.chips').material_chip();
    /*$('.chips-placeholder').material_chip({
     placeholder: 'ID нарушителей',
     secondaryPlaceholder: ' ',
     });*/

    $('.chips-placeholder').chips({
        placeholder: 'ID нарушителей',
        secondaryPlaceholder: '',
    });

    $("nav #hover-black").hover(
        function() {
            $("nav #hover-black").attr("style", "color: rgba(0, 0, 0, 0.3) !important");
            $(this).attr("style", "color: #000 !important");
        },
        function() {
            $("nav #hover-black").attr("style", "color: #000 !important");
        }
    );

    $("nav #hover").hover(
        function() {

            if ($('link[href="/client/css/black.css"]').length) {
                $("nav #hover").attr("style", "color: rgba(255, 255, 255, 0.3) !important");
                $(this).attr("style", "color: #FFF !important");
            }
            else {
                $("nav #hover").attr("style", "color: rgba(0, 0, 0, 0.3) !important");
                $(this).attr("style", "color: #000 !important");
            }
        },
        function() {
            if ($('link[href="/client/css/black.css"]').length) {
                $("nav #hover").attr("style", "color: #FFF !important");
            }
            else {
                $("nav #hover").attr("style", "color: #000 !important");
            }
        }
    );

    setTimeout(function () {
        $('.chips input').keyup(function(e) {

            let arrayToString = [];

            let instance = M.Chips.getInstance($('.chips'));
            let data = instance.getData();
            data.forEach(item => {
                arrayToString.push(item.tag);
            });

            $('.inputChipSend').val(arrayToString.toString());

            if (data.length > 0)
                $('#btnSendReport').removeAttr('disabled');
        });
    }, 500);

    $('main').show();
};


$.hideMain = function() {
    $('main').hide();
};

$.showPreloader = function() {
    $('#preloader').removeClass('fadeOut');
    $('#preloader').addClass('fadeIn');
};

$.hidePreloader = function() {
    $('#preloader').removeClass('fadeIn');
    $('#preloader').addClass('fadeOut');
};

$.generateSignature = function() {

    $.ajax({
        type: 'POST',
        url: '/index.php',
        data: 'ajax=true&action=getSignature&acc=' + $('#donateAcc').val() + '&desc=' + $('#donateDesc').val() + '&sum=' + $('#donateSum').val(),
        success: function(data) {
            $('#donateSig').val(data)
        },
        error: function () {
        }
    });
};

$.updateDonateLabel = function() {
    let sum = $('#donateSumTrade').val();
    $('#moneyDcLabel').html('Введите сумму (' + sum + 'dc = $' + sum * 250 + ')')
};

$.updateDonateNcLabel = function() {
    let sum = $('#donateSumNcTrade').val();
    $('#moneyNcLabel').html('Введите сумму (' + sum + 'bp = $' + sum * 100 + ')')
};

$.buttonScroll = function() {
    $('#scrollup').click( function() {
        $('html, body').animate({scrollTop: 0}, '500', 'swing');
        return false;
    });
};

$.buttonCheckScroll = function() {

    if ($document.scrollTop() > 100 ) {
        $('#scrollup').css('opacity', 1);
        $('#scrollup').removeClass('bounceOutDown');
        $('#scrollup').addClass('bounceInUp');
    } else {
        $('#scrollup').removeClass('bounceInUp');
        $('#scrollup').addClass('bounceOutDown');
    }
};