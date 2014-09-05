<?php
echo $header;

// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('app/shippingpickup', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
        <form method="post" action="" class="" data-parsley-validate>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_SHIPPINGPICKUP_ENABLE')?></label>
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
                    <h4><?php echo Sumo\Language::getVar('APP_SHIPPINGPICKUP_SETTINGS_FOR_GEOZONES')?></h4>
                </div>
                <div class="col-md-6 pull-right">
                    <div class="col-md-6"></div>
                    <div class="col-md-5">
                        <select class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_FORM_CHOOSE')?></option>
                            <?php foreach ($zones as $list): ?>
                            <option value="<?php echo $list['geo_zone_id']?>" <?php if (isset($geo_zone_id) && $geo_zone_id == $list['geo_zone_id']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                            <option value="general"><?php echo Sumo\Language::getVar('SUMO_NOUN_ALL_GEOZONES')?></option>
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
                            <tr class="">
                                <th>&nbsp;</th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_ENABLED')?></th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="zone-general" class="<?php if (!isset($settings['general']['enabled'])) { echo 'hidden'; } ?>">
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_ALL_GEOZONES')?></td>
                                <td style="width:150px;">
                                    <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                        <input id="enabled" name="settings[general][enabled]" type="checkbox" <?php if (isset($settings['general']['enabled'])) { echo 'checked'; }?>>
                                    </div>
                                </td>
                                <td style="width:70px;">
                                    <a href="#remove-zone" class="btn btn-sm btn-danger pull-right remove-zone">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php foreach ($zones as $list):
                                $show = false;
                                if (isset($settings['general']['enabled'])) {
                                    if (!isset($settings['zone'][$list['geo_zone_id']]['enabled'])) {
                                        $show = true;
                                    }
                                    else {
                                        $show = true;
                                    }
                                }
                                else if (isset($settings['zone'][$list['geo_zone_id']]['enabled'])) {
                                    $show = true;
                                }
                            ?>
                            <tr id="zone-<?php echo $list['geo_zone_id']?>" class="<?php if (!$show) { echo 'hidden'; } ?>">
                                <td><?php echo $list['name']?></td>
                                <td style="width:150px;">
                                    <div class="switch switch-small" data-on-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_YES'))?>" data-off-label="<?php echo strtoupper(Sumo\Language::getVar('SUMO_NOUN_NO'))?>">
                                        <input id="enabled" name="settings[zone][<?php echo $list['geo_zone_id']?>][enabled]" type="checkbox" <?php if (isset($settings['zone'][$list['geo_zone_id']]['enabled'])) { echo 'checked'; }?>>
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
        $(this).parent().parent().find('input').prop('checked', 0);
        $(this).parent().parent().hide();
    });
})
</script>
<?php echo $footer ?>
