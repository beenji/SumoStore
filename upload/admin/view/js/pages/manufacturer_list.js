$(function() {
    $('.fancy-upload').on('click', 'a[href="#delete"]', function() {
        var that = $(this).closest('.fancy-upload');

        $('img', that).remove();
        $('a.fu-delete', that).remove();
        $('a.fu-edit', that).removeClass('fu-edit').addClass('fu-new');
        $('a.fu-new i', that).removeClass('fa-wrench').addClass('fa-plus-circle');

        $('input[type=hidden]', that).val('');

        return false;
    });

    new AjaxUpload('#upload-btn', {
        action: 'common/images/upload?token=' + sessionToken,
        name: 'uploads',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function(file, extension) {
            // Loading
            $('#upload-btn').attr('disabled', true);
        },
        onComplete: function(file, result) {
            if (result['success']) {
                result = result['success'][0];

                $('#upload').val(result['location']);

                // Add a link and image
                $('#upload-btn').prev('img').remove();
                $('#upload-btn').next('a').remove();

                $('#upload-btn').before('<img src="../image/' + result['location'] + '" />');
                $('#upload-btn').after('<a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>');
                $('#upload-btn').removeClass('fu-new').addClass('fu-edit');
                $('#upload-btn i').removeClass('fa-plus-circle').addClass('fa-wrench');
            }
            else {
                var message = 'Er is iets misgegaan met het uploaden.';
                if (result['error']) {
                    message = result['error'];
                }

                // Show error
            }
        }
    });
});
