var showTax = true,
    totalProductWeight = 0,
    totalProductValue = 0,
    totalProductQuantity = 0,
    shippingName = shippingPrice = shippingTax = shippingTotal = '',
    paymentName = paymentPrice = paymentTax = paymentTotal = '',
    paymentOptions = shippingOptions = {};

$('input[name="customer[customer_id]"]').on('change', function() {
    if (!$(this).val() || $(this).val() == '') {
        $('#url_to_customer').hide();
    }
    else {
        $('#url_to_customer').show();
    }
}).trigger('change');

$(function() {
    // If customerID is not empty on page load, it means we're probably editing an
    // existing order
    if ($('input[name="customer[customer_id]"]').val() != '') {
        // Set shipping
        shippingPrice = $('#shipping_method_price').val();
        shippingTax   = $('#shipping_method_tax_percentage').val();
        shippingTotal = $('#shipping_method_total').val();
        shippingName  = $('#shipping_method_name').val();

        fillShippingOptions();

        // Set payment
        paymentPrice = $('#payment_method_price').val();
        paymentTax   = $('#payment_method_tax_percentage').val();
        paymentTotal = $('#payment_method_total').val();
        paymentName  = $('#payment_method_name').val();

        fillPaymentOptions();
    }

    buildSummary();
});

$('.pc-api').blur(function() {
    var cont = $(this).closest('.tab-pane');

    var pc = $('input[name$="[postcode]"]', cont).val().replace(' ', ''),
        nm = $('input[name$="[number]"]', cont).val(),
        ct = $('select[name$="[country_id]"]', cont).val();

    if (ct == 150 && pc.match(/^[0-9]{4}[a-z]{2}/i) && nm.length > 0) {
        $.getJSON('./common/pc?token=' + sessionToken + '&q=' + pc + '/' + nm, function(resp) {
            $('input[name$="[postcode]"]', cont).val(resp.resource.postcode);
            $('input[name$="[address_1]"]', cont).val(resp.resource.street);
            $('input[name$="[city]"]', cont).val(resp.resource.town);

            // Set province
            var zone = resp.resource.province;

            if ($('select[name$="[zone_id]"] option:contains("' + zone + '")', cont).length) {
                $('select[name$="[zone_id]"]', cont).val($('select[name$="[zone_id]"] option:contains("' + zone + '")', cont).val())
            }
            else if ($('select[name$="[zone_id]"] option:contains("' + zone.replace('-', ' ') + '")', cont).length) {
                $('select[name$="[zone_id]"]', cont).val($('select[name$="[zone_id]"] option:contains("' + zone.replace('-', ' ') + '")', cont).val())
            }
        });
    }
});

$('#order_form').submit(function() {
    // Make sure all products are added
    $('a[rel=add-product]').trigger('click');

    // Re-build summary
    buildSummary();
});

$('#coupon_code').blur(function() {
    var couponCode = $(this).val(),
        customerID = $('input[name$="[customer_id]"]').val(),
        products = {product: []};

    $('input[name$="[product_id]"]').each(function() {
        if ($(this).val() != '') {
            products.product.push($(this).val());
        }
    });

    // Get total product value
    totalProductValue = 0.0;

    $('#product-list tr').each(function() {
        var total = parseFloat($('input[name$="[total]"]', $(this)).val());

        totalProductValue += total;
    });

    // Get coupon info
    $.getJSON('./sale/orders/get_coupon_info?order_id=' + order_id + '&coupon_code=' + couponCode + '&totalamount=' + totalProductValue + '&customer_id=' + customerID + '&' + $.param(products) + '&token=' + sessionToken, function(data) {
        // Fill relevant fields
        if (data.coupon_id != undefined) {
            $('input[name="discount[coupon][coupon_id]"]').val(data.coupon_id);
            $('input[name="discount[coupon][value]"]').val(data.discount);
            $('input[name="discount[coupon][type]"]').val(data.type);

            buildSummary();
        }
    });
});

