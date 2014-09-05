<?php echo $header; ?>

<form action="" method="post">
    <div class="row">
        <div class="col-md-5">
            <div class="block-flat">
                <div class="header">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT_FIELDS'); ?></h3>
                </div>
                <div class="content">
                    <ul class="export-list">
                        <?php foreach ($fields as $field) { ?>
                        <li>
                            <dl>
                                <dt><input type="checkbox" class="icheck" id="field_<?php echo $field; ?>" name="field[]" value="<?php echo $field; ?>" /></dt>
                                <dd><label for="field_<?php echo $field; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIELD_' . mb_strtoupper($field)); ?></label></dd>
                            </dl>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="block-flat">
                <div class="header">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTIONS_FOR_EXPORT'); ?></h3>
                </div>
                <div class="content">
                    <div class="form-group">
                        <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FILENAME'); ?>:</label>
                        <input type="text" name="name" id="name" class="form-control" />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sort" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SORT_BY'); ?>:</label>
                                <select name="sort" id="sort" class="form-control">
                                    <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_CHOOSE'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_START_AT_ROW'); ?>:</label>
                                <input type="text" name="start" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="limit" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NUMBER_OF_PRODUCTS'); ?>:</label>
                                <input type="text" name="limit" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="separator" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEPARATOR'); ?>:</label>
                                <input type="text" name="separator" id="separator" class="form-control" value="," />
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="wrap" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_WRAP_VALUES'); ?>:</label>
                                <label for="wrap" class="checkbox" style="margin-top: 7px; font-weight: normal;">
                                    <input type="checkbox" name="wrap" id="wrap" value="1" /> <?php echo Sumo\Language::getVar('SUMO_NOUN_WRAP_VALUES_YES'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="align-right">
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_START_EXPORT'); ?>" />
    </p>
</form>

<?php echo $footer; ?>