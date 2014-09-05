<?php echo $header; ?>

<?php if ($error) { ?>
<script type="text/javascript">
    formError = '<?php echo $error; ?>';
</script>
<?php } ?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" autocomplete="off">
    <div class="block-flat">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_LANGUAGE'); ?>:
                    </label>
                    <div class="control-group">
                        <input type="text" name="name" value="<?php echo $name?>" class="form-control">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_LANGUAGE_HELP'); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:
                    </label>
                    <div class="control-group">
                        <div class="radio-inline">
                            <input type="radio" name="status" value="1" <?php if ($status) { echo 'checked="checked"'; } ?>>
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_ENABLED'); ?>
                        </div>
                        <div class="radio-inline">
                            <input type="radio" name="status" value="0" <?php if (!$status) { echo 'checked="checked"'; } ?>>
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_DISABLED'); ?>
                        </div>
                        <br />
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_HELP'); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_FALLBACK_LANGUAGE'); ?>:
                    </label>
                    <div class="control-group">
                        <select name="fallback" id="fallback" class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_PICK_ONE'); ?></option>
                            <?php foreach ($languages as $language) { ?>
                                <?php if ($language['language_id'] != $language_id) { ?>
                                <option<?php if ($language['language_id'] == $fallback) { echo ' selected="selected"'; } ?> value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_ISO_CODE'); ?>:
                    </label>
                    <div class="control-group">
                        <input type="text" name="code" value="<?php echo $code?>" class="form-control">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_ISO_CODE_HELP'); ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_LOCALE'); ?>:
                    </label>
                    <div class="control-group">
                        <input type="text" name="locale" value="<?php echo $locale?>" class="form-control">
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_LOCALE_HELP'); ?></span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGE'); ?>:
                            </label>
                            <div class="control-group">
                                <input type="text" name="image" value="<?php echo $image?>" class="form-control">
                                <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGE_HELP'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_SORT_ORDER'); ?>:
                            </label>
                            <div class="control-group">
                                <input type="text" name="sort_order" value="<?php echo $sort_order?>" class="form-control" size="1">
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="align-right">
        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?>" />
    </p>
</form>

<?php echo $footer?>