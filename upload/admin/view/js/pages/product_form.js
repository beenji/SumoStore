var infoTypes = {'sku': 1, 'upc': 1, 'isbn': 1, 'ean': 1, 'jan': 1, 'mpn': 1},
    optionList = [];

$(function() {
    // Trigger some buttons before sending the product form
    $('#product-form').submit(function() {
        $('#extra-info').click();
        if ($('.table-option').is(':visible')) {
            // Trigger option save
            $('#add-option').click();
        }
        $('input[type=submit]', $(this)).click();
    });

    $('#save_product').on('click', function() {
        $('#product-form').trigger('submit');
    //    alert('bla');
    });

    $('#tax_settings_link').click(function() {
        localStorage.setItem('settings_form', '#taxes');
    });

    $('#find-product').autocomplete({
        url: './catalog/product/find_product',
        param: 'product',
        callback: function(elem, data) {
            // Get product options
            if (data.id != undefined) {
                $.getJSON('./catalog/product/get_product_options?token=' + sessionToken + '&product_id=' + data.id, function(optData) {
                    if (optData.length > 0) {
                        optionList = optData;

                        $('#find-option').empty();
                        $('#find-option').append($('<option />').val('').html('Maak een keuze'));

                        for (option in optData) {
                            $('#find-option').append($('<option />').val(option).html(optData[option].name));
                        }
                    }
                });
            }
        },
        extraParams: {
            token: sessionToken
        }
    });

    $('#duplicate-option').click(function() {
        if (optionList[$('#find-option option:selected').val()] != undefined) {
            var option = optionList[$('#find-option option:selected').val()];

            // 1. Set label
            for (languageID in option.option_description) {
                $('#option_name_' + languageID).val(option.option_description[languageID].name);
            }

            // 2. Set type
            $('#option-type').val(option.type);

            // 3. Set choices
            if (option.product_option_value.length > 0) {
                for (valueIndex in option.product_option_value) {
                    var optionValue = option.product_option_value[valueIndex];

                    // Subtract weight?
                    if (optionValue.weight_prefix == '-') {
                        var weight = 0 - optionValue.weight;
                    } else {
                        var weight = optionValue.weight;
                    }

                    // Subtract from price?
                    if (optionValue.price_prefix == '-') {
                        var price = 0 - optionValue.price;
                    } else {
                        var price = optionValue.price;
                    }

                    // Translate labels
                    var label = {};

                    for (languageID in optionValue.option_value_description) {
                        label[languageID] = optionValue.option_value_description[languageID].name;
                    }

                    addChoiceRow(0, optionValue.active, label, optionValue.quantity, optionValue.subtract, price, '', weight)
                }
            }

            $('#option-type').trigger('change');
        }

        // Close modal
        $('#optionModal').modal('hide');
    });

    $('#search-related-product').autocomplete({
        url: './catalog/product/find_product',
        param: 'product',
        callback: function(elem, data) {
            // Check if product is not already chosen
            var skip = false;

            elem.val('');

            $("#table-related-product tr").each(function(k, v) {
                if ($(this).data('productid') == data.id) {
                    skip = true;

                    return;
                }
            });

            if (skip) {
                return;
            }

            var row = $('<tr />').data('productid', data.id);
                row.append('<td style="width: 66px;"><img src="' + data.image + '" /></td>');
                row.append('<td><strong>' + data.name + '</strong><input type="hidden" name="product_related[]" value="' + data.id + '" /></td>');
                row.append('<td class="right"><a href="#delete" class="btn btn-primary btn-xs">' + deleteButton + '</a></td>');

            $('#table-related-product').append(row);
            $('#table-related-product').show();
        },
        extraParams: {
            token: sessionToken
        }
    });

    $('#download').autocomplete({
        url: './catalog/download/autocomplete',
        param: 'filter_name',
        labelKey: 'name',
        callback: function(elem, data) {
            // Check if download is not already chosen
            var skip = false;

            elem.val('');

            $("#table-downloads tr").each(function(k, v) {
                if ($(this).data('downloadid') == data.download_id) {
                    skip = true;

                    return;
                }
            });

            if (skip) {
                return;
            }

            var row = $('<tr />').data('downloadid', data.download_id);
                row.append('<td><strong>' + data.name + '</strong><input type="hidden" name="product_download[]" value="' + data.download_id + '" /></td>');
                row.append('<td class="right"><a href="#delete" class="btn btn-primary btn-xs">' + deleteButton + '</a></td>');

            $('#table-downloads').append(row);
            $('#table-downloads').show();
        },
        extraParams: {
            token: sessionToken
        }
    });

    $('#category').change(function() {
        // Only do this on category-change
        shopElem = $('#shop option:selected'),
        catElem = $('#category option:selected');

        if (catElem.val() <= 0) {
            return;
        }

        if ($('#category-box li').length > 0) {
            // Change first row
            $('#category-box li:nth-child(1)').remove();
        }

        $('#category-box').prepend($('<li class="category-row" />').html('\
            <a href="#remove-category" class="pull-right"><i class="fa fa-times-circle"></i></a>\
            <strong>' + shopElem.html() + ':</strong>\
            ' + catElem.html() + '\
            <input type="hidden" name="product_store[]" value="' + shopElem.val() + '" />\
            <input type="hidden" name="product_category[]" value="' + catElem.val() + '" />').data({'shop-id': shopElem.val(), 'category-id': catElem.val()}));
    });

    $('a[href="#extra-category"]').click(function() {
        var row = $(this).closest('.row').clone();

        $('select[name^=shop]', row).val("");
        $('select[name^=shop] option[value=""]', row).attr('selected', true);
        $('select[name^=category] option:gt(0)', row).remove();

        $('a', row).attr('href', '#remove-category').html('<i class="fa fa-times-circle" style="font-size: 16px;"></i>').removeClass('btn').removeClass('btn-default').css('line-height', '34px');

        row.appendTo($(this).closest('.modal-body'));

        return false;
    });

    $('a[href="#extra-attribute"]').click(function() {
        var row = $(this).closest('.row').clone();

        $('input', row).val("");

        $('a', row).attr('href', '#remove-attribute').html('<i class="fa fa-times-circle" style="font-size: 16px;"></i>').removeClass('btn').removeClass('btn-default').css('line-height', '34px');

        row.appendTo($(this).closest('.form-group'));

        return false;
    });

    $('input[name$="[name]"]').blur(function() {
        var lang = $(this).data('lang-id');
        $('input[name="seo_name[' + lang + ']"]').val($(this).val());
    });

    $('#tax').change(function() {
        var inc    = $('#price_in').val(),
            ex     = $('#price_ex').val(),
            sp_inc = $('#sp_price_in').val(),
            sp_ex  = $('#sp_price_ex').val(),
            perc   = $('option:selected', $(this)).data('percentage');

        if (inc > 0) {
            ex = inc / (1 + (perc / 100));
        }
        else if (ex > 0) {
            inc = ex * (1 + (perc / 100));
        }

        ex  = Number(ex);
        inc = Number(inc);

        ex = ex.toFixed(4);
        inc = inc.toFixed(4);

        $('#price_in').val(inc);
        $('#price_ex').val(ex);

        // Special price
        if (sp_inc > 0) {
            sp_ex = sp_inc / (1 + (perc / 100));
        }
        else if (ex > 0) {
            sp_inc = sp_ex * (1 + (perc / 100));
        }

        sp_ex  = Number(sp_ex);
        sp_inc = Number(sp_inc);

        sp_ex = sp_ex.toFixed(4);
        sp_inc = sp_inc.toFixed(4);

        $('#sp_price_in').val(sp_inc);
        $('#sp_price_ex').val(sp_ex);
    });

    $('#sp_price_in').blur(function() {
        var inc  = $(this).val(),
            ex   = $('#sp_price_ex').val(),
            perc = $('#tax option:selected').data('percentage');

        ex = inc / (1 + (perc / 100));

        ex = Number(ex);
        inc = Number(inc);

        ex = ex.toFixed(4);
        inc = inc.toFixed(4);

        $('#sp_price_in').val(inc);
        $('#sp_price_ex').val(ex);
    });

    $('#sp_price_ex').blur(function() {
        var inc  = $('#sp_price_in').val(),
            ex   = $(this).val(),
            perc = $('#tax option:selected').data('percentage');

        inc = ex * (1 + (perc / 100));

        ex  = Number(ex);
        inc = Number(inc);

        ex  = ex.toFixed(4);
        inc = inc.toFixed(4);

        $('#sp_price_in').val(inc);
        $('#sp_price_ex').val(ex);
    });

    $('#price_in, #price_ex, #cost').keyup(function() {
        // Replace ',' with '.'
        var val = $(this).val();

        val = val.replace(',', '.');
        val = val.replace(/[^\d.-]/g, '');

        $(this).val(val);
    });

    $('#price_in').blur(function() {
        var inc  = $(this).val(),
            ex   = $('#price_ex').val(),
            perc = $('#tax option:selected').data('percentage');

        ex = inc / (1 + (perc / 100));

        ex = Number(ex);
        inc = Number(inc);

        ex = ex.toFixed(4);
        inc = inc.toFixed(4);

        $('#price_in').val(inc);
        $('#price_ex').val(ex);
    });

    $('#price_ex').blur(function() {
        var inc  = $('#price_in').val(),
            ex   = $(this).val(),
            perc = $('#tax option:selected').data('percentage');

        inc = ex * (1 + (perc / 100));

        ex  = Number(ex);
        inc = Number(inc);

        ex  = ex.toFixed(4);
        inc = inc.toFixed(4);

        $('#price_in').val(inc);
        $('#price_ex').val(ex);
    });

    $('input[name=stock_product]').click(function() {
        if ($(this).val() == 1) {
            // Attach!
            $('.stock-independent').hide();
            $('.stock-attached').show();
        } else {
            $('.stock-independent').show();
            $('.stock-attached').hide();
        }
    });

    // Walk through the types once to turn them on or off
    $.each(infoTypes, function(type, visible) {
        if ($('option[value=' + type + ']').is(':selected')) {
            infoTypes[type] = 0;
        }
    });

    $('#extra-info').click(function() {
        var parent = $('.control-group', $(this).closest('.form-group')),
            infoBlock = $('.product-info:last-child', parent),
            extraInfoBlock = infoBlock.clone();

        if ($('select option', extraInfoBlock).length <= 1) {
            return;
        }

        if ($('input[type=text]', extraInfoBlock).val() == '') {
            return;
        }

        // Lock option
        opt = $('select option:selected', extraInfoBlock).val();
        $('input[type=text]', infoBlock).attr('name', opt);
        $('select', infoBlock).attr('disabled', true);

        // Turn off type
        infoTypes[opt] = 0;

        // Remove the active option
        /*$('select option:selected', extraInfoBlock).remove();
        $('select option:first-child', extraInfoBlock).attr('selected', true);*/

        $('select', extraInfoBlock).attr('disabled', false).empty();
        $.each(infoTypes, function(type, visible) {
            if (visible) {
                $('select', extraInfoBlock).append($('<option />').val(type).html(type.toUpperCase()));
            }
        })

        $('input[type=text]', extraInfoBlock).val('');

        extraInfoBlock.appendTo(parent);

        return false;
    })

    $('input[name^=option_name_]').keyup(function() {
        var elem = $(this),
            skip = false;

        //$('#add-option').removeData('row-index');

        // Not already filled in?
        /*$('input[name^=option_name_]').each(function() {
            if ($(this).val() != '' && $(this).attr('name') != elem.attr('name')) {
                skip = true;
            }
        });

        if (skip) {
            return;
        }

        $('.table-option tbody').empty();
        $('.table-option').hide();
        $('#add-option').show();*/
    })

    $('#option-type').change(function() {
        if ($(this).val() == 'select' ||
            $(this).val() == 'checkbox' ||
            $(this).val() == 'radio' ||
            $(this).val() == 'image') {
            $('.table-option').show();
            $('#extra-option-choice').show();
            $('#add-option').show();

            if ($('.table-option tbody tr').length == 0) {
                addChoiceRow(false, 1, '', '', true, 0, 0, 0);
            }
        } else {
            $('#extra-option-choice').hide();
            $('.table-option').hide();
        }
    })

    $('#extra-option-choice').click(function() {
        addChoiceRow(false, 1, '', '', true, 0, 0, 0);

        return false;
    })

/*  $('#existing-option').keyup(function() {
        //addChoiceRow(true, 1, '', '', true, 0, 0, 0);

        var val = $(this).val();

        if (val == $(this).data('selected-option') || val == '') {
            return;
        }

        $('#new-option').val('');
        $('#extra-option-choice').show();
        $('.new-option').hide();

        $(this).parent().css('position', 'relative');
        var ac = $('<ul class="autocomplete" />');

        $.getJSON('./catalog/product/find_option?token=' + sessionToken + '&option=' + val, function(result) {

            $.each(result, function(label, option) {
                var li = $('<li />');
                var a = $('<a />').text(label);

                a.click(function() {
                    $('.table-option tbody tr').empty();

                    $.each(option.choices, function(k, c) {
                        addChoiceRow(c['id'], c.active, c.label, c.stock, c.use_stock, c.price, c.points, c.weight);
                    });

                    $('#existing-option').val(label);
                    $('#existing-option').data({
                        'selected-option': label,
                        'type': option['type'],
                        'id': option['id']
                    });
                    $('#add-option').show();

                    $.each(languages, function(k, v) {
                        $('#existing-option').data('label-lang-' + v.language_id, option['label'][v.language_id]);
                    });

                    ac.remove();
                })

                a.appendTo(li);
                li.appendTo(ac);
            });

            $('#existing-option').after(ac);
        });
    });

    $('#existing-option').blur(function() {
        if ($(this).val() != $(this).data('selected-option')) {
            $('.table-option').hide();
            $('.table-option tbody tr').empty();
            $(this).val('');

            setTimeout(function() { $('ul.autocomplete').remove() }, 200);
        }
    });*/

    $('input[name=discount-type]').click(function() {
        if ($(this).val() == 'simple') {
            $('#staffle').hide();
        } else {
            $('#staffle').show();
        }
    });

    $('.add-discount').click(function() {
        // Type
        var type = $('input[name=discount-type]:checked').val(),
            typeText = $('input[name=discount-type]:checked').parent().text(),
            customerGroupValue = $('select[name=customer-group] option:selected').val(),
            customerGroup = $('select[name=customer-group] option:selected').text(),
            priceIn = parseFloat($('#sp_price_in').val()),
            priceEx = $('#sp_price_ex').val(),
            dateStart = $('#date-start').val(),
            dateEnd = $('#date-end').val(),
            prio = $('#prio').val(),
            min = $('#min-amount').val();

        var row = $('<tr />');
        $('<td />').text(customerGroup).append('<input type="hidden" name="product_' + type + '[' + specCount + '][customer_group_id]" value="' + customerGroupValue +'" />').appendTo(row);
        $('<td />').text(typeText).append('<input type="hidden" name="product_' + type + '[' + specCount + '][type]" value="' + type +'" />').appendTo(row);
        $('<td />').text(priceIn.formatMoney()).append('<input type="hidden" name="product_' + type + '[' + specCount + '][price]" value="' + priceEx +'" />').appendTo(row);
        $('<td />').text(dateStart + ' - ' + dateEnd).append('<input type="hidden" name="product_' + type + '[' + specCount + '][date_start]" value="' + dateStart + '" />').append('<input type="hidden" name="product_' + type + '[' + specCount + '][date_end]" value="' + dateEnd + '" />').appendTo(row);

        if (type == 'special') {
            $('<td />').html('&mdash;').appendTo(row);
        } else {
            $('<td />').text(min).append('<input type="hidden" name="product_' + type + '[' + specCount + '][quantity]" value="' + min +'" />').appendTo(row);
        }

        $('<td />').text(prio).append('<input type="hidden" name="product_' + type + '[' + specCount + '][priority]" value="' + prio + '" />').appendTo(row);

        var a = $('<a class="btn btn-xs btn-primary remove-discount" />').html('<i class="fa fa-minus-circle"></i>');
        $('<td />').append(a).appendTo(row);

        specCount++;
        row.appendTo('#discount-table');

        $('#discount-table').css('display', 'table');
        $('#discount-placeholder').hide();
    });

    $('.ge-trigger').keyup(function() {
        updateSeo();
    });

    $('textarea[name$="meta_description]"]').keyup(function() {
        var block = $(this).closest('.form-group'),
            val = $(this).val(),
            remaining = 156 - val.length;

        /*if (remaining < 0) {
            remaining = 0;
        }*/

        if (remaining < 0) {
            block.removeClass('has-warning').removeClass('has-success').addClass('has-error');
        }
        else if (remaining < 60) {
            block.removeClass('has-warning').removeClass('has-error').addClass('has-success');
        }
        else {
            block.removeClass('has-success').removeClass('has-error').addClass('has-warning');
        }

        $('span.meta-desc-length', block).text(remaining);
    });

    var storeID = $('#shop').val(),
        categoryID = $('#category').val(),
        productID = $('#product_id').val();

    // Handle file uploads
    new AjaxUpload('#upload-image', {
        action: 'common/images/upload?token=' + sessionToken + '&store=' + storeID + '&category=' + categoryID + '&product=' + productID,
        name: 'uploads',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function (file, extension) {
            // Uploading...
        },
        onComplete: function (file, result) {
            if (result['success']) {
                var result = result['success'][0];
                var tpl = ' <li>\
                                <img src="../image/' + result['location'] + '" />\
                                <input type="hidden" name="product_image[]" value="' + result['location'] + '">\
                                <label class="image-list-footer">\
                                    <a href="#" class="push-left">\
                                        <i class="fa fa-chevron-left"></i>\
                                    </a>\
                                    <a href="#" class="push-right">\
                                        <i class="fa fa-chevron-right"></i>\
                                    </a>\
                                </label>\
                                <a class="remove" href="#"><i class="fa fa-times-circle-o"></i></a>\
                            </li>';

                $('#product-image-list').append(tpl);
            }
            else {
                var message = 'Er is iets misgegaan met het uploaden.';
                if (result['error']) {
                    message = result['error'];
                }

                // Show error
                alert(message);
            }
        }
    });

    $('#stock-id-selector').keyup(function() {
        //addChoiceRow(true, 1, '', '', true, 0, 0, 0);

        var val = $(this).val();

        $(this).parent().css('position', 'relative');
        $('ul.autocomplete').addClass('obsolete');
        var ac = $('<ul class="autocomplete" />');

        $.getJSON('./catalog/product/find_product?token=' + sessionToken + '&product=' + val, function(result) {

            $.each(result, function(label, info) {
                var li = $('<li />');
                var a = $('<a />').text(label);

                a.click(function() {

                    // Add ID and label
                    $('#stock-id-selector').val(label).data('selected-option', label);
                    $('#stock-id').val(info.id);

                    ac.remove();
                })

                a.appendTo(li);
                li.appendTo(ac);
            });

            $('#stock-id-selector').after(ac);
            $('.obsolete').remove();
        });
    });

    $('#stock-id-selector').blur(function() {
        if ($(this).val() != $(this).data('selected-option')) {
            $(this).val('');
            $('#stock-id').val('');
        }

        setTimeout(function() { $('ul.autocomplete').remove() }, 200);
    });
})

