<?php echo $header; ?>

<style type="text/css">
    .super-form .align-right .control-label {
        line-height: 24px;
        padding: 5px 0;
    }

    .super-form .align-right .control-label.offset-label {
        margin-top: 38px;
    }
</style>

<script type="text/javascript">
    var payment_zone_id     = '<?php echo isset($order['customer']) ? $order['customer']['payment_address']['zone_id'] : 0; ?>',
        shipping_zone_id    = '<?php echo isset($order['customer']) ? $order['customer']['shipping_address']['zone_id'] : 0; ?>',
        productCount        = 0,
        order_id            = <?php echo isset($order['order_id']) ? $order['order_id'] : 0?>,
        symbol_left         = '<?php echo $currency['symbol_left']; ?>',
        decimal_place       = '<?php echo $currency['decimal_place']; ?>',
        decimal_point       = ',',
        thousand_point      = '.',
        discountName        = '<?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT')?>',
        selectDefault       = '<?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE'))?>',
        textOTSubtotal      = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_SUBTOTAL')?>',
        textOTPointsInj     = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_POINTS_INJ')?>',
        textOTDiscountInj   = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_DISCOUNT_INJ')?>',
        textOTPayment       = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_PAYMENT')?>',
        textOTShipping      = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_SHIPPING')?>',
        textOTCouponInj     = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_COUPON_INJ')?>',
        textOTTotal         = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_TOTAL')?>',
        textOTTaxInj        = '<?php echo Sumo\Language::getVar('SUMO_NOUN_OT_TAX_INJ')?>',
        pointValue          = <?php echo floatval($this->config->get('points_value')) ?>;
</script>

