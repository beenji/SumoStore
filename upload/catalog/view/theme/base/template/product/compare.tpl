<?php echo $header; ?>

<div class="container">
    <div class="row">
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
        <?php endif; ?>

        <div class="col-md-9">
            <!-- Compare products -->
            <h1>Productvergelijking</h1>
            <div class="compare row">
                <div class="col-md-3">
                    <ul class="compare-list compare-labels">
                        <li class="p-price"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?>:</li>
                        <li><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?>:</li>
                        <li><?php echo Sumo\Language::getVar('SUMO_NOUN_BRAND'); ?>:</li>
                        <li><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK'); ?>:</li>
                        <li><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_REVIEW'); ?>:</li>
                        <li class="p-description"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_DESCRIPTION'); ?>:</li>
                        <li><?php echo Sumo\Language::getVar('SUMO_NOUN_WEIGHT'); ?>:</li>
                        <li><?php echo Sumo\Language::getVar('SUMO_NOUN_SIZE'); ?>:</li>
                    </ul>
                </div>
                
                <div class="col-md-9">
                    <div class="compare-container">
                        <div style="width: <?php echo sizeof($products) * 329; ?>px;">
                            <?php foreach ($products as $product) { ?>
                            <div class="compare-item">
                                <ul class="compare-list compare-values">
                                    <li class="p-title"><?php echo $product['name']; ?></li>
                                    <li class="p-image"><img src="<?php echo $product['thumb']; ?>" /></li>
                                    <li class="p-price"><?php echo $product['price']; ?></li>
                                    <li><?php echo $product['model']; ?></li>
                                    <li><?php echo $product['manufacturer']; ?></li>
                                    <li><?php echo $product['availability']; ?></li>
                                    <li><img src="catalog/view/theme/base/image/stars/stars1-<?php echo $product['rating']; ?>.png" /></li>
                                    <li class="p-description"><?php echo $product['description']; ?></li>
                                    <li><?php echo $product['weight']; ?>kg</li>
                                    <li><?php echo $product['length']; ?>cm x <?php echo $product['width']; ?>cm x <?php echo $product['height']; ?>cm</li>
                                    <li class="p-footer">
                                        <a class="btn btn-primary" href="<?php echo $product['order']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_ORDER'); ?></a>
                                        <a class="btn btn-secondary" href="<?php echo $product['remove']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                                    </li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>