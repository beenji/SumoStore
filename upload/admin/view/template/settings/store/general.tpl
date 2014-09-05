<?php echo $header ?>

<script type="text/javascript">
    var sessionToken    = '<?php echo $this->session->data['token'] ?>',
        textNone        = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION_NONE') ?>',
        textSelect      = '<?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT') ?>',
        textExtra       = '<?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA')?>',
        confirmGroupDeleteMsg   = '<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_GROUP_DELETE')?>',
        customerGroupRow        = '<tr class="customer-group-CGID"><td class="customer_group_name">CGNAME</td><td><a href="<?php echo $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&customer_group_id=', 'SSL')?>CGID" class="not-external link-external">0</a></td><td><div class="btn-group pull-right"><a href="#edit-group" class="btn btn-sm btn-secondary btn-edit-group" group="CGID"><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT')?></a><a href="#remove-group" class="btn btn-sm btn-primary mono4 btn-remove-group" group="CGID"><i class="fa fa-trash-o"></i></a></div></td></tr>',
        storeID         = '<?php echo isset($current_store) ? $current_store : 0?>',
        formType        = '<?php echo $form?>';
</script>

<div class="col-md-4 col-md-offset-8 page-head-actions align-right">
    <div class="btn-group align-left">
        <?php
        foreach ($languages as $list):
            if ($list['is_default']):
        ?>
        <button class="btn btn-primary dropdown-toggle" id="language-selector-btn" data-toggle="dropdown" type="button"><span><img src="view/img/flags/<?php echo $list['image']; ?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></span>&nbsp; <span class="caret"></span></button>
        <?php
                break;
            endif;
        endforeach; ?>
        <ul class="dropdown-menu pull-right" id="language-selector">
            <?php foreach ($languages as $list): ?>
            <li><a href="#other-language" data-lang-id="<?php echo $list['language_id']; ?>" <?php if ($list['is_default']) { echo 'class="lang-default"'; } ?>><img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php if (isset($this->data['warning'])): ?>
    <div class="alert alert-danger"><?php echo $this->data['warning']?></div>
<?php endif?>