function updateSeo() {
    $('.google-example').each(function() {
        var lang = $(this).data('lang-id');

        $('.ge-title', $(this)).html($('#ge-title-' + lang).val());
        $('.ge-description', $(this)).html($('#ge-description-' + lang).val());
        $('.ge-url', $(this)).html($('#ge-url-' + lang).val());
    });
}

function addChoiceRow(valueID, active, label, stock, use_stock, price, points, weight)
{
    $('.table-option').show();

    active = parseInt(active);
    use_stock = parseInt(use_stock);

    // Active
    var elActive = $('<div class="switch switch-small" data-on-label="AAN" data-off-label="UIT" />');
    $('<input type="checkbox" name="active[]" />').prop('checked', active).appendTo(elActive);
    elActive.bootstrapSwitch();

    /*if (active == 1) {
        elActive.bootstrapSwitch('setState', true);
    } else {
        elActive.bootstrapSwitch('setState', false);
    }*/

    // Label
    var elLabel = $('<div />');

    $.each(languages, function(k, v) {
        elLabelCont = $('<div class="input-group lang-block lang-' + v.language_id + '" />');

        if (v.is_default) {
            elLabelCont.addClass('lang-active');
        }

        var labelValue = label[v.language_id] == undefined ? '' : label[v.language_id];

        elLabelFlag = $('<img src="view/img/flags/' + v.image + '" alt="' + v.name + '" />');
        elLabelAddon = $('<div class="input-group-addon" />');
        elLabelInp = $('<input type="text" name="label[]" class="form-control" value="' + labelValue + '" data-lang-id="' + v.language_id + '" />');

        if (valueID) {
            elLabelInp.attr('disabled', true);
        }

        elLabelAddon.append(elLabelFlag);
        elLabelCont.append(elLabelAddon);
        elLabelCont.append(elLabelInp);

        elLabelCont.appendTo(elLabel);
    })

    // Stock
    var elStock = $('<input type="text" name="quantity[]" class="form-control" value="' + stock + '" />');

    // Use stock
    var elUseStock = $('<div class="switch switch-small" data-on-label="AAN" data-off-label="UIT" />');
    $('<input type="checkbox" name="subtract[]" />').prop('checked', use_stock).appendTo(elUseStock);
    elUseStock.bootstrapSwitch();

    /*if (use_stock == 1) {
        elUseStock.bootstrapSwitch('setState', true);
    } else {
        elUseStock.bootstrapSwitch('setState', false);
    }*/

    // Price
    var elPrice = $('<input type="text" name="price[]" class="form-control" />').val(Math.abs(price));
    var elPriceSelector = $('<select name="price_prefix[]" class="form-control" />');
    $('<option />').val('+').html('+').appendTo(elPriceSelector);
    $('<option />').val('-').html('-').appendTo(elPriceSelector);

    if (price >= 0) {
        elPriceSelector.val('+');
    } else {
        elPriceSelector.val('-');
    }

    // Weight
    var elWeight = $('<input type="text" name="weight[]" class="form-control" />').val(Math.abs(weight));
    var elWeightSelector = $('<select name="weight_prefix[]" class="form-control" />');
    $('<option />').val('+').html('+').appendTo(elWeightSelector);
    $('<option />').val('-').html('-').appendTo(elWeightSelector);

    if (weight >= 0) {
        elWeightSelector.val('+');
    } else {
        elWeightSelector.val('-');
    }

    var row = $('<tr />');

    if (valueID !== false) {
        row.data('value-id', valueID);
    }

    $('<td />').append(elActive).appendTo(row);
    $('<td />').append(elLabel).appendTo(row);
    $('<td />').append(elStock).appendTo(row);
    $('<td />').append(elUseStock).appendTo(row);
    $('<td style="width: 55px; padding-right: 0;" />').append(elPriceSelector).appendTo(row);
    $('<td />').append(elPrice).appendTo(row);
    $('<td style="width: 55px; padding-right: 0;" />').append(elWeightSelector).appendTo(row);
    $('<td />').append(elWeight).appendTo(row);
    $('<td style="width: 20px; padding-top: 17px;"><a href="#delete-choice"><i style="font-size: 14px;" class="fa fa-trash-o"></i></a></td>').appendTo(row);

    row.appendTo($('.table-option tbody'));
}

