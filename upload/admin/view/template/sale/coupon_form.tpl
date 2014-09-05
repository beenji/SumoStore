<?php echo $header; ?>

<?php if ($error) { ?>
<script type="text/javascript">
    formError = '<?php echo $error; ?>';
</script>
<?php } ?>

<form method="post" action="<?php echo $action?>" class="form-horizontal" id="form" data-parsley-validate novalidate>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#coupon" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUPON')?></a></li>
        <li><a href="#exclude" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUPON_PRODUCTS_CATEGORIES')?></a></li>
        <?php if ($coupon_id > 0) { ?><li><a href="#history" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_USAGE')?></a></li><?php } ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="coupon">
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
                <div class="col-md-5">
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
            <div class="form-group">
                <label class="col-md-2 control-label" for="name"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>: *</label>
                <div class="col-md-5">
                    <input type="text" name="name" id="name" value="<?php echo $name?>" class="form-control" required data-parsley-length="[3,128]">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="code"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUPON_CODE'); ?>: *</label>
                <div class="col-md-2">
                    <input type="text" name="code" id="code" value="<?php echo $code?>" class="form-control" required data-parsley-length="[4,50]">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="type_p"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE'); ?>: *</label>
                <div class="col-md-3">
                    <label class="radio-inline">
                        <input type="radio" name="type" id="type_p" value="P" <?php if ($type == 'P') { echo 'checked="checked"'; } ?>>
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_PERCENTAGE'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" id="type_f" value="F" <?php if ($type == 'F') { echo 'checked="checked"'; } ?>>
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_FIXED_AMOUNT'); ?>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT'); ?>:</label>
                <div class="col-md-2">
                    <input type="text" name="discount" value="<?php echo $discount?>" class="form-control">
                </div>
                <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT_IS_EX')?></span>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIN_ORDER_AMOUNT'); ?>:</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-addon">&euro;</span>
                        <input type="text" name="total" value="<?php echo $total?>" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE')?>:</label>
                <div class="col-md-2">
                    <select name="tax_percentage" class="form-control">
                        <option value="<?php echo $tax['default']?>"><?php echo $tax['default']?>%</option>
                        <?php if (is_array($tax['extra'])): foreach ($tax['extra'] as $rate): ?>
                        <option value="<?php echo $rate?>" <?php if (isset($tax_percentage) && $tax_percentage == $rate) { echo 'selected'; } ?>><?php echo $rate ?>%</option>
                        <?php endforeach; endif?>
                    </select>
                </div>
            </div>
            <!--
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LOGIN_REQUIRED'); ?>:</label>
                <div class="col-md-3">
                    <div class="radio-inline">
                        <input type="radio" name="logged" value="1" <?php if ($logged) { echo 'checked="checked"'; } ?>>
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                    </div>
                    <div class="radio-inline">
                        <input type="radio" name="logged" value="0" <?php if (!$logged) { echo 'checked="checked"'; } ?>>
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FREE_SHIPPING'); ?>:</label>
                <div class="col-md-3">
                    <div class="radio-inline">
                        <input type="radio" name="shipping" value="1" <?php if ($shipping) { echo 'checked="checked"'; } ?>>
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                    </div>
                    <div class="radio-inline">
                        <input type="radio" name="shipping" value="0" <?php if (!$shipping) { echo 'checked="checked"'; } ?>>
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                    </div>
                </div>
            </div>
            -->
            <hr>
            <input type="hidden" name="logged" value="1">
            <input type="hidden" name="shipping" value="0">
            <input type="hidden" name="coupon_product[]">
            <input type="hidden" name="coupon_category[]">
            <div class="form-group">
                <label class="col-md-2 control-label">
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PERIOD'); ?>:
                </label>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_START'); ?></small></div>
                        <input type="text" name="date_start" value="<?php echo $date_start?>" class="form-control date-picker">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_END'); ?></small></div>
                        <input type="text" name="date_end" value="<?php echo $date_end?>" class="form-control date-picker">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_USAGE'); ?>:
                </label>
                <div class="col-md-2">
                    <div class="input-group">
                        <input data-parsley-ui-enabled="false" type="text" name="uses_total" value="<?php echo $uses_total?>" class="form-control">
                        <div class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></small></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <input data-parsley-ui-enabled="false" type="text" name="uses_customer" value="<?php echo $uses_customer?>" class="form-control">
                        <div class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_PER_CUSTOMER'); ?></small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="exclude">
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_EXCLUDE_DESC'); ?></p>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORIES'); ?></strong></h5>
                    <div class="exclude-list">
                        <ul>
                            <?php foreach ($categories as $category) { ?>
                            <li>
                                <dl>
                                    <dt><input type="checkbox" id="category_<?php echo $category['category_id']; ?>" name="coupon_category[]" value="<?php echo $category['category_id']; ?>"<?php if ($category['selected']) { echo ' checked="checked"'; } ?> /></dt>
                                    <dd<?php if ($category['level'] > 0) { ?> class="indent" style="padding-left: <?php echo $category['level'] * 20; ?>px;"<?php } ?>><label for="category_<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></label></dd>
                                </dl>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS'); ?></strong></h5>
                    <div class="exclude-list">
                        <ul>
                            <?php foreach ($products as $product) { ?>
                            <li>
                                <dl>
                                    <dt><input type="checkbox" id="product_<?php echo $product['product_id']; ?>" name="coupon_product[]" value="<?php echo $product['product_id']; ?>"<?php if ($product['selected']) { echo ' checked="checked"'; } ?> /></dt>
                                    <dd><label for="product_<?php echo $product['product_id']; ?>"><?php echo $product['name']; ?></label></dd>
                                </dl>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($coupon_id > 0) { ?>
        <div class="tab-pane" id="history">
            <?php if ($histories) { ?>
            <table class="table no-border list">
                <thead class="no-border">
                    <tr>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_ID'); ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER'); ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT'); ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_ADDED'); ?></strong></th>
                    </tr>
                </thead>
                <tbody class="no-border-y items">
                    <?php foreach ($histories as $history) { ?>
                    <tr>
                        <td><?php echo $history['order_id']; ?></td>
                        <td><?php echo $history['customer']; ?></td>
                        <td><?php echo $history['amount']; ?></td>
                        <td><?php echo $history['date_added']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_HISTORY'); ?></p>
            <?php } ?>
        </div>
        <?php } ?>
    </div>

    <p class="align-right"><input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_COUPON'); ?>" /></p>
</form>

<?php echo $footer; ?>
