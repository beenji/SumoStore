<?php echo $header; ?>

<script type="text/javascript">
    var sessionToken = '<?php echo $token; ?>';
</script>

<div class="page-head-actions align-right">
    <a class="btn btn-primary" href="<?php echo $print; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PRINT_INVOICE'); ?></a>
    <a class="btn btn-primary" href="<?php echo $send; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SEND_INVOICE'); ?></a>
</div>

<div class="row">
    <div class="col-md-8 col-lg-9">
        <div class="block-flat">
            <div class="row" style="margin-top: 0;">
                <div class="col-md-4">
                    <dl>
                        <dt><label for="debtor"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NAME'); ?>:</label></dt>
                        <dd>
                            <?php echo $customer; ?><br />
                            <?php echo $customer_address; ?><br />
                            <?php echo $customer_postcode; ?> <?php echo $customer_city; ?><br />
                            <?php echo $customer_country; ?>
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
                            <dd><?php echo $reference; ?></dd>
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
                    </tr>
                </thead>
                <tbody id="invoice_lines" class="no-border-y">
                    <?php foreach (array_keys($description) as $line) { ?>
                        <tr>
                            <td><?php echo $quantity[$line]; ?></td>
                            <td><?php echo $product[$line]; ?></td>
                            <td><?php echo $description[$line]; ?></td>
                            <td>21%</td>
                            <td style="width: 130px;" class="right"><?php echo $amount[$line]; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pull-right">
                <table class="table no-border">
                    <tbody class="no-border no-border-x no-border-y">
                        <?php foreach ($totals as $i => $total) { ?>
                        <tr style="background-color: #fff;">
                            <?php if ($i == 0 || $i == (sizeof($totals) - 1)) { ?>
                            <td class="right" style="border-top: 2px solid #ddd;"><strong><?php echo $total['label']; ?>:</strong></td>
                            <td class="right" style="border-top: 2px solid #ddd; width: 130px;"><strong><?php echo $total['value_hr']; ?></strong></td>
                            <?php } else { ?>
                            <td class="right" style="border-top: none;"><?php echo $total['label']; ?>:</td>
                            <td class="right" style="border-top: none; width: 130px;"><?php echo $total['value_hr']; ?></td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>

    <div class="col-md-4 col-lg-3">
        <div class="block-flat align-center">
            <form action="<?php echo $process_payment; ?>" id="paymentForm" style="display: none;">
                <div class="form-group">
                    <div class="input-group">
                        <label for="amount" class="input-group-addon control-label"><small>&euro;</small></label>
                        <input type="text" id="amount" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_SIMPLE'); ?>" name="amount" class="form-control" />
                        <label for="amount" class="input-group-addon control-label"><small id="amountOpen"><?php echo $total_open; ?></small></label>
                    </div>
                </div>

                <hr>

                <input type="hidden" name="invoiceID" value="<?php echo $invoice_id; ?>" />
            </form>

            <a href="javascript:;" id="processPayment" class="btn btn-primary"><i class="fa fa-eur"></i> <?php echo Sumo\Language::getVar('SUMO_BUTTON_PROCESS_PAYMENT'); ?></a>
        </div>

        <div class="block-flat">
            <dl>
                <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_PAY_DATE'); ?></dt>
                <dd><?php echo $pay_date; ?></dd>
            </dl>

            <dl>
                <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_PROPERTIES'); ?></dt>
                <?php if ($sent) { ?>
                <dd><?php echo $sent; ?>x <?php echo Sumo\Language::getVar('SUMO_NOUN_SENT'); ?></dd>
                <?php } else { ?>
                <dd><?php echo Sumo\Language::getVar('SUMO_NOUN_NOT_SENT'); ?></dd>
                <?php } ?>
            </dl>

            <dl>
                <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_SEND'); ?></dt>
                <dd><?php echo $customer_email; ?></dd>
            </dl>
        </div>
    </div>
</div>

<div class="tab-container">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#extra" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA_OPTIONS'); ?></a></li>
        <li><a href="#notes" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div id="extra" class="tab-pane cont active">
            <div class="row">
                <div class="col-md-6">
                    <h4><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SEND'); ?></strong></h4>
                    <hr>

                    <dl class="dl-horizontal">
                        <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_SENT_DATE'); ?>:</dt>
                        <dd><?php echo $sent_date; ?></dd>
                    </dl>

                    <dl class="dl-horizontal">
                        <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_TEMPLATE'); ?>:</dt>
                        <dd>
                            <?php if ($template == 'invoice') { echo Sumo\Language::getVar('SUMO_NOUN_INVOICE'); } ?>
                            <?php if ($template == 'credit') { echo Sumo\Language::getVar('SUMO_NOUN_CREDIT'); } ?>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h4><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT'); ?></strong></h4>
                    <hr>

                    <dl class="dl-horizontal">
                        <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_PAYMENT_TERM'); ?>:</dt>
                        <dd>
                            <?php echo $term; ?> <?php echo Sumo\Language::getVar('SUMO_NOUN_DAYS'); ?>
                        </dd>
                    </dl>

                    <dl class="dl-horizontal">
                        <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_AUTO_PAYMENT'); ?>:</dt>
                        <dd>
                            <?php if ($auto)  { echo Sumo\Language::getVar('SUMO_NOUN_YES'); } ?>
                            <?php if (!$auto) { echo Sumo\Language::getVar('SUMO_NOUN_NO'); } ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div id="notes" class="tab-pane cont">
            <dl>
                <dt><label for="note"><?php echo Sumo\Language::getVar('SUMO_NOUN_NOTES'); ?>:</label></dt>
                <dd><?php if ($notes) { echo $notes; } else { echo Sumo\Language::getVar('SUMO_NOUN_NO_NOTES'); } ?></dd>
            </dl>
        </div>
    </div>
</div>

<?php echo $footer; ?>
