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
        <a href="<?php echo $this->url->link('app/sisow', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_ENABLE')?></label>
                        <div class="control-group">
                            <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                <input id="enabled" name="settings[enabled]" type="checkbox" <?php if (isset($settings['enabled'])) { echo 'checked'; }?>>
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
                    <h4><?php echo Sumo\Language::getVar('APP_SISOW_SETTINGS')?></h4>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_MERCHANT_ID')?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control api-required" name="settings[merchant][id]" value="<?php if (isset($settings['merchant']['id'])) { echo $settings['merchant']['id']; } ?>">
                                    <span class="input-group-addon">
                                        <i class="fa fa-check api-ok-icon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_MERCHANT_KEY')?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control api-required" name="settings[merchant][key]" value="<?php if (isset($settings['merchant']['key'])) { echo $settings['merchant']['key']; } ?>">
                                    <span class="input-group-addon">
                                        <i class="fa fa-check api-ok-icon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_API_HELPER')?></span>
                                <div id="api-debug" class="alert alert-white-alt rounded">
                                    <div class="icon"></div>
                                    <div class="message"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row settingstable api-ok">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_BANK_DESCRIPTION')?></label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="settings[purchaseid][<?php echo $list['language_id']?>]" class="form-control" value="<?php if (isset($settings['purchaseid'][$list['language_id']])) { echo $settings['purchaseid'][$list['language_id']]; } ?>" placeholder="<?php echo Sumo\Language::getVar('APP_SISOW_BANK_DESCRIPTION_PLACEHOLDER', $current_store_name)?>">
                        </div>
                        <?php endforeach; ?>
                        <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_BANK_DESCRIPTION_HELPER')?></span>
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

            <div class="settingstable api-ok">
                <h4><?php echo Sumo\Language::getVar('APP_MOLLIE_PAYMENT_OPTIONS')?></h4>
                <div class="payment" id="payment-ideal">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-3"><img src="/apps/sisow/paylogos/ideal.png" /></div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>iDeal</label>
                                    <div>
                                        <?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_IDEAL_DESCRIPTION')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[ideal][enabled]" type="checkbox" <?php if (isset($settings['ideal']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="ideal">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 payment-zone">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?>:</label>
                                    <select name="settings[ideal][zone]" class="form-control tax-class">
                                        <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                                        <?php foreach ($zones as $list): ?>
                                        <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($settings['ideal']['zone']) && $settings['ideal']['zone'] == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_IDEAL_ZONE_HELPER')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 payment-zone">
                            <div class="hidden">
                                <input type="text" name="settings[ideal][rate]" class="excluding-tax" value="<?php if(isset($settings['ideal']['rate'])) { echo $settings['ideal']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_TRANSACTION_FEE')?>:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[ideal][rate_type]" value="f" <?php if (isset($settings['ideal']['rate_type']) && $settings['ideal']['rate_type'] == 'f' || !isset($settings['ideal']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[ideal][rate_type]" value="p" <?php if (isset($settings['ideal']['rate_type']) && $settings['ideal']['rate_type'] == 'p') { echo 'checked'; } ?>>%</label></span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="clearfix"></div>

                <div class="payment" id="payment-ecare">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-3 logo"><img src="/apps/sisow/paylogos/logo_ecare.gif"></div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_ECARE')?></label>
                                    <div>
                                        <?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_ECARE_DESCRIPTION')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[ecare][enabled]" type="checkbox" <?php if (isset($settings['ecare']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="ecare">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 payment-zone">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?>:</label>
                                    <select name="settings[ecare][zone]" class="form-control tax-class">
                                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                    </select>
                                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_ECARE_ZONE_HELPER')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 payment-zone">
                            <div class="hidden">
                                <input type="text" name="settings[ecare][rate]" class="excluding-tax" value="<?php if(isset($settings['ecare']['rate'])) { echo $settings['ecare']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[ecare][rate_type]" value="f" <?php if (isset($settings['ecare']['rate_type']) && $settings['ecare']['rate_type'] == 'f' || !isset($settings['ecare']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label> <label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[ecare][rate_type]" value="p" <?php if (isset($settings['ecare']['rate_type']) && $settings['ecare']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="clearfix"></div>

                <div class="payment" id="payment-mistercash">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-3 logo"><img src="/apps/sisow/paylogos/mistercash.png"></div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_MISTERCASH')?></label>
                                    <div>
                                        <?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_MISTERCASH_DESCRIPTION')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[mistercash][enabled]" type="checkbox" <?php if (isset($settings['mistercash']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="mistercash">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 payment-zone">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?>:</label>
                                    <select name="settings[mistercash][zone]" class="form-control tax-class">
                                        <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                                        <?php foreach ($zones as $list): ?>
                                        <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($settings['mistercash']['zone']) && $settings['mistercash']['zone'] == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_MISTERCASH_ZONE_HELPER')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 payment-zone">
                            <div class="hidden">
                                <input type="text" name="settings[mistercash][rate]" class="excluding-tax" value="<?php if(isset($settings['mistercash']['rate'])) { echo $settings['mistercash']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[mistercash][rate_type]" value="f" <?php if (isset($settings['mistercash']['rate_type']) && $settings['mistercash']['rate_type'] == 'f' || !isset($settings['mistercash']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label> <label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[mistercash][rate_type]" value="p" <?php if (isset($settings['mistercash']['rate_type']) && $settings['mistercash']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="payment hidden" id="payment-overboeking-disabled">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-3 logo"><img src="/apps/sisow/paylogos/banktransfer.png"></div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_BANKTRANSFER')?></label>
                                    <div>
                                        <?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_BANKTRANSFER_DESCRIPTION')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[overboeking][enabled]" type="checkbox" <?php if (isset($settings['overboeking']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="overboeking">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 payment-zone">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?></label>
                                    <select name="settings[overboeking][zone]" class="form-control tax-class">
                                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                    </select>
                                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_BANKTRANSFER_ZONE_HELPER')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 payment-zone">
                            <div class="hidden">
                                <input type="text" name="settings[overboeking][rate]" class="excluding-tax" value="<?php if(isset($settings['overboeking']['rate'])) { echo $settings['overboeking']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[overboeking][rate_type]" value="f" <?php if (isset($settings['overboeking']['rate_type']) && $settings['overboeking']['rate_type'] == 'f' || !isset($settings['overboeking']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[overboeking][rate_type]" value="p" <?php if (isset($settings['overboeking']['rate_type']) && $settings['overboeking']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="payment" id="payment-paypalec">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-3 logo"><img src="/apps/sisow/paylogos/paypalec.png"></div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_PAYPAL')?></label>
                                    <div>
                                        <?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_PAYPAL_DESCRIPTION')?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[paypalec][enabled]" type="checkbox" <?php if (isset($settings['paypalec']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="paypalec">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 payment-zone">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?></label>
                                    <select name="settings[paypalec][zone]" class="form-control tax-class">
                                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                    </select>
                                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_PAYPAL_ZONE_HELPER')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 payment-zone">
                            <div class="hidden">
                                <input type="text" name="settings[paypalec][rate]" class="excluding-tax" value="<?php if(isset($settings['paypalec']['rate'])) { echo $settings['paypalec']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[paypalec][rate_type]" value="f" <?php if (isset($settings['paypalec']['rate_type']) && $settings['paypalec']['rate_type'] == 'f' || !isset($settings['paypalec']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[paypalec][rate_type]" value="p" <?php if (isset($settings['paypalec']['rate_type']) && $settings['paypalec']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="payment" id="payment-sofort">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-3 logo"><img src="/apps/sisow/paylogos/directebanking.gif"></div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_SOFORT')?></label>
                                    <div>
                                        <?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_SOFORT_DESCRIPTION')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                                <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                    <input name="settings[sofort][enabled]" type="checkbox" <?php if (isset($settings['sofort']['enabled'])) { echo 'checked'; }?> class="enable-payment" payment="sofort">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 payment-zone">
                            <div class="col-md-9 col-md-offset-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?></label>
                                    <select name="settings[sofort][zone]" class="form-control tax-class">
                                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                                    </select>
                                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_SOFORT_ZONE_HELPER')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 payment-zone">
                            <div class="hidden">
                                <input type="text" name="settings[sofort][rate]" class="excluding-tax" value="<?php if(isset($settings['sofort']['rate'])) { echo $settings['sofort']['rate']; } ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_TRANSACTION_FEE')?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[sofort][rate_type]" value="f" <?php if (isset($settings['sofort']['rate_type']) && $settings['sofort']['rate_type'] == 'f' || !isset($settings['sofort']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[sofort][rate_type]" value="p" <?php if (isset($settings['sofort']['rate_type']) && $settings['sofort']['rate_type'] == 'p') { echo 'checked'; } ?>></label> %</span>
                                    <input type="text" class="form-control price including-tax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="row payment" id="payment-none">
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
                <div class="col-md-12">
                    <h4><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS')?></h4>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS_SUCCESS')?></label>
                        <select name="settings[status][success]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']['success']) && $list['order_status_id'] == $settings['status']['success']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS_CANCELLED')?></label>
                        <select name="settings[status][cancelled]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']['cancelled']) && $list['order_status_id'] == $settings['status']['cancelled']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS_EXPIRED')?></label>
                        <select name="settings[status][expired]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']['expired']) && $list['order_status_id'] == $settings['status']['expired']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS_FAILURE')?></label>
                        <select name="settings[status][failure]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']['failure']) && $list['order_status_id'] == $settings['status']['failure']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS_PENDING')?></label>
                        <select name="settings[status][pending]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']['pending']) && $list['order_status_id'] == $settings['status']['pending']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                            <?php endforeach; endif?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SISOW_PAYMENT_STATUS_REVERSED')?></label>
                        <select name="settings[status][reversed]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php if (is_array($statusses)): foreach ($statusses as $list): ?>
                            <option value="<?php echo $list['order_status_id']?>" <?php if (isset($settings['status']['reversed']) && $list['order_status_id'] == $settings['status']['reversed']) { echo 'selected'; }?>><?php echo $list['name']?></option>
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
    $('#api-debug, .api-ok, .payment, .payment-zone, .discount').hide();
    <?php if (!isset($settings['idontwanttohavediscount']) && !isset($settings['ialreadyhavediscount'])): ?>
    $('.no-discount').slideDown();
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
    $('.api-required').on('keyup', function() {
        var d           = new Date();
        window.keyup    = d.getTime();
        var t           = this;
        setTimeout(function() {
            var d       = new Date();
            if (d.getTime() - window.keyup >= 500) {
                var key = $(t).val();
                if (key.length >= 5) {
                    $.post('app/sisow/ajax?token=<?php echo $this->session->data['token']?>', {check: 'validate-api', id: $('input[name="settings[merchant][id]"]').val(), key: $('input[name="settings[merchant][key]"]').val()}, function(data) {
                        if (data.response == true) {
                            canHazOptions = true;
                            $('.api-ok-icon').parent().removeClass('alert-warning').addClass('alert-success');
                            $('.api-ok-icon').removeClass('fa-exclamation-triangle').addClass('fa-check');
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
                            $('.api-ok-icon').parent().removeClass('alert-success').addClass('alert-warning');
                            $('.api-ok-icon').removeClass('fa-check').addClass('fa-exclamation-triangle');
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
                            if (data.options) {
                                $.each(data.options.payment, function(name, id) {
                                    $('#payment-' + id).show();
                                })
                            }
                            else {
                                $('#payment-none').slideDown();
                            }
                        }
                    }, 'JSON');
                }
                else {
                    $('.api-ok-icon').removeClass('fa-check').addClass('fa-exclamation-triangle');
                    $('#api-debug').find('.message').html('<?php echo Sumo\Language::getVar('APP_MOLLIE_ERROR_INVALID_API_KEY')?>');
                    $('#api-debug').removeClass('alert-info').addClass('alert alert-warning');
                    $('#api-debug').find('.icon').html('<i class="fa fa-warning"></i>');
                    if ($('#api-debug').is(':hidden')) {
                        $('#api-debug').slideDown();
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
    <?php if (!isset($settings['idontwanttohavediscount'])): ?>
    $('.no-discount').find('.message').css({cursor: 'pointer'});
    $('.no-discount').find('.message').on('click', function(e) {
        bootbox.dialog({
            message: $('#discount-form').html(),
            buttons: {
                ignore: {
                    "label": '<?php echo Sumo\Language::getVar('APP_MOLLIE_BUTTON_IGNORE_DISCOUNT')?>',
                    'className': 'btn-danger',
                    'callback': function(e) {
                        e.stopPropagation();
                    }
                },
                validate: {
                    "label": '<?php echo Sumo\Language::getVar('APP_MOLLIE_BUTTON_VALIDATE')?>',
                    "className": 'btn-primary',
                    "callback": function (e) {
                        e.stopPropagation();
                        $('.discount-form-loader').show();
                        $.post('app/sisow/ajax?token=<?php echo $this->session->data['token']?>', {check: 'validate-discount', username: $('.bootbox .username').val(), password: $('.bootbox .password').val(), store_id: <?php echo $current_store?>}, function(data) {
                            if (data.response == true) {
                                $('.bootbox').modal('hide');
                                $('.no-discount').slideUp(function(){
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
                    "callback": function () {
                        $('.discount').hide();
                        $.post('app/mollie/ajax?token=<?php echo $this->session->data['token']?>', {check: 'ignore-discount', store_id: <?php echo $current_store?>}, function(data){

                        })
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
