<?php
$taxes = $totals = array();
?>
<div class="col-md-12">
    <table class="table table-list table-striped">
        <thead class="no-border">
            <tr>
                <th>&nbsp;</th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_SINGULAR') ?></th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL') ?></th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY_SINGULAR') ?></th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_SINGULAR') ?></th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_SINGULAR') ?></th>
            </tr>
        </thead>
        <tbody class="no-border">
            <?php $this->load->model('tool/image');
            $products = $this->cart->getProducts();

            foreach ($products as $list):
                $list['tax_percentage'] = round($list['tax_percentage']);
                if (!isset($taxes[$list['tax_percentage']])) {
                    $taxes[$list['tax_percentage']] = 0;
                }
                $productTotal = round($list['price'] * (1 + ($list['tax_percentage'] / 100)), 2) * $list['quantity'];
                $productTaxAmount = $productTotal / (1 + ($list['tax_percentage'] / 100));
                $taxes[$list['tax_percentage']] = $productTotal - $productTaxAmount;
            ?>
            <tr>
                <td><?php if (!empty($list['image'])): ?><img src="<?php echo $this->model_tool_image->resize($list['image'], 60, 60)?>"><?php endif; ?></td>
                <td>
                    <?php echo $list['name']?>
                    <?php if (count($list['options_data'])): foreach ($list['options_data'] as $data): foreach ($data['options'] as $option):?>
                    <br /><small><?php echo $data['name'] . ': ' . $option['name']?></small>
                    <?php endforeach; endforeach; endif?>
                </td>
                <td><?php echo $list['model']?></td>
                <td><?php echo $list['quantity']?></td>
                <td>
                    <?php if ($this->config->get('tax_enabled')) {
                        $price = round($list['price'] + ($list['price'] / 100 * $list['tax_percentage']), 2);
                        echo Sumo\Formatter::currency($price);
                    }
                    else {
                        $price = $list['price'];
                        echo Sumo\Formatter::currency($price);
                    }?>
                </td>
                <td>
                    <?php
                    $total = $price * $list['quantity'];
                    $total = round($list['price'] * (1 + ($list['tax_percentage'] / 100)) * $list['quantity']);
                    echo Sumo\Formatter::currency($productTotal);?>
                </td>
            </tr>

            <?php endforeach;
            $subtotal = $this->cart->getTotal();
            $subTotalOnePercent = $subtotal / 100;
            $totals[] = array(
                'label' => 'SUMO_NOUN_OT_SUBTOTAL',
                'value' => $this->cart->getTotal()
            );
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">&nbsp;</td>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_SUBTOTAL')?></th>
                <th><?php echo Sumo\Formatter::currency($subtotal)?></th>
            </tr>
            <?php if ($this->config->get('points_value') && !empty($this->session->data['discount']['reward']) && $this->session->data['discount']['reward']):
            $pointsValue = $this->session->data['discount']['reward'] * $this->config->get('points_value');
            $pointsPercentage = $pointsValue / $subTotalOnePercent;
            ?>
            <tr>
                <td colspan="4" class="text-right"><small><?php echo $this->session->data['discount']['reward']?></small></td>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_POINTS')?></th>
                <th>- <?php echo Sumo\Formatter::currency($pointsValue)?></th>
            </tr>
            <?php
            $totals[] = array(
                'label'         => 'SUMO_NOUN_OT_POINTS',
                'label_inject'  => $this->session->data['discount']['reward'],
                'value'         => '-' . $pointsValue
            );
            foreach ($taxes as $perc => $amount) {
                $taxes[$perc] = ($amount * (1 - ($pointsPercentage / 100)));
            }
            $subtotal -= $pointsValue;

            endif;
            if (!empty($this->session->data['discount']['coupon']) && !empty($this->session->data['discount']['coupon']['discount'])): $coupon = $this->session->data['discount']['coupon'];?>
            <tr>
                <th colspan="4">&nbsp;</th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_DISCOUNT')?> <?php if ($coupon['type'] == 'P') { echo '(' . round($coupon['discount']) . '%)'; }?></th>
                <th>-
                    <?php

                    if ($coupon['type'] == 'P') {
                        $discount = $subTotalOnePercent * $coupon['discount'];
                        //discountValue = subTotalOnePercent * discountPercentage;
                        $percentage = $coupon['discount'];
                    }
                    else {
                        $discount = $coupon['discount'];
                        //discountPercentage = parseFloat(discount / subTotalOnePercent);
                        $percentage = $coupon['discount'] / $subTotalOnePercent;
                    }
                    echo Sumo\Formatter::currency($discount);
                    $totals[] = array(
                        'label'         => 'SUMO_NOUN_OT_DISCOUNT',
                        'label_inject'  => round($percentage),
                        'value'         => $discount
                    );
                    ?>
                </th>
            </tr>
            <?php
            foreach ($taxes as $perc => $amount) {
                $taxes[$perc] = ($amount * (1 - ($percentage / 100)));
            }
            $subtotal -= $discount;

            endif;
            if (!empty($this->session->data['discount']['voucher']) && !empty($this->session->data['discount']['voucher']['amount'])): ?>
            <tr>
                <th colspan="4">&nbsp;</th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_VOUCHER')?></th>
                <th>- <?php echo Sumo\Formatter::currency($this->session->data['discount']['voucher']['amount'])?></th>
            </tr>
            <?php
            $percentage = $this->session->data['discount']['voucher']['amount'] / $subTotalOnePercent;
            foreach ($taxes as $perc => $amount) {
                $taxes[$perc] = ($amount * (1 - ($percentage / 100)));
            }
            $subtotal -= $this->session->data['discount']['voucher']['amount'];

            endif;

            if (!empty($this->session->data['shipping_method']['option'])):
            $shipping = $this->session->data['shipping_method']['app'];
            $shoption = $this->session->data['shipping_method']['option'];
            if (!empty($shipping['options'][$shoption]['price'])) {
                $price = $shipping['options'][$shoption]['price'];
            }
            else {
                $price = 0;
            }
            if (empty($shipping['options'][$shoption]['name'])) {
                $shipping['options'][$shoption]['name'] = $shipping['name'];
            }
            if ($price && $shipping['options'][$shoption]['tax']) {
                $priceTax = ($price / 100) * $shipping['options'][$shoption]['tax'];
                $taxes[$shipping['options'][$shoption]['tax']] += $priceTax;
                $price += $priceTax;
            }
            $subtotal += $price;
            endif;

            if (!empty($this->session->data['payment_method']['option'])):
            $payment = $this->session->data['payment_method']['app'];
            $option = $this->session->data['payment_method']['option'];

            /*echo '<pre>'.print_r($payment, true).'</pre>';
            echo '<pre>'.print_r($option, true).'</pre>';
            exit;*/

            if (!empty($payment['options'][$option]['price'])) {
                if (!isset($payment['options'][$option]['rate_type']) || strtolower($payment['options'][$option]['rate_type']) == 'f') {
                    $payPrice = $payment['options'][$option]['price'];
                }
                else {
                    $payPrice = $subtotal / (1 - ($payment['options'][$option]['price'] / 100)) - $subtotal;
                }
            }
            else {
                $payPrice = 0;
            }

            if (empty($payment['options'][$option]['name'])) {
                $payment['options'][$option]['name'] = $payment['name'];
            }
            else {
                $payment['options'][$option]['name'] .= ' (via ' . $payment['name'] . ')';
            }

            // Payment price has a tax-percentage applied? Calculate the amount of tax
            // and add it to the desired tax-group. Warning! Payment method price is 
            // always including tax.
            if ($payPrice && $payment['options'][$option]['tax']) {
                $payPriceTax = $payPrice - ($payPrice / (1 + ($payment['options'][$option]['tax'] / 100)));

                $taxes[$payment['options'][$option]['tax']] += $payPriceTax;
            }
            
            $subtotal += $payPrice;
            endif;

            if ($this->config->get('tax_display')):
                foreach ($taxes as $type => $amount): ?>
            <tr>
                <th colspan="4">&nbsp;</th>
                <th><small><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_TAX') . ' (' . $type . '%)'?></small></th>
                <th><small><?php echo Sumo\Formatter::currency($amount)?></small></th>
            </tr>
            <?php
                $totals[] = array(
                    'label'         => 'SUMO_NOUN_OT_TAX',
                    'label_inject'  => round($type),
                    'value'         => $amount
                );
                endforeach;
            endif;

            if (!empty($shipping) && !empty($shoption)): ?>
            <tr>
                <td colspan="4" class="text-right"><small><?php echo $shipping['options'][$shoption]['name']?></small></td>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_SHIPPING')?></th>
                <th><?php echo Sumo\Formatter::currency($price)?></th>
            </tr>
            <?php
                $totals[] = array(
                    'label'         => 'SUMO_NOUN_OT_SHIPPING',
                    'value'         => $price
                );
                endif;
            if (!empty($payment) && !empty($option)): ?>
            <tr>
                <td colspan="4" class="text-right"><small><?php echo $payment['options'][$option]['name']?></small></td>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_PAYMENT')?></th>
                <th><?php echo Sumo\Formatter::currency($payPrice)?></th>
            </tr>
            <?php
                $totals[] = array(
                    'label'         => 'SUMO_NOUN_OT_PAYMENT',
                    'value'         => $payPrice
                );
                endif; ?>
            <tr>
                <th colspan="4">&nbsp;</th>
                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_TOTAL')?></th>
                <th><?php echo Sumo\Formatter::currency($subtotal)?></th>
            </tr>
        </tfoot>
    </table>

    <?php
    $totals[] = array(
        'label'         => 'SUMO_NOUN_OT_TOTAL',
        'value'         => $subtotal
    );
    $this->session->data['totals'] = $totals;
    $this->session->data['total_amount'] = $subtotal;
    ?>

    <div class="row">
        <div class="col-sm-6">
            <div class="content">
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_BY')?>:</dt>
                    <dd><?php echo $payment['options'][$option]['name']?></dd>
                </dl>

                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_METHOD')?>:</dt>
                    <dd><?php echo $shipping['options'][$shoption]['name']?></dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <?php echo $this->getChild('app/' . $this->session->data['payment_method']['app']['list_name'] . '/checkout/checkout')?>
        </div>

        <div class="col-sm-12 text-right">
            <div class="form-group">
                <label class="radio-inline"><input type="checkbox" id="agree" value="1"> <?php echo Sumo\Language::getVar('SUMO_PAYMENT_AGREE', array($this->url->link('information/information/info', 'information_id=' . $this->config->get('customer_policy_id'), 'SSL')))?></label>
                <label class="radio-inline"><input type="submit" class="btn btn-primary" id="continue" value="<?php echo Sumo\Language::getVar('SUMO_CHECKOUT_STEP_CONFIRM')?>"></label>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('#continue').on('click', function(e) {
        e.preventDefault();
        if ($('#agree').is(':checked')) {
            startLoader();
            $('#agree').parent().parent().removeClass('has-error');
            $('#continue').prop('disabled', 1).addClass('disabled');
            disableHeaders();
            $.post('checkout/checkout/confirm', {agree: true}, function(data) {
                if (data.success) {
                    window.location = data.location;
                }
                else {
                    $('#continue').prop('disabled', 0).removeClass('disabled');
                    alert(data.message);
                    enableHeaders();
                }
            }, 'json');
        }
        else {
            $('#agree').parent().parent().addClass('has-error');
            alert('<?php echo Sumo\Language::getVar('SUMO_CHECKOUT_STEP_CONFIRM_CHECKED')?>');
        }
    })
    $('#agree').parent().find('a').on('click', function(e) {
        $(this).attr('target', '_blank');
    })
})

function disableHeaders() {
    $('#checkout-holder > div').each(function() {
        $(this).find('.header').find('span').addClass('disabled');
    })
}
function enableHeaders() {
    $('#checkout-holder > div').each(function() {
        $(this).find('.header').find('span').removeClass('disabled');
    })
}
</script>
