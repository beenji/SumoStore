<?php echo $header; ?>

<script type="text/javascript">
    sessionToken = '<?php echo $token; ?>';
    formError = '<?php echo $error; ?>';
</script>

<form action="<?php echo $action; ?>" method="post" class="form-horizontal">
    <div class="block-flat">
        <div class="form-group">
            <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AUTHOR'); ?>:</label>
            <div class="col-md-6">
                <input type="text" name="author" value="<?php echo $author?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">
                <?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?>:
            </label>
            <div class="col-md-6">
                <input type="text" name="product" id="product" class="form-control" value="<?php echo $product?>" data-selected-option="<?php echo $product; ?>">
                <input type="hidden" name="product_id" id="product_id" class="form-control" value="<?php echo $product_id?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-2 control-label">
                <?php echo Sumo\Language::getVar('SUMO_NOUN_TEXT'); ?>:
            </label>
            <div class="col-md-10">
                <textarea name="text" class="redactor"><?php echo $text?></textarea>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-2 control-label">
                <?php echo Sumo\Language::getVar('SUMO_NOUN_RATING'); ?>:
            </label>
            <div class="col-md-8 form-control-static">
                <label for="rating_1"><?php echo Sumo\Language::getVar('SUMO_NOUN_RATING_BAD'); ?> &nbsp;</label>
                <?php for ($i = 1; $i <= 5; $i++) {
                    echo '<input type="radio" id="rating_' . $i . '" name="rating" value="' . $i . '" ';
                    if ($i == $rating) {
                        echo 'checked="checked"';
                    }
                    echo '>&nbsp;'.PHP_EOL;
                }
                ?>
                <label for="rating_5">&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_RATING_GOOD'); ?></label>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
            <div class="col-md-8">
                <label class="radio-inline">
                    <input type="radio" name="status" value="1" <?php if ($status) { echo 'checked="checked"'; } ?>>
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_ENABLED'); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="status" value="0" <?php if (!$status) { echo 'checked="checked"'; } ?>>
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_DISABLED'); ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?>:</label>
            <div class="col-md-2 col-sm-3">
                <input type="text" name="date_added" class="form-control date-picker" value="<?php echo $date_added; ?>" />
            </div>
        </div>
    </div>

    <p class="align-right">
        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?>" />
    </p>
</form>

<?php echo $footer; ?>