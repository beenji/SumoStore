<?php namespace Sumo; echo $header?>
<div class="container" itemscope itemtype="http://schema.org/Product">
    <div class="product-container">
        <h1 itemprop="name"><?php echo $heading_title; ?></h1>

        <ol class="breadcrumb" itemscope itemtype="http://schema.org/WebPage">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '" itemprop="breadcrumb">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach ?>
            <li class="product-meta pull-right">
                <?php if ($manufacturer) { ?>
                <div class="product-meta-item">
                    <span><?php echo Language::getVar('SUMO_NOUN_MANUFACTURER_SINGULAR') ?>:</span>
                    <a href="<?php echo $manufacturer_link; ?>" itemprop="brand"><?php echo $manufacturer; ?></a>
                </div>
                <?php } ?>
                <div class="product-meta-item" itemscope itemtype="http://schema.org/Offer">
                    <span><?php echo Language::getVar('SUMO_NOUN_STOCK') ?>:</span>
                    <span itemprop="availability"><?php echo $stock; ?></span>
                </div>
                <div class="product-meta-item">
                    <span><?php echo Language::getVar('SUMO_NOUN_MODEL') ?>:</span>
                    <span itemprop="model"><?php echo !empty($product_info['model_2']) ? $product_info['model_2'] : $product_info['model']; ?></span>
                </div>
                <?php
                $skus = array('sku', 'upc', 'ean', 'jan', 'isbn', 'mpn');
                foreach ($skus as $sku) {
                    if ($product_info[$sku . '_visible'] && $product_info[$sku]) {
                        echo '<div class="product-meta-item"><span>' . strtoupper($sku) . ':</span> <span itemprop="' . $sku . '">' . $product_info[$sku] . '</span></div>';
                    }
                } ?>

                <div class="product-meta-item">
                    <span><?php echo Language::getVar('SUMO_PRODUCT_DETAILS_VIEWS') ?>:</span>
                    <?php echo $product_info['viewed']; ?>
                </div>
            </li>
        </ol>

        <div class="row">
            <div class="col-md-6">
                <ul class="image-slider gallery list-unstyled cS-hidden">
                    <li <?php if (!empty($thumb)): ?>data-thumb="<?php echo $thumb?>"<?php endif?>>
                        <a href="javascript:void(0)"><img src="<?php echo $popup?>"></a>
                    </li>
                    <?php if ($images):
                        foreach ($images as $image): ?>
                    <li data-thumb="<?php echo $image['thumb']?>">
                        <a href="javascript:void(0)"><img src="<?php echo $image['popup']?>" alt="<?php echo $product_info['name']?>"></a>
                    </li>
                    <?php endforeach;
                    endif; ?>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="review-preview">
                    <span class="review" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        <div class="review-stars">
                            <div class="for-search-engines-only">
                                <br>Rated <span itemprop="ratingValue"><?php echo (!empty($rating) ? $rating : 0) ?>/5</span>
                                based on <span itemprop="reviewCount"><?php echo (!empty($product_info['reviews']) ? $product_info['reviews'] : 0) ?></span> customer reviews
                            </div>
                            <div class="for-humans-only">
                                <a href="#reviews" onclick="$('html,body').animate({scrollTop:$('#review').offset().top - 10}); return false;">
                                    <img src="catalog/view/theme/<?php echo $this->config->get('template')?>/image/stars/stars1-<?php echo $rating ?>.png" onerror="this.src='catalog/view/theme/base/image/stars/stars1-<?php echo $rating?>.png'; return;"alt="<?php echo $product_info['reviews']?>">
                                    <span><?php echo Language::getVar('SUMO_PRODUCT_REVIEW_RATING', array((string)$rating ? $rating : 0, (string)$product_info['reviews'] ? $product_info['reviews'] : 0))?></span>
                                </a>
                            </div>
                        </div>
                    </span>
                </div>
                <div class="clearfix"><br /></div>
                <?php
                    if (empty($product_info['description'])) {
                        if (!empty($product_info['meta_description'])) {
                            $intro = $product_info['meta_description'];
                        }
                    }
                    else {
                        $intro = html_entity_decode($product_info['description']);
                    }
                    if (!empty($intro)): ?><h3><?php echo Language::getVar('SUMO_PRODUCT_DESCRIPTION')?></h3><?php endif?>
                <div class="full-description" itemprop="description">
                    <?php echo $intro ?>
                </div>

                <form method="post" id="product_form">
                    <div class="options" id="product-options">
                        <?php if (!empty($options)) { ?>
                        <h3><?php echo Language::getVar('SUMO_PRODUCT_OPTION_PLURAL') ?></h3>

                        <?php foreach ($options as $list): if ($list['type'] == 'file') { continue; }?>
                        <div class="option-<?php echo $list['type']?>" id="option-<?php echo $list['option_id']?>" data-option="<?php echo $list['option_id']?>">
                            <div class="form-group">
                                <label class="control-label"><?php echo $list['name']?></label>
                                <?php switch ($list['type']):
                                    case 'select': ?>
                                    <select name="option[<?php echo $list['option_id']?>]" class="form-control">
                                        <option value=""><?php echo Language::getVar('SUMO_NOUN_CHOOSE')?></option>
                                        <?php foreach ($list['product_option_value'] as $option): ?>
                                        <option value="<?php echo $option['value_id']?>" data-price="<?php echo $option['price']?>" data-price-prefix="<?php echo $option['price_prefix']?>" data-weight="<?php echo $option['weight']?>" data-weight-prefix="<?php echo $option['weight_prefix']?>" data-type="<?php echo $list['type']?>"><?php echo $option['name']?> <?php if ((($this->config->get('customer_price') && $this->customer->isLogged()) || !$this->config->get('customer_price')) && !empty($option['price']) && $option['price'] != 0.00) { echo '(' . $option['price_prefix'] . ' ' . Formatter::currency(round($option['price'] + ($this->config->get('tax_enabled') ? $option['price'] / 100 * $product_info['tax_percentage'] : 0), 2)) . ')'; } ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <?php break;

                                    case 'radio':?>
                                    <div class="">
                                        <?php foreach ($list['product_option_value'] as $option): ?>
                                        <label class="radio-inline"><input type="radio" name="option[<?php echo $list['option_id']?>]" value="<?php echo $option['value_id']?>" data-price="<?php echo $option['price']?>" data-price-prefix="<?php echo $option['price_prefix']?>" data-weight="<?php echo $option['weight']?>" data-weight-prefix="<?php echo $option['weight_prefix']?>" data-type="<?php echo $list['type']?>"><?php echo $option['name']?> <?php if ((($this->config->get('customer_price') && $this->customer->isLogged()) || !$this->config->get('customer_price')) && !empty($option['price']) && $option['price'] != 0.00) { echo '(' . $option['price_prefix'] . ' ' . Formatter::currency(round($option['price'] + ($this->config->get('tax_enabled') ? $option['price'] / 100 * $product_info['tax_percentage'] : 0), 2)) . ')'; } ?></label><br />
                                        <?php endforeach?>
                                    </div>
                                    <?php break;

                                    case 'checkbox': ?>
                                    <div class="">
                                        <?php foreach ($list['product_option_value'] as $option): ?>
                                        <label class="checkbox-inline"><input type="checkbox" name="option[<?php echo $list['option_id']?>][]" value="<?php echo $option['value_id']?>" data-price="<?php echo $option['price']?>" data-price-prefix="<?php echo $option['price_prefix']?>" data-weight="<?php echo $option['weight']?>" data-weight-prefix="<?php echo $option['weight_prefix']?>" data-type="<?php echo $list['type']?>"><?php echo $option['name']?> <?php if ((($this->config->get('customer_price') && $this->customer->isLogged()) || !$this->config->get('customer_price')) && !empty($option['price']) && $option['price'] != 0.00) { echo '(' . $option['price_prefix'] . ' ' . Formatter::currency(round($option['price'] + ($this->config->get('tax_enabled') ? $option['price'] / 100 * $product_info['tax_percentage'] : 0), 2)) . ')'; } ?></label><br />
                                        <?php endforeach ?>
                                    </div>
                                    <?php break;

                                    case 'text': ?>
                                    <input type="text" name="option[<?php echo $list['option_id']?>]" required class="form-control">
                                    <?php break;

                                    case 'textarea': ?>
                                    <textarea name="option[<?php echo $list['option_id']?>]" required class="form-control" rows="5"></textarea>
                                    <?php break;

                                    case 'file': ?>
                                    <input type="file" name="option[<?php echo $list['option_id']?>]" class="">
                                    <?php break;

                                    case 'date':
                                    case 'datetime':
                                    case 'time': ?>
                                    <input type="text" name="option[<?php echo $list['option_id']?>]" class="form-control <?php echo $list['type']?>-picker">
                                    <?php break;
                                endswitch; ?>
                            </div>
                        </div>
                        <?php endforeach;
                        } else { ?><input type="hidden"> <?php } ?>
                    </div>
                    <div class="product-offer-holder">
                        <?php $banner = $this->getChild('app/widgetsimplesidebar', array('type' => 'usp', 'location' => 'product'));?>
                        <div class="col-md-5 col-sm-12">
                            <?php
                            if ($price) {
                                if (!isset($special) || !$special) {
                                    echo '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span class="new-price" itemprop="price" id="product-price" >' . $price . '</span></span>';
                                }
                                else {
                                    echo '<span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">';
                                    echo '<span class="new-price"  id="product-price" itemprop="lowPrice">' . $special . '</span> ';
                                    echo '<span class="old-price" itemprop="highPrice">' . $price . '</span>';
                                    if ($settings['display_percentage']) {
                                        echo '<div class="saved"><span class="you-save">' . Language::getVar('SUMO_PRODUCT_DETAILS_YOU_SAVE') . ':</span> <span class="save-percent">' . $percent_savings . '%</span></div>';
                                    }
                                }
                                echo '</span>';
                            }
                            if ($this->config->get('tax_display') && $tax) {
                                echo '<div class="tax"><span class="tax-price">';
                                if ($this->config->get('tax_enabled')) {
                                    echo Language::getVar('SUMO_PRICE_EX_TAX') . ': <span id="product-tax">' . $tax;
                                }
                                else {
                                    echo Language::getVar('SUMO_NOUN_TAX_AMOUNT') . ': <span id="product-tax">' . Formatter::currency($price_raw / 100 * $product_info['tax_percentage']);
                                }
                                echo '</span></span></div>';
                            }
                            if ($discounts) {
                                echo '<div class="discount">';
                                foreach ($discounts as $discount) {
                                    echo '<span class="price-tax">' . Language::getVar('SUMO_PRODUCT_DISCOUNT_TEXT', array($discount['quantity'], $discount['price'])) . '</span><br />';
                                }
                                echo '</div>';
                            }
                            ?>
                            <div class="add-to-cart">
                                <div id="qty-dec"><input type="button" class="dec btn" value="-" /></div>
                                <div id="qty"><input type="text" name="quantity" size="3" class="i-d-quantity input-mini form-control" value="<?php echo $minimum; ?>" /></div>
                                <div id="qty-inc"><input type="button" class="inc btn" value="+" /></div>
                            </div>
                            <?php if ($minimum > 1): ?>
                            <div class="minimum">
                                <?php echo Language::getVar('SUMO_PRODUCT_MINIMUM', $minimum) ?>
                            </div>
                            <?php endif;
                            if (!empty($banner)): ?>
                            <div class="order-button">
                                <input type="button" value="<?php echo Language::getVar('BUTTON_PRODUCT_PAGE_ADD_TO_CART') ?>" product="<?php echo $product_id?>" id="button-cart" class="btn btn-md btn-primary btn-order btn-lg pull-right" />
                            </div>
                            <?php endif ?>
                        </div>
                        <div class="col-md-7">
                            <?php if (!empty($banner)) { echo $banner; } ?>

                            <div class="wishlist-compare-friend">
                                <div class="prod-wishlist">
                                    <a onclick="addToWishList('<?php echo $product_id; ?>');">
                                        <span class="icon-wishlist"></span><?php echo Language::getVar('BUTTON_ADD_TO_WISHLIST'); ?>
                                    </a>
                                </div>
                                <div class="prod-compare">
                                    <a onclick="addToCompare('<?php echo $product_id; ?>');">
                                        <span class="icon-compare"></span><?php echo Language::getVar('BUTTON_ADD_TO_COMPARE') ?>
                                    </a>
                                </div>
                                <div class="prod-friend">
                                    <a href="mailto:?subject=<?php echo $heading_title; ?>&amp;body=<?php echo $heading_title; ?>: <?php echo $this->url->link('product/product', 'path=unknown&product_id=' . $product_id); ?>">
                                        <span class="icon-friend"></span><?php echo Language::getVar('SUMO_PRODUCT_SEND_TO_FRIEND') ?>
                                    </a>
                                </div>
                            </div>
                            <?php if (empty($banner)): ?>
                            <div class="order-button">
                                <input type="button" value="<?php echo Language::getVar('BUTTON_PRODUCT_PAGE_ADD_TO_CART') ?>" product="<?php echo $product_id?>" id="button-cart" class="btn btn-md btn-primary btn-order btn-lg" />
                            </div>
                        <?php endif?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php $count = 0; foreach ($attributes as $list): ?>
                <h3><?php echo $list['name']?></h3>
                <ul>
                <?php foreach ($list['attribute'] as $attr): ?>
                    <li><?php echo $attr['name']?></li>
                <?php endforeach?>
                </ul>
                <?php $count++; if ($count < count($attributes)): ?><hr /><?php endif ?>
                <?php endforeach ?>
            </div>
            <div class="col-md-6">
                <h3><?php echo Language::getVar('SUMO_PRODUCT_REVIEW')?></h3>
                <div id="review"></div>
            </div>
        </div>
    </div>

    <?php echo $this->getChild('app/widgetsimpleproduct', array('type' => 'related', 'product_id' => $product_info['product_id'], 'limit' => 6, 'title' => Language::getVar('SUMO_NOUN_RELATED_PRODUCTS'), 'class' => 'product-bottom-related'))?>

