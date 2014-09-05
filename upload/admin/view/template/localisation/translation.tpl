<?php
echo $header;
$range = range('a', 'z');
?>

<div id="pad-wrapper">
    <div class="alert alert-warning" id="warning">
        <i class="icon-warning-sign"></i><p></p>
    </div>
    <div class="alert alert-success" id="success">
        <i class="icon-ok-sign"></i><p></p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4><img src="img/icons/cats-24.png" alt="" /> <?php echo Language::getVar('SUMO_ADMIN_LOCALISATION_TRANSLATION_TITLE') ?></h4>
            <br /><br /><br /><br />
        </div>
        <div class="col-md-6">
            <div class="loader">
                <div class="alert alert-info">
                    <i class="icon-refresh icon-spin"></i> <?php echo Language::getVar('SUMO_NOUN_LOADING')?>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"><br /></div>

    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <?php foreach ($languages as $list): ?>
                <li class="<?php if ($list['active']){ echo 'active'; }?>">
                    <a href="<?php echo $list['url']?>">
                        <?php echo $list['name']?>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>

    <div class="clearfix"><br /></div>

    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs" id="lettertab">
                <?php foreach($range as $letter): ?>
                <li class="letter">
                    <a href="#tab-<?php echo $letter?>" data-toggle="tab" key="<?php echo $letter?>">
                        <?php echo $letter?>
                    </a>
                </li>
                <?php endforeach?>
                <li class="letter">
                    <a href="#tab-empty" data-toggle="tab" key="empty">
                        <i class="fa-keyboard-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="clearfix"><br /></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="tab-content">
                <?php foreach ($range as $letter): ?>
                <div class="tab-pane" id="tab-<?php echo $letter?>">
                    <table class="table table-striped dataTable" letter="<?php echo $letter?>">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo Language::getVar('SUMO_NOUN_TRANSLATION_KEY')?>
                                </th>
                                <th>
                                    <?php echo Language::getVar('SUMO_NOUN_TRANSLATION')?>
                                </th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($translations[$letter])): foreach ($translations[$letter] as $list): ?>
                            <tr>
                                <td>
                                    <?php
                                    if (!empty($list['default_value'])) {
                                        $name = $list['default_value'];
                                        $tooltip = ' title="' . $list['name'] . '"';
                                    }
                                    else {
                                        $name = $list['name'];
                                        $tooltip = '';
                                    }
                                    ?>
                                    <strong id="language-name-<?php echo $list['id']?>" <?php echo $tooltip?>><?php echo $name?></strong>
                                </td>
                                <td>
                                    <textarea class="form-control language-key" id="language-key-<?php echo $list['id']?>" key="<?php echo $list['id']?>"></textarea>
                                </td>
                                <td>
                                    <span class="btn btn-sm btn-primary btn-save" key="<?php echo $list['id']?>"><i class="icon-save"></i></span>
                                </td>
                            </tr>
                            <?php endforeach; endif?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach?>
                <div class="tab-pane" id="tab-empty">
                    <table class="table table-striped" id="untranslated">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo Language::getVar('SUMO_NOUN_TRANSLATION_KEY')?>
                                </th>
                                <th>
                                    <?php echo Language::getVar('SUMO_NOUN_TRANSLATION')?>
                                </th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div class="alert alert-info">
                        <i class="icon-info"></i> <?php echo Language::getVar('SUMO_ADMIN_LOCALISATION_TRANSLATION_COMPLETED')?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#warning, #success, .loading, #tab-empty .alert').hide();
    $('#lettertab a').on('click, shown.bs.tab', function (e) {
        if ($(this).attr('href') == '#tab-empty') {
            fetchOther();
        }
        else {
            fetchKeys();
        }
    });
    $('#lettertab a:first').tab('show').trigger('click');
    $('.dataTable').each(function(){
        $(this).dataTable({
            "sPaginationType": "full_numbers",
            "oLanguage": DATATABLES_LANG,
            "iDisplayLength": 250,
            'bAutoWidth': false,
            'aoColumns': [
                {'sWidth': '15%'},
                {'sWidth': '75%'},
                {'sWidth': '15%'},
            ],
            'aaSorting': [
                [0, 'asc']
            ]
        });
    });
    setTimeout(function(){
        fetchKeys();
    }, 500);
});
function checkSave()
{
    $('.btn-save').on('click', function() {
        var tthis   = $(this);
        var input   = tthis.parent().parent().find('textarea');
        var row     = tthis.parent().parent();
        tthis.prop('disabled', 1);
        input.prop('disabled', 1);
        $.post(
            'localisation/translation/ajax?token=<?php echo $token?>&action=save',
            {
                key_id: input.attr('key'),
                value:  input.val()
            },
            function() {
                tthis.find('i').removeClass('icon-save').addClass('icon-heart');
                row.addClass('has-success');
                setTimeout(function(){
                    tthis.find('i').removeClass('icon-heart').addClass('icon-save');
                    tthis.prop('disabled', 0);
                    input.prop('disabled', 0);
                    row.removeClass('has-success');
                }, 3000);

            }
        );
    })
}
function fetchKeys()
{
    $('#pad-wrapper .dataTable:visible').hide();
    $('.loader').show();

    var language = <?php echo $language?>;
    var fetch = [];
    $('#pad-wrapper .tab-pane:visible').find('.language-key').each(function(){
        fetch.push($(this).attr('key'));
        $(this).val('');
    });

    if (fetch.length) {
        $.post('localisation/translation/ajax?action=fetch&token=<?php echo $token?>', {language:language,keys:fetch}, function(data){
            $.each(data, function(key, value) {
                $('#language-key-' + value.key_id).val(value.value);
                if (value.default_name && value.default_name.length) {
                    $('#language-name-' + value.key_id).val(value.default_name);
                }
                else if (value.default_key && value.default_key.length) {
                    $('#language-name-' + value.key_id).val(value.default_key);
                }
            });
            $('#pad-wrapper .tab-pane:visible .dataTable').slideDown(function(){
                $('.loader').hide();
            });
            //$('textarea').autosize();
            checkSave();
        }, 'json');
    }
    else {
        $('.loader').slideUp();
    }
}
function fetchOther()
{
    $('.loader').show();
    var language = <?php echo $language?>;
    $.post('localisation/translation/ajax?action=empty&token=<?php echo $token?>', {language:language,action:'empty'}, function(data){
        if (data.nothing_to_translate == true) {
            $('#untranslated').hide(function(){
                $('#tab-empty .alert').show();
            })
        }
        else {
            $('#untranslated').show();
            $('#tab-empty .alert').hide();
            var newHtml = '';
            $.each(data, function(key, value) {
                var name = value.name;
                if (value.default_value) {
                    name = value.default_value;
                }
                if (value.value) {
                    name = value.value;
                }
                newHtml += '<tr><td style="width:35%;"><p class="form-control-static"><strong>' + (name) + '</strong></p></td><td><textarea class="form-control language-key" id="language-key-' + key + '" key="' + key + '"></textarea></td><td><span class="btn btn-sm btn-primary btn-save" key="'+key+'"><i class="icon-save"></i></span></td></tr>';
            });
            $('#untranslated').html(newHtml);
            checkSave();
        }
        $('.loader').slideUp();
    }, 'json');
}
</script>
<?php echo $footer?>
