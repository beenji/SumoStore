<form method="post" id="pages-form" class="form-horizontal">
    <div class="col-md-12">
        <div class="col-md-2">
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs" id="tab-pages">
                    <li>
                        <a href="#tab-pages-layout" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_LAYOUT_SINGULAR') ?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-pages-header" data-toggle="tab">
                            Header
                        </a>
                    </li>
                    <li>
                        <a href="#tab-pages-menu" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_MENU')?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-pages-home" data-toggle="tab">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="#tab-pages-category" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_CATEGORY_PLURAL')?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-pages-product" data-toggle="tab">
                            <?php echo Language::getVar('SUMO_NOUN_PRODUCT_PLURAL')?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-pages-footer" data-toggle="tab">
                            Footer
                        </a>
                    </li>
                </ul>
            </div>
            <div class="clearfix"><br /></div>
        </div>
        <div class="col-md-10">
            <div class="tab-content">
                <div class="tab-pane" id="tab-pages-layout">
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_NOUN_LAYOUT_STYLE')?>
                        </label>
                        <div class="col-md-4">
                            <select name="general[layout_style]" class="form-control" id="layout_style">
                                <option value="full_width" <?php if (isset($settings['general']['layout_style']) && $settings['general']['layout_style'] == 'full_width') { echo 'selected'; } ?>>
                                    <?php echo Language::getVar('SUMO_NOUN_FULL_WIDTH')?>
                                </option>
                                <option value="fixed" <?php if (isset($settings['general']['layout_style']) && $settings['general']['layout_style'] == 'fixed') { echo 'selected'; } ?>>
                                    <?php echo Language::getVar('SUMO_NOUN_FIXED_WIDTH')?>
                                </option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-7">
                            <span class="help-block">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_LAYOUT_STYLE')?>
                            </span>
                        </div>
                    </div>
                    <div class="form-group fixed">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_NOUN_MAX_WIDTH')?>
                        </label>
                        <div class="col-md-4">
                            <select name="general[max_width]" class="form-control">
                                <option value="980" <?php if (isset($settings['general']['max_width']) && $settings['general']['max_width'] == '980') { echo 'selected'; } ?>>980 px</option>
                                <option value="1170" <?php if (isset($settings['general']['max_width']) && $settings['general']['max_width'] == '1170') { echo 'selected'; } ?>>1170 px</option>
                                <option value="1440" <?php if (isset($settings['general']['max_width']) && $settings['general']['max_width'] == '1440') { echo 'selected'; } ?>>1440 px</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_NOUN_MOBILE_LAYOUT')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="general[mobile]" value="1" <?php if ((isset($settings['general']['mobile']) && $settings['general']['mobile']) || !isset($settings['general']['mobile'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="general[mobile]" value="0" <?php if (isset($settings['general']['mobile']) && !$settings['general']['mobile']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_MOBILE')?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-pages-header">
                    <h3><?php echo Language::getVar('SUMO_NOUN_GENERAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_NOUN_FIXED_HEADER')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="header[fixed_header]" value="1" <?php if ((isset($settings['header']['fixed_header']) && $settings['header']['fixed_header']) || !isset($settings['header']['fixed_header'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="header[fixed_header]" value="0"  <?php if (isset($settings['header']['fixed_header']) && !$settings['header']['fixed_header']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FIXED_HEADER')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_NOUN_AUTO_SUGGEST')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="header[auto_suggest]" value="1" <?php if ((isset($settings['header']['auto_suggest']) && $settings['header']['auto_suggest']) || !isset($settings['header']['auto_suggest'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="header[auto_suggest]" value="0"  <?php if (isset($settings['header']['auto_suggest']) && !$settings['header']['auto_suggest']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FIXED_AUTO_SUGGEST')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_ARRANGE')?></h3>
                    <div class="col-md-12 well column-div">
                        <div class="col-md-4" class="column-div column-1">
                            <span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_PART')?> 1</span>
                            <input type="radio" name="header[columns][1]" class="column-empty" value="empty" <?php if (isset($settings['header']['columns'][1]) && $settings['header']['columns'][1] == 'empty') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_EMPTY')?><br />
                            <input type="radio" name="header[columns][1]" class="column-logo" value="logo" <?php if ((isset($settings['header']['columns'][1]) && $settings['header']['columns'][1] == 'logo') || !isset($settings['header']['columns'][1])) { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_LOGO')?><br />
                            <input type="radio" name="header[columns][1]" class="column-search" value="search" <?php if (isset($settings['header']['columns'][1]) && $settings['header']['columns'][1] == 'search') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_SEARCHBAR')?><br />
                            <input type="radio" name="header[columns][1]" class="column-cart" value="cart" <?php if (isset($settings['header']['columns'][1]) && $settings['header']['columns'][1] == 'cart') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_SHOPPING_CART')?>
                        </div>
                        <div class="col-md-4" class="column-div column-2">
                            <span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_PART')?> 2</span>
                            <input type="radio" name="header[columns][2]" class="column-empty" value="empty" <?php if (isset($settings['header']['columns'][2]) && $settings['header']['columns'][2] == 'empty') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_EMPTY')?><br />
                            <input type="radio" name="header[columns][2]" class="column-logo" value="logo" <?php if (isset($settings['header']['columns'][2]) && $settings['header']['columns'][2] == 'logo') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_LOGO')?><br />
                            <input type="radio" name="header[columns][2]" class="column-search" value="search" <?php if ((isset($settings['header']['columns'][2]) && $settings['header']['columns'][2] == 'search') || !isset($settings['header']['columns'][2])) { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_SEARCHBAR')?><br />
                            <input type="radio" name="header[columns][2]" class="column-cart" value="cart" <?php if (isset($settings['header']['columns'][2]) && $settings['header']['columns'][2] == 'cart') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_SHOPPING_CART')?>
                        </div>
                        <div class="col-md-4" class="column-div column-3">
                            <span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_PART')?> 3</span>
                            <input type="radio" name="header[columns][3]" class="column-empty" value="empty" <?php if (isset($settings['header']['columns'][3]) && $settings['header']['columns'][3] == 'empty') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_EMPTY')?><br />
                            <input type="radio" name="header[columns][3]" class="column-logo" value="logo" <?php if (isset($settings['header']['columns'][3]) && $settings['header']['columns'][3] == 'logo') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_LOGO')?><br />
                            <input type="radio" name="header[columns][3]" class="column-search" value="search" <?php if (isset($settings['header']['columns'][3]) && $settings['header']['columns'][3] == 'search') { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_SEARCHBAR')?><br />
                            <input type="radio" name="header[columns][3]" class="column-cart" value="cart" <?php if ((isset($settings['header']['columns'][3]) && $settings['header']['columns'][3] == 'cart') || !isset($settings['header']['columns'][3])) { echo 'checked'; } ?>> <?php echo Language::getVar('SUMO_NOUN_SHOPPING_CART')?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"><br /></div>
                </div>
                <div class="tab-pane" id="tab-pages-menu">
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_HOME')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_HOME_LINK_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[home_link]" value="1" <?php if ((isset($settings['menu']['home_link']) && $settings['menu']['home_link']) || !isset($settings['menu']['home_link'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[home_link]" value="0" <?php if (isset($settings['menu']['home_link']) && !$settings['menu']['home_link']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_HOME_LINK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group home_link">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_HOME_LINK_AS_ICON')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[home_link_as_icon]" value="1" <?php if ((isset($settings['menu']['home_link_as_icon']) && $settings['menu']['home_link_as_icon']) || !isset($settings['menu']['home_link_as_icon'])) { echo 'checked'; } ?>>
                                <span style="font-size:20px;"><i class="icon-home"></i></span>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[home_link_as_icon]" value="0" <?php if ((isset($settings['menu']['home_link_as_icon']) && !$settings['menu']['home_link_as_icon']) || !isset($settings['menu']['home_link'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_HOME') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_HOME_LINK_AS_ICON')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_CATEGORY_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_CATEGORY_LINK_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[category_link]" value="1" <?php if ((isset($settings['menu']['category_link']) && $settings['menu']['category_link']) || !isset($settings['menu']['category_link'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[category_link]" value="0" <?php if (isset($settings['menu']['category_link']) && !$settings['menu']['category_link']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_LINK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group category_link">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_CATEGORY_LINK_STYLE')?>
                        </label>
                        <div class="col-md-6">
                            <select name="menu[category_link_style]" class="form-control">
                                <option value=""><?php echo Language::getVar('SUMO_FORM_SELECT_CHOOSE')?></option>
                                <option value="horizontal" <?php if ((isset($settings['menu']['category_link_style']) && $settings['menu']['category_link_style'] == 'horizontal') || !isset($settings['menu']['category_link_style'])) { echo 'selected'; }?>><?php echo Language::getVar('SUMO_NOUN_HORIZONTAL')?></option>
                                <option value="vertical" <?php if (isset($settings['menu']['category_link_style']) && $settings['menu']['category_link_style'] == 'vertical') { echo 'selected'; }?>><?php echo Language::getVar('SUMO_NOUN_VERTICAL')?></option>
                            </select>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_LINK_STYLE')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group category_link_horizontal">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_CATEGORY_LINK_ICONS')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[category_link_items]" value="1" <?php if (isset($settings['menu']['category_link_items']) && $settings['menu']['category_link_items']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[category_link_items]" value="0" <?php if ((isset($settings['menu']['category_link_items']) && !$settings['menu']['category_link_items']) || !isset($settings['menu']['category_link_items'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_LINK_ICONS')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group category_link_horizontal">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_CATEGORY_LINK_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="menu[category_link_limit]" class="form-control">
                                <?php foreach(range (3, 5) as $i) {
                                    $selected = '';
                                    if ((isset($settings['home']['category_link_limit']) && $settings['home']['category_link_limit'] == $i) || $i == 5) {
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_LINK_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group category_link">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_LINK_DEEP')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[category_link_deep]" value="1" <?php if (isset($settings['menu']['category_link_deep']) && $settings['menu']['category_link_deep']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[category_link_deep]" value="0" <?php if ((isset($settings['menu']['category_link_deep']) && !$settings['menu']['category_link_deep']) || !isset($settings['menu']['category_link_deep'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_LINK_DEEP')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_MANUFACTURER_LINK_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                           <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[manufacturer_link]" value="1" <?php if ((isset($settings['menu']['manufacturer_link']) && $settings['menu']['manufacturer_link']) || !isset($settings['menu']['manufacturer_link'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[manufacturer_link]" value="0" <?php if (isset($settings['menu']['manufacturer_link']) && !$settings['menu']['manufacturer_link']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_MANUFACTURER_LINK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group manufacturer_link">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_MANUFACTURER_LINK_STYLE')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[manufacturer_link_style]" value="1" <?php if ((isset($settings['menu']['manufacturer_link_style']) && $settings['menu']['manufacturer_link_style']) == 1 || !isset($settings['menu']['manufacturer_link_style'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_NAME') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[manufacturer_link_style]" value="2" <?php if (isset($settings['menu']['manufacturer_link_style']) && $settings['menu']['manufacturer_link_style'] == 2) { echo 'checked'; }?>>
                                <?php echo Language::getVar('SUMO_NOUN_LOGO') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[manufacturer_link_style]" value="3" <?php if (isset($settings['menu']['manufacturer_link_style']) && $settings['menu']['manufacturer_link_style'] == 3) { echo 'checked'; }?>>
                                <?php echo Language::getVar('SUMO_NOUN_NAME') ?> &amp; <?php echo strtolower(Language::getVar('SUMO_NOUN_LOGO')) ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_MANUFACTURER_LINK_STYLE')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group manufacturer_link">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_MANUFACTURER_LINK_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="menu[manufacturer_link_limit]" class="form-control">
                                <?php foreach(range (3, 12) as $i) {
                                    $selected = '';
                                    if ((isset($settings['home']['manufacturer_link_limit']) && $settings['home']['manufacturer_link_limit'] == $i) || $i == 5) {
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_MANUFACTURER_LINK_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_INFORMATION_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_INFORMATION_LINK_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[information_link]" value="1" <?php if ((isset($settings['menu']['information_link']) && $settings['menu']['information_link']) || !isset($settings['menu']['information_link'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[information_link]" value="0" <?php if (isset($settings['menu']['information_link']) && !$settings['menu']['information_link']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_INFORMATION_LINK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group information_link">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_INFORMATION_LINK_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="menu[information_link_limit]" class="form-control">
                                <?php foreach(range (3, 5) as $i) {
                                    $selected = '';
                                    if ((isset($settings['home']['information_link_limit']) && $settings['home']['information_link_limit'] == $i) || $i == 5) {
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_INFORMATION_LINK_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_CONTACT_US')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_MENU_CONTACT_LINK_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[contact_link]" value="1" <?php if ((isset($settings['menu']['contact_link']) && $settings['menu']['contact_link']) || !isset($settings['menu']['contact_link'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="menu[contact_link]" value="0" <?php if (isset($settings['menu']['contact_link']) && !$settings['menu']['contact_link']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CONTACT_LINK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-pages-home">
                    <p class="hidden"><span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_GOTO_LAYOUT', Language::getVar('SUMO_NOUN_LAYOUTS'))?></span></p>
                    <h3>Slideshow</h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SLIDESHOW_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[slideshow_enabled]" value="1" <?php if ((isset($settings['home']['slideshow_enabled']) && $settings['home']['slideshow_enabled']) || !isset($settings['home']['slideshow_enabled'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[slideshow_enabled]" value="0" <?php if (isset($settings['home']['slideshow_enabled']) && !$settings['home']['slideshow_enabled']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_SLIDESHOW')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="slideshow_enabled">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <ul class="nav nav-tabs" id="slideshowtabs">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <li><a href="#slideshow-<?php echo $i?>" data-toggle="tab">Slide <?php echo $i?></a></li>
                                <?php endfor?>
                            </ul>
                        </div>
                        <div class="clearfix"><br /></div>
                        <div class="col-md-12">
                            <div class="tab-content">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="tab-pane" id="slideshow-<?php echo $i?>">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo Language::getVar('SUMO_NOUN_ACTIVE')?>
                                        </label>
                                        <div class="col-md-6">
                                            <div class="col-md-3 inline-radio">
                                                <input type="radio" class="slide" name="home[slideshow][slides][<?php echo $i?>][enabled]" value="1" <?php if ((isset($settings['home']['slideshow']['slides'][$i]['enabled']) && $settings['home']['slideshow']['slides'][$i]['enabled']) || !isset($settings['home']['slideshow']['slides'][$i]['enabled'])) { echo 'checked'; } ?>>
                                                <?php echo Language::getVar('YES') ?>
                                            </div>
                                            <div class="col-md-3 inline-radio">
                                                <input type="radio" class="slide" name="home[slideshow][slides][<?php echo $i?>][enabled]" value="0" <?php if (isset($settings['home']['slideshow']['slides'][$i]['enabled']) && !$settings['home']['slideshow']['slides'][$i]['enabled']) { echo 'checked'; } ?>>
                                                <?php echo Language::getVar('NO') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content-form">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo Language::getVar('SUMO_NOUN_BACKGROUND_IMAGE')?>
                                            </label>
                                            <div class="col-md-8">
                                                <div class="col-md-6">
                                                    <input type="text" name="home[slideshow][slides][<?php echo $i?>][background]" value="<?php if (isset($settings['home']['slideshow']['slides'][$i]['background'])) { echo $settings['home']['slideshow']['slides'][$i]['background']; } else { echo ''; } ?>" class="upload_file-<?php echo $i?> form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <a class="button-upload btn btn-sm btn-primary"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SELECT_IMAGE_TO_UPLOAD')?></a>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-md-12 upload_message-<?php echo $i?>">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo Language::getVar('SUMO_NOUN_CONTENT')?>
                                            </label>
                                            <div class="col-md-9">
                                                <?php foreach ($languages as $list): ?>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                                        </span>
                                                        <textarea name="home[slideshow][slides][<?php echo $i?>][content][<?php echo $list['language_id']?>]" class="redactor"><?php if (isset($settings['home']['slideshow']['slides'][$i]['content'][$list['language_id']])) { echo $settings['home']['slideshow']['slides'][$i]['content'][$list['language_id']]; } else { echo ''; } ?></textarea>
                                                    </div>
                                                    <br />
                                                <?php endforeach?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SLIDER_BUTTON_URL')?>
                                            </label>
                                            <div class="col-md-6">
                                                <input type="text" name="home[slideshow][slides][<?php echo $i?>][url]" name="home[slideshow][slides][<?php echo $i?>][url]" class="form-control" value="<?php if (isset($settings['home']['slideshow']['slides'][$i]['url'])) { echo $settings['home']['slideshow']['slides'][$i]['url']; } else { echo ''; } ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SLIDER_BUTTON_TEXT')?>
                                            </label>
                                            <div class="col-md-4">
                                                <?php foreach ($languages as $list): ?>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                                        </span>
                                                        <input name="home[slideshow][slides][<?php echo $i?>][button][<?php echo $list['language_id']?>]" class="form-control" value="<?php if (isset($settings['home']['slideshow']['slides'][$i]['button'][$list['language_id']])) { echo $settings['home']['slideshow']['slides'][$i]['button'][$list['language_id']]; } else { echo ''; } ?>">
                                                    </div>
                                                    <br />
                                                <?php endforeach?>
                                            </div>
                                            <div class="clearfix"><br /></div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-5">
                                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_SLIDER_BUTTON_TEXT_HELP')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor?>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_CATEGORY_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HOME_CATEGORY_BLOCK')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[category_wall]" value="1" <?php if ((isset($settings['home']['category_wall']) && $settings['home']['category_wall']) || !isset($settings['home']['category_wall'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[category_wall]" value="0" <?php if (isset($settings['home']['category_wall']) && !$settings['home']['category_wall']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_BLOCK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group category_wall">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HOME_CATEGORY_BLOCK_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="home[category_wall_limit]" class="form-control">
                                <?php foreach(range (4, 16) as $i) {
                                    $selected = '';
                                    if ((isset($settings['home']['category_wall_limit']) && $settings['home']['category_wall_limit'] == $i) || $i == 8) { 
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_BLOCK_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <h3><?php echo Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HOME_MANUFACTURER_BLOCK')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[manufacturer_wall]" value="1" <?php if ((isset($settings['home']['manufacturer_wall']) && $settings['home']['manufacturer_wall']) || !isset($settings['home']['manufacturer_wall'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[manufacturer_wall]" value="0" <?php if (isset($settings['home']['manufacturer_wall']) && !$settings['home']['manufacturer_wall']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_MANUFACTURER_BLOCK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group manufacturer_wall">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HOME_MANUFACTURER_BLOCK_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="home[manufacturer_wall_limit]" class="form-control">
                                <?php foreach(range (4, 16) as $i) {
                                    $selected = '';
                                    if ((isset($settings['home']['manufacturer_wall_limit']) && $settings['home']['manufacturer_wall_limit'] == $i) || $i == 8) { 
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-6">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_MANUFACTURER_BLOCK_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <h3><?php echo Language::getVar('SUMO_NOUN_LATEST_PRODUCTS')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HOME_LATEST_PRODUCTS_BLOCK')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[latest_products]" value="1" <?php if ((isset($settings['home']['latest_products']) && $settings['home']['latest_products']) || !isset($settings['home']['latest_products'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="home[latest_products]" value="0" <?php if (isset($settings['home']['latest_products']) && !$settings['home']['latest_products']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_LATEST_PRODUCTS_BLOCK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group latest_products">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HOME_LATEST_PRODUCTS_BLOCK_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="home[latest_products_limit]" class="form-control">
                                <?php foreach(range (4, 20) as $i) {
                                    $selected = '';
                                    if ((isset($settings['home']['latest_products_limit']) && $settings['home']['latest_products_limit'] == $i) || $i == 10) { 
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-6">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_LATEST_PRODUCTS_BLOCK_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-pages-category">
                    <p><span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_GOTO_LAYOUT', Language::getVar('SUMO_NOUN_LAYOUTS'))?></span></p>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_PRODUCT_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCT_DISPLAY_DEFAULT')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="category[product_display_default]" value="grid" <?php if ((isset($settings['category']['product_display_default']) && $settings['category']['product_display_default'] == 'grid') || !isset($settings['category']['product_display_default'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_GRID') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="category[product_display_default]" value="list" <?php if (isset($settings['category']['product_display_default']) && $settings['category']['product_display_default'] == 'list') { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_LIST') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_PRODUCT_DISPLAY_DEFAULT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCT_GRID_LIMIT')?>
                        </label>
                        <div class="col-md-6">
                            <select name="category[product_grid_limit]" class="form-control">
                                <?php foreach(range (3, 5) as $i) {
                                    $selected = '';
                                    if ((isset($settings['category']['product_grid_limit']) && $settings['category']['product_grid_limit'] == $i) || $i == 4) { 
                                        $selected = ' selected';
                                    }
                                echo '<option' . $selected . '>' . $i . '</option>';
                                } ?>
                            </select>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_PRODUCT_GRID_LIMIT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_SUBCATEGORY_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_SUBCATEGORIES_ENABLED')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="category[subcategories_enabled]" value="1" <?php if ((isset($settings['category']['subcategories_enabled']) && $settings['category']['subcategories_enabled']) || !isset($settings['category']['subcategories_enabled'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="category[subcategories_enabled]" value="0" <?php if (isset($settings['category']['subcategories_enabled']) && !$settings['category']['subcategories_enabled']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_SUBCATEGORIES')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group subcategories_enabled">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_SUBCATEGORY_DISPLAY_DEFAULT')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="category[subcategory_display_default]" value="grid" <?php if ((isset($settings['category']['subcategory_display_default']) && $settings['category']['subcategory_display_default'] == 'grid') || !isset($settings['category']['subcategory_display_default'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_GRID') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="category[subcategory_display_default]" value="list" <?php if (isset($settings['category']['subcategory_display_default']) && $settings['category']['subcategory_display_default'] == 'list') { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_LIST') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_CATEGORY_SUBCATEGORY_DISPLAY_DEFAULT')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_PRODUCT_SINGULAR')?> box</h3>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_NAME')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_name]" value="1" <?php if ((isset($settings['category']['productbox_show_name']) && $settings['category']['productbox_show_name']) || !isset($settings['category']['productbox_show_name'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_name]" value="0" <?php if (isset($settings['category']['productbox_show_name']) && !$settings['category']['productbox_show_name']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_ZOOM_OR_SWAP')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_image_zoom]" value="1" <?php if ((isset($settings['category']['productbox_image_zoom']) && $settings['category']['productbox_image_zoom']) || !isset($settings['category']['productbox_image_zoom'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('SUMO_NOUN_IMAGE_ZOOM') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_image_zoom]" value="0" <?php if (isset($settings['category']['productbox_image_zoom']) && !$settings['category']['productbox_image_zoom']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('SUMO_NOUN_IMAGE_SWAP') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_PRICE')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_price]" value="1" <?php if ((isset($settings['category']['productbox_show_price']) && $settings['category']['productbox_show_price']) || !isset($settings['category']['productbox_show_price'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_price]" value="0" <?php if (isset($settings['category']['productbox_show_price']) && !$settings['category']['productbox_show_price']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_SALE')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_sale]" value="1" <?php if ((isset($settings['category']['productbox_show_sale']) && $settings['category']['productbox_show_sale']) || !isset($settings['category']['productbox_show_sale'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_sale]" value="0" <?php if (isset($settings['category']['productbox_show_sale']) && !$settings['category']['productbox_show_sale']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_MANUFACTURER', Language::getVar('SUMO_NOUN_MANUFACTURER_SINGULAR'))?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_manufacturer]" value="1" <?php if ((isset($settings['category']['productbox_show_manufacturer']) && $settings['category']['productbox_show_manufacturer']) || !isset($settings['category']['productbox_show_manufacturer'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_manufacturer]" value="0" <?php if (isset($settings['category']['productbox_show_manufacturer']) && !$settings['category']['productbox_show_manufacturer']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_RATING')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_rating]" value="1" <?php if ((isset($settings['category']['productbox_show_rating']) && $settings['category']['productbox_show_rating']) || !isset($settings['category']['productbox_show_rating'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_rating]" value="0" <?php if (isset($settings['category']['productbox_show_rating']) && !$settings['category']['productbox_show_rating']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_CART', Language::getVar('SUMO_NOUN_CART'))?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_cart]" value="1" <?php if ((isset($settings['category']['productbox_show_cart']) && $settings['category']['productbox_show_cart']) || !isset($settings['category']['productbox_show_cart'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_cart]" value="0" <?php if (isset($settings['category']['productbox_show_cart']) && !$settings['category']['productbox_show_cart']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_WISHLIST', Language::getVar('SUMO_NOUN_WISHLIST'))?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_wishlist]" value="1" <?php if ((isset($settings['category']['productbox_show_wishlist']) && $settings['category']['productbox_show_wishlist']) || !isset($settings['category']['productbox_show_wishlist'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_wishlist]" value="0" <?php if (isset($settings['category']['productbox_show_wishlist']) && !$settings['category']['productbox_show_wishlist']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_SHOW_COMPARE', Language::getVar('SUMO_NOUN_COMPARE'))?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_compare]" value="1" <?php if ((isset($settings['category']['productbox_show_compare']) && $settings['category']['productbox_show_compare']) || !isset($settings['category']['productbox_show_compare'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('YES') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_show_compare]" value="0" <?php if (isset($settings['category']['productbox_show_compare']) && !$settings['category']['productbox_show_compare']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('NO') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-5 control-label">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_PRODUCTBOX_TEXT_ALIGN')?>
                            </label>
                            <div class="col-md-6">
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_text_align]" value="1" <?php if ((isset($settings['category']['productbox_text_align']) && $settings['category']['productbox_text_align']) || !isset($settings['category']['productbox_text_align'])) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('SUMO_NOUN_CENTER') ?>
                                </div>
                                <div class="inline-radio col-md-6">
                                    <input type="radio" name="category[productbox_text_align]" value="0" <?php if (isset($settings['category']['productbox_text_align']) && !$settings['category']['productbox_show_wishlist']) { echo 'checked'; } ?>>
                                    <?php echo Language::getVar('SUMO_NOUN_LEFT') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <?php /* RC3, preview */ ?>
                    </div>
                </div>
                <div class="tab-pane" id="tab-pages-product">
                    <h3><?php echo Language::getVar('SUMO_NOUN_PRODUCT_SINGULAR')?> layout</h3>
                    <div class="col-md-12">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="col-md-2" onclick="$(this).find('input').prop('checked', 1);">
                            <img src="../image/no_image.jpg" /><br />
                            
                            <div class="inline-radio">
                                <input type="radio" name="product[layout]" value="<?php echo $i?>" <?php if ((isset($settings['product']['layout']) && $settings['product']['layout'] == $i) || !isset($settings['product']['layout']) && $i == 1) { echo 'checked'; } ?>>
                                [preview <?php echo $i?>]
                            </div>
                        </div>
                        <?php endfor?>
                        <div class="clearfix"></div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_NOUN_IMAGE_PLURAL')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_IMAGE_DISPLAY_TYPE')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="product[display_image]" value="lightbox" <?php if ((isset($settings['product']['display_image']) && $settings['product']['display_image'] == 'lightbox') || !isset($settings['product']['display_image'])) { echo 'checked'; } ?>>
                                Lightbox
                            </div>
                            <div class="inline-radio col-md-4">
                                <input type="radio" name="product[display_image]" value="zoom" <?php if (isset($settings['product']['display_image']) && $settings['product']['display_image'] == 'zoom') { echo 'checked'; } ?>>
                                Zoom
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_IMAGE_DISPLAY_TYPE')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_INFO')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_INFO_MANUFACTURER', Language::getVar('SUMO_NOUN_MANUFACTURER_SINGULAR'))?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_manufacturer]" value="1" <?php if ((isset($settings['product']['display_manufacturer']) && $settings['product']['display_manufacturer']) || !isset($settings['product']['display_manufacturer'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_manufacturer]" value="0" <?php if (isset($settings['product']['display_manufacturer']) && !$settings['product']['display_manufacturer']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_INFO_MANUFACTURER')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_INFO_PERCENTAGE')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_percentage]" value="1" <?php if ((isset($settings['product']['display_percentage']) && $settings['product']['display_percentage']) || !isset($settings['product']['display_percentage'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_percentage]" value="0" <?php if (isset($settings['product']['display_percentage']) && !$settings['product']['display_percentage']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_INFO_PERCENTAGE')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_INFO_QUANTITY')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_quantity]" value="1" <?php if ((isset($settings['product']['display_quantity']) && $settings['product']['display_quantity']) || !isset($settings['product']['display_quantity'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_quantity]" value="0" <?php if (isset($settings['product']['display_quantity']) && !$settings['product']['display_quantity']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_INFO_QUANTITY')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_INFO_VIEWS')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_views]" value="1" <?php if ((isset($settings['product']['display_views']) && $settings['product']['display_views']) || !isset($settings['product']['display_views'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_views]" value="0" <?php if (isset($settings['product']['display_views']) && !$settings['product']['display_views']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_INFO_VIEWS')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_INFO_TAX')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_tax]" value="1" <?php if ((isset($settings['product']['display_tax']) && $settings['product']['display_tax']) || !isset($settings['product']['display_tax'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_tax]" value="0" <?php if (isset($settings['product']['display_tax']) && !$settings['product']['display_tax']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_INFO_TAX')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_RELATED')?></h3>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_RELATED_BLOCK')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_related]" value="1" <?php if ((isset($settings['product']['display_related']) && $settings['product']['display_related']) || !isset($settings['product']['display_related'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('YES') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_related]" value="0" <?php if (isset($settings['product']['display_related']) && !$settings['product']['display_related']) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('NO') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_RELATED_BLOCK')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group product_related">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_RELATED_POSITION')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_related_position]" value="bottom" <?php if ((isset($settings['product']['display_related_position']) && $settings['product']['display_related_position'] == 'bottom') || !isset($settings['product']['display_related_position'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('BOTTOM') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_related_position]" value="right" <?php if (isset($settings['product']['display_related_position']) && $settings['product']['display_related_position'] == 'right') { echo 'checked'; } ?>>
                                <?php echo Language::getVar('RIGHT') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_RELATED_POSITION')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php /*
                    <div class="form-group product_related">
                        <label class="col-md-3 control-label">
                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_PRODUCT_RELATED_STYLE')?>
                        </label>
                        <div class="col-md-6">
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_related_style]" value="grid" <?php if ((isset($settings['product']['display_related_style']) && $settings['product']['display_related_style'] == 'grid')) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_GRID') ?>
                            </div>
                            <div class="inline-radio col-md-3">
                                <input type="radio" name="product[display_related_style]" value="slider" <?php if (isset($settings['product']['display_related_style']) && $settings['product']['display_related_style'] == 'slider' || !isset($settings['product']['display_related_style'])) { echo 'checked'; } ?>>
                                <?php echo Language::getVar('SUMO_NOUN_SLIDER') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span class="help-block">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_PRODUCT_RELATED_STYLE')?>
                                </span>
                            </div>
                        </div>
                    </div>
                    */ ?>
                </div>
                <div class="tab-pane" id="tab-pages-footer">
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="#tab-pages-footer-blocks" data-toggle="tab">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_BLOCKS')?>
                            </a>
                        </li>
                        <li>
                            <a href="#tab-pages-footer-copyright" data-toggle="tab">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT')?>
                            </a>
                        </li>
                        <li>
                            <a href="#tab-pages-footer-text" data-toggle="tab">
                                <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_TEXT')?>
                            </a>
                        </li>
                    </ul>
                    <div class="clearfix"><br /></div>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-pages-footer-blocks">
                            <p><span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_BLOCKS')?></span></p>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_BLOCKS_AMOUNT')?>
                                </label>
                                <div class="col-md-6">
                                    <select name="footer[blocks][amount]" class="form-control">
                                        <?php 
                                        for($i = 0; $i <= 4; $i++) {
                                            $selected = '';
                                            if ((isset($settings['footer']['blocks']['amount']) && $settings['footer']['blocks']['amount'] == $i) || $i == 3) {
                                                $selected = ' selected';
                                            }
                                            echo '<option' . $selected . '>' . $i . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <?php for ($i = 0; $i <= 4; $i++): ?>
                            <div class="form-group footer-block" <?php if (!$i) { echo 'style="display:none;"'; }?>>
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_NOUN_TITLE') . ' ' . $i ?>
                                </label>
                                <div class="col-md-6">
                                    <?php foreach ($languages as $list): ?>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                        </span>
                                        <input type="text" name="footer[blocks][blocks][<?php echo $i?>][title][<?php echo $list['language_id']?>]" value="<?php if (isset($settings['footer']['blocks']['blocks'][$i]['title'][$list['language_id']])) { echo $settings['footer']['blocks']['blocks'][$i]['title'][$list['language_id']]; } ?>" class="form-control product-name" language="<?php echo $list['language_id']?>" />
                                    </div>
                                    <br />
                                    <?php endforeach; ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-3"></div>
                                <div class="col-md-4">
                                    <div class="inline-radio choose-type col-md-5">
                                        <input type="radio" name="footer[blocks][<?php echo $i?>][type]" value="content" <?php if (isset($settings['footer']['blocks'][$i]['type']) && $settings['footer']['blocks'][$i]['type'] == 'content') { echo 'checked'; }?>> Content
                                    </div>
                                    <div class="inline-radio choose-type col-md-5">
                                        <input type="radio" name="footer[blocks][<?php echo $i?>][type]" value="links" <?php if (isset($settings['footer']['blocks'][$i]['type']) && $settings['footer']['blocks'][$i]['type'] == 'links') { echo 'checked'; }?>> Links
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="footer-block-links">
                                    <table class="table table-striped table-urls" row="<?php echo $i?>">
                                        <thead>
                                            <tr>
                                                <th style="width: 60%" colspan="2">URL</th>
                                                <th style="width: 30%"><?php echo Language::getVar('SUMO_NOUN_DISPLAY_TITLE')?></th>
                                                <th><span class="btn btn-sm btn-success btn-add-url"><i class="icon-plus"></i></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($settings['footer']['blocks']['blocks'][$i]['links'])) {
                                                foreach ($settings['footer']['blocks']['blocks'][$i]['links'] as $nr => $list): 
                                                    if (empty($list['url'])) {
                                                        continue;
                                                    }?>
                                            <tr number="<?php echo $nr?>">
                                                <td>
                                                    <input type="text" name="footer[blocks][blocks][<?php echo $i?>][links][<?php echo $nr?>][url]" class="form-control input-url" value="<?php echo $list['url']?>">
                                                </td>
                                                <td><select class="form-control link-holder"></select></td>
                                                <td>
                                                    <br />
                                                    <?php foreach ($languages as $lang): ?>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><img src="view/image/flags/<?php echo $lang['image']?>" title="<?php echo $lang['name']?>" /></span>
                                                        <input type="text" name="footer[blocks][blocks][<?php echo $i?>][links][<?php echo $nr?>][name][<?php echo $lang['language_id']?>]" value="<?php if (isset($list['name'][$lang['language_id']])) { echo $list['name'][$lang['language_id']]; } ?>" class="form-control product-name" language="<?php echo $lang['language_id']?>" />
                                                    </div>
                                                    <br />
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><span class="btn btn-sm btn-danger btn-remove-url"><i class="icon-trash"></i></span></td>
                                            </tr>  
                                                <?php endforeach;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <div class="clearfix"></div>
                                    <div class="col-md-3"></div>
                                    <div class="col-md-9">
                                        <span class="help-block"><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_LINKS')?></span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                
                                <div class="footer-block-content">
                                    <div class="col-md-3">&nbsp;</div>
                                    <div class="col-md-9">
                                        <?php foreach ($languages as $list): ?>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                            </span>
                                            <textarea class="redactor" name="footer[blocks][blocks][<?php echo $i?>][content][<?php echo $list['language_id']?>]"><?php if (isset($settings['footer']['blocks']['blocks'][$i]['content'][$list['language_id']])) { echo $settings['footer']['blocks']['blocks'][$i]['content'][$list['language_id']]; } ?></textarea>
                                        </div>
                                        <br />
                                        <?php endforeach?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <hr />
                            </div>
                            <?php endfor?>
                        </div>
                        <div class="tab-pane" id="tab-pages-footer-copyright">
                            <h3><?php echo Language::getVar('SUMO_NOUN_LEFT')?></h3>
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT')?>
                                </label>
                                <div class="col-md-9">
                                    <?php foreach ($languages as $list): ?>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                        </span>
                                        <textarea name="footer[copyright][notice][<?php echo $list['language_id']?>]" class="redactor"><?php if (isset($settings['footer']['copyright']['notice'][$list['language_id']])) { echo $settings['footer']['copyright']['notice'][$list['language_id']]; } else { echo '<p>&copy; Copyright [websitename], 2013-[currentyear]</p>'; } ?></textarea>
                                    </div>
                                    <br />
                                    <?php endforeach?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT_POWERED_BY')?>
                                </label>
                                <div class="col-md-6">
                                    <div class="inline-radio col-md-3">
                                        <input type="radio" name="footer[copyright][powered_by]" value="1" <?php if ((isset($settings['footer']['copyright']['powered_by']) && $settings['footer']['copyright']['powered_by']) || !isset($settings['footer']['copyright']['powered_by'])) { echo 'checked'; } ?>>
                                        <?php echo Language::getVar('YES') ?>
                                    </div>
                                    <div class="inline-radio col-md-3">
                                        <input type="radio" name="footer[copyright][powered_by]" value="list" <?php if (isset($settings['footer']['copyright']['powered_by']) && !$settings['footer']['copyright']['powered_by']) { echo 'checked'; } ?>>
                                        <?php echo Language::getVar('NO') ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12">
                                        <span class="help-block">
                                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_COPYRIGHT_POWERED_BY')?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <h3><?php echo Language::getVar('SUMO_NOUN_RIGHT')?></h3>
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_SHOW_ICONS')?>
                                </label>
                                <div class="col-md-6">
                                    <div class="inline-radio col-md-3">
                                        <input type="radio" name="footer[copyright][icons_enabled]" value="1" <?php if ((isset($settings['footer']['copyright']['icons_enabled']) && $settings['footer']['copyright']['icons_enabled']) || !isset($settings['footer']['copyright']['icons_enabled'])) { echo 'checked'; } ?>>
                                        <?php echo Language::getVar('YES') ?>
                                    </div>
                                    <div class="inline-radio col-md-3">
                                        <input type="radio" name="footer[copyright][icons_enabled]" value="list" <?php if (isset($settings['footer']['copyright']['icons_enabled']) && !$settings['footer']['copyright']['icons_enabled']) { echo 'checked'; } ?>>
                                        <?php echo Language::getVar('NO') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group icons_enabled">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo Language::getVar('SUMO_NOUN_ICON')?></th>
                                            <th><?php echo Language::getVar('SUMO_NOUN_ENABLED')?></th>
                                            <th>URL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($icons as $list): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo $list['src']?>">
                                                <input type="hidden" name="footer[copyright][icons][<?php echo $list['name']?>][image]" value="<?php echo str_replace('../', '', $list['src'])?>">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="footer[copyright][icons][<?php echo $list['name']?>][enabled]" value="1" <?php if (isset($settings['footer']['copyright']['icons'][$list['name']]['enabled']) && $settings['footer']['copyright']['icons'][$list['name']]['enabled']) { echo 'checked'; } ?>>
                                            </td>
                                            <td>
                                                <input type="text" name="footer[copyright][icons][<?php echo $list['name']?>][url]" value="<?php if (isset($settings['footer']['icons']['copyright'][$list['name']]['url'])) { echo $settings['footer']['copyright']['icons'][$list['name']]['url']; } else if (isset($list['url'])) { echo $list['url']; } ?>" <?php if (isset($list['url_noneditable'])) { echo 'disabled'; } ?> class="form-control">
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-pages-footer-text">
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT_BOTTOM')?>
                                </label>
                                <div class="col-md-6">
                                    <div class="inline-radio col-md-3">
                                        <input type="radio" name="footer[copyright][bottom_enabled]" value="1" <?php if ((isset($settings['footer']['copyright']['bottom_enabled']) && $settings['footer']['copyright']['bottom_enabled']) || !isset($settings['footer']['copyright']['bottom_enabled'])) { echo 'checked'; } ?>>
                                        <?php echo Language::getVar('YES') ?>
                                    </div>
                                    <div class="inline-radio col-md-3">
                                        <input type="radio" name="footer[copyright][bottom_enabled]" value="0" <?php if (isset($settings['footer']['copyright']['bottom_enabled']) && !$settings['footer']['copyright']['bottom_enabled']) { echo 'checked'; } ?>>
                                        <?php echo Language::getVar('NO') ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12">
                                        <span class="help-block">
                                            <?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_COPYRIGHT_BOTTOM')?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group bottom-enabled">
                                <label class="col-md-3 control-label">
                                    <?php echo Language::getVar('SUMO_NOUN_CONTENT')?>
                                </label>
                                <div class="col-md-9">
                                    <?php foreach ($languages as $list): ?>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                        </span>
                                        <textarea name="footer[copyright][bottom_text][<?php echo $list['language_id']?>]" class="redactor"><?php if (isset($settings['footer']['copyright']['bottom_text'][$list['language_id']])) { echo $settings['footer']['copyright']['bottom_text'][$list['language_id']]; } else { echo ''; } ?></textarea>
                                    </div>
                                    <br />
                                    <?php endforeach?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" src="js/redactor.js"></script> 
<link href="css/redactor.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="view/javascript/jquery/ajaxupload.js"></script>
<script type="text/javascript">
$(function(){
    $('#tab-pages a').on('shown.bs.tab', function(e) {
        $('#tab-pages a').removeClass('active');
        $('a[href=' + $(e.target).attr('href') + ']').addClass('active');
    })
    $('.loader').slideUp();
    $('#tab-pages a:first, #tab-pages-footer .nav-tabs a:first').tab('show');
    
    $('.fixed').hide();
    
    $('#layout_style').on('change', function() {
        if ($('option:selected', this).val() == 'fixed') {
            if ($('.fixed').is(':visible') == false) {
                $('.fixed').slideDown();
            }
        }
        else {
            if ($('.fixed').is(':visible')) {
                $('.fixed').slideUp();
            }
        }
    }).trigger('change');
    
    $('.column-div input').each(function() {
        $(this).on('click', function() {
            if ($(this).val() != 'empty') {
                var value = $(this).val();
                $('.column-' + value).prop('disabled', 1);
                $(this).prop('disabled', 0);
            }
            if ($('.column-logo').is(':checked') == true) {
                $('.column-logo').prop('disabled', 1);
            }
            else {
                $('.column-logo').prop('disabled', 0);
            }
            if ($('.column-search').is(':checked') == true) {
                $('.column-search').prop('disabled', 1);
            }
            else {
                $('.column-search').prop('disabled', 0);
            }
            if ($('.column-cart').is(':checked') == true) {
                $('.column-cart').prop('disabled', 1);
            }
            else {
                $('.column-cart').prop('disabled', 0);
            }
        });
    });
    setTimeout(function(){
        $('.column-logo, .column-search, .column-cart').trigger('click');
    }, 500);
    
    $('.home_link').hide();
    $('input[name="menu[home_link]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.home_link').is(':visible') == false) {
                    $('.home_link').slideDown();
                }
            }
            else {
                if ($('.home_link').is(':visible')) {
                    $('.home_link').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.category_link, .category_link_horizontal').hide();
    $('input[name="menu[category_link]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.category_link').is(':visible') == false) {
                    $('.category_link').slideDown();
                }
                $('select[name="menu[category_link_style]"]').trigger('change');
            }
            else {
                if ($('.category_link').is(':visible')) {
                    $('.category_link').slideUp();
                }
                if ($('.category_link_horizontal').is(':visible')) {
                    $('.category_link_horizontal').slideUp();
                }
            }
        }
    }).trigger('change');
    $('select[name="menu[category_link_style]"]').on('change', function() {
        if ($('option:selected', this).val() == 'horizontal') {
            if ($('.category_link_horizontal').is(':visible') == false) {
                $('.category_link_horizontal').slideDown();
            }
        }
        else {
            if ($('.category_link_horizontal').is(':visible')) {
                $('.category_link_horizontal').slideUp();
            }
        }
    }).trigger('change');
    
    $('.manufacturer_link').hide();
    $('input[name="menu[manufacturer_link]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.manufacturer_link').is(':visible') == false) {
                    $('.manufacturer_link').slideDown();
                }
            }
            else {
                if ($('.manufacturer_link').is(':visible')) {
                    $('.manufacturer_link').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.information_link').hide();
    $('input[name="menu[information_link]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.information_link').is(':visible') == false) {
                    $('.information_link').slideDown();
                }
            }
            else {
                if ($('.information_link').is(':visible')) {
                    $('.information_link').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.category_wall').hide();
    $('input[name="home[category_wall]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.category_wall').is(':visible') == false) {
                    $('.category_wall').slideDown();
                }
            }
            else {
                if ($('.category_wall').is(':visible')) {
                    $('.category_wall').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.manufacturer_wall').hide();
    $('input[name="home[manufacturer_wall]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.manufacturer_wall').is(':visible') == false) {
                    $('.manufacturer_wall').slideDown();
                }
            }
            else {
                if ($('.manufacturer_wall').is(':visible')) {
                    $('.manufacturer_wall').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.latest_products').hide();
    $('input[name="home[latest_products]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.latest_products').is(':visible') == false) {
                    $('.latest_products').slideDown();
                }
            }
            else {
                if ($('.latest_products').is(':visible')) {
                    $('.latest_products').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.subcategories_enabled').hide();
    $('input[name="category[subcategories_enabled]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.subcategories_enabled').is(':visible') == false) {
                    $('.subcategories_enabled').slideDown();
                }
            }
            else {
                if ($('.subcategories_enabled').is(':visible')) {
                    $('.subcategories_enabled').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('.product_related').hide();
    $('input[name="product[display_related]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.product_related').is(':visible') == false) {
                    $('.product_related').slideDown();
                }
            }
            else {
                if ($('.product_related').is(':visible')) {
                    $('.product_related').slideUp();
                }
            }
        }
    }).trigger('change');
    var firstrun = 0;
    $('.choose-type input[type=radio]').each(function() {
        $(this).on('change click', function() {
            var value = $(this).val();
            var parent = $(this).parent().parent().parent();
            
            parent.find('.footer-block-links').hide();
            parent.find('.footer-block-content').hide();
            parent.find('.footer-block-' + value).show();
            $(this).parent().parent().find('input').each(function(){
                $(this).prop('checked', 0);
            })
            $(this).prop('checked', 1);
        });
    });
    
    $('.footer-block-content').hide();
    $('.footer-block-links').hide();
    var firstrun = true;
    $('select[name="footer[blocks][amount]"]').on('change', function() {
        var until   = parseInt($('option:selected', this).val());
        var current = 0;
        
        $('.footer-block').each(function() {
            if (!current) {
                $(this).hide();
                current++;
            }
            else if (current <= until) {
                if ($(this).is(':visible') == false) {
                    $(this).slideDown();
                }
                $(this).find('.choose-type input[type=radio]:checked').trigger('click');
                current++;
                if (current > until) {
                    $(this).find('hr').hide();
                }
                else {
                    $(this).find('hr').show();
                }
            }
            else {
                if ($(this).is(':visible')) {
                    $(this).slideUp();
                }
                if (firstrun) {
                    firstrun = false;
                    $(this).hide();
                }
            }
        })
    }).trigger('change');
    
    $('.redactor').redactor({
        minHeight: 150
    });
    
    var links = '<option value=""><?php echo Language::getVar('SUMO_FORM_SELECT_CHOOSE')?></option>';
    links += '<option value="/account/account"><?php echo Language::getVar('SUMO_NOUN_ACCOUNT_TITLE')?></option>';
    links += '<option value="/account/order"><?php echo Language::getVar('SUMO_NOUN_ORDER')?></option>';
    links += '<option value="/account/newsletter"><?php echo Language::getVar('SUMO_NOUN_NEWSLETTER'); ?></option>';
    links += '<option value="/account/return/insert"><?php echo Language::getVar('SUMO_NOUN_RETURN')?></option>';
    links += '<option value="/account/voucher"><?php echo Language::getVar('SUMO_ACCOUNT_VOUCHER_TITLE')?></option>';
    links += '<option value="/affiliate/account"><?php echo Language::getVar('SUMO_NOUN_AFFILIATE_TITLE')?></option>';
    links += '<option value="/information/contact"><?php echo Language::getVar('SUMO_NOUN_CONTACT_US')?></option>';
    <?php foreach ($pages as $page): if ($page['status'] == 0) { continue; }?>
    links += '<option value="/information/information/info?information_id=<?php echo $page['information_id']?>"><?php echo $page['title']?></option>';
    <?php endforeach; ?>
    
    function addLinks() {
        $('.link-holder').each(function(){
            $(this).html(links);
            $(this).on('change', function() {
                var value = $('option:selected', this).val();
                if (value != '') {
                    $(this).parent().parent().find('.input-url').val(value);
                }
            });
        });
    }
    addLinks();
    function removeLink() {
        $('.btn-remove-url').on('click', function() {
            $(this).parent().parent().remove();
        })
    }
    removeLink();
    
    $('.btn-add-url').each(function(){
        $(this).on('click', function(){
            var body = $(this).parent().parent().parent().parent().find('tbody');
            var newI = 1;
            $(body).find('tr').each(function(){
                if (newI <= $(this).attr('number')) {
                    newI = $(this).attr('number');
                }
                newI++;
            })
            var rowI = body.parent().attr('row');
            //$settings['footer']['link_blocks']['blocks'][$i]['links']
            body.append('<tr><td><input type="text" name="footer[blocks][blocks][' + rowI + '][links][' + newI + '][url]" class="form-control input-url"></td><td><select class="form-control link-holder"></select></td><td><br /><?php foreach ($languages as $list): ?><div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" /></span><input type="text" name="footer[blocks][blocks][' + rowI + '][links][' + newI + '][name][<?php echo $list['language_id']?>]" value="" class="form-control product-name" language="<?php echo $list['language_id']?>" /></div><br /><?php endforeach; ?></td><td><span class="btn btn-sm btn-danger btn-remove-url"><i class="icon-trash"></i></span></td></tr>');
            addLinks();
            removeLink();
        }).trigger('click');
    });
        
    $('.icons_enabled').hide();
    $('input[name="footer[copyright][icons_enabled]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.icons_enabled').is(':visible') == false) {
                    $('.icons_enabled').slideDown();
                }
            }
            else {
                if ($('.icons_enabled').is(':visible')) {
                    $('.icons_enabled').slideUp();
                }
            }
        }
    }).trigger('change');
    
    $('#slideshowtabs a:first').tab('show');
    $('.slideshow_enabled').hide();
    $('input[name="home[slideshow_enabled]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.slideshow_enabled').is(':visible') == false) {
                    $('.slideshow_enabled').slideDown();
                }
            }
            else {
                if ($('.slideshow_enabled').is(':visible')) {
                    $('.slideshow_enabled').slideUp();
                }
            }
        }
    }).trigger('change');
    $('.slide').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($(this).parent().parent().parent().parent().find('.content-form').is(':visible') == false) {
                    $(this).parent().parent().parent().parent().find('.content-form').slideDown();
                }
            }
            else {
                if ($(this).parent().parent().parent().parent().find('.content-form').is(':visible')) {
                    $(this).parent().parent().parent().parent().find('.content-form').slideUp();
                }
            }
        }
    }).trigger('change');
    
    var uploadI = 0;
    $('.button-upload').each(function(){
        uploadI++;
        var thisID = 'button-upload-' + (uploadI);
        $(this).attr('id', thisID);
        new AjaxUpload('#' + thisID, {
            action: 'common/images/upload?token=<?php echo $this->session->data['token']?>',
            name: 'uploads[]',
            autoSubmit: true,
            data: {
                theme_id: $('#themepicker .themepicker-item.active').attr('theme'),
                slider: uploadI
            },
            responseType: 'json',
            onSubmit: function(file, extension) {
                $('.loader').slideDown();
                $(thisID).attr('disabled', true);
            },
            onComplete: function(file, json) {
                $('.loader').slideUp(); 
                $(thisID).attr('disabled', false);
                $('.upload_message-' + this._settings.data.slider).removeClass('alert alert-success alert-warning');  
                              
                if (json['error']) {
                    //console.log('error found');
                    $('.upload_message-' + this._settings.data.slider).html('<div class="alert alert-warning">' + json['error'] + '</div>');
                }
                else if (json['errors']) {
                    //console.log('errors found');
                    $('.upload_message-' + this._settings.data.slider).html('<div class="alert alert-warning">' + json['errors'][0] + '</div>');
                }
                else if (json['success']) {
                    //console.log('success found for upload-' + this._settings.data.slider);
                    //console.log(json['success'][0]['message']);
                    $('.upload_message-' + this._settings.data.slider).html('<div class="alert alert-success">' + json['success'][0]['message'] + '</div>');
                    
                    $('.upload_file-' + this._settings.data.slider).val(json['success'][0]['location']);
                }
                else {
                    //console.log('erh, mudkipz?');
                    //console.log(json.success[0]['location']);
                }
            }
        })
    })
})
</script>
