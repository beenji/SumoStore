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
        <h1><?php echo Sumo\Language::getVar('SUMO_DOWNLOAD_TITLE')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <?php if ($downloads) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_DOWNLOADS_ALLOWED'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_FILENAME'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_SIZE'); ?></th>
                    <th style="width: 25px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($downloads as $download) { ?>
                <tr>
                    <td><?php echo $download['order_id']; ?></td>
                    <td><?php echo $download['date']; ?></td>
                    <td><?php echo $download['remaining']; ?></td>
                    <td><?php echo $download['name']; ?></td>
                    <td><?php echo $download['size']; ?></td>
                    <td><a href="<?php echo $download['download']; ?>"><i style="font-size: 16px;" class="glyphicon glyphicon-download icn-visible"></i></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
        <div class="alert alert-info">
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_DOWNLOADS_ACCOUNT'); ?></p>
        </div>
        <?php } ?>
        
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