/**
* Find a customer based on the supplied name
*/
$('#find_customer').keyup(function() {
    var elem = $(this),
        val  = elem.val()

    if (val == elem.data('selected-option') || val == '') {
        return;
    }

    $(this).parent().css('position', 'relative');
    var ac = $('<ul class="autocomplete" />');

    ac.css('top', (elem.parent().height() - 1));

    $.getJSON('./sale/customer/autocomplete?full&token=' + sessionToken + '&filter_name=' + val, function(result) {

        // Remove old autocomplete
        $('ul.autocomplete').remove();

        $.each(result, function(label, data) {
            var li = $('<li />');
            var a = $('<a />').text(data['name']);

            a.click(function() {
                //elem.data('product-id', productData['id']);
                elem.val(data['name']);
                $('input[name="customer[customer_id]"]').val(data['customer_id']);

                // Show URL to customer data
                $('#url_to_customer').data('customer-id', data['customer_id']);
                $('#url_to_customer').show();

                if (data['payment_address'] && data['payment_address']['zone_id']) {
                    window.payment_zone_id = data['payment_address']['zone_id'];
                }
                if (data['shipping_address'] && data['shipping_address']['zone_id']) {
                    window.shipping_zone_id = data['shipping_address']['zone_id'];
                }
                $('input[name="customer[firstname]"]').val(data['firstname']);
                $('input[name="customer[middlename]"]').val(data['middlename']);
                $('input[name="customer[lastname]"]').val(data['lastname']);
                $('input[name="customer[email]"]').val(data['email']);
                $('input[name="customer[telephone]"]').val(data['telephone']);
                $('input[name="customer[mobile]"]').val(data['mobile']);
                $('input[name="customer[fax]"]').val(data['fax']);
                $('input[name="customer[birthdate]"]').val(data['birthdate']);

                var selectValues = '<option value="">' + selectDefault + '</option>';
                $.each(data['address'], function(id, address) {
                    selectValues += '<option value="' + id + '">' + address['firstname'] + ' ' + address['lastname'] + ', ' + address['address_1'] + ', ' + address['city'] + ', ' + address['country'] + '</option>';
                })
                $('select[name="customer[payment_address]"]').html(selectValues);
                $('select[name="customer[shipping_address]"]').html(selectValues);
                ac.remove();
            })

            a.appendTo(li);
            li.appendTo(ac);
        });

        elem.after(ac);
    });
});

$('#find_customer').blur(function() {
    if ($(this).val() != $(this).data('selected-option')) {
        $(this).val('');
    }

    setTimeout(function() { $('ul.autocomplete').remove() }, 200);
});

// Redirect user to customer data
$('#url_to_customer').on('click', function(e) {
    e.preventDefault();

    window.location = $(this).find('a').attr('href') + $(this).data('customer-id');
});

$('#shipping_method').on('change', function() {
    var curValue = $(this).val();

    if (shippingOptions[curValue] != undefined) {
        var data = shippingOptions[curValue];
        $('#shipping_method_name').val(data.name).prop('disabled', 0);

        /*alert(data['options'].length);
        alert(data.options[curValue]);
        alert(data.options[curValue].tax);*/

        if (data.options[curValue].rate != undefined && data.options[curValue].tax != undefined) {
            var rate = data.options[curValue].rate;

            if (typeof rate == 'string') {
                rate = rate.replace(',', '.').replace('€', '');
            }

            $('#shipping_method_tax_percentage').val(data.options[curValue].tax);
            $('#shipping_method_price').val(rate).prop('disabled', 0);
        }
        else {
            $('#shipping_method_name').prop('disabled', 1);
            $('#shipping_method_price, #shipping_method_total, #shipping_method_tax_percentage').val('').prop('disabled', 1);
        }
    }

    $('#shipping_method_price').trigger('change');
});

$('#payment_method').on('change', function() {
    var curValue = $(this).val();

    if (paymentOptions[curValue] != undefined) {
        var data = paymentOptions[curValue];
        $('#payment_method_name').val(data.name).prop('disabled', 0);

        // Method has options
        if (data["options"] != undefined) {
            //if (data.options[curValue].rate != undefined && data.options[curValue].tax != undefined) {

            // Is rate a price or a percentage?
            var rate = data.options[curValue].rate;

            if (typeof rate == 'string') {
                rate = rate.replace(',', '.').replace('€', '');
            }

            if (data.options[curValue].rate_type != undefined && data.options[curValue].rate_type == 'P') {
                // Percentage, translate to a price
                //rate = totalProductValue / 100 * parseFloat(rate);
                rate = totalProductValue / (1 - (parseFloat(rate) / 100)) - totalProductValue;

                // Reduce with tax amount?
                if (data.options[curValue].tax != undefined) {
                    rate = rate / (1 + parseFloat(data.options[curValue].tax) / 100);
                }
            }

            $('#payment_method_tax_percentage').val(data.options[curValue].tax);
            $('#payment_method_price').val(rate).prop('disabled', 0);
            //}
        }
        else {
            $('#payment_method_name').prop('disabled', 1);
            $('#payment_method_price, #payment_method_total, #payment_method_tax_percentage').val('').prop('disabled', 1);
        }

        $('#payment_method_price').trigger('change');
    }
});

