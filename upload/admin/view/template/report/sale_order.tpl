<?php echo $header?>

<script type="text/javascript">
    sessionToken = '<?php echo $token; ?>';
</script>

<form method="get" action="">
    <input type="hidden" name="token" value="<?php echo $token; ?>" />

    <div class="block-flat" style="padding-bottom: 5px; margin: 20px 0 10px;">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="date_start"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_START'); ?>:</label> 
                    <input type="text" name="filter_date_start" id="date_start" value="<?php echo $filter_date_start; ?>" class="date-picker form-control" />
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="date_end"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_END'); ?>:</label> 
                    <input type="text" name="filter_date_end" id="date_end" value="<?php echo $filter_date_end; ?>" class="date-picker form-control" />
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="group" class="control-label"><?php echo Sumo\Language::getVar('SUMO_VERB_GROUP'); ?>:</label>
                    <select name="filter_group" class="form-control" id="group">
                        <?php foreach ($groups as $groups) { ?>
                        <?php if ($groups['value'] == $filter_group) { ?>
                        <option value="<?php echo $groups['value']; ?>" selected="selected"><?php echo $groups['text']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $groups['value']; ?>"><?php echo $groups['text']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="status_id" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_ID'); ?></label>
                    <select name="filter_order_status_id" id="status_id" class="form-control">
                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_ALL'); ?></option>
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <p class="align-right">
        <a class="btn btn-secondary btn-sm" href="<?php echo $reset; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SHOW_ALL'); ?></a>
        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_FILTER_REPORT'); ?>" class="btn btn-primary btn-sm" />
    </p>
</form>

<div class="block-flat">
    <?php if ($orders) { ?>
    <table class="table no-border hover">
        <thead class="no-border">
            <tr>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_START'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_END'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDERS'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_AMOUNT'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></strong></th>
            </tr>
        </thead>
        <tbody class="no-border-y">
            <?php foreach ($orders as $order) { ?>
            <tr>
                <td><?php echo $order['date_start']; ?></td>
                <td><?php echo $order['date_end']; ?></td>
                <td><?php echo $order['orders']; ?></td>
                <td><?php echo $order['products']; ?></td>
                <td><?php echo $order['tax']; ?></td>
                <td><?php echo $order['total']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if ($pagination) { ?>
    <div class="pull-right" style="margin-left: 20px;">
        <ul class="pagination pagination-sm" style="margin: 0;">
            <?php echo $pagination; ?>
        </ul>
    </div>
    <?php } ?>
    <?php } else { ?>
    <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_ORDERS'); ?></p>
    <?php } ?>
</div>

<?php echo $footer; ?>