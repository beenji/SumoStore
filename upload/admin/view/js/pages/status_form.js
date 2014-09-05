$(function(){
    $('.watch-table').on('click', '.trigger-edit-button', function(e) {
        e.preventDefault();
        var type        = $(this).attr('data-type');
        var id          = $(this).attr('data-id');
        edit(type, id);
    })
    reload('order_status');
    reload('stock_status');
    reload('return_status');
    reload('return_action');
    reload('return_reason');
})

function reload(part)
{
    var table = $('#' + part + '_table');
    table.html('<tr><td class="text-center"><i class="fa fa-refresh fa-spin"></i></td></tr>');
    $.post('settings/status/ajaxgetlist?token=' + token, {type: part}, function(response) {
        if (response.isEmpty) {
            table.html('<tr><td colspan="2" class="text-center">' + response.isEmpty + '</td></tr>');
        }
        else {
            table.html('');
            var html = '';
            $.each(response.return, function(k, list) {
                html += '<tr class="trigger-edit"><td>' + list.name + ' <a href="#edit" class="pull-right trigger-edit-button" data-type="' + part + '" data-id="' + list.id + '"><i class="fa fa-edit"></i></a></td></tr>';
            })
            table.html(html);
        }
    }, 'json');
}

function edit(type, id)
{
    $.post('settings/status/ajaxgetdata?token=' + token, {type: type, id: id}, function(response) {
        bootbox.dialog({
            title:      $('#edit-title').html() + ' (' + type + '_status_' + id + ')',
            message:    '<form id="bootbox-form">' + $('#edit-form').html() + '</form>',
            buttons:    {
                remove:     {
                    label:      $('#edit-form-remove').html(),
                    className:  'btn-danger',
                    callback:   function() {
                        bootbox.confirm($('#edit-form-remove-confirm').html(), function(result) {
                            if (result) {
                                $.post('settings/status/ajaxremove?token=' + token, {type: type, id: id}, function() {
                                    reload(type);
                                })
                            }
                            else {
                                edit(type, id);
                            }
                        })
                    }
                },
                save:       {
                    label:      $('#edit-form-save').html(),
                    className:  'btn-primary',
                    callback:   function() {
                        $.post('settings/status/update?token=' + token, {type: type, id: id, data: $('#bootbox-form').serialize()}, function() {
                            reload(type);
                        })
                    }
                },
                nothing:    {
                    label:      $('#edit-form-cancel').html(),
                    classname:  'btn-secondary',
                    callback:   function() {}
                }
            }
        })
        $('.bootbox-body .edit-type').val(type);
        $.each(response.names, function(k, name) {
            $('.bootbox-body .name-' + k).val(name);
        })
    }, 'json');
}
