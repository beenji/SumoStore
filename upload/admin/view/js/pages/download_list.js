$(function() {
    new AjaxUpload('#upload-btn', {
        action: 'catalog/download/upload&token=' + sessionToken,
        name: 'file',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function(file, extension) {
            // Loading
            console.log('working....');
            $('#upload-btn').attr('disabled', true);
        },
        onComplete: function(file, json) {
            console.log('done!');
            console.log(file);
            console.log(json);
            $('#upload-btn').attr('disabled', false);

            if (json['success']) {
                $('#upload').val(json['filename']);
                $('#mask').val(json['mask']);
            } else {
                $('#mask').val(json['mask']);
            }
            /*$('#upload_message').removeClass('alert alert-success alert-warning');
            if (json['success']) {
                $('#upload_message').addClass('alert alert-success').html(json['success']);

                $('input[name=\'filename\']').attr('value', json['filename']);
                $('input[name=\'mask\']').attr('value', json['mask']);
            }

            if (json['error']) {
                $('#upload_message').addClass('alert alert-warning').html(json['error']);
            }*/
        }
    });
});