$(document).on('click', '.remove-info', function() {
    var block = $(this).closest('.product-info');

    if ($('.product-info').length <= 1) {
        // Clear
        $('.product-info select').attr('disabled', false);
        $('.product-info input[type=text]').val('');
        $('.product-info input[type=checkbox]').attr('checked', false);

        return false;
    } else {
        // Give back this option to all product info selects
        infoTypes[$('select option:selected', block).val()] = 1;
    }

    block.remove();

    // Refill all options
    $('.product-info select').each(function() {
        var elem = $(this);

        if (!elem.attr('disabled')) {
            elem.empty();

            $.each(infoTypes, function(type, visible) {
                if (visible) {
                    elem.append($('<option />').val(type).html(type.toUpperCase()));
                }
            });
        }
    });

    return false;
})

$(document).on('click', '.remove-discount', function() {
    if ($('tr', $(this).closest('tbody')).length <= 1) {
        $('#discount-placeholder').show();
        $('#discount-table').hide();
    }

    $(this).closest('tr').remove();

    return false;
})

$(document).on('change', '.shop-selector', function() {
    // Refill categories!
    var shop = $(this).val(),
    catElem = $(this).parent().parent().find('.category-selector');

    if (productCategories[shop] != undefined) {
        $('option:gt(0)', catElem).remove();

        if (productCategories[shop].length > 0) {
            $.each(productCategories[shop], function(k, v) {
                catElem.append('<option value="' + v['id'] + '">' + v['name'] + '</option>');
            });
        }
    }
})

