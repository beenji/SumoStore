<?php echo $header ?>
<div id="pad-wrapper">
    <div class="row">
        <div class="col-md-4">
            <h4>
                <i class="icon-desktop"></i>
                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_TITLE')?>
            </h4>
        </div>
        <div class="col-md-8 pull-right">
            
        </div>
    </div>
    <?php /* ?>
    <div class="clearfix"><br /></div>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs" id="tabchoose">
                <li>
                    <a href="#tab-general" data-toggle="tab">
                        <?php echo Language::getVar('SUMO_NOUN_GENERAL')?>
                    </a>
                </li>
                <li>
                    <a href="#tab-themes" data-toggle="tab">
                        <?php echo Language::getVar('SUMO_NOUN_THEME_PLURAL')?>
                    </a>
                </li>
                <li>
                    <a href="#tab-widgets" data-toggle="tab">
                        <?php echo Language::getVar('SUMO_NOUN_WIDGET_PLURAL')?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php */ ?>
    <div class="clearfix"><br /></div>
    <div class="row">
        <div class="col-sm-12">
            <?php /*
            <div class="tab-content">
                <div class="tab-pane" id="tab-general">
                    <form method="post" class="form-horizontal" id="general-form">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?php echo Language::getVar('SUMO_NOUN_STATUS')?>:
                            </label>
                            <div class="col-sm-6">
                                <div class="inline-radio col-md-2">
                                    <input type="radio" name="status" value="1">
                                    <?php echo Language::getVar('ACTIVE')?>
                                </div>
                                <div class="inline-radio col-md-2">
                                    <input type="radio" name="status" value="0">
                                    <?php echo Language::getVar('INACTIVE')?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-4">
                                    <span class="help-block">
                                        <?php echo Language::getVar('SUMO_ADMIN_DESIGN_SUMOBUILDER_HELP_STATUS')?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="tab-themes">
                */?>
                    <div class="col-sm-12" id="themepicker">
                        <?php foreach ($themes as $id => $name): ?>
                        <div class="themepicker-item well col-sm-2" theme="<?php echo $id?>">
                            <span class="themepicker-item-title">
                                <?php if ($id == 1) {
                                    echo Language::getVar('SUMO_NOUN_DEFAULT');
                                }
                                else {
                                    echo $name;
                                }?>
                            </span>
                        </div>
                        <?php endforeach ?>
                    </div>
                    <div class="clearfix"><br /></div>
                    <div class="col-sm-12" id="themesettings">
                        <ul class="nav nav-tabs">
                            <li>
                                <a href="#tab-themes-pages" data-toggle="tab">
                                    <?php echo Language::getVar('SUMO_NOUN_PAGE_PLURAL')?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-themes-colors" data-toggle="tab">
                                    <?php echo Language::getVar('SUMO_NOUN_COLOR_PLURAL')?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-themes-fonts" data-toggle="tab">
                                    <?php echo Language::getVar('SUMO_NOUN_FONT_PLURAL')?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-themes-custom" data-toggle="tab">
                                    <?php echo Language::getVar('SUMO_NOUN_CUSTOM')?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-themes-save" data-toggle="tab">
                                    <?php echo Language::getVar('SUMO_NOUN_SAVE...')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"><br /></div>
                    <div class="loader">
                        <div class="alert alert-info">
                            <i class="icon-refresh icon-spin"></i> 
                            <span class="loading-message m1">
                                <?php echo Language::getVar('SUMO_NOUN_LOADING')?>
                            </span>
                            <span class="loading-message m2">
                                <?php echo Language::getVar('SUMO_NOUN_LOADING_2')?>
                            </span>
                            <span class="loading-message m3">
                                <?php echo Language::getVar('SUMO_NOUN_LOADING_3')?>
                            </span>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-themes-pages" page="pages"></div>
                        <div class="tab-pane" id="tab-themes-backgrounds" page="backgrounds"></div>
                        <div class="tab-pane" id="tab-themes-colors" page="colors"></div>
                        <div class="tab-pane" id="tab-themes-fonts" page="fonts"></div>
                        <div class="tab-pane" id="tab-themes-custom" page="custom"></div>
                        <div class="tab-pane" id="tab-themes-save" page="save"></div>
                    </div>
                    <?php /* ?>
                </div>
            </div>
            <?php */ ?>
        </div>
    </div>
</div>
<div class="clearfix"><br /></div>
<script type="text/javascript">
$(function(){
    $('#tabchoose a:first').tab('show');
    $('#themesettings, .loader .loading-message, .loader').hide();
    $('#themesettings .nav a').on('show.bs.tab', function(e){        
        $('.loader').slideDown(); // goes away when page is loaded to prevent it goes away too early
        $('.loading-message').hide();
        $('.m1').show();
        $('.sp-container').remove(); // cleanup
        
        var data    = $($(e.relatedTarget).attr('href') + ' :input').prop('disabled', 0).serialize();
        $($(e.relatedTarget).attr('href')).html('');
        var action  = $(e.target).attr('href');
        var page    = $(action).attr('page');
        var theme   = $('#themepicker .themepicker-item.active').attr('theme');
        
        switch (page) {
            case 'save':
            case 'custom':
                break;
                
            case 'pages':
            case 'colors':
                setTimeout(function(){
                    $('.m1').hide();
                    $('.m2').show();
                    setTimeout(function() {
                        $('.m2').hide();
                        $('.m3').show();
                    }, 700);
                }, 700);
                break;
            case 'fonts':
                setTimeout(function(){
                    $('.m1').hide();
                    $('.m2').show();
                }, 700);
                break;
        }
        
        $(action).html('');
        
        $.post('design/sumobuilder/ajax?token=<?php echo $token?>', {action: 'midsave', theme_id: theme, data: data});
        
        $.post('design/sumobuilder/ajax?token=<?php echo $token?>', {action: page, theme_id: theme}, function(data) {
            $(action).slideUp(function(){
                $(action).html(data).slideDown();
            })
        })
    })
    $('#themepicker .themepicker-item').each(function(){
        $(this).on('click', function(){
            $('#themepicker .themepicker-item').removeClass('active alert-success');
            $(this).addClass('active alert-success');
            if ($('#themesettings').is(':visible') == false) {
                $('#themesettings').slideDown();
            }
            $('#themesettings .nav a:first').tab('show');
        })
    })
});
</script>
<?php echo $footer ?>