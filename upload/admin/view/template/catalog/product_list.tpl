<?php echo $header; ?>

<div class="page-head-actions align-right" style="max-width: 300px;">
    <form action="">
        <div class="input-group" style="margin-bottom: 5px;">
            <span class="input-group-addon">
                <i class="fa fa-search"></i>
            </span>
            <input type="search" name="search" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_QUICK_SEARCH_PLURAL'); ?>" class="form-control" value="<?php if (isset($search)) { echo $search; }?>">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCH_PLURAL'); ?></button>
            </span>
        </div>
        <p style="padding-top: 4px;"><a href="#search-advanced" data-toggle="collapse"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADVANCED_SEARCH_PLURAL'); ?></a></p>
    </form>
</div>

<div class="clearfix"></div>

<div id="search-advanced"<?php if (!$advanced_search) { ?> class="collapse collapsed"<?php } else { ?> class="in"<?php } ?>>
    <form role="form" action="<?php echo $current_url; ?>" class="form-horizontal" method="get" id="advanced_filter_form">
        <input type="hidden" name="token" value="<?php echo $token; ?>" />

        <div class="block-flat" style="padding-bottom: 5px; margin: 20px 0 10px;">
            <div class="row" style="margin-top: 0;">
                <div class="col-sm-6 col-lg-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label class="control-label col-sm-5" for="name"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_NAME'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_name" id="name" value="<?php echo $filter_name; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-5" for="id"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_model" id="id" value="<?php echo $filter_model; ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?>:</label>
                        <div class="col-sm-3" style="padding-right: 0;">
                            <input type="text" name="filter_price_from" value="<?php echo $filter_price_from; ?>" class="form-control">
                        </div>
                        <label class="control-label col-sm-1 align-center" style="padding: 7px 0 0;">&mdash;</label>
                        <div class="col-sm-3" style="padding-left: 0;">
                            <input type="text" name="filter_price_to" value="<?php echo $filter_price_to; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stock-control" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK'); ?>:</label>
                        <div class="col-sm-3" style="padding-right: 0;">
                            <select name="stock-control" id="stock-control" class="form-control">
                                <option value="">&mdash;</option>
                                <option<?php if ($filter_stock_control == 'stock_from') { echo ' selected="selected"'; } ?> value="from"><?php echo Sumo\Language::getVar('SUMO_NOUN_MORE_THAN'); ?></option>
                                <option<?php if ($filter_stock_control == 'stock') { echo ' selected="selected"'; } ?> value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_EXACT'); ?></option>
                                <option<?php if ($filter_stock_control == 'stock_to') { echo ' selected="selected"'; } ?> value="to"><?php echo Sumo\Language::getVar('SUMO_NOUN_LESS_THAN'); ?></option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="filter_<?php echo $filter_stock_control; ?>" value="<?php echo $filter_stock; ?>" id="stock" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="row" style="margin-top: 0;">
                        <div class="col-sm-6 col-lg-12">
                            <div class="form-group" style="margin-top: 0;">
                                <label for="category" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORY'); ?>:</label>
                                <div class="col-sm-7">
                                    <select name="filter_category" id="category" class="form-control">
                                        <option value=""><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE')); ?></option>
                                        <?php foreach ($stores as $store) { ?>
                                        <?php if (!empty($categories[$store['store_id']])) { ?>
                                            <optgroup label="<?php echo $store['name']; ?>">
                                                <?php foreach ($categories[$store['store_id']] as $category): ?>
                                                <option<?php if ($category['category_id'] == $filter_category) { echo ' selected="selected"'; } ?> value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-12">
                            <div class="form-group" style="margin-top: 0;">
                                <label for="brand" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_BRAND'); ?>:</label>
                                <div class="col-sm-7">
                                    <select name="filter_brand" id="brand" class="form-control">
                                        <option value=""><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE')); ?></option>
                                        <?php foreach ($manufacturers as $manufacturer): ?>
                                        <option<?php if ($manufacturer['manufacturer_id'] == $filter_brand) { ?> selected="selected"<?php } ?> value="<?php echo $manufacturer['manufacturer_id']; ?>"><?php echo $manufacturer['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label for="supplier" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL_SUPPLIER'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_model_supplier" id="supplier" class="form-control" value="<?php echo $filter_model_supplier; ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="align-right">
            <a class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
            <button class="btn btn-primary" type="submit"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SHOW_RESULTS'); ?></button>
        </p>
    </form>
</div>

<!-- TABS -->
<ul class="nav nav-tabs">
    <?php
    $i = 0;
    foreach ($stores as $store) {
    ?>
    <li<?php if ($store['store_id'] == $filter_store) { echo ' class="active"'; } ?>>
        <a href="<?php echo $store['store_link']; ?>">
            <?php echo $store['name']?> (<?php echo $store['total']?>)
        </a>
    </li>
    <?php
    }
    ?>
</ul>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="tab-content">
        <div class="tab-pane active">
            <?php if (!empty($products)) { ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $copy; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_COPY'); ?></a></li>
                            <li><a href="<?php echo $delete; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                        </ul>
                    </div>

                    <select id="extra_category_filter" class="form-control input-sm" style="display: inline-block; width: auto; margin-bottom: 5px;">
                        <option value=""><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_CATEGORY')); ?></option>
                        <?php foreach ($stores as $store) { ?>
                        <?php if (!empty($categories[$store['store_id']])) { ?>
                            <optgroup label="<?php echo $store['name']; ?>">
                                <?php foreach ($categories[$store['store_id']] as $category): ?>
                                <option<?php if ($category['category_id'] == $filter_category) { echo ' selected="selected"'; } ?> value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-6 align-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOW'); ?>: <?php echo $limit; ?> <?php echo Sumo\Language::getVar('SUMO_NOUN_RESULTS'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu align-left" role="menu">
                            <li><a href="<?php echo $filter_url; ?>&amp;limit=25">25</a></li>
                            <li><a href="<?php echo $filter_url; ?>&amp;limit=50">50</a></li>
                            <li><a href="<?php echo $filter_url; ?>&amp;limit=100">100</a></li>
                        </ul>
                    </div>

                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_PRODUCT'); ?></a>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-12 align-center">
                    <?php if (!empty($pagination) && isset($pagination)) {
                        echo $pagination;
                    } ?>
                </div>
            </div>

            <div class="clearfix"></div>

            <table class="table no-border hover table-product" style="margin: 20px 0;">
                <thead class="no-border">
                    <tr>
                        <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                        <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></strong></th>
                        <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></strong></th>
                        <th class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_VISIBLE'); ?></strong></th>
                        <th class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK'); ?></strong></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="no-border-y">
                    <?php
                    foreach ($products as $product) {
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
                        <td><input type="checkbox" class="icheck" name="selected[]" id="selected_<?php echo $product['product_id']; ?>" value="<?php echo $product['product_id']; ?>" /></td>
                        <td style="width: 100px; text-align: center;">
                            <div class="img">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
                            </div>
                        </td>
                        <td>
                            <strong><a href="<?php echo $product['edit']; ?>"><?php echo $product['name']; ?></a></strong><br />
                            Model: <?php echo $product['model']; ?>
                        </td>
                        <td style="width: 100px;">
                            <strong><?php echo Sumo\Formatter::currency($product['price'])?></strong><br />
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_EX')?>
                        </td>
                        <td style="width: 100px;">
                            <strong><?php echo Sumo\Formatter::currency($product['price_in'])?></strong><br />
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_IN')?>
                        </td>
                        <td style="width: 80px;">
                            <div class="switch switch-small" data-on-label="<?php echo Sumo\Language::getVar('SUMO_NOUN_ON')?>" data-off-label="<?php echo Sumo\Language::getVar('SUMO_NOUN_OFF')?>">
                                <input type="checkbox" name="visible_<?php echo $product['product_id']; ?>" value="1"<?php if ($product['status']) { ?> checked="checked"<?php } ?>>
                            </div>
                        </td>
                        <td style="width: 80px; position: relative;" class="has-<?php echo $qclass; ?>">
                            <?php if ($stock): ?>
                            <input type="hidden" name="stock_id_<?php echo $product['product_id']; ?>" value="<?php echo $product['stock_id']?>" />
                            <input type="text" data-stock="<?php echo $product['quantity']; ?>" name="stock_<?php echo $product['product_id']; ?>" value="<?php echo $product['quantity']; ?>" class="form-control stock-<?php echo $product['stock_id']?>" data-stock-id="<?php echo $product['stock_id']?>">
                            <?php else: ?>
                            <input type="text" class="form-control open-option input-option-<?php echo $product['product_id']?>" data-option="option-<?php echo $product['product_id']?>" value="<?php echo $total?>">
                            <?php endif?>
                        </td>
                        <td style="width: 140px;" class="right">
                            <div class="btn-group">
                                <a href="<?php echo $product['edit']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_MORE'); ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="<?php echo $copy; ?>" rel="singleItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_COPY'); ?></a></li>
                                        <li><a href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE_CONFIRM_PRODUCT'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                                    </ul>
                                </div>
                            </div>
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
                        <td colspan="2">&nbsp;</td>
                        <td><?php echo $items['name']?>: <?php echo $option['name']?></td>
                        <td style="width: 100px;">
                            <!--<small><?php echo $option['price_prefix'] . Sumo\Formatter::currency($option['price'])?></small><br />-->
                            <strong><?php echo Sumo\Formatter::currency($price)?></strong><br />
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_EX')?>
                        </td>
                        <td style="width: 100px;">
                            <!--<small><?php echo $option['price_prefix'] . Sumo\Formatter::currency($option['price'] + ($option['price'] / 100 * $product['tax_percentage']))?></small><br />-->
                            <strong><?php echo Sumo\Formatter::currency($priceIn)?></strong><br />
                            <?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_IN')?>
                        </td>
                        <td>&nbsp;</td>
                        <td class="has-<?php echo $qclass?>">
                            <input type="text" name="option_<?php echo $option['value_id']?>" data-option="<?php echo $option['value_id']?>" class="form-control" value="<?php echo $option['quantity']?>">
                        </td>
                        <td></td>
                    </tr>
                                <?php endif;
                            endforeach;
                        endif;
                    endforeach;
                    endif;
                    } ?>
                </tbody>
            </table>

            <div class="row">
                <div class="col-sm-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $copy; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_COPY'); ?></a></li>
                            <li><a href="<?php echo $delete; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6 align-center">
                    <?php if (!empty($pagination) && isset($pagination)) {
                        echo $pagination;
                    } ?>
                </div>

                <div class="col-sm-3 align-right">
                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_PRODUCT'); ?></a>
                </div>
            </div>

            <div class="clearfix"></div>
            <?php
            } else {
            ?>
            <p class="pull-left">
                <select id="extra_category_filter" class="form-control input-sm">
                    <option value=""><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_CATEGORY')); ?></option>
                    <?php foreach ($stores as $store) { ?>
                    <?php if (!empty($categories[$store['store_id']])) { ?>
                        <optgroup label="<?php echo $store['name']; ?>">
                            <?php foreach ($categories[$store['store_id']] as $category): ?>
                            <option<?php if ($category['category_id'] == $filter_category) { echo ' selected="selected"'; } ?> value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php } ?>
                    <?php } ?>
                </select>
            </p>
            <p class="pull-right">
                <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_PRODUCT'); ?></a>
            </p>
            <div class="clearfix"></div>
            <p class="well" style="margin-bottom: 0;"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_RESULTS'); ?></p>
            <?php
            }
            ?>
        </div>
    </div>
</form>

<h4 class="hnormal"><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_LEGEND'); ?></h4>
<p>
    <span class="label label-danger">0</span>&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_NO_STOCK'); ?>
    <span style="margin-left: 20px;" class="label label-warning">1-5</span>&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_LIMITED_STOCK'); ?>
    <span style="margin-left: 20px;" class="label label-success">5+</span>&nbsp; <?php echo Sumo\Language::getVar('SUMO_NOUN_SUFFICIENT_STOCK'); ?>
</p>

<script type="text/javascript">
    sessionToken = '<?php echo $token; ?>';
</script>

<?php echo $footer; ?>
