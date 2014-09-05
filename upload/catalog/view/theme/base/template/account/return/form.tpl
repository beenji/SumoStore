<?php echo $header; ?>

<script type="text/javascript">
    var orders = <?php echo $raw_orders; ?>;

    $(function() {
        $('#order_id').on('change', function() {
            var orderID = $(this).val();

            if (orders[orderID] == undefined) {
                $('#product_id').html('<option><?php echo Sumo\Language::getVar('SUMO_NOUN_PICK_ONE'); ?></option>');
                $('#model, #date_ordered').val('');
                $('#quantity').val(1);

                return;
            }

            // Fill products dropdown
            $('#product_id option').remove();

            for (productID in orders[orderID]['products']) {
                var product = orders[orderID]['products'][productID];

                $('#product_id').append('<option value="' + productID + '">' + product.product + '</option>');
            }

            $('#date_ordered').val(orders[orderID]['order_date']);
            $('#product_id').trigger('change');
        }); 

        $('#product_id').on('change', function() {
            var orderID = $('#order_id').val(),
                productID = $(this).val(),
                productInfo = orders[orderID]['products'][productID];

            if (productInfo != undefined) {
                $('#model').val(productInfo.model);
                $('#quantity').val(productInfo.quantity);
                $('#product').val(productInfo.product);
            }
        }).trigger('change');
    });
</script>

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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_RETURN_NEW')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <?php if (isset($done)) { ?>
        <div class="alert alert-success">
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_SUCCESS'); ?></p>
        </div>

        <a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_BACK_TO_OVERVIEW'); ?></a>
        <?php } else { ?>

        <?php if ($error) { ?>
        <div class="alert alert-danger">
            <p><?php echo $error; ?></p>
        </div>
        <?php } ?>
        <form action="" method="post">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="order_id" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO'); ?>:</label>
                        <select name="order_id" id="order_id" class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_PICK_ONE'); ?></option>
                            <?php foreach ($orders as $order) { ?>
                            <option value="<?php echo $order['order_id']; ?>"<?php if ($order['order_id'] == $order_id) { echo ' selected="selected"'; } ?>><?php echo str_pad($order['order_id'], 6, 0, STR_PAD_LEFT); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_ordered" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_DATE'); ?>:</label>
                        <input type="text" class="form-control" name="date_ordered" id="date_ordered" value="<?php echo $date_ordered; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="firstname" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                        <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $firstname; ?>" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="lastname" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LAST_NAME'); ?>:</label>
                        <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $lastname; ?>" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="email" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>:</label>
                        <input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="telephone" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE'); ?>:</label>
                        <input type="text" class="form-control" name="telephone" id="telephone" value="<?php echo $telephone; ?>" />
                    </div>
                </div>
            </div>

            <hr>

            <div class="row" style="margin-top: 0;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="product_id" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?>:</label>
                        <input type="hidden" name="product" id="product" value="<?php echo $product; ?>" />
                        <select name="product_id" id="product_id" class="form-control">
                            <?php if ($products) { ?>
                                <?php foreach ($products as $product) { ?>
                                <option value="<?php echo $product['product_id']; ?>"><?php echo $product['product']; ?></option>
                                <?php } ?>
                            <?php } else { ?>
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_PICK_ONE'); ?></option>
                            <?php } ?> 
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?>:</label>
                        <input type="text" name="model" id="model" value="<?php echo $model; ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY'); ?>:</label>
                        <input type="text" name="quantity" id="quantity" value="<?php echo $quantity; ?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_REASON'); ?>:</label>
                        <select name="return_reason_id" class="form-control">
                            <?php foreach ($return_reasons as $reason) { ?>
                            <option value="<?php echo $reason['return_reason_id']; ?>"><?php echo $reason['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_OPENED')); ?>:</label>
                        <div style="padding-top: 4px;">
                            <label class="radio-inline">
                                <input type="radio" name="opened" value="1"<?php if ($opened) { echo ' checked="checked"'; } ?>> <?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_OPENED')); ?> 
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="opened" value="0"<?php if (!$opened) { echo ' checked="checked"'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_UNOPENED'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENTS'); ?>:</label>
                <textarea name="comment" rows="5" class="form-control"><?php echo $comment; ?></textarea>
            </div>

            <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_CONFIRM'); ?>" />
            <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        </form>
        <?php } ?>
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

<?php echo $footer; ?>