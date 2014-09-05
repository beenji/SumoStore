$(function() {
    // Update category-list
    $('#shop').change(function() {
        var val = $(this).val();

        $('#category option:gt(0)').remove();

        if (categories[val] != undefined) {
            $.each(categories[val], function(k, v) {
                $('#category').append($('<option />').val(v.id).html(v.label));
            });
        }
    });

    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });

    $('.ge-name-trigger').blur(function() {
        // No title? Prefill with name
        var langID = $(this).attr('id').replace('name-', '');

        if ($('#title-' + langID).val() == '') {
            $('#title-' + langID).val($(this).val());

            updateSeo();
        }
    });

    $('.ge-description-trigger').keyup(function() {
        var length = 156,
            parent = $(this).closest('.form-group');

        length -= $(this).val().length;

        if (length == 156) {
            return;
        }

        if ($(this).val().length > 17) {
            parent.removeClass('has-warning');
            parent.addClass('has-success');
        } else {
            parent.addClass('has-warning');
            parent.removeClass('has-success');
        }

        $('span.counter', $(this).parent().parent()).html(length);

        updateSeo();
    });

    $('.ge-title-trigger,.ge-url-trigger').keyup(function() {
        updateSeo();
    });

    $('.fancy-upload').on('click', 'a[href="#delete"]', function() {
        var that = $(this).closest('.fancy-upload');

        $('img', that).remove();
        $('a.fu-delete', that).remove();
        $('a.fu-edit', that).removeClass('fu-edit').addClass('fu-new');
        $('a.fu-new i', that).removeClass('fa-wrench').addClass('fa-plus-circle');

        $('input[type=hidden]', that).val('');

        return false;
    });

    var storeID = $('#shop').val(),
        categoryID = $('#category_id').val();

    new AjaxUpload('#upload-image', {
        action: 'common/images/upload&token=' + sessionToken + '&store=' + storeID + '&category_id=' + categoryID + '&is_category=1',
        name: 'uploads',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function (file, extension) {
            // Uploading...
        },
        onComplete: function (file, result) {
            if (result['success']) {
                var result = result['success'][0];

                $('#image').val(result['location']);

                // Add a link and image
                $('#upload-image').prev('img').remove();
                $('#upload-image').next('a').remove();

                $('#upload-image').before('<img src="../image/' + result['location'] + '?reload=' + new Date().getTime() + '" />');
                $('#upload-image').after('<a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>');
                $('#upload-image').removeClass('fu-new').addClass('fu-edit');
                $('#upload-image i').removeClass('fa-plus-circle').addClass('fa-wrench');
            }
            else {
                var message = 'Er is iets misgegaan met het uploaden.';
                if (result['error']) {
                    message += result['error'];
                }

                // Show error
                alert(message);
            }
        }
    });

    $('.ge-description-trigger').trigger('keyup');
});

function updateSeo() {
    $('.ge-title').each(function() {
        var elem = $(this),
            langID = elem.attr('id').replace('ge-title-', '');

        elem.html($('#title-' + langID).val());
    });

    $('.ge-url').each(function() {
        var elem = $(this),
            langID = elem.attr('id').replace('ge-url-', '');

        // Set URL
        var categoryID = $('#category').val(),
            storeID = $('#shop').val(),
            name = $('#name-' + langID).val();

        $.getJSON('./catalog/category/preview_url?token=' + sessionToken + '&category_id=' + categoryID + '&store_id=' + storeID + '&language_id=' + langID + '&name=' + name, function(data) {
                $('#url-' + langID).val(data);
                elem.html(data);
            }
        );
    });

    $('.ge-description').each(function() {
        var elem = $(this),
            langID = elem.attr('id').replace('ge-description-', '');

        elem.html($('#meta-desc-' + langID).val());
    });
}