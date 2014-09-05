<?php echo $header; ?>


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
        <h1><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>
        
        <?php if (isset($order_id)) { ?>
        <div class="row">
            <div class="col-md-3">
                <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_ADDRESS'); ?></h4>
                <p><?php echo nl2br($payment_address); ?></p>
            </div>

            <div class="col-md-3">
                <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_ADDRESS'); ?></h4>
                <p><?php echo nl2br($shipping_address); ?></p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO'); ?>:</dt>
                    <dd><?php echo $order_id; ?></dd>
                </dl>

                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_NO'); ?>:</dt>
                    <dd><?php echo $invoice_no; ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_DATE'); ?>:</dt>
                    <dd><?php echo $order_date; ?></dd>
                </dl>
            </div>
        </div>

        <table class="table" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th style="width: 65px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></th>
                    <th style="width: 75px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?></th>
                    <th class="text-right" style="width: 75px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></th>
                    <th class="text-right" style="width: 75px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></th>
                    <th style="width: 30px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) { ?>
                <tr>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['model']; ?></td>
                    <td class="text-right"><?php echo $product['price']; ?></td>
                    <td class="text-right"><?php echo $product['total']; ?></td>
                    <td><a href="<?php echo $product['return']; ?>"><i class="glyphicon glyphicon-share-alt icn-visible"></i></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <div class="content">
                    <dl class="info">
                        <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_BY'); ?>:</dt>
                        <dd><?php echo $payment_method; ?></dd>
                    </dl>

                    <dl class="info">
                        <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIPPING_METHOD'); ?>:</dt>
                        <dd><?php echo $shipping_method; ?></dd>
                    </dl>
                </div>
            </div>

            <div class="col-md-6">
                <table class="table order-summary pull-right">
                    <?php foreach ($totals as $total) { ?>
                    <tr>
                        <th><?php echo $total['label']; ?>:</th>
                        <td class="text-right" style="width: 75px; padding-right: 38px;"><?php echo $total['value_hr']; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        
        <?php if ($histories) { ?>        
        <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_HISTORY'); ?></h4>

        <ul class="timeline">
            <?php foreach ($histories as $history) { ?>
            <li>
                <dl>
                    <dt><strong><?php echo $history['date_added']; ?></strong> <?php if ($history['status']) { echo $history['status']; } ?></dt>
                    <dd><?php echo $history['comment']; ?></dd>
                </dl>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </div>

    <?php } else { ?>
    <div class="alert alert-danger">
        <p><?php echo Sumo\Language::getVar('SUMO_ERROR_NO_ORDER'); ?></p>
    </div>
    <?php } ?>
</div>

<?php echo $footer; ?>