$(document).on('click', 'a[href="#remove-category"]', function() {
    $(this).closest('.category-row').remove();

    return false;
 });

$(document).on('click', 'a[href="#remove-attribute"]', function() {
    $(this).closest('.attribute-row').remove();

    return false;
 });

$('#categoryModal').on('show.bs.modal', function (e) {
    // 1: Any categories in cat-box?
    // 2: Any categories selected in simple-view?
    // 3: Show empty dropdowns
    var catBox = $('ul#category-box'),
        i = 0;

    if ($('li', catBox).length > 0) {
        $('li', catBox).each(function() {
            var shopID = $(this).data('shop-id'),
                catID = $(this).data('category-id');

            if (i == 0) {
                var parent = $('#categoryModal');
            } else {
                // Trigger extra category, trigger selects
                $('a[href="#extra-category"]').click();

                var parent = $('#categoryModal .row:last-child');
            }

            // Trigger selects
            $('.shop-selector option', parent).attr('selected', false);
            $('.shop-selector', parent).val(shopID);
            $('.shop-selector option[value="' + shopID + '"]', parent).attr('selected', true);
            $('.shop-selector', parent).change();

            $('.category-selector option', parent).attr('selected', false);
            $('.category-selector', parent).val(catID);
            $('.category-selector option[value="' + catID + '"]', parent).attr('selected', true);

            i++;
        });
    }
    else if ($('#shop option:selected').val() != '' || $('#category option:selected').val() != '') {
        var shopID = $('#shop option:selected').val(),
            catID = $('#category option:selected').val();

        // Trigger selects
        $('#categoryModal .shop-selector option').attr('selected', false);
        $('#categoryModal .shop-selector option[value="' + shopID + '"]').attr('selected', true);
        $('#categoryModal .shop-selector').change();

        $('#categoryModal .category-selector option').attr('selected', false);
        $('#categoryModal .category-selector option[value="' + catID + '"]').attr('selected', true);
    }
    else {
        // Nothing to do
    }
})

