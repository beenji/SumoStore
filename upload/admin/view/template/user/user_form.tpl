<?php echo $header; ?>

<?php if ($error) { ?>
<script type="text/javascript">
    formError = '<?php echo $error; ?>';
</script>
<?php } ?>

<form action="<?php echo $action; ?>" method="post" autocomplete="off">
    <div class="block-flat">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_USERNAME'); ?>:</label>
                    <input type="text" name="username" value="<?php echo $username?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>:</label>
                    <input type="text" autocomplete="off" name="email" value="<?php echo $email?>" class="form-control">
                </div>

                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD'); ?>:</label>
                    <input type="password" autocomplete="off" name="password" value="" class="form-control">
                </div>

                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD_CONFIRM'); ?>:</label>
                    <input type="password" name="confirm" value="" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRSTNAME'); ?>:</label>
                    <input type="text" name="firstname" value="<?php echo $firstname?>" class="form-control">
                </div>

                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LASTNAME'); ?>:</label>
                    <input type="text" name="lastname" value="<?php echo $lastname?>" class="form-control">
                </div>

                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
                    <div class="control-group">
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
            </div>
        </div>
    </div>

    <p class="align-right">
        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?>" />
    </p>
</form>

<?php echo $footer; ?> 