$(function() {
    $('#product').autocomplete({
        url: './catalog/product/find_product',
        param: 'product',
        callback: function(elem, data) {
            //$('input[name$="description[]"]', elem.closest('tr')).val(data['name']);
            //$('input[name$="amount[]"]', elem.closest('tr')).val(data['price']);
            $('#product_id').val(data['id']);
            $('#product').val(data['name']);
            $('#product').data('selected-option', data['name']);

            //updateTotals();
        },
        extraParams: {
            token: sessionToken
        }
    })
});