</div>
<script type="text/javascript" src="admin/view/js/bootstrap/bootstrap.datetimepicker.js"></script>
<script type="text/javascript">
$(function(){
    $('#qty-dec').on('click', function() {
        var cur = parseInt($('.i-d-quantity').val());
        if (cur >= <?php echo $minimum + 1; ?>) {
            cur--;
        }
        else {
            cur = <?php echo $minimum; ?>;

        }
        $('input[name="quantity"]').removeClass('has-error alert-danger');
        $('.i-d-quantity').val(cur);
        $('#product-options :input').trigger('change');
    });
    $('#qty-inc').on('click', function() {
        var cur = parseInt($('.i-d-quantity').val());
        if (cur >= <?php echo $minimum; ?>) {
            cur++;
        }
        else {
            cur = <?php echo $minimum; ?>;
        }
        $('input[name="quantity"]').removeClass('has-error alert-danger');
        $('.i-d-quantity').val(cur);
        $('#product-options :input').trigger('change');
    });
    $('[name="quantity"]').on('keyup', function() {
        $('#product-options :input').trigger('change');
    })
    $('.date-picker').datetimepicker({
        format: '<?php echo Formatter::dateFormatToJS()?>',
        autoclose: true,
        todayHighlight: true,
        startDate: '<?php echo Formatter::date(time())?>',
        minView: 2,
        maxView: 2
    })
    $('.time-picker').datetimepicker({
        format: 'hh:ii',
        autoclose: true,
        minView: 0,
        maxView: 0,
        startView: 0
    })
    $('.datetime-picker').datetimepicker({
        format: '<?php echo Formatter::dateFormatToJS()?> hh:ii',
        autoclose: true,
        minView: 0,
        maxView: 2
    });
    $('#button-cart').on('click', function(e) {
        e.preventDefault();
        var hasError = false;
        if ($('input[name="quantity"]').val() <= 0) {
            $('input[name="quantity"]').addClass('has-error alert-danger');
            return false;
        }
        else {
            $('input[name="quantity"]').removeClass('has-error alert-danger');
        }
        var elements = $('#product_form :input');
        var last = elements.length - 1;
        var checked = [];
        $.each(elements, function(i) {
            if ($(this).attr('name') == undefined || $(this).attr('name') == 'quantity') {
                //
            }
            else
            if ($(this).data('type') == 'radio') {

                if ($(this).filter(':checked').val() == undefined || !$(this).filter(':checked').val() || $(this).filter(':checked').val() == '') {
                    if ($.inArray($(this).attr('name'), checked) >= 0) {
                        //

                    }
                    else {
                        $(this).closest('.form-group').removeClass('has-success').addClass('has-error');
                    }

                }
                else {
                    $(this).closest('.form-group').removeClass('has-error').addClass('has-success');
                    hasError = false;
                }
                if ($(this).closest('.form-group').hasClass('has-error')) {
                    hasError = true;
                }
                checked.push($(this).attr('name'));
            }
            else if ($(this).val() == undefined || $(this).val() == '' || $(this).val().length == 0) {
                $(this).closest('.form-group').removeClass('has-success').addClass('has-error');
                hasError = true;
            }
            else {
                $(this).closest('.form-group').removeClass('has-error').addClass('has-success');
            }

            if (i == last) {
                if (!hasError) {
                    addToCart(<?php echo $product_id?>, null, $('#product_form').serialize());
                }
                else {
                    return false;
                }
            }
        })
    })

    <?php
    // Kinda dynamic review fetcher
    $reviewType = $this->config->get('review_app');
    if (empty($reviewType)): ?>
    $.post('?route=product/product/review&product_id=<?php echo $product_info['product_id']?>', function(resp) {
        $('#review').html(resp);
    })
    <?php else: ?>
    $.post('app/<?php echo $reviewType?>', {id: <?php echo $product_info['product_id']?>}, function(resp) {
        $('#review').html(resp);
    })
    <?php endif ?>

    <?php if (($this->config->get('customer_price') && $this->customer->isLogged()) || !$this->config->get('customer_price')):  ?>
    $('#product-options :input').on('change', function() {
        var tax = <?php echo !empty($tax_raw) ? $tax_raw : $product_info['price']?>;
        var tax_percentage = <?php echo $this->config->get('tax_enabled') ? $product_info['tax_percentage'] : 0?>;
        var price = parseFloat(<?php echo $price_raw?>);
        var extra = null;
        $('#product-options :input').each(function() {
            if (!$(this).is(':checked') && !$(this[this.selectedIndex]).val()) {
                return;
            }
            if ($(this).is(':checked')) {
                var elem = $(this);
            }
            else {
                var elem = $('[value=' + $(this).val() + ']', this);
            }
            var thisPrice = elem.data('price');
            if (thisPrice == undefined || !thisPrice || thisPrice == '') {
                return;
            }
            if (elem.data('price-prefix') == '-') {
                price -= parseFloat(thisPrice);
            }
            else {
                price += parseFloat(thisPrice);
            }
        })
        <?php if ($this->config->get('tax_enabled')): ?>

        $('#product-tax').html(parseFloat(price * parseInt($('input[name="quantity"]').val())).formatMoney());
        <?php endif?>
        price = parseFloat(price + ((price / 100) * tax_percentage));
        price = price * parseInt($('input[name="quantity"]').val());
        $('#product-price').html(parseFloat(price).formatMoney());
    })

    <?php endif?>
})
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};
Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator, currencySymbol) {
    // check the args and supply defaults:
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
    decSeparator = decSeparator == undefined ? "," : decSeparator;
    thouSeparator = thouSeparator == undefined ? "." : thouSeparator;
    currencySymbol = currencySymbol == undefined ? "<?php echo Formatter::$currency_info['symbol_left']?>" : currencySymbol;

    // I'll probably burn in hell for this, but JavaScripts idiotic
    // way of handling floating points forces us to do it this way
    var n = this.toFixed(decPlaces + 1);

    if (n.substr(n.length - 1, 1) == '5') {
        n = n.substr(0, n.length - 1) + '6';
    }

    n = parseFloat(n);

    var sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;

    return sign + currencySymbol + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};

</script>
<?php echo $footer?>
