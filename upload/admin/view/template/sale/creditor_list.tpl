<?php echo $header; ?>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($creditors) { ?>
        <table class="table no-border list">
            <thead class="no-border items">
                <tr>
                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CREDITOR_NO'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CREDITORGROUP'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_OPEN_AMOUNT'); ?></strong></th>
                    <th style="wdith: 100px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($creditors as $list) { ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?php echo $list['creditor_id']; ?>" class="icheck"></td>
                    <td>CRID.<?php echo str_pad($list['creditor_id'], 5, 0, STR_PAD_LEFT); ?></td>
                    <td>
                        <?php if (!empty($list['companyname'])) { ?>
                        <?php echo $list['companyname']; ?>
                        <?php } else { ?>
                        <?php echo $list['contact_surname']; ?>, <?php echo $list['contact_name']; ?>
                        <?php } ?>
                    </td>
                    <td><?php echo $list['contact_email']; ?></td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td class="right">
                        <div class="btn-group">
                           	<a href="<?php echo $list['update']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                           	<a href="<?php echo $delete; ?>" class="btn btn-sm btn-primary" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_CREDITOR'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
        <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_CREDITORS'); ?></p>
        <?php } ?>

        <hr>

        <div class="row">
            <?php if ($creditors) { ?>
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
            <div class="<?php if ($creditors) { ?>col-md-4 <?php } else { ?>col-md-12 <?php } ?>align-right">
                <div class="table-padding">
                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo $footer?>