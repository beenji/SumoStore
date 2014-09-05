<div class="col-md-12" id="<?php echo $type?>_address">
    <?php if (is_array($addresses) && count($addresses)): ?>
    <div class="form-group">
        <label class="control-label radio-inline"><input type="radio" class="<?php echo $type?>_address" value="existing" checked><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_BOOK')?></label>
    </div>
    <div class="form-group <?php echo $type?>_existing">
        <select name="customer[<?php echo $type?>_address][address_id]" class="form-control" size="5">
            <?php foreach ($addresses as $list): ?>
            <option value="<?php echo $list['address_id']?>" <?php if (!empty($address_id) && $address_id == $list['address_id']) { echo 'selected'; } ?>><?php echo $list['firstname'] . ' ' . (!empty($list['middlename']) ? $list['middlename'] . ' ' : '') . $list['lastname'] . ', ' . $list['address_1']  . (!empty($list['number']) ? ' ' . $list['number'] : '') . (!empty($list['addon']) ? ' ' . $list['addon'] : '') . ', ' . $list['postcode'] . ' ' . $list['city'] . ', ' . $list['country']?></option>
            <?php endforeach ?>
        </select>
    </div>
    <?php endif?>
    <div class="form-group">
        <label class="control-label radio-inline"><input type="radio" class="<?php echo $type?>_address" value="new"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEW_ADDRESS')?></label>
    </div>
    <div class="<?php echo $type?>_new <?php if (isset($addresses) && count($addresses)) { echo 'hidden'; } ?> col-sm-12 col-md-12">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRSTNAME')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][firstname]" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLENAME')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][middlename]" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_LASTNAME')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][lastname]" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY')?>:</label>
                    <div class="col-sm-8">
                        <select name="customer[<?php echo $type?>_address][country_id]" class="form-control" required id="<?php echo $type?>_address_country">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_FORM_SELECT_CHOOSE'); ?></option>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']?>" <?php if ($list['country_id'] == $this->config->get('country_id')) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][postcode]" class="form-control pc-check" required>
                        <span class="form-control-feedback hidden"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][address_1]" class="form-control" required id="<?php echo $type?>_address_street">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_NUMBER')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][number]" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_ADDON')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][addon]" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_2')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][address_2]" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY')?>:</label>
                    <div class="col-sm-8">
                        <input type="text" name="customer[<?php echo $type?>_address][city]" class="form-control" required id="<?php echo $type?>_address_city">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE')?>:</label>
                    <div class="col-sm-8">
                        <select name="customer[<?php echo $type?>_address][zone_id]" class="form-control" required id="<?php echo $type?>_address_zone">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_FORM_SELECT_CHOOSE'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="form-group">
                    <label class="control-label col-sm-6"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY')?>:</label>
                    <div class="col-sm-6">
                        <input type="text" name="" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-6"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY_ID')?>:</label>
                    <div class="col-sm-6">
                        <input type="text" name="" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-6"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_ID')?>:</label>
                    <div class="col-sm-6">
                        <input type="text" name="" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <input type="button" class="btn btn-primary pull-right" data-step="<?php if ($type == 'payment') { echo 3; } else { echo 4; } ?>" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONTINUE')?>">
    </div>

    <div class="clearfix"></div>
</div>

