$(function() {
    // Re-fetch gateways
    $('#account,#site_secure_code,#site_id').blur(function() {
        if ($(this).data('orig') == undefined || $(this).data('orig') != $(this).val()) {
            // Reload settings
            var appSettings = $('#appSettings').serialize();

            $.post('app/multisafepay/ajax?token=' + sessionToken, appSettings, function(data) {
                if (!data || data == '[empty_output]') {
                    $('.api-ok').hide();
                    $('.api-notifier').removeClass('hidden');
                }
                else {
                    $('.api-ok').show();
                    $('.api-notifier').addClass('hidden');

                    $('#gateways').html(data);
                    $('#gateways .switch').bootstrapSwitch();

                    $('.excluding-tax').each(function() {
                        if ($(this).val().length) {
                            var parent      = $(this).parent().parent();
                            var tofind      = parent.find('.including-tax');
                            var rate_type   = parent.find('.rate_type:checked');
                            var element     = parent.parent().parent().find('.tax-class');
                            if (rate_type.val() == 'f') {
                                tofind.val(calculateIn($(this).val(), element));
                            }
                            else {
                                tofind.val($(this).val());
                            }
                        }
                    })
                }
            })

            // Reset orig-value
            $(this).data('orig', $(this).val());
        }
    });

    $('#account').trigger('blur');
})

$('#gateways').on('change keyup', '.including-tax', function() {
    if ($(this).val().length) {
        var parent      = $(this).parent().parent().parent();
        var tofind      = parent.find('.excluding-tax');
        var rate_type   = $(this).parent().parent().find('.rate_type:checked');
        var element     = parent.parent().parent().find('.tax-class');

        if (rate_type.val() == 'f') {
            var newvalue= calculateEx($(this).val(), element);
            tofind.val(newvalue);
        }
        else {
            tofind.val($(this).val());
        }
    }
})

function getTaxRate(element) {
    return $('#tax').val();
}

function calculateIn(price, element) {
    return Math.max(parseFloat(price.replace(',', '.')) + (parseFloat(price.replace(',', '.')) * getTaxRate(element) / 100)).toFixed(2);
}

function calculateEx(price, element) {
    return Math.max(parseFloat(price.replace(',', '.')) / (1 + (getTaxRate(element) / 100))).toFixed(4);
}
