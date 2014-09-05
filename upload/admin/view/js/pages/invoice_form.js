var productAcOptions = {
    url: './catalog/product/find_product',
    param: 'product',
    callback: function(elem, data) {
        elem.val(data['model']);
        $('input[name="description[]"]', elem.closest('tr')).val(data['name']);
        $('input[name="amount[]"]', elem.closest('tr')).val(data['price']);
        $('input[name="product_id[]"]', elem.closest('tr')).val(data['id']);
        $('select[name="tax_percentage[]"]', elem.closest('tr')).val(parseFloat(data['tax']));
        
        updateTotals();
    },
    extraParams: {
        token: sessionToken
    }
};

var customerAcOptions = {
    url: './sale/customer/find_customer',
    callback: function(elem, data) {
        $('#customer_id').val(data['id']);
        $('#customer_no').html('CID.' + data['customer_no']);
    },
    extraParams: {
        token: sessionToken,
        simple: 1
    }
};

var pointValue = 0.5;

$(function() {
    updateTotals();

    $('input[name$="product[]"]').autocomplete(productAcOptions);
    $('#customer').autocomplete(customerAcOptions);

    $('a[rel="add-line"]').click(function() {
        var line = $('#invoice_lines tr:first-child').clone();
        $('input', line).val('');

        $('input[name$="product[]"]', line).autocomplete(productAcOptions);

        $('#invoice_lines').append(line);
    });
});

$('#coupon_code').blur(function() {
    var couponCode = $(this).val(),
        customerID = $('#customer_id').val(),
        products = {product: []};

    $('input[name="product_id[]"]').each(function() {
        if ($(this).val() != '') {
            products.product.push($(this).val());
        }
    });

    // Get total product value
    totalProductValue = 0.0;

    $('#invoice_lines tr').each(function() {
        var price = parseFloat($('input[name="amount[]"]', $(this)).val()),
            quantity = parseFloat($('input[name="quantity[]"]', $(this)).val());

        totalProductValue += price * quantity;
    });

    // Get coupon info
    $.getJSON('./sale/orders/get_coupon_info?order_id=0&coupon_code=' + couponCode + '&totalamount=' + totalProductValue + '&customer_id=' + customerID + '&' + $.param(products) + '&token=' + sessionToken, function(data) {
        // Fill relevant fields
        if (data.coupon_id != undefined) {
            $('input[name="discount[coupon][coupon_id]"]').val(data.coupon_id);
            $('input[name="discount[coupon][value]"]').val(data.discount);
            $('input[name="discount[coupon][type]"]').val(data.type);

            updateTotals();
        }
    });
});

$(document).on('click', 'a[rel=delete]', function() {
    $(this).closest('tr').remove();

    updateTotals();

    return false;
})

// Update totals on these events
$(document).on('keyup change blur', '#invoice_lines input, #invoice_lines select', function() {
    updateTotals();
});

$(document).on('blur', '#discount_value, #points', function() {
    updateTotals();
});

$(document).on('click', 'input[name="discount[type]"]', function() {
    updateTotals();
});

function updateTotals() 
{
    var products = new Array,
        taxes = new Object,
        subTotal = 0,
        otIndex = 0;

    // Find all products
    $('#invoice_lines tr').each(function() {
        var elem = $(this);

        productData = {
            'name': $('input[name="name[]"]', elem).val(),
            'model': $('input[name="product[]"]', elem).val(),
            'price': $('input[name="amount[]"]', elem).val(),
            'quantity': $('input[name="quantity[]"]', elem).val(),
            'tax_percentage': $('select[name="tax_percentage[]"] option:selected', elem).val()
        }

        products.push(productData)
    });

    // Set taxes
    for (product in products) {    
        var product = products[product],
            price = parseFloat(product.price * (1 + (product.tax_percentage / 100))),
            price = Math.round(price * 1000) / 1000
            total = parseFloat(price * product.quantity),
            taxAmount = total - (product.price * product.quantity);

        // Remove extra zeros from tax-percentage
        product.tax_percentage = parseFloat(product.tax_percentage);

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
        <td colspan="4" class="right">Subtotaal:</td>\
        <td style="width: 130px;" class="right">' + subTotal.formatMoney() + inputFields + '</td>\
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
            <td colspan="4" class="right">Spaarpunten (' + points + '):</td>\
            <td style="width: 130px;" class="right">- ' + pointsValue.formatMoney() + inputFields + '</td>\
        </tr>');

        subTotal -= pointsValue;
    }

    // Show discount
    var discount = $('#discount_value').val(),
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
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="-' + discountValue + '" />';

        if (discountType == 'P') {
            inputFields += '<input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + discountPercentage + '" />';
        }

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">Korting' + (discountType == 'P' ? ' (' + discountPercentage + '%)' : '') + ':</td>\
            <td style="width: 130px;" class="right">- ' + discountValue.formatMoney() + inputFields + '</td>\
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
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="-' + couponValue + '" />';

        if (couponType == 'P') {
            inputFields += '<input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + couponCode + ' - ' + couponPercentage + '%" />';
        } else {
            inputFields += '<input type="hidden" name="totals[' + otIndex + '][label_inject]" value="' + couponCode + '" />';
        }

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">Coupon' + (couponType == 'P' ? ' (' + couponCode + ' - ' + couponPercentage + '%)' : ' (' + couponCode + ')') + ':</td>\
            <td style="width: 130px;" class="right">- ' + couponValue.formatMoney() + inputFields + '</td>\
        </tr>');

        subTotal -= couponValue;
    }

    // Add tax-amount for shipping
    var shippingPrice = parseFloat($('#shipping_amount').val()),
        shippingTax   = parseFloat($('#shipping_tax').val()),
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

    // Add tax-amount for payment
    var paymentPrice = parseFloat($('#payment_amount').val()),
        paymentTax   = parseFloat($('#payment_tax').val()),
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
            <td colspan="4" class="right"><small>BTW (' + taxPercentage + '%):</small></td>\
            <td style="width: 130px;" class="right"><small>' + tax.formatMoney() + inputFields + '</small></td>\
        </tr>');
    }

    // Show shipping
    if (shippingPrice > 0) {
        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_SHIPPING" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="' + shippingPrice + '" />';

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">Verzendkosten:</td>\
            <td style="width: 130px;" class="right">' + shippingPrice.formatMoney() + inputFields + '</td>\
        </tr>');
    }

    if (paymentPrice > 0) {
        var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_PAYMENT" />\
                           <input type="hidden" name="totals[' + otIndex + '][value]" value="' + paymentPrice + '" />';

        otIndex++;

        $('#summary-totals').append('<tr>\
            <td colspan="4" class="right">Transactiekosten:</td>\
            <td style="width: 130px;" class="right">' + paymentPrice.formatMoney() + inputFields + '</td>\
        </tr>');
    }

    // Show total price
    subTotal = parseFloat(subTotal);

    var inputFields = '<input type="hidden" name="totals[' + otIndex + '][label]" value="SUMO_NOUN_OT_TOTAL" />\
                       <input type="hidden" name="totals[' + otIndex + '][value]" value="' + subTotal + '" />';

    $('#summary-totals').append('<tr>\
        <td colspan="4" class="right"><strong>Totaal:</strong></td>\
        <td style="width: 130px;" class="right"><strong>' + subTotal.formatMoney() + inputFields + '</strong></td>\
    </tr>');
}

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator, currencySymbol) {
    // check the args and supply defaults:
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
    decSeparator = decSeparator == undefined ? "," : decSeparator;
    thouSeparator = thouSeparator == undefined ? "." : thouSeparator;
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