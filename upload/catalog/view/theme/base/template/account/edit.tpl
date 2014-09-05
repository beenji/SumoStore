<?php echo $header;
// Note; keep the form (fields) mostly the same as the register page for consistency..
?>
<div class="container">
    <?php if (!empty($settings['left']) && count($settings['left'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['left'] as $key => $item) {
            if (!$item || $item == null) {
                unset($settings['left'][$key]);
                continue;
            }
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    $mainClass = 'col-md-12';
    if (!empty($settings['left']) && !empty($settings['right'])) {
        $mainClass = 'col-md-6';
    }
    else if (!empty($settings['left']) || !empty($settings['right'])) {
        $mainClass = 'col-md-9';
    }
    ?>

    <div class="<?php echo $mainClass?>">
        <div class="row">
            <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_EDIT_TITLE')?></h1>

            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                <?php endforeach?>
            </ol>
            <div class="col-md-12">
                <?php if (!empty($success)): ?>
                <div class="alert alert-success"><p><?php echo $success?></p></div>
                <?php endif?>
                <?php if (!empty($warning)): ?>
                <div class="alert alert-danger"><p><?php echo $warning?></p></div>
                <?php endif?>
            </div>
        </div>
        <div class="row">
            <form method="post" action="<?php echo $this->url->link('account/edit', '', 'SSL')?>">
                <div class="col-md-6">
                    <h2><?php echo Sumo\Language::getVar('SUMO_NOUN_MY_DETAILS'); ?></h2>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIRSTNAME')?></label>
                        <input type="text" name="firstname" value="<?php echo $firstname?>" required class="form-control">
                        <?php if ($error_firstname): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_firstname?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIDDLENAME')?></label>
                        <input type="text" name="middlename" value="<?php echo $middlename?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LASTNAME')?></label>
                        <input type="text" name="lastname" value="<?php echo $lastname?>" required class="form-control">
                        <?php if ($error_lastname): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_lastname?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL')?></label>
                        <input type="email" name="email" value="<?php echo $email?>" required class="form-control">
                        <?php if ($error_email): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_email?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TELEPHONE')?></label>
                        <input type="text" name="telephone" value="<?php echo $telephone?>" required class="form-control">
                        <?php if ($error_telephone): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_telephone?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MOBILE_PHONE')?></label>
                        <input type="text" name="mobile" value="<?php echo $mobile?>" class="form-control">
                        <?php if ($error_mobile): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_mobile?></span>
                        <?php endif?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2>&nbsp;</h2>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER')?></label>
                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="gender" value="m" required <?php if (isset($gender) && $gender == 'm' || empty($gender) || ($gender != 'm' && $gender != 'f')) { echo 'checked'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_MALE')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" value="f" required <?php if (isset($gender) && $gender == 'f') { echo 'checked'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_FEMALE')?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEWSLETTER')?></label>
                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="newsletter" value="1" <?php if ($this->customer->getData('newsletter')) { echo 'checked'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="newsletter" value="0" <?php if (!$this->customer->getData('newsletter')) { echo 'checked'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BIRTHDATE')?></label>
                        <input type="text" name="birthdate" value="<?php echo Sumo\Formatter::dateShort(strtotime($birthdate))?>" <?php if ($this->config->get('age_checkout')) { echo 'required';}?> class="form-control birthdatepicker" placeholder="<?php echo Sumo\Formatter::dateShort(time())?>">
                        <?php if ($error_birthdate): ?>
                        <span class="help-block alert alert-danger"><?php echo $error_birthdate?></span>
                        <?php endif?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FAX')?></label>
                        <input type="text" name="fax" value="<?php echo $fax?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>" class="form-control btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($settings['right'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['right'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    if (isset($settings['bottom'])): ?>
    <div class="col-md-12">
        <?php
        foreach ($settings['bottom'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <div class="clearfix"></div>
    <?php endif ?>
</div>
<script type="text/javascript">
$(function(){
    $('.birthdatepicker').datetimepicker({
        format: '<?php echo Sumo\Formatter::dateFormatToJS(); ?>',
        autoclose: true,
        minView: 2,
        maxView: 4,
        startView: 4
    });
})
</script>
<script src="admin/view/js/bootstrap/bootstrap.datetimepicker.js"></script>
<?php echo $footer ?>
