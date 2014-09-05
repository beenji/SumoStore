<?php if (!isset($products) || !count($products)) { return; } $this->load->model('tool/image'); ?>

    <?php if (isset($input['class']) || count($products) > 3): ?>
        <div class="product-list-type-grid <?php echo !empty($input['class']) ? $input['class'] : ''?> <?php echo count($products) > 3 ? 'product-slider' : ''?>">
    <?php endif?>

    <?php if (isset($input['title'])):?>
        <h4><?php echo $input['title']?></h4>
    <?php endif?>

    <?php foreach ($products as $list):
    if (!is_array($list) || !count($list) || empty($list['product_id'])) {
        //continue;
    }
    $link = $this->url->link('product/product', 'path=unknown&product_id=' . $list['product_id']);
    ?>
    <div class="product-list-item product-box col-md-4 col-sm-6" url="<?php echo $link?>">
        <div class="product-image">
            <?php if (!empty($list['special'])): ?>
            <span class="sale-price"><?php echo Sumo\Language::getVar('SUMO_NOUN_SALE')?></span>
            <?php endif ?>
            <a href="<?php echo $link?>">
                <img src="<?php echo $this->model_tool_image->resize(!empty($list['image']) ? $list['image'] : 'no_image.jpg', $this->config->get('image_category_width'), $this->config->get('image_category_height'))?>" data-original="<?php echo $this->model_tool_image->resize(!empty($list['image']) ? $list['image'] : 'no_image.jpg', $this->config->get('image_category_width'), $this->config->get('image_category_height'))?>" data-alt="<?php if (!empty($list['images'][1])) { echo $this->model_tool_image->resize($list['images'][1], $this->config->get('image_category_width'), $this->config->get('image_category_height')); }?>" alt="<?php echo $list['title']?>">
            </a>
        </div>
        <div class="product-info">
            <h3><a href="<?php echo $link?>"><span class="crop-name"><?php echo $list['name']?></span></a></h3>
            <?php if (isset($data['description'])): ?>
            <p class="product-description"><?php echo substr(strip_tags(html_entity_decode($list['description'])), 0, 300); if (strlen(strip_tags(html_entity_decode($list['description']))) > 300) { echo '...'; }?></p>
            <?php endif?>
        </div>
        <div class="product-buttons">
            <?php
            if (!$this->config->get('customer_display_price') || ($this->customer->loggedIn())):
            if (!$list['special']): ?>
            <big class="price">
                <?php
                if ($this->config->get('tax_enabled')) {
                    echo Sumo\Formatter::currency($list['price'] / 100 * $list['tax_percentage'] + $list['price']);
                }
                else {
                    echo Sumo\Formatter::currency($list['price']);
                }
                ?>
            </big>
            <?php else: ?>
            <big class="old-price">
                <?php
                if ($this->config->get('tax_enabled')) {
                    echo Sumo\Formatter::currency($list['price'] / 100 * $list['tax_percentage'] + $list['price']);
                }
                else {
                    echo Sumo\Formatter::currency($list['price']);
                }
                ?>
            </big>
            <big class="new-price">
                <?php
                if ($this->config->get('tax_enabled')) {
                    echo Sumo\Formatter::currency($list['special'] / 100 * $list['tax_percentage'] + $list['special']);
                }
                else {
                    echo Sumo\Formatter::currency($list['special']);
                }
                ?>
            </big>
            <?php endif;
            endif ?>
            <?php if (isset($input['compare'])): ?>
            <a href="<?php echo $link?>#compare" product="<?php echo $list['product_id']?>" class="btn btn-primary btn-compare">
                <?php //echo Sumo\Language::getVar('BUTTON_ADD_TO_COMPARE')?>
                <i class="picons-justice" title="<?php echo Sumo\Language::getVar('BUTTON_ADD_TO_COMPARE')?>"></i>
            </a>
            <?php endif?>
            <a href="<?php echo $link?>#to_cart" product="<?php echo $list['product_id']?>" class="btn btn-primary btn-order">
                <?php //echo Sumo\Language::getVar('BUTTON_ADD_TO_CART')?>
                <i class="picons-cart" title="<?php echo Sumo\Language::getVar('BUTTON_ADD_TO_CART')?>"></i>
            </a>
        </div>
    </div>
    <?php endforeach ?>
<?php if (isset($input['class']) || count($products) > 3): ?></div><?php endif?>
<div class="clearfix"></div>
