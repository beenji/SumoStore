<?php echo $header; ?>

<div class="page-head-actions align-right">
    <div class="table-padding">
        <a class="btn btn-primary" href="<?php echo $this->url->link('localisation/language/insert', '', 'SSL')?>">
            <?php echo Sumo\Language::getVar('SUMO_NOUN_ADD')?>
        </a>
    </div>
</div>

<?php if (!empty($error_warning)) { ?>
    <div class="alert alert-warning">
        <i class="icon-warning-sign"></i>
        <?php echo $error_warning; ?>
    </div>
<?php } ?>
<?php if (!empty($success)) { ?>
    <div class="alert alert-success">
        <i class="icon-ok-sign"></i>
        <?php echo $success; ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="block-flat">
            <table class="table no-border hover">
                <thead class="no-border">
                    <tr>
                        <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME_SINGULAR')?></strong></th>
                        <th><strong>Locale</strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS')?></strong></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="no-border-y">
                    <?php foreach($languages as $list): ?>
                    <tr<?php if ($language_id == $list['language_id']) { echo ' class="active"'; } ?>>
                        <td><img src="view/img/flags/<?php echo $list['image']?>"></td>
                        <td><?php echo $list['name']?></td>
                        <td><?php echo $list['locale']?></td>
                        <td><?php echo $list['active'] ? Sumo\Language::getVar('SUMO_NOUN_ACTIVE') : Sumo\Language::getVar('SUMO_NOUN_INACTIVE')?></td>
                        <td class="right">
                            <div class="btn-group">
                                <a href="<?php echo $list['edit']?>" class="btn btn-sm btn-secondary">
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT')?>
                                </a>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown" type="button"><?php echo Sumo\Language::getVar('SUMO_NOUN_MORE')?><span class="caret"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="<?php echo $this->url->link('localisation/language', 'language_id=' . $list['language_id'] . '&token=' . $this->session->data['token'], 'SSL')?>">
                                                <?php echo Sumo\Language::getVar('SUMO_NOUN_TRANSLATE')?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->url->link('localisation/language/duplicate', 'language_id=' . $list['language_id'] . '&token=' . $this->session->data['token'], 'SSL')?>">
                                                <?php echo Sumo\Language::getVar('SUMO_NOUN_DUPLICATE')?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->url->link('localisation/language/delete', 'language_id=' . $list['language_id'] . '&token=' . $this->session->data['token'], 'SSL')?>" rel="confirm" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_LANGUAGE')?>">
                                                <?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE')?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_TRANSLATING', $language)?></h3>
    </div>
    <div class="col-md-12">
        <ul class="nav nav-tabs" id="letter-tab">
            <?php foreach (range('a', 'z') as $letter): ?>
            <li class="">
                <a href="#tab-<?php echo $letter?>" data-toggle="tab" data-letter="<?php echo $letter?>">
                    <?php echo $letter?>
                </a>
            </li>
            <?php endforeach?>
            <li class="">
                <a href="#tab-other" data-toggle="tab" data-letter="other">
                    0-9
                </a>
            </li>
            <li class="">
                <a href="#tab-empty" data-toggle="tab" data-letter="empty">
                    <i class="fa fa-keyboard-o"></i>
                </a>
            </li>
            <?php if (defined('DEVELOPER')): ?>
            <li class="">
                <a href="#tab-developer" data-toggle="tab" data-letter="developer">
                    <i class="fa fa-lab"></i>
                </a>
            </li>
            <?php endif?>
        </ul>
        <div class="tab-content">
            <?php foreach (range('a', 'z') as $letter): ?>
            <div class="tab-pane" id="tab-<?php echo $letter?>">
                <table class="table no-border hover table-translations">
                    <thead class="no-border">
                        <tr>
                            <th><strong>Key</strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_LANGUAGE_ORIGINAL_TRANSLATION')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_LANGUAGE_TRANSLATION')?></strong></th>
                            <th><a href="#" class="btn btn-xs btn-success" onclick="saveAll(); return false;"><i class="fa fa-floppy-o"></i></a></th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y">

                    </tbody>
                </table>
            </div>
            <?php endforeach ?>
            <div class="tab-pane" id="tab-empty">
                <table class="table no-border hover table-translations">
                    <thead class="no-border">
                        <tr>
                            <th style="overflow: hidden;"><strong>Key</strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_LANGUAGE_TRANSLATION')?></strong></th>
                            <th><a href="#" class="btn btn-sm btn-success" onclick="saveAll(); return false;"><i class="fa fa-floppy-o"></i></a></th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y">

                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="tab-other">
                <table class="table no-border hover table-translations">
                    <thead class="no-border">
                        <tr>
                            <th><strong>Key</strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_LANGUAGE_ORIGINAL_TRANSLATION')?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_LANGUAGE_TRANSLATION')?></strong></th>
                            <th><a href="#" class="btn btn-sm btn-success" onclick="saveAll(); return false;"><i class="fa fa-floppy-o"></i></a></th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var token = '<?php echo $this->session->data['token']?>';
var language = <?php echo $language_id?>;
</script>
<?php echo $footer; ?>
