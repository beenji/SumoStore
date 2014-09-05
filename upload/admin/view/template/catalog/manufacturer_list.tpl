<?php echo $header; ?>
    
    <script type="text/javascript">
        sessionToken = '<?php echo $token; ?>';
    </script>

    <div class="row">
        <div class="col-md-8">
            <form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
                <div class="block-flat">
                    <?php if ($manufacturers) { ?>
                    <table class="table no-border list">
                        <thead class="no-border items">
                            <tr>
                                <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                                <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_URL'); ?></strong></th>
                                <th style="wdith: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody class="no-border-y items">
                            <?php foreach ($manufacturers as $manufacturer) { ?>
                            <tr>
                                <td><input type="checkbox" name="selected[]" value="<?php echo $manufacturer['manufacturer_id']; ?>" class="icheck"></td>
                                <td style="width: 100px; text-align: center;">
                                    <div class="img">
                                        <img src="../image/<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>" />
                                    </div>
                                </td>
                                <td><?php echo $manufacturer['name']; ?></td>
                                <td><?php echo $manufacturer['keyword']; ?></td>
                                <td class="right">
                                    <div class="btn-group">
                                        <a href="<?php echo $manufacturer['edit']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                        <a href="<?php echo $delete; ?>" class="btn btn-sm btn-primary" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE_CONFIRM_MANUFACTURER'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
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
                                        <li><a href="<?php echo $delete; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-right">
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <p class="well" style="margin-bottom: 0;"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_MANUFACTURERS'); ?></p>
                    <?php } ?>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form action="<?php echo $action; ?>" method="post">
                <div class="block-flat">
                    <div class="header">
                        <h3><?php if ($manufacturer_id) { ?><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT_BRAND'); ?><?php } else { ?><?php echo Sumo\Language::getVar('SUMO_NOUN_ADD_BRAND'); ?><?php } ?></h3>
                    </div>

                    <div class="content">
                        <div class="form-group">
                            <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                            <input type="text" name="name" id="name" value="<?php echo $name; ?>" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="keyword" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_URL'); ?>:</label>
                            <input type="text" name="keyword" id="keyword" value="<?php echo $keyword; ?>" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="image" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGE'); ?>:</label>
                            <div class="control-group">
                                <div class="fancy-upload">
                                    <?php if ($image): ?>
                                    <img src="../image/<?php echo $image; ?>" />
                                    <a class="fu-edit" href="#edit" id="upload-btn"><i class="fa fa-wrench"></i></a>
                                    <a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>
                                    <?php else: ?>
                                    <a class="fu-new" href="#upload" id="upload-btn"><i class="fa fa-plus-circle"></i></a>
                                    <?php endif; ?>

                                    <input type="hidden" name="image" id="upload" value="<?php echo $image ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shop" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STORES'); ?>:</label>
                            <div class="control-group">
                                <?php foreach ($stores as $list): ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="manufacturer_store[]" value="<?php echo $list['store_id']?>"<?php if (in_array($list['store_id'], $manufacturer_store)) { echo ' checked="checked"'; } ?> />
                                        <?php echo $list['name']?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <hr>

                        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_BRAND'); ?>">
                        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php echo $footer; ?>