var alfa = 'abcdefghijklmnopqrstuvwxy';

$(function() {
    $('#firstname').blur(function() {
        // Prefill firstname of first address?
        var val = $(this).val();

        if ($('#a input[name$="[firstname]"]').val() == '') {
            $('#a input[name$="[firstname]"]').val(val);
        }
    });

    $('#lastname').blur(function() {
        // Prefill lastname of first address?
        var val = $(this).val();

        if ($('#a input[name$="[lastname]"]').val() == '') {
            $('#a input[name$="[lastname]"]').val(val);
        }
    });

    $('#generate_password').click(function() {
        var chars = 'abcdefghijklmnpqrstuvwxyz123456789!@#$%&_',
            pwd   = '';

        for (i = 0; i < 8; i++) {
            var start = Math.floor(Math.random() * chars.length);
            pwd += chars.substr(start, 1);
        }

        $('#password, #confirm').prop('type', 'text').val(pwd);
    });

    $('.pc-api').blur(function() {
        var cont = $(this).closest('.tab-pane');

        var pc = $('input[name$="[postcode]"]', cont).val().replace(' ', ''),
            nm = $('input[name$="[number]"]', cont).val(),
            ct = $('select[name$="[country_id]"]', cont).val();

        if (ct == 150 && pc.match(/^[0-9]{4}[a-z]{2}/i) && nm.length > 0) {
            $.getJSON('./common/pc?token=' + sessionToken + '&q=' + pc + '/' + nm, function(resp) {
                $('input[name$="[postcode]"]', cont).val(resp.resource.postcode);
                $('input[name$="[address_1]"]', cont).val(resp.resource.street);
                $('input[name$="[city]"]', cont).val(resp.resource.town);

                // Set province
                var zone = resp.resource.province;

                if ($('select[name$="[zone_id]"] option:contains("' + zone + '")', cont).length) {
                    $('select[name$="[zone_id]"]', cont).val($('select[name$="[zone_id]"] option:contains("' + zone + '")', cont).val())
                }
                else if ($('select[name$="[zone_id]"] option:contains("' + zone.replace('-', ' ') + '")', cont).length) {
                    $('select[name$="[zone_id]"]', cont).val($('select[name$="[zone_id]"] option:contains("' + zone.replace('-', ' ') + '")', cont).val())
                }
            });
        }
    });

    $('.add-ip-ban').click(function() {
        var elem = $(this),
            ip   = elem.data('ip');

        $.ajax({
            url: 'index.php?route=sale/customer/addbanip&token=' + sessionToken,
            type: 'post',
            dataType: 'json',
            data: 'ip=' + encodeURIComponent(ip),
            success: function(json) {
                if (json['success']) {
                    //elem.addClass('remove-ip-ban').removeClass('add-ip-ban');
                    $('.add-ip-ban').hide();
                    $('.remove-ip-ban').removeClass('hidden').show();
                }
            }
        });
    });

    $('.remove-ip-ban').click(function() {
        var elem = $(this),
            ip   = elem.data('ip');

        $.ajax({
            url: 'index.php?route=sale/customer/removebanip&token=' + sessionToken,
            type: 'post',
            dataType: 'json',
            data: 'ip=' + encodeURIComponent(ip),
            success: function(json) {
                if (json['success']) {
                    //elem.removeClass('remove-ip-ban').addClass('add-ip-ban');
                    $('.add-ip-ban').show();
                    $('.remove-ip-ban').removeClass('hidden').hide();
                }
            }
        });
    });

    $('#add-reward').click(function() {
        // AJAX submit
        $.ajax({
            url: 'index.php?route=sale/customer/reward&token=' + sessionToken + '&customer_id=' + customerID,
            type: 'post',
            dataType: 'json',
            data: 'description=' + encodeURIComponent($('#description-rw').val()) + '&points=' + encodeURIComponent($('#points-rw').val()),
            success: function(json) {
                if (json.success != undefined) {
                    $.gritter.add({
                        text: json.success,
                        sticky: true,
                        class_name: 'success'
                    });

                    // Get reward table
                    //$('#table-rw').remove();
                    $('#table-rw').load('sale/customer/update?token=' + sessionToken + '&customer_id=' + customerID + ' #table-rw table', function() {
                        $(this).prepend('<hr />');
                    });

                    $('#description-rw').val('');
                    $('#points-rw').val('');
                }
                else if (json.error != undefined) {
                    $.gritter.add({
                        text: json.error,
                        sticky: true,
                        class_name: 'danger'
                    });
                }
                else {
                    // Not good...
                    alert('Something went wrong, please contact SumoStore');
                }
            }
        });

        return false;
    });

    $('#add-transaction').click(function() {
        // AJAX submit
        $.ajax({
            url: 'index.php?route=sale/customer/transaction&token=' + sessionToken + '&customer_id=' + customerID,
            type: 'post',
            dataType: 'json',
            data: 'description=' + encodeURIComponent($('#description-tr').val()) + '&amount=' + encodeURIComponent($('#amount-tr').val()),
            success: function(json) {
                if (json.success != undefined) {
                    $.gritter.add({
                        text: json.success,
                        sticky: true,
                        class_name: 'success'
                    });

                    //$('#table-tr').remove();
                    $('#table-tr').load('sale/customer/update?token=' + sessionToken + '&customer_id=' + customerID + ' #table-tr table', function() {
                        $(this).prepend('<hr />');
                    });

                    $('#description-tr').val('');
                    $('#amount-tr').val('');
                }
                else if (json.error != undefined) {
                    $.gritter.add({
                        text: json.error,
                        sticky: true,
                        class_name: 'danger'
                    });
                }
                else {
                    // Not good...
                    alert('Something went wrong, please contact SumoStore');
                }
            }
        });

        return false;
    });

    $('#add-history').click(function() {
        // AJAX submit
        $.ajax({
            url: 'index.php?route=sale/customer/history&token=' + sessionToken + '&customer_id=' + customerID,
            type: 'post',
            dataType: 'json',
            data: 'comment=' + encodeURIComponent($('#description-hs').val()),
            success: function(json) {
                if (json.success != undefined) {
                    $.gritter.add({
                        text: json.success,
                        sticky: true,
                        class_name: 'success'
                    });

                    //$('#table-hs').remove();
                    $('#table-hs').load('sale/customer/update?token=' + sessionToken + '&customer_id=' + customerID + ' #table-hs table', function() {
                        $(this).prepend('<hr />');
                    });

                    $('#description-hs').val('');
                }
                else if (json.error != undefined) {
                    $.gritter.add({
                        text: json.error,
                        sticky: true,
                        class_name: 'danger'
                    });
                }
                else {
                    // Not good...
                    alert('Something went wrong, please contact SumoStore');
                }
            }
        });

        return false;
    });

    $('#new-book').click(function() {
        // Add tab
        var tab = $('<li />').append($('<a />').data('toggle', 'tab'));
        $(this).parent().before(tab);

        var tabPane = $('#address-tab-panes .tab-pane:first-child').clone();
        tabPane.removeClass('active');
        $(':input', tabPane).each(function() {
            $(this).attr('name', $(this).attr('name').replace('[0]', '[1]'));
            $(this).val('');
        });

        var removeTabPane = $('<a class="btn btn-danger btn-sm" />').text('Verwijder adres');
        removeTabPane.click(function() {
            $('#address-tabs li:nth-child(' + ($(this).closest('.tab-pane').index() + 1) + ')').remove();
            $(this).closest('.tab-pane').remove();

            var tabCount = $('#address-tabs li').length;

            // Redo numbering
            $('#address-tabs li').each(function(k, elem) {
                if (k < (tabCount - 1)) {
                    $('#address-tab-panes .tab-pane:nth-child(' + (k + 1) + ')').attr('id', alfa.substr(k, 1));
                    $('a', $(this)).html(alfa.substr(k, 1).toUpperCase()).attr('href', '#' + alfa.substr(k, 1));
                }
            });

            $('#address-tabs li:first-child a').click();
        });

        tabPane.append('<hr />');
        tabPane.append(removeTabPane);

        $('#address-tab-panes').append(tabPane);

        var tabCount = $('#address-tabs li').length;

        $('#address-tabs li').each(function(k, elem) {
            if (k < (tabCount - 1)) {
                $('#address-tab-panes .tab-pane:nth-child(' + (k + 1) + ')').attr('id', alfa.substr(k, 1));
                $('a', $(this)).html(alfa.substr(k, 1).toUpperCase()).attr('href', '#' + alfa.substr(k, 1)).click(function(e) {
                    e.preventDefault();
                    $(this).tab('show');
                });
            }
        })
    });

    $('select[name$="[country_id]"]').change();
})