$('#categoryModal').on('hidden.bs.modal', function (e) {
    // Reset modal, we'll set it again each time it gets shown
    $('#categoryModal .row:nth-child(n+2)').remove();

    $('#categoryModal .shop-selector option').attr('selected', false);
    $('#categoryModal .shop-selector option[value=""]').attr('selected', true);
    $('#categoryModal .shop-selector').change();
})

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var regex = new RegExp("\#seo$");

    if (regex.test(e.target)) {
        // Ok, prefill names & urls
        $('input[name^=seo_name]').each(function() {
            var langID = $(this).attr('name').replace('seo_name[', '').replace(']', ''),
                categoryID = $('#category-box li:first-child').data('category-id'),
                storeID = $('#category-box li:first-child').data('shop-id'),
                name = $('input[name="product_description[' + langID + '][name]"]').val();

                // Fallback to page title
            if ($('input[name="product_description[' + langID + '][title]"]').val() == '') {
                $('input[name="product_description[' + langID + '][title]"]').val(name);
            }

            $.getJSON('./catalog/product/preview_url?token=' + sessionToken + '&category_id=' + categoryID + '&store_id=' + storeID + '&language_id=' + langID + '&name=' + encodeURIComponent(name), function(data) {
                    $('input[name="product_description[' + langID + '][keyword]"]').val(data);

                    // Yeah yeah.. a bit dirty, but we need to update the URL
                    $('input[name="product_description[' + langID + '][title]"]').keyup();
                });

            $(this).val(name);
        });

        $('textarea[name$="meta_description]"]').keyup();
    }
});

