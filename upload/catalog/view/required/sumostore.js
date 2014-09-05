
function getURLVar(key) {
    var value = [];

    var query = String(document.location).split('?');

    if (query[1]) {
        var part = query[1].split('&');

        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }

        if (value[key]) {
            return value[key];
        }
        return '';
    }
}

function addToCart(product_id, quantity, option) {
    quantity = typeof(quantity) != 'undefined' ? quantity : 1;
    var string = 'product_id=' + product_id + '&';
    if (typeof(option) != 'undefined') {
        string += option;
    }
    else if (typeof(quantity) != 'undefined' && quantity != null) {
        string += '&quantity=' + quantity;
    }

    $.ajax({
        url: 'checkout/cart/add',
        type: 'post',
        data: string,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information, .error').remove();

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (json['success']) {
                $('#notification').html('<div class="alert alert-info alert-dismissable" style="display: none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + json['success'] + ' </div>');
                $('#notification a').each(function() {
                    $(this).addClass('alert-link');
                })
                $('#notification > div').fadeIn('slow');
                $('#cart').trigger('mouseleave');
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
            else {
                $('#notification').html('<div class="alert alert-danger alert-dismissable" style="display:none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + json['error'] + '</div>');
            }
        }
    });
    return false;
}

function addToWishList(product_id) {
    $.ajax({
        url: 'account/wishlist/add',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();

            if (json['success']) {
                $('#notification').html('<div class="alert alert-info alert-dismissable" style="display: none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + json['success'] + ' </div>');
                $('#notification a').each(function() {
                    $(this).addClass('alert-link');
                })
                $('#notification > div').fadeIn('slow');
                $('#wishlist-total').html(json['total']);
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        }
    });
    return false;
}

function addToCompare(product_id) {
    $.ajax({
        url: 'product/compare/add',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();

            if (json['success']) {
                $('#notification').html('<div class="alert alert-info alert-dismissable" style="display: none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + json['success'] + ' </div>');
                $('#notification a').each(function() {
                    $(this).addClass('alert-link');
                })
                $('#notification > div').fadeIn('slow');
                $('#compare-total').html(json['total']);
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        }
    });
    return false;
}

$(function() {
    $('.btn-compare, .btn-wishlist, .btn-order').on('click', function(e) {
        e.preventDefault();
        if ($(this).hasClass('btn-compare')) {
            addToCompare($(this).attr('product'));
        }
        else
        if ($(this).hasClass('btn-wishlist')) {
            addToWishlist($(this).attr('product'));
        }
        else
        if ($(this).hasClass('btn-order') && !$(this).hasClass('btn-ignore')) {
            if ($(this).attr('id') != 'button-cart') {
                addToCart($(this).attr('product'));
            }
        }
        else {
            window.location = $(this).attr('href');
        }
        $('#cart').trigger('mouseleave');
        return false;
    })

    $('#cart').on('click mouseover', '.header', function() {
        $('#cart').addClass('active');
    });
    $('#cart').on('mouseleave', function() {
        $('#cart').removeClass('active');
        $('#cart').load('./app/widgetsimpleheader/index/cart #cart > *');
    });
    $('#cart').trigger('mouseleave');

    /* Mega Menu */
    $('#menu ul > li > a + div').each(function(index, element) {
        // IE6 & IE7 Fixes
        /*
        if ($.browser.msie && ($.browser.version == 7 || $.browser.version == 6)) {
            var category = $(element).find('a');
            var columns = $(element).find('ul').length;

            $(element).css('width', (columns * 143) + 'px');
            $(element).find('ul').css('float', 'left');
        }
        */
        var menu = $('#menu').offset();
        var dropdown = $(this).parent().offset();

        i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

        if (i > 0) {
            $(this).css('margin-left', '-' + (i + 5) + 'px');
        }
    });

    $('.success img, .warning img, .attention img, .information img').on('click', function() {
        $(this).parent().fadeOut('slow', function() {
            $(this).remove();
        });
    });

    $('.product-list-item').not('a').on('click', function(e) {
        if ($(this).attr('url')) {
            window.location = $(this).attr('url');
        }
    })
    $('.product-list-item').hover(
        function() {
            var alt = $(this).find('img').data('alt');
            if (alt == undefined || alt == '') {
                return;
            }
            $(this).find('img').data('original', $(this).find('img').attr('src'));
            $(this).find('img').attr('src', $(this).find('img').data('alt'));
        },
        function() {
            var original = $(this).find('img').data('original');
            if (original == undefined || original == '') {
                return;
            }
            $(this).find('img').data('alt', $(this).find('img').data('src'));
            $(this).find('img').attr('src', $(this).find('img').data('original'));
        }
    );

    $('.product-slider').each(function() {
        $(this).lightSlider({
            slideMove:1,
            slideMargin:3,
            slideWidth: 259,
            loop: true,
            pager: false,
            auto: true,
            pause: 6000
        })
    });
    $('.image-slider').each(function() {
        $(this).lightSlider({
            gallery: true,
            minSlide: 1,
            maxSlide: 1,
            slideMove: 1,
            mode: 'fade',
            onSliderLoad: function() {
                $('.image-slider').removeClass('cS-hidden');
                $('.csPager').show();
            },
            loop: true
        })
    });
    $('.home-slider').each(function() {
        $(this).lightSlider({
            minSlide: 1,
            maxSlide: 1,
            slideMove: 1,
            mode: 'fade',
            onSliderLoad: function() {
                $('.image-slider').removeClass('cS-hidden');
                $('.csPager').show();
            },
            loop: true,
            controls: false,
            auto: true,
            pause: 4000,
            slideWidth: 200
        })
    });
});

