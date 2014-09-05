$(function() {
    // Fill sort-list with selected fields
    $('input[name="field[]"]').on('ifToggled click', function() {
        var elem = $(this),
            elemLabel = $('dd', elem.closest('dl')).html();

        if (elem.is(':checked')) {
            // Add
            $('#sort').append($('<option />').val(elem.val()).html(elemLabel));
        } else {
            // Remove
            $('#sort option[value=' + elem.val() + ']').remove();
        }
    });
})
