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
            <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_FORGOTTEN_TITLE')?></h1>

            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                <?php endforeach?>
            </ol>
            <div class="col-md-12">
                <?php if (!empty($warning)): ?>
                <div class="alert alert-warning"><p><?php echo $warning?></p></div>
                <?php endif?>
            </div>
        </div>
        <form method="post" class="form">
            <div class="row">
                <div class="col-md-8">
                    <h2><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_FORGOTTEN_SUB_TITLE'); ?></h2>
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                        <p><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_FORGOTTEN_DESCRIPTION'); ?></p>

                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?></label>
                            <input type="email" name="email" value="<?php echo $email; ?>" class="form-control" required />
                        </div>

                        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONTINUE'); ?>" class="btn btn-primary" />
                        <a href="<?php echo $this->url->link('account/login')?>" class="pull-right"><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURNING_CUSTOMER_DESCRIPTION'); ?></a>
                    </form>
                </div>
            </div>
        </form>
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

<?php echo $footer ?>

