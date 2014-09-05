<?php echo $header; ?>

<script type="text/javascript">
    sessionToken = '<?php echo $token; ?>';
    formError    = '<?php echo $error_warning; ?>';
</script>

<form method="post" action="<?php echo $action?>">
    <div class="block-flat">
        <div class="row" style="margin-top: 0;">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label" for="order_id"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO'); ?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon">OID</span>
                        <input type="text" name="order_id" id="order_id" value="<?php echo $order_id?>" class="form-control">
                        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" />
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_DATE'); ?>:</label>
                    <input type="text" name="date_ordered" id="date" value="<?php echo $date_ordered?>" class="date-picker form-control">
                </div>
            </div>
        </div>
        
        <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                    <input type="text" name="firstname" id="firstname" value="<?php echo $firstname?>" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LAST_NAME'); ?>:</label>
                    <input type="text" name="lastname" id="lastname" value="<?php echo $lastname?>" class="form-control">
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>:</label>
                    <input type="text" name="email" id="email" value="<?php echo $email?>" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE'); ?>:</label>
                    <input type="text" name="telephone" id="telephone" value="<?php echo $telephone?>" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="block-flat">
        <div class="row" style="margin-top: 0;">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="product_id" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?>:</label>
                    <select name="product_id" id="product_id" class="form-control">
                        <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT'); ?></option>
                        <?php foreach ($products as $prod) { ?>
                        <option<?php if ($prod['product_id'] == $product_id) { ?> selected="selected"<?php } ?> value="<?php echo $prod['product_id']; ?>" data-model="<?php echo $prod['model']; ?>" data-quantity="<?php echo $prod['quantity']; ?>"><?php echo $prod['name']; ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="product" id="product" value="<?php echo $product; ?>" />
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?>:</label>
                    <input type="text" name="model" id="model" value="<?php echo $model?>" class="form-control" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY'); ?>:</label>
                    <input type="text" name="quantity" id="quantity" value="<?php echo $quantity?>" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_REASON'); ?>:</label>
                    <select name="return_reason_id" class="form-control">
                        <?php foreach ($return_reasons as $return_reason) { ?>
                        <?php if ($return_reason['return_reason_id'] == $return_reason_id) { ?>
                        <option value="<?php echo $return_reason['return_reason_id']; ?>" selected="selected"><?php echo $return_reason['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $return_reason['return_reason_id']; ?>"><?php echo $return_reason['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_OPENED')); ?>:</label>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="opened" value="1" <?php if ($opened) { echo 'checked="checked"'; }?>>
                            <?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_OPENED')); ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="opened" value="0" <?php if (!$opened) { echo 'checked="checked"'; }?>>
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_UNOPENED'); ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ACTION'); ?>:</label>
                    <select name="return_action_id" class="form-control">
                        <option value="0"></option>
                        <?php foreach ($return_actions as $return_action) { ?>
                        <?php if ($return_action['return_action_id'] == $return_action_id) { ?>
                        <option value="<?php echo $return_action['return_action_id']; ?>" selected="selected"> <?php echo $return_action['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $return_action['return_action_id']; ?>"><?php echo $return_action['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
                    <select name="return_status_id" class="form-control">
                        <?php foreach ($return_statuses as $return_status) { ?>
                        <?php if ($return_status['return_status_id'] == $return_status_id) { ?>
                        <option value="<?php echo $return_status['return_status_id']; ?>" selected="selected"><?php echo $return_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $return_status['return_status_id']; ?>"><?php echo $return_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENT'); ?>:</label>
            <textarea name="comment" rows="5" class="form-control"><?php echo $comment?></textarea>
        </div>
    </div>

    <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE_RETURN'); ?>" />
    <a class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL'); ?></a>
</form>

<?php echo $footer?>