<form method="post" action="" class="super-form" id="order_form">
    <div class="page-head-actions align-right">
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_ORDER')?>" /></a>
        <a href="<?php echo $this->url->link('sale/orders') ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL')?></a>
    </div>

    <ul class="nav nav-tabs" id="choose-tab">
        <li class="active"><a href="#customer" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER')?></a></li>
        <li><a href="#payment" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_ADDRESS')?></a></li>
        <li><a href="#shipping" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_ADDRESS')?></a></li>
        <li><a href="#product" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS')?></a></li>
        <li><a href="#shipping-method" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING')?></a></li>
        <li><a href="#payment-method" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_METHOD')?></a></li>
        <li><a href="#total" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL')?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="customer">
            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STORE')?>:</label>
                </div>
                <div class="col-md-3">
                    <select name="store[id]" id="store_id" class="form-control">
                        <?php foreach ($stores as $list): ?>
                            <option value="<?php echo $list['store_id']?>" <?php if (isset($order['store']['id']) && $list['store_id'] == $order['store']['id']) { echo 'selected'; }?>><?php echo $list['name']?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-2 align-right">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_SINGULAR')?>:</label>
                </div>
                <div class="col-md-3">
                    <div class="input-group" style="width: 100%;">
                        <input type="text" value="<?php echo isset($order['customer']['firstname']) ? str_replace('  ', ' ', implode(' ', array($order['customer']['firstname'], $order['customer']['middlename'], $order['customer']['lastname']))) : '' ?>" class="form-control" id="find_customer" autocomplete="off" />
                        <span class="input-group-btn" id="url_to_customer">
                            <a class="btn btn-primary" href="<?php echo $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=', 'SSL')?>" data-customer-id="<?php echo (isset($order['customer']) ? $order['customer']['customer_id'] : '')?>">
                                <?php echo Sumo\Language::getVar('SUMO_ADMIN_VIEW_CUSTOMER')?>
                            </a>
                        </span>
                    </div>

                    <input type="hidden" name="customer[customer_id]" value="<?php echo isset($order['customer']['customer_id']) ? $order['customer']['customer_id'] : '' ?>" />
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-10 col-md-offset-2">
                    <p><?php echo Sumo\Language::getVar('SUMO_ADMIN_ORDER_CUSTOMER_DATA_CHANGE_INFO')?></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?>:</label>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRST_NAME')?> *:</label>
                        <input type="text" name="customer[firstname]" value="<?php echo isset($order['customer']['firstname']) ? $order['customer']['firstname'] : '' ?>" class="form-control" required />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLE_NAME')?>:</label>
                        <input type="text" name="customer[middlename]" value="<?php echo isset($order['customer']['middlename']) ? $order['customer']['middlename'] : ''?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LAST_NAME')?> *:</label>
                        <input type="text" name="customer[lastname]" value="<?php echo isset($order['customer']['lastname']) ? $order['customer']['lastname'] : '' ?>" class="form-control" required />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-md-offset-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP')?>:</label>
                        <select class="form-control" name="customer[customer_group_id]" <?php echo isset($order['customer']['customer_id']) ? 'disabled="disabled"' : '' ?>>
                            <?php foreach ($customer_groups as $list): ?>
                                <option value="<?php echo $list['customer_group_id']; ?>"<?php if (isset($order['customer']['customer_group_id']) && $list['customer_group_id'] == $order['customer']['customer_group_id']) { echo 'selected'; } ?>><?php echo $list['name']; ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER')?> *:</label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="customer[gender]" value="m" <?php if (isset($order['customer']['gender']) && $order['customer']['gender'] == 'm' || !isset($order['customer']['gender'])) { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_MALE')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="customer[gender]" value="f" <?php if (isset($order['customer']['gender']) && $order['customer']['gender'] == 'f') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_FEMALE')?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-2 align-right">
                    <label for="" class="control-label offset-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONTACT'); ?>:</label>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE')?> *:</label>
                        <input type="text" name="customer[telephone]" value="<?php echo isset($order['customer']['telephone']) ? $order['customer']['telephone'] : '' ?>" class="form-control" required />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MOBILE')?>:</label>
                        <input type="text" name="customer[mobile]" value="<?php echo isset($order['customer']['mobile']) ? $order['customer']['mobile'] : '' ?>" class="form-control" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FAX')?>:</label>
                        <input type="text" name="customer[fax]" value="<?php echo isset($order['customer']['fax']) ? $order['customer']['fax'] : '' ?>" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-md-offset-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL')?> *:</label>
                        <input type="email" name="customer[email]" value="<?php echo isset($order['customer']['email']) ? $order['customer']['email'] : '' ?>" class="form-control" required />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BIRTHDATE')?>:</label>
                        <input type="text" name="customer[birthdate]" value="<?php echo isset($order['customer']['birthdate']) && $order['customer']['birthdate'] != '0000-00-00' ? Sumo\Formatter::date($order['customer']['birthdate']) : '' ?>" class="form-control date-picker"  />
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="payment">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2 align-right">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS')?>:</label>
                    </div>
                    <div class="col-md-3">
                        <select name="customer[payment_address]" class="form-control">
                            <option value="0" selected="selected"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE'))?></option>
                            <?php if (isset($addresses)): foreach ($addresses as $list): ?>
                            <option value="<?php echo $list['address_id']?>" <?php if (isset($order['customer']['payment_address']['address_id']) && $order['customer']['payment_address']['address_id'] == $list['address_id']) { echo 'selected'; } ?>>
                                <?php echo $list['firstname'] . ' ' . $list['lastname'] . ', ' . $list['address_1'] . ', ' . $list['city'] . ', ' . $list['country']?>
                            </option>
                            <?php endforeach; endif ?>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-2 align-right">
                    <label for="" class="control-label offset-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?>:</label>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRST_NAME')?> *:</label>
                        <input type="text" name="customer[payment_address][firstname]" value="<?php echo isset($order['customer']['payment_address']['firstname']) ? $order['customer']['payment_address']['firstname'] : ''?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLE_NAME')?>:</label>
                        <input type="text" name="customer[payment_address][middlename]" value="<?php echo isset($order['customer']['payment_address']['middlename']) ? $order['customer']['payment_address']['middlename'] : ''?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LAST_NAME')?> *:</label>
                        <input type="text" name="customer[payment_address][lastname]" value="<?php echo isset($order['customer']['payment_address']['lastname']) ? $order['customer']['payment_address']['lastname'] : ''?>" class="form-control" required>
                    </div>
                </div>
            </div>

            <hr>

            <?php if ($this->config->get('pc_api_key')) { ?>
            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label">Adresgegevens:</label>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?>:</label>
                        <input type="text" name="customer[payment_address][postcode]" value="<?php echo isset($order['customer']['payment_address']['postcode']) ? $order['customer']['payment_address']['postcode'] : ''?>" class="form-control pc-api" required>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NUMBER')?>:</label>
                        <div class="row">
                            <div class="col-md-2" style="padding-right: 0;">
                                <input type="text" name="customer[payment_address][number]" value="<?php echo isset($order['customer']['payment_address']['number']) ? $order['customer']['payment_address']['number'] : ''?>" class="form-control pc-api" required>
                            </div>
                            <div class="col-md-2" style="padding-right: 0;">
                                <input type="text" name="customer[payment_address][addon]" value="<?php echo isset($order['customer']['payment_address']['addon']) ? $order['customer']['payment_address']['addon'] : ''?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1')?> *:</label>
                        <input type="text" name="customer[payment_address][address_1]" value="<?php echo isset($order['customer']['payment_address']['address_1']) ? $order['customer']['payment_address']['address_1'] : ''?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY')?> *:</label>
                        <input type="text" name="customer[payment_address][city]" value="<?php echo isset($order['customer']['payment_address']['city']) ? $order['customer']['payment_address']['city'] : ''?>" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY')?> *:</label>
                        <select name="customer[payment_address][country_id]" id="payment_country_id" class="form-control pc-api" required>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']; ?>"<?php if (isset($order['customer']) && $list['country_id'] == $order['customer']['payment_address']['country_id']) { echo 'selected'; } ?>>
                                <?php echo $list['name']?>
                            </option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE')?> *:</label>
                        <select name="customer[payment_address][zone_id]" id="payment_zone_id" class="form-control" required></select>
                    </div>
                </div>
            </div>
            <?php } else { ?>
            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label">Adresgegevens:</label>
                </div>

                <div class="col-md-10">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1')?> *:</label>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="customer[payment_address][address_1]" value="<?php echo isset($order['customer']['payment_address']['address_1']) ? $order['customer']['payment_address']['address_1'] : ''?>" class="form-control" required>
                            </div>
                            <div class="col-md-1" style="padding-right: 0; padding-left: 0;">
                                <input type="text" name="customer[payment_address][number]" value="<?php echo isset($order['customer']['payment_address']['number']) ? $order['customer']['payment_address']['number'] : ''?>" class="form-control pc-api" required>
                            </div>
                            <div class="col-md-1" style="padding-right: 0;">
                                <input type="text" name="customer[payment_address][addon]" value="<?php echo isset($order['customer']['payment_address']['addon']) ? $order['customer']['payment_address']['addon'] : ''?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-md-offset-2 col-lg-1" style="padding-right: 0;">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?>:</label>
                        <input type="text" name="customer[payment_address][postcode]" value="<?php echo isset($order['customer']['payment_address']['postcode']) ? $order['customer']['payment_address']['postcode'] : ''?>" class="form-control pc-api" required>
                    </div>
                </div>
                <div class="col-md-5 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY')?> *:</label>
                        <input type="text" name="customer[payment_address][city]" value="<?php echo isset($order['customer']['payment_address']['city']) ? $order['customer']['payment_address']['city'] : ''?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-3 col-md-offset-2 col-lg-offset-0 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY')?> *:</label>
                        <select name="customer[payment_address][country_id]" id="payment_country_id" class="form-control pc-api" required>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']; ?>"<?php if (isset($order['customer']) && $list['country_id'] == $order['customer']['payment_address']['country_id']) { echo 'selected'; } ?>>
                                <?php echo $list['name']?>
                            </option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE')?> *:</label>
                        <select name="customer[payment_address][zone_id]" id="payment_zone_id" class="form-control" required></select>
                    </div>
                </div>
            </div>
            <?php } ?>

            <hr>

            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label">Bedrijfsgegevens:</label>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY')?>:</label>
                        <input type="text" name="customer[payment_address][company]" value="<?php echo isset($order['customer']['payment_address']['company']) ? $order['customer']['payment_address']['company'] : ''?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group" id="company-id-display">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY_ID')?> *:</label>
                        <input type="text" name="customer[payment_address][company_id]" value="<?php echo isset($order['customer']['payment_address']['company_id']) ? $order['customer']['payment_address']['company_id'] : ''?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group" id="tax-id-display">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_ID')?>:</label>
                        <input type="text" name="customer[payment_address][tax_id]" value="<?php echo isset($order['customer']['payment_address']['tax_id']) ? $order['customer']['payment_address']['tax_id'] : ''?>" class="form-control">
                    </div>
                </div>
            </div>
        </div>

                <div class="tab-pane" id="shipping">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2 align-right">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS')?>:</label>
                    </div>
                    <div class="col-md-3">
                        <select name="customer[shipping_address]" class="form-control">
                            <option value="0" selected="selected"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE'))?></option>
                            <?php if (isset($addresses)): foreach ($addresses as $list): ?>
                            <option value="<?php echo $list['address_id']?>" <?php if (isset($order['customer']['shipping_address']['address_id']) && $order['customer']['shipping_address']['address_id'] == $list['address_id']) { echo 'selected'; } ?>>
                                <?php echo $list['firstname'] . ' ' . $list['lastname'] . ', ' . $list['address_1'] . ', ' . $list['city'] . ', ' . $list['country']?>
                            </option>
                            <?php endforeach; endif ?>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-2 align-right">
                    <label for="" class="control-label offset-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?>:</label>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRST_NAME')?> *:</label>
                        <input type="text" name="customer[shipping_address][firstname]" value="<?php echo isset($order['customer']['shipping_address']['firstname']) ? $order['customer']['shipping_address']['firstname'] : ''?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLE_NAME')?>:</label>
                        <input type="text" name="customer[shipping_address][middlename]" value="<?php echo isset($order['customer']['shipping_address']['middlename']) ? $order['customer']['shipping_address']['middlename'] : ''?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LAST_NAME')?> *:</label>
                        <input type="text" name="customer[shipping_address][lastname]" value="<?php echo isset($order['customer']['shipping_address']['lastname']) ? $order['customer']['shipping_address']['lastname'] : ''?>" class="form-control" required>
                    </div>
                </div>
            </div>

            <hr>

            <?php if ($this->config->get('pc_api_key')) { ?>
            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label">Adresgegevens:</label>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?>:</label>
                        <input type="text" name="customer[shipping_address][postcode]" value="<?php echo isset($order['customer']['shipping_address']['postcode']) ? $order['customer']['shipping_address']['postcode'] : ''?>" class="form-control pc-api" required>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NUMBER')?>:</label>
                        <div class="row">
                            <div class="col-md-2" style="padding-right: 0;">
                                <input type="text" name="customer[shipping_address][number]" value="<?php echo isset($order['customer']['shipping_address']['number']) ? $order['customer']['shipping_address']['number'] : ''?>" class="form-control pc-api" required>
                            </div>
                            <div class="col-md-2" style="padding-right: 0;">
                                <input type="text" name="customer[shipping_address][addon]" value="<?php echo isset($order['customer']['shipping_address']['addon']) ? $order['customer']['shipping_address']['addon'] : ''?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1')?> *:</label>
                        <input type="text" name="customer[shipping_address][address_1]" value="<?php echo isset($order['customer']['shipping_address']['address_1']) ? $order['customer']['shipping_address']['address_1'] : ''?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY')?> *:</label>
                        <input type="text" name="customer[shipping_address][city]" value="<?php echo isset($order['customer']['shipping_address']['city']) ? $order['customer']['shipping_address']['city'] : ''?>" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY')?> *:</label>
                        <select name="customer[shipping_address][country_id]" id="shipping_country_id" class="form-control pc-api" required>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']; ?>"<?php if (isset($order['customer']) && $list['country_id'] == $order['customer']['shipping_address']['country_id']) { echo 'selected'; } ?>>
                                <?php echo $list['name']?>
                            </option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE')?> *:</label>
                        <select name="customer[shipping_address][zone_id]" id="shipping_zone_id" class="form-control" required></select>
                    </div>
                </div>
            </div>
            <?php } else { ?>
            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label">Adresgegevens:</label>
                </div>

                <div class="col-md-10">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_1')?> *:</label>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="customer[shipping_address][address_1]" value="<?php echo isset($order['customer']['shipping_address']['address_1']) ? $order['customer']['shipping_address']['address_1'] : ''?>" class="form-control" required>
                            </div>
                            <div class="col-md-1" style="padding-right: 0; padding-left: 0;">
                                <input type="text" name="customer[shipping_address][number]" value="<?php echo isset($order['customer']['shipping_address']['number']) ? $order['customer']['shipping_address']['number'] : ''?>" class="form-control pc-api" required>
                            </div>
                            <div class="col-md-1" style="padding-right: 0;">
                                <input type="text" name="customer[shipping_address][addon]" value="<?php echo isset($order['customer']['shipping_address']['addon']) ? $order['customer']['shipping_address']['addon'] : ''?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-md-offset-2 col-lg-1" style="padding-right: 0;">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POSTCODE')?>:</label>
                        <input type="text" name="customer[shipping_address][postcode]" value="<?php echo isset($order['customer']['shipping_address']['postcode']) ? $order['customer']['shipping_address']['postcode'] : ''?>" class="form-control pc-api" required>
                    </div>
                </div>
                <div class="col-md-5 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY')?> *:</label>
                        <input type="text" name="customer[shipping_address][city]" value="<?php echo isset($order['customer']['shipping_address']['city']) ? $order['customer']['shipping_address']['city'] : ''?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-3 col-md-offset-2 col-lg-offset-0 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUNTRY')?> *:</label>
                        <select name="customer[shipping_address][country_id]" id="shipping_country_id" class="form-control pc-api" required>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']; ?>"<?php if (isset($order['customer']) && $list['country_id'] == $order['customer']['shipping_address']['country_id']) { echo 'selected'; } ?>>
                                <?php echo $list['name']?>
                            </option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ZONE')?> *:</label>
                        <select name="customer[shipping_address][zone_id]" id="shipping_zone_id" class="form-control" required></select>
                    </div>
                </div>
            </div>
            <?php } ?>

            <hr>

            <div class="row">
                <div class="col-md-2 align-right">
                    <label class="control-label offset-label">Bedrijfsgegevens:</label>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY')?>:</label>
                        <input type="text" name="customer[shipping_address][company]" value="<?php echo isset($order['customer']['shipping_address']['company']) ? $order['customer']['shipping_address']['company'] : ''?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group" id="company-id-display">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMPANY_ID')?> *:</label>
                        <input type="text" name="customer[shipping_address][company_id]" value="<?php echo isset($order['customer']['shipping_address']['company_id']) ? $order['customer']['shipping_address']['company_id'] : ''?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group" id="tax-id-display">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_ID')?>:</label>
                        <input type="text" name="customer[shipping_address][tax_id]" value="<?php echo isset($order['customer']['shipping_address']['tax_id']) ? $order['customer']['shipping_address']['tax_id'] : ''?>" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="product">
            <table class="table no-border">
                <thead class="no-border">
                    <tr>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT')?></strong></th>
                        <th style="width: 150px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL')?></strong></th>
                        <th style="width: 150px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY')?></strong></th>
                        <th style="width: 150px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE')?></strong></th>
                        <th style="width: 150px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL')?></strong></th>
                        <th></th>
                    </tr>
                </thead>
                <?php $product_row = $option_row = $download_row = 0; ?>
                <tbody id="product-list" class="no-border-y">
                    <?php if (isset($order['lines']) && sizeof($order['lines']) > 0) { ?>
                    <?php foreach ($order['lines'] as $line) { ?>
                    <tr id="product-row<?php echo $product_row; ?>" data-order-product-row="<?php echo $product_row; ?>">
                        <td class="left">
                            <?php echo $line['name']; ?><br />
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][product_id]" value="<?php echo $line['product_id']; ?>" />
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][name]" value="<?php echo $line['name']; ?>" />
                            <?php  foreach ($line['option'] as $option) { ?>
                                - <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][order_option_id]" value="<?php echo $option['order_option_id']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][product_option_id]" value="<?php echo $option['product_option_id']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][product_option_value_id]" value="<?php echo $option['product_option_value_id']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][name]" value="<?php echo $option['name']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][value]" value="<?php echo $option['value']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][type]" value="<?php echo $option['type']; ?>" />
                                <?php $option_row++;
                            } ?>

                            <?php if (!empty($line['download'])) { foreach ($line['download'] as $download) { ?>
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][download][<?php echo $download_row; ?>][order_download_id]" value="<?php echo $download['order_download_id']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][download][<?php echo $download_row; ?>][name]" value="<?php echo $download['name']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][download][<?php echo $download_row; ?>][filename]" value="<?php echo $download['filename']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][download][<?php echo $download_row; ?>][mask]" value="<?php echo $download['mask']; ?>" />
                                <input type="hidden" name="lines[<?php echo $product_row; ?>][download][<?php echo $download_row; ?>][remaining]" value="<?php echo $download['remaining']; ?>" />
                                <?php $download_row++;
                            } } ?>
                        </td>
                        <td class="left">
                            <?php echo $line['model']; ?>
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][model]" value="<?php echo $line['model']; ?>" />
                        </td>
                        <td class="right">
                            <?php echo $line['quantity']; ?>
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][quantity]" value="<?php echo $line['quantity']; ?>" />
                        </td>
                        <td class="right">
                            <span><?php echo Sumo\Formatter::currency($line['price'] * (1 + ($line['tax_percentage'] / 100))) ?></span>
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][price]" value="<?php echo $line['price']; ?>" />
                        </td>
                        <td class="right">
                            <span><?php echo Sumo\Formatter::currency($line['price'] * (1 + ($line['tax_percentage'] / 100)) * $line['quantity']) ?></span>
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][total]" value="<?php echo $line['price'] * $line['quantity']; ?>" />
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][tax]" value="<?php echo $line['tax_percentage']?>" />
                            <input type="hidden" name="lines[<?php echo $product_row; ?>][weight]" value="<?php echo $line['weight']?>" />
                        </td>
                        <td style="width: 40px;"><a href="javascript:;" rel="remove-product"><i style="font-size: 15px;" class="fa fa-minus-circle"></i></a></td>
                    </tr>
                    <?php $product_row++;
                    } } ?>
                </tbody>
                <tfoot class="no-border-y">
                    <tr>
                        <td>
                            <input type="hidden" id="new_product_id" name="new_product[product_id]" value="" />
                            <div>
                                <input type="text" id="new_product_name" autocomplete="off" name="new_product[name]" value="" class="form-control" />
                            </div>
                        </td>
                        <td>
                            <div>
                                <input type="text" id="new_product_model" autocomplete="off" name="new_product[model]" value="" class="form-control" />
                            </div>
                        </td>
                        <td class="align-right"><input type="text" id="new_product_quantity" name="new_product[quantity]" value="" class="form-control align-right" /></td>
                        <td class="align-right">
                            <div class="input-group">
                                <span class="input-group-addon">&euro;</span>
                                <input type="text" id="new_product_price" name="new_product[price]" value="" class="form-control align-right" />
                            </div>
                        </td>
                        <td class="align-right">
                            <div class="input-group">
                                <span class="input-group-addon">&euro;</span>
                                <input type="text" id="new_product_total" name="new_product[total]" value="" class="form-control align-right" />
                                <input type="hidden" id="new_product_tax" name="new_product[tax]" value="" />
                                <input type="hidden" id="new_product_weight" name="new_product[weight]" value="" />
                            </div>
                        </td>
                        <td style="width: 40px;"><a href="javascript:;" rel="add-product"><i style="font-size: 15px;" class="fa fa-plus-circle"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-pane" id="shipping-method">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING')?>:</label>
                        <select id="shipping_method" class="form-control"></select>
                    </div>
                </div>

                <div class="col-md-7 col-md-offset-1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?>:</label>
                                <input type="text" name="method[shipping][name]" value="<?php echo isset($order['shipping']['name']) ? $order['shipping']['name'] : ''?>" id="shipping_method_name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_EX')?>:</label>
                                <input type="text" name="method[shipping][price]" value="<?php echo isset($order['shipping']['price']) ? $order['shipping']['price'] : ''?>" id="shipping_method_price" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_PERCENTAGE')?>:</label>
                                <input type="text" name="method[shipping][tax_percentage]" value="<?php echo isset($order['shipping']['tax_percentage']) ? $order['shipping']['tax_percentage'] : ''?>" id="shipping_method_tax_percentage" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_IN')?>:</label>
                                <input type="text" name="method[shipping][total]" value="<?php echo isset($order['shipping']['total']) ? $order['shipping']['total'] : ''?>" id="shipping_method_total" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="payment-method">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_METHOD')?>:</label>
                        <select id="payment_method" class="form-control"></select>
                    </div>
                </div>
                <div class="col-md-7 col-md-offset-1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?>:</label>
                                <input type="text" name="method[payment][name]" value="<?php echo isset($order['payment']['name']) ? $order['payment']['name'] : ''?>" id="payment_method_name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_EX')?>:</label>
                                <input type="text" name="method[payment][price]" value="<?php echo isset($order['payment']['price']) ? $order['payment']['price'] : ''?>" id="payment_method_price" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_PERCENTAGE')?>:</label>
                                <input type="text" name="method[payment][tax_percentage]" value="<?php echo isset($order['payment']['tax_percentage']) ? $order['payment']['tax_percentage'] : ''?>" id="payment_method_tax_percentage" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_IN')?>:</label>
                                <input type="text" name="method[payment][total]" value="<?php echo isset($order['payment']['total']) ? $order['payment']['total'] : ''?>" id="payment_method_total" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="total">
            <table class="table no-border table-invoice">
                <thead class="no-border">
                    <tr>
                        <th class="left"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT')?></strong></th>
                        <th class="left"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL')?></strong></th>
                        <th class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY')?></strong></th>
                        <th class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE')?></strong></th>
                        <th class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL')?></strong></th>
                    </tr>
                </thead>
                <tbody class="no-border-y" id="summary-product-list"></tbody>
                <tfoot class="no-border-y totals" id="summary-totals"></tfoot>
            </table>

            <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS')?></strong></h5>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_STATUS')?></label>
                        <select name="order_status_id" class="form-control">
                            <?php foreach ($statuses as $list) { ?>
                            <option value="<?php echo $list['order_status_id']; ?>" <?php if (isset($order['order_status']) && $list['order_status_id'] == $order['order_status']) { echo 'selected'; } ?>><?php echo $list['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_POINTS')?>:</label>
                        <input type="text" name="points" id="points" value="<?php echo isset($order['points']) ? $order['points'] : 0?>" class="form-control">
                        <input type="hidden" name="points_calculation" value="<?php echo $this->config->get('points_value')?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT')?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="radio" name="discount[type]" value="F" <?php if (isset($order['discount']['type']) && $order['discount']['type'] == 'F') { echo 'checked'; } ?>> <?php echo $currency['symbol_left']?>
                            </span>
                            <input type="text" name="discount[discount]" id="discount" value="<?php echo isset($order['discount']['discount']) ? $order['discount']['discount'] : ''?>" class="form-control">
                            <span class="input-group-addon">
                                <input type="radio" name="discount[type]" value="P" <?php if (isset($order['discount']['type']) && $order['discount']['type'] == 'P' || !isset($order['discount']['type'])) { echo 'checked'; } ?>> %
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUPON')?>:</label>
                        <?php if (isset($order['discount']['coupon'])) { ?>
                        <input type="text" id="coupon_code" name="discount[coupon][code]" value="<?php echo $order['discount']['coupon']['code']; ?>" class="form-control">
                        <input type="hidden" name="discount[coupon][coupon_id]" value="<?php echo $order['discount']['coupon']['coupon_id'];?>">
                        <input type="hidden" name="discount[coupon][type]" value="<?php echo $order['discount']['coupon']['type'];?>">
                        <input type="hidden" name="discount[coupon][value]" value="<?php echo $order['discount']['coupon']['value'];?>">
                        <?php } else { ?>
                        <input type="text" id="coupon_code" name="discount[coupon][code]" class="form-control">
                        <input type="hidden" name="discount[coupon][coupon_id]">
                        <input type="hidden" name="discount[coupon][type]">
                        <input type="hidden" name="discount[coupon][value]">
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="form-group">
                <label for="comment" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENT')?>:</label>
                <textarea name="comment" class="form-control" rows="3"><?php echo isset($order['comment']) ? $order['comment'] : '' ?></textarea>
            </div>
        </div>
    </div>
</form>

<?php echo $footer; ?>
