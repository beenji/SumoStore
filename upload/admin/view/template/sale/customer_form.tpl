<?php echo $header; ?>

<script type="text/javascript">
    var customerID = '<?php echo $customer_id; ?>',
        formError = '<?php echo $form_error; ?>',
        sessionToken = '<?php echo $token; ?>';
</script>

<form action="" method="post">
    <div class="row">
        <div class="col-md-7">
            <?php if ($customer_id == 0) { ?>
            <h3 style="margin-bottom: 20px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL'); ?></h3>
            <?php } else { ?>
            <ul class="nav nav-tabs" style="margin-top: 28px;">
                <li class="active"><a href="#general" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL'); ?></a></li>
                <li><a href="#history" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_HISTORY'); ?></a></li>
                <li><a href="#transactions" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_TRANSACTIONS'); ?></a></li>
                <li><a href="#reward" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD'); ?></a></li>
                <li><a href="#ip" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_IPS'); ?></a></li>
            </ul>
            <?php } ?>

            <div class="tab-content">
                <div class="tab-pane active" id="general">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
                                <div class="control-group">
                                    <div class="radio-inline">
                                        <input type="radio" name="status" value="1" <?php if ($status) { echo 'checked="checked"'; } ?>>
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_ACTIVE'); ?>
                                    </div>
                                    <div class="radio-inline">
                                        <input type="radio" name="status" value="0" <?php if (!$status) { echo 'checked="checked"'; } ?>>
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_INACTIVE'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMERGROUP'); ?>:</label>
                                <select name="customer_group_id" class="form-control">
                                    <?php foreach ($customer_groups as $customer_group) { ?>
                                        <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                                        <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>: *</label>
                                <input type="text" id="firstname" name="firstname" value="<?php echo $firstname?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLE_NAME')?>:</label>
                                <input type="text" name="middlename" value="<?php echo isset($middlename) ? $middlename : ''?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SURNAME'); ?>: *</label>
                                <input type="text" id="lastname" name="lastname" value="<?php echo $lastname?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BIRTHDATE'); ?>:</label>
                                <input type="text" name="birthdate" value="<?php echo $birthdate?>" class="form-control birthdate-picker">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE_NO'); ?>: *</label>
                                <input type="text" name="telephone" value="<?php echo $telephone?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MOBILE'); ?>:</label>
                                <input type="text" name="mobile" value="<?php echo $mobile?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FAX'); ?>:</label>
                                <input type="text" name="fax" value="<?php echo $fax?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>: *</label>
                                <input type="text" name="email" value="<?php echo $email?>" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD'); ?>:</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" value="" class="form-control">
                                    <span class="input-group-addon"><a href="javascript:;" id="generate_password"><i class="fa fa-lock"></i></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_PASSWORD'); ?>:</label>
                                <input type="password" id="confirm" name="confirm" value="" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER'); ?>:</label>
                                <div class="control-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" value="m" <?php if ($gender == 'm') { echo 'checked="checked"'; } ?>>
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_MALE'); ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" value="f" <?php if ($gender == 'f') { echo 'checked="checked"'; } ?>>
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_FEMALE'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEWSLETTER'); ?>:</label>
                                <div class="control-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="newsletter" value="1" <?php if ($newsletter) { echo 'checked="checked"'; } ?>>
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="newsletter" value="0" <?php if (!$newsletter) { echo 'checked="checked"'; } ?>>
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($customer_id > 0) { ?>
                <div class="tab-pane" id="history">
                    <div class="form-group">
                        <label for="description-hs" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTE'); ?>:</label>
                        <textarea id="description-hs" cols="*" rows="4" class="form-control"></textarea>
                    </div>

                    <a style="margin-left: 0;" href="javascript:;" id="add-history" class="btn btn-primary btn-sm"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_NOTE'); ?></a>

                    <div id="table-hs">
                        <?php if ($histories) { ?>
                        <hr>

                        <table class="table no-border">
                            <thead class="no-border">
                                <tr>
                                    <th style="width: 90px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></strong></th>
                                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTE'); ?></strong></th>
                                </tr>
                            </thead>
                            <tbody class="no-border-y items">
                                <?php foreach ($histories as $history) { ?>
                                <tr>
                                    <td><?php echo $history['date_added']; ?></td>
                                    <td><?php echo $history['comment']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>

                <div class="tab-pane" id="transactions">
                    <div class="row" style="margin-top: 0;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description-tr" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?>:</label>
                                <input type="text" id="description-tr" value="" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount-tr" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_SHORT'); ?>:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><small>&euro;</small></span>
                                    <input type="text" id="amount-tr" value="" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <a style="margin-left: 0;" href="javascript:;" id="add-transaction" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_TRANSACTION'); ?></a>

                    <div id="table-tr">
                        <?php if ($transactions) { ?>
                        <hr>

                        <table class="table no-border">
                            <thead class="no-border">
                                <tr>
                                    <th style="width: 90px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></strong></th>
                                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></strong></th>
                                    <th style="width: 90px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_SHORT'); ?></strong></th>
                                </tr>
                            </thead>
                            <tbody class="no-border-y items">
                                <?php foreach ($transactions as $transaction) { ?>
                                <tr>
                                    <td><?php echo $transaction['date_added']; ?></td>
                                    <td><?php echo $transaction['description']; ?></td>
                                    <td class="right"><?php echo $transaction['amount']; ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_BALANCE'); ?>:</strong></td>
                                    <td class="right"><?php echo $transaction_balance; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>

                <div class="tab-pane" id="reward">
                    <div class="row" style="margin-top: 0;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description-rw" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?>:</label>
                                <input type="text" id="description-rw" value="" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="points-rw" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POINTS'); ?>:</label>
                                <input type="text" id="points-rw" value="" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <a style="margin-left: 0;" href="javascript:;" id="add-reward" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_REWARD'); ?></a>

                    <div id="table-rw">
                        <?php if ($rewards) { ?>
                        <hr>

                        <table class="table no-border">
                            <thead class="no-border">
                                <tr>
                                    <th style="width: 90px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></strong></th>
                                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></strong></th>
                                    <th style="width: 90px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_POINTS'); ?></strong></th>
                                </tr>
                            </thead>
                            <tbody class="no-border-y items">
                                <?php foreach ($rewards as $reward) { ?>
                                <tr>
                                    <td class="left"><?php echo $reward['date_added']; ?></td>
                                    <td class="left"><?php echo $reward['description']; ?></td>
                                    <td class="right"><?php echo $reward['points']; ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td></td>
                                    <td class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_BALANCE'); ?>:</strong></td>
                                    <td class="right"><?php echo $points_balance; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>

                <div class="tab-pane" id="ip">
                    <?php if ($ips) { ?>
                    <table class="table no-border">
                        <thead class="no-border">
                            <tr>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_IP'); ?></strong></th>
                                <th style="width: 90px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></strong></th>
                                <th style="width: 90px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></strong></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="no-border-y items">
                            <?php foreach ($ips as $ip) { ?>
                            <tr>
                                <td><a href="http://www.geoiptool.com/en/?IP=<?php echo $ip['ip']; ?>" target="_blank"><?php echo $ip['ip']; ?></a></td>
                                <td><a href="<?php echo $ip['filter_ip']; ?>" target="_blank"><?php echo $ip['total']; ?></a></td>
                                <td><?php echo $ip['date_added']; ?></td>
                                <td class="right">
                                    <a href="javascript:;" class="btn btn-sm btn-primary remove-ip-ban <?php if (!$ip['ban_ip']): echo 'hidden'; endif; ?>" data-ip="<?php echo $ip['ip']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_REMOVE_BAN'); ?></a>
                                    <a href="javascript:;" class="btn btn-sm btn-primary add-ip-ban <?php if ($ip['ban_ip']): echo 'hidden'; endif; ?>" data-ip="<?php echo $ip['ip']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_ADD_BAN'); ?></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_IPS'); ?></p>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-5">
            <h3 style="margin-bottom: 20px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_BOOK'); ?></h3>

            <div class="tab-container tab-right">
                <ul class="nav nav-tabs" id="address-tabs">
                    <?php foreach ($address as $address_row => $list) { ?>
                    <li<?php if ($address_row == 0) { ?> class="active"<?php } ?>><a href="#<?php echo chr(97 + $address_row); ?>" data-toggle="tab"><?php echo chr(65 + $address_row); ?></a></li>
                    <?php } ?>
                    <li><a href="javascript:;" id="new-book"><i class="fa fa-plus"></i></a></li>
                </ul>

                <div class="tab-content" id="address-tab-panes">
                    <?php foreach ($address as $address_row => $list) { ?>
                    <div class="tab-pane<?php if ($address_row == 0) { ?> active<?php } ?>" id="<?php echo chr(97 + $address_row); ?>">
                        <input type="hidden" name="address[<?php echo $address_row; ?>][address_id]" value="<?php echo $list['address_id']; ?>" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][firstname]" value="<?php echo $list['firstname']?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLE_NAME')?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][middlename]" value="<?php echo isset($list['middlename']) ? $list['middlename'] : ''?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SURNAME'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][lastname]" value="<?php echo $list['lastname']?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY_NAME'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][company]" value="<?php echo $list['company']?>" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group company-id-display">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY_NUMBER'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][company_id]" value="<?php echo $list['company_id']?>" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group tax-id-display">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_NUMBER'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][tax_id]" value="<?php echo $list['tax_id']?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY'); ?>:</label>
                                    <select name="address[<?php echo $address_row; ?>][country_id]" data-zone-id="<?php echo $list['zone_id']; ?>" class="form-control pc-api">
                                        <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_CHOOSE'); ?></option>
                                        <?php foreach ($countries as $country) { ?>
                                        <?php if ($country['country_id'] == $list['country_id']) { ?>
                                        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_REGION'); ?>:</label>
                                    <select name="address[<?php echo $address_row; ?>][zone_id]" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                        <?php /* Improved workflow with PC API */ if ($this->config->get('pc_api_key') != '') { ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][postcode]" value="<?php echo $list['postcode']?>" class="form-control pc-api">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NUMBER'); ?>:</label>
                                    <input type="text" class="form-control pc-api" value="<?php echo $list['number']?>" name="address[<?php echo $address_row?>][number]">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_ADDON')?></label>
                                    <input type="text" class="form-control" value="<?php echo $list['addon']?>" name="address[<?php echo $address_row?>][addon]">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1'); ?>:</label>
                                    <input type="text" class="form-control" name="address[<?php echo $address_row?>][address_1]" value="<?php echo $list['address_1']?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][city]" value="<?php echo $list['city']?>" class="form-control">
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][postcode]" value="<?php echo $list['postcode']?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY'); ?>:</label>
                                    <input type="text" name="address[<?php echo $address_row?>][city]" value="<?php echo $list['city']?>" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1'); ?>:</label>
                                    <input type="text" class="form-control" name="address[<?php echo $address_row?>][address_1]" value="<?php echo $list['address_1']?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_NUMBER')?></label>
                                    <input type="text" class="form-control" value="<?php echo $list['number']?>" name="address[<?php echo $address_row?>][number]">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_ADDON')?></label>
                                    <input type="text" class="form-control" value="<?php echo $list['addon']?>" name="address[<?php echo $address_row?>][addon]">
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_2'); ?>:</label>
                            <input type="text" name="address[<?php echo $address_row?>][address_2]" value="<?php echo $list['address_2']?>" class="form-control">
                        </div>
                    </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <p>
        <a class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_DEBTOR'); ?>" />
    </p>
</form>

<?php echo $footer; ?>
