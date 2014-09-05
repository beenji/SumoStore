<?php echo $header; ?>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($coupons) { ?>
        <table class="table no-border list">
            <thead class="no-border">
                <tr class="items">
                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_COUPON_CODE'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_START_DATE'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_END_DATE'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></strong></th>
                    <th style="wdith: 100px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($coupons as $coupon) { ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?php echo $coupon['coupon_id']; ?>" class="icheck"></td>
                    <td><?php echo $coupon['name']; ?></td>
                    <td><?php echo $coupon['code']; ?></td>
                    <td><?php if ($coupon['type'] == 'P') { echo $coupon['discount'] . '%'; } else { echo Sumo\Formatter::currency($coupon['discount']); } ?></td>
                    <td><?php echo $coupon['date_start']; ?></td>
                    <td><?php echo $coupon['date_end']; ?></td>
                    <td><?php echo $coupon['status']; ?></td>
                    <td class="right">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-secondary" href="<?php echo $coupon['edit']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                            <a class="btn btn-sm btn-primary" href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_COUPON'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>
        <?php } else { ?>
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_COUPONS'); ?></p>
        <?php } ?>

        <div class="row">
            <?php if ($coupons) { ?>
            <div class="col-md-4">
                <div class="table-padding">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $delete; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 align-center">
                <?php echo $pagination; ?>
            </div>
            <?php } ?>
            <div class="<?php if ($coupons) { ?>col-md-4 <?php } else { ?>col-md-12 <?php } ?>align-right">
                <div class="table-padding"<?php if (!$coupons) { ?> style="padding-right: 0;"<?php } ?>>
                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo $footer?>
