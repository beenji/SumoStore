
$(function() {
    $('#product').autocomplete({
        url: './catalog/product/find_product',
        callback: function(elem, data) {
            $('#product_id').val(data.id);
        },
        param: 'product',
        extraParams: {
            token: sessionToken,
            simple: 1
        }
    });
});