$(document).on('change', 'select[name$="[country_id]"]', function() {
    var val    = $(this).val(),
        zoneID = $(this).data('zone-id'),
        index  = $(this).attr('name').replace('address[', '').replace('][country_id]', '');

    $.ajax({
        url: './sale/customer/country&token=' + sessionToken + '&country_id=' + val,
        dataType: 'json',
        success: function(json) {
            if (json['postcode_required'] == '1') {
                $('#postcode-required' + index).show();
            } else {
                $('#postcode-required' + index).hide();
            }

            html = '<option value="">Maak een keuze</option>';

            if (json['zone'] != '' && json['zone'] != undefined) {
                for (i = 0; i < json['zone'].length; i++) {
                    html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                    if (json['zone'][i]['zone_id'] == zoneID) {
                        html += ' selected="selected"';
                    }

                    html += '>' + json['zone'][i]['name'] + '</option>';
                }
            } else {
                html += '<option value="0">Geen</option>';
            }

            $('select[name=\'address[' + index + '][zone_id]\']').html(html);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});

$(document).on('blur', 'input[name$="[postcode]"]', function() {
    var val    = $(this).val(),
        index  = $(this).attr('name').replace('address[', '').replace('][postcode]', '');

    // Fill stuff
    if ($('select[name="address[' + index + '][country_id]"]').val() == 150) {
        $.getJSON('common/pc?token=' + sessionToken + '&q=' + val, function(data) {
            if (data.resource != undefined) {
                $('input[name="address[' + index + '][address_1]"]').val(data.resource.street);
                $('input[name="address[' + index + '][city]"]').val(data.resource.town);

                // Set province
                $('select[name="address[' + index + '][zone_id]"] option').each(function() {
                    if ($(this).html().replace(' ', '-') == data.resource.province) {
                        $(this).attr('selected', true);
                        $('select[name="address[' + index + '][zone_id]"]').val($(this).attr('value'));

                        return;
                    }
                })
            }
        });
    }
});