// Calculate incl tax price
$('#shipping_method_price').on('change', function() {
    var price = parseFloat(parseFloat($('#shipping_method_price').val()) + (parseFloat($('#shipping_method_price').val()) / 100 * parseFloat($('#shipping_method_tax_percentage').val())));
    $('#shipping_method_total').val(price.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left))
});

$('#payment_method_price').on('change', function() {
    var price = parseFloat(parseFloat($('#payment_method_price').val()) + (parseFloat($('#payment_method_price').val()) / 100 * parseFloat($('#payment_method_tax_percentage').val())));
    $('#payment_method_total').val(price.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left))
});

/**
* Add new product to product row
*/
$('#new_product_name').keyup(function() {
    var elem = $(this),
        val  = elem.val()

    if (val == elem.data('selected-option') || val == '') {
        return;
    }

    $(this).parent().css('position', 'relative');
    var ac = $('<ul class="autocomplete" />');

    ac.css('top', (elem.parent().height() - 1));

    $.getJSON('./catalog/product/find_product?token=' + sessionToken + '&product=' + val, function(result) {

        // Remove old autocomplete
        $('ul.autocomplete').remove();

        $.each(result, function(label, productData) {
            var li = $('<li />');
            var a = $('<a />').text(label);

            a.click(function() {
                //elem.data('product-id', productData['id']);
                elem.val(label);
                elem.data('selected-option', label);

                var price = parseFloat(productData['price']) * (1 + (parseFloat(productData['tax']) / 100));

                $('#new_product_id').val(productData['id']);
                $('#new_product_model').val(productData['model']);
                $('#new_product_price').val(price.formatMoney(decimal_place, thousand_point, decimal_point, ''));
                $('#new_product_price').data('raw-value', productData['price']);
                $('#new_product_tax').val(productData['tax']);
                $('#new_product_weight').val(productData['weight']);

                ac.remove();
            })

            a.appendTo(li);
            li.appendTo(ac);
        });

        elem.after(ac);
    });
});

$('#new_product_name').blur(function() {
    if ($(this).val() != $(this).data('selected-option')) {
        $(this).val('');
    }

    setTimeout(function() { $('ul.autocomplete').remove() }, 200);
});

$('#new_product_quantity').blur(function() {
    var price    = parseFloat($('#new_product_price').val().replace(',', '.')),
        quantity = $(this).val(),
        tax      = parseFloat($('#new_product_tax').val());

    if (isNaN($(this).val()) || isNaN(price)) {
        return;
    }

    // Calculate prices
    var productTotal = price * quantity,
        productTotal = parseFloat(productTotal);

    //$('#new_product_total').data('raw-value', productTotal);
    $('#new_product_total').val(productTotal.formatMoney(decimal_place, thousand_point, decimal_point, ''));
    //$('#new_product_tax').val((tax * (price / 100) * quantity).toFixed(4));
});

$('#new_product_total').blur(function() {
    var productPriceTotal = $(this).val().replace(',', '.');


    if (isNaN($(this).val())) {
        return;
    }

    if (isNaN($('#new_product_quantity').val())) {
        $('#new_product_quantity').val(1);
    }

    // Recalculate piece-price
    var productPriceTotal = parseFloat(productPriceTotal),
        productPrice = parseFloat(productPriceTotal / $('#new_product_quantity').val());

    $('#new_product_price').val(productPrice.formatMoney(decimal_place, thousand_point, decimal_point, ''));
    $(this).val(productPriceTotal.formatMoney(decimal_place, thousand_point, decimal_point, ''));
});