$('#save-categories').on('click', function() {
    // Save categories in catbox
    var catBox = $('ul#category-box');

    // Remove existing categories
    catBox.empty();

    $('#categoryModal .row').each(function() {
        var shopElem = $('.shop-selector option:selected', $(this)),
            catElem = $('.category-selector option:selected', $(this));

        if (shopElem.val() != '' && catElem.val() > 0) {
            // Append!
            catBox.append($('<li class="category-row" />').html('\
                <a href="#remove-category" class="pull-right"><i class="fa fa-times-circle"></i></a>\
                <strong>' + shopElem.html() + ':</strong>\
                ' + catElem.html() + '\
                <input type="hidden" name="product_store[]" value="' + shopElem.val() + '" />\
                <input type="hidden" name="product_category[]" value="' + catElem.val() + '" />').data({'shop-id': shopElem.val(), 'category-id': catElem.val()}));
        }
    });

    // Close modal
    $('#categoryModal').modal('hide');
});

$('#save-attribute').on('click', function() {
    var formData = $('#attribute-form').serialize();

    $.post('catalog/attribute/insert_ajax?token=' + sessionToken, formData, function(data) {
        console.log(data);

        if (data.error != undefined) {
            // Something went wrong
            alert(data.error);
        } else {
            // Assume all is good
            var attributes = '';

            $.each(data["attributes"], function(k, v) {
                attributes += '<label class="checkbox-inline"><input style="margin-top: 3px;" checked="checked" type="checkbox" name="attribute[]" value="' + v.attribute_id + '" /> ' + v.name + '</label>';
            });

            $('#attributes tbody').append('<tr><td><strong>' + data.group + ':</strong></td><td>' + attributes + '</td></tr>');
            $('#attributes table').show();
            $('#no-attributes').remove();

            $('#attributeModal').modal('hide');
        }
    }, 'json');
})

