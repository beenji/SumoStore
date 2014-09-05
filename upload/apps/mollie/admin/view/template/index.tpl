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
        <a href="<?php echo $this->url->link('app/mollie', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
        <form method="post" action="" class="form">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_ENABLE')?></label>
                        <div class="control-group">
                            <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                <input id="enabled" name="settings[enabled]" type="checkbox" value="1" <?php if (isset($settings['enabled']) && $settings['enabled'] == 1) { echo 'checked'; }?>>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="submit" class="btn btn-primary pull-right" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                </div>
            </div>
            <div class="row settingstable">
                <div class="col-md-12">
                    <h4><?php echo Sumo\Language::getVar('APP_MOLLIE_SETTINGS')?></h4>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_API_KEY')?></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="api-key" name="settings[general][api_key]" value="<?php if (isset($settings['general']['api_key'])) { echo $settings['general']['api_key']; } ?>">
                            <span class="input-group-addon">
                                <i class="fa fa-check" id="api-ok"></i>
                            </span>
                        </div>
                        <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_API_KEY_HELPER')?></span>
                        <div id="api-debug" class="alert alert-white-alt rounded collapsed">
                            <div class="icon"></div>
                            <div class="message"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 settingstable api-ok">
                    <?php if (!isset($settings['discount']) || $settings['discount'] == 0): ?>
                    <div class="form-group set-discount">
                        <label class="control-label">&nbsp;</label>
                        <div class="alert alert-info rounded">
                            <div class="message"><?php echo Sumo\Language::getVar('APP_MOLLIE_SUMO_IDEAL_DISCOUNT_DESCRIPTION')?></div>
                        </div>
                    </div>
                    <input type="hidden" name="discount" id="discount" value="0">
                    <?php endif ?>
                </div>
            </div>

            <div class="row settingstable api-ok">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_BANK_DESCRIPTION')?></label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="settings[instructions][<?php echo $list['language_id']?>]" class="form-control" value="<?php if (isset($settings['instructions'][$list['language_id']])) { echo $settings['instructions'][$list['language_id']]; } ?>" placeholder="<?php echo Sumo\Language::getVar('APP_MOLLIE_API_BANK_DESCRIPTION_PLACEHOLDER', $current_store_name)?>">
                        </div>
                        <?php endforeach; ?>
                        <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_API_BANK_DESCRIPTION_HELPER')?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE')?></label>
                        <select name="settings[tax]" id="tax" class="form-control">
                            <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                            <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                            <option value="<?php echo $rate?>" <?php if (isset($settings['tax']) && $settings['tax'] == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row settingstable api-ok">
                <div class="col-md-12"><hr /></div>
            </div>

            <div class="row settingstable api-ok">
                <div class="col-md-12">
                    <h4><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_OPTIONS')?></h4>
                </div>
                <div class="col-md-12 payment" id="payment-ideal">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>iDeal</label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_IDEAL_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_IDEAL_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[ideal][enabled]" type="checkbox" <?php if (isset($settings['ideal']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="ideal">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_IDEAL_ZONE')?></label>
                                <select name="settings[ideal][zone]" class="form-control tax-class">
                                    <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                                    <?php foreach ($zones as $list): ?>
                                    <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($settings['ideal']['zone']) && $settings['ideal']['zone'] == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                                    <?php endforeach ?>
                                </select>
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_IDEAL_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[ideal][rate]" class="excluding-tax" value="<?php if(isset($settings['ideal']['rate'])) { echo $settings['ideal']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[ideal][rate_type]" value="f" <?php if (isset($settings['ideal']['rate_type']) && $settings['ideal']['rate_type'] == 'f' || !isset($settings['ideal']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[ideal][rate_type]" value="p" <?php if (isset($settings['ideal']['rate_type']) && $settings['ideal']['rate_type'] == 'p') { echo 'checked'; } ?>>%</label></span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--
                    <div class="clearfix"></div>
                    <div class="col-md-9 payment-zone">
                        <div class="col-md-9 col-md-offset-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_IDEAL_LINK_IN_ORDER')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[ideal][link][enabled]" type="checkbox" <?php if (isset($settings['ideal']['link']['enabled'])) { echo 'checked'; }?>>
                                </div>
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_IDEAL_LINK_IN_ORDER_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    -->
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 payment" id="payment-creditcard">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_CREDITCARD')?></label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_CREDITCARD_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_CREDITCARD_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[creditcard][enabled]" type="checkbox" <?php if (isset($settings['creditcard']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="creditcard">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_CREDITCARD_ZONE')?></label>
                                <select name="settings[creditcard][zone]" class="form-control tax-class">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                </select>
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_CREDITCARD_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[creditcard][rate]" class="excluding-tax" value="<?php if(isset($settings['creditcard']['rate'])) { echo $settings['creditcard']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[creditcard][rate_type]" value="f" <?php if (isset($settings['creditcard']['rate_type']) && $settings['creditcard']['rate_type'] == 'f' || !isset($settings['creditcard']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label> <label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[creditcard][rate_type]" value="p" <?php if (isset($settings['creditcard']['rate_type']) && $settings['creditcard']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 payment" id="payment-mistercash">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_MISTERCASH')?></label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_MISTERCASH_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_MISTERCASH_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[mistercash][enabled]" type="checkbox" <?php if (isset($settings['mistercash']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="mistercash">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_MISTERCASH_ZONE')?></label>
                                <select name="settings[mistercash][zone]" class="form-control tax-class">
                                    <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                                    <?php foreach ($zones as $list): ?>
                                    <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($settings['mistercash']['zone']) && $settings['mistercash']['zone'] == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                                    <?php endforeach ?>
                                </select>
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_MISTERCASH_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[mistercash][rate]" class="excluding-tax" value="<?php if(isset($settings['mistercash']['rate'])) { echo $settings['mistercash']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[mistercash][rate_type]" value="f" <?php if (isset($settings['mistercash']['rate_type']) && $settings['mistercash']['rate_type'] == 'f' || !isset($settings['mistercash']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label> <label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[mistercash][rate_type]" value="p" <?php if (isset($settings['mistercash']['rate_type']) && $settings['mistercash']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 payment" id="payment-banktransfer">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_BANKTRANSFER')?></label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_BANKTRANSFER_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_BANKTRANSFER_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[banktransfer][enabled]" type="checkbox" <?php if (isset($settings['banktransfer']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="banktransfer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_BANKTRANSFER_ZONE')?></label>
                                <select name="settings[banktransfer][zone]" class="form-control tax-class">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                </select>
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_BANKTRANSFER_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[banktransfer][rate]" class="excluding-tax" value="<?php if(isset($settings['banktransfer']['rate'])) { echo $settings['banktransfer']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[banktransfer][rate_type]" value="f" <?php if (isset($settings['banktransfer']['rate_type']) && $settings['banktransfer']['rate_type'] == 'f' || !isset($settings['banktransfer']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[banktransfer][rate_type]" value="p" <?php if (isset($settings['banktransfer']['rate_type']) && $settings['banktransfer']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 payment" id="payment-paypal">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL')?></label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[paypal][enabled]" type="checkbox" <?php if (isset($settings['paypal']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="paypal">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ZONE')?></label>
                                <select name="settings[paypal][zone]" class="form-control tax-class">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                </select>
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[paypal][rate]" class="excluding-tax" value="<?php if(isset($settings['paypal']['rate'])) { echo $settings['paypal']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[paypal][rate_type]" value="f" <?php if (isset($settings['paypal']['rate_type']) && $settings['paypal']['rate_type'] == 'f' || !isset($settings['paypal']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[paypal][rate_type]" value="p" <?php if (isset($settings['paypal']['rate_type']) && $settings['paypal']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 payment" id="payment-bitcoin">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>BitCoin</label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_CATALOG_BITCOIN_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[bitcoin][enabled]" type="checkbox" <?php if (isset($settings['bitcoin']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="bitcoin">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ZONE')?></label>
                                <select name="settings[bitcoin][zone]" class="form-control tax-class">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                </select>
                                <span class="help-block"><?php #echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[bitcoin][rate]" class="excluding-tax" value="<?php if(isset($settings['bitcoin']['rate'])) { echo $settings['bitcoin']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[bitcoin][rate_type]" value="f" <?php if (isset($settings['bitcoin']['rate_type']) && $settings['bitcoin']['rate_type'] == 'f' || !isset($settings['bitcoin']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[bitcoin][rate_type]" value="p" <?php if (isset($settings['bitcoin']['rate_type']) && $settings['bitcoin']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 payment" id="payment-sofort">
                    <div class="col-md-6">
                        <div class="col-md-3 logo"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>SoFort</label>
                                <div>
                                    <?php echo Sumo\Language::getVar('APP_MOLLIE_CATALOG_SOFORT_DESCRIPTION')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ENABLED')?></label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[sofort][enabled]" type="checkbox" <?php if (isset($settings['sofort']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="sofort">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ZONE')?></label>
                                <select name="settings[sofort][zone]" class="form-control tax-class">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                </select>
                                <span class="help-block"><?php #echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_PAYPAL_ZONE_HELPER')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 payment-zone">
                        <div class="col-md-9">
                            <div class="hidden">
                                <input type="text" name="settings[sofort][rate]" class="excluding-tax" value="<?php if(isset($settings['sofort']['rate'])) { echo $settings['sofort']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[sofort][rate_type]" value="f" <?php if (isset($settings['sofort']['rate_type']) && $settings['sofort']['rate_type'] == 'f' || !isset($settings['sofort']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[sofort][rate_type]" value="p" <?php if (isset($settings['sofort']['rate_type']) && $settings['sofort']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 payment" id="payment-none">
                    <div class="col-md-9">
                        <div class="alert alert-warning alert-white-alt rounded">
                            <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
                            <div class="message"><?php echo Sumo\Language::getVar('APP_MOLLIE_NO_PAYMENT_OPTIONS_AVAILABLE')?></div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>
            <div class="row settingstable api-ok">
                <div class="col-md-12"><hr /></div>
            </div>
            <div class="row settingstable api-ok">
                <div class="col-md-12">
                    <h4><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS')?></h4>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS_OPEN')?></label>
                        <select name="settings[status][open]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['open']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS_CANCELLED')?></label>
                        <select name="settings[status][cancelled]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['cancelled']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS_EXPIRED')?></label>
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
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS_PAID')?></label>
                        <select name="settings[status][paid]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['paid']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS_PAIDOUT')?></label>
                        <select name="settings[status][paidout]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['paidout']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_STATUS_REFUNDED')?></label>
                        <select name="settings[status][refunded]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']) && $list['order_status_id'] == $settings['status']['refunded']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="hidden" id="discount-form">
    <h3><?php echo Sumo\Language::getVar('APP_MOLLIE_DISCOUNT_FORM_TITLE')?></h3>
    <p class="help-block"><?php echo Sumo\Language::getVar('APP_MOLLIE_DISCOUNT_FORM_HELPER')?></p>
    <div class="form-group">
        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_DISCOUNT_FORM_USERNAME')?></label>
        <input type="text" class="username form-control" placeholder="">
    </div>
    <div class="form-group">
        <label class="control-label"><?php echo Sumo\Language::getVar('APP_MOLLIE_DISCOUNT_FORM_PASSWORD')?></label>
        <input type="password" class="password form-control">
    </div>
    <div class="response"></div>
</div>
<script type="text/javascript">
$(function(){
    $('#api-debug, .api-ok, .payment, .payment-zone, .set-discount').hide();
    <?php if (!isset($settings['discount']) || $settings['discount'] == 0): ?>
    $('.set-discount').slideDown();
    <?php endif ?>
    $('#enabled').on('change click', function() {
        if ($('#enabled').is(':checked')) {
            if ($('.settingstable').is(':visible') == false) {
                $('.settingstable:not(.api-ok, .payment-zone)').slideDown(function(){
                    $('#api-key').trigger('change');
                    $('.enable-payment').each(function() { $(this).trigger('change'); });
                });
            }
            else {
                $('.enable-payment').each(function() { $(this).trigger('change'); });
            }
        }
        else {
            if ($('.settingstable').is(':visible')) {
                $('.settingstable').slideUp();
            }
        }
    }).trigger('change');
    $('.excluding-tax').each(function() {
        if ($(this).val().length) {
            var parent      = $(this).parent().parent();
            var tofind      = parent.find('.including-tax');
            var rate_type   = parent.find('.rate_type:checked');
            var element     = parent.parent().parent().find('.tax-class');
            if (rate_type.val() == 'f') {
                tofind.val(calculateIn($(this).val(), element));
            }
            else {
                tofind.val($(this).val());
            }
        }
    })
    $('.including-tax').on('change keyup', function() {
        if ($(this).val().length) {
            var parent      = $(this).parent().parent().parent();
            var tofind      = parent.find('.excluding-tax');
            var rate_type   = $(this).parent().parent().find('.rate_type:checked');
            var element     = parent.parent().parent().find('.tax-class');

            if (rate_type.val() == 'f') {
                var newvalue= calculateEx($(this).val(), element);
                tofind.val(newvalue);
            }
            else {
                tofind.val($(this).val());
            }
        }
    })
    $('.fee-type').each(function() {
        $(this).on('change', function() {
            var parent = $(this).parent().parent().parent().parent().parent();
            if ($(this).is(':checked')) {
                parent.find('.type-fixed').hide();
                parent.find('.type-procent').show();
            }
            else {
                parent.find('.type-fixed').show();
                parent.find('.type-procent').hide();
            }
        }).trigger('change');
    })
    $('.enable-payment').on('change click', function() {
        if ($(this).is(':checked')) {
            $('#payment-' + $(this).attr('payment')).find('.payment-zone').slideDown();
        }
        else {
            $('#payment-' + $(this).attr('payment')).find('.payment-zone').slideUp();
        }
    }).trigger('change');

    var firstCheck = true;
    var canHazOptions = false;
    var keyup = 0;
    $('#api-key').on('keyup', function() {
        var d           = new Date();
        window.keyup    = d.getTime();
        var t           = this;
        setTimeout(function() {
            var d       = new Date();
            if (d.getTime() - window.keyup >= 500) {
                var key = $(t).val();
                if (key.length >= 5) {
                    $.post('app/mollie/ajax?token=<?php echo $this->session->data['token']?>', {check: 'validate-api', key: key}, function(data) {
                        if (data.response == true) {
                            canHazOptions = true;
                            $('#api-ok').parent().removeClass('alert-warning').addClass('alert-success');
                            $('#api-ok').removeClass('fa-exclamation-triangle').addClass('fa-check');
                            $('.api-ok').each(function() {
                                if ($(this).is(':visible') == false) {
                                    $(this).slideDown();
                                }
                                if (firstCheck) {
                                    $(this).slideDown();
                                    firstCheck = false;
                                }
                            })
                        }
                        else {
                            canHazOptions = true;
                            $('#api-ok').parent().removeClass('alert-success').addClass('alert-warning');
                            $('#api-ok').removeClass('fa-check').addClass('fa-exclamation-triangle');
                            $('.api-ok').each(function() {
                                if ($(this).is(':visible')) {
                                    $(this).slideUp();
                                }
                                if (firstCheck) {
                                    $(this).slideUp();
                                    firstCheck = false;
                                }
                            })
                        }
                        if (data.message && data.message.length) {
                            $('#api-debug').find('.message').html(data.message);
                            if (data.response == true) {
                                $('#api-debug').removeClass('alert-warning').addClass('alert-info');
                                $('#api-debug').find('.icon').html('<i class="fa fa-info-circle"></i>');
                            }
                            else {
                                $('#api-debug').removeClass('alert-info').addClass('alert-warning');
                                $('#api-debug').find('.icon').html('<i class="fa fa-warning"></i>');
                            }
                            if ($('#api-debug').is(':hidden')) {
                                $('#api-debug').slideDown();
                            }
                        }
                        else {
                            if ($('#api-debug').is(':visible')) {
                                $('#api-debug').slideUp();
                            }
                        }

                        if (canHazOptions) {
                            $('.payment').hide();
                            $.post('app/mollie/ajax?token=<?php echo $this->session->data['token']?>', {check: 'payment-options', key: key}, function(data2) {
                                if (data2.options && data2.options.length) {
                                    $.each(data2.options, function() {
                                        $('#payment-' + this.id).show().find('.logo').html('<img src="' + this.image.bigger + '" alt="' + this.description + '">');
                                    })
                                }
                                else {
                                    $('#payment-none').slideDown();
                                }
                            }, 'JSON');
                        }
                    }, 'JSON');
                }
                else {
                    $('#api-ok').removeClass('fa-check').addClass('fa-exclamation-triangle');

                    if (!firstCheck) {
                        $('#api-debug').find('.message').html('<?php echo Sumo\Language::getVar('APP_MOLLIE_ERROR_INVALID_API_KEY')?>');
                        $('#api-debug').removeClass('alert-info').addClass('alert alert-warning');
                        $('#api-debug').find('.icon').html('<i class="fa fa-warning"></i>');
                        if ($('#api-debug').is(':hidden')) {
                            $('#api-debug').slideDown();
                        }
                    }
                }
            }
        }, 501);
    }).trigger('keyup');

    $('.add-zone').on('click', function(e) {
        e.preventDefault();
        var geozone = $(this).parent().parent().find(':input');
        if (!$(geozone, 'option:selected').val()) {
            return;
        }
        var curid   = $(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var element = $('#' + curid + '-' + $(geozone, 'option:selected').val());
        if (element.is(':visible')) {
            return;
        }
        element.find('.including-tax').val($('#general').val()).trigger('change');
        element.removeClass('hidden').show();
    })
    <?php if (!isset($settings['discount']) || $settings['discount'] == 0): ?>
    $('.set-discount').find('.message').css({cursor: 'pointer'});
    $('.set-discount').find('.message').on('click', function(e) {
        bootbox.dialog({
            message: $('#discount-form').html(),
            buttons: {
                ignore: {
                    "label": '<?php echo Sumo\Language::getVar('APP_MOLLIE_BUTTON_IGNORE_DISCOUNT')?>',
                    'className': 'btn-danger',
                    'callback': function(e) {
                        $('.set-discount').hide();
                        $.post('app/mollie/ajax?token=<?php echo $this->session->data['token']?>', {check: 'ignore-discount', store_id: <?php echo $current_store?>});
                    }
                },
                validate: {
                    "label": '<?php echo Sumo\Language::getVar('APP_MOLLIE_BUTTON_VALIDATE')?>',
                    "className": 'btn-primary',
                    "callback": function (e) {
                        e.stopPropagation();
                        $('.set-discount-form-loader').show();
                        $.post('app/mollie/ajax?token=<?php echo $this->session->data['token']?>', {check: 'validate-discount', username: $('.bootbox .username').val(), password: $('.bootbox .password').val(), store_id: <?php echo $current_store?>}, function(data) {
                            if (data.response == true) {
                                $('.bootbox').modal('hide');
                                $('.set-discount').slideUp(function(){
                                    $('.has-discount').slideDown();
                                })
                            }
                            else {
                                console.log(data.text);
                                $('.bootbox .response').html('<div class="alert alert-danger">' + data.text + '</div>');
                            }

                        }, 'json');
                        return false;
                    }
                },
                cancel: {
                    "label": '<?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?>',
                    "className": 'btn-secondary',
                    "callback": function (e) {
                        e.stopPropagation();
                    }
                }
            }
        });
    });
    <?php endif?>
})

function getTaxRate(element) {
    return $('#tax').val();

}
function calculateIn(price, element) {
    return Math.max(parseFloat(price.replace(',', '.')) + (parseFloat(price.replace(',', '.')) * getTaxRate(element) / 100)).toFixed(2);
}
function calculateEx(price, element) {
    return Math.max(parseFloat(price.replace(',', '.')) / (1 + (getTaxRate(element) / 100))).toFixed(4);
}
</script>

<?php echo $footer ?>
