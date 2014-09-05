<?php echo $header?>
<div class="container">
    <?php if (!empty($settings['left']) && count($settings['left'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['left'] as $key => $item) {
            if (!$item || $item == null) {
                unset($settings['left'][$key]);
                continue;
            }
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    $mainClass = 'col-md-12';
    if (!empty($settings['left']) && !empty($settings['right'])) {
        $mainClass = 'col-md-6';
    }
    else if (!empty($settings['left']) || !empty($settings['right'])) {
        $mainClass = 'col-md-9';
    }
    ?>

    <div class="<?php echo $mainClass?>">
        <div class="row">
            <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_REGISTER_TITLE')?></h1>

            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                <?php endforeach?>
            </ol>
            <div class="col-md-12">
                <div class="alert alert-info"><p><?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT_ALREADY', $this->url->link('account/login', '', 'SSL')); ?></p></div>
                <?php if (!empty($warning)): ?>
                <div class="alert alert-warning"><p><?php echo $warning?></p></div>
                <?php endif?>
            </div>
        </div>
        <form method="post" class="form">
            <div class="row">
                <div class="col-md-6">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_YOUR_DETAILS')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER')?></label>
                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="gender" value="m" required <?php if (isset($gender) && $gender == 'm' || empty($gender) || ($gender != 'm' && $gender != 'f')) { echo 'checked'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_MALE')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" value="f" required <?php if (isset($gender) && $gender == 'f') { echo 'checked'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_FEMALE')?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRSTNAME')?></label>
                        <input type="text" name="firstname" value="<?php echo $firstname?>" required class="form-control">
                        <?php if ($error_firstname): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_firstname?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLENAME')?></label>
                        <input type="text" name="middlename" value="<?php echo $middlename?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LASTNAME')?></label>
                        <input type="text" name="lastname" value="<?php echo $lastname?>" required class="form-control">
                        <?php if ($error_lastname): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_lastname?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL')?></label>
                        <input type="email" name="email" value="<?php echo $email?>" required class="form-control">
                        <?php if ($error_email): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_email?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TELEPHONE')?></label>
                        <input type="text" name="telephone" value="<?php echo $telephone?>" required class="form-control">
                        <?php if ($error_telephone): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_telephone?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MOBILE_PHONE')?></label>
                        <input type="text" name="mobile" value="<?php echo $mobile?>" class="form-control">
                        <?php if ($error_mobile): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_mobile?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FAX')?></label>
                        <input type="text" name="fax" value="<?php echo $fax?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BIRTHDATE')?></label>
                        <input type="text" name="birthdate" value="<?php echo $birthdate?>" <?php if ($this->config->get('age_checkout')) { echo 'required';}?> class="form-control birthdatepicker" placeholder="<?php echo Sumo\Formatter::dateShort(strtotime('-' . $this->config->get('age_checkout') . ' year'))?>">
                        <?php if ($error_birthdate): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_birthdate?></span>
                        <?php endif?>
                    </div>
                </div>

                <div class="col-md-6">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_YOUR_ADDRESS')?></h3>
                    <?php if (count($customer_groups) > 1): ?>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP')?></label>
                        <select name="customer_group_id" class="form-control" required>
                            <?php foreach ($customer_groups as $list): ?>
                            <option value="<?php echo $list['customer_group_id']?>" <?php if ($list['customer_group_id'] == $customer_group_id || (empty($customer_group_id) && $list['customer_group_id'] == $this->config->get('customer_group_id'))): echo 'selected'; endif; ?>><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="customer_group_id" value="<?php echo $this->config->get('customer_group_id')?>">
                    <?php endif?>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY')?></label>
                        <input type="text" name="company" value="<?php echo $company?>" class="form-control">
                        <?php if ($error_company): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_company?></span>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY_ID')?></label>
                        <input type="text" name="company_id" value="<?php echo $company_id?>"class="form-control">
                        <?php if ($error_company_id): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_company_id?></span>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_ID')?></label>
                        <input type="text" name="tax_id" value="<?php echo $tax_id?>"class="form-control">
                        <?php if ($error_tax_id): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_tax_id?></span>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY')?></label>
                        <select name="country_id" class="form-control">
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']?>" <?php if ($country_id == $list['country_id']): echo 'selected'; endif; ?>><?php echo $list['name']?></option>
                            <?php endforeach?>
                        </select>
                    </div>
                    <?php if ($this->config->get('pc_api_key')): ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?></label>
                                <input type="text" name="postcode" value="<?php echo $postcode?>" class="form-control" required>
                                <?php if ($error_postcode): ?>
                                <span class="help-block alert alert-danger"><?php echo $error_postcode?></span>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1')?></label>
                                <input type="text" name="address_1" value="<?php echo $address_1?>" class="form-control" required>
                                <?php if ($error_address_1): ?>
                                <span class="help-block alert alert-danger"><?php echo $error_address_1?></span>
                                <?php endif ?>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_NUMBER')?></label>
                                <input type="text" name="number" value="<?php echo $number?>" class="form-control" required>
                                <?php if ($error_number): ?>
                                <span class="help-block alert alert-danger"><?php echo $error_number?></span>
                                <?php endif ?>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_ADDON')?></label>
                                <input type="text" name="addon" value="<?php echo !empty($addon) ? $addon : ''?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_2')?></label>
                        <input type="text" name="address_2" value="<?php echo $address_2?>" class="form-control">
                    </div>
                    <?php if (!$this->config->get('pc_api_key')): ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?></label>
                                <input type="text" name="postcode" value="<?php echo $postcode?>" class="form-control" required>
                                <?php if ($error_postcode): ?>
                                <span class="help-block alert alert-danger"><?php echo $error_postcode?></span>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY')?></label>
                        <input type="text" name="city" value="<?php echo $city?>" class="form-control" required>
                        <?php if ($error_city): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_city?></span>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE')?></label>
                        <select name="zone_id" class="form-control"></select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_YOUR_PASSWORD')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD')?></label>
                        <input type="password" name="password" value="" autocomplete="off" required class="form-control">
                        <?php if ($error_password): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_password?></span>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD_CONFIRM')?></label>
                        <input type="password" name="confirm" value="" autocomplete="off" required class="form-control">
                        <?php if ($error_confirm): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_confirm?></span>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_NEWSLETTER')?></h3>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEWSLETTER')?></label>
                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="newsletter" value="1" <?php if ($newsletter): echo 'checked'; endif; ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="newsletter" value="0" <?php if (!$newsletter): echo 'checked'; endif; ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <div class="form-control">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="agree" value="1" <?php if ($agree): echo 'checked'; endif; ?>> <?php echo $text_agree?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="pull-right">
                    <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONTINUE')?>">
                </div>
            </div>
        </form>
    </div>

    <?php if (isset($settings['right'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['right'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    if (isset($settings['bottom'])): ?>
    <div class="col-md-12">
        <?php
        foreach ($settings['bottom'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <div class="clearfix"></div>
    <?php endif ?>
</div>
<script type="text/javascript">
$(function() {
    <?php if ($this->config->get('pc_api_key')): ?>
$(document).on('blur', 'input[name="postcode"]', function() {
        var val    = $(this).val();
        // Fill stuff
        if ($('select[name="country_id"]').val() == 150) {
            $('[name=address_1],[name=city],[name=zone_id]').prop('disabled', 1);
            $.getJSON('common/pc?token_pc=<?php echo $this->session->data['pc']?>&q=' + val, function(data) {
                if (data.resource != undefined) {
                    $('[name=address_1],[name=city],[name=zone_id]').prop('disabled', 0);
                    $('[name=postcode]').parent().removeClass('has-error').addClass('has-success');
                    $('input[name="address_1"]').val(data.resource.street);
                    $('input[name="city"]').val(data.resource.town);

                    // Set province
                    $('select[name="zone_id"] option').each(function() {
                        if ($(this).html().replace(' ', '-') == data.resource.province) {
                            $(this).attr('selected', true);
                            $('select[name="zone_id"]').val($(this).attr('value'));
                            return;
                        }
                    })
                }
                else {
                    $('[name=postcode]').parent().removeClass('has-success').addClass('has-error');
                }
            });
        }
        else {
            $('[name=address_1],[name=city],[name=zone_id]').prop('disabled', 0);
            $('[name=postcode]').parent().removeClass('has-success has-error');
        }
    });
    <?php endif?>
    $("select[name='country_id']").bind('change', function() {
        $.ajax({
            url: 'account/register/country&country_id=' + this.value,
            dataType: 'json',
            beforeSend: function() {
                $("select[name='country_id']").after('<span class="wait">&nbsp;<img src="catalog/view/theme/<?php echo $this->config->get('template')?>/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('.wait').remove();
            },
            success: function(json) {
                html = '';
                if (json['zone'] != '') {
                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                        if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                            html += ' selected="selected"';
                        }
                        html += '>' + json['zone'][i]['name'] + '</option>';
                    }
                }
                else {
                    html += '<option value="0" selected="selected"><?php echo Sumo\Language::getVar('SUMO_NOUN_NONE'); ?></option>';
                }

                $("select[name='zone_id']").html(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $("select[name='country_id']").trigger('change');
    $('.birthdatepicker').datetimepicker({
        format: '<?php echo Sumo\Formatter::dateFormatToJS(); ?>',
        autoclose: true,
        minView: 2,
        maxView: 4,
        startView: 4
    });
})
</script>
<script src="admin/view/js/bootstrap/bootstrap.datetimepicker.js"></script>

<?php echo $footer ?>

