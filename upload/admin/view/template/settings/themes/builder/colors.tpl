<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" action="" class="form">
            <div class="row">
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLORS')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_BODY')?></label>
                        <input type="text" name="colors[body_background]" value="<?php if (isset($colors['body_background'])) { echo $colors['body_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_TOP_BAR')?></label>
                        <input type="text" name="colors[top_bar_background]" value="<?php if (isset($colors['top_bar_background'])) { echo $colors['top_bar_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_HEADER')?></label>
                        <input type="text" name="colors[header_background]" value="<?php if (isset($colors['header_background'])) { echo $colors['header_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_CONTENT')?></label>
                        <input type="text" name="colors[content_background]" value="<?php if (isset($colors['content_background'])) { echo $colors['content_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_FOOTER')?></label>
                        <input type="text" name="colors[footer_background]" value="<?php if (isset($colors['footer_background'])) { echo $colors['footer_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_PRICEBOX')?></label>
                        <input type="text" name="colors[pricebox_background]" value="<?php if (isset($colors['pricebox_background'])) { echo $colors['pricebox_background']; }?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_IMAGE')?></h3>
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <div class="input-group">
                            <input type="text" name="colors[body_background_image]" value="<?php if (isset($colors['body_background_image'])) { echo $colors['body_background_image']; }?>" class="form-control" id="upload-image-input">
                            <span class="input-group-addon"><a href="#" onclick="return false;" id="upload-image"><i class="fa fa-plus-circle"></i></a></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <select name="colors[body_background_repeat]" class="form-control">
                            <option <?php if (isset($colors['body_background_repeat']) && $colors['body_background_repeat'] == 'no-repeat') { echo 'selected'; }?>>no-repeat</option>
                            <option <?php if (isset($colors['body_background_repeat']) && $colors['body_background_repeat'] == 'repeat') { echo 'selected'; }?>>repeat</option>
                            <option <?php if (isset($colors['body_background_repeat']) && $colors['body_background_repeat'] == 'repeat-x') { echo 'selected'; }?>>repeat-x</option>
                            <option <?php if (isset($colors['body_background_repeat']) && $colors['body_background_repeat'] == 'repeat-y') { echo 'selected'; }?>>repeat-y</option>
                        </select>
                    </div>

                    <div id="colorwheel">
                        <div  style="margin: 0 auto;"class="colorwheel"></div>
                        <br />
                        <div class="form-group">
                            <input type="text" class="form-control" value="#374558" readonly id="colorexample" style="">
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_PLURAL')?></h3>
                    <?php foreach (array('price_color', 'old_price_color', 'new_price_color', 'tax_price_color', 'sale_price_color', 'pricebox_text_color') as $color): ?>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($color))?></label>
                        <input type="text" name="colors[<?php echo $color ?>]" value="<?php if (isset($colors[$color])) { echo $colors[$color]; }?>" class="form-control">
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_TEXT_COLOR_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TEXT_COLOR_GENERAL')?></label>
                        <input type="text" name="colors[text_color]" value="<?php if (isset($colors['text_color'])) { echo $colors['text_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LINK_COLOR_GENERAL')?></label>
                        <input type="text" name="colors[link_color]" value="<?php if (isset($colors['link_color'])) { echo $colors['link_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LINK_COLOR_HOVER')?></label>
                        <input type="text" name="colors[link_hover_color]" value="<?php if (isset($colors['link_hover_color'])) { echo $colors['link_hover_color']; }?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_MENU')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_MENU')?></label>
                        <input type="text" name="colors[menu_background]" value="<?php if (isset($colors['menu_background'])) { echo $colors['menu_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKGROUND_COLOR_MENU_HOVER')?></label>
                        <input type="text" name="colors[menu_hover_background]" value="<?php if (isset($colors['menu_hover_background'])) { echo $colors['menu_hover_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TEXT_COLOR_MENU')?></label>
                        <input type="text" name="colors[menu_link_color]" value="<?php if (isset($colors['menu_link_color'])) { echo $colors['menu_link_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TEXT_COLOR_MENU_HOVER')?></label>
                        <input type="text" name="colors[menu_link_hover_color]" value="<?php if (isset($colors['menu_link_hover_color'])) { echo $colors['menu_link_hover_color']; }?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_BUTTON_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BUTTON_PRIMARY')?></label>
                        <input type="text" name="colors[button_primary]" value="<?php if (isset($colors['button_primary'])) { echo $colors['button_primary']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BUTTON_SECONDARY')?></label>
                        <input type="text" name="colors[button_secondary]" value="<?php if (isset($colors['button_secondary'])) { echo $colors['button_secondary']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BUTTON_ORDER')?></label>
                        <input type="text" name="colors[button_order]" value="<?php if (isset($colors['button_order'])) { echo $colors['button_order']; }?>" class="form-control">
                    </div>
                </div>
            </div>
            
            <hr>

            <div class="row">
                <div class="col-md-4">
                    <h3>Footer</h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_LINK_NORMAL_COLOR')?></label>
                        <input type="text" name="colors[footer_link_color]" value="<?php if (isset($colors['footer_link_color'])) { echo $colors['footer_link_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?></label>
                        <input type="text" name="colors[footer_text_color]" value="<?php if (isset($colors['footer_text_color'])) { echo $colors['footer_text_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_LINK_HOVER_COLOR')?></label>
                        <input type="text" name="colors[footer_link_hover_color]" value="<?php if (isset($colors['footer_link_hover_color'])) { echo $colors['footer_link_hover_color']; }?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_MENU')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_DROPDOWN_NORMAL_BACKGROUND_COLOR')?></label>
                        <input type="text" name="colors[menu_dropdown_background]" value="<?php if (isset($colors['menu_dropdown_background'])) { echo $colors['menu_dropdown_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_DROPDOWN_HOVER_BACKGROUND_COLOR')?></label>
                        <input type="text" name="colors[menu_dropdown_hover_background]" value="<?php if (isset($colors['menu_dropdown_hover_background'])) { echo $colors['menu_dropdown_hover_background']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_DROPDOWN_NORMAL_TEXT_COLOR')?></label>
                        <input type="text" name="colors[menu_dropdown_link_color]" value="<?php if (isset($colors['menu_dropdown_link_color'])) { echo $colors['menu_dropdown_link_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_DROPDOWN_HOVER_TEXT_COLOR')?></label>
                        <input type="text" name="colors[menu_dropdown_link_hover_color]" value="<?php if (isset($colors['menu_dropdown_link_hover_color'])) { echo $colors['menu_dropdown_link_hover_color']; }?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <h3><?php $left = Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_LEFT_COLUMN_HEADER'); $left = explode(' ', $left); echo $left[0] . '/' . Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_RIGHT_COLUMN_HEADER')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_HEADING_BACKGROUND_COLOR')?></label>
                        <input type="text" name="colors[sidebar_header_background_color]" value="<?php if (isset($colors['sidebar_header_background_color'])) { echo $colors['sidebar_header_background_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?></label>
                        <input type="text" name="colors[sidebar_header_text_color]" value="<?php if (isset($colors['sidebar_header_text_color'])) { echo $colors['sidebar_header_text_color']; }?>" class="form-control">
                    </div>
                    
                    <h3><?php $left = Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_LEFT_COLUMN_BOX'); $left = explode(' ', $left); echo $left[0] . '/' . Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_RIGHT_COLUMN_BOX')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?></label>
                        <input type="text" name="colors[sidebar_box_background_color]" value="<?php if (isset($colors['sidebar_box_background_color'])) { echo $colors['sidebar_box_background_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_LINK_NORMAL_COLOR')?></label>
                        <input type="text" name="colors[sidebar_box_link_color]" value="<?php if (isset($colors['sidebar_box_link_color'])) { echo $colors['sidebar_box_link_color']; }?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_LINK_HOVER_COLOR')?></label>
                        <input type="text" name="colors[sidebar_box_link_hover_color]" value="<?php if (isset($colors['sidebar_box_link_hover_color'])) { echo $colors['sidebar_box_link_hover_color']; }?>" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="pull-right">
                    <a href="#reset" class="btn btn-danger" id="reset"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_RESET')?></a>
                    <a href="#import" class="btn btn-secondary" id="import"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_IMPORT')?></a>
                    <a href="<?php echo $this->url->link('settings/themes/colorsexport', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->get['store_id'] . '&theme=' . $this->request->get['theme'], 'SSL')?>" class="btn btn-secondary" id="export"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_EXPORT')?></a>
                    <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="view/js/fixes/rafael.js"></script>
