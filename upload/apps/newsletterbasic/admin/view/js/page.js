$(function() {
    redactorSettings.minHeight = 350;
    redactorSettings.visual = false;
    //$('.redactor-newsletter').redactor(redactorSettings);
    $('.filter-hide, .no-age, .customer_group, #progress').hide();
    $('input[name=filter]').on('change', function() {
        if ($('input[name=filter]:checked').val() == '1') {
            if ($('.filter-0').is(':visible') ) {
                $('.filter-0').slideUp();
            }
            $('.filter-1').slideDown();
        }
        else if ($('input[name=filter]:checked').val() == '0') {
            if ($('.filter-1').is(':visible')) {
                $('.filter-1').slideUp();
            }
            $('.filter-0').slideDown();
        }
    });

    var triggered = false;
    $('#newsletter-form :input').each(function(){
        $(this).on('change', function() {
            recalculate();
        })
        if (!triggered) {
            recalculate();
            triggered = true;
        }
    })

    $('select[name=age]').on('change', function() {
        if ($('option:selected', this).val() != '0') {
            if (!$('.no-age').is(':visible')) {
                $('.no-age').slideDown();
            }
        }
        else {
            if ($('.no-age').is(':visible')) {
                $('.no-age').slideUp();
            }
        }
    });

    $('select[name=to]').on('change', function() {
        if ($('option:selected', this).val() == 'customer_group') {
            if (!$('.customer_group').is(':visible')) {
                $('.customer_group').slideDown();
            }
        }
        else {
            if ($('.customer_group').is(':visible')) {
                $('.customer_group').slideUp();
            }
        }
    });

    $('#newsletter-form').on('submit', function(e) {
        e.preventDefault();
        if ($('#newsletter-form').parsley().isValid()) {
            bootbox.dialog({
                message: $('#newsletter-dialog').html(),
                buttons: {
                    cancel: {
                        label:      labelCancel,
                        className:  'btn-secondary'
                    },
                    test: {
                        label:      labelTest,
                        className:  'btn-primary',
                        callback:   function(e) {
                            e.stopPropagation();
                            $.post('app/newsletterbasic/testmail?token=' + sessionToken, $('#newsletter-form').serialize(), function(data) {
                                alert(data);
                            })
                            return false;
                        }
                    },
                    confirm: {
                        label:      labelConfirm,
                        className:  'btn-success',
                        callback:   function(e) {
                            e.stopPropagation();
                            sendMail('app/newsletterbasic/sendbatchmail?token=' + sessionToken, $('#newsletter-form').serialize());
                            return false;
                        }
                    }
                }
            })
        }
    });
})

function recalculate() {
    $.post('app/newsletterbasic/filter?token=' + sessionToken, $('#newsletter-form').serialize(), function(data) {
        $('#receivers').html(data);
        return data;
    })
}
function sendMail(url, data) {
    $.ajax({
        url:   url,
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function () {

        },
        complete: function () {
            $('#progress').html('');
            $('#form :input').prop('disabled', 0);
            sending = false;
        },
        success: function (json) {
            if (json['error']) {
                if (json['error']['warning']) {
                    $('#progress').html('<div class="alert alert-danger">' + json['error']['warning'] + '</div>');
                }
            }

            if (json['next'] && json['next'] != '') {
                if (json['success']) {
                    $('#progress').html('<div class="alert alert-info">' + json['success'] + '</div>');
                    sendMail(json['next'], data);
                }
            } else {
                if (json['success']) {
                    alert(json['success']);
                    bootbox.hideAll();
                }
            }
        }
    })
}
