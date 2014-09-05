<?php echo $header ?>

<div class="col-md-4 col-md-offset-8 page-head-actions align-right settingstable">
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
            <li><a href="#other-language" data-lang-id="<?php echo $list['language_id']; ?>"><img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php
// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; $current_store_name = $list['name']; } ?>">
        <a href="<?php echo $this->url->link('app/multisafepay', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
            <?php echo $list['name']?>
        </a>
    </li>
    <?php endforeach?>
</ul>
<?php
endif;
?>

<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" action="" class="form" id="appSettings">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_ENABLE')?></label>
                        <div class="control-group">
                            <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                <input id="enabled" value="1" name="settings[enabled]" type="checkbox" <?php if (isset($settings['enabled'])) { echo 'checked'; }?>>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="submit" class="btn btn-primary pull-right" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                </div>
            </div>
            
            <h4><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_ACCOUNT')?></h4>
            <hr />

            <div class="api-notifier alert alert-danger rounded hidden">
                <div class="message"><?php echo Sumo\Language::getVar('APP_ERROR_MULTISAFEPAY_ACCOUNT'); ?></div>
            </div>

            <div class="row settings">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_ACCOUNT') ?>:</label>
                        <input id="account" type="text" name="settings[account]" class="form-control" required value="<?php if (isset($settings['account'])) { echo $settings['account']; } ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_SITE_ID') ?>:</label>
                        <input id="site_id" type="text" name="settings[site_id]" class="form-control" required value="<?php if (isset($settings['site_id'])) { echo $settings['site_id'] . '" data-orig="' . $settings['site_id'] . '"'; } ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_SITE_SECURE_CODE') ?>:</label>
                        <input id="site_secure_code" type="text" name="settings[site_secure_code]" class="form-control" required value="<?php if (isset($settings['site_secure_code'])) { echo $settings['site_secure_code'] . '" data-orig="' . $settings['site_secure_code'] . '"'; } ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_DEPLOYMENT') ?>:</label>
                        <div class="control-group" style="height: 34px;">
                            <div class="switch switch-small" data-on-label="LIVE" data-off-label="TEST">
                                <input name="settings[live]" value="1" type="checkbox" <?php if (isset($settings['live'])) { echo 'checked'; }?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_NOTIFICATION_URL') ?>:</label>
                        <input type="text" class="form-control" readonly value="<?php echo $stores[$current_store]['base_' . $stores[$current_store]['base_default']] . 'apps/multisafepay/return.php'?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE')?>:</label>
                        <select name="settings[tax]" id="tax" class="form-control">
                            <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                            <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                            <option value="<?php echo $rate?>" <?php if (isset($settings['tax']) && $settings['tax'] == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MULTISAFEPAY_NO_ACCOUNT') ?></label>
                        <span class="help-block"><?php echo sprintf(Sumo\Language::getVar('APP_MULTISAFEPAY_NO_ACCOUNT_DESC'), $link_msp); ?></span>
                    </div>
                </div>
            </div>
            <div class="settings api-ok">
                <hr />

                <div id="gateways"></div>
            </div>

            <div class="row settings api-ok">
                <div class="col-md-12">
                    <h4><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS')?></h4>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_COMPLETED')?>:</label>
                        <select name="settings[status][completed]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['completed']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_INITIALIZED')?>:</label>
                        <select name="settings[status][initialized]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['initialized']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_UNCLEARED')?>:</label>
                        <select name="settings[status][uncleared]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['uncleared']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_EXPIRED')?>:</label>
                        <select name="settings[status][expired]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['expired']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_VOID')?>:</label>
                        <select name="settings[status][void]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['void']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_DECLINED')?>:</label>
                        <select name="settings[status][declined]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['declined']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_REFUNDED')?>:</label>
                        <select name="settings[status][refunded]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['refunded']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_STATUS_SHIPPED')?>:</label>
                        <select name="settings[status][shipped]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['shipped']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="store_id" value="<?php echo $current_store; ?>" />
        </form>
    </div>
</div>

<?php echo $footer?>
