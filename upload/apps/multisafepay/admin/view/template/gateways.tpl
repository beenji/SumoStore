<h4><?php echo Sumo\Language::getVar('APP_PAYMENT_OPTIONS')?></h4>
<?php foreach ($gateways as $gateway) { ?>
    <div class="row" id="payment-<?php echo $gateway['code']; ?>">
        <div class="col-md-6">
            <div class="col-md-3 logo align-center" style="padding-top: 15px;">
                <?php if (is_file('apps/multisafepay/admin/view/img/' . $gateway['code'] . '.png')) { ?>
                <img src="../apps/multisafepay/admin/view/img/<?php echo $gateway['code']; ?>.png" />
                <?php } ?>
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label><?php echo $gateway['label']; ?></label>
                    <div>
                        <?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_' . $gateway['lang_code'] . '_DESCRIPTION')?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-9">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_GATEWAY_ENABLED')?>:</label><br />
                    <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                        <input value="1" name="settings[<?php echo $gateway['code']; ?>][enabled]" type="checkbox" <?php if (isset($settings[$gateway['code']]['enabled']) && $settings[$gateway['code']]['enabled'] == 1) { echo 'checked'; }?> class="enable-payment" payment="<?php echo $gateway['code']; ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-md-6 payment-zone">
            <div class="col-md-9 col-md-offset-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_ZONE')?>:</label>
                    <select name="settings[<?php echo $gateway['code']; ?>][zone]" class="form-control tax-class">
                        <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                        <?php foreach ($zones as $list): ?>
                        <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($settings[$gateway['code']]['zone']) && $settings[$gateway['code']]['zone'] == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                        <?php endforeach ?>
                    </select>
                    <span class="help-block"><?php echo Sumo\Language::getVar('APP_MSP_PAYMENT_' . $gateway['lang_code'] . '_ZONE_HELPER')?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 payment-zone">
            <div class="col-md-9">
                <div class="hidden">
                    <input type="text" name="settings[<?php echo $gateway['code']; ?>][rate]" class="excluding-tax" value="<?php if(isset($settings[$gateway['code']]['rate'])) { echo $settings[$gateway['code']]['rate']; } ?>">
                </div>
                <div class="form-group">

                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_PAYMENT_TRANSACTION_FEE')?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[<?php echo $gateway['code']; ?>][rate_type]" value="f" <?php if (isset($settings[$gateway['code']]['rate_type']) && $settings[$gateway['code']]['rate_type'] == 'f' || !isset($settings[$gateway['code']]['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[<?php echo $gateway['code']; ?>][rate_type]" value="p" <?php if (isset($settings[$gateway['code']]['rate_type']) && $settings[$gateway['code']]['rate_type'] == 'p') { echo 'checked'; } ?>>%</label></span>
                        <input type="text" class="including-tax form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
<?php } ?>