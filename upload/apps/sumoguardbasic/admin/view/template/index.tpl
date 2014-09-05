<?php
echo $header
?>
<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_SUMOGUARDBASIC_ENABLE')?></label>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="filter" value="1" <?php if (isset($active) && $active == true && $active >= 1) { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="filter" value="0" <?php if (isset($active) && $active < 1 || !isset($active)) { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                        </label>
                    </div>
                </div>
                <div class="form-group" id="status-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_SUMOGUARDBASIC_STATUS')?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" readonly value="" id="status-text">
                        <span class="input-group-addon" id="status-icon">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="block">
                    <div class="header"><h3><?php echo Sumo\Language::getVar('APP_SUMOGUARDBASIC_HELP_TITLE')?></h3></div>
                    <div class="content"><?php echo Sumo\Language::getVar('APP_SUMOGUARDBASIC_HELP_CONTENT')?></div>
                </div>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>
<script type="text/javascript">
var sessionToken    = '<?php echo $this->session->data['token']?>';
$(function() {
    $('input[name="filter"]').on('click change', function() {
        $.post('app/sumoguardbasic/check?token=' + sessionToken, {status: $('input[name="filter"]:checked').val()}, function(resp) {
            $('#status-text').val(resp.text);
            $('#status-icon').html('<i class="fa ' + resp.icon + '"></i>');
            $('#status-group').removeClass('has-warning has-error has-success').addClass(resp.class);
        }, 'json');
    }).trigger('change')
})
</script>
<?php echo $footer?>
