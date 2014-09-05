<?php echo $header; ?>

<div class="block-flat">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h3 class="align-center" id="data"><?php echo Sumo\Language::getVar('SUMO_ADMIN_CACHE_HEADER', array($file_count, $objects, $file_size))?></h3>
            <p class="align-center" style="margin-top: 20px;"><a href="#clearcache" id="clearcache" class="btn btn-lg btn-info"><i class="fa fa-undo"></i> <strong><?php echo Sumo\Language::getVar('SUMO_ADMIN_CACHE_BUTTON')?></strong></a></p>
        </div>
    </div>
</div>

<div class="block-flat">
    <div class="header">
        <h3><?php echo Sumo\Language::getVar('SUMO_ADMIN_CACHE_INFO_HEADER')?></h3>
    </div>
    <div class="content">
        <p><?php echo Sumo\Language::getVar('SUMO_ADMIN_CACHE_INFO_CONTENT')?></p>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#clearcache').on('click', function(e) {
        e.preventDefault();
        $('#clearcache').find('.fa-undo').addClass('fa-spin');
        $.post('tool/cache/remove?token=<?php echo $this->session->data['token']?>', function(data) {
            $('#data strong').each(function() {
                $(this).html('<?php echo strtolower(Sumo\Language::getVar('SUMO_NOUN_NONE'))?>');
            });
            $('#clearcache').find('.fa-undo').removeClass('fa-spin');
        }, 'json');
    })
})
</script>
<?php echo $footer; ?>
