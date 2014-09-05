<form method="post" id="colors-form" class="form-horizontal">
    <div class="col-md-12">
        <div class="col-md-2">
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs" id="tab-colors">
                    <li>
                        <a href="#tab-colors-general" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_GENERAL') ?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-colors-header" data-toggle="tab">
                            Header
                        </a>
                    </li>
                    <li>
                        <a href="#tab-colors-menu" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_MENU')?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-colors-content" data-toggle="tab">
                            Content
                        </a>
                    </li>
                    <li>
                        <a href="#tab-colors-prices" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_PRICE_PLURAL')?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-colors-buttons" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_BUTTON_PLURAL')?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-colors-footer" data-toggle="tab">
                            Footer
                        </a>
                    </li>
                </ul>
            </div>
            <div class="clearfix"><br /></div>
        </div>
        <div class="col-md-10">
            <div class="tab-content">
                <div class="tab-pane" id="tab-colors-general">
                    <h3><?php echo Language::getVar('SUMO_NOUN_GENERAL')?></h3>
                    <?php
                    $colors = array();
                    
                    $colors['body_background_color']    = '#FFFFFF';
                    $colors['body_text_color']          = '#000000';
                    $colors['body_light_color']         = '#666666';
                    $colors['link_normal_color']        = '#4BB8E2';
                    $colors['link_hover_color']         = '#B6B6B6';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[<?php echo $key?>]" value="<?php echo isset($settings['colors'][$key]) ? $settings['colors'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_HEADINGS')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_HEADINGS_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[headings][color]" value="<?php echo isset($settings['colors']['headings']['color']) ? $settings['colors']['headings']['color'] : '#DEDEDE';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[headings][border_enabled]" value="1" <?php if (isset($settings['colors']['headings']['border_enabled']) && $settings['colors']['headings']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[headings][border_enabled]" value="0" <?php if (isset($settings['colors']['headings']['border_enabled']) && !$settings['colors']['headings']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group heading_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                        </label>
                        <div class="col-md-2">
                            <select name="colors[headings][border][width]" class="form-control">
                                <?php for ($i = 0; $i <= 10; $i++) {
                                    $selected = isset($settings['colors']['headings']['border']['width']) ? ($settings['colors']['headings']['border']['width'] == $i ? 'selected' : '') : '';
                                    echo '<option ' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group heading_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                        </label>
                        <div class="col-md-2">
                            <select name="colors[headings][border][style]" class="form-control">
                                <option value="solid" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                    Solid
                                </option>
                                <option value="dotted" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                    Dotted
                                </option>
                                <option value="dashed" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                    Dashed
                                </option>
                                <option value="double" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                    Double
                                </option>
                                <option value="groove" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                    Groove
                                </option>
                                <option value="ridge" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                    Ridge
                                </option>
                                <option value="inset" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                    Inset
                                </option>
                                <option value="outset" <?php if (isset($settings['colors']['headings']['border']['style']) && $settings['colors']['headings']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                    Outset
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group heading_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[headings][border][color]" value="<?php echo isset($settings['colors']['headings']['border']['color']) ? $settings['colors']['headings']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MAIN_COLUMN')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHOW_BACKGROUND')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[main_column][background_enabled]" value="1" <?php if (isset($settings['colors']['main_column']['background_enabled']) && $settings['colors']['main_column']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[main_column][background_enabled]" value="0" <?php if (isset($settings['colors']['main_column']['background_enabled']) && !$settings['colors']['main_column']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group main_background_enabled pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[main_column][background_color]" value="<?php echo isset($settings['colors']['main_column']['background_color']) ? $settings['colors']['main_column']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHADOW')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[main_column][shadow]" value="1" <?php if (isset($settings['colors']['main_column']['shadow']) && $settings['colors']['main_column']['shadow']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[main_column][shadow]" value="0" <?php if (isset($settings['colors']['main_column']['shadow']) && !$settings['colors']['main_column']['shadow']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[main_column][border_enabled]" value="1" <?php if (isset($settings['colors']['main_column']['border_enabled']) && $settings['colors']['main_column']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[main_column][border_enabled]" value="0" <?php if (isset($settings['colors']['main_column']['border_enabled']) && !$settings['colors']['main_column']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group main_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                        </label>
                        <div class="col-md-2">
                            <select name="colors[main_column][border][width]" class="form-control">
                                <?php for ($i = 0; $i <= 10; $i++) {
                                    $selected = isset($settings['colors']['main_column']['border']['width']) ? ($settings['colors']['main_column']['border']['width'] == $i ? 'selected' : '') : '';
                                    echo '<option ' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group main_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                        </label>
                        <div class="col-md-2">
                            <select name="colors[main_column][border][style]" class="form-control">
                                <option value="solid" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                    Solid
                                </option>
                                <option value="dotted" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                    Dotted
                                </option>
                                <option value="dashed" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                    Dashed
                                </option>
                                <option value="double" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                    Double
                                </option>
                                <option value="groove" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                    Groove
                                </option>
                                <option value="ridge" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                    Ridge
                                </option>
                                <option value="inset" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                    Inset
                                </option>
                                <option value="outset" <?php if (isset($settings['colors']['main_column']['border']['style']) && $settings['colors']['main_column']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                    Outset
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group main_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[main_column][border][color]" value="<?php echo isset($settings['colors']['main_column']['border']['color']) ? $settings['colors']['main_column']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane" id="tab-colors-header">
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_TOP_BAR')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHOW_BACKGROUND')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[topbar][background_enabled]" value="1" <?php if (isset($settings['colors']['topbar']['background_enabled']) && $settings['colors']['topbar']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[topbar][background_enabled]" value="0" <?php if (isset($settings['colors']['topbar']['background_enabled']) && !$settings['colors']['topbar']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group topbar_background_enabled pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[topbar][background_color]" value="<?php echo isset($settings['colors']['topbar']['background_color']) ? $settings['colors']['topbar']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <?php
                    $colors = array();
                    
                    $colors['text_color']                       = '#CCCCCC';
                    $colors['separator_color']                  = '#525252';
                    $colors['link_normal_color']                = '#4BB8E2';
                    $colors['link_hover_color']                 = '#B6B6B6';
                    $colors['dropdown_normal_background_color'] = '#FFFFFF';
                    $colors['dropdown_normal_text_color']       = '#CCCCCC';
                    $colors['dropdown_hover_background_color']  = '#B6B6B6';
                    $colors['dropdown_hover_text_color']        = '#FFFFFF';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[topbar][<?php echo $key?>]" value="<?php echo isset($settings['colors']['topbar'][$key]) ? $settings['colors']['topbar'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    
                    <h3>Header</h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHOW_BACKGROUND')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[header][background_enabled]" value="1" <?php if (isset($settings['colors']['header']['background_enabled']) && $settings['colors']['header']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[header][background_enabled]" value="0" <?php if (isset($settings['colors']['header']['background_enabled']) && !$settings['colors']['header']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group header_background_enabled pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[header][background_color]" value="<?php echo isset($settings['colors']['header']['background_color']) ? $settings['colors']['header']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <div class="form-group header_background_enabled pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?> mini-header
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[header][mini][background_color]" value="<?php echo isset($settings['colors']['header']['mini']['background_color']) ? $settings['colors']['header']['mini']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_NOUN_SHOPPING_CART')?></h3>
                        <?php
                        $colors = array();
                        $colors['background_color']     = '#F3F3F3';
                        $colors['text_color']           = '#CCCCCC';
                        $colors['border_color']         = '#525252';
                        $colors['border_hover_color']   = '#4BB8E2';
                        
                        foreach ($colors as $key => $default): ?>
                            <div class="form-group">
                                <label class="col-md-6 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="colors[shopping_cart][<?php echo $key?>]" value="<?php echo isset($settings['colors']['shopping_cart'][$key]) ? $settings['colors']['shopping_cart'][$key] : $default;?>" class="form-control color" style="width:75%;">
                                </div>
                            </div>
                        <?php endforeach?>
                    </div>
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_NOUN_SEARCHBAR')?></h3>
                        <?php
                        $colors = array();
                        $colors['background_color']     = '#F3F3F3';
                        $colors['text_color']           = '#CCCCCC';
                        $colors['border_color']         = '#525252';
                        $colors['border_hover_color']   = '#4BB8E2';
                        
                        foreach ($colors as $key => $default): ?>
                            <div class="form-group">
                                <label class="col-md-6 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="colors[searchbar][<?php echo $key?>]" value="<?php echo isset($settings['colors']['searchbar'][$key]) ? $settings['colors']['searchbar'][$key] : $default;?>" class="form-control color" style="width:75%;">
                                </div>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>
                
                <div class="tab-pane" id="tab-colors-menu">
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_NOUN_MENU')?></h3>
                        <?php
                        $colors = array();
                        $colors['background_color']         = '#F3F3F3';
                        $colors['background_hover_color']   = '#4BB8E2';
                        $colors['link_color']               = '#CCCCCC';
                        $colors['link_hover_color']         = '#525252';
                        $colors['seperator_border_color']   = '#F1F1F1';
                        
                        foreach ($colors as $key => $default): ?>
                            <div class="form-group">
                                <label class="col-md-6 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="colors[menu][<?php echo $key?>]" value="<?php echo isset($settings['colors']['menu'][$key]) ? $settings['colors']['menu'][$key] : $default;?>" class="form-control color" style="width:75%;">
                                </div>
                            </div>
                        <?php endforeach?>
                        
                        <h3><?php echo Language::getVar('SUMO_NOUN_SUB_MENU')?></h3>
                        <?php
                        $colors = array();
                        $colors['background_color']         = '#F3F3F3';
                        $colors['background_hover_color']   = '#4BB8E2';
                        $colors['heading_background_color'] = '#F5F5F5';
                        $colors['text_color']               = '#464646';
                        $colors['link_color']               = '#464646';
                        $colors['link_hover_color']         = '#FFFFFF';
                        $colors['seperator_border_color']   = '#F1F1F1';
                        
                        foreach ($colors as $key => $default): ?>
                            <div class="form-group">
                                <label class="col-md-6 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="colors[submenu][<?php echo $key?>]" value="<?php echo isset($settings['colors']['submenu'][$key]) ? $settings['colors']['submenu'][$key] : $default;?>" class="form-control color" style="width:75%;">
                                </div>
                            </div>
                        <?php endforeach?>
                    </div>
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_NOUN_MOBILE_MENU')?></h3>
                        <?php
                        $colors = array();
                        $colors['background_color']         = '#F3F3F3';
                        $colors['background_hover_color']   = '#4BB8E2';
                        $colors['heading_background_color'] = '#F5F5F5';
                        $colors['text_color']               = '#464646';
                        
                        foreach ($colors as $key => $default): ?>
                            <div class="form-group">
                                <label class="col-md-6 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                                </label>
                                <div class="col-md-6">
                                    <input type="text" name="colors[mobilemenu][<?php echo $key?>]" value="<?php echo isset($settings['colors']['mobilemenu'][$key]) ? $settings['colors']['mobilemenu'][$key] : $default;?>" class="form-control color" style="width:75%;">
                                </div>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>
                
                <div class="tab-pane" id="tab-colors-content">
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CONTENT_COLUMN')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHOW_BACKGROUND')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[content_column][background_enabled]" value="1" <?php if (isset($settings['colors']['content_column']['background_enabled']) && $settings['colors']['content_column']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[content_column][background_enabled]" value="0" <?php if (isset($settings['colors']['content_column']['background_enabled']) && !$settings['colors']['content_column']['background_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group content_background_enabled pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[content_column][background_color]" value="<?php echo isset($settings['colors']['content_column']['background_color']) ? $settings['colors']['content_column']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHADOW')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[content_column][shadow]" value="1" <?php if (isset($settings['colors']['content_column']['shadow']) && $settings['colors']['content_column']['shadow']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[content_column][shadow]" value="0" <?php if (isset($settings['colors']['content_column']['shadow']) && !$settings['colors']['content_column']['shadow']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[content_column][border_enabled]" value="1" <?php if (isset($settings['colors']['content_column']['border_enabled']) && $settings['colors']['content_column']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[content_column][border_enabled]" value="0" <?php if (isset($settings['colors']['content_column']['border_enabled']) && !$settings['colors']['content_column']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group content_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                        </label>
                        <div class="col-md-2">
                            <select name="colors[content_column][border][width]" class="form-control">
                                <?php for ($i = 0; $i <= 10; $i++) {
                                    $selected = isset($settings['colors']['content_column']['border']['width']) ? ($settings['colors']['content_column']['border']['width'] == $i ? 'selected' : '') : '';
                                    echo '<option ' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group content_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                        </label>
                        <div class="col-md-2">
                            <select name="colors[content_column][border][style]" class="form-control">
                                <option value="solid" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                    Solid
                                </option>
                                <option value="dotted" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                    Dotted
                                </option>
                                <option value="dashed" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                    Dashed
                                </option>
                                <option value="double" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                    Double
                                </option>
                                <option value="groove" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                    Groove
                                </option>
                                <option value="ridge" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                    Ridge
                                </option>
                                <option value="inset" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                    Inset
                                </option>
                                <option value="outset" <?php if (isset($settings['colors']['content_column']['border']['style']) && $settings['colors']['content_column']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                    Outset
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group content_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[content_column][border][color]" value="<?php echo isset($settings['colors']['content_column']['border']['color']) ? $settings['colors']['content_column']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_TABS')?></h3>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[tabs][background_color]" value="<?php echo isset($settings['colors']['tabs']['background_color']) ? $settings['colors']['tabs']['background_color'] : '#666666';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SELECTED_TAB_BACKGROUND_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[tabs][selected_background_color]" value="<?php echo isset($settings['colors']['tabs']['selected_background_color']) ? $settings['colors']['tabs']['selected_background_color'] : '#4BB8E2';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[tabs][text_color]" value="<?php echo isset($settings['colors']['tabs']['text_color']) ? $settings['colors']['tabs']['text_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_BREADCRUMBS')?></h3>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[breadcrumbs][text_color]" value="<?php echo isset($settings['colors']['breadcrumbs']['text_color']) ? $settings['colors']['breadcrumbs']['text_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_HOVER_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[breadcrumbs][text_hover_color]" value="<?php echo isset($settings['colors']['breadcrumbs']['text_hover_color']) ? $settings['colors']['breadcrumbs']['text_hover_color'] : '#666666';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"><br /></div>
                    <?php for ($col = 0; $col <= 2; $col++): $type = 'left'; if ($col == 1) { $type = 'right'; } if ($col == 2) { $type = 'category'; } ?>
                    <div class="col-md-6">
                        <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_' . strtoupper($type) . '_COLUMN_HEADER')?></h3>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHOW_BACKGROUND')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_header][background_enabled]" value="1" <?php if (isset($settings['colors'][$type .'_column_header']['background_enabled']) && $settings['colors'][$type .'_column_header']['background_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('YES')?>
                                </div>
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_header][background_enabled]" value="0" <?php if (isset($settings['colors'][$type .'_column_header']['background_enabled']) && !$settings['colors'][$type .'_column_header']['background_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_header_background pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[<?php echo $type?>_column_header][background_color]" value="<?php echo isset($settings['colors'][$type .'_column_header']['background_color']) ? $settings['colors'][$type .'_column_header']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[<?php echo $type?>_column_header][text_color]" value="<?php echo isset($settings['colors'][$type .'_column_header']['text_color']) ? $settings['colors'][$type .'_column_header']['text_color'] : '#C6DCF4';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHADOW')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_header][shadow]" value="1" <?php if (isset($settings['colors'][$type .'_column_header']['shadow']) && $settings['colors'][$type .'_column_header']['shadow']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('YES')?>
                                </div>
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_header][shadow]" value="0" <?php if (isset($settings['colors'][$type .'_column_header']['shadow']) && !$settings['colors'][$type .'_column_header']['shadow']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_header][border_enabled]" value="1" <?php if (isset($settings['colors'][$type .'_column_header']['border_enabled']) && $settings['colors'][$type .'_column_header']['border_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('YES')?>
                                </div>
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_header][border_enabled]" value="0" <?php if (isset($settings['colors'][$type .'_column_header']['border_enabled']) && !$settings['colors'][$type .'_column_header']['border_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_header_border pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                            </label>
                            <div class="col-md-4">
                                <select name="colors[<?php echo $type?>_column_header][border][width]" class="form-control">
                                    <?php for ($i = 0; $i <= 10; $i++) {
                                        $selected = isset($settings['colors'][$type . '_column_header']['border']['width']) ? ($settings['colors'][$type . '_column_header']['border']['width'] == $i ? 'selected' : '') : '';
                                        echo '<option ' . $selected . '>' . $i . '</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_header_border pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                            </label>
                            <div class="col-md-4">
                                <select name="colors[<?php echo $type?>_column_header][border][style]" class="form-control">
                                    <option value="solid" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                        Solid
                                    </option>
                                    <option value="dotted" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                        Dotted
                                    </option>
                                    <option value="dashed" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                        Dashed
                                    </option>
                                    <option value="double" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                        Double
                                    </option>
                                    <option value="groove" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                        Groove
                                    </option>
                                    <option value="ridge" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                        Ridge
                                    </option>
                                    <option value="inset" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                        Inset
                                    </option>
                                    <option value="outset" <?php if (isset($settings['colors'][$type . '_column_header']['border']['style']) && $settings['colors'][$type . '_column_header']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                        Outset
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_header_border pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[<?php echo $type?>_column_header][border][color]" value="<?php echo isset($settings['colors'][$type . '_column_header']['border']['color']) ? $settings['colors'][$type . '_column_header']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        
                        <!-- BOX -->
                        
                        <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_' . strtoupper($type) . '_COLUMN_BOX')?></h3>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHOW_BACKGROUND')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_box][background_enabled]" value="1" <?php if (isset($settings['colors'][$type . '_column_box']['background_enabled']) && $settings['colors'][$type . '_column_box']['background_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('YES')?>
                                </div>
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_box][background_enabled]" value="0" <?php if (isset($settings['colors'][$type . '_column_box']['background_enabled']) && !$settings['colors'][$type . '_column_box']['background_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_box_background pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[<?php echo $type?>_column_box][background_color]" value="<?php echo isset($settings['colors'][$type . '_column_box']['background_color']) ? $settings['colors'][$type . '_column_box']['background_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[<?php echo $type?>_column_box][text_color]" value="<?php echo isset($settings['colors'][$type .'_column_box']['text_color']) ? $settings['colors'][$type .'_column_box']['text_color'] : '#C6DCF4';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHADOW')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_box][shadow]" value="1" <?php if (isset($settings['colors'][$type . '_column_box']['shadow']) && $settings['colors'][$type . '_column_box']['shadow']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('YES')?>
                                </div>
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_box][shadow]" value="0" <?php if (isset($settings['colors'][$type . '_column_box']['shadow']) && !$settings['colors'][$type . '_column_box']['shadow']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_box][border_enabled]" value="1" <?php if (isset($settings['colors'][$type . '_column_box']['border_enabled']) && $settings['colors'][$type . '_column_box']['border_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('YES')?>
                                </div>
                                <div class="inline-radio col-md-4">
                                    <input type="radio" name="colors[<?php echo $type?>_column_box][border_enabled]" value="0" <?php if (isset($settings['colors'][$type . '_column_box']['border_enabled']) && !$settings['colors'][$type . '_column_box']['border_enabled']) { echo 'checked'; }?>>
                                    <?php echo Language::getVar('NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_box_border pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                            </label>
                            <div class="col-md-4">
                                <select name="colors[<?php echo $type?>_column_box][border][width]" class="form-control">
                                    <?php for ($i = 0; $i <= 10; $i++) {
                                        $selected = isset($settings['colors'][$type . '_column_box']['border']['width']) ? ($settings['colors'][$type . '_column_box']['border']['width'] == $i ? 'selected' : '') : '';
                                        echo '<option ' . $selected . '>' . $i . '</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_box_border pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                            </label>
                            <div class="col-md-4">
                                <select name="colors[<?php echo $type?>_column_box][border][style]" class="form-control">
                                    <option value="solid" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                        Solid
                                    </option>
                                    <option value="dotted" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                        Dotted
                                    </option>
                                    <option value="dashed" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                        Dashed
                                    </option>
                                    <option value="double" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                        Double
                                    </option>
                                    <option value="groove" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                        Groove
                                    </option>
                                    <option value="ridge" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                        Ridge
                                    </option>
                                    <option value="inset" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                        Inset
                                    </option>
                                    <option value="outset" <?php if (isset($settings['colors'][$type . '_column_box']['border']['style']) && $settings['colors'][$type . '_column_box']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                        Outset
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group <?php echo $type?>_column_box_border pre-hide">
                            <label class="col-md-6 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="colors[<?php echo $type?>_column_box][border][color]" value="<?php echo isset($settings['colors'][$type . '_column_box']['border']['color']) ? $settings['colors'][$type . '_column_box']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    </div>
                    <?php if ($type == 'right') { echo '<div class="clearfix"><br /></div>'; } endfor; ?>
                </div>
                
                <div class="tab-pane" id="tab-colors-prices">
                    <h3><?php echo Language::getVar('SUMO_NOUN_PRICE_PLURAL')?></h3>
                    <?php
                    $colors = array();
                    $colors['price_color']      = '#4BB8E2';
                    $colors['old_price_color']  = '#B6B6B6';
                    $colors['new_price_color']  = '#ED5053';
                    $colors['tax_price_color']  = '#B6B6B6';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[prices][<?php echo $key?>]" value="<?php echo isset($settings['colors']['prices'][$key]) ? $settings['colors']['prices'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRICE_BOX')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_TEXT_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[price_box][text_color]" value="<?php echo isset($settings['price_box']['text_color']) ? $settings['price_box']['text_color'] : '#DEDEDE'; ?>" class="form-control color" style="width: 75%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BACKGROUND_HOVER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[price_box][background_hover_color]" value="<?php echo isset($settings['colors']['price_box']['background_hover_color']) ? $settings['colors']['price_box']['background_hover_color'] : '#FFFFFF';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SHADOW_HOVER')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[price_box][shadow_hover]" value="1" <?php if (isset($settings['colors']['price_box']['shadow_hover']) && $settings['colors']['price_box']['shadow_hover']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[price_box][shadow_hover]" value="0" <?php if (isset($settings['colors']['price_box']['shadow_hover']) && !$settings['colors']['price_box']['shadow_hover']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[price_box][border_enabled]" value="1" <?php if (isset($settings['colors']['price_box']['border_enabled']) && $settings['colors']['price_box']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[price_box][border_enabled]" value="0" <?php if (isset($settings['colors']['price_box']['border_enabled']) && !$settings['colors']['price_box']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group price_box_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                        </label>
                        <div class="col-md-3">
                            <select name="colors[price_box][border][width]" class="form-control">
                                <?php for ($i = 0; $i <= 10; $i++) {
                                    $selected = isset($settings['colors']['price_box']['border']['width']) ? ($settings['colors']['price_box']['border']['width'] == $i ? 'selected' : '') : '';
                                    echo '<option ' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group price_box_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                        </label>
                        <div class="col-md-3">
                            <select name="colors[price_box][border][style]" class="form-control">
                                <option value="solid" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                    Solid
                                </option>
                                <option value="dotted" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                    Dotted
                                </option>
                                <option value="dashed" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                    Dashed
                                </option>
                                <option value="double" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                    Double
                                </option>
                                <option value="groove" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                    Groove
                                </option>
                                <option value="ridge" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                    Ridge
                                </option>
                                <option value="inset" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                    Inset
                                </option>
                                <option value="outset" <?php if (isset($settings['colors']['price_box']['border']['style']) && $settings['colors']['price_box']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                    Outset
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group price_box_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[price_box][border][color]" value="<?php echo isset($settings['colors']['price_box']['border']['color']) ? $settings['colors']['price_box']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_SALE_BADGE')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[price_box][sale_badge]" value="<?php echo isset($settings['colors']['price_box']['sale_badge']) ? $settings['colors']['price_box']['sale_badge'] : '#ED5050';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane" id="tab-colors-buttons">
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            Buttons border radius
                        </label>
                        <div class="col-md-3">
                            <select name="colors[buttons][border_radius]" class="form-control">
                                <?php for ($i = 0; $i <= 15; $i++) {
                                    echo '<option ' . ((isset($settings['colors']['buttons']['border_radius']) && $settings['colors']['buttons']['border_radius'] == $i) || (!isset($settings['colors']['buttons']['border_radius']) && $i == 4) ? 'selected': '') . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_FORM_BUTTONS')?></h3>
                    <?php
                    $colors = array();
                    $colors['background_color']         = '#4BB8E2';
                    $colors['background_hover_color']   = '#B6B6B6';
                    $colors['text_color']               = '#ED5053';
                    $colors['text_hover_color']         = '#B6B6B6';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[buttons][forms][<?php echo $key?>]" value="<?php echo isset($settings['colors']['buttons']['forms'][$key]) ? $settings['colors']['buttons']['forms'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_CART_BUTTONS')?></h3>
                    <?php
                    $colors = array();
                    $colors['background_color']         = '#4BB8E2';
                    $colors['background_hover_color']   = '#B6B6B6';
                    $colors['text_color']               = '#ED5053';
                    $colors['text_hover_color']         = '#B6B6B6';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[buttons][cart][<?php echo $key?>]" value="<?php echo isset($settings['colors']['buttons']['cart'][$key]) ? $settings['colors']['buttons']['cart'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_OTHER_BOX_BUTTONS')?></h3>
                    <?php
                    $colors = array();
                    $colors['background_color']         = '#4BB8E2';
                    $colors['background_hover_color']   = '#B6B6B6';
                    $colors['text_color']               = '#ED5053';
                    $colors['text_hover_color']         = '#B6B6B6';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[buttons][pricebox][<?php echo $key?>]" value="<?php echo isset($settings['colors']['buttons']['pricebox'][$key]) ? $settings['colors']['buttons']['pricebox'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    
                </div>
                
                <div class="tab-pane" id="tab-colors-footer">
                    <h3>Footer</h3>
                    <?php
                    $colors = array();
                    $colors['background_color']         = '#373737';
                    $colors['title_color']              = '#FFFFFF';
                    $colors['text_color']               = '#ED5053';
                    $colors['link_color']               = '#4BB8E2';
                    $colors['link_hover_color']         = '#FFFFFF';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[footer][<?php echo $key?>]" value="<?php echo isset($settings['colors']['footer'][$key]) ? $settings['colors']['footer'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[footer][border_enabled]" value="1" <?php if (isset($settings['colors']['footer']['border_enabled']) && $settings['colors']['footer']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[footer][border_enabled]" value="0" <?php if (isset($settings['colors']['footer']['border_enabled']) && !$settings['colors']['footer']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group footer_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                        </label>
                        <div class="col-md-3">
                            <select name="colors[footer][border][width]" class="form-control">
                                <?php for ($i = 0; $i <= 10; $i++) {
                                    $selected = isset($settings['colors']['footer']['border']['width']) ? ($settings['colors']['footer']['border']['width'] == $i ? 'selected' : '') : '';
                                    echo '<option ' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group footer_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                        </label>
                        <div class="col-md-3">
                            <select name="colors[footer][border][style]" class="form-control">
                                <option value="solid" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                    Solid
                                </option>
                                <option value="dotted" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                    Dotted
                                </option>
                                <option value="dashed" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                    Dashed
                                </option>
                                <option value="double" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                    Double
                                </option>
                                <option value="groove" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                    Groove
                                </option>
                                <option value="ridge" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                    Ridge
                                </option>
                                <option value="inset" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                    Inset
                                </option>
                                <option value="outset" <?php if (isset($settings['colors']['footer']['border']['style']) && $settings['colors']['footer']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                    Outset
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group footer_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[footer][border][color]" value="<?php echo isset($settings['colors']['footer']['border']['color']) ? $settings['colors']['footer']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT_BOTTOM')?></h3>
                    <?php
                    $colors = array();
                    $colors['background_color']         = '#373737';
                    $colors['title_color']              = '#FFFFFF';
                    $colors['text_color']               = '#ED5053';
                    $colors['link_color']               = '#4BB8E2';
                    $colors['link_hover_color']         = '#FFFFFF';
                    
                    foreach ($colors as $key => $default): ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_' . strtoupper($key))?>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="colors[footer_copyright][<?php echo $key?>]" value="<?php echo isset($settings['colors']['footer_copyright'][$key]) ? $settings['colors']['footer_copyright'][$key] : $default;?>" class="form-control color" style="width:75%;">
                            </div>
                        </div>
                    <?php endforeach?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_ENABLED')?>
                        </label>
                        <div class="col-md-3">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[footer_copyright][border_enabled]" value="1" <?php if (isset($settings['colors']['footer_copyright']['border_enabled']) && $settings['colors']['footer_copyright']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('YES')?>
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="colors[footer_copyright][border_enabled]" value="0" <?php if (isset($settings['colors']['footer_copyright']['border_enabled']) && !$settings['colors']['footer_copyright']['border_enabled']) { echo 'checked'; }?>>
                                <?php echo Language::getVar('NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group footer_copyright_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_WIDTH')?>
                        </label>
                        <div class="col-md-3">
                            <select name="colors[footer_copyright][border][width]" class="form-control">
                                <?php for ($i = 0; $i <= 10; $i++) {
                                    $selected = isset($settings['colors']['footer_copyright']['border']['width']) ? ($settings['colors']['footer_copyright']['border']['width'] == $i ? 'selected' : '') : '';
                                    echo '<option ' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group footer_copyright_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_TYPE')?>
                        </label>
                        <div class="col-md-3">
                            <select name="colors[footer_copyright][border][style]" class="form-control">
                                <option value="solid" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'solid') { echo 'selected'; } ?>>
                                    Solid
                                </option>
                                <option value="dotted" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'dotted') { echo 'selected'; } ?>>
                                    Dotted
                                </option>
                                <option value="dashed" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'dashed') { echo 'selected'; } ?>>
                                    Dashed
                                </option>
                                <option value="double" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'double') { echo 'selected'; } ?>>
                                    Double
                                </option>
                                <option value="groove" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'groove') { echo 'selected'; } ?>>
                                    Groove
                                </option>
                                <option value="ridge" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'ridge') { echo 'selected'; } ?>>
                                    Ridge
                                </option>
                                <option value="inset" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'inset') { echo 'selected'; } ?>>
                                    Inset
                                </option>
                                <option value="outset" <?php if (isset($settings['colors']['footer_copyright']['border']['style']) && $settings['colors']['footer_copyright']['border']['style'] == 'outset') { echo 'selected'; } ?>>
                                    Outset
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group footer_copyright_border pre-hide">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_BORDER_COLOR')?>
                        </label>
                        <div class="col-md-3">
                            <input type="text" name="colors[footer_copyright][border][color]" value="<?php echo isset($settings['colors']['footer_copyright']['border']['color']) ? $settings['colors']['footer_copyright']['border']['color'] : '#CCCCCC';?>" class="form-control color" style="width:75%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" src="view/javascript/jquery/spectrum.js"></script>
<script type="text/javascript">
$(function(){
    $('#tab-colors a').on('shown.bs.tab', function(e) {
        //$('#tab-colors a').removeClass('active');
        //$('a[href=' + $(e.target).attr('href') + ']').addClass('active');
        //$(e.target).tab('show');
    });
    $('.loader').slideUp();
    
    $('#tab-colors a:first').tab('show');
    $('.color').spectrum({
        showPalette: true,
        palette: [
            ['black', 'white', 'blanchedalmond'],
            [],
            [],
            [],
            [],
        ],
        localStorageKey: "spectrum.sumobuilder"
    }).show();
    $('.pre-hide').hide();
    
    var firstrun = true;
    $('input[name="colors[main_column][background_enabled]"]').on('change', function() {
        if ($(this).is(':checked') && $(this).val() != undefined) {
            if ($(this).val() == 1) {
                if (firstrun) {
                    $('.main_background_enabled').show();
                    firstrun = false;
                }
                else {
                    if ($('.main_background_enabled').is(':visible') == false) {
                        $('.main_background_enabled').slideDown();
                    }
                }
            }
            else {
                if ($('.main_background_enabled').is(':visible')) {
                    $('.main_background_enabled').slideUp();
                }
            }
        }
    }).trigger('change');
    
    
    
    var iterateEach = {
        'input[name="colors[main_column][background_enabled]"]': '.main_background_enabled',
        'input[name="colors[main_column][border_enabled]"]': '.main_border',
        
        'input[name="colors[content_column][background_enabled]"]': '.content_background_enabled',
        'input[name="colors[content_column][border_enabled]"]': '.content_border',
        
        'input[name="colors[category_column_header][background_enabled]"]': '.category_column_header_background',
        'input[name="colors[category_column_header][border_enabled]"]': '.category_column_header_border',
        
        'input[name="colors[category_column_box][background_enabled]"]': '.category_column_box_background',
        'input[name="colors[category_column_box][border_enabled]"]': '.category_column_box_border',
        
        'input[name="colors[left_column_header][background_enabled]"]': '.left_column_header_background',
        'input[name="colors[right_column_header][background_enabled]"]': '.right_column_header_background',
        
        'input[name="colors[left_column_header][border_enabled]"]': '.left_column_header_border',
        'input[name="colors[right_column_header][border_enabled]"]': '.right_column_header_border',
        
        'input[name="colors[left_column_box][background_enabled]"]': '.left_column_box_background',
        'input[name="colors[right_column_box][background_enabled]"]': '.right_column_box_background',
        
        'input[name="colors[left_column_box][border_enabled]"]': '.left_column_box_border',
        'input[name="colors[right_column_box][border_enabled]"]': '.right_column_box_border',
        
        'input[name="colors[price_box][border_enabled]"]': '.price_box_border',
        
        'input[name="colors[headings][border_enabled]"]': '.heading_border',
        
        'input[name="colors[topbar][background_enabled]"]': '.topbar_background_enabled',
        
        'input[name="colors[header][background_enabled]"]': '.header_background_enabled',
        
        
        'input[name="colors[footer][border_enabled]"]': '.footer_border',
        'input[name="colors[footer_copyright][border_enabled]"]': '.footer_copyright_border',
        
    };
    $.each(iterateEach, function (watch, find) {
        var firstrun = true;
        $(watch).on('change', function() {
            if ($(this).is(':checked') && $(this).val() != undefined) {
                if ($(this).val() == 1) {
                    if (firstrun) {
                        $(find).show();
                        firstrun = false;
                    }
                    else {
                        if ($(find).is(':visible') == false) {
                            $(find).slideDown();
                        }
                    }
                }
                else {
                    if ($(find).is(':visible')) {
                        $(find).slideUp();
                    }
                }
            }
        }).trigger('change');
    })
});
</script>