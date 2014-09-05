$(function() {
    $('#trigger-upload-control').click(function() {
        $('#upload-control').click();

        return false;
    });

    $('.image-list').on('click', 'a.remove', function() {
        $(this).closest('li').remove();

        return false;
    });

    $('.image-list').on('click', 'a.push-left', function() {
        // Get parent li
        var li = $(this).closest('li'),
            index = li.index();

        if (index > 1) {
            $('.image-list li:nth-child(' + (index - 1) + ')').after(li);
        }

        return false;
    });

    $('.image-list').on('click', 'a.push-right', function() {
        // Get parent li
        var li = $(this).closest('li'),
            index = li.index();
            listLength = $('li', $(this).closest('ul')).length;

        if (index < (listLength - 1)) {
            $('.image-list li:nth-child(' + (index + 2) + ')').after(li);
        }

        return false;
    });
});
