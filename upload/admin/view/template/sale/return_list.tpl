<?php echo $header; ?>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($returns) { ?>
        <table class="table no-border list">
            <thead class="no-border items">
                <tr>
                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_RETOUR'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_ADDED'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_MODIFIED'); ?></strong></th>
                    <th style="wdith: 100px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($returns as $list) { ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?php echo $list['return_id']; ?>" class="icheck"></td>
                    <td>RID.<?php echo str_pad($list['return_id'], 5, 0, STR_PAD_LEFT); ?></td>
                    <td>OID.<?php echo str_pad($list['order_id'], 5, 0, STR_PAD_LEFT); ?></td>
                    <td><?php echo $list['customer']; ?></td>
                    <td><?php echo $list['product']; ?></td>
                    <td><?php echo $list['status']; ?></td>
                    <td><?php echo $list['date_added']; ?></td>
                    <td><?php echo $list['date_modified']; ?></td>
                    <td class="right">
                        <div class="btn-group">
                            <a href="<?php echo $list['info']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INFO'); ?></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown">Meer... <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="<?php echo $list['edit']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a></li>
                                    <li><a href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_RETURN'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <hr>
        <?php } else { ?>
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_RETURNS'); ?></p>
        <?php } ?>

        <div class="row">
            <?php if ($returns) { ?>
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
            <div class="<?php if ($returns) { ?>col-md-4 <?php } else { ?>col-md-12 <?php } ?>align-right">
                <div class="table-padding"<?php if (!$returns) { ?> style="padding-right: 0;"<?php } ?>>
                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo $footer?>