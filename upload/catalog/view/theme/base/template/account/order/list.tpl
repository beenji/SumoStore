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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_ORDER_TITLE')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <ul class="element-list">
            <?php foreach ($orders as $order) { ?>
            <li>
                <div class="row">
                    <div class="col-md-6">
                        <dl>
                            <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO'); ?>:</dt>
                            <dd><?php echo $order['order_id']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="pull-right">
                            <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</dt>
                            <dd><?php echo $order['status']; ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <dl>
                            <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?>:</dt>
                            <dd><?php echo $order['order_date']; ?></dd>
                        </dl>
                        <dl>
                            <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS'); ?>:</dt>
                            <dd><?php echo $order['products']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl>
                            <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</dt>
                            <dd><?php echo $order['name']; ?></dd>
                        </dl>
                        <dl>
                            <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?>:</dt>
                            <dd><?php echo $order['total']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-4 text-right">
                        <p class="action">
                            <a href="<?php echo $order['view']; ?>" title="Details"><i class="glyphicon glyphicon-search"></i></a>
                        </p>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>
        
        <?php if ($pagination) { ?>
        <div class="pagination">
            <?php echo $pagination; ?>
        </div>
        <?php } ?>
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

<?php echo $footer?>