<?php echo $header; ?>


<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($users) { ?>
        <table class="table no-border list">
            <thead class="no-border">
                <tr>
                    <th style="width: 45px;"></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_USERNAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_ADDED'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_LAST_LOGIN'); ?></strong></th>
                    <th style="wdith: 100px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($users as $user) { ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?php echo $user['user_id']; ?>" class="icheck"></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['status']; ?></td>
                    <td><?php echo $user['date_added']; ?></td>
                    <td><?php echo $user['date_last_seen']; ?></td>
                    <td class="right">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-secondary" href="<?php echo $user['edit']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                            <a class="btn btn-sm btn-primary" href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_USER'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>

        <hr>

        <div class="row">
            <?php if ($users) { ?>
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
            <div class="<?php if ($users) { ?>col-md-4 <?php } else { ?>col-md-12 <?php } ?>align-right">
                <div class="table-padding">
                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo $footer; ?> 