<form action="" method="post" data-parsley-validate novalidate>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#general" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL') ?></a></li>
        <?php if ($form == 'store'): ?>
        <li><a href="#shop" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISPLAY')?></a></li>
        <?php endif?>
        <li><a href="#local" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_LANGUAGE_REGION') ?></a></li>
        <li><a href="#taxes" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAXES')?></a></li>
        <li><a href="#customers" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_PLURAL')?></a></li>
        <li><a href="#options" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA') ?></a></li>
        <li><a href="#images" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGES') ?></a></li>
        <li><a href="#cookies" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_SETTINGS') ?></a></li>
        <li><a href="#server" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_SERVER_SETTINGS')?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active cont" id="general">
            <?php if ($form == 'store'): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_URL')?>: *
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon">http://</span>
                            <input type="url" name="base_http" value="<?php echo isset($settings['base_http']) ? str_replace(array('https', 'http', '://'), '', $settings['base_http']) : ''?>" placeholder="" size="40" class="form-control" required>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_URL_HELP')?></span>
                        <?php if (isset($errors['base_http'])): ?>
                        <span class="has-error"><span class="help-block"><?php echo $errors['base_http']?></span></span>
                        <?php endif?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_URL_SSL')?>: *
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon">https://</span>
                            <input type="url" name="base_https" value="<?php echo isset($settings['base_https']) ? str_replace(array('https', 'http', '://'), '', $settings['base_http']) : ''?>" placeholder="" size="40" class="form-control">
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_URL_SSL_HELP')?></span>
                        <?php if (isset($errors['base_https'])): ?>
                        <span class="has-error"><span class="help-block"><?php echo $errors['base_https']?></span></span>
                        <?php endif?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SECURE')?>:
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="base_default" value="https" <?php if (isset($settings['base_default']) && $settings['base_default'] == 'https') { echo 'checked'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="base_default" value="http" <?php if (!isset($settings['base_default']) || isset($settings['base_default']) && $settings['base_default'] == 'http') { echo 'checked'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info"><?php echo Sumo\Language::getVar('SUMO_NOUN_SECURE_HELP')?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_MAINTENANCE_MODE')?>
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="in_maintenance" value="1" <?php if (isset($settings['in_maintenance']) && $settings['in_maintenance'] == '1') { echo 'checked'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="in_maintenance" value="0" <?php if (!isset($settings['in_maintenance']) || isset($settings['in_maintenance']) && $settings['in_maintenance'] == '0') { echo 'checked'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info"><?php echo Sumo\Language::getVar('SUMO_NOUN_MAINTENANCE_MODE_HELP')?></div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_NAME')?>: *
                        </label>
                        <div class="control-group">
                            <input type="text" name="name" value="<?php echo isset($settings['name']) ? $settings['name'] : ''?>" size="40" class="form-control" required>
                            <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_NAME_HELP')?></span>
                            <?php if (isset($errors['name'])): ?>
                            <span class="has-error"><span class="help-block"><?php echo $errors['name']?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_CATEGORY')?>: *
                        </label>
                       <select id="category" class="form-control" name="category" required>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <option value="1" <?php if (isset($settings['category']) && $settings['category'] == 1) { echo 'selected="selected"'; } ?>>Appliances</option>
                            <option value="2" <?php if (isset($settings['category']) && $settings['category'] == 2) { echo 'selected="selected"'; } ?>>Baby Goods/Kids Goods</option>
                            <option value="3" <?php if (isset($settings['category']) && $settings['category'] == 3) { echo 'selected="selected"'; } ?>>Bags/Luggage</option>
                            <option value="4" <?php if (isset($settings['category']) && $settings['category'] == 4) { echo 'selected="selected"'; } ?>>Board Game</option>
                            <option value="5" <?php if (isset($settings['category']) && $settings['category'] == 5) { echo 'selected="selected"'; } ?>>Building Materials</option>
                            <option value="6" <?php if (isset($settings['category']) && $settings['category'] == 6) { echo 'selected="selected"'; } ?>>Camera/Photo</option>
                            <option value="7" <?php if (isset($settings['category']) && $settings['category'] == 7) { echo 'selected="selected"'; } ?>>Cars</option>
                            <option value="8" <?php if (isset($settings['category']) && $settings['category'] == 8) { echo 'selected="selected"'; } ?>>Clothing</option>
                            <option value="9" <?php if (isset($settings['category']) && $settings['category'] == 9) { echo 'selected="selected"'; } ?>>Commercial Equipment</option>
                            <option value="10" <?php if (isset($settings['category']) && $settings['category'] == 10) { echo 'selected="selected"'; } ?>>Computers</option>
                            <option value="11" <?php if (isset($settings['category']) && $settings['category'] == 11) { echo 'selected="selected"'; } ?>>Drugs</option>
                            <option value="12" <?php if (isset($settings['category']) && $settings['category'] == 12) { echo 'selected="selected"'; } ?>>Electronics</option>
                            <option value="13" <?php if (isset($settings['category']) && $settings['category'] == 13) { echo 'selected="selected"'; } ?>>Food/Beverages</option>
                            <option value="14" <?php if (isset($settings['category']) && $settings['category'] == 14) { echo 'selected="selected"'; } ?>>Furniture</option>
                            <option value="15" <?php if (isset($settings['category']) && $settings['category'] == 15) { echo 'selected="selected"'; } ?>>Games/Toys</option>
                            <option value="16" <?php if (isset($settings['category']) && $settings['category'] == 16) { echo 'selected="selected"'; } ?>>Health/Beauty</option>
                            <option value="17" <?php if (isset($settings['category']) && $settings['category'] == 17) { echo 'selected="selected"'; } ?>>Home Decor</option>
                            <option value="18" <?php if (isset($settings['category']) && $settings['category'] == 18) { echo 'selected="selected"'; } ?>>Household Supplies</option>
                            <option value="19" <?php if (isset($settings['category']) && $settings['category'] == 19) { echo 'selected="selected"'; } ?>>Jewelry/Watches</option>
                            <option value="20" <?php if (isset($settings['category']) && $settings['category'] == 20) { echo 'selected="selected"'; } ?>>Kitchen/Cooking</option>
                            <option value="21" <?php if (isset($settings['category']) && $settings['category'] == 21) { echo 'selected="selected"'; } ?>>Office Supplies</option>
                            <option value="22" <?php if (isset($settings['category']) && $settings['category'] == 22) { echo 'selected="selected"'; } ?>>Outdoor Gear/Sporting Goods</option>
                            <option value="23" <?php if (isset($settings['category']) && $settings['category'] == 23) { echo 'selected="selected"'; } ?>>Patio/Garden</option>
                            <option value="24" <?php if (isset($settings['category']) && $settings['category'] == 24) { echo 'selected="selected"'; } ?>>Pet Supplies</option>
                            <option value="25" <?php if (isset($settings['category']) && $settings['category'] == 25) { echo 'selected="selected"'; } ?>>Phone/Tablet</option>
                            <option value="26" <?php if (isset($settings['category']) && $settings['category'] == 26) { echo 'selected="selected"'; } ?>>Product/Service</option>
                            <option value="27" <?php if (isset($settings['category']) && $settings['category'] == 27) { echo 'selected="selected"'; } ?>>Software</option>
                            <option value="28" <?php if (isset($settings['category']) && $settings['category'] == 28) { echo 'selected="selected"'; } ?>>Tools/Equipment</option>
                            <option value="29" <?php if (isset($settings['category']) && $settings['category'] == 29) { echo 'selected="selected"'; } ?>>Video Game</option>
                            <option value="30" <?php if (isset($settings['category']) && $settings['category'] == 30) { echo 'selected="selected"'; } ?>>Vitamins/Supplements</option>
                            <option value="31" <?php if (isset($settings['category']) && $settings['category'] == 31) { echo 'selected="selected"'; } ?>>Website</option>
                            <option value="32" <?php if (isset($settings['category']) && $settings['category'] == 32) { echo 'selected="selected"'; } ?>>Wine/Spirits</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr />
            <?php endif ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_OWNER') ?>: *
                        </label>
                        <div class="control-group">
                            <input type="text" name="owner" value="<?php if (!empty($settings['owner'])) { echo $settings['owner']; } elseif (isset($default['owner'])) { echo $default['owner']; }?>" data-parsley-length="[3,64]" required size="40" class="form-control">
                            <?php if (!empty($errors['owner'])): ?>
                            <span class="has-error"><span class="help-block"><?php echo $errors['owner']?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_ADDRESS') ?>: *
                        </label>
                        <div class="control-group">
                            <textarea name="address" data-parsley-length="[3,256]" required rows="3" class="form-control" style="height: 111px;"><?php if (!empty($settings['address'])) { echo $settings['address']; } elseif (isset($default['address'])) { echo $default['address']; }?></textarea>
                            <?php if (!empty($errors['address'])): ?>
                            <span class="has-error"><span class="help-block"><?php echo $errors['address']?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_COC_NUMBER')?>: *
                        </label>
                        <input type="text" class=" form-control" name="coc_number" value="<?php if (!empty($settings['coc_number'])) { echo $settings['coc_number']; } else if (isset($default['coc_number'])) { echo $default['coc_number']; }?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_VAT_NUMBER')?>: *
                        </label>
                        <input type="text" class=" form-control" name="vat_number" value="<?php if (!empty($settings['vat_number'])) { echo $settings['vat_number']; } else if (isset($default['vat_number'])) { echo $default['vat_number']; }?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL') ?>: *
                        </label>
                        <div class="control-group">
                            <input type="email" name="email" data-parsley-maxlength="96" required  value="<?php if (!empty($settings['email'])) { echo $settings['email']; } elseif (isset($default['email'])) { echo $default['email']; }?>" size="40" class="form-control">
                            <?php if (!empty($errors['email'])): ?>
                            <span class="has-error"><span class="help-block"><?php echo $errors['email']?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_TEL') ?>: *
                        </label>
                        <div class="control-group">
                            <input type="text" name="telephone" data-parsley-length="[3,32]" required value="<?php if (!empty($settings['telephone'])) { echo $settings['telephone']; } elseif (isset($default['telephone'])) { echo $default['telephone']; }?>" size="40" class="form-control">
                            <?php if (!empty($errors['telephone'])): ?>
                            <span class="has-error"><span class="help-block"><?php echo $errors['telephone']?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_FAX') ?>:
                        </label>
                        <div class="">
                            <input type="text" name="fax" value="<?php if (!empty($settings['fax'])) { echo $settings['fax']; } elseif (isset($default['fax'])) { echo $default['fax']; }?>" size="40" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_BIC')?>: *
                        </label>
                        <input type="text" class=" form-control" name="bic" value="<?php if (!empty($settings['bic'])) { echo $settings['bic']; } else if (isset($default['bic'])) { echo $default['bic']; }?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_IBAN')?>: *
                        </label>
                        <input type="text" class=" form-control" name="iban" value="<?php if (!empty($settings['iban'])) { echo $settings['iban']; } else if (isset($default['iban'])) { echo $default['iban']; }?>">
                    </div>
                </div>
            </div>

            <?php if ($form == 'store'): ?>
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label"><?php echo Sumo\Language::getVar('SUMO_GOOGLE_VERIFICATION')?>:</label>
                        <input type="text" name="google_verification" value="<?php !empty($settings['google_verification']) ? $settings['google_verification'] : '' ?>" class="form-control" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label"><?php echo Sumo\Language::getVar('SUMO_GOOGLE_ANALYTICS')?>:</label>
                        <input type="text" name="google_analytics" value="<?php echo !empty($settings['google_analytics']) ? $settings['google_analytics'] : '' ?>" class="form-control" />
                    </div>
                </div>
            </div>
            <?php endif ?>
        </div>

        <?php if ($form == 'store'): ?>
        <div class="tab-pane cont" id="shop">
            <input type="hidden" name="config_meta_description">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_TITLE')?>: *
                        </label>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" name="title" value="<?php echo !empty($settings['title']) ? $settings['title'] : ''?>" size="40" class="form-control" required>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_TITLE_HELP')?></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_TEMPLATE')?>: *
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <select name="template" onchange="" class="form-control" required>
                                    <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                                <?php foreach ($templates as $list) :?>
                                    <option value="<?php echo $list['directory']?>" <?php if (isset($settings['template']) && $settings['template'] == $list['directory']) { echo 'selected'; } ?> preview="<?php echo !empty($list['preview']) ? $list['preview'] : '/image/no_image.jpg'?>"><?php echo $list['name'][$this->config->get('language_id')]?></option>
                                <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div id="template"></div>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_TEMPLATE_HELP')?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_LOGO')?>:
                        </label>
                        <div class="control-group">
                            <div class="fancy-upload">
                                <?php if (isset($settings['logo'])): ?>
                                <img src="../image/<?php echo $settings['logo']; ?>" />
                                <a class="fu-edit" href="#edit" id="upload-logo"><i class="fa fa-wrench"></i></a>
                                <a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>
                                <?php else: ?>
                                <a class="fu-new" href="#upload" id="upload-logo"><i class="fa fa-plus-circle"></i></a>
                                <?php endif; ?>

                                <input type="hidden" name="logo" id="config_logo" value="<?php echo isset($settings['logo']) ? $settings['logo'] : '' ?>" />
                            </div>

                            <hr>
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_LOGO_HELP')?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_ICON')?>:
                        </label>
                        <div class="control-group">
                            <div class="fancy-upload">
                                <?php if (isset($settings['icon'])): ?>
                                <img src="../image/<?php echo $settings['icon']; ?>" alt="" />
                                <a class="fu-edit" href="#edit" id="upload-icon"><i class="fa fa-wrench"></i></a>
                                <a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>
                                <?php else: ?>
                                <a class="fu-new" href="#upload" id="upload-icon"><i class="fa fa-plus-circle"></i></a>
                                <?php endif; ?>

                                <input type="hidden" name="icon" id="config_icon" value="<?php echo isset($settings['icon']) ? $settings['icon'] : '' ?>" />
                            </div>

                            <hr>
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_ICON_HELP')?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_META_DESCRIPTION')?>:
                        </label>
                        <?php foreach($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>">
                            </span>
                            <textarea name="meta_description[<?php echo $list['language_id']?>]" rows="3" class="form-control"><?php echo isset($settings['meta_description'][$list['language_id']]) ? $settings['meta_description'][$list['language_id']] : ''?></textarea>
                        </div>
                        <?php endforeach?>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_META_DESCRIPTION_HELP')?></span>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_META_KEYWORDS')?>:
                        </label>
                        <?php foreach($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>">
                            </span>
                            <textarea name="meta_keywords[<?php echo $list['language_id']?>]" rows="3" class="form-control"><?php echo isset($settings['meta_keywords'][$list['language_id']]) ? $settings['meta_keywords'][$list['language_id']] : ''?></textarea>
                        </div>
                        <?php endforeach?>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_META_KEYWORDS_HELP')?></span>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_PRODUCT_META_TEMPLATE')?>:
                        </label>
                        <?php foreach($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>">
                            </span>
                            <textarea name="meta_template[<?php echo $list['language_id']?>]" rows="3" class="form-control"><?php echo isset($settings['meta_template'][$list['language_id']]) ? $settings['meta_template'][$list['language_id']] : ''?></textarea>
                        </div>
                        <?php endforeach?>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_PRODUCT_META_TEMPLATE_HELP')?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_DISPLAY_LIMIT')?>:
                        </label>
                        <input name="catalog_display_limit" class="form-control" value="<?php if (!empty($settings['catalog_display_limit'])) { echo intval($settings['catalog_display_limit']); }?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_DISPLAY_TYPE')?>:
                        </label>
                        <select name="catalog_display_type" class="form-control">
                            <option value="grid">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_GRID')?>
                            </option>
                            <option value="list" <?php if (isset($settings['catalog_display_type']) && $settings['catalog_display_type'] == 'list') { echo 'selected'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_LIST')?>
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_CATEGORY_SUBCATEGORIES_ENABLED')?>
                        </label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="catalog_display_subcategories" value="1" <?php if (isset($settings['catalog_display_subcategories']) && $settings['catalog_display_subcategories'] || !isset($settings['catalog_display_subcategories'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="catalog_display_subcategories" value="0" <?php if (isset($settings['catalog_display_subcategories']) && !$settings['catalog_display_subcategories']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>

        <div class="tab-pane cont" id="local">
            <div class="row">
                <div class="col-md-6">
                     <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY') ?>:
                        </label>
                        <select name="country_id" class="form-control country_id" required>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']?>" <?php if (isset($settings['country_id']) && $settings['country_id'] == $list['country_id'] || isset($default['country_id']) && $default['country_id'] == $list['country_id']) { echo 'selected'; } ?>>
                                <?php echo $list['name'] ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY_HELP') ?></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE') ?>:
                        </label>
                        <select name="zone_id" class="form-control" required></select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE_HELP') ?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_LANGUAGE') ?>:
                        </label>
                        <select name="language_id" class="form-control" required>
                            <?php foreach ($languages as $list): ?>
                            <option value="<?php echo $list['language_id']?>" <?php if (isset($settings['language_id']) && $list['language_id'] == $settings['language_id'] || isset($default['language_id']) && $default['language_id'] == $list['language_id']) { echo 'selected'; } ?>>
                                <?php echo $list['name']?>
                            </option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_LANGUAGE_HELP') ?></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_CURRENCY') ?>:
                        </label>
                        <select name="currency_id" class="form-control" required>
                            <?php foreach ($currencies as $list): ?>
                            <option value="<?php echo $list['currency_id']?>" <?php if (isset($settings['currency_id']) && $list['currency_id'] == $settings['currency_id'] || isset($default['currency_id']) && $default['currency_id'] == $list['currency_id']) { echo 'selected'; } ?>>
                                <?php echo $list['title']?> - <?php echo $list['symbol_left']?>
                            </option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_CURRENCY_HELP') ?></span>
                    </div>
                </div>
            </div>

            <hr />
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_TIMEZONE')?></label>
                        <select name="date_timezone" class="form-control form-date" required>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php
                            if (isset($settings['date_time'])) {
                                $time = $settings['date_time'];
                            } else if (isset($default['date_time'])) {
                                $time = $default['date_time'];
                            } else {
                                $time = '%T';
                            }
                            $timezones = DateTimeZone::listIdentifiers();
                            foreach ($timezones as $timezone) { ?>
                            <option value="<?php echo $timezone?>" <?php if (isset($settings['date_timezone']) && $settings['date_timezone'] == $timezone || !isset($settings['date_timezone']) && isset($default['date_timezone']) && $default['date_timezone'] == $timezone) { echo 'selected'; }?>>
                                <?php
                                $dt = new DateTime(null, new DateTimeZone($timezone));
                                echo $dt->format("e (P)");
                                ?></option>
                            <?php } ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_TIMEZONE_HELP')?>: <span class="date_preview" type="timezone"></span></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_FORMAT_SHORT')?></label>
                        <select class="form-control form-date" name="date_format_short">
                            <?php
                            if (isset($settings['date_format_short'])) {
                                $date_short = $settings['date_format_short'];
                            }
                            elseif (isset($default['date_format_short'])) {
                                $date_short = $default['date_format_short'];
                            }
                            else {
                                $date_short = '%d-%m-%Y';
                            }
                            ?>
                            <option <?php if ($date_short == '%d-%m-%Y') { echo 'selected'; }?>>%d-%m-%Y</option>
                            <option <?php if ($date_short == '%Y-%m-%d') { echo 'selected'; }?>>%Y-%m-%d</option>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_FORMAT_SHORT_HELP')?>: <span class="date_preview" type="format_short"></span></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_FORMAT_LONG')?></label>
                        <select class="form-control form-date" name="date_format_long">
                            <?php
                            if (isset($settings['date_format_long'])) {
                                $date_long = $settings['date_format_long'];
                            }
                            elseif (isset($default['date_format_long'])) {
                                $date_long = $default['date_format_long'];
                            }
                            else {
                                $date_long = '%A %e %B %Y';
                            }
                            ?>
                            <option <?php if ($date_long == '%A %e %B %Y') { echo 'selected'; }?>>%A %e %B %Y</option>
                            <option <?php if ($date_long == '%A, %B %e %Y') { echo 'selected'; }?>>%A, %B %e %Y</option>
                            <option <?php if ($date_long == '%e %B %Y') { echo 'selected'; }?>>%e %B %Y</option>
                            <option <?php if ($date_long == '%B %e %Y') { echo 'selected'; }?>>%B %e %Y</option>
                            <option <?php if ($date_long == '%d-%m-%Y') { echo 'selected'; }?>>%d-%m-%Y</option>
                            <option <?php if ($date_long == '%Y-%m-%d') { echo 'selected'; }?>>%Y-%m-%d</option>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_FORMAT_LONG_HELP')?>: <span class="date_preview" type="format_long"></span></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_TIME')?></label>
                        <select class="form-control form-date" name="date_time">
                            <option <?php if ($time == '%X') { echo 'selected'; }?>>%X</option>
                            <option <?php if ($time == '%H:%M:%S') { echo 'selected'; }?>>%H:%M:%S</option>
                            <option <?php if ($time == '%r') { echo 'selected'; }?>>%r</option>
                            <option <?php if ($time == '%T') { echo 'selected'; }?>>%T</option>
                            <option <?php if ($time == '%I:%M:%S %P') { echo 'selected'; }?>>%I:%M:%S %P</option>
                            <option <?php if ($time == '%I:%M:%S %p') { echo 'selected'; }?>>%I:%M:%S %p</option>
                            <option <?php if ($time == '%l:%M:%S %P') { echo 'selected'; }?>>%l:%M:%S %P</option>
                            <option <?php if ($time == '%l:%M:%S %p') { echo 'selected'; }?>>%l:%M:%S %p</option>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_FORMAT_TIME_HELP')?>: <span class="date_preview" type="time"></span></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info rounded">
                        <div class="message"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_FORMAT_HELP')?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane cont" id="taxes">
            <div class="row">
                <div class="<?php if ($form == 'general'): ?>col-md-6<?php else: ?>col-md-4<?php endif ?>">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX')?>:
                        </label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="tax_enabled" value="1" <?php if (isset($settings['tax_enabled']) && $settings['tax_enabled'] || !isset($settings['tax_enabled'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="tax_enabled" value="0" <?php if (isset($settings['tax_enabled']) && !$settings['tax_enabled']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_HELP')?></span>
                    </div>
                    <?php if ($form != 'general'):?></div><div class="col-md-4"><?php endif?>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_DISPLAY')?>:
                        </label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="tax_display" value="1" <?php if (isset($settings['tax_display']) && $settings['tax_display'] || !isset($settings['tax_display'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="tax_display" value="0" <?php if (isset($settings['tax_display']) && !$settings['tax_display']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_DISPLAY_HELP')?></span>
                    </div>
                    <?php if ($form != 'general'):?></div><div class="col-md-4"><?php endif?>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_PRFIX') ?>:
                        </label>
                        <input type="text" <?php if ($form == 'general') { echo 'name="invoice_prefix"'; } else { echo 'readonly'; } ?> class="form-control" value="<?php if (isset($settings['invoice_prefix'])) { echo $settings['invoice_prefix']; } ?>">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_PRFIX_HELP') ?></span>
                    </div>
                </div>
                <?php if ($form == 'general'): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE')?>:
                        </label>

                            <table class="table table-subtle list">
                                <tbody class="no-border-y items" id="tax-table">
                                    <tr>
                                        <td><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DEFAULT')?>:</strong></td>
                                        <td>
                                            <select name="tax_percentage[default]" class="form-control" data-parsley-ui-enabled="false">
                                                <?php for ($i = 0; $i <= 25; $i++): ?>
                                                <option value="<?php echo $i?>" <?php if (isset($settings['tax_percentage']['default']) && $settings['tax_percentage']['default'] == $i || isset($default['tax_percentage']['default']) && $default['tax_percentage']['default'] == $i) { echo 'selected'; } ?>><?php echo $i?>%</option>
                                                <?php endfor?>
                                            </select>
                                        </td>
                                        <td style="width: 50px;"><a href="#add-tax" class="btn btn-sm btn-success btn-add-tax"><i class="fa fa-plus"></i></a></td>
                                    </tr>
                                    <?php if (isset($settings['tax_percentage']['extra'])):
                                    $procents = isset($settings['tax_percentage']['extra']) ? $settings['tax_percentage']['extra'] : $default['tax_percentage']['extra'];
                                    foreach ($procents as $procent): ?>
                                    <tr>
                                        <td><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA')?>:</strong></td>
                                        <td>
                                            <select name="tax_percentage[extra][]" class="form-control" data-parsley-ui-enabled="false">
                                                <?php for ($i = 0; $i <= 25; $i++): ?>
                                                <option value="<?php echo $i?>" <?php if ($i == $procent) { echo 'selected'; } ?>><?php echo $i?>%</option>
                                                <?php endfor?>
                                            </select>
                                        </td>
                                        <td style="width: 50px;"><a href="#delete-tax" class="btn btn-sm btn-danger btn-remove-tax"><i class="fa fa-trash-o"></i></a></td>
                                    </tr>
                                    <?php endforeach; endif?>
                                </tbody>
                            </table>

                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE_HELP')?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="clearfix"></div>
                <div class="col-md-12"><a href="<?php echo $this->url->link('settings/store/general')?>"><?php echo Sumo\Language::getVar('SUMO_ADMIN_CATALOG_TAX_PERCENTAGE_GENERAL_SETTINGS')?></a></div>
                <?php endif ?>
            </div>
        </div>

        <div class="tab-pane cont" id="customers">
            <?php if ($form == 'general'): ?>
            <div class="row">
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_PLURAL')?></h4>
                    <table class="table no-border list">
                        <thead class="no-border">
                            <tr>
                                <th style="width: 40%;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_NAME')?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMERS')?></strong></th>
                                <th>
                                    <a href="#add-group" class="pull-right" id="add-group" style="font-size: 14px;">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="no-border-y items" id="customer_groups_table">
                            <?php foreach ($customer_groups as $list): ?>
                            <tr class="customer-group-<?php echo $list['customer_group_id']?>">
                                <td><?php echo $list['name']?></td>
                                <td><a href="<?php echo $list['url']?>" class="not-external link-external"><?php echo $list['amount']?></a></td>
                                <td>
                                    <div class="btn-group pull-right">
                                        <a href="#edit-group" class="btn btn-sm btn-secondary btn-edit-group" group="<?php echo $list['customer_group_id']?>">
                                            <?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT')?>
                                        </a>
                                        <a href="#remove-group" class="btn btn-sm btn-primary mono4 btn-remove-group" group="<?php echo $list['customer_group_id']?>">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div id="customer_group_editor">
                        <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP')?> <span class="group-edit"><?php echo strtolower(Sumo\Language::getVar('SUMO_NOUN_EDIT'))?></span><span class="group-add"><?php echo strtolower(Sumo\Language::getVar('SUMO_NOUN_ADD'))?></span></h4>
                        <div class="hidden">
                            <input type="text" name="group[customer_group_id]" id="customer_group_id">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_NAME')?>:</label>
                            <?php foreach ($languages as $list): ?>
                            <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                </span>
                                <input type="text" name="group[name][<?php echo $list['language_id']?>]" class="form-control">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_DESCRIPTION')?>:</label>
                            <?php foreach ($languages as $list): ?>
                            <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                </span>
                                <textarea name="group[description][<?php echo $list['language_id']?>]" class="form-control"></textarea>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_OPTION_CONFIRM')?></label>
                            <div class="control-group">
                                <div class="radio-inline">
                                    <input type="radio" name="group[approval]" value="1">
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                                </div>
                                <div class="radio-inline">
                                    <input type="radio" name="group[approval]" value="0" checked>
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_OPTION_COMPANY_ID')?></label>
                            <div class="control-group">
                                <div class="radio-inline">
                                    <input type="radio" name="group[company_id_required]" value="1">
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                                </div>
                                <div class="radio-inline">
                                    <input type="radio" name="group[company_id_required]" value="0" checked>
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_OPTION_TAX_ID')?></label>
                            <div class="control-group">
                                <div class="radio-inline">
                                    <input type="radio" name="group[tax_id_required]" value="1">
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                                </div>
                                <div class="radio-inline">
                                    <input type="radio" name="group[tax_id_required]" value="0" checked>
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                                </div>
                            </div>
                        </div>
                        <p class="align-right">
                            <a href="#group-edit-cancel" class="btn btn-secondary btn-cancel"><?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?></a>
                            <a href="#group-edit-save" class="btn btn-primary btn-save"><?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?></a>
                        </p>
                    </div>
                </div>
            </div>

            <hr />
            <?php endif ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT_POLICY')?>:
                        </label>
                        <div class="control-group">
                            <select name="customer_policy_id" class="form-control">
                                <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION_NONE')?></option>
                                <?php foreach ($informations as $list): ?>
                                <option value="<?php echo $list['information_id']?>" <?php if (isset($settings['customer_policy_id']) && $list['information_id'] == $settings['customer_policy_id']) { echo 'selected'; } ?>><?php echo $list['title']?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT_POLICY_HELP')?></spam>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_DISPLAY')?>:
                        </label>
                        <div class="control-group">
                            <div id="customer_group_list">
                                <?php foreach ($customer_groups as $list): ?>
                                <label class="checkbox customer-group-<?php echo $list['customer_group_id']?>">
                                    <input type="checkbox" name="customer_group_display[]" value="<?php echo $list['customer_group_id']?>" <?php if (isset($settings['customer_group_display']) && in_array($list['customer_group_id'], $settings['customer_group_display'])) { echo 'checked="checked"'; } ?>>
                                    <?php echo $list['name']?>
                                </label>
                                <?php endforeach?>
                            </div>

                            <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_DISPLAY_HELP')?></span>
                            <?php if (isset($errors['customer_group_display'])): ?>
                            <span class="has-error"><span class="help-block"><?php echo $errors['customer_group_display']?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_DEFAULT_CUSTOMER_GROUP')?>:
                        </label>
                        <select name="customer_group_id" class="form-control">
                            <?php foreach ($customer_groups as $list): ?>
                            <option class="customer-group-<?php echo $list['customer_group_id']?>" value="<?php echo $list['customer_group_id']?>" <?php if (isset($settings['customer_group_id']) && $settings['customer_group_id'] == $list['customer_group_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><a href="<?php echo $this->url->link('settings/store/general')?>"><?php echo Sumo\Language::getVar('SUMO_ADMIN_CUSTOMER_GROUP_GENERAL_SETTINGS')?></a></span>
                    </div>
                </div>
            </div>

            <hr />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_PRICE')?>:
                        </label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="customer_display_price" value="1" <?php if (isset($settings['customer_display_price']) && $settings['customer_display_price']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="customer_display_price" value="0" <?php if (!isset($settings['customer_display_price']) || !$settings['customer_display_price']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_PRICE_HELP')?></span>
                    </div>
                </div>
                <?php /* This isn't even allowed officially, so if you really want to do this, you'll need to edit this
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_GUEST_CHECKOUT')?>
                        </label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="guest_checkout" value="1" <?php if (isset($settings['guest_checkout']) && $settings['guest_checkout']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="guest_checkout" value="0" <?php if (!isset($settings['guest_checkout']) || !$settings['guest_checkout']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_GUEST_CHECKOUT_HELP')?></span>
                    </div>
                </div>
                */?>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_AGE_CHECKOUT')?>
                        </label>
                        <select name="age_checkout" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_AGE_NONE')?></option>
                        <?php for ($i = 13; $i <= 21; $i++): ?>
                            <option value="<?php echo $i?>" <?php if (isset($settings['age_checkout']) && $settings['age_checkout'] == $i || !isset($settings['age_checkout']) && isset($default['age_checkout']) && $default['age_checkout'] == $i) { echo 'selected'; }?>><?php echo $i?> <?php echo Sumo\Language::getVar('SUMO_NOUN_YEAR')?></option>
                        <?php endfor?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_AGE_CHECKOUT_HELP')?></span>
                    </div>
                </div>
            </div>
            <?php if ($form == 'store'):
            $points = 0.0000;
            if (isset($settings['points_value'])) {
                $points = number_format(str_replace(',', '.', $settings['points_value']), 4);
            }
            elseif (isset($default['points_value'])) {
                $points = number_format(str_replace(',', '.', $default['points_value']), 4);
            }
            ?>
            <hr />
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD_VALUE')?>:</label>
                        <input type="text" name="points_value" value="<?php echo $points?>" class="form-control" id="points_convert">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_REWARD_VALUE_HELPER')?></span>
                    </div>
                </div>
            </div>
            <?php endif?>

        </div>

        <div class="tab-pane" id="options">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_STATUS')?>:
                        </label>
                        <select name="order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['order_status_id']) && $settings['order_status_id'] == $list['order_status_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_STATUS_HELP')?></span>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_STATUS')?>:
                        </label>
                        <select name="order_status_id" class="form-control">
                            <?php foreach ($return_statuses as $list): ?>
                            <option value="<?php echo $list['return_status_id']?>" <?php if (isset($settings['return_status_id']) && $settings['return_status_id'] == $list['return_status_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_STATUS_HELP')?></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CHECKOUT_POLICY')?>:
                        </label>
                        <select name="checkout_policy_id" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION_NONE')?></option>
                            <?php foreach ($informations as $list): ?>
                                <option value="<?php echo $list['information_id']?>" <?php if (isset($settings['checkout_policy_id']) && $list['information_id'] == $settings['checkout_policy_id']) { echo 'selected'; } ?>><?php echo $list['title']?></option>
                                <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CHECKOUT_POLICY_HELP')?></span>
                    </div>
                    <div class="form-group">
                        <label class="ontrol-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CART_WEIGHT')?>:
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="display_cart_weight" value="1" <?php if (isset($settings['display_cart_weight']) && $settings['display_cart_weight']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="display_cart_weight" value="0" <?php if (isset($settings['display_cart_weight']) && !$settings['display_cart_weight'] || !isset($settings['display_cart_weight'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CART_WEIGHT_HELP')?></span>
                    </div>
                </div>
            </div>

            <hr />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_STATUS')?></label>
                        <select name="stock_status_id" class="form-control">
                            <?php foreach ($stock_statuses as $list): ?>
                            <option value="<?php echo $list['stock_status_id']?>" <?php if (isset($settings['stock_status_id']) && $settings['stock_status_id'] == $list['stock_status_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_STATUS_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_DISPLAY')?>:
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="display_stock" value="1" <?php if (isset($settings['display_stock']) && $settings['display_stock']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="display_stock" value="0" <?php if (isset($settings['display_stock']) && !$settings['display_stock'] || !isset($settings['display_stock'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_DISPLAY_HELP')?></span>
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_DISPLAY_NO_STOCK')?>:
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="display_stock_empty" value="1" <?php if (isset($settings['display_stock_empty']) && $settings['display_stock_empty']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="display_stock_empty" value="0" <?php if (isset($settings['display_stock_empty']) && !$settings['display_stock_empty'] || !isset($settings['display_stock_empty'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_DISPLAY_NO_STOCK_HELP')?></span>
                    </div>

                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_CHECKOUT')?>:
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="checkout_stock_empty" value="1" <?php if (isset($settings['checkout_stock_empty']) && $settings['checkout_stock_empty']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="checkout_stock_empty" value="0" <?php if (isset($settings['checkout_stock_empty']) && !$settings['checkout_stock_empty'] || !isset($settings['checkout_stock_empty'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_CHECKOUT_HELP')?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane cont" id="images">
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGE_SETTINGS_INTRO'); ?></p>
            <div class="row">
                <?php
                $image_type = array('image_category', 'image_thumb', 'image_popup', 'image_product', 'image_additional', 'image_related', 'image_compare', 'image_wishlist', 'image_cart');
                foreach ($image_type as $type):
                    if (isset($settings[$type . '_width'])) {
                        $width = $settings[$type . '_width'];
                    }
                    else if (isset($default[$type . '_width'])) {
                        $width = $default[$type .'_width'];
                    }
                    else {
                        $width = '';
                    }
                    if (isset($settings[$type . '_height'])) {
                        $height = $settings[$type . '_height'];
                    }
                    else if (isset($default[$type . '_height'])) {
                        $height = $default[$type .'_height'];
                    }
                    else {
                        $height = '';
                    }
                    ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_' . mb_strtoupper($type))?>:</label>
                        <div class="control-group">
                            <div class="row" style="margin-top: 0;">
                                <div class="col-md-5" style="padding-right: 0;">
                                    <input type="text" name="<?php echo $type?>_width" value="<?php echo $width?>" class="form-control align-center" <?php if (!isset($current_store)) { echo 'required'; }?>>
                                </div>
                                <div class="col-md-2 align-center" style="line-height: 34px;">
                                    x
                                </div>
                                <div class="col-md-5" style="padding-left: 0;">
                                    <input type="text" name="<?php echo $type?>_height" value="<?php echo $height?>" class="form-control align-center" <?php if (!isset($current_store)) { echo 'required'; }?>>
                                </div>
                            </div>
                            <?php if (isset(${'error_' . $type})): ?>
                            <span class="has-error"><span class="help-block"><?php echo ${'error_' . $type}?></span></span>
                            <?php endif?>
                        </div>
                    </div>
                </div>
                <?php endforeach?>
            </div>
        </div>

        <div class="tab-pane cont" id="cookies">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_LOCATION')?>:
                        </label>
                        <select name="cookie_location" class="form-control">
                            <option value="hidden"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE'))?></option>
                            <option value="corner" <?php if (isset($settings['cookie_location']) && $settings['cookie_location'] == 'corner' || !isset($settings['cookie_location']) && isset($default['cookie_location']) && $default['cookie_location'] == 'corner') { echo 'selected'; } ?>><?php echo Sumo\Language::getVar('SUMO_NOUN_CORNER')?></option>
                            <option value="top" <?php if (isset($settings['cookie_location']) && $settings['cookie_location'] == 'top' || !isset($settings['cookie_location']) && isset($default['cookie_location']) && $default['cookie_location'] == 'top') { echo 'selected'; } ?>><?php echo Sumo\Language::getVar('SUMO_NOUN_TOP')?></option>
                            <option value="bottom" <?php if (isset($settings['cookie_location']) && $settings['cookie_location'] == 'bottom' || !isset($settings['cookie_location']) && isset($default['cookie_location']) && $default['cookie_location'] == 'bottom') { echo 'selected'; } ?>><?php echo Sumo\Language::getVar('SUMO_NOUN_BOTTOM')?></option>
                        </select>
                    </div>

                    <?php foreach ($languages as $list): ?>
                    <div class="lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_PAGE')?>:
                            </label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>">
                                </div>
                                <select name="cookie_page[<?php echo $list['language_id']?>]" class="form-control">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION_NONE')?></option>
                                    <?php foreach ($informations as $information): ?>
                                        <option value="<?php echo $information['information_id']; ?>" <?php if (isset($settings['cookie_page'][$list['language_id']]) && $settings['cookie_page'][$list['language_id']] == $information['information_id'] || !isset($settings['cookie_page'][$list['language_id']]) && isset($default['cookie_page'][$list['language_id']]) && $default['cookie_page'][$list['language_id']] == $information['information_id']) { echo 'selected'; } ?>><?php echo $information['title']; ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_TEXT')?>:
                            </label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>">
                                </div>
                                <textarea rows="5" class="form-control" name="cookie_text[<?php echo $list['language_id']?>]"><?php if (isset($settings['cookie_text'][$list['language_id']])) { echo $settings['cookie_text'][$list['language_id']]; } elseif (isset($default['cookie_text'][$list['language_id']])) { echo $default['cookie_text'][$list['language_id']]; } ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endforeach?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_BACKGROUND_COLOR')?></label>
                        <input type="color" class="form-control" name="cookie_colors[background]" value="<?php if (isset($settings['cookie_colors']['background'])) { echo $settings['cookie_colors']['background']; } elseif (isset($default['cookie_colors']['background'])) { echo $default['cookie_colors']['background']; } ?>">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_BACKGROUND_COLOR_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_TEXT_COLOR')?></label>
                        <input type="color" class="form-control" name="cookie_colors[text]" value="<?php if (isset($settings['cookie_colors']['text'])) { echo $settings['cookie_colors']['text']; } elseif (isset($default['cookie_colors']['text'])) { echo $default['cookie_colors']['text']; }?>">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_TEXT_COLOR_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_BUTTON_ACCEPT_COLOR')?></label>
                        <input type="color" class="form-control" name="cookie_colors[button_accept]" value="<?php if (isset($settings['cookie_colors']['button_accept'])) { echo $settings['cookie_colors']['button_accept']; } elseif (isset($default['cookie_colors']['button_accept'])) { echo $default['cookie_colors']['button_accept']; }?>">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_BUTTON_ACCEPT_COLOR_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_BUTTON_INFO_COLOR')?></label>
                        <input type="color" class="form-control" name="cookie_colors[button_info]" value="<?php if (isset($settings['cookie_colors']['button_info'])) { echo $settings['cookie_colors']['button_info']; } elseif (isset($default['cookie_colors']['button_info'])) { echo $default['cookie_colors']['button_info']; }?>">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_COOKIE_BUTTON_INFO_COLOR_HELP')?></span>
                    </div>
                </div>
            </div>
        </div>

        <div id="server" class="tab-pane">
            <?php if ($form == 'general'): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADMIN_DIRECTORY')?>:</label>
                        <input type="text" name="admin_directory" class="form-control" value="<?php if (!empty($settings['admin_directory'])) { echo $settings['admin_directory']; } else { echo 'admin'; }?>" placeholder="admin<?php echo rand(1, 100)?>">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADMIN_DIRECTORY_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_ADMINISTRATOR_RESET_PASSWORD')?>:
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="admin_reset_password" value="1" <?php if (isset($settings['admin_reset_password']) && $settings['admin_reset_password']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="admin_reset_password" value="0" <?php if (isset($settings['admin_reset_password']) && !$settings['admin_reset_password'] || !isset($settings['admin_reset_password'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADMINISTRATOR_RESET_PASSWORD_HELP')?></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_ENCRYPTION_KEY')?>:</label>
                        <input type="text" name="encryption_key" value="<?php if (!empty($settings['encryption_key'])) { echo $settings['encryption_key']; } else { echo md5($_SERVER['REMOTE_ADDR'] . $_SERVER['SERVER_ADDR'] . microtime(true)); } ?>" required class="form-control">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_ENCRYPTION_KEY_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_POSTCODE_API')?>:</label>
                        <input type="text" name="pc_api_key" value="<?php if (!empty($settings['pc_api_key'])) { echo $settings['pc_api_key']; } ?>" class="form-control">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_POSTCODE_API_HELP')?></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_LICENSE_KEY')?>:</label>
                        <input type="text" name="license_key" value="<?php if (!empty($settings['license_key'])) { echo $settings['license_key']; } else { echo ''; } ?>" class="form-control">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_LICENSE_KEY_HELP')?></span>
                    </div>
                </div>
            </div>

            <hr />
            <?php endif ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_OWNER_NOTIFY_EMAIL')?>
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="admin_notify_email" value="1" <?php if (isset($settings['admin_notify_email']) && $settings['admin_notify_email']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="admin_notify_email" value="0" <?php if (isset($settings['admin_notify_email']) && !$settings['admin_notify_email'] || !isset($settings['admin_notify_email'])) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_OWNER_NOTIFY_EMAIL_HELP')?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NOTIFY_EMAIL')?>
                        </label>
                        <div class="control-group">
                            <div class="radio-inline">
                                <input type="radio" name="customer_notify_email" value="1" <?php if (isset($settings['customer_notify_email']) && $settings['customer_notify_email'] || isset($default['customer_notify_email']) && $default['customer_notify_email']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="customer_notify_email" value="0" <?php if (isset($settings['customer_notify_email']) && !$settings['customer_notify_email'] || isset($default['customer_notify_email']) && !$default['customer_notify_email']) { echo 'checked="checked"'; } ?>>
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </div>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NOTIFY_EMAIL_HELP')?>:</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA_NOTIFY_EMAIL')?>:</label>
                        <textarea name="extra_notify_email" class="form-control"><?php if (isset($settings['extra_notify_email'])) { echo $settings['extra_notify_email']; } elseif (isset($default['extra_notify_email'])) { echo $default['extra_notify_email']; } ?></textarea>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA_NOTIFY_EMAIL_HELP')?></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_EMAIL_PROTOCOL')?>:</label>
                        <select name="email_protocol" class="form-control">
                            <option value="mail" <?php if (isset($settings['email_protocol']) && $settings['email_protocol'] == 'mail') { echo 'selected'; } ?>>Mail</option>
                            <option value="smtp" <?php if (isset($settings['email_protocol']) && $settings['email_protocol'] == 'smtp' || !isset($settings['email_protocol']) && isset($default['email_protocol']) && $default['email_protocol'] == 'smtp') { echo 'selected'; } ?>>SMTP</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="email_protocol email_protocol_mail">
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_EMAIL_MAIL_PARAMETERS')?>:</label>
                            <input type="text" name="mail[parameters]" value="<?php if (isset($settings['mail']['parameters'])) { echo $settings['mail']['parameters']; } elseif (isset($default['mail']['parameters'])) { echo $default['mail']['parameters']; } ?>" class="form-control">
                            <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_EMAIL_MAIL_PARAMETERS_HELP')?></span>
                        </div>
                    </div>
                    <div class="email_protocol email_protocol_smtp">
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_EMAIL_SMTP_HOSTNAME')?>:</label>
                            <div class="input-group">
                                <input type="text" name="smtp[hostname]" value="<?php if (!empty($settings['smtp']['hostname'])) { echo $settings['smtp']['hostname']; } else { echo 'localhost'; }?>" class="form-control" data-parsley-ui-enabled="false">
                                <span class="input-group-addon">
                                    <input type="text" name="smtp[port]" value="<?php if (!empty($settings['smtp']['port'])) { echo $settings['smtp']['port']; } else { echo 25; } ?>" class="form-control" style="padding: 0; margin: 0; height: 20px;width: 40px;" data-parsley-ui-enabled="false">
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_EMAIL_SMTP_USERNAME')?>:</label>
                            <input type="text" name="smtp[username]" value="<?php if (isset($settings['smtp']['username'])) { echo $settings['smtp']['username']; } ?>" class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SYSTEM_EMAIL_SMTP_PASSWORD')?>:</label>
                            <div class="input-group">
                                <input type="password" autocomplete="off" name="smtp[password]" value="<?php if (isset($settings['smtp']['password'])) { echo $settings['smtp']['password']; } ?>" class="form-control" data-parsley-ui-enabled="false">
                                <span class="input-group-addon">
                                    <a href="#view-password" id="view-password">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="align-right">
        <a class="btn btn-secondary" href="<?php echo $this->url->link('settings/dashboard', '', 'SSL') ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL') ?></a>
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE') ?>" />
    </p>
</form>

<?php echo $footer ?>
