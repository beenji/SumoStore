<?php echo $header; ?>

<div class="page-head-actions align-right">
    <a href="<?php echo $cancel; ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?></a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="block-flat">
            <form action="" method="post" class="form-horizontal">
                <div class="form-group">
                    <label for="status" class="col-md-3 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS') ?>:</label>
                    <div class="col-md-9">
                        <select name="return_status_id" class="form-control">
                        <?php foreach ($return_statuses as $stat) { ?>
                            <?php if ($stat['return_status_id'] == $return_status_id) { ?>
                            <option value="<?php echo $stat['return_status_id']; ?>" selected="selected"><?php echo $stat['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $stat['return_status_id']; ?>"><?php echo $stat['name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notify" class="col-md-3 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_INFORM_CUSTOMER') ?>:</label>
                    <div class="col-md-9">
                        <label class="checkbox">
                            <input type="checkbox" name="notify" id="notify" value="1" />
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_INFORM_CUSTOMER_YES') ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment" class="col-md-3 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENTS') ?>:</label>
                    <div class="col-md-9">
                        <textarea name="comment" cols="*" rows="3" id="comment" class="form-control"></textarea>
                    </div>
                </div>

                <hr>

                <p class="align-right">
                    <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_ADD_STATUS') ?>" />
                </p>
            </form>
        </div>

        <?php if (isset($histories) && count($histories)) { ?>
        <div class="block-flat">
            <table class="table no-border list">
                <thead class="no-border">
                    <tr>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDED') ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENTS') ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS') ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_INFORMED') ?>?</strong></th>
                    </tr>
                </thead>
                <tbody class="no-border-y items">
                    <?php foreach ($histories as $history) { ?>
                    <tr>
                        <td><?php echo $history['date_added']; ?></td>
                        <td><?php echo $history['comment']; ?></td>
                        <td><?php echo $history['status']; ?></td>
                        <td><?php echo $history['notify']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>

    <div class="col-md-4">
        <div class="block-flat">
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_NO') ?>:</strong></dt>
                <dd><?php echo $return_id; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO') ?>:</strong></dt>
                <dd><a href="<?php echo $this->url->link('sale/orders/info', 'order_id=' . $raw_order_id . '&token=' . $token, 'SSL')?>"><?php echo $order_id?></a></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_ORDERED') ?>:</strong></dt>
                <dd><?php echo $date_ordered; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER') ?>:</strong></dt>
                <dd><?php echo $firstname . ' ' . $lastname; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL') ?>:</strong></dt>
                <dd><?php echo $email; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE') ?>:</strong></dt>
                <dd><?php echo $telephone; ?></dd>
            </dl>
            <?php if ($return_status) { ?>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_STATUS') ?>:</strong></dt>
                <dd><?php echo $return_status; ?></dd>
            </dl>
            <?php } ?>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_ADDED') ?>:</strong></dt>
                <dd><?php echo $date_added; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_MODIFIED') ?>:</strong></dt>
                <dd><?php echo $date_modified; ?></dd>
            </dl>
        </div>

        <div class="block-flat">
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT') ?>:</strong></dt>
                <dd><?php echo $product; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL') ?>:</strong></dt>
                <dd><?php echo $model; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY') ?>:</strong></dt>
                <dd><?php echo $quantity; ?></dd>
            </dl>
            <dl>
                <dt><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_REASON') ?>:</strong></dt>
                <dd><?php echo $return_reason; ?></dd>
            </dl>
            <?php if (!empty($comment)): ?>
            <dl>
                <dd><?php echo $comment?></dd>
            </dl>
            <?php endif; ?>
            <dl>
                <dt><strong><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_OPENED')) ?>:</strong></dt>
                <dd><?php if ($opened) { echo Sumo\Language::getVar('SUMO_NOUN_YES'); } else { echo Sumo\Language::getVar('SUMO_NOUN_NO'); } ?></dd>
            </dl>
        </div>
    </div>
</div>

<?php echo $footer; ?>
