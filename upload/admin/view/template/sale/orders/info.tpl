<?php echo $header; ?>
<script type="text/javascript">
    var paymentAddress  = "<?php echo htmlentities($order['customer']['payment_address']['address_1'] . ',' . $order['customer']['payment_address']['city'] . ',' . $order['customer']['payment_address']['country']) ?>",
        shippingAddress = "<?php echo htmlentities($order['customer']['shipping_address']['address_1'] . ',' . $order['customer']['shipping_address']['city'] . ',' . $order['customer']['shipping_address']['country']) ?>",
        sessionToken    = "<?php echo $this->session->data['token']; ?>";

    // Some texts
    var textCreditAdd        = '<?php echo Sumo\Language::getVar('SUMO_NOUN_CREDIT_ADD'); ?>',
        textCreditRemove     = '<?php echo Sumo\Language::getVar('SUMO_NOUN_CREDIT_REMOVE'); ?>',
        textRewardAdd        = '<?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD_ADD'); ?>',
        textRewardRemove     = '<?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD_REMOVE'); ?>',
        textCommissionAdd    = '<?php echo Sumo\Language::getVar('SUMO_NOUN_COMMISSION_ADD'); ?>',
        textCommissionRemove = '<?php echo Sumo\Language::getVar('SUMO_NOUN_COMMISSION_REMOVE'); ?>';
</script>

<div class="page-head-actions align-right">
    <?php if (!isset($order['invoice_no'])) { ?>
    <a href="<?php echo $url_invoice ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_GENERATE_INVOICE'); ?></a>
    <a href="<?php echo $this->url->link('sale/orders/edit', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'], 'SSL') ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT_ORDER') ?></a>
    <?php } else { ?>
    <a href="<?php echo $invoice; ?>" target="_blank" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PRINT_INVOICE'); ?></a>
    <?php } ?>

    <a href="<?php echo $this->url->link('sale/orders') ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
</div>

