$(function(){
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        localStorage.setItem('settings_form', $(this).attr('href'));
    })
    var lastTab = localStorage.getItem('settings_form');
    if (lastTab) {
        $('.nav-tabs a[href=' + lastTab + ']').tab('show');
    }

    $('#customer_group_editor, .email_protocol').hide();

    $('.btn-add-tax').on('click', function(e) {
        e.preventDefault();
        $('#tax-table').append('<tr><td>' + textExtra + '</td><td><select name="tax_percentage[extra][]" class="form-control">' + generateTaxOptions() + '</select></td><td><a href="#delete-tax" class="btn btn-sm btn-danger btn-remove-tax"><i class="fa fa-trash-o"></i></a></td></tr>');
    })
    $('#tax-table').on('click', '.btn-remove-tax', function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    })

    $('.country_id').on('change', function() {
        var selected = $('option:selected', this).val();
        $('.country_id').each(function() {
            $(this).val(selected);
        })
    })

    $('#customers').on('click', '.btn-edit-group', function(e) {
        e.preventDefault();
        var group_id = $(this).attr('group');
        if ($('#customer_group_editor').is(':hidden')) {
            $('#customer_group_editor').slideDown();
        }
        $('#customer_group_editor').find('.group-add').hide();
        $('#customer_group_editor').find('.group-edit').show();
        $.post('settings/store/ajax?token=' + sessionToken, {action: 'group_info', customer_group_id: group_id}, function(response) {
            $('#customer_group_editor').find(':input').each(function() {
                if ($(this).attr('type') != 'radio') {
                    $(this).val('');
                }
            })
            $('input[name="group[approval]"]').filter('[value=' + response.data.approval + ']').prop('checked', 1);
            $('input[name="group[company_id_required]"]').filter('[value=' + response.data.company_id_required + ']').prop('checked', 1);
            $('input[name="group[tax_id_required]"]').filter('[value=' + response.data.tax_id_required + ']').prop('checked', 1);
            $.each(response.data.info, function(lang_id, lang) {
                $.each(lang, function(find, value) {
                    $('[name="group[' + find + '][' + lang_id + ']"]').val(value);
                });
            });
            $('#customer_group_id').val(group_id);
        }, 'JSON');
    })
    $('#customers').on('click', '.btn-remove-group', function(e) {
        e.preventDefault();
        var group_id = $(this).attr('group');
        bootbox.confirm(confirmGroupDeleteMsg, function(result) {
            if (result) {
                $.post('settings/store/ajax?token=' + sessionToken, {action: 'group_remove', customer_group_id: group_id}, function(response) {
                    if (response.ok != undefined) {
                        $('.customer-group-' + group_id).remove();
                    }
                    else {
                        $.gritter.add({
                            title: result.title,
                            text: result.text,
                            class_name: 'error',
                            time: ''
                        })
                    }
                }, 'JSON')
            }
        });
    })
    $('#add-group').on('click', function(e) {
        e.preventDefault();
        if ($('#customer_group_editor').is(':hidden')) {
            $('#customer_group_editor').slideDown();
        }
        $('#customer_group_editor').find('.group-edit').hide();
        $('#customer_group_editor').find('.group-add').show();
        $('#customer_group_editor').find(':input').each(function() {
            if ($(this).attr('type') != 'radio') {
                $(this).val('');
            }
        })
        $('#customer_group_id').val(0);
    })
    $('#customer_group_editor').on('click', '.btn-cancel', function(e) {
        e.preventDefault();
        $('#customer_group_editor').slideUp();
    })
    $('#customer_group_editor').on('click', '.btn-save', function(e) {
        e.preventDefault();
        $.post('settings/store/ajax?token=' + sessionToken, {action: 'group_info_save', data: $('#customer_group_editor :input').serialize(), customer_group_id: $('#customer_group_id').val()}, function(response) {
            if (response.ok != undefined) {
                $('#customer_group_editor').slideUp(function() {
                    $.gritter.add({
                        title: response.title,
                        text: response.text,
                        class_name: 'clean',
                        time: ''
                    })
                })
                var name = $('input[name="group[name][' + $('.lang-default').data('lang-id') + ']"]').val();
                if ($('#customer_group_id').val() > 0) {
                    var cgi = $('#customer_group_id').val();
                    $('#customer_groups_table .customer-group-' + cgi + ' td:first').html(name);
                    $('#customer_group_list .customer-group-' + cgi).html('<input type="checkbox" name="customer_group_display[]" value="' + cgi + '"> ' + name);
                    $('select[name="customer_group_id"] .customer-group-' + cgi).html(name);
                }
                else {
                    $('#customer_groups_table').append(customerGroupRow.replace(/CGID/g, response.customer_group_id).replace('CGNAME', name));
                    $('#customer_group_list').append('<label class="checkbox customer-group-' + response.customer_group_id + '"><input type="checkbox" name="customer_group_display[]" value="' + response.customer_group_id + '"> ' + name + '</label>');
                    $('select[name="customer_group_id"]').append('<option class="customer-group-' + response.customer_group_id + '" value="' + response.customer_group_id + '">' + name + '</option>');
                }
                $('#customer_group_editor').slideUp();
            }
            else {
                $.gritter.add({
                    title: response.title,
                    text: response.text,
                    class_name: 'error',
                    time: ''
                })
            }
        }, 'JSON')
    })
    $('.country_id').on('change', function() {
        $.post('settings/store/ajax?token=' + sessionToken, {action: 'get_zone', country_id: this.value}, function(response) {
            var html = '<option value="">' + textSelect + '</option>';
            $.each(response.zone, function(key, list) {
                html += '<option value="' + list['zone_id'] + '" ' + (list.selected != undefined ? 'selected' : '') + '>' + list['name'] + '</option>';
            })
            $('[name=zone_id]').html(html);
        }, 'JSON');
    }).trigger('change');
    $('select[name="email_protocol"]').on('change', function() {
        var option = $('option:selected', this).val();
        console.log(option);
        $('.email_protocol').hide();
        $('.email_protocol_' + option).slideDown();
    }).trigger('change');
    $('#view-password').on('click', function(e) {
        e.preventDefault();
        var i = $(this).find('i');
        if (i.hasClass('fa-eye')) {
            i.removeClass('fa-eye');
            i.addClass('fa-asterisk');
            $('input[name="smtp[password]"]').attr('type', 'text');
        }
        else {
            i.addClass('fa-eye');
            i.removeClass('fa-asterisk');
            $('input[name="smtp[password]"]').attr('type', 'password');
        }
    })
    $('select[name=template]').on('change', function(){
        var preview = $('option:selected', this).attr('preview');
        if (preview == undefined) {
            preview = '/image/no_image.jpg';
        }
        $('#template').html('<img src="' + preview + '" alt="Preview" onerror="this.onerror=null;this.title=\'Warning: image \' + this.src +\' could not be found!\';this.src=\'/image/no_image.jpg\';" />');
    }).trigger('change');

    $('.country_id').on('change', function() {
        $.post('settings/store/ajax?token=' + sessionToken, {action: 'get_zone', country_id: this.value, store: storeID}, function(response) {
            var html = '<option value="">' + textSelect + '</option>';
            $.each(response.zone, function(key, list) {
                html += '<option value="' + list['zone_id'] + '" ' + (list.selected != undefined ? 'selected' : '') + '>' + list['name'] + '</option>';
            })
            $('[name=zone_id]').html(html);
        }, 'JSON');
    }).trigger('change');

    if (formType == 'store') {
        $('.fancy-upload').on('click', 'a[href="#delete"]', function() {
            var that = $(this).closest('.fancy-upload');

            $('img', that).remove();
            $('a.fu-delete', that).remove();
            $('a.fu-edit', that).removeClass('fu-edit').addClass('fu-new');
            $('a.fu-new i', that).removeClass('fa-wrench').addClass('fa-plus-circle');

            $('input[type=hidden]', that).val('');

            return false;
        });

        new AjaxUpload('#upload-logo', {
            action: 'common/images/upload?token=' + sessionToken + '&store_id=' + storeID,
            name: 'uploads',
            autoSubmit: true,
            responseType: 'json',
            onSubmit: function (file, extension) {
                // Uploading...
            },
            onComplete: function (file, result) {
                if (result['success']) {
                    result = result['success'][0];
                    $('#config_logo').val(result['location']);

                    // Add a link and image
                    $('#upload-logo').prev('img').remove();
                    $('#upload-logo').next('a').remove();

                    $('#upload-logo').before('<img src="../image/' + result['location'] + '" />');
                    $('#upload-logo').after('<a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>');
                    $('#upload-logo').removeClass('fu-new').addClass('fu-edit');
                    $('#upload-logo i').removeClass('fa-plus-circle').addClass('fa-wrench');
                }
                else {
                    var message = 'Er is iets misgegaan met het uploaden.';
                    if (result['error']) {
                        message = result['error'];
                    }

                    // Show error
                }
            }
        })

        new AjaxUpload('#upload-icon', {
            action: 'common/images/upload?token=' + sessionToken + '&store_id=' + storeID,
            name: 'uploads',
            autoSubmit: true,
            responseType: 'json',
            onSubmit: function (file, extension) {
                // Uploading...
            },
            onComplete: function (file, result) {
                if (result['success']) {
                    result = result['success'][0];
                    $('#config_icon').val(result['location']);

                    // Add a link and image
                    $('#upload-icon').prev('img').remove();
                    $('#upload-icon').next('a').remove();

                    $('#upload-icon').before('<img src="../image/' + result['location'] + '" />');
                    $('#upload-icon').after('<a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>');
                    $('#upload-icon').removeClass('fu-new').addClass('fu-edit');
                    $('#upload-icon i').removeClass('fa-plus-circle').addClass('fa-wrench');
                }
                else {
                    var message = 'Er is iets misgegaan met het uploaden.';
                    if (result['error']) {
                        message = result['error'];
                    }

                    // Show error
                    alert(message);
                }
            }
        })
    }

    $('#local .form-date').on('change', function() {
        var preview = $(this).parent().find('.date_preview');
        $.post('settings/store/ajax?token=' + sessionToken, {action: 'preview', value: $(this).val(), type: preview.attr('type')}, function(response){
            preview.html(response.value);
        }, 'JSON')
    }).trigger('change');

    $('#points_convert').on('change keyup', function() {
        $(this).val($(this).val().replace(',', '.'));
    })
})

function generateTaxOptions()
{
    var output = '';
    for(var i = 0; i <= 25; i++) {
        output += '<option value="' + i + '">' + i + '%</option>';
    }
    return output;
}
