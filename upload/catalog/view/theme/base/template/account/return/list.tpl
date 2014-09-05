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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_RETURN_TITLE')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <?php if ($returns) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_ID_ABBR'); ?></th>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></th>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></th>
                    <th style="width: 150px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></th>                    
                    <th style="width: 25px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($returns as $return) { ?>
                <tr>
                    <td><?php echo $return['return_id']; ?></td>
                    <td><a href="<?php echo $return['order']; ?>"><?php echo $return['order_id']; ?></a></td>
                    <td><?php echo $return['name']; ?></td>
                    <td><?php echo $return['date']; ?></td>
                    <td><?php echo $return['status']; ?></td>
                    <td><a href="<?php echo $return['view']; ?>"><i style="font-size: 16px;" class="glyphicon glyphicon-search icn-visible"></i></a></td>
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
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_RETURNS'); ?></p>
        </div>
        <?php } ?>
        
        <p>
            <a href="<?php echo $insert; ?>" class="btn btn-primary">Toevoegen</a>
        </p>
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