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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_TRANSACTION_TITLE')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <?php if ($transactions) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 120px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_TRANSACTION_ID'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></th>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></th>
                    <th style="width: 100px;" class="text-right"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_SHORT'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction) { ?>
                <tr>
                    <td><?php echo $transaction['transaction_id']; ?></td>
                    <td><?php echo $transaction['description']; ?></td>
                    <td><?php echo $transaction['date_added']; ?></td>
                    <td class="text-right"><?php echo $transaction['amount']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <?php if ($pagination) { ?>
        <div class="pagination">
            <?php echo $pagination; ?>
        </div>
        <?php } ?>

        <?php } else { ?>
        <div class="alert alert-info">
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_TRANSACTIONS'); ?></p>
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