<div class="row">
    <div class="col-md-8">

        <div class="block-flat">
            <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?></strong></h5>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="no-border no-striping">
                        <tbody class="no-border-x no-border-y no-padding-left">
                            <tr>
                                <td valign="top"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO'); ?>:</strong></td>
                                <td valign="top"><?php echo str_pad($order['order_id'], 6, 0, STR_PAD_LEFT);?></td>
                                <td valign="top"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STORE'); ?>:</strong></td>
                                <td valign="top"><a href="<?php echo $order['store']['url']; ?>"><?php echo empty($order['store']['name']) ? '&mdash;' : $order['store']['name']; ?></a></td>
                            </tr>
                            <tr>
                                <td valign="top"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_NO'); ?>:</td>
                                <td valign="top" id="invoice"><?php if (isset($order['invoice_no'])) { echo $order['invoice_no']; } else { ?><a href="<?php echo $url_invoice; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_GENERATE_INVOICE'); ?></a><?php } ?></td>
                                <td valign="top"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_DATE'); ?>:</strong></td>
                                <td valign="top"><?php echo Sumo\Formatter::date($order['order_date']); ?></td>
                            </tr>
                            <?php if (!empty($order['status'])) { ?>
                            <tr>
                                <td valign="top"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_STATUS')?>:</strong></td>
                                <td valign="top"><span id="order_status"><?php echo $order['status']?></span></td>
                            </tr>
                            <?php } ?>
                            <?php if (isset($order['reward']) && isset($order['customer']) && $order['reward'] && $order['customer']) { ?>
                            <tr>
                                <td><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD'); ?>:</strong></td>
                                <td><?php echo $reward; ?>
                                    <?php if (!$reward_total) { ?>
                                    <span id="reward"><b>[</b> <a id="reward-add"><?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD_ADD'); ?></a> <b>]</b></span>
                                    <?php } else { ?>
                                    <span id="reward"><b>[</b> <a id="reward-remove"><?php echo Sumo\Language::getVar('SUMO_NOUN_REWARD_REMOVE'); ?></a> <b>]</b></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <hr />
                    <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?></strong></h5>
                    <form action="" method="post" id="add-note">
                        <input type="hidden" id="order-id" value="<?php echo $order['order_id']; ?>" />
                        <textarea name="comment" id="comment" cols="*" class="form-control" rows="3"></textarea>
                        <div class="textarea-footer">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="status" id="order_status_id" class="form-control">
                                        <?php foreach ($order_statuses as $list) { ?>
                                        <option value="<?php echo $list['order_status_id']; ?>"<?php if ($list['order_status_id'] == $order['order_status']) { echo ' selected="selected"'; } ?>><?php echo $list['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="notify" value="1" />
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_NOTIFY_CUSTOMER'); ?>
                                    </label>
                                </div>

                                <div class="col-md-4 align-right">
                                    <input type="submit" class="btn btn-primary btn-sm" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_NOTE'); ?>" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="block-flat">
            <table class="table no-border hover table-invoice">
                <thead class="no-border">
                    <tr>
                        <th style="width: 70px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY'); ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></strong></th>
                        <th style="width: 100px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?></strong></th>
                        <th style="width: 100px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></strong></th>
                        <th style="width: 100px;" class="right"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></strong></th>
                    </tr>
                </thead>
                <tbody class="no-border-y">
                    <?php foreach ($order['lines'] as $list) { ?>
                    <tr>
                        <td><?php echo $list['quantity']; ?></td>
                        <td>
                            <a href="<?php echo $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $list['product_id'], 'SSL') ?>"><strong><?php echo $list['name']; ?></strong></a>
                            <?php if (!empty($product['option'])) { ?>
                            <ul class="product-options">
                                <?php foreach ($list['option'] as $option) { ?>
                                    <?php if ($option['type'] != 'file') { ?>
                                    <li><?php echo $option['name']; ?>: <?php echo $option['value']; ?></li>
                                    <?php } else { ?>
                                    <li><?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </td>
                        <td><?php echo isset($list['model_2']) ? $list['model_2'] : $list['model']; ?></td>
                        <td class="right"><?php echo Sumo\Formatter::currency($list['price'] * (1 + ($list['tax_percentage'] / 100))); ?></td>
                        <td class="right"><?php echo Sumo\Formatter::currency(round($list['price'] * (1 + ($list['tax_percentage'] / 100))) * $list['quantity']); ?></td>
                    </tr>
                    <?php }
                    if (isset($vouchers)) { foreach ($vouchers as $voucher) { ?>
                    <tr>
                        <td><a href="<?php echo $voucher['href']; ?>"><?php echo $voucher['description']; ?></a></td>
                        <td></td>
                        <td>1</td>
                        <td class="right"><?php echo $voucher['amount']; ?></td>
                        <td class="right"><?php echo $voucher['amount']; ?></td>
                    </tr>
                    <?php }
                    }
                    ?>
                </tbody>
            </table>

            <hr>

            <div class="pull-left">
                <p class="well well-sm">
                    <strong style="width: 120px; display: inline-block;"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_BY'); ?>:</strong>
                    <?php if ($order['payment']['list_name']) { ?>
                    <a href="<?php echo $this->url->link('app/' . $order['payment']['list_name'], '', 'SSL') ?>"><?php echo $order['payment']['name']?></a>
                    <?php } else { ?>
                    <?php echo $order['payment']['name']?>
                    <?php } ?>
                    <br />
                    <?php if ($order['shipping']['list_name']) { ?>
                    <strong style="width: 120px; display: inline-block;"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_METHOD'); ?>:</strong> <a href="<?php echo $this->url->link('app/' . $order['shipping']['list_name'], '', 'SSL') ?>"><?php echo $order['shipping']['name']?></a>
                    <?php } else { ?>
                    <?php echo $order['shipping']['name']?>
                    <?php } ?>
                </p>
            </div>

            <div class="pull-right">
                <table class="table no-border hover table-invoice">
                    <tfoot>
                        <?php foreach ($order['totals'] as $total) { ?>
                        <tr class="totals">
                            <th><?php echo $total['label']?>:</th>
                            <td class="right"><strong><?php echo Sumo\Formatter::currency($total['value'])?></strong></td>
                        </tr>
                        <?php } ?>
                    </tfoot>
                </table>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="block-flat">
            <div class="header">
                <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_ADDRESS'); ?></strong></h5>
            </div>
            <div class="content">
                <address style="line-height: 24px;" id="payment_address">
                    <?php
                    if (!empty($order['customer']['gender'])) {
                        echo '<i class="fa ';
                        if ($order['customer']['gender'] == 'f') {
                            echo 'fa-female';
                        }
                        else {
                            echo 'fa-male';
                        }
                        echo '"></i> ';
                    }
                    echo nl2br($payment_address);
                    ?>
                </address>
                <div class="" style="line-height: 24px;">
                    <?php if (!empty($order['customer']['telephone'])): ?>
                    <a href="javascript:return void();"><i class="fa fa-phone"></i>&nbsp; <?php echo $order['customer']['telephone']?></a><br />
                    <?php endif; if(!empty($order['customer']['mobile'])): ?>
                    <a href="javascript:return void();"><i class="fa fa-phone-square"></i>&nbsp; <?php echo $order['customer']['mobile']?></a><br />
                    <?php endif; if (!empty($order['customer']['email'])): ?>
                    <a href="mailto:<?php echo $order['customer']['email']?>">
                        <i class="fa fa-envelope"></i>&nbsp; <?php echo $order['customer']['email']?>
                    </a><br />
                    <?php endif; if (!empty($order['customer']['customer_id'])): ?>
                    <a href="<?php echo $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $order['customer']['customer_id'], 'SSL')?>">
                        <i class="fa fa-user"></i>&nbsp; <?php echo Sumo\Language::getVar('SUMO_ADMIN_VIEW_CUSTOMER')?>
                    </a><br />
                    <?php endif?>
                </div>
                <div class="clearfix"><br /></div>
                <p class="pull-left">
                    <strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_TYPE'); ?>:</strong><br />
                    <?php if (!empty($order['customer']['payment']['company'])) { echo 'Zakelijk'; } else { echo 'Particulier'; } ?>
                </p>
                <?php if (isset($order['customer']['customer_group_name'])) { ?>
                <p class="pull-right">
                    <strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP'); ?>:</strong><br />
                    <?php echo $order['customer']['customer_group_name']; ?>
                </p>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
            <div class="content full-width">
                <div id="invoice_map" style="height: 250px;"></div>
            </div>
        </div>

        <div class="block-flat">
            <div class="header">
                <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_ADDRESS'); ?></strong></h5>
            </div>
            <div class="content">
                <address style="line-height: 24px;">
                    <?php echo nl2br($shipping_address)?>
                </address>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="block-flat">
            <h5><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_HISTORY'); ?></strong></h5>
            <div class="content">
                <ul class="list-history" id="list-history">
                    <?php if (isset($order['history']) && $order['history']) {

                    krsort($order['history']); foreach ($order['history'] as $history) { ?>
                    <li>
                        <p class="history-heading"><strong><?php echo Sumo\Formatter::dateTime($history['history_date']) ?></strong>
                        <?php
                        if (isset($history['notify']) && $history['notify']) {
                            echo ' <i class="fa fa-envelope-o"></i> ';
                        }
                        echo $order_statuses[$history['status_id']]['name'] ?></p>
                        <?php if ($history['comment']) { ?><p><?php echo $history['comment'] ?></p><?php } ?>
                    </li>
                    <?php } }?>
                    <li>
                        <p class="history-heading"><strong><?php echo Sumo\Formatter::dateTime($order['order_date']) ?></strong>
                        <?php
                        echo Sumo\Language::getVar('SUMO_NOUN_ORDER_PLACED') ?></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<?php echo $footer; ?>