<script type="text/javascript" src="view/js/jquery/jquery.colorwheel.js"></script>
<script type="text/javascript">
$(function(){
    var cw = Raphael.colorwheel($('#colorwheel .colorwheel')[0], 227);
    cw.color('#374558');
    cw.input($('#colorwheel input')[0]);

    $('#reset').on('click', function(e) {
        e.preventDefault();
        bootbox.dialog({
            message: '<?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_RESET_CONFIRM')?>',
            buttons: {
                cancel: {
                    label: '<?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?>',
                    className: 'btn-secondary'
                },
                confirm: {
                    label: '<?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_RESET')?>',
                    className: 'btn-danger',
                    callback: function() {
                        window.location = '<?php echo str_replace('&amp;', '&', $this->url->link('settings/themes/reset', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->get['store_id'] . '&theme=' . $this->request->get['theme'], 'SSL')) ?>';
                    }
                }
            }
        })
    })
    // Handle file uploads
    new AjaxUpload('#upload-image', {
        action: 'common/images/upload?token=<?php echo $this->session->data['token'] ?>',
        name: 'uploads',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function (file, extension) {
            // Uploading...
        },
        onComplete: function (file, result) {
            if (result['success']) {
                var result = result['success'][0];
                $('#upload-image-input').val(result['location']);
            }
            else {
                alert('Boo');
                var message = 'Er is iets misgegaan met het uploaden.';
                if (result['error']) {
                    message = result['error'];
                }

                // Show error
            }
        }
    });
    new AjaxUpload('#import', {
        action: 'settings/themes/colorsimport?token=<?php echo $this->session->data['token'] ?>&store_id=<?php echo $this->request->get['store_id']?>&theme=<?php echo $this->request->get['theme']?>',
        name: 'upload',
        autoSubmit: false,
        responseType: 'json',
        onChange: function() {
            var _this = this;
            bootbox.confirm('<?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_IMPORT_CONFIRM')?>', function(resp) {
                if (resp) {
                    _this.submit();
                }
            })
        },
        onSubmit: function (file, extension) {
            // Uploading...
        },
        onComplete: function (file, result) {
            if (result['result'] == 'OK') {
                window.location = window.location;
            }
            else {
                alert('<?php echo Sumo\Language::getVar('SUMO_ADMIN_STYLE_IMPORT_ERROR')?>');

                // Show error
            }
        }
    });
});
</script>
