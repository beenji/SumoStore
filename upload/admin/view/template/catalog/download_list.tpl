<?php echo $header; ?>

    <script type="text/javascript">
        sessionToken = '<?php echo $token; ?>';
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
                    <?php if ($downloads) { ?>
                    <table class="table no-border list">
                        <thead class="no-border items">
                            <tr>
                                <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DOWNLOADS_ALLOWED'); ?></strong></th>
                                <th style="width: 30px;"></th>
                                <th style="width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody class="no-border-y items">
                            <?php foreach ($downloads as $download) { ?>
                            <tr>
                                <td><input type="checkbox" name="selected[]" value="<?php echo $download['download_id']; ?>" <?php if ($download['selected']) { echo 'checked="checked"'; }?> class="icheck"></td>
                                <td><?php echo $download['name']; ?></td>
                                <td><?php echo $download['remaining']; ?></td>
                                <td><a href="<?php echo $this->url->link('catalog/download/download', 'token=' . $this->session->data['token'] . '&download_id=' . $download['download_id'])?>"><i class="fa fa-cloud-download"></i></a></td>
                                <td class="right">
                                    <div class="btn-group">
                                        <a href="<?php echo $download['action'][0]['href']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                        <a href="<?php echo $delete; ?>" class="btn btn-sm btn-primary" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE_CONFIRM_DOWNLOAD'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
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
                            <?php if (isset($pagination)) {
                                echo $pagination;
                            } ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <p class="well" style="margin-bottom: 0;"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_DOWNLOADS'); ?></p>
                    <?php } ?>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form action="<?php if (!$download_id) { echo $insert; } ?>" method="post" data-parsley-validate novalidate>
                <div class="block-flat">
                    <div class="header">
                        <h3><?php if ($download_id) { ?><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT_DOWNLOAD'); ?><?php } else { ?><?php echo Sumo\Language::getVar('SUMO_NOUN_NEW_DOWNLOAD'); ?><?php } ?></h3>
                    </div>

                    <div class="content">
                        <div class="form-group">
                            <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                            <?php foreach ($languages as $list): ?>
                            <div class="input-group lang-block<?php if ($list['is_default']) { ?> lang-active<?php } ?> lang-<?php echo $list['language_id'];?>">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" alt="<?php echo $list['name']; ?>" />
                                </span>
                                <input type="text" required data-parsley-length="[3,64]" name="download_description[<?php echo $list['language_id']?>][name]" value="<?php echo isset($download_description[$list['language_id']]) ? $download_description[$list['language_id']]['name'] : ''; ?>" class="form-control" />
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label for="file" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FILE'); ?>:</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" id="upload-btn" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_CHOOSE_FILE'); ?></button>
                                </span>
                                <input class="form-control" required data-parsley-length="[3,128]" readonly="readonly" name="filename" id="upload" value="<?php echo $filename; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mask" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FILENAME_FOR_DOWNLOAD'); ?>:</label>
                            <input type="text" required data-parsley-length="[3,128]" id="mask" name="mask" value="<?php echo $mask?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="remaining" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DOWNLOADS_ALLOWED'); ?>:</label>
                            <input type="text" data-parsley-type="integer" min="0" name="remaining" value="<?php echo $remaining; ?>" id="remaining" class="form-control" />
                        </div>

                        <?php if ($download_id) { ?>
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="update" id="update" <?php if ($update) { echo 'checked="checked" '; }?>value="1" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_UPDATE_ORDERS'); ?>
                            </label>
                        </div>
                        <?php } ?>

                        <hr>

                        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_DOWNLOAD'); ?>">
                        <?php if ($download_id) { ?><a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a><?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</form>

<?php echo $footer; ?>
