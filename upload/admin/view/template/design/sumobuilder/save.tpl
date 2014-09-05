<form class="form-horizontal" id="save-form">
    <div class="col-md-12">
        <div class="col-md-8">
            <h3><?php echo Language::getVar('SUMO_NOUN_SAVE')?></h3>
            <div class="form-group">
                <label class="col-md-3">&nbsp;</label>
                <div class="col-md-4">
                    <span class="btn btn-primary" id="savebutton"><?php echo Language::getVar('SUMO_NOUN_SAVE_CHANGES')?></span>
                </div>
            </div>
            
            <h3><?php echo Language::getVar('SUMO_NOUN_SAVE_AS')?></h3>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo Language::getVar('SUMO_NOUN_NAME')?>
                </label>
                <div class="col-md-4">
                    <input type="text" id="save_as_name" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3">&nbsp;</label>
                <div class="col-md-4">
                    <span class="btn btn-primary" id="save_as_new"><?php echo Language::getVar('SUMO_NOUN_SAVE_AS_NEW')?></span>
                </div>
            </div>
            
            
            <h3><?php echo Language::getVar('SUMO_NOUN_REMOVE')?></h3>
            <div class="form-group">
                <label class="col-md-3">&nbsp;</label>
                <div class="col-md-4">
                    <span class="btn btn-primary" id="deletebutton"><?php echo Language::getVar('SUMO_NOUN_DELETE_THEME')?></span>
                </div>
            </div>
            
        </div>
        <div class="col-md-4">
            <h4><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SAVE_INFO')?></h4>
            <p><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SAVE_CONTENT')?></p>
            
            <div id="saved" class="alert alert-info"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SAVED')?></div>
            <div id="savemessage" class="alert alert-warning"></div>
        </div>
    </div>
</form>
<script type="text/javascript">
$(function(){
    $('.loader').slideUp();
    $('#saved, #savemessage').hide();
    $('#savebutton').on('click', function() {
        $.post('design/sumobuilder/ajax/?token=<?php echo $token?>', {action: 'absolutesave', theme_id: $('#themepicker .themepicker-item.active').attr('theme')}, function(data) {
            $('#saved').slideDown();
            $('#savebutton').prop('disabled', 1);
            setTimeout(function(){
                $('#saved').slideUp();
                $('#savebutton').prop('disabled', 0);
            }, 5000);
        })
    });
    $('#save_as_new').on('click', function() {
        var name = $('#save_as_name').val();
        if (name.length == 0) {
            saveAlert();
            return false;
        }
        $.post('design/sumobuilder/ajax/?token=<?php echo $token?>', {action: 'save_as', theme_id: $('#themepicker .themepicker-item.active').attr('theme'), name: name}, function(data) {
            data = $.parseJSON(data);
            if (data.result == 'OK') {
                $('#save, .loader').slideDown();
                $('#savebutton').prop('disabled', 1);
                $('#save_as_name').val('');
                setTimeout(function(){
                    //$('#saved').slideUp();
                    //$('#savebutton').prop('disabled', 0);
                    window.location=window.location;
                }, 1000);
            }
            else {
                saveAlert(data.result);
            }
        })
    })
    $('#deletebutton').on('click', function() {
        var msg1 = '<?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_REMOVE_THEME_CONFIRM')?>';
        var msg2 = '<?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_REMOVE_THEME_CONFIRM_CONFIRM')?>';
        if (confirm(msg1)) {
            if (confirm(msg2)) {
                $.post('design/sumobuilder/ajax/?token=<?php echo $token?>', {action: 'delete', theme_id: $('#themepicker .themepicker-item.active').attr('theme')}, function (data) {
                    data = $.parseJSON(data);
                    if (data.removed) {
                        $('.loading').show();
                        $('#savemessage').html(data.result).show();
                        window.location=window.location;
                    }
                    else {
                        alert(data.result);
                    }
                })
            }
        }
    })
})

function saveAlert(message)
{
    $('#save_as_name').parent().parent().addClass('has-error');
    setTimeout(function(){
        $('#save_as_name').parent().parent().removeClass('has-error');
        $('#savebutton').prop('disabled', 0);
    }, 2500);
    $('#savebutton').prop('disabled', 1);
    if (message) {
        if ($('#savemessage').is(':hidden')) {
            $('#savemessage').html(message).slideDown();
        }
        setTimeout(function(){
            if ($('#savemessage').is(':visible')) {
                $('#savemessage').slideUp();
            }
        }, 10000);
    }
}
</script>