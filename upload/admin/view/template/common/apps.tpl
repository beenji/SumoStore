<?php echo $header; ?>

<?php if (!empty($success)): ?>
<div class="alert alert-success">
    <p><?php echo $success?></p>
</div>
<?php endif?>
<?php if (!empty($warning)): ?>
<div class="alert alert-warning">
    <p><?php echo $warning?></p>
</div>
<?php endif?>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <?php foreach ($stores as $list): ?>
            <li <?php if ($list['store_id'] == $store_id) { echo 'class="active"'; } ?>>
                <a href="<?php echo $this->url->link('common/apps/' . $action, 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
                    <?php echo $list['name']?>
                </a>
            </li>
            <?php endforeach ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">
                <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_INSTALLED')?></h3>
                <table class="table no-border">
                    <thead class="no-border">
                        <tr>
                            <th style="width: 90px;">&nbsp;</th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_NAME')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_VERSION')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_DESCRIPTION')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_AUTHOR')?></strong></th>
                            <?php if (isset($sort)): ?>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SORT_ORDER')?></strong></th>
                            <?php endif?>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y">
                        <?php if (isset($installed)):
                        foreach ($installed as $list): if (isset($list['hidden'])) { continue; }?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if (isset($list['info']['logo'])): ?>
                                <img src="<?php echo $list['info']['logo']?>" style="max-width: 60px; max-height: 60px;">
                                <?php endif?>
                            </td>
                            <td><strong><?php echo htmlentities($list['name'])?></strong></td>
                            <td>
                                <?php echo htmlentities($list['info']['version'])?>
                                <?php if (isset($list['info']['premium'])): ?>
                                <img src="view/img/premium.png" alt="Premium" title="Premium" />
                                <?php endif?>
                            </td>
                            <td style="width:40%;">
                                <?php
                                $description = $list['description'];
                                if (strlen($description) > 255) {
                                    echo '<span class="short">' . substr(strip_tags($description, '<br><br />'), 0, 255) . '... <a href="#readmore" onclick="$(this).parent().hide(); $(this).parent().parent().find(\'.full\').show(); return false;">' . Sumo\Language::getVar('SUMO_NOUN_READ_MORE') .'</a></span>';
                                    echo '<span class="full hidden">' . substr(strip_tags($description, '<br><br />'), 0, 750) . '</span>';
                                }
                                else {
                                    echo strip_tags($description, '<br><br />');
                                }
                                ?>
                            </td>
                            <td style="width: 15%">
                                <?php if (!empty($list['info']['url'])): ?>
                                <a href="<?php echo $list['info']['url']?>" target="_blank" class="link-external">
                                <?php endif?>
                                <?php echo htmlentities($list['info']['author'])?>
                                <?php if (!empty($list['info']['url'])): ?>
                                <i class="fa fa-link"></i></a>
                                <?php endif?>
                            </td>
                            <?php if (isset($sort)): ?>

                            <?php endif?>
                            <td style="width: 20%" class="right">
                                <div class="btn-group">

                                    <a class="btn btn-sm btn-secondary btn-<?php echo $list['checked'] ? 'success' : 'warning'; ?>" href="<?php echo $this->url->link('app/' . $list['list_name'], 'token=' . $this->session->data['token'] . '&store_id=' . $store_id, 'SSL')?>">
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_SETTINGS')?>
                                    </a>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown" type="button">
                                            <?php echo Sumo\Language::getVar('SUMO_NOUN_MORE')?><span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="<?php echo $this->url->link('common/apps/deinstall', 'token=' . $this->session->data['token'] . '&list_name=' . $list['list_name'] . '&category=' . $action . '&store_id=' . $store_id, 'SSL')?>" rel="confirm" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_APP_DEINSTALL')?>">
                                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_DEINSTALL')?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://www.sumostore.net/appstore/details/<?php echo $list['list_name'] . '-' . $list['app_id']?>" target="_blank">
                                                    <?php echo Sumo\Language::getVar('SUMO_ADMIN_APP_GOTO_APPSTORE')?> <i class="fa fa-link"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php /*
                        <tr>
                            <td colspan="6">
                                <?php echo print_r($list, true) ?>
                            </td>
                        </tr>
                        <?php */ ?>
                        <?php endforeach;
                        else: ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="<?php if (isset($sort)) { echo 6; } else { echo 5; }?>">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_APP_NONE_INSTALLED')?>
                            </td>
                        </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="block-flat">
            <div class="header">
                <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_AVAILABLE')?></h3>
            </div>
            <div class="content">
                <table class="table no-border list">
                    <thead class="no-border">
                        <tr>
                            <th style="width: 90px;">&nbsp;</th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_NAME')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_VERSION')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_DESCRIPTION')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APP_AUTHOR')?></strong></th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y items">
                        <?php if (isset($available)):
                        foreach ($available as $list): if (isset($list['hidden'])) { continue; }?>
                        <tr class="<?php if (isset($list['error'])){ echo 'danger'; } ?>">
                            <td style="text-align:center;">
                                <?php if (isset($list['logo'])): ?>
                                <img src="<?php echo $list['logo']?>" style="max-width: 60px; max-height: 60px;">
                                <?php endif?>
                            </td>
                            <td>
                                <strong><?php echo htmlentities($list['name'][$this->config->get('config_language_id')])?></strong>
                            </td>
                            <td>
                                <?php echo htmlentities($list['version'])?>
                                <?php if (isset($list['premium'])): ?>
                                <img src="view/img/premium.png" alt="Premium" title="Premium" />
                                <?php endif?>
                            </td>
                            <td style="width: 40%;">
                                <?php
                                if (isset($list['description'][$this->config->get('config_language_id')])) {
                                    $description = $list['description'][$this->config->get('config_language_id')];
                                }
                                else {
                                    $description = reset($list['description']);
                                }
                                if (strlen($description) > 255) {
                                    echo '<span class="short">' . substr(strip_tags($description, '<br><br />'), 0, 255) . '... <a href="#readmore" onclick="$(this).parent().hide(); $(this).parent().parent().find(\'.full\').show(); return false;">' . Sumo\Language::getVar('SUMO_NOUN_READ_MORE') .'</a></span>';
                                    echo '<span class="full hidden">' . substr(strip_tags($description, '<br><br />'), 0, 750) . '</span>';
                                }
                                else {
                                    echo strip_tags($description, '<br><br />');
                                }
                                ?>
                            </td>
                            <td style="width: 15%">
                                <?php if (!empty($list['url'])): ?>
                                <a href="<?php echo $list['url']?>" target="_blank" class="link-external">
                                <?php endif?>
                                <?php echo htmlentities($list['author'])?>
                                <?php if (!empty($list['url'])): ?>
                                <i class="fa fa-link"></i></a>
                                <?php endif?>
                            </td>
                            <td style="width: 20%" class="right">
                                <?php if (!isset($list['error'])): ?>
                                <div class="btn-group">
                                    <?php if (!isset($list['info'])): ?>
                                    <a class="btn btn-sm btn-secondary" href="<?php echo $this->url->link('common/apps/install', 'token=' . $this->session->data['token'] . '&list_name=' . $list['list_name'] . '&category=' . $action . '&store_id=' . $store_id, 'SSL')?>">
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_INSTALL')?>
                                    </a>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown" type="button">
                                            <?php echo Sumo\Language::getVar('SUMO_NOUN_MORE')?><span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="<?php echo $this->url->link('common/apps/remove', 'token=' . $this->session->data['token'] . '&list_name=' . $list['list_name'] . '&category=' . $action . '&store_id=' . $store_id, 'SSL')?>" rel="confirm" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_APP_REMOVAL')?>">
                                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_REMOVE')?>
                                                </a>
                                            </li>
                                    <?php else: ?>
                                    <a class="btn btn-sm btn-secondary" href="<?php echo $this->url->link('app/' . $list['list_name'], 'token=' . $this->session->data['token'] . '&store_id=' . $store_id, 'SSL')?>">
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_ACTIVATE')?>
                                    </a>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown" type="button">
                                            <?php echo Sumo\Language::getVar('SUMO_NOUN_MORE')?><span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="<?php echo $this->url->link('common/apps/deinstall', 'token=' . $this->session->data['token'] . '&list_name=' . $list['list_name'] . '&category=' . $action . '&store_id=' . $store_id, 'SSL')?>" rel="confirm" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_APP_DEINSTALL')?>">
                                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_DEINSTALL')?>
                                                </a>
                                            </li>
                                    <?php endif; ?>
                                            <li>
                                                <a href="https://www.sumostore.net/appstore/details/<?php echo $list['list_name'] . '-' . $list['app_id']?>" target="_blank">
                                                    <?php echo Sumo\Language::getVar('SUMO_ADMIN_APP_GOTO_APPSTORE')?> <i class="fa fa-link"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php endif?>
                            </td>
                        </tr>
                        <?php endforeach;
                        else: ?>
                        <tr>
                            <td colspan="6">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_APP_NONE_AVAILABLE')?>
                            </td>
                        </tr>
                        <?php endif?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php echo $footer ?>