$('#new_product_price').blur(function() {
    var productPrice = $(this).val().replace(',', '.');

    if (isNaN(productPrice)) {
        return;
    }

    if (isNaN($('#new_product_quantity').val())) {
        $('#new_product_quantity').val(1);
    }

    // Recalculate piece-price
    var productPrice = parseFloat(productPrice),
        productPriceTotal = parseFloat(productPrice * $('#new_product_quantity').val());

    $('#new_product_total').val(productPriceTotal.formatMoney(decimal_place, thousand_point, decimal_point, ''));
    $(this).val(productPrice.formatMoney(decimal_place, thousand_point, decimal_point, ''));
});

$('a[rel=add-product]').click(function() {
    var elem = $(this);

    // Do some basic error checking
    if ($('#new_product_name').val() == '' ||
        isNaN($('#new_product_tax').val()) ||
        isNaN($('#new_product_price').val().replace(',', '.')) ||
        isNaN($('#new_product_quantity').val()) ||
        isNaN($('#new_product_id').val())) {
        return;
    }

    if ($('#new_product_quantity').val() == '') {
        $('#new_product_quantity').val(1);
        $('#new_product_quantity').blur();
    }
    var new_product_price_raw = $('#new_product_price').val().replace(',', '.'),
        new_product_total_raw = $('#new_product_price').val().replace(',', '.') * $('#new_product_quantity').val(),
        row = elem.closest('tr').clone();

    $('input[type=text]', row).each(function() {
        // Add value as text to <td>
        if ($(this).parent().is('div')) {
            $(this).unwrap();
        }

        $(this).attr('type', 'hidden');

        if ($(this).attr('name') == 'new_product[price]') {
            $(this).closest('td').prepend(symbol_left + $(this).val());
            // Store as ex-tax price
            $(this).val(new_product_price_raw / (1 + ($('#new_product_tax').val() / 100)));
        }
        else if ($(this).attr('name') == 'new_product[total]') {
            $(this).closest('td').prepend(symbol_left + $(this).val());
            // Store as ex-tax price
            $(this).val(new_product_total_raw / (1 + ($('#new_product_tax').val() / 100)));
        }
        else {
            $(this).closest('td').prepend($(this).val());
        }
    });

    $('.input-group-addon', row).remove();

    // Change names
    $(':input', row).each(function() {
        $(this).attr('name', $(this).attr('name').replace('new_product', 'lines[' + productCount + ']'));
        $(this).attr('id', '');
    });

    $('a', row).attr('rel', 'remove-product');
    $('a i', row).removeClass('fa-plus-circle').addClass('fa-minus-circle');

    row.data('order-product-row', productCount);

    $('#product-list').append(row);

    productCount++;

    // Clear all inputs
    $(':input', elem.closest('tr')).val('').removeData();

    return false;
});

// Remove a product row
$(document).on('click', 'a[rel=remove-product]', function() {
    $(this).closest('tr').remove();
})

/**
* Prefill payment address
*/
$('select[name="customer[payment_address]"]').on('change', function() {
    $.ajax({
        url: 'sale/customer/address&token=' + sessionToken + '&address_id=' + this.value,
        dataType: 'json',
        success: function(json) {
            $.each(json, function(key, value) {
                $('[name="customer[payment_address][' + key + ']"]').val(value);
            })
            $('select[name="customer[payment_address][country_id]"]').trigger('change');
            setTimeout(function() {
                $('select[name="customer[payment_address][zone_id]"]').val(json['zone_id']);
            }, 250)
        }
    });
});

/**
* Prefill shipping address
*/
$('select[name="customer[shipping_address]"]').on('change', function() {
    $.ajax({
        url: 'sale/customer/address&token=' + sessionToken + '&address_id=' + this.value,
        dataType: 'json',
        success: function(json) {
            $.each(json, function(key, value) {
                $('[name="customer[shipping_address][' + key + ']"]').val(value);
            })
            $('select[name="customer[shipping_address][country_id]"]').trigger('change');
            setTimeout(function() {
                $('select[name="customer[shipping_address][zone_id]"]').val(json['zone_id']);
            }, 250)
        }
    });
});

