<?php echo $header; ?>

    <script type="text/javascript">
        var attrCount = <?php if (isset($attributes)) { echo count($attributes); } else { echo '1'; } ?>;
    </script>

    <div class="align-right page-head-actions">
        <div class="btn-group align-left">
            <?php 
            foreach ($languages as $list): 
                if ($list['is_default']):
            ?>
            <button class="btn btn-primary dropdown-toggle" id="language-selector-btn" data-toggle="dropdown" type="button"><span><img src="view/img/flags/<?php echo $list['image']; ?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></span>&nbsp; <span class="caret"></span></button>
            <?php 
                    break;
                endif;
            endforeach; ?>
            <ul class="dropdown-menu pull-right" id="language-selector">
                <?php foreach ($languages as $list): ?>
                <li><a href="#other-language" data-lang-id="<?php echo $list['language_id']; ?>"><img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
                <div class="block-flat">
                    <?php if ($attribute_groups) { ?>
                    <table class="table no-border list">
                        <thead class="no-border items">
                            <tr>
                                <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_GROUP'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ATTRIBUTES'); ?></strong></th>
                                <th style="wdith: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody class="no-border-y items">
                            <?php foreach ($attribute_groups as $attribute_group) { ?>
                            <tr>
                                <td><input type="checkbox" name="selected[]" value="<?php echo $attribute_group['attribute_group_id']; ?>" class="icheck"></td>
                                <td><?php echo $attribute_group['name']; ?></td>
                                <td><?php echo $attribute_group['attributes']; ?></td>
                                <td class="right">
                                    <div class="btn-group">
                                        <a href="<?php echo $attribute_group['edit']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                        <a href="<?php echo $delete; ?>" class="btn btn-sm btn-primary" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE_CONFIRM_ATTRIBUTE'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-padding">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a rel="selectedItemTrigger" href="<?php echo $delete; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-right">
                            <?php if (isset($pagination)) { ?>
                                <?php echo $pagination; ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <p class="well" style="margin-bottom: 0;"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_ATTRIBUTES'); ?></p>
                    <?php } ?>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form action="<?php echo $action; ?>" method="post">
                <div class="block-flat">
                    <div class="header">
                        <h3><?php if ($attribute_group_id) { ?><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT_ATTRIBUTE'); ?><?php } else { ?><?php echo Sumo\Language::getVar('SUMO_NOUN_NEW_ATTRIBUTE'); ?><?php } ?></h3>
                    </div>

                    <div class="content">
                        <?php if ($error_warning) { ?>
                        <p class="alert alert-danger"><?php echo $error_warning; ?></p>
                        <?php } ?>

                        <div class="form-group">
                            <label for="group" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GROUP_NAME'); ?>:</label>
                            <?php foreach ($languages as $list) { ?>
                            <div class="input-group lang-block<?php if ($list['is_default']) { ?> lang-active<?php } ?> lang-<?php echo $list['language_id']; ?>">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" alt="<?php echo $list['name']; ?>" />
                                </span>
                                <input type="text" name="attribute_group_description[<?php echo $list['language_id']; ?>][name]" id="group" class="form-control" value="<?php if (isset($attribute_group_description[$list['language_id']]['name'])) { echo $attribute_group_description[$list['language_id']]['name']; } ?>" />
                            </div>
                            <?php } ?>
                        </div>

                        <div class="form-group">
                            <label for="attributes" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ATTRIBUTES'); ?>:</label>
                            <?php foreach ($languages as $list) { ?>
                            <div class="attribute-block lang-block<?php if ($list['is_default']) { ?> lang-active<?php } ?> lang-<?php echo $list['language_id']; ?>">
                                <?php if ($attributes) { ?>
                                    <?php foreach ($attributes as $i => $attribute) { ?>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <img src="view/img/flags/<?php echo $list['image']?>" alt="<?php echo $list['name']; ?>" />
                                        </span>
                                        <input type="text" name="attribute[<?php echo $i; ?>][attribute_description][<?php echo $list['language_id']; ?>][name]" id="group" class="form-control" value="<?php echo $attribute[$list['language_id']]['name']; ?>" />
                                        <span class="input-group-btn">
                                            <?php if ($i > 0) { ?>
                                            <a href="#" rel="delete_attribute" class="btn btn-default"><i class="fa fa-times"></i></a>
                                            <?php } else { ?>
                                            <a href="#" rel="new_attribute" class="btn btn-default"><i class="fa fa-plus"></i></a>
                                            <?php } ?>
                                        </span>
                                        <?php if (!empty($attribute[$list['language_id']]['attribute_id'])) { ?>
                                        <input type="hidden" name="attribute[<?php echo $i; ?>][attribute_id]" value="<?php echo $attribute[$list['language_id']]['attribute_id']; ?>" />
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                <?php } else { ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <img src="view/img/flags/<?php echo $list['image']?>" alt="<?php echo $list['name']; ?>" />
                                    </span>
                                    <input type="text" name="attribute[0][attribute_description][<?php echo $list['language_id']; ?>][name]" id="group" class="form-control" />
                                    <span class="input-group-btn">
                                        <a href="#" rel="new_attribute" class="btn btn-default"><i class="fa fa-plus"></i></a>
                                    </span>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>

                        <hr>

                        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_SET'); ?>">
                        <?php if ($attribute_group_id) { ?>
                        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php echo $footer; ?>