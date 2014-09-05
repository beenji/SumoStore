$(document).on('click', 'a[rel=new_attribute]', function() {
    //var attrCount = $('div.attribute-block').length;

    $('.attribute-block').each(function() {
        var newBlock = $('.input-group:first-child', $(this)).clone();

        $('input', newBlock).each(function() {
            $(this).attr('name', $(this).attr('name').replace('attribute[0]', 'attribute[' + attrCount + ']')).val('');

            if ($(this).attr('type') == 'hidden') {
                $(this).remove();
            }
        });

        // Change add to delete
        $('a', newBlock).attr('rel', 'delete_attribute');
        $('i', newBlock).removeClass('fa-plus').addClass('fa-times');

        $(this).append(newBlock);
    });

    attrCount++;

    return false;
});

$(document).on('click', 'a[rel=delete_attribute]', function() {
    var index = $(this).closest('.input-group').index() + 1;

    $('.attribute-block').each(function() {
        $('.input-group:nth-child(' + index + ')', $(this)).remove();
    });

    return false;
});
