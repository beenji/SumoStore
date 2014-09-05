<?php echo $header; ?>

<form action="" method="post">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#general"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL'); ?></a></li>
        <li><a data-toggle="tab" href="#notes"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane cont active" id="general">
            <div class="row">
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_CREDITOR_INFO'); ?></h4>

                    <div class="form-group">
                        <label for="companyname" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANYNAME'); ?>:</label>
                        <input type="text" name="companyname" id="companyname" class="form-control" value="<?php echo $companyname; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="contact_name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONTACT'); ?>:</label>
                        <div class="control-group row" style="margin-top: 0;">
                            <div class="col-md-3">
                                <select name="contact_gender" id="contact_gender" class="form-control">
                                    <option value="m"<?php if ($contact_gender == 'm') { echo ' selected="selected"'; } ?>><?php echo Sumo\Language::getVar('SUMO_NOUN_SIR'); ?></option>
                                    <option value="f"<?php if ($contact_gender == 'f') { echo ' selected="selected"'; } ?>><?php echo Sumo\Language::getVar('SUMO_NOUN_MISS'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="contact_name" id="contact_name" class="form-control" value="<?php echo $contact_name; ?>" />
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="contact_surname" id="contact_surname" class="form-control" value="<?php echo $contact_surname; ?>" />
                            </div>
                        </div>
                        
                    </div>

                    <div class="form-group">
                        <label for="address" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS'); ?>:</label>
                        <input type="text" name="address" id="address" class="form-control" value="<?php echo $address; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="postcode" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE'); ?>:</label>
                        <div class="control-group row" style="margin-top: 0;">
                            <div class="col-md-3">
                                <input type="text" name="postcode" id="postcode" class="form-control" value="<?php echo $postcode; ?>" />
                            </div>

                            <div class="col-md-9">
                                <input type="text" name="city" id="city" class="form-control" value="<?php echo $city; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="country_id" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY'); ?>:</label>
                        <select name="country_id" id="country_id" class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT'); ?></option>
                            <?php foreach ($countries as $country) { ?>
                            <option value="<?php echo $country['country_id']; ?>"<?php if ($country['country_id'] == $country_id) { echo ' selected="selected"'; } ?>><?php echo $country['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_BANK_INFO'); ?></h4>

                    <div class="form-group">
                        <label for="bank_iban" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_IBAN'); ?>:</label>
                        <input type="text" name="bank_iban" id="bank_iban" class="form-control" value="<?php echo $bank_iban; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="bank_account" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BANK_ACCOUNT'); ?>:</label>
                        <input type="text" name="bank_account" id="bank_account" class="form-control" value="<?php echo $bank_account; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="bank_name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BANK'); ?>:</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?php echo $bank_name; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="bank_city" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BANK_CITY'); ?>:</label>
                        <input type="text" name="bank_city" id="bank_city" class="form-control" value="<?php echo $bank_city; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="bank_bic" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BIC'); ?>:</label>
                        <input type="text" name="bank_bic" id="bank_bic" class="form-control" value="<?php echo $bank_bic; ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_CONTACT_INFO'); ?></h4>

                    <div class="form-group">
                        <label for="contact_email" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>:</label>
                        <input type="text" name="contact_email" id="contact_email" class="form-control" value="<?php echo $contact_email; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="contact_phone" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE'); ?>:</label>
                        <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="<?php echo $contact_phone; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="contact_mobile" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MOBILE'); ?>:</label>
                        <input type="text" name="contact_mobile" id="contact_mobile" class="form-control" value="<?php echo $contact_mobile; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="contact_fax" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FAX'); ?>:</label>
                        <input type="text" name="contact_fax" id="contact_fax" class="form-control" value="<?php echo $contact_fax; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="customer_number" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NUMBER'); ?>:</label>
                        <input type="text" name="customer_number" id="customer_number" class="form-control" value="<?php echo $customer_number; ?>" />
                    </div>
                </div>
                <div class="col-md-6">
                    <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_FINANCIAL_INFO'); ?></h4>

                    <div class="form-group">
                        <label for="term" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TERM'); ?>:</label>
                        <div class="input-group">
                            <input type="text" name="term" id="term" class="form-control" value="<?php echo $term; ?>" />
                            <span class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_DAYS'); ?></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane cont" id="notes">
            <div class="form-group">
                <label for="notes" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?>:</label>
                <textarea name="notes" id="notes" rows="8" class="form-control"><?php echo $notes; ?></textarea>
            </div>
        </div>
    </div>

    <p class="align-right">
        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_CREDITOR'); ?>" class="btn btn-primary" />
    </p>
</form>

<?php echo $footer; ?>