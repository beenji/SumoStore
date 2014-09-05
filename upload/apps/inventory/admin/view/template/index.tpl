<?php echo $header; ?>

<script type="text/javascript">
    var categories = <?php echo $json_categories ?>,
        sessionToken = '<?php echo $token; ?>';
</script>

<form method="get" action="">
    <input type="hidden" name="token" value="<?php echo $token; ?>" />

    <div class="block-flat" style="padding-bottom: 5px; margin: 20px 0 10px;">
        <div class="row" style="margin-top: 0;">
            <div class="<?php if ($filter_store == '') { echo 'col-sm-4'; } else { echo 'col-lg-3 col-sm-6'; } ?> col-filter">
                <div class="form-group">
                    <label class="control-label" for="name"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_NAME'); ?>:</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo $filter_name; ?>">
                </div>
            </div>

            <div class="<?php if ($filter_store == '') { echo 'col-sm-4'; } else { echo 'col-lg-3 col-sm-6'; } ?> col-filter">
                <div class="form-group">
                    <label for="stock-control" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK'); ?>:</label>
                    <div class="row" style="margin-top: 0;">
                        <div class="col-sm-5" style="padding-right: 0;">
                            <select id="stock-control" class="form-control">
                                <option value="">&mdash;</option>
                                <option<?php if ($filter_quantity_type == 'quantity_from') { echo ' selected="selected"'; } ?> value="quantity_from">Meer dan</option>
                                <option<?php if ($filter_quantity_type == 'quantity') { echo ' selected="selected"'; } ?> value="quantity">Exact</option>
                                <option<?php if ($filter_quantity_type == 'quantity_to') { echo ' selected="selected"'; } ?> value="quantity_to">Minder dan</option>
                            </select>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" id="quantity" name="<?php echo $filter_quantity_type; ?>" class="form-control" value="<?php echo $filter_quantity; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="<?php if ($filter_store == '') { echo 'col-sm-4'; } else { echo 'col-lg-3 col-sm-6'; } ?> col-filter">
                <div class="form-group">
                    <label for="store" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STORE'); ?>:</label>
                    <select name="store" id="store" class="form-control">
                        <option value=""><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_ALL')); ?></option>
                        <?php foreach ($stores as $store) { ?>
                        <option value="<?php echo $store['store_id']; ?>"<?php if ((string)$store['store_id'] === $filter_store) { echo ' selected="selected"'; } ?>><?php echo $store['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div id="category_filter" class="col-lg-3 col-sm-6 col-filter"<?php if ($filter_store == '') { ?> style="display: none;"<?php } ?>>
                <div class="form-group">
                    <label for="category" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORY'); ?>:</label>
                    <select name="category" id="category" class="form-control">
                        <option value=""><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_ALL')); ?></option>
                        <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['category_id']; ?>"<?php if ($category['category_id'] == $filter_category) { echo ' selected="selected"'; } ?>><?php echo $category['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <p class="align-right">
        <a class="btn btn-secondary btn-sm" href="<?php echo $reset; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SHOW_ALL'); ?></a>
        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_FILTER_INVENTORY'); ?>" class="btn btn-primary btn-sm" />
    </p>
</form>

<div class="block-flat">
    <?php if ($products) { ?>
    <div class="pull-left">
        <p style="line-height: 26px;">
            <strong><?php echo $total_products; ?></strong> <?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS'); ?>&nbsp; /&nbsp;
            <?php echo Sumo\Language::getVar('SUMO_NOUN_CREDIT_VALUE'); ?>: <strong><?php echo $total_value; ?></strong>&nbsp; /&nbsp;
            <?php echo Sumo\Language::getVar('SUMO_NOUN_DEBET_VALUE'); ?>: <strong><?php echo $total_price; ?></strong>
        </p>
    </div>

    <?php if ($pagination) { ?>
    <div class="pull-right" style="margin-left: 20px;">
        <ul class="pagination pagination-sm" style="margin: 0;">
            <?php echo $pagination; ?>
        </ul>
    </div>
    <?php } ?>

    <div class="pull-right">
        <div class="btn-group">
            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOW'); ?>: <?php echo $limit; ?> <?php echo Sumo\Language::getVar('SUMO_NOUN_RESULTS'); ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo $url; ?>&amp;limit=25">25</a></li>
                <li><a href="<?php echo $url; ?>&amp;limit=50">50</a></li>
                <li><a href="<?php echo $url; ?>&amp;limit=100">100</a></li>
            </ul>

            <a href="<?php echo $export; ?>" class="btn btn-secondary btn-sm"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EXPORT_CSV'); ?></a>
        </div>
    </div>

    <div class="clearfix"></div>

    <table class="table no-border hover table-product" style="margin: 20px 0;">
        <thead class="no-border">
            <tr>
                <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_COST_PRICE'); ?></strong></th>
                <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SELL_PRICE'); ?></strong></th>
                <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PROFIT_PRICE'); ?></strong></th>
                <th class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_SIMPLE'); ?></strong></th>
            </tr>
        </thead>
        <tbody class="no-border-y">
            <?php foreach ($products as $product) {
                $stock = true;
                $total = 0;
                if (isset($product['options']) && is_array($product['options']) && count($product['options'])) {
                    foreach ($product['options'] as $items) {
                        if (isset($items['product_option_value']) && is_array($items['product_option_value']) && count($items['product_option_value'])) {
                            foreach ($items['product_option_value'] as $option) {
                                if ($option['active']) {
                                    $stock = false;
                                    $total += $option['quantity'];
                                }
                            }
                        }
                    }
                }

                if ($product['quantity'] <= 0) {
                    $qclass = 'error';
                }
                else if ($product['quantity'] <= 5) {
                    $qclass = 'warning';
                }
                else {
                    $qclass = 'success';
                }

                if (!$stock) {
                    $qclass = '';
                }
            ?>
            <tr>
                <td style="width: 100px; text-align: center;">
                    <div class="img">
                        <img src="../image/<?php echo $product['image']; ?>" />
                    </div>
                </td>
                <td>
                    <strong><a href="<?php echo $product['edit']; ?>"><?php echo $product['name']; ?></a></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?>: <?php echo $product['model']; ?>
                </td>
                <td style="width: 150px;">
                    <strong><?php echo Sumo\Formatter::currency($product['value'])?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_GROSS')?>
                </td>
                <td style="width: 100px;">
                    <strong><?php echo Sumo\Formatter::currency($product['price'])?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_GROSS')?>
                </td>
                <td style="width: 100px;" class="light">
                    <strong><?php echo Sumo\Formatter::currency($product['price_net'])?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_NET')?>
                </td>
                <td style="width: 100px;">
                    <strong><?php echo @round((($product['price'] - $product['value']) / $product['price']) * 100)?>%</strong>
                </td>
                <td style="width: 100px;" class="light">
                    <strong><?php echo Sumo\Formatter::currency($product['price'] - $product['value'])?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_GROSS')?>
                </td>
                <td style="width: 80px; position: relative;" class="has-<?php echo $qclass?>">
                    <?php if ($stock): ?>
                    <input type="hidden" name="stock_id_<?php echo $product['product_id']?>" value="<?php echo $product['stock_id']?>" />
                    <input type="text" data-stock="<?php echo $product['quantity']; ?>" name="stock_<?php echo $product['product_id']?>" value="<?php echo $product['quantity']?>" class="form-control stock-<?php echo $product['stock_id']?>" data-stock-id="<?php echo $product['stock_id']?>">
                    <?php else: ?>
                    <input type="text" class="form-control open-option input-option-<?php echo $product['product_id']?>" data-option="option-<?php echo $product['product_id']?>" value="<?php echo $total?>">
                    <?php endif?>
                </td>
            </tr>
            <?php if (!$stock):
            foreach ($product['options'] as $items):
                if (isset($items['product_option_value']) && is_array($items['product_option_value']) && count($items['product_option_value'])):
                    foreach ($items['product_option_value'] as $option):
                        if ($option['active']):
                        if ($option['quantity'] <= 0) {
                            $qclass = 'error';
                        }
                        else if ($option['quantity'] <= 5) {
                            $qclass = 'warning';
                        }
                        else {
                            $qclass = 'success';
                        }
                        $price = $product['price'];
                        if ($option['price'] > 0.0000) {
                            if ($option['price_prefix'] == '+') {
                                $price += $option['price'];
                            }
                            else {
                                $price -= $option['price'];
                            }
                        }

                        $priceIn = $price + ($price / 100 * $product['tax_percentage']);
                        ?>
            <tr class="hidden hidden-option option-<?php echo $product['product_id']?>" data-product="<?php echo $product['product_id']?>">
                <td>&nbsp;</td>
                <td><?php echo $items['name']?>: <?php echo $option['name']?></td>
                <td>&nbsp;</td>
                <td style="width: 100px;">
                    <!--<small><?php echo $option['price_prefix'] . Sumo\Formatter::currency($option['price'])?></small><br />-->
                    <strong><?php echo Sumo\Formatter::currency($price)?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_EX')?>
                </td>
                <td style="width: 100px;" class="light">
                    <!--<small><?php echo $option['price_prefix'] . Sumo\Formatter::currency($option['price'] + ($option['price'] / 100 * $product['tax_percentage']))?></small><br />-->
                    <strong><?php echo Sumo\Formatter::currency($priceIn)?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_IN')?>
                </td>
                <td style="width: 100px;">
                    <strong><?php echo @round((($price - $product['value']) / $price) * 100)?>%</strong>
                </td>
                <td style="width: 100px;" class="light">
                    <strong><?php echo Sumo\Formatter::currency($price - $product['value'])?></strong><br />
                    <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_GROSS')?>
                </td>
                <td class="has-<?php echo $qclass?>">
                    <input type="text" name="option_<?php echo $option['value_id']?>" data-option="<?php echo $option['value_id']?>" class="form-control" value="<?php echo $option['quantity']?>">
                </td>
            </tr>
                        <?php endif;
                    endforeach;
                endif;
            endforeach;
            endif;
            } ?>
        </tbody>
    </table>

    <div class="pull-left">
        <p style="line-height: 26px;"><strong><?php echo $total_products; ?></strong> <?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCTS'); ?>. &nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_CREDIT_VALUE'); ?>: <strong><?php echo $total_value; ?></strong>&nbsp; /&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_DEBET_VALUE'); ?>: <strong><?php echo $total_price; ?></strong></p>
    </div>

    <?php if ($pagination) { ?>
    <div class="pull-right" style="margin-left: 20px;">
        <ul class="pagination pagination-sm" style="margin: 0;">
            <?php echo $pagination; ?>
        </ul>
    </div>
    <?php } ?>

    <div class="pull-right">
        <a href="<?php echo $export; ?>" class="btn btn-secondary btn-sm"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EXPORT_CSV'); ?></a>
    </div>
    <?php } else { ?>
    <p class="well" style="margin-bottom: 0;"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_PRODUCTS_MATCH'); ?></p>
    <?php } ?>

    <div class="clearfix"></div>
</div>

<?php echo $footer; ?>