/**
* Fill zones and change payment-options
*/
$('select[name="customer[payment_address][country_id]"]').on('change', function() {
    var elem = $('#payment_zone_id');

    $.ajax({
        url: 'sale/order/country&token=' + sessionToken + '&country_id=' + this.value,
        dataType: 'json',
        success: function(json) {
            if (json['zone'] == undefined) {
                return;
            }

            if (json['postcode_required'] == '1') {
                // Gritter postcode required?
            }

            if (json['zone'].length > 0) {
                $('option', elem).remove();

                elem.append($('<option value="">Maak een keuze</option>'));

                $.each(json['zone'], function(k, v) {
                    var opt = $('<option />').val(v.zone_id).html(v.name);

                    if (payment_zone_id == v.zone_id) {
                        opt.attr('selected', true);
                    }

                    opt.appendTo(elem);
                });
            } else {
                elem.html('<option value="">Geen</option>');
            }
        }
    });
}).trigger('change');

/**
* Fill zones and change shipping-options
*/
$('select[name="customer[shipping_address][country_id]"]').on('change', function() {
    var elem = $('#shipping_zone_id');

    $.ajax({
        url: 'sale/order/country&token=' + sessionToken + '&country_id=' + this.value,
        dataType: 'json',
        success: function(json) {
            if (json['zone'] == undefined) {
                return;
            }

            if (json['postcode_required'] == '1') {
                // Do something?
            }

            if (json['zone'].length > 0) {
                $('option', elem).remove();

                elem.append($('<option value="">Maak een keuze</option>'));

                $.each(json['zone'], function(k, v) {
                    var opt = $('<option />').val(v.zone_id).html(v.name);

                    if (payment_zone_id == v.zone_id) {
                        opt.attr('selected', true);
                    }

                    opt.appendTo(elem);
                });
            } else {
                elem.html('<option value="">Geen</option>');
            }
        }
    });
}).trigger('change');

$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    var totalRegex = new RegExp("\#total$"),
        paymentRegex = new RegExp("\#payment-method$"),
        shippingRegex = new RegExp("\#shipping-method$");

    if (paymentRegex.test(e.target)) {
        fillPaymentOptions();
    }
    else if (shippingRegex.test(e.target)) {
        fillShippingOptions();
    }

    console.log(paymentOptions);

    // Always try to add the 'new' product and rebuild the summary
    $('a[rel=add-product]').trigger('click');
    buildSummary();
});

// This may be removed in the future
window.preventSubmitForm = false;
$('form').on('submit', function(){
    if (window.preventSubmitForm) {
        return false;
    }
});

// Re-build the summary-table
$('#discount, #points').blur(function() {
    buildSummary();
});

$('input[name="discount[type]"]').click(function() {
    buildSummary();
});

