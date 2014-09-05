<?php echo $header?>

<script type="text/javascript">
    sessionToken = '<?php echo $this->session->data['token'] ?>';
</script>

<form method="get" action="">
    <input type="hidden" name="token" value="<?php echo $this->session->data['token'] ?>" />

    <div class="block-flat" style="padding-bottom: 5px; margin: 20px 0 10px;">
        <div class="row">
            <?php if (!$disable_dates): ?>
            <div class="<?php if (isset($statuses) && count($statuses)) { echo 'col-sm-3'; } else { echo 'col-sm-4'; }?>">
                <div class="form-group">
                    <label class="control-label" for="date_start"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_START'); ?>:</label>
                    <input type="text" name="date_start" id="date_start" value="<?php echo $date_start; ?>" class="date-picker form-control" />
                </div>
            </div>

            <div class="<?php if (isset($statuses) && count($statuses)) { echo 'col-sm-3'; } else { echo 'col-sm-4'; }?>">
                <div class="form-group">
                    <label class="control-label" for="date_end"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_END'); ?>:</label>
                    <input type="text" name="date_end" id="date_end" value="<?php echo $date_end; ?>" class="date-picker form-control" />
                </div>
            </div>
            <?php endif; if (!isset($disable_group)): ?>
            <div class="<?php if (isset($statuses) && count($statuses)) { echo 'col-sm-3'; if ($disable_dates) { echo ' col-sm-offset-6'; } } else { echo 'col-sm-4'; if ($disable_dates) { echo ' col-sm-offset-8'; } }?>  ?>">
                <div class="form-group">
                    <label for="group" class="control-label"><?php echo Sumo\Language::getVar('SUMO_VERB_GROUP'); ?>:</label>
                    <select name="group" class="form-control" id="group">
                        <?php foreach ($groups as $groups) { ?>
                        <?php if ($groups['value'] == $group) { ?>
                        <option value="<?php echo $groups['value']; ?>" selected="selected"><?php echo $groups['text']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $groups['value']; ?>"><?php echo $groups['text']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php endif; if (isset($statuses) && count($statuses)): ?>
            <div class="<?php if (isset($disable_group)){ echo 'col-md-offset-9'; }?> col-sm-3">
                <div class="form-group">
                    <label for="status_id" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_ID'); ?></label>
                    <select name="status_id" id="status_id" class="form-control">
                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_ALL'); ?></option>
                        <?php foreach ($statuses as $status):  ?>
                        <option value="<?php echo $status['status_id']; ?>"<?php if ($status['status_id'] == $status_id) { echo 'selected'; } ?>><?php echo $status['name']; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <?php endif ?>
        </div>
    </div>

    <p class="align-right">
        <a class="btn btn-secondary btn-sm" href="<?php echo $reset; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SHOW_ALL'); ?></a>
        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_REPORT'); ?>" class="btn btn-primary btn-sm" />
    </p>
</form>

<div class="block-flat">
    <?php if (isset($items) && count($items)) { ?>
    <table class="table no-border hover">
        <thead class="no-border">
            <tr>
                <?php if (isset($table_head) && !empty($table_head)) {
                    foreach ($table_head as $name): ?>
                    <th><strong><?php echo $name?></strong></th>
                    <?php endforeach;
                }
                else { ?>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_START'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_END'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDERS'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TAX_AMOUNT'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></strong></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody class="no-border-y">
            <?php foreach ($items as $time => $order) {
                echo '<tr>';
                foreach ($order as $field) {
                    echo '<td>' . $field . '</td>';
                }
                echo '</tr>';
            } ?>
        </tbody>
    </table>

    <?php if (isset($pagination)) { ?>
    <div class="pull-right" style="margin-left: 20px;">
        <ul class="pagination pagination-sm" style="margin: 0;">
            <?php echo $pagination; ?>
        </ul>
    </div>
    <?php } ?>
    <?php } else { ?>
    <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_REPORT'); ?></p>
    <?php } ?>
</div>

<?php echo $footer; ?>