$(document).on('click', 'a[href=#delete-option]', function() {
    if ($('tr', $(this).closest('tbody')).length <= 1) {
        $('#product-options').hide();
        $('#product-options-intro').show();
    }

    $(this).closest('tr').remove();

    return false;
});

$(document).on('click', 'a[href="#delete-choice"]', function() {
    $(this).closest('tr').remove();

    return false;
});

$(document).on('click', 'a[href=#edit-option]', function() {
    // Clear table and such
    $('.table-option tbody tr').empty();
    $('.table-option').hide();
    $('input[name^=option_name_]').val('');

    var row = $(this).closest('tr'),
        optionType = $('input[name$="[type]"]', row),
        optionIndex = optionType.attr('name').replace('product_option[', '').replace('][type]', '');

    $('#add-option').data('row-index', row.index());

    // How many choices for this option? (if any at all)
    if (optionType.val() == 'select' || optionType.val() == 'radio' || optionType.val() == 'checkbox' || optionType.val() == 'image') {
        // Go on..
        $('input[name$="[quantity]"]', row).each(function(k, v) {
            var choiceIndex   = $(this).attr('name').replace('product_option[' + optionIndex + '][product_option_value][', '').replace('][quantity]', ''),
                choicePrefix  = 'product_option[' + optionIndex + '][product_option_value][' + choiceIndex + ']',
                cActive       = $('input[name="' + choicePrefix + '[active]"]').val(),
                cStock        = $('input[name="' + choicePrefix + '[quantity]"]').val(),
                cUseStock     = $('input[name="' + choicePrefix + '[subtract]"]').val(),
                cPrice        = $('input[name="' + choicePrefix + '[price]"]').val(),
                cPricePrefix  = $('input[name="' + choicePrefix + '[price_prefix]"]').val(),
                cWeight       = $('input[name="' + choicePrefix + '[weight]"]').val();
                cWeightPrefix = $('input[name="' + choicePrefix + '[weight_prefix]"]').val();

            var cLabel = new Object;

            // Get us some labels
            $.each(languages, function(k, v) {
                cLabel[v.language_id] = $('input[name="' + choicePrefix + '[option_value_description][' + v.language_id + '][name]"]').val();
            });

            if (cWeightPrefix == '-') {
                cWeight = 0 - cWeight;
            }

            if (cPricePrefix == '-') {
                cPrice = 0 - cPrice;
            }

            addChoiceRow(0, cActive, cLabel, cStock, cUseStock, cPrice, '', cWeight);
        });

        $('#extra-option-choice').show();
    }

    // Add option labels
    $.each(languages, function(k, v) {
        var optionLabel = $('input[name="product_option[' + optionIndex + '][option_description][' + v.language_id + '][name]"]', row).val();

        // Fill new-option fields
        $('#option_name_' + v.language_id).val(optionLabel);
    });

    // Set option type
    $('#option-type option').prop('selected', false);
    $('#option-type option[value="' + optionType.val() + '"]').prop('selected', true);
    $('#option-type').val(optionType.val());

    return false;
});

