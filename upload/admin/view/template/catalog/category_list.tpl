<?php echo $header; ?>

<script type="text/javascript">
    sessionToken = '<?php echo $token; ?>';
</script>

<?php if (sizeof($categories) > 0): ?>
<ul class="nav nav-tabs">
    <?php $i = 0; foreach ($categories as $store_id => $cats): ?>
    <li<?php if ($i == 0) { ?> class="active"<?php } ?>><a href="#store-<?php echo $store_id; ?>" data-toggle="tab"><?php echo $stores[$store_id]['name']; ?></a></li>
    <?php $i++; endforeach; ?>
</ul>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="tab-content">
        <?php $i = 0; foreach ($categories as $store_id => $cats):  ?>
        <div class="tab-pane<?php if ($i == 0) { ?> active<?php } ?> cont" id="store-<?php echo $store_id; ?>">
            <div class="row">
                <div class="col-md-4 align-right col-md-offset-8">
                    <div class="table-padding"<?php if (!$cats) { echo ' style="padding-right: 0;"'; } ?>>
                        <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_CATEGORY'); ?></a>
                    </div>
                </div>
            </div>

            <?php if ($cats): ?>
            <table class="table list no-border">
                <thead class="no-border items">
                    <tr>
                        <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                        <th style="width: 140px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_VISIBLE'); ?></strong></th>
                        <th style="width: 100px;" class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></strong></th>
                        <th style="width: 100px;" class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_META_DESCRIPTION_ABBR'); ?></strong></th>
                        <th style="width: 100px;" class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_KEYWORDS_SHORT'); ?></strong></th>
                        <th style="width: 60px;"></td>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody class="no-border-y items">
                    <?php foreach ($cats as $category): ?>
                    <tr>
                        <td><input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" class="icheck"></td>
                        <td<?php if ($category['parent_id'] > 0) { echo ' class="indent" style="padding-left: 30px;"'; } ?>><?php echo $category['name']; ?></td>
                        <td>
                            <div class="switch switch-small switch-status <?php if ($category['parent_id'] > 0) { echo 'switch-status-parent-' . $category['parent_id']; }?>" data-on-label="<?php echo Sumo\Language::getVar('SUMO_NOUN_ON'); ?>" data-off-label="<?php echo Sumo\Language::getVar('SUMO_NOUN_OFF'); ?>" data-category-id="<?php echo $category['category_id']; ?>" data-parent-id="<?php echo $category['parent_id']; ?>">
                                <input type="checkbox" name="status[]" value="1"<?php if ($category['status']) { ?> checked<?php } ?>>
                            </div>
                        </td>
                        <td class="align-center">
                            <?php
                            if (empty($category['description'])) {
                                $class = '#f41958';
                            }
                            elseif (strlen($category['description']) <= 25) {
                                $class = '#FED16C';
                            }
                            else {
                                $class = '#629e14';
                            }
                            ?>
                            <i class="fa fa-circle" style="color: <?php echo $class; ?>"></i>
                        </td>
                        <td class="align-center">
                            <?php
                            if (empty($category['meta_description'])) {
                                $class = '#f41958';
                            }
                            else {
                                $class = '#629e14';
                            }
                            ?>
                            <i class="fa fa-circle" style="color: <?php echo $class; ?>"></i>
                        </td>
                        <td class="align-center">
                            <?php
                            if (empty($category['meta_keyword'])) {
                                $class = '#f41958';
                            }
                            else {
                                $class = '#629e14';
                            }
                            ?>
                            <i class="fa fa-circle" style="color: <?php echo $class; ?>"></i>
                        </td>
                        <td>
                            <a href="<?php echo $category['move_up']; ?>" class="fa-enlarged"><i class="fa fa-arrow-circle-o-up"></i></a>
                            <a href="<?php echo $category['move_down']; ?>" class="fa-enlarged"><i class="fa fa-arrow-circle-o-down"></i></a>
                        </td>
                        <td class="right">
                            <div class="btn-group">
                                <a href="<?php echo $this->url->link('catalog/category/update', 'token=' . $this->session->data['token'] . '&category_id=' . $category['category_id'], 'SSL') ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                <a href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_CATEGORY'); ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="table-padding">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo $delete; ?>" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_CATEGORY'); ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 align-center">
                    <?php if (isset($stores[$store_id]['pagination'])) { ?>
                    <?php echo $stores[$store_id]['pagination']; ?>
                    <?php } ?>
                </div>
                <div class="col-md-4 align-right">
                    <div class="table-padding">
                        <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_CATEGORY'); ?></a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_CATEGORIES_STORE'); ?></p>
            <?php endif; ?>
        </div>
        <?php $i++; endforeach; ?>
    </div>
</form>

<h4 class="hnormal"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEO_LEGEND'); ?>:</h4>
<p>
    <i class="fa fa-circle" style="color: #f41958"></i>&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_NOT_SET'); ?> &nbsp;
    <i class="fa fa-circle" style="color: #FED16C"></i>&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_CAN_BE_IMPROVED'); ?> &nbsp;
    <i class="fa fa-circle" style="color: #629e14"></i>&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_PERFECT'); ?></p>
<?php else: ?>
<p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_STORES'); ?></p>
<?php endif; ?>

<?php echo $footer; ?>