<script type="text/javascript">
$(function(){
    var checkAddressType = '<?php echo $type?>_address';
    var classAddressExisting = '.<?php echo $type?>_existing';
    var classAddressNew = '.<?php echo $type?>_new';
    $('#<?php echo $type?>_address').on('click', '.btn', function(e) {
        e.preventDefault();
        var elem = $(this);
        if ($('.' + checkAddressType + ':checked').val() == 'existing') {
            var address_id = $('select[name="customer[' + checkAddressType + '][address_id]"]').val();
            $.post('checkout/checkout/addresscheck', {address_id: address_id}, function(data) {
                if (handleAddressResponse(elem, data)) {
                    step(elem.data('step'));
                }
            }, 'json')
        }
        else {
            var contSubmit = true;
            $('#' + checkAddressType).find(':input').each(function() {
                if ($(this).prop('required') && $(this).val() == '') {
                    $(this).parent().parent().addClass('has-error');
                    contSubmit = false;
                }
                else if ($(this).prop('required') && $(this).val() != '') {
                    $(this).parent().parent().removeClass('has-error');
                }
            })
            $.post('checkout/checkout/addresscheck', {address:$('#' + checkAddressType ).find(':input').serialize()}, function(data) {
                var contCheck = handleAddressResponse(elem, data);
                if (contCheck && contSubmit) {
                    $('select[name="customer[' + checkAddressType + '][address_id]"]').val('0')
                    step(elem.data('step'));
                }
            }, 'json');
        }
    })
    $('.' + checkAddressType).on('change click', function() {
        $('.' + checkAddressType).prop('checked', 0);
        $(this).prop('checked', 1);
        if ($(this).val() == 'existing') {
            if ($(classAddressNew).is(':visible')) {
                $(classAddressNew).addClass('hidden');
            }
            $(classAddressExisting).removeClass('hidden');
        }
        else {
            if ($(classAddressExisting).is(':visible')) {
                $(classAddressExisting).addClass('hidden');
            }
            $(classAddressNew).removeClass('hidden');
        }
    })
    $('#<?php echo $type?>_address_country').on('change click', function() {
        $.post('checkout/checkout/fetchzones', {country_id: $(this).val()}, function(data) {
            var html = '<option value=""><?php echo Sumo\Language::getVar('SUMO_FORM_SELECT_CHOOSE'); ?></option>';
            if (data.zones) {
                $('#<?php echo $type?>_address_zone').prop('required', 1);
                $.each(data.zones, function(k, zone) {
                    html += '<option value="' + zone.zone_id + '">' + zone.name + '</option>';
                })
            }
            else {
                $('#<?php echo $type?>_address_zone').prop('required', 0);
                html = '<option value="1"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE'))?></option>';
            }
            $('#<?php echo $type?>_address_zone').html(html);
        }, 'json');
    }).trigger('change');
    $('.pc-check').on('change keyup', function() {
        if ($(this).val().length >= 6) {
            var elem = $(this);
            $.getJSON('common/pc?token_pc=<?php echo $this->session->data['pc']?>&q=' + $(this).val(), function(data){
                if (data.success && data.resource) {
                    elem.parent().find('.form-control-feedback').addClass('picons-yes-success').removeClass('picons-close-exit hidden');
                    elem.parent().parent().addClass('has-success').removeClass('has-error');
                    $('#<?php echo $type?>_address_street').val(data.resource['street']);
                    $('#<?php echo $type?>_address_city').val(data.resource['town']);
                    $('#<?php echo $type?>_address_country').val(150).trigger('change');
                    setTimeout(function() {
                        $('#<?php echo $type?>_address_zone option').each(function() {
                            if ($(this).html() == data.resource['province']) {
                                $('#<?php echo $type?>_address_zone').val($(this).val());
                            }
                        })
                    }, 500);
                }
                else {
                    if (!data.disabled) {
                        elem.parent().parent().addClass('has-error').removeClass('has-success');
                        elem.parent().find('.form-control-feedback').addClass('picons-close-exit').removeClass('picons-yes-success hidden');
                    }
                    else {
                        elem.parent().parent().removeClass('has-error has-success');
                        elem.parent().find('.form-control-feedback').addClass('hidden');
                    }
                }
            })
        }
    })
})

function handleAddressResponse(elem, data) {
    if (data.ok) {
        if ($('#alerts').is(':visible')) {
            $('#alerts').removeClass('alert-danger alert').addClass('hidden').html('');
        }
        return true;
    }
    else {
        if (data.message && data.message.length > 0) {
            if ($('#alerts').is(':hidden')) {
                $('#alerts').removeClass('hidden');
            }
            $('#alerts').addClass('alert alert-danger').html(data.message);
        }
        else {
            if ($('#alerts').is(':visible')) {
                $('#alerts').addClass('hidden');
            }
            $('#alerts').html('');
        }
        return false;
    }
}
</script>
