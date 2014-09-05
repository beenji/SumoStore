$(function() {
    $('input[name^=stock]').on('change', function(e) {
        var input   = $(this),
            prodID  = input.attr('name').replace('stock_', ''),
            stockID = $('input[name=stock_id_' + prodID + ']').val(),
            value   = parseInt(input.val()),
            parent  = input.parent(),
            elemWidth = input.width(),
            pos = input.position();

        if (value == input.data('stock')) {
            return;
        }

        $('<span />').addClass('input-indicator').css({
            width: input.width() + 18,
            marginLeft: 0 - (input.width() / 2) - 9,
            top: pos.top + 1
        }).appendTo(input.parent());

        $.post('catalog/product/stock&token=' + sessionToken, {
            stock_id: stockID,
            quantity: value
        }, function(result) {
            // Do something
            $('.input-indicator', input.parent()).css('background-image', 'none').append('<span class="fa fa-check" />');
            setTimeout(function() {
                $('.input-indicator', input.parent()).fadeOut(500, function() {
                    $('.input-indicator', input.parent()).remove()
                });
            }, 1000);

            $('.stock-' + input.data('stock-id')).each(function() {
                $(this).data('stock', value);
                $(this).val(value);
                // Reset stock indicator
                $(this).parent().removeClass('has-error has-warning has-success');
                if (value <= 0) {
                    $(this).parent().addClass('has-error');
                }
                else if (value <= 5) {
                    $(this).parent().addClass('has-warning');
                }
                else {
                    $(this).parent().addClass('has-success');
                }
            })
        });
    });

    $('input[name^=option]').on('change', function(e) {
        var input   = $(this),
            id      = input.data('option'),
            value   = parseInt(input.val()),
            parent  = input.parent(),
            elemWidth = input.width(),
            pos = input.position();
        if (value == input.data('stock')) {
            return;
        }

        $('<span />').addClass('input-indicator').css({
            width: input.width() + 18,
            marginLeft: 0 - (input.width() / 2) - 9,
            top: pos.top + 1
        }).appendTo(input.parent());

        $.post('catalog/product/optionstock&token=' + sessionToken, {
            value_id: id,
            quantity: value
        }, function(result) {
            // Do something
            $('.input-indicator', input.parent()).css('background-image', 'none').append('<span class="fa fa-check" />');
            setTimeout(function() {
                $('.input-indicator', input.parent()).fadeOut(500, function() {
                    $('.input-indicator', input.parent()).remove()
                });
            }, 1000);

            // Reset stock indicator
            parent.removeClass('has-error has-warning has-success');

            if (value <= 0) {
                parent.addClass('has-error');
            }
            else if (value <= 5) {
                parent.addClass('has-warning');
            }
            else {
                parent.addClass('has-success');
            }

            input.data('stock', value);
        });
    })

    $('input[name^=visible]').on('change', function() {
        var input     = $(this),
            status    = 0,
            productID = $(this).attr('name').replace('visible_', '');

        if ($(this).is(':checked')) {
            status = 1;
        } else {
            status = 0;
        }

        $.post('catalog/product/status&token=' + sessionToken,  {
            product_id: productID,
            status: status
        }, function(result) {
            // Do something
        });
    });

    $('#stock-control').on('change', function() {
        var val = $(this).val();

        if (val != '') {
            $('#stock').attr('name', 'filter_stock_' + val);
        }
        else {
            $('#stock').attr('name', 'filter_stock');
        }
    });

    $('#extra_category_filter').on('change', function() {
        $('#category').val($(this).val());
        $('#advanced_filter_form').submit();
    });

    $('.open-option').on('click', function() {
        $('.hidden-option').each(function() {
            $(this).addClass('hidden');
            $('.input-option-' + $(this).data('product')).prop('disabled', 0).removeClass('disabled');
        })
        $(this).prop('disabled', 1).addClass('disabled');
        $('.' + $(this).data('option')).removeClass('hidden');
    })
});
