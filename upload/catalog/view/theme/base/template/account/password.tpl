<?php echo $header?>
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
            <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_PASSWORD_TITLE')?></h1>

            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                <?php endforeach?>
            </ol>
            <div class="col-md-12">
                <?php if (!empty($success)): ?>
                <div class="alert alert-success"><p><?php echo $success?></p></div>
                <?php endif?>
                <?php if (!empty($error_token)): ?>
                <div class="alert alert-danger"><p><?php echo $error_token?></p></div>
                <?php endif?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h2><?php echo Sumo\Language::getVar('SUMO_NOUN_MY_PASSWORD'); ?></h2>
                <form method="post" action="<?php echo $this->url->link('account/password', 'token=' . $this->session->data['token'], 'SSL')?>" id="password-form">
                    <div class="form-group <?php if (!empty($error_password)) { echo 'has-error'; }?>">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD')?></label>
                        <input type="password" name="password" class="form-control" required>
                        <?php if (!empty($error_password)) { ?>
                        <span class="help-block"><?php echo $error_password; ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group <?php if (!empty($error_confirm)) { echo 'has-error'; }?>">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PASSWORD_CONFIRM')?></label>
                        <input type="password" name="confirm" class="form-control" required>
                        <?php if (!empty($error_confirm)) { ?>
                        <span class="help-block"><?php echo $error_confirm; ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>" class="form-control btn btn-primary">
                    </div>
                </form>
            </div>
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
$(function() {
    $('input[name="newsletter"]').on('change click', function() {
        console.log('changed');
        $('#newsletter-form').submit();
    })
})
</script>
<?php echo $footer ?>

