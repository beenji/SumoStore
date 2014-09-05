<?php if (!isset($manufacturers) || !count($manufacturers)) { return; } $this->load->model('tool/image'); ?>


    <?php if (isset($input['title'])):?>
        <h3><?php echo $input['title']?></h3>
    <?php endif?>
    <?php if (isset($input['class']) || count($manufacturers) > 3): ?>
        <div class="<?php echo !empty($input['class']) ? $input['class'] : ''?> <?php echo count($manufacturers) > 3 ? 'product-slider' : ''?>">
    <?php endif?>

    <?php foreach ($manufacturers as $list): $link = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $list['manufacturer_id'])?>
    <div class="product-list-item product-box col-md-4 col-sm-6" url="<?php echo $link?>">
        <div class="product-image">
            <?php if (empty($list['image'])): 
            $list['image'] = 'no_image.jpg';
            endif ?>
            <a href="<?php echo $link?>">
                <img src="<?php echo $this->model_tool_image->resize($list['image'], $this->config->get('image_product_width'), $this->config->get('image_product_height'))?>" alt="<?php echo $list['name']?>">
            </a>
        </div>
        <div class="product-info">
            <h3><a href="<?php echo $link?>"><span class="crop-name"><?php echo $list['name']?></span></a></h3>
            <?php if (isset($data['description'])): ?>
            <p class="product-description"><?php echo substr(strip_tags(html_entity_decode($list['description'])), 0, 300); if (strlen(strip_tags(html_entity_decode($list['description']))) > 300) { echo '...'; }?></p>
            <?php endif?>
        </div>
    </div>
    <?php endforeach ?>
<?php if (isset($input['class']) || count($manufacturers) > 3): ?></div><?php endif?>
<div class="clearfix"></div>