$(document).on('click', '#add-option', function() {
    // Add option to product
    optionValueCount = 0;

    // Existing option? Incorporate ID
    var formValues = '',
        optionValues = new Array,
        defaultLangID = 0,
        optionType = $('#option-type option:selected').html(),
        optionTypeID = $('#option-type option:selected').val();

    formValues += '<input type="hidden" name="product_option[' + optionCount + '][type]" value="' + optionTypeID + '" />';
    formValues += '<input type="hidden" name="product_option[' + optionCount + '][active]" value="1" />';

    $.each(languages, function(k, v) {
        var lbl = $('input[name=option_name_' + v.language_id + ']').val();

        if (v.is_default) {
            optionLabel = lbl;
            defaultLangID = v.language_id;
        }

        formValues += '<input type="hidden" name="product_option[' + optionCount + '][option_description][' + v.language_id + '][name]" value="' + lbl + '" />';
    });

    // Add all values from table-option row
    if ($('.table-option').is(':visible')) {
        $('.table-option tbody tr').each(function() {
            var row = $(this);

            $(':input', row).each(function() {
                var name = $(this).attr('name').replace('[', '').replace(']', ''),
                    skip = false,
                    val = $(this).val();

                // Checkbox checked?
                if ($(this).attr('type') == 'checkbox') {
                    if (!$(this).parent().bootstrapSwitch('status') || !$(this).prop('checked')) {
                        val = 0;
                    } else {
                        val = 1;
                    }
                }

                if (name == 'label') {
                    name = 'option_value_description][' + $(this).data('lang-id') + '][name';

                    if ($(this).data('lang-id') == defaultLangID) {
                        optionValues.push(val);
                    }
                }

                if (!skip) {
                    // Add input
                    formValues += '<input type="hidden" name="product_option[' + optionCount + '][product_option_value][' + optionValueCount + '][' + name + ']" value="' + val + '" />';
                }
            });

            optionValueCount++;
        });
    }

    // Add table row
    if ($('#add-option').data('row-index') != undefined) {
        $('#product-options tbody tr:nth-child(' + ($('#add-option').data('row-index') + 1) + ')').remove();
        $('#add-option').removeData('row-index');
    }

    $('#product-options tbody').append('<tr>\
        <td>' + optionLabel + '</td>\
        <td>' + optionType + '</td>\
        <td>' + optionValues.join(', ') + '</td>\
        <td class="right">' + formValues + '\
        <a href="#edit-option" class="btn btn-xs btn-secondary">' + editButton + '</a>\
        <a href="#delete-option" class="btn btn-xs btn-primary">' + deleteButton + '</a></td></tr>');

    $('#product-options').show();
    $('#product-options-intro').hide();

    // Clear table and such
    $('.table-option tbody tr').empty();
    $('.table-option').hide();
    $('input[name^=option_name_]').val('');

    $('#option-type').val('');
    $('#extra-option-choice').hide();

    optionCount++;
});

$('#table-related-product').on('click', 'a[href=#delete]', function() {
    $(this).closest('tr').remove();

    if ($('#table-related-product tr').length <= 0) {
        $('#table-related-product').hide();
    }

    return false;
});

$('#table-downloads').on('click', 'a[href=#delete]', function() {
    $(this).closest('tr').remove();

    if ($('#table-downloads tr').length <= 0) {
        $('#table-downloads').hide();
    }

    return false;
});

window.ParsleyValidator.addValidator('requiredifradio', function(value, requirement) {
    requirement = requirement.split(',');
    if ($('input[name=' + requirement[0] + '][value=' + requirement[1] + ']').is(':checked') && value == '') {
        return false;
    }
    return true;
});

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator, currencySymbol) {
    // check the args and supply defaults:
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
    decSeparator = decSeparator == undefined ? "." : decSeparator;
    thouSeparator = thouSeparator == undefined ? "," : thouSeparator;
    currencySymbol = currencySymbol == undefined ? "€" : currencySymbol;

    // I'll probably burn in hell for this, but JavaScripts idiotic 
    // way of handling floating points forces us to do it this way
    var n = this.toFixed(decPlaces + 1);

    if (n.substr(n.length - 1, 1) == '5') {
        n = n.substr(0, n.length - 1) + '6';
    }

    n = parseFloat(n);

    var sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;

    return sign + currencySymbol + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};