function buildSummary()
{
    var products = new Array,
        taxes = new Object,
        subTotal = 0,
        otIndex = 0;

    // Find all products
    $('#product-list tr').each(function() {
        var elem = $(this);

        productData = {
            'name': $('input[name$="[name]"]', elem).val(),
            'model': $('input[name$="[model]"]', elem).val(),
            'price': $('input[name$="[price]"]', elem).val(),
            'quantity': $('input[name$="[quantity]"]', elem).val(),
            'tax_percentage': $('input[name$="[tax]"]', elem).val()
        }

        products.push(productData)
    });

    // Add all products to overview
    $('#summary-product-list').empty();

    for (product in products) {
        var product = products[product],
            productRow = $('<tr />'),
            price = parseFloat(product.price * (1 + (product.tax_percentage / 100))),
            total = parseFloat(price * product.quantity),
            taxAmount = total - (product.price * product.quantity);

        // Remove extra zeros from tax-percentage
        product.tax_percentage = parseFloat(product.tax_percentage);

        productRow.append('<td>' + product.name + '</td>');
        productRow.append('<td>' + product.model + '</td>');
        productRow.append('<td class="right">' + product.quantity + '</td>');
        productRow.append('<td class="right">' + price.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + '</td>');
        productRow.append('<td class="right">' + total.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + '</td>');

        // Show to user
        $('#summary-product-list').append(productRow);

        // Add tax amount
        if (taxes[product.tax_percentage] == undefined) {
            taxes[product.tax_percentage] = taxAmount;
        }
        else {
            taxes[product.tax_percentage] += taxAmount;
        }

        subTotal += total;
    }

    /**
    * Build totals
    */
    $('#summary-totals tr').remove();

    // Add subtotal
    var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_SUBTOTAL" />\
                       <input type="hidden" name="totals[' + otIndex + '][value]" value="' + subTotal + '" />';

    $('#summary-totals').append('<tr>\
        <td colspan="4" class="right">' + textOTSubtotal + ':</td>\
        <td class="right">' + subTotal.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</td>\
    </tr>');

    otIndex++;

    // Show points
    var points = $('#points').val(),
        subTotalOnePercent = subTotal / 100;

    if (points > 0) {
        // Translate points to fixed value
        var pointsValue = points * pointValue,
            pointsPercentage = parseFloat(pointsValue / subTotalOnePercent);

        // Apply points discount percentage on all taxes
        for (taxPercentage in taxes) {
            taxes[taxPercentage] = (taxes[taxPercentage] * (1 - (pointsPercentage / 100)));
        }

        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_POINTS" />\
                           <input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + points + '" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="-' + pointsValue + '" />';

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">' + textOTPointsInj.replace('%s', points) + ':</td>\
            <td class="right">- ' + pointsValue.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</td>\
        </tr>');

        subTotal -= pointsValue;
    }

    // Show discount
    var discount = $('#discount').val(),
        discountPercentage = parseFloat(discount),
        discountValue = parseFloat(discount),
        subTotalOnePercent = subTotal / 100,
        discountType = $('input[name="discount[type]"]:checked').val();

    // Fixed amount? Translate to percentage for correct tax-calculation.
    if (discountType == 'F') {
        discountPercentage = parseFloat(discount / subTotalOnePercent);
    } else {
        discountValue = subTotalOnePercent * discountPercentage;
    }

    if (discount > 0) {
        // Apply discount percentage on all taxes
        for (taxPercentage in taxes) {
            taxes[taxPercentage] = (taxes[taxPercentage] * (1 - (discountPercentage / 100)));
        }

        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_DISCOUNT" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="-' + discountValue + '" />',
            label = '';

        if (discountType == 'P') {
            inputFields += '<input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + discountPercentage + '%" />';
            label = textOTDiscountInj.replace('%s', discountPercentage).replace('%%', '%');
        } else {
            label = textOTDiscount;
        }

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">' + label + ':</td>\
            <td class="right">- ' + discountValue.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</td>\
        </tr>');

        subTotal -= discountValue;
    }

    // Show coupon
    var coupon = $('input[name="discount[coupon][value]"]').val(),
        couponCode = $('input[name="discount[coupon][code]"]').val(),
        couponPercentage = parseFloat(coupon),
        couponValue = parseFloat(coupon),
        subTotalOnePercent = subTotal / 100,
        couponType = $('input[name="discount[coupon][type]"]').val();

    // Fixed amount? Translate to percentage for correct tax-calculation.
    if (couponType == 'F') {
        couponPercentage = parseFloat(coupon / subTotalOnePercent);
    } else {
        couponValue = subTotalOnePercent * couponPercentage;
    }

    if (coupon > 0) {
        // Apply discount percentage on all taxes
        for (taxPercentage in taxes) {
            taxes[taxPercentage] = (taxes[taxPercentage] * (1 - (couponPercentage / 100)));
        }

        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_COUPON" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="-' + couponValue + '" />',
            inj = '';

        if (couponType == 'P') {
            inputFields += '<input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + couponCode + ' - ' + couponPercentage + '%" />';
            inj = couponCode + ' - ' + couponPercentage + '%';
        } else {
            inputFields += '<input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + couponCode + '" />';
            inj = couponCode;
        }

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">' + textOTCouponInj.replace('%s', inj) + ':</td>\
            <td class="right">- ' + couponValue.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</td>\
        </tr>');

        subTotal -= couponValue;
    }

    // Add tax-amount for shipping
    var shippingPrice = parseFloat($('#shipping_method_price').val()),
        shippingTax   = parseFloat($('#shipping_method_tax_percentage').val()),
        shippingPrice = shippingPrice * (1 + (shippingTax / 100));

    if (shippingPrice > 0) {
        // Shipping has tax on it, so add the shipping-value before calculating any discounts
        subTotal += shippingPrice;

        if (taxes[shippingTax] == undefined) {
            taxes[shippingTax] = shippingPrice - (shippingPrice / (1 + (shippingTax / 100)));
        }
        else {
            taxes[shippingTax] += shippingPrice - (shippingPrice / (1 + (shippingTax / 100)));
        }
    }

    /*
    if (isNaN(paymentPrice)) {
        totalProductValue = parseFloat(subTotal);
    }
    else {
        totalProductValue = parseFloat(subTotal) - paymentPrice;
    }*/

    totalProductValue = parseFloat(subTotal);
    $('#payment_method').trigger('change');

    // Add tax-amount for payment
    var paymentPrice = parseFloat($('#payment_method_price').val()),
        paymentTax   = parseFloat($('#payment_method_tax_percentage').val()),
        paymentPrice = paymentPrice * (1 + (paymentTax / 100));

    if (paymentPrice > 0) {
        // Shipping has tax on it, so add the shipping-value before calculating any discounts
        subTotal += paymentPrice;

        if (taxes[paymentTax] == undefined) {
            taxes[paymentTax] = paymentPrice - (paymentPrice / (1 + (paymentTax / 100)));
        }
        else {
            taxes[paymentTax] += paymentPrice - (paymentPrice / (1 + (paymentTax / 100)));
        }
    }

    // Add tax-amounts for user
    for (taxPercentage in taxes) {
        var tax = parseFloat(taxes[taxPercentage]);

        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_TAX" />\
                           <input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + taxPercentage + '" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="' + tax + '" />';

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right"><small>' + textOTTaxInj.replace('%s', taxPercentage).replace('%%', '%') + ':</small></td>\
            <td class="right"><small>' + tax.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</small></td>\
        </tr>');
    }

    // Show shipping
    if (shippingPrice > 0) {
        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_SHIPPING" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="' + shippingPrice + '" />';

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">' + textOTShipping + ':</td>\
            <td class="right">' + shippingPrice.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</td>\
        </tr>');
    }

    if (paymentPrice > 0) {
        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_PAYMENT" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="' + paymentPrice + '" />';

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">' + textOTPayment + ':</td>\
            <td class="right">' + paymentPrice.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</td>\
        </tr>');
    }

    // Show total price
    subTotal = parseFloat(subTotal);

    var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_TOTAL" />\
                       <input type="hidden" name="totals[' + otIndex + '][value]" value="' + subTotal + '" />';

    $('#summary-totals').append('<tr>\
        <td colspan="4" class="right"><strong>' + textOTTotal + ':</strong></td>\
        <td class="right"><strong>' + subTotal.formatMoney(decimal_place, thousand_point, decimal_point, symbol_left) + inputFields + '</strong></td>\
    </tr>');
}

function fillPaymentOptions()
{
    var countryID       = $('#payment_country_id').val(),
        storeID         = $('#store_id').val(),
        currentValue    = $('#payment_method').val();

    // Get total product value
    totalProductValue = 0.0;

    $('#product-list tr').each(function() {
        var total = parseFloat($('input[name$="[total]"]', $(this)).val());

        totalProductValue += total;
    });

    $.getJSON('./sale/orders/getMethods?token=' + sessionToken + '&method=2&store_id=' + storeID + '&country_id=' + countryID + '&totalamount=' + totalProductValue, function (resp) {
        var methodsChanged = false;

        // Treat numbers as strings
        for (method in resp) {
            if (paymentOptions[method] == undefined) {
                methodsChanged = true;
                break;
            }

            for (prop in resp[method]) {
                if (prop == "options") {
                    // Go deeper
                    for (opt in resp[method][prop][method]) {
                        if (paymentOptions[method][prop][method][opt] == undefined || paymentOptions[method][prop][method][opt] != resp[method][prop][method][opt]) {
                            methodsChanged = true;
                            // ... wait for it
                            break;
                        }
                    }
                }
                else {
                    if (paymentOptions[method][prop] == undefined || paymentOptions[method][prop] != resp[method][prop]) {
                        methodsChanged = true;
                        // ... wait for it
                        break;
                    }
                }
            }

            // And JUMP!
            if (methodsChanged) {
                break;
            }
        }

        // Lists are the same?
        if (!methodsChanged) {
            return;
        }

        $('#payment_method').html('');

        console.log(resp);

        $.each(resp, function(key, value) {
            $('#payment_method').append('<option value="' + key + '" ' + (value.selected != undefined ? 'selected' : '') + '>' + value.name + '</option>');
        });

        // Anything pre-selected from PHP?
        if (paymentName != '') {
            var preSelectedMethod = $('#payment_method option:contains(' + paymentName + ')');

            // Does it actually still exist?
            if (preSelectedMethod.length) {
                $('#payment_method option').prop('selected', false);
                $('#payment_method').val(preSelectedMethod.val());
                preSelectedMethod.prop('selected', true);

                currentValue = preSelectedMethod.val();
            }
        }

        // Was there a value selected before? (and is that object unchanged?)
        if (currentValue != '' && resp[currentValue] != undefined && JSON.stringify(paymentOptions[currentValue]) == JSON.stringify(resp[currentValue])) {
            $('#payment_method option').prop('selected', false);
            $('#payment_method').val(currentValue);
            $('#payment_method option[value=' + currentValue + ']').prop('selected', true);
        } else {
            // Reset payment method
            $('#payment_method').trigger('change');
        }

        // Store payment options globally
        paymentOptions = resp;
    });
}

function fillShippingOptions()
{
    var countryID       = $('#shipping_country_id').val(),
        storeID         = $('#store_id').val(),
        currentValue    = $('#shipping_method').val();

    // Get total product quantity
    totalProductQuantity = totalProductWeight = 0;

    $('#product-list tr').each(function() {
        var quantity = parseInt($('input[name$="[quantity]"]', $(this)).val()),
            weight   = parseFloat($('input[name$="[weight]"]', $(this)).val());

        totalProductQuantity += quantity;
        totalProductWeight += (weight * quantity * 1000);
    });

    $.getJSON('./sale/orders/getMethods?token=' + sessionToken + '&method=1&store_id=' + storeID + '&country_id=' + countryID + '&weight=' + totalProductWeight + '&totalquantity=' + totalProductQuantity, function (resp) {

        var methodsChanged = false;

        // Treat numbers as strings
        for (method in resp) {
            if (shippingOptions[method] == undefined) {
                methodsChanged = true;
                break;
            }

            for (prop in resp[method]) {
                if (prop == "options") {
                    // Go deeper
                    for (opt in resp[method][prop][method]) {
                        if (shippingOptions[method][prop][method][opt] == undefined || shippingOptions[method][prop][method][opt] != resp[method][prop][method][opt]) {
                            methodsChanged = true;
                            // ... wait for it
                            break;
                        }
                    }
                }
                else {
                    if (shippingOptions[method][prop] == undefined || shippingOptions[method][prop] != resp[method][prop]) {
                        methodsChanged = true;
                        // ... wait for it
                        break;
                    }
                }
            }

            // And JUMP!
            if (methodsChanged) {
                break;
            }
        }

        // Lists are the same?
        if (!methodsChanged) {
            return;
        }

        $('#shipping_method').html('');

        console.log(resp);

        $.each(resp, function(key, value) {
            $('#shipping_method').append('<option value="' + key + '" ' + (value.selected != undefined ? 'selected' : '') + '>' + value.name + '</option>');
        });

        // Anything pre-selected from PHP?
        if (shippingName != '') {
            var preSelectedMethod = $('#shipping_method option:contains(' + shippingName + ')');

            // Does it actually still exist?
            if (preSelectedMethod.length) {
                $('#shipping_method option').prop('selected', false);
                $('#shipping_method').val(preSelectedMethod.val());
                preSelectedMethod.prop('selected', true);

                currentValue = preSelectedMethod.val();
            }
        }

        // Was there a value selected before?
        if (currentValue != '' && resp[currentValue] != undefined && JSON.stringify(shippingOptions[currentValue]) == JSON.stringify(resp[currentValue])) {
            $('#shipping_method option').prop('selected', false);
            $('#shipping_method').val(currentValue);
            $('#shipping_method option[value=' + currentValue + ']').prop('selected', true);
        } else {
            // Reset shipping method
            $('#payment_method').trigger('change');
        }

        // Store sjipping options globally
        shippingOptions = resp;
    });
}

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator, currencySymbol) {
    // check the args and supply defaults:
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
    decSeparator = decSeparator == undefined ? "." : decSeparator;
    thouSeparator = thouSeparator == undefined ? "," : thouSeparator;
    currencySymbol = currencySymbol == undefined ? "$" : currencySymbol;

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
