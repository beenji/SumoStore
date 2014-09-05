<?php echo $header; ?>

<form action="<?php echo $this->url->link('settings/store/remove', 'token=' . $this->session->data['token'], 'SSL') ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($stores) { ?>
        <table class="table no-border list">
            <thead class="no-border">
                <tr>
                    <th style="width: 45px;"></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPNAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPURL'); ?></strong></th>
                    <th style="width: 185px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y">
                <?php
                foreach ($stores as $list) {
                $url = $list['base_' . $list['base_default']];
                ?>
                <tr>
                    <td>
                        <input type="checkbox" class="icheck" name="selected[]" value="<?php echo $list['store_id']; ?>"<?php if ($list['selected']) { ?> checked="checked"<?php } ?> />
                    </td>
                    <td><?php echo $list['name']; if ($list['store_id'] == 0) { echo ' (' . Sumo\Language::getVar('SUMO_NOUN_DEFAULT_STORE') . ')'; } ?></td>
                    <td><a href="<?php echo $url?>" target="_blank" class="link-external"><?php echo $url?> <i class="fa fa-link"></i></a></td>
                    <td class="right">
                        <div class="btn-group">
                            <a href="<?php echo $list['edit']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT'); ?></a>
                            <a href="<?php echo $this->url->link('settings/store/remove', 'token=' . $this->session->data['token'], 'SSL') ?>" class="btn btn-sm btn-primary" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_CONFIRM_DELETE'); ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE'); ?></a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>

        <div class="table-padding">
            <div class="btn-group pull-left">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?php echo $this->url->link('settings/store/remove', 'token=' . $this->session->data['token'], 'SSL') ?>" rel="selectedItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_CONFIRM_DELETE'); ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE'); ?></a></li>
                </ul>
            </div>

            <div class="btn-group pull-right">
                <a href="<?php echo $this->url->link('settings/store/insert', 'token=' . $this->session->data['token'], 'SSL') ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_NEW'); ?></a>
            </div>

            <div class="clearfix"></div>
        </div>
        <?php } else { ?>
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_STORES'); ?></p>
        <?php } ?>
    </div>
</form>

<?php echo $footer; ?>
