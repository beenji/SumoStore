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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_REWARD_TITLE'); ?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <?php if ($rewards) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 120px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></th>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></th>
                    <th style="width: 100px;" class="text-right"><?php echo Sumo\Language::getVar('SUMO_NOUN_POINTS'); ?></th>
                    <th style="width: 30px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rewards as $reward) { ?>
                <tr>
                    <td>
                        <?php if ($reward['order_id'] > 0) { ?>
                        <a href="<?php echo $reward['order']; ?>"><?php echo str_pad($reward['order_id'], 6, 0, STR_PAD_LEFT); ?></a>
                        <?php } else { ?>
                        &mdash;
                        <?php } ?>
                    </td>
                    <td><?php echo $reward['description']; ?></td>
                    <td><?php echo $reward['date_added']; ?></td>
                    <td class="text-right"><?php echo $reward['points']; ?></td>
                    <td>
                        <?php if ($reward['order_id'] > 0) { ?>
                        <a href="<?php echo $reward['order']; ?>"><i class="glyphicon glyphicon-share-alt icn-visible"></i></a>
                        <?php } ?>
                    </td>
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
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_REWARDS'); ?></p>
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