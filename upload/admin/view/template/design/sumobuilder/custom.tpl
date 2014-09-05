<form method="post" id="custom-form" class="form-horizontal">
    <div class="col-md-12">
        <div class="col-md-2">
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs" id="tab-custom">
                    <li>
                        <a href="#tab-custom-javascript" data-toggle="tab">
                            Javascript
                        </a>
                    </li>
                    <li>
                        <a href="#tab-custom-css" data-toggle="tab">
                            CSS
                        </a>
                    </li>
                </ul>
            </div>
            <div class="clearfix"><br /></div>
        </div>
        <div class="col-md-10">
            <div class="tab-content">
                <div class="tab-pane" id="tab-custom-javascript">
                    <textarea name="custom[js]" class="form-control" rows="15"><?php echo isset($settings['custom']['js']) ? htmlentities($settings['custom']['js']) : ''?></textarea>
                </div>
                <div class="tab-pane" id="tab-custom-css">
                    <textarea name="custom[css]" class="form-control" rows="15"><?php echo isset($settings['custom']['css']) ? $settings['custom']['css'] : ''?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"><br /></div>
</form>
<script type="text/javascript">
$(function(){
    $('#tab-custom a').on('shown.bs.tab', function(e) {
        //$('#tab-custom a').removeClass('active');
        //$('a[href=' + $(e.target).attr('href') + ']').addClass('active');
        //$(e.target).tab('show');
    });
    $('.loader').slideUp();
    
    $('#tab-custom a:first').tab('show');
});
</script>