$(function(){
    $('#letter-tab a').on('click, shown.bs.tab', function (e) {
        fetch($(this).data('letter'));
    });
    $('#letter-tab a:first').tab('show').trigger('click');
})
function saveAll()
{
    $('.btn-save').each(function(){
        $(this).trigger('click');
    })
}
function checkSave()
{
    $('.btn-save').on('click', function() {
        var tthis   = $(this);
        var input   = tthis.parent().parent().find('textarea');
        var row     = tthis.parent().parent();
        tthis.prop('disabled', 1);
        input.prop('disabled', 1);
        $.post(
            'localisation/language/ajax?token=' + token + '&key_id=' + input.attr('key') + '&language_id=' + language,
            {
                key_id: input.attr('key'),
                value:  input.val()
            },
            function() {
                tthis.find('i').removeClass('fa-heart-o').addClass('fa-heart');
                row.addClass('has-success');
                setTimeout(function(){
                    tthis.find('i').removeClass('fa-heart').addClass('fa-heart-o');
                    row.removeClass('has-success');
                    tthis.prop('disabled', 0);
                    input.prop('disabled', 0);
                }, 3000);

            }
        );
    })
}
function fetch(letter)
{
    $('.loader').show();
    var tab = '#tab-' + letter + ' > table > tbody';
    $.post('localisation/language/ajax?letter=' + letter + '&token=' + token + '&language_id=' + language, function(data){
        if (data.nothing_to_translate == true) {
            $(tab).html('');
        }
        else {
            var newHtml = '';
            $.each(data, function(key, value) {
                var original = value.name;
                if (value.default_value) {
                    original = value.default_value;
                }
                else
                if (value.value) {
                    original = value.value;
                }

                if (original == value.name) {
                    original = '';
                }

                if (!value.value) {
                    value.value = '';
                }
                if (letter == 'empty') {
                    if (value.default_value) {
                        value.value = value.default_value;
                    }
                    var save = value.id;
                }
                else {
                    var save = value.key_id;
                }
                newHtml += '<tr>';

                if (letter != 'empty') {
                    newHtml += '<td><p class="form-control-static"><strong title="' + (value.name) + '">' + (value.name) + '</strong></p></td>';
                    newHtml += '<td class="original">' + (original) + '</td>';
                } else {
                    newHtml += '<td style="width: 400px;"><p style="width: 400px;" class="form-control-static"><strong title="' + (value.name) + '">' + (value.name) + '</strong></p></td>';
                }

                newHtml += '<td><textarea class="form-control language-key" id="language-key-' + save + '" key="' + save + '">' + value.value + '</textarea></td><td><span class="btn btn-sm btn-primary btn-save" key="'+ save +'"><i class="fa fa-heart-o"></i></span></td></tr>';
            });
            $(tab).html(newHtml);
            checkSave();
        }
        $('.loader').slideUp();
    }, 'json');
}
