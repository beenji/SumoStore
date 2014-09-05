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
        <a href="<?php echo $this->url->link('app/cashondelivery', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_ENABLE')?></label>
                        <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                            <input id="enabled" name="settings[enabled]" type="checkbox" <?php if (isset($settings['enabled'])) { echo 'checked'; }?>>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="submit" class="btn btn-primary pull-right" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                </div>
            </div>
            <div class="row settings">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_STATUS')?></label>
                        <div class="">
                            <select name="settings[payment][status]" class="form-control">
                                <?php if (empty($settings['payment']['status'])) { $settings['payment']['status'] = $this->config->get('config_order_status_id'); } foreach ($statusses as $list): ?>
                                <option value="<?php echo $list['order_status_id']?>" <?php if ($settings['payment']['status'] == $list['order_status_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row settingstable">
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_SETTINGS_FOR_GEOZONES')?></h4>
                </div>
                <div class="col-md-6 pull-right">
                    <div class="col-md-5 col-md-offset-6">
                        <select class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                            <?php foreach ($zones as $list): ?>
                            <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($geo_zone_id) && $geo_zone_id == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                            <option value="general"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <a href="#add-zone" class="btn btn btn-success pull-right" id="add-zone">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row settingstable">
                <div class="col-md-12">
                    <table class="">
                        <thead>
                            <tr>
                                <th><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_ZONE')?></th>
                                <th><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_MINIMUM')?></th>
                                <th><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_MAXIMUM')?></th>
                                <th><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_TRANSACTION_FEE')?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE')?></th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="zone-general" class="<?php if (empty($settings['general']['rate']) || empty($settings['general']['tax'])) { echo 'hidden'; } ?>">
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></td>
                                <td style="width: 175px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                            <input type="text" class="form-control price including-tax">
                                            <input type="hidden" name="settings[general][minimum]" class="excluding-tax" value="<?php if (isset($settings['general']['minimum'])) { echo $settings['general']['minimum']; }?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width:175px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                            <input type="text" id="general" class="form-control price including-tax">
                                            <input type="hidden" name="settings[general][maximum]" class="excluding-tax" value="<?php if (isset($settings['general']['maximum'])) { echo $settings['general']['maximum'];}?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width:200px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[general][rate_type]" value="f" <?php if (isset($settings['general']['rate_type']) && $settings['general']['rate_type'] == 'f' || !isset($settings['general']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[general][rate_type]" value="p" <?php if (isset($settings['general']['rate_type']) && $settings['general']['rate_type'] == 'p') { echo 'checked'; } ?>>%</label></span>
                                            <input type="text" class="form-control price including-tax">
                                            <input type="hidden" name="settings[general][rate]" class="excluding-tax" value="<?php if (isset($settings['general']['rate'])) { echo $settings['general']['rate']; }?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 125px;">
                                    <select name="settings[general][tax]" class="form-control">
                                        <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                                        <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                                        <option value="<?php echo $rate?>" <?php if (isset($settings['general']['tax']) && $settings['general']['tax'] == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                                        <?php endforeach; endif?>
                                    </select>
                                </td>
                                <td style="width:70px;">
                                    <a href="#remove-zone" class="btn btn-sm btn-danger pull-right remove-zone">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php foreach ($zones as $list): ?>
                            <tr id="zone-<?php echo $list['geo_zone_id']?>" class="<?php if (empty($settings['zone'][$list['geo_zone_id']]['rate']) || empty($settings['zone'][$list['geo_zone_id']]['tax'])) { echo 'hidden'; } ?>">
                                <td><?php echo $list['name']?></td>
                                <td style="width: 175px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                            <input type="text" class="form-control price including-tax">
                                            <input type="hidden" name="settings[zone][<?php echo $list['geo_zone_id']?>][minimum]" class="excluding-tax" value="<?php if (isset($settings['zone'][$list['geo_zone_id']]['minimum'])) { echo $settings['zone'][$list['geo_zone_id']]['minimum']; }?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width:175px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                            <input type="text" class="form-control price including-tax">
                                            <input type="hidden" name="settings[zone][<?php echo $list['geo_zone_id']?>][maximum]" class="excluding-tax" value="<?php if (isset($settings['zone'][$list['geo_zone_id']]['maximum'])) { echo $settings['zone'][$list['geo_zone_id']]['maximum'];}?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width:200px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[zone][<?php echo $list['geo_zone_id']?>][rate_type]" value="f" <?php if (isset($settings['zone'][$list['geo_zone_id']]['rate_type']) && $settings['zone'][$list['geo_zone_id']]['rate_type'] == 'f' || !isset($settings['zone'][$list['geo_zone_id']]['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[zone][<?php echo $list['geo_zone_id']?>][rate_type]" value="p" <?php if (isset($settings['zone'][$list['geo_zone_id']]['rate_type']) && $settings['zone'][$list['geo_zone_id']]['rate_type'] == 'p') { echo 'checked'; } ?>>%</label></span>
                                            <input type="text" class="form-control price including-tax">
                                            <input type="hidden" name="settings[zone][<?php echo $list['geo_zone_id']?>][rate]" class="excluding-tax" value="<?php if (isset($settings['zone'][$list['geo_zone_id']]['rate'])) { echo $settings['zone'][$list['geo_zone_id']]['rate']; }?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 125px;">
                                    <select name="settings[zone][<?php echo $list['geo_zone_id']?>][tax]" class="form-control">
                                        <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                                        <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                                        <option value="<?php echo $rate?>" <?php if (isset($settings['zone'][$list['geo_zone_id']]['tax']) && $settings['zone'][$list['geo_zone_id']]['tax'] == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                                        <?php endforeach; endif?>
                                    </select>
                                </td>
                                <td style="width:70px;">
                                    <a href="#remove-zone" class="btn btn-sm btn-danger pull-right remove-zone">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php /*
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_ZONE')?></label>
                        <select name="settings[zone]" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></option>
                            <?php foreach ($zones as $zone): ?>
                            <option value="<?php echo $zone['geo_zone_id']?>" <?php if (isset($settings['zone']) && $settings['zone'] == $zone['geo_zone_id']) { echo 'selected'; } ?>><?php echo $zone['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="hidden">
                        <input type="text" name="settings[minimum]" class="excluding-tax" value="<?php if (isset($settings['minimum'])) { echo $settings['minimum']; }?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_MINIMUM')?></label>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                            <input type="text" class="form-control price including-tax">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="hidden">
                        <input type="text" name="settings[maximum]" class="excluding-tax" value="<?php if (isset($settings['maximum'])) { echo $settings['maximum']; }?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_MAXIMUM')?></label>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                            <input type="text" class="form-control price including-tax">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="hidden">
                        <input type="text" name="settings[rate]" class="excluding-tax-rate" value="<?php if(isset($settings['rate'])) { echo $settings['rate']; } ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_CASHONDELIVERY_PAYMENT_TRANSACTION_FEE')?></label>
                        <div class="input-group">
                            <span class="input-group-addon"><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[rate_type]" value="f" <?php if (isset($settings['rate_type']) && $settings['rate_type'] == 'f' || !isset($settings['ideal']['rate_type'])) { echo 'checked'; } ?>> <?php echo $this->currency->getSymbolLeft()?></label><label class="radio-inline" style=""><input type="radio" class="rate_type" name="settings[rate_type]" value="p" <?php if (isset($settings['rate_type']) && $settings['rate_type'] == 'p') { echo 'checked'; } ?>>%</label></span>
                            <input type="text" class="form-control price including-tax">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
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
            <?php */ ?>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#add-zone').on('click', function(e){
        e.preventDefault();
        var geozone = $(this).parent().parent().find(':input');
        if (!$(geozone, 'option:selected').val()) {
            return;
        }
        var element = $('#zone-' + $(geozone, 'option:selected').val());
        if (element.is(':visible')) {
            return;
        }
        //element.find('.including-tax').val($('#general').val()).trigger('change');
        element.removeClass('hidden').show();
    });
    $('#enabled').on('change click', function(){
        if ($('#enabled').is(':checked')) {
            if ($('.settingstable').is(':visible') == false) {
                $('.settingstable').slideDown();
            }
        }
        else {
            if ($('.settingstable').is(':visible')) {
                $('.settingstable').slideUp();
            }
        }
    }).trigger('change');
    $('.remove-zone').on('click', function(e){
        e.preventDefault();
        $(this).parent().parent().find('.price').val('');
        $(this).parent().parent().hide();
    });

    $('.excluding-tax').each(function(){
        if ($(this).val().length > 0) {
            var parent = $(this).parent();
            var tofind = parent.find('.including-tax');
            var element = parent.parent().parent().parent().find('select');

            tofind.val(calculateIn($(this).val(), element));
        }
    })
    $('.excluding-tax-extra').each(function(){
        if ($(this).val().length) {
            var parent = $(this).parent();
            var tofind = parent.find('.including-tax-extra');
            var element = parent.parent().parent().parent().find('select');

            tofind.val(calculateIn($(this).val(), element));
        }
    })
    $('.including-tax').on('change keyup', function() {
        if ($(this).val().length) {
            var parent = $(this).parent();
            var tofind = parent.find('.excluding-tax');
            var element = parent.parent().parent().parent().find('select');

            tofind.val(calculateEx($(this).val(), element));
        }
    })
    $('.including-tax-extra').on('change keyup', function() {
        if ($(this).val().length) {
            var parent = $(this).parent().parent().parent().parent();
            var tofind = parent.find('.excluding-tax-extra');
            var element = parent.find('select');

            tofind.val(calculateEx($(this).val(), element));
        }
    })
})

function getTaxRate(element) {
    return $('option:selected', element).val();
}
function calculateIn(price, element) {
    return Math.max(parseFloat(price.replace(',', '.')) + (parseFloat(price.replace(',', '.')) * getTaxRate(element) / 100)).toFixed(2);
}
function calculateEx(price, element) {
    return Math.max(parseFloat(price.replace(',', '.')) / (1 + (getTaxRate(element) / 100))).toFixed(4);
}
</script>
<?php echo $footer?>
