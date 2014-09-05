<?php echo $header; ?>

<form action="<?php echo $this->url->link('settings/store/remove', 'token=' . $this->session->data['token'], 'SSL') ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($themes) { ?>
        <table class="table no-border list">
            <thead class="no-border">
                <tr>
                    <th style="width: 90px;"></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_THEME_NAME')?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_THEME_VERSION')?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_THEME_AUTHOR')?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_THEME_ACTIVE')?></strong></th>
                    <th style="width: 185px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y">
                <?php foreach ($themes as $theme_name => $list) {?>
                <tr>
                    <td style="text-align: center;">
                        <?php
                        if (isset($list['logo'])) {
                            echo '<img style="max-width:60px; max-height: 60px;" src="' . $list['logo'] . '">';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (isset($list['error'])) {
                            echo $theme_name . ': ' . Sumo\Language::getVar('SUMO_NOUN_TEMPLATE_ERROR');
                        }
                        else {
                            if (isset($list['name'][$this->config->get('language_id')])) {
                                echo $list['name'][$this->config->get('language_id')];
                            }
                            else {
                                echo $theme_name;
                            }
                        }?>
                    </td>
                    <td>
                        <?php echo isset($list['version']) ? $list['version'] : '-'?>
                    </td>
                    <td>
                        <?php
                        if (isset($list['url'])) {
                            echo '<a href="' . $list['url'] . '" target="_blank" class="link-external">';
                        }
                        if (isset($list['author'])) {
                            echo $list['author'];
                        }
                        if (isset($list['url'])) {
                            echo ' <i class="fa fa-link"></i></a>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo implode(', ', $list['active'])?>
                    </td>
                    <td class="right">
                        <?php if (isset($list['edit'])): ?>
                        <div class="btn-group">
                            <a href="<?php echo $list['edit']?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT'); ?></a>
                        </div>
                        <?php endif ?>
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

            <div class="clearfix"></div>
        </div>
        <?php } else { ?>
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_THEMES'); ?></p>
        <?php } ?>
    </div>
</form>

<?php echo $footer; ?>
