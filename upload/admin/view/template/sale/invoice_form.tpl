<?php echo $header; ?>

<script type="text/javascript">
    var sessionToken = '<?php echo $token; ?>',
        formError    = '<?php if (isset($form_error)) { echo $form_error; } ?>';
</script>

<?php if ($sent) { ?>
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <i class="fa fa-warning sign"></i><?php echo Sumo\Language::getVar('SUMO_WARNING_INVOICE_SENT'); ?>
 </div>
<?php } ?>

<form method="post" class="form-horizontal">
    <div class="block-flat">
        <div class="row">
            <div class="col-md-4">
                <dl>
                    <dt><label for="debtor"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NAME'); ?>:</label></dt>
                    <dd>
                        <input type="text" name="customer" id="customer" class="form-control" data-selected-option="<?php echo $customer; ?>" value="<?php echo $customer; ?>" />
                        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" />
                    </dd>
                </dl>   
            </div>

            <div class="col-md-offset-2 col-md-6">
                <div class="well well-sm">
                    <dl class="dl-horizontal">
                        <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NO'); ?>:</strong></dt>
                        <dd id="customer_no"><?php echo $customer_no; ?></dd>
                    </dl>
                    <dl class="dl-horizontal">
                        <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_NO'); ?>:</strong></dt>
                        <dd><?php echo $invoice_no; ?></dd>
                    </dl>
                    <dl class="dl-horizontal">
                        <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?>:</strong></dt>
                        <dd><?php echo $date; ?></dd>
                    </dl>
                    <dl class="dl-horizontal">
                        <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_REFERENCE'); ?>:</strong></dt>
                        <dd>
                            <input type="text" name="reference" class="form-control" style="margin: -8px 0;" value="<?php echo $reference; ?>" />
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <table class="table no-border">
            <thead class="no-border">
                <tr>
                    <th style="width: 60px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY'); ?></strong></th>
                    <th style="width: 100px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_NO'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></strong></th>
                    <th style="width: 80px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_VAT'); ?></strong></th>
                    <th style="width: 130px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_EX'); ?></strong></th>
                    <th style="width: 30px;"></th>
                </tr>
            </thead>
            <tbody id="invoice_lines" class="no-border-y">
                <?php if (sizeof($description)) { ?>
                <?php foreach (array_keys($description) as $line) { ?>
                    <tr>
                        <td><input type="text" name="quantity[]" class="form-control" value="<?php echo $quantity[$line]; ?>" /></td>
                        <td>
                            <input type="text" name="product[]" class="form-control" data-selected-option="<?php echo $product[$line]; ?>" value="<?php echo $product[$line]; ?>" />
                            <input type="hidden" name="product_id[]" value="<?php echo $product_id[$line]; ?>" />
                        </td>
                        <td><input type="text" name="description[]" class="form-control" value="<?php echo $description[$line]; ?>" /></td>
                        <td>
                            <select name="tax_percentage[]" class="form-control">
                                <?php foreach ($tax_percentages as $tp) { ?>
                                <option value="<?php echo $tp; ?>"<?php if ($tp == $tax_percentage[$line]) { ?> selected="selected"<?php } ?>><?php echo $tp; ?>%</option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="text" name="amount[]" class="form-control" value="<?php echo $amount[$line]; ?>" /></td>
                        <td style="padding-top: 16px;"><a href="#" rel="delete"><i style="font-size: 14px;" class="fa fa-trash-o"></i></a></td>
                    </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                    <td><input type="text" name="quantity[]" class="form-control" /></td>
                    <td>
                        <input type="text" name="product[]" class="form-control" />
                        <input type="hidden" name="product_id[]" />
                    </td>
                    <td><input type="text" name="description[]" class="form-control" /></td>
                    <td>
                        <select name="tax_percentage[]" class="form-control">
                            <?php foreach ($tax_percentages as $tp) { ?>
                                <option value="<?php echo $tp; ?>"><?php echo $tp; ?>%</option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input type="text" name="amount[]" class="form-control" /></td>
                    <td style="padding-top: 16px;"><a href="#" rel="delete"><i style="font-size: 14px;" class="fa fa-trash-o"></i></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <p class="pull-left table-padding"><a href="javascript:;" rel="add-line"><i class="fa fa-plus-circle"></i> <?php echo Sumo\Language::getVar('SUMO_BUTTON_ADD_INVOICE_LINE'); ?></a></p>
        <div class="pull-right">
            <table class="table no-border">
                <tbody class="no-border no-border-x no-border-y" id="summary-totals"></tbody>
            </table>
        </div>

        <div class="clearfix"></div>
    </div>

    <p class="align-right">
        <input type="submit" class="btn btn-primary" value="Factuur opslaan" />
    </p>

    <div class="tab-container">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#extra" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA_OPTIONS'); ?></a></li>
            <li><a href="#notes" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?></a></li>
            <li><a href="#discount" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNTS'); ?></a></li>
        </ul>

        <div class="tab-content">
            <div id="extra" class="tab-pane cont active">
                <div class="row">
                    <div class="col-md-6">
                        <h4><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SEND'); ?></strong></h4>
                        <hr>

                        <div class="form-group">
                            <label for="sent_date" class="col-md-4 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SENT_DATE'); ?>:</label>
                            <div class="col-md-8">
                                <input type="text" name="sent_date" id="sent_date" class="form-control date-picker" value="<?php echo $sent_date; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="template" class="col-md-4 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_TEMPLATE'); ?>:</label>
                            <div class="col-md-4">
                                <select name="template" id="template" class="form-control">
                                    <option<?php if ($template == 'invoice') { echo ' selected="selected"'; } ?> value="invoice"><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE'); ?></option>
                                    <option<?php if ($template == 'credit') { echo ' selected="selected"'; } ?> value="credit"><?php echo Sumo\Language::getVar('SUMO_NOUN_CREDIT'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shipping_amount" class="col-md-4 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_FEE'); ?>:</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">&euro;</span>
                                    <input type="text" name="shipping_amount" class="form-control" id="shipping_amount" value="<?php echo $shipping_amount; ?>" />
                                    <input type="hidden" name="shipping_tax" id="shipping_tax" value="21" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT'); ?></strong></h4>
                        <hr>

                        <div class="form-group">
                            <label for="term" class="col-md-4 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_TERM'); ?>:</label>
                            <div class="col-md-4 input-group" style="margin-bottom: 0;">
                                <input type="text" name="term" id="term" class="form-control" value="<?php echo $term; ?>" />
                                <span class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_DAYS'); ?></small></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="auto" class="col-md-4 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AUTO_PAYMENT'); ?>:</label>
                            <div class="col-md-3">
                                <select name="auto" id="auto" class="form-control">
                                    <option<?php if ($auto)  { echo ' selected="selected"'; } ?> value="1"><?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?></option>
                                    <option<?php if (!$auto) { echo ' selected="selected"'; } ?> value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payment_amount" class="col-md-4 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TRANSACTION_FEE'); ?>:</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">&euro;</span>
                                    <input type="text" name="payment_amount" id="payment_amount" class="form-control" value="<?php echo $payment_amount; ?>" />
                                    <input type="hidden" name="payment_tax" id="payment_tax" value="21" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="notes" class="tab-pane cont">
                <dl>
                    <dt><label for="note"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?>:</label></dt>
                    <dd><textarea name="notes" id="note" class="form-control" cols="*" rows="6"><?php echo $notes; ?></textarea></dd>
                </dl>
            </div>

            <div id="discount" class="tab-pane cont">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="discount" class="control-label col-md-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT'); ?>:</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="discount[type]" value="F" <?php if (isset($discount['type']) && $discount['type'] == 'F') { echo 'checked'; } ?>> &euro;
                                    </span>
                                    <input type="text" name="discount[discount]" id="discount_value" value="<?php if (isset($discount['discount'])) { echo $discount['discount']; } ?>" class="form-control">
                                    <span class="input-group-addon">
                                        <input type="radio" name="discount[type]" value="P" <?php if (isset($discount['type']) && $discount['type'] == 'P' || !isset($discount['type'])) { echo 'checked'; } ?>> %
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="points" class="control-label col-md-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_POINTS'); ?>:</label>
                            <div class="col-md-4">
                                <input type="text" name="discount[points]" id="points" class="form-control" value="<?php if (isset($discount['points'])) {echo $discount['points']; } ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="discount" class="control-label col-md-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_COUPON'); ?>:</label>
                            <div class="col-md-4">
                                <?php if (isset($discount['coupon'])) { ?>
                                <input type="text" id="coupon_code" name="discount[coupon][code]" value="<?php echo $discount['coupon']['code']; ?>" class="form-control">
                                <input type="hidden" name="discount[coupon][coupon_id]" value="<?php echo $discount['coupon']['coupon_id']; ?>">
                                <input type="hidden" name="discount[coupon][type]" value="<?php echo $discount['coupon']['type']; ?>">
                                <input type="hidden" name="discount[coupon][value]" value="<?php echo $discount['coupon']['value']; ?>">
                                <?php } else { ?>
                                <input type="text" id="coupon_code" name="discount[coupon][code]" class="form-control">
                                <input type="hidden" name="discount[coupon][coupon_id]">
                                <input type="hidden" name="discount[coupon][type]">
                                <input type="hidden" name="discount[coupon][value]">
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo $footer; ?>