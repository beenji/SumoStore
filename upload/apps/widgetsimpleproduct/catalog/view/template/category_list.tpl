
<?php if (is_array($products) && count($products)): ?>

<div class="row product-filter">
    <div class="col-md-4">
        <p class="input-label">
            <a href="#display::change" onclick="display('list'); return false;"><span class="glyphicon glyphicon-th-list"></span></a>
            <a href="#display::change" onclick="display('grid'); return false;"><span class="glyphicon glyphicon-th"></span></a>
            <a href="<?php echo $this->url->link('product/compare'); ?>" id="compare-total"><?php echo Sumo\Language::getVar('SUMO_PRODUCT_COMPARE', isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0)?></a>
        </p>
    </div>
    <div class="col-md-4 text-center">
        <select onchange="location = this.value;" class="form-control">
            <?php foreach ($limits as $limits):  ?>
            <option value="<?php echo $limits['href']?>" <?php if (isset($input['data']['limit']) && $limits['value'] == $input['data']['limit']) { echo 'selected'; } ?>><?php echo $limits['text']?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="col-md-4 text-right">
        <select onchange="location = this.value;" class="form-control">
            <?php foreach ($sorts as $sorts): ?>
            <option value="<?php echo $sorts['href']; ?>" <?php if (isset($data['data']['sort']) && $sorts['value'] == $input['data']['sort'] . '-' . $input['data']['order']) { echo 'selected'; } ?>><?php echo $sorts['text']; ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>

<div id="product-list" class="row product-list-type">
    <?php foreach ($products as $list):
    $extra = '';
    if (isset($input['path'])) {
        $extra = 'path=' . $input['path'];
    }
    else {
        $extra = 'path=unknown';
    }
    $link = $this->url->link('product/product', $extra  . '&product_id=' . $list['product_id']);
    if (isset($input['filter_manufacturer_id'])) {
        $link .= '?manufacturer_id=' . $input['filter_manufacturer_id'];
    }
    ?>
    <div class="col-md-4 col-sm-6 product-list-item" url="<?php echo $link?>">
        <div class="product-box">
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
                <p class="product-description"><?php echo substr(strip_tags(html_entity_decode($list['description'])), 0, 300); if (strlen(strip_tags(html_entity_decode($list['description']))) > 300) { echo '...'; }?></p>
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
                <small class="old-price">
                    <?php
                    if ($this->config->get('tax_enabled')) {
                        echo Sumo\Formatter::currency($list['price'] / 100 * $list['tax_percentage'] + $list['price']);
                    }
                    else {
                        echo Sumo\Formatter::currency($list['price']);
                    }
                    ?>
                </small>
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
                <div class="inner-buttons">
                    <a href="<?php echo $link?>#compare" product="<?php echo $list['product_id']?>" class="btn-compare">
                        <?php //echo Sumo\Language::getVar('BUTTON_ADD_TO_COMPARE')?>
                        <i class="icon-compare" title="<?php echo Sumo\Language::getVar('BUTTON_ADD_TO_COMPARE')?>"></i>
                    </a>
                    <!--
                    <a href="<?php echo $link?>#wishlist" product="<?php echo $list['product_id']?>" class="btn-wishlist">
                        <?php //echo Sumo\Language::getVar('BUTTON_ADD_TO_wishlist')?>
                        <i class="icon-wishlist" title="<?php echo Sumo\Language::getVar('BUTTON_ADD_TO_WISHLIST')?>"></i>
                    </a>
                    -->
                    <a href="<?php echo $link?>#to_cart" product="<?php echo $list['product_id']?>" class="btn btn-primary btn-order">
                        <?php //echo Sumo\Language::getVar('BUTTON_ADD_TO_CART')?>
                        <i class="picons-cart" title="<?php echo Sumo\Language::getVar('BUTTON_ADD_TO_CART')?>"></i>
                    </a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
    <?php endforeach ?>
</div>

<div class="pull-right" id="pagination">
    <?php echo $pagination?>
</div>

<?php else: ?>
<div class="alert alert-info"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS_NONE')?></div>
<?php endif ?>

<div class="clearfix"></div>

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
