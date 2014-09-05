$(function() {
    $('#order_id').blur(function() {
        var orderID = $(this).val();

        $.getJSON('./sale/return/get_order_info?token=' + sessionToken + '&order_id=' + orderID, function(data) {
            // Prefill some fields
            console.log(data);
            if (data.customer.payment_address.firstname != undefined) {
                $('#firstname').val(data.customer.payment_address.firstname);
                $('#lastname').val(data.customer.payment_address.lastname);
                $('#email').val(data.customer.email);
                $('#telephone').val(data.customer.telephone);
                $('#date').val(data.order_date);
                $('#customer_id').val(data.customer.customer_id);

                $('#model').val('');
                $('#product_id').val('');
                $('#product_id option:first-child').attr('selected', true);

                // Fill products dropdown
                if (data.lines.length > 0) {
                    var data = data.lines;

                    $.each(data, function(k, product) {
                        $('#product_id option:gt(0)').remove();
                        $('#product_id').append($('<option />').val(product.product_id).html(product.name).data({model: product.model, quantity: product.quantity}));
                    })
                }
            } else {
                // Something went wrong
                alert(data);
            }
        })
    });

    $('#product_id').change(function() {
        var elem = $('option:selected', $(this));

        $('#model').val(elem.data('model'));
        $('#quantity').val(elem.data('quantity'));
        $('#product').val(elem.html());
    });
})
