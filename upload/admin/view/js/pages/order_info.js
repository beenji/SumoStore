$(function() {
    var orderID = $('#order-id').val(),
        processingInvoice = false;

    $('a[href="#generate-invoice"]').on('click', function() {
        if (processingInvoice) {
            return false;
        }

        $.ajax({
            url: 'sale/order/createinvoiceno&token=' + sessionToken + '&order_id=' + orderID,
            dataType: 'json',
            beforeSend: function() {
                processingInvoice = true;
            },
            success: function(json) {
                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    });
                }

                if (json.invoice_no) {
                    $('#invoice').html(json['invoice_no']);
                }

                processingInvoice = false;
            }
        });

        return false;
    });

    $('#credit-add').click(function() {
        $.ajax({
            url: 'sale/order/addcredit&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                $('.success, .warning').remove();

                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    })
                }

                if (json['success']) {
                    $.gritter.add({
                        text: json['success']
                    });

                    $('#credit').html('<b>[</b> <a id="credit-remove">' + textCreditRemove + '</a> <b>]</b>');
                }
            }
        });
    });

    $('#credit-remove').click(function() {
        $.ajax({
            url: 'sale/order/removecredit&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            success: function(json) {

                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    })
                }

                if (json['success']) {
                    $.gritter.add({
                        text: json['success']
                    });

                    $('#credit').html('<b>[</b> <a id="credit-add">' + textCreditAdd + '</a> <b>]</b>');
                }
            }
        });
    });

    $('#reward-add').click(function() {
        $.ajax({
            url: 'sale/order/addreward&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    })
                }

                if (json['success']) {
                    $.gritter.add({
                        text: json['success']
                    });

                    $('#reward').html('<b>[</b> <a id="reward-remove">' + textRewardRemove + '</a> <b>]</b>');
                }
            }
        });
    });

    $('#reward-remove').click(function() {
        $.ajax({
            url: 'sale/order/removereward&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    })
                }

                if (json['success']) {
                    $.gritter.add({
                        text: json['success']
                    });

                    $('#reward').html('<b>[</b> <a id="reward-add">' + textRewardAdd + '</a> <b>]</b>');
                }
            }
        });
    });

    $('#commission-add').click(function() {
        $.ajax({
            url: 'sale/order/addcommission&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    })
                }

                if (json['success']) {
                    $.gritter.add({
                        text: json['success']
                    });

                    $('#commission').html('<b>[</b> <a id="commission-remove">' + textCommissionRemove + '</a> <b>]</b>');
                }
            }
        });
    });

    $('#commission-remove').click(function() {
        $.ajax({
            url: 'sale/order/removecommission&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                if (json['error']) {
                    $.gritter.add({
                        text: json['error']
                    })
                }

                if (json['success']) {
                    $.gritter.add({
                        text: json['success']
                    });

                    $('#commission').html('<b>[</b> <a id="commission-add">' + textCommissionAdd + '</a> <b>]</b>');
                }
            }
        });
    });

    var processing = false;

    $('#add-note').submit(function() {
        if (processing) {
            return false;
        }

        var formData = $(this).serialize();

        $.ajax({
            url: 'sale/orders/history&token=' + sessionToken + '&order_id=' + orderID,
            type: 'post',
            dataType: 'json',
            data: formData,
            beforeSend: function() {
                processing = true;
            },
            complete: function() {
                processing = false;
            },
            success: function(info) {
                $.gritter.add({
                    title: info['title'],
                    text: info['message'],
                    class_name: 'clean'
                });

                // Add list item
                if (info.success) {
                    window.location = window.location;
                }
            }
        });

        return false;
    });

    $('#invoice_map').css('opacity', 0);

    // Set center on address
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        'address': paymentAddress
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var map;
            var mapOptions = {
                zoom: 16,
                mapTypeControl: false,
                streetViewControl: false,
                center: results[0].geometry.location,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById('invoice_map'), mapOptions);

            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        }
        else {
            $('#invoice_map').html('[Google maps: ' + status + ']');
        }
        setTimeout(function() {
            $('#invoice_map').animate({opacity: 1}, 300);
        }, 300);
    });
});
