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
            <div class="col-md-12">
                <h1><?php echo $heading_title?></h1>

                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $crumb): ?>
                    <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                    <?php endforeach ?>
                </ol>
                <div class="category-info manufacturer-info">
                    <?php if (!empty($thumb)): ?>
                    <div class="image pull-left"><img src="<?php echo $thumb?>" alt="<?php echo $heading_title?>"></div>
                    <?php endif ?>

                    <?php if (!empty($description)): ?>
                    <p><?php echo $description?></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="<?php echo $this->url->link('product/manufacturer')?>#"><?php echo Sumo\Language::getVar('SUMO_NOUN_MANUFACTURER_OVERVIEW')?></a></li>
                    <?php foreach ($manufacturers as $letter => $unused): ?>
                    <li><a href="<?php echo $this->url->link('product/manufacturer')?>#<?php echo $letter?>"><?php echo $letter?></a></li>
                    <?php endforeach ?>
                </ol>
            </div>

            <?php foreach ($manufacturers as $letter => $data):?>
            <div class="col-md-12">
                <h2><a name="<?php echo $letter?>"></a><?php echo $letter?></h2>
            </div>
            <div class="col-md-12 product-container">
                <?php foreach ($data['manufacturer'] as $list): ?>
                <div class="product-list-item product-box col-md-4 col-sm-6">
                    <div class="product-image">
                        <?php if (empty($list['image'])): 
                        $list['image'] = 'no_image.jpg';
                        endif ?>
                        <a href="<?php echo $list['href']?>">
                            <img src="<?php echo $this->model_tool_image->resize($list['image'], $this->config->get('image_product_width'), $this->config->get('image_product_height'))?>" alt="<?php echo $list['name']?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <h3><a href="<?php echo $list['href']?>"><span class="crop-name"><?php echo $list['name']?></span></a></h3>
                        <?php if (isset($data['description'])): ?>
                        <p class="product-description"><?php echo substr(strip_tags(html_entity_decode($list['description'])), 0, 300); if (strlen(strip_tags(html_entity_decode($list['description']))) > 300) { echo '...'; }?></p>
                        <?php endif?>
                    </div>
                </div>
                <?php endforeach?>
            </div>
            <?php endforeach?>
        </div>
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
<script type="text/javascript">
$(function() {
    $('#product-list .product-list-item').each(function() {
        $(this).on('click', function(e) {
            if ($(e.target).is('a')) {
                return;
            }
            else {
                window.location = $(this).attr('url');
            }
        })
    })

    var defaultDisplay = localStorage.getItem('wsp_category');
    if (!defaultDisplay || defaultDisplay == undefined || defaultDisplay.length == 0) {
        defaultDisplay = '<?php echo $this->config->get('catalog_display_type')?>';
    }
    display(defaultDisplay);
})

function display(type) {
    localStorage.setItem('wsp_category', type);
    $('#product-list').removeClass('product-list-type-grid product-list-type-list').addClass('product-list-type-' + type);

    $('#product-list .product-box').each(function() {
        var desc = $(this).find('.product-description');
        if (!desc.attr('original')) {
            desc.attr('original', desc.html());
        }
        var original = desc.attr('original');

        var title = $(this).find('.crop-name');
        if (!title.attr('original')) {
            title.attr('original', title.html());
        }
        var originalTitle = title.attr('original');

        if (type == 'grid') {
            /*$(this).removeClass('col-md-12').addClass('col-sm-6 col-md-4');
            $(this).find('div').each(function() {
                $(this).removeClass('pull-left col-md-2 col-md-3 col-md-7');
            })*/
            var newText = original.substring(0, 170);
            if (original.length > 170) {
                newText += '...';
            }
            desc.html(newText);

            var newTitle = originalTitle.substr(0, 23);
            if (originalTitle.length > 23) {
                newTitle += '...';
            }
            title.html(newTitle);

        }
        else {
            var isFirst = true;
            /*$(this).removeClass('col-sm-6 col-md-4').addClass('col-md-12');
            $(this).find('.product-image').addClass('col-md-2');
            $(this).find('.product-info').addClass('col-md-7');
            $(this).find('.product-buttons').addClass('col-md-3');*/
            var newText = original.substring(0, 265);
            if (original.length > 265) {
                newText += '...';
            }
            desc.html(newText);

            title.html(originalTitle);
        }
    })
}
</script>

<?php echo $footer ?>
