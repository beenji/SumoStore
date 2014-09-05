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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_WISHLIST_TITLE')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>

        <?php if (!empty($success)) { ?>
        <div class="alert alert-success">
            <p><?php echo $success; ?></p>
        </div>
        <?php } ?>

        <?php if ($products) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></th>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?></th>
                    <th style="width: 100px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK'); ?></th>
                    <th style="width: 100px;" class="text-right"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></th>
                    <th style="width: 60px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) { ?>
                <tr>
                    <td style="width: 70px;"><img class="small-thumb" src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></td>
                    <td><a href="<?php echo $product['view']; ?>"><strong><?php echo $product['name']; ?></strong></a></td>
                    <td><?php echo $product['model']; ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td class="text-right">
                        <?php if (empty($product['special'])) { ?>
                        <?php echo $product['price']; ?>
                        <?php } else { ?>
                        <s><?php echo $product['price']; ?></s><br />
                        <strong style="color: #9ed607;"><?php echo $product['special']; ?></strong>
                        <?php } ?>
                    </td>
                    <td class="text-right">
                        <a href="<?php echo $product['view']; ?>"><i style="font-size: 16px;" class="glyphicon glyphicon-share-alt icn-visible"></i></a>
                        <a href="<?php echo $product['remove']; ?>"><i style="font-size: 16px;" class="glyphicon glyphicon-remove-circle icn-visible"></i></a>
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
            <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_PRODUCTS_WISHLIST'); ?></p>
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