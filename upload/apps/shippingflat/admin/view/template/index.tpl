<?php
echo $header;

// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('app/shippingflat', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SHIPPINGFLAT_ENABLE')?></label>
                        <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                            <input id="enabled" name="settings[enabled]" type="checkbox" <?php if (isset($settings['enabled'])) { echo 'checked'; }?>>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="submit" class="btn btn-primary pull-right" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                </div>
            </div>
            <div class="row settingstable">
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('APP_SHIPPINGFLAT_SETTINGS_FOR_GEOZONES')?></h4>
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
            </div>
            <div class="row settingstable">
                <div class="col-sm-12">
                    <table class="">
                        <thead>
                            <tr class="">
                                <th>&nbsp;</th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_INCL')?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_TAX')?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_EXCL')?></th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="zone-general" class="<?php if (empty($settings['general']['rate'])) { echo 'hidden'; } ?>">
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL_RATE')?></td>
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
                                        <select name="settings[general][tax]" class="form-control">
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
                                            <input type="text" class="form-control price excluding-tax" readonly name="settings[general][rate]" value="<?php if (isset($settings['general']['rate'])) { echo $settings['general']['rate']; }?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width:70px;">
                                    <a href="#remove-zone" class="btn btn-sm btn-danger pull-right remove-zone">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php foreach ($zones as $list): ?>
                            <tr id="zone-<?php echo $list['geo_zone_id']?>" class="<?php if (empty($settings['zone'][$list['geo_zone_id']]['rate']) || $settings['zone'][$list['geo_zone_id']]['rate'] == $settings['general']['rate']) { echo 'hidden'; }?>">
                                <td><?php echo $list['name']?></td>
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
                                        <select name="settings[zone][<?php echo $list['geo_zone_id']?>][tax]" class="form-control">
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
                                            <input type="text" class="form-control price excluding-tax" readonly name="settings[zone][<?php echo $list['geo_zone_id']?>][rate]" value="<?php if (!empty($settings['zone'][$list['geo_zone_id']]['rate'])) { echo $settings['zone'][$list['geo_zone_id']]['rate']; }?>">
                                        </div>
                                    </div>
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
        element.find('.including-tax').val($('#general').val()).trigger('change');
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
        if ($(this).val().length) {
            var parent = $(this).parent().parent().parent().parent();
            var tofind = parent.find('.including-tax');
            var element = parent.find('select');

            tofind.val(calculateIn($(this).val(), element));
        }
    })
    $('.including-tax').on('change keyup', function() {
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
