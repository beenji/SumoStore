<?php
echo $header;

// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('app/shippingweight', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
        <form method="post" action="" class="">
            <div class="row">
                <div class="col-md-10">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('APP_SHIPPINGWEIGHT_ENABLE')?></label>
                            <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                <input id="enabled" name="settings[enabled]" type="checkbox" <?php if (isset($settings['enabled'])) { echo 'checked'; }?>>
                            </div>
                        </div>
                    </div>
                    <?php if (count($stores)): ?>
                    <div class="col-md-7">
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="control-label"><?php echo Sumo\Language::getVar('APP_SHIPPING_WEIGHT_COPY')?></label>
                            </div>
                            <div class="col-md-5">
                                <select id="copy_settings" class="form-control">
                                    <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                                    <?php foreach ($stores as $list): if ($list['store_id'] == $current_store) { continue; }?>
                                    <option value="<?php echo $list['store_id']?>"><?php echo $list['name']?></option>
                                    <?php endforeach?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif?>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary pull-right" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
            </div>
            <div class="row settingstable">
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('APP_SHIPPINGWEIGHT_SETTINGS_FOR_GEOZONES')?></h4>
                </div>
                <div class="col-md-6 pull-right">
                    <div class="col-md-6"></div>
                    <div class="col-md-5">
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
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <p><?php echo Sumo\Language::getVar('APP_SHIPPING_WEIGHT_HELPER')?></p>
                </div>
            </div>
            <div class="row settingstable">
                <div class="col-md-12">
                    <table class="">
                        <thead>
                            <tr class="">
                                <th>&nbsp;</th>
                                <th style="width:155px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_WEIGHT')?></th>
                                <th style="width:150px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_INCL')?></th>
                                <th style="width:195px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_TAX')?></th>
                                <th style="width:150px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_EXCL')?></th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="zone-general" class="<?php if (empty($settings['general'])) { echo 'hidden'; } ?>">
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?> <a href="#add-entry" class="btn btn-xs btn-success pull-right add-entry"><i class="fa fa-plus"></i></a></td>
                                <td colspan="5">
                                    <table class="no-border rate-table">
                                        <tbody class="no-border-y">
                                            <?php if (!empty($settings['general'])) {
                                            $i = 0;
                                            foreach ($settings['general'] as $list): $i++; ?>
                                            <tr rate="<?php echo $i?>">
                                                <td style="width:150px;">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="text" name="settings[general][<?php echo $i?>][weight]" class="form-control" value="<?php if (isset($list['weight'])) { echo $list['weight']; } ?>">
                                                            <span class="input-group-addon"><?php echo $this->weight->getUnit(1)?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:150px;">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                                            <input type="text" id="general" class="form-control price including-tax">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:200px;">
                                                    <div class="form-group">
                                                        <select name="settings[general][<?php echo $i?>][tax]" class="form-control">
                                                            <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                                                            <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                                                            <option value="<?php echo $rate?>" <?php if (isset($settings['general']['tax']) && $settings['general']['tax'] == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                                                            <?php endforeach; endif?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td style="width:150px;">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                                            <input type="text" class="form-control price excluding-tax" readonly name="settings[general][<?php echo $i?>][rate]" value="<?php if (isset($list['rate'])) { echo $list['rate']; }?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:70px;">
                                                    <a href="#remove-entry" class="btn btn-xs btn-danger pull-right remove-entry">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; } ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="width:70px;">
                                    <a href="#remove-zone" class="btn btn-sm btn-danger pull-right remove-zone">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php foreach ($zones as $list): ?>
                            <tr id="zone-<?php echo $list['geo_zone_id']?>" class="<?php if (empty($settings['zone'][$list['geo_zone_id']]) || !count($settings['zone'][$list['geo_zone_id']])) { echo 'hidden'; }?>" data-zone="<?php echo $list['geo_zone_id']?>">
                                <td><?php echo $list['name']?> <a href="#add-entry" class="btn btn-xs btn-success pull-right add-entry"><i class="fa fa-plus"></i></a></td>
                                <td colspan="5">
                                    <table class="no-border rate-table">
                                        <tbody class="no-border-y">
                                            <?php if (!empty($settings['zone'][$list['geo_zone_id']])) {
                                            $i = 0;
                                            foreach ($settings['zone'][$list['geo_zone_id']] as $list2): $i++; ?>
                                            <tr rate="<?php echo $i?>" zone="<?php echo $list['geo_zone_id']?>">
                                                <td style="width:150px;">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="text" name="settings[zone][<?php echo $list['geo_zone_id']?>][<?php echo $i?>][weight]" class="form-control" value="<?php if (isset($list2['weight'])) { echo $list2['weight']; } ?>">
                                                            <span class="input-group-addon"><?php echo $this->weight->getUnit(1)?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:150px;">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                                            <input type="text" class="form-control price including-tax">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:200px;">
                                                    <div class="form-group">
                                                        <select name="settings[zone][<?php echo $list['geo_zone_id']?>][<?php echo $i?>][tax]" class="form-control">
                                                            <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                                                            <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                                                            <option value="<?php echo $rate?>" <?php if (isset($settings['zone'][$list['geo_zone_id']]['tax']) && $settings['zone'][$list['geo_zone_id']]['tax'] == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                                                            <?php endforeach; endif?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td style="width:150px;">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span>
                                                            <input type="text" class="form-control price excluding-tax" readonly name="settings[zone][<?php echo $list['geo_zone_id']?>][<?php echo $i?>][rate]" value="<?php if (!empty($list2['rate'])) { echo $list2['rate']; }?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:70px;">
                                                    <a href="#remove-entry" class="btn btn-xs btn-danger pull-right remove-entry">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; } ?>
                                        </tbody>
                                    </table>
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
        </form>
    </div>
</div>

<!-- @todo to be moved to own JS or make a similar function general to use -->
<script type="text/javascript">
$(function(){
    $('#copy_settings').on('change', function() {
        var copy_id = $(this).val();
        if (copy_id) {
            bootbox.confirm('<?php echo Sumo\Language::getVar('APP_SHIPPINGWEIGHT_CONFIRM_COPY')?>', function(result) {
                if (result) {
                    $.post('app/shippingweight/copy?token=<?php echo $this->session->data['token']?>', {store_id: <?php echo $current_store?>, copy_id: copy_id}, function(json){
                        if (json.ok) {
                            window.location = window.location;
                        }
                    }, 'json');
                }
            });
        }
    })
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
        element.removeClass('hidden').show();
        if (!element.find('.rate-table tbody tr').is(':visible')) {
            element.find('.add-entry').trigger('click');
        }
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
        $(this).parent().parent().find('.rate-table tbody tr').each(function(){
            $(this).remove();
        })
        $(this).parent().parent().hide();
    });
    $('.rate-table').on('click', '.remove-entry', function(e){
        e.preventDefault();
        $(this).parent().parent().remove();
    });
    $('.add-entry').on('click', function(e) {
        e.preventDefault();
        var table = $(this).parent().parent().find('.rate-table');
        var newI = 1;
        var zone = table.parent().parent().data('zone');
        table.find('tbody > tr').each(function(){
            if ($(this).attr('rate')+ 1 > newI) {
                newI = (parseInt($(this).attr('rate')) + 1);
            }
            if ($(this).attr('zone')) {
                zone = $(this).attr('zone');
            }
        });
        if (zone) {
            newID = 'zone][' + zone + '][' + newI;
        }
        else {
            newID = 'general][' + newI;
        }
        table.append('<tr rate="' + newI + '"><td style="width:150px;"><div class="form-group"><div class="input-group"><input type="text" name="settings[' + newID + '][weight]" class="form-control" value=""><span class="input-group-addon"><?php echo $this->weight->getUnit(1)?></span></div></div></td><td style="width:150px;"><div class="form-group"><div class="input-group"><span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span><input type="text" class="form-control price including-tax"></div></div></td><td style="width:200px;"><div class="form-group"><select name="settings[' + newID + '][tax]" class="form-control"><option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option><?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?><option value="<?php echo $rate?>"><?php echo $rate ?>%</option><?php endforeach; endif?></select></div></td><td style="width:150px;"><div class="form-group"><div class="input-group"><span class="input-group-addon"><?php echo $this->currency->getSymbolLeft()?></span><input type="text" class="form-control price excluding-tax" readonly name="settings[' + newID + '][rate]" value=""></div></div></td><td style="width:70px;"><a href="#remove-entry" class="btn btn-xs btn-danger pull-right remove-entry"><i class="fa fa-trash-o"></i></a></td></tr>');
    })
    $('.excluding-tax').each(function(){
        if ($(this).val().length) {
            var parent = $(this).parent().parent().parent().parent();
            var tofind = parent.find('.including-tax');
            var element = parent.find('select');

            tofind.val(calculateIn($(this).val(), element));
        }
    })
    $('.rate-table').on('change keyup', '.including-tax', function() {
        if ($(this).val().length) {
            var parent = $(this).parent().parent().parent().parent();
            var tofind = parent.find('.excluding-tax');
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
<?php echo $footer ?>
