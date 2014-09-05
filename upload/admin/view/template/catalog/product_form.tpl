<?php echo $header?>

<script type="text/javascript">
    var sessionToken = '<?php echo $token; ?>',
        optionCount = <?php echo sizeof($product_options); ?>,
        editButton = '<?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?>',
        deleteButton = '<?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?>',
        optionChoose = '<?php echo Sumo\Language::getVar('SUMO_NOUN_CHOOSE'); ?>',
        productCategories = {
            <?php $j = 0; foreach ($stores as $store) { $i = 0; ?>
            <?php if ($j > 0) { echo ','; } ?>
            <?php echo $store['store_id']; ?>: [
                <?php foreach ($product_categories as $category) { ?>
                    <?php if ($category['store_id'] == $store['store_id']) { ?>
                    <?php if ($i > 0) { echo ','; } ?>
                    {
                        'id': <?php echo $category['category_id']; ?>,
                        'name': '<?php echo addslashes($category['name']); ?>'
                    }
                    <?php $i++; } ?>
                <?php } ?>
            ]
            <?php $j++; } ?>
        };

    var languages = <?php echo json_encode($languages); ?>,
        formError = '<?php echo addslashes($error); ?>';
</script>

<div class="page-head-actions align-right">
        <div class="btn-group align-left">
            <?php
            foreach ($languages as $list):
                if ($list['is_default']):
            ?>
            <button class="btn btn-primary dropdown-toggle" id="language-selector-btn" data-toggle="dropdown" type="button"><span><img src="view/img/flags/<?php echo $list['image']; ?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></span>&nbsp; <span class="caret"></span></button>
            <?php
                    break;
                endif;
            endforeach; ?>
            <ul class="dropdown-menu pull-right" id="language-selector">
                <?php foreach ($languages as $list): ?>
                <li><a href="#other-language" data-lang-id="<?php echo $list['language_id']; ?>"><img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

    <a class="btn btn-primary" id="save_product" href="javascript:;"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PRODUCT_SAVE'); ?></a>
</div>


<form action="" method="post" id="option-form">
    <div class="modal fade colored-header info" id="optionModal" tabindex="-1" role="dialog" aria-labelledby="optionModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="optionModalLabel"><?php echo Sumo\Language::getVar('SUMO_SEARCH_OPTION'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="product" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?>:</label>
                        <input type="text" id="find-product" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="product" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION'); ?>:</label>
                        <select id="find-option" class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT_PRODUCT'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></button>
                    <button type="button" class="btn btn-primary" id="duplicate-option"><?php echo Sumo\Language::getVar('SUMO_BUTTON_ADD'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="" method="post" id="attribute-form">
    <div class="modal fade colored-header info" id="attributeModal" tabindex="-1" role="dialog" aria-labelledby="attributeModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="attributeModalLabel"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_ATTRIBUTE_SET'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php foreach ($languages as $list) { if ($list['is_default']) { ?>
                    <div class="form-group">
                        <label for="group" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GROUP_NAME'); ?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="attribute_group_description[<?php echo $list['language_id']; ?>][name]" class="form-control" />
                        </div>
                    </div>
                    <?php }} ?>

                    <div class="form-group">
                        <label for="attributes" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ATTRIBUTES'); ?>:</label>
                        <div class="row attribute-row">
                            <div class="col-md-10">
                                <?php foreach ($languages as $list) { if ($list['is_default']) { ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                    </span>
                                    <input type="text" name="attribute[][attribute_description][<?php echo $list['language_id']; ?>][name]" class="form-control" />
                                </div>
                                <?php }} ?>
                            </div>
                            <div class="col-md-2">
                                <a href="#extra-attribute" class="btn btn-default"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EXTRA'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></button>
                    <button type="button" class="btn btn-primary" id="save-attribute"><?php echo Sumo\Language::getVar('SUMO_BUTTON_ADD'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="" data-parsley-validate novalidate method="post" id="product-form">
    <div class="modal fade colored-header info" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="categoryModalLabel"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT_CATEGORIES'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row category-row">
                        <div class="col-md-5" style="padding-right: 0;">
                            <select name="shop[]" class="form-control shop-selector">
                                <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT'); ?></option>
                                <?php foreach ($stores as $store) { ?>
                                <option<?php if ($store['store_id'] == $product_store) { echo ' selected="selected"'; } ?> value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-5" style="padding-right: 0;">
                            <select name="category[]" class="form-control category-selector">
                                <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORY'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="#extra-category" class="btn btn-default"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EXTRA'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></button>
                    <button type="button" class="btn btn-primary" id="save-categories"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#general" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENERAL'); ?></a></li>
        <li><a href="#seo" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEO'); ?></a></li>
        <li><a href="#options" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTIONS'); ?></a></li>
        <li><a href="#attributes" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_ATTRIBUTES'); ?></a></li>
        <li><a href="#discounts" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNTS'); ?></a></li>
        <li><a href="#other" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_OTHER'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active cont" id="general">
            <div class="row">
                <div class="col-md-8">
                    <div class="row" style="margin-top: 0;">
                        <div class="col-md-8 col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                                <?php foreach ($languages as $list): ?>
                                <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                                    <span class="input-group-addon">
                                        <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                    </span>
                                    <input type="text" required data-parsley-length="[1,255]" data-parsley-error-message="Voer voor iedere taal een naam in." name="product_description[<?php echo $list['language_id']?>][name]" data-lang-id="<?php echo $list['language_id']; ?>" value="<?php echo isset($product_description[$list['language_id']]['name']) ? $product_description[$list['language_id']]['name'] : ''?>" class="form-control" />

                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <div class="form-group">
                                <label for="model" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?>:</label>
                                <input type="text" name="model_2" id="model" class="form-control" value="<?php echo $model?>">
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <div class="form-group">
                                <label for="model_supplier" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL_SUPPLIER'); ?>:</label>
                                <input type="text" name="model_supplier" id="model_supplier" class="form-control" value="<?php echo $model_supplier?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <textarea rows="8" name="product_description[<?php echo $list['language_id']?>][description]" class="redactor form-control"><?php if (isset($product_description[$list['language_id']]['description'])) { echo $product_description[$list['language_id']]['description']; } ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="image" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_IMAGES'); ?></label>
                        <ul class="image-list" id="product-image-list">
                            <li>
                                <a class="upload" id="upload-image" href="#"><i class="fa fa-plus-circle"></i></a>
                                <span class="image-list-footer"><?php echo Sumo\Language::getVar('SUMO_NOUN_SORT_ORDER'); ?>:</span>
                            </li>
                            <?php if ($image) { ?>
                            <li>
                                <img src="../image/<?php echo $image; ?>" />
                                <input type="hidden" name="product_image[]" value="<?php echo $image; ?>">
                                <label class="image-list-footer">
                                    <a href="#" class="push-left">
                                        <i class="fa fa-chevron-left"></i>
                                    </a>
                                    <a href="#" class="push-right">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </label>
                                <a class="remove" href="#"><i class="fa fa-times-circle-o"></i></a>
                            </li>
                            <?php } ?>
                            <?php foreach ($product_images as $image) { ?>
                            <li>
                                <img src="../image/<?php echo $image['image']; ?>" />
                                <input type="hidden" name="product_image[]" value="<?php echo $image['image']; ?>">
                                <label class="image-list-footer">
                                    <a href="#" class="push-left">
                                        <i class="fa fa-chevron-left"></i>
                                    </a>
                                    <a href="#" class="push-right">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </label>
                                <a class="remove" href="#"><i class="fa fa-times-circle-o"></i></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <hr>

                    <div class="row" style="margin-top: 0;">
                        <div class="col-md-3">
                            <?php if ($tax_percentages) { ?>
                            <div class="form-group">
                                <label class="control-label" for="price_ex"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATALOG_TAX_PERCENTAGE'); ?>:</label>
                                <select name="tax_percentage" required id="tax" class="form-control">
                                    <?php foreach ($tax_percentages as $tp): ?>
                                    <option value="<?php echo $tp?>"<?php if ($tp == $tax_percentage) { echo ' selected="selected"'; } ?> data-percentage="<?php echo $tp?>">
                                        <?php echo $tp; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php } ?>

                            <?php if (!$tax_percentages) { ?>
                            <a href="<?php echo $tax_settings; ?>" id="tax_settings_link" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_ADD_TAX_PERCENTAGE'); ?></a>
                            <input type="hidden" name="tax_percentage" id="tax" value="0" />
                            <?php } ?>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="price_in"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_IN'); ?>:</label>
                                <input type="text" id="price_in" value="<?php echo number_format($price * (1 + ($tax_percentage / 100)), 4, '.', ','); ?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="price_ex"><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_EX'); ?>:</label>
                                <input type="text" name="product_price" value="<?php echo $price; ?>" id="price_ex" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="cost"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_PURCHASE'); ?>:</label>
                                <input type="text" name="cost_price" id="cost" class="form-control" value="<?php echo $cost_price; ?>">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row" style="margin-top: 0;">
                        <div class="col-md-4 col-lg-3">
                            <div class="form-group">
                                <label for="stock-attached" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ATTACH_STOCK'); ?>:</label>
                                <div class="control-group">
                                    <label class="radio-inline" style="margin-bottom: 6px;">
                                        <input type="radio" checked="checked" name="stock_product" value="0"<?php if (!$stock_product) { echo ' checked="checked"'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                                    </label>
                                    <label class="radio-inline" style="margin-bottom: 6px;">
                                        <input type="radio" name="stock_product" value="1"<?php if ($stock_product) { echo ' checked="checked"'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="stock-independent col-md-8 col-lg-4<?php if ($stock_product) { ?> collapse collapsed<?php } ?>">
                            <div class="form-group">
                                <label for="stock" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_FIELD_QUANTITY'); ?>:</label>
                                <input type="text" id="stock" name="product_quantity" class="form-control" value="<?php echo $quantity; ?>">
                            </div>
                        </div>

                        <div class="stock-independent col-lg-5<?php if ($stock_product) { ?> collapse collapsed<?php } ?>">
                            <div class="form-group">
                                <label for="hide-stock" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISPLAY_NO_STOCK'); ?></label>
                                <div class="control-group">
                                    <label class="radio-inline">
                                        <input <?php if ($stock_visible == 1) { echo ' checked="checked"'; }?>type="radio" name="stock_visible" value="1"> <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input<?php if ($stock_visible == 0) { echo ' checked="checked"'; }?> type="radio" name="stock_visible" value="0"> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input<?php if ($stock_visible == 2) { echo ' checked="checked"'; }?> type="radio" name="stock_visible" value="2">
                                        <?php echo Sumo\Language::getVar('SUMO_NOUN_DEFAULT'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="stock-attached<?php if (!$stock_product) { ?> collapse collapsed<?php } ?>">
                            <div class="col-md-8 col-lg-9">
                                <div class="form-group">
                                    <label for="product" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?>:</label>
                                    <input name="stock_product_name" data-parsley-error-message="<?php echo Sumo\Language::getVar('SUMO_ERROR_NO_STOCK_ID'); ?>" data-parsley-validate-if-empty="true" data-parsley-requiredifradio="stock_product,1" type="text" id="stock-id-selector" class="form-control" value="<?php echo $stock_product_name; ?>" data-selected-option="<?php echo $stock_product_name; ?>">
                                    <input type="hidden" id="stock-id" name="stock_id" value="<?php echo $stock_id; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="stock-independent<?php if ($stock_product) { ?> collapse collapsed<?php } ?>">
                            <div class="col-md-8 col-lg-9 col-md-offset-4 col-lg-offset-3">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_OUT_OF_STOCK')?>:</label>
                                <select name="stock_status_id" class="form-control">
                                    <?php foreach ($stock_statuses as $list): ?>
                                    <option value="<?php echo $list['stock_status_id']?>" <?php if ($list['stock_status_id'] == $stock_status_id) { echo 'selected'; }?>><?php echo $list['name']?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_RELATED_PRODUCTS')?>:</label>
                                <input type="text" id="search-related-product" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table id="table-related-product" class="table no-border">
                                <tbody class="no-border-y">
                                    <?php foreach ($product_related as $list): ?>
                                    <tr data-productid="<?php echo $list['product_id']?>">
                                        <td style="width: 66px;">
                                            <img src="<?php echo $list['image']; ?>">
                                        </td>
                                        <td><strong>
                                            <?php
                                            if (!empty($list['model_2'])) {
                                                echo $list['model_2'];
                                            }
                                            else {
                                                echo $list['model'];
                                            }
                                            echo ' - ';
                                            echo $list['name'];
                                            ?>
                                            </strong>
                                            <input type="hidden" name="product_related[]" value="<?php echo $list['product_id']?>">
                                        </td>
                                        <td class="right">
                                            <a class="btn btn-xs btn-primary" href="#delete"><?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE')?></a>
                                        </td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="tags" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TAGS'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="product_description[<?php echo $list['language_id']; ?>][tag]" value="<?php echo isset($product_description[$list['language_id']]['tag']) ? $product_description[$list['language_id']]['tag'] : ''?>" class="form-control">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="form-group">
                        <a class="pull-right btn btn-xs btn-secondary" id="extra-info"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA_FIELD'); ?></a>
                        <label for="info" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_INFORMATION'); ?>:</label>
                        <div class="control-group">
                            <?php $info_types = array('sku' => '', 'upc' => '', 'isbn' => '', 'ean' => '', 'jan' => '', 'mpn' => ''); ?>
                            <?php foreach ($extra_info as $list) { ?>
                            <?php unset($info_types[$list['type']]); ?>
                            <div class="row product-info" style="margin-top: 0;">
                                <div class="col-md-2">
                                    <select data-parsley-ui-enabled="false" disabled="disabled" class="form-control">
                                        <option value="<?php echo $list['type']; ?>"><?php echo strtoupper($list['type'])?></option>
                                    </select>
                                </div>
                                <div class="col-md-10 input-group">
                                    <input data-parsley-ui-enabled="false" type="text" name="<?php echo $list['type']?>" class="form-control" value="<?php echo $list['value']?>" />
                                    <span class="input-group-addon" style="border-left: none;">
                                        <label class="checkbox-inline" style="font-size: 13px; margin-top: 0;">
                                            <input data-parsley-ui-enabled="false" type="checkbox" name="<?php echo $list['type']?>_visible" value="1"<?php if ($list['visible']) { ?> checked="checked"<?php } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_VISIBLE'); ?>
                                        </label>
                                    </span>
                                    <div class="input-group-btn">
                                        <a href="#" class="btn remove-info">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="row product-info" style="margin-top: 0;">
                                <div class="col-md-2">
                                    <select data-parsley-ui-enabled="false" name="" id="" class="form-control">
                                        <?php foreach (array_keys($info_types) as $type) { ?>
                                        <option value="<?php echo $type; ?>"><?php echo mb_strtoupper($type); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-10 input-group">
                                    <input data-parsley-ui-enabled="false" type="text" name="" class="form-control" />
                                    <span class="input-group-addon" style="border-left: none;">
                                        <label class="checkbox-inline" style="font-size: 13px; margin-top: 0;">
                                            <input data-parsley-ui-enabled="false" type="checkbox" name="_visible"> <?php echo Sumo\Language::getVar('SUMO_NOUN_VISIBLE'); ?>
                                        </label>
                                    </span>
                                    <div class="input-group-btn">
                                        <a href="#" class="btn remove-info">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <a href="#categoryModal" class="pull-right" data-toggle="modal">Meerdere categorie&euml;n?</a>
                        <label for="shop" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP'); ?>:</label>
                        <select required name="product_store_selector" id="shop" class="form-control shop-selector">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT'); ?></option>
                            <?php foreach ($stores as $store) { ?>
                                <option<?php if (($store['store_id'] == $product_store) || ($store['store_id'] == $product_store[0])) { echo ' selected="selected"'; } ?> value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORY'); ?>:</label>
                        <select required name="product_category_selector" id="category" class="form-control category-selector">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT'); ?></option>
                            <?php foreach ($product_categories as $category) { ?>
                                <?php if ($category['store_id'] == $product_store[0]) { ?>
                                <option<?php if (isset($product_category[0]) && $category['category_id'] == $product_category[0]) { echo ' selected="selected"'; } ?> value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                    <hr>

                    <ul id="category-box">
                        <?php foreach ($product_categories as $product_category) { ?>
                            <?php if ($product_category['selected']) { ?>
                            <li class="category-row" data-shop-id="<?php echo $product_category['store_id']; ?>" data-category-id="<?php echo $product_category['category_id']; ?>">
                                <a href="#remove-category" class="pull-right"><i class="fa fa-times-circle"></i></a>
                                <strong><?php echo $product_category['store_name']; ?></strong>
                                <?php echo $product_category['name']; ?>
                                <input type="hidden" name="product_store[]" value="<?php echo $product_category['store_id']; ?>" />
                                <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                            </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>

                    <div class="form-group">
                        <label for="status" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="status" value="1"<?php if ($status) { echo ' checked="checked"'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_ACTIVE'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="0"<?php if (!$status) { echo ' checked="checked"'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_INACTIVE'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="weight" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_WEIGHT'); ?>:</label>
                        <div class="control-group row" style="margin-top: 0;">
                            <div class="col-md-8 col-lg-9">
                                <input type="text" name="product_weight" id="weight" class="form-control" value="<?php echo (float)$weight; ?>">
                            </div>
                            <div class="col-md-4 col-lg-3" style="padding-left: 0;">
                                <select name="weight_class_id" id="unit" class="form-control">
                                    <?php foreach ($weight_classes as $weight_class) { ?>
                                        <?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
                                        <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="length" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DIMENSIONS'); ?>:</label>
                        <div class="control-group row" style="margin-top: 0;">
                            <div class="col-md-4">
                                <input type="text" id="length" name="length" class="form-control" value="<?php echo (float)$length?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="width" name="width" class="form-control" value="<?php echo (float)$width?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="height" name="height" class="form-control" value="<?php echo (float)$height?>">
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="brand" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BRAND'); ?>:</label>
                        <select name="manufacturer_id" id="brand" class="form-control">
                            <?php foreach ($manufacturers as $list): ?>
                                <option value="<?php echo $list['manufacturer_id']?>" <?php if ($manufacturer_id == $list['manufacturer_id']) { echo 'selected="selected"'; } ?>>
                                <?php echo $list['name']?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="download" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DOWNLOAD'); ?>:</label>
                        <input type="text" id="download" class="form-control">

                        <table id="table-downloads" class="table no-border">
                            <tbody class="no-border-y">
                                <?php foreach ($product_downloads as $list): ?>
                                <tr data-productid="<?php echo $list['download_id']?>">
                                    <td>
                                        <strong><?php echo $list['name']; ?></strong>
                                        <input type="hidden" name="product_download[]" value="<?php echo $list['download_id']?>">
                                    </td>
                                    <td class="right">
                                        <a class="btn btn-xs btn-primary" href="#delete"><?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE')?></a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane cont" id="seo">
            <div class="row">
                <?php foreach ($languages as $k => $list): ?>
                <div class="col-md-7 lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="seo_name[<?php echo $list['language_id']; ?>]" id="seo_name_<?php echo $k; ?>" disabled="disabled" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="title" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAGE_TITLE'); ?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="product_description[<?php echo $list['language_id']; ?>][title]" value="<?php echo $product_description[$list['language_id']]['title']; ?>" id="ge-title-<?php echo $list['language_id']; ?>" class="form-control ge-trigger">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEO_URL'); ?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="product_description[<?php echo $list['language_id']; ?>][keyword]" readonly="readonly" id="ge-url-<?php echo $list['language_id']; ?>" class="form-control ge-trigger">
                        </div>
                    </div>

                    <div class="form-group has-warning">
                        <label class="control-label" for="meta-desc"><?php echo Sumo\Language::getVar('SUMO_NOUN_META_DESCRIPTION'); ?> (<span class="meta-desc-length">156</span> <?php echo Sumo\Language::getVar('SUMO_NOUN_CHARACTERS'); ?>):</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <textarea rows="3" id="ge-description-<?php echo $list['language_id']; ?>" name="product_description[<?php echo $list['language_id']; ?>][meta_description]" class="form-control ge-trigger" maxlength="255"><?php echo $product_description[$list['language_id']]['meta_description']; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="meta-keywords"><?php echo Sumo\Language::getVar('SUMO_NOUN_KEYWORDS'); ?>:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" id="meta-keywords" name="product_description[<?php echo $list['language_id']; ?>][meta_keyword]" class="form-control" value="<?php echo $product_description[$list['language_id']]['meta_keyword']; ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="col-md-5" style="padding-top: 32px;">
                    <p><?php echo Sumo\Language::getVar('SUMO_NOUN_SEO_HELP'); ?></p>
                    <hr>
                    <?php foreach ($languages as $list): ?>
                    <div class="google-example lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>" data-lang-id="<?php echo $list['language_id']; ?>">
                        <span class="ge-title"><?php if (!empty($product_description[$list['language_id']]['title'])) { echo $product_description[$list['language_id']]['title']; } else { ?>SumoStore BV<?php } ?></span>
                        <span class="ge-url">www.sumostore.net</span>
                        <span class="ge-description"><?php if (!empty($product_description[$list['language_id']]['meta_description'])) { echo $product_description[$list['language_id']]['meta_description']; } else { ?>Lorem ipsum dolor sit amet consectuor vel avis<?php } ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="tab-pane cont" id="options">
            <p class="well<?php if (sizeof($product_options)) { ?> collapse collapsed<?php } ?>" id="product-options-intro">Gebruik onderstaand formulier om bestaande product-opties of nieuwe product-opties aan dit product toe te voegen.</p>

            <table class="table no-border<?php if (!sizeof($product_options)) { ?> collapse collapsed<?php } ?>" id="product-options">
                <thead class="no-border">
                    <tr>
                        <th style="width: 125px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION'); ?></strong></th>
                        <th style="width: 185px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE'); ?></strong></th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody class="no-border-y">
                    <?php foreach ($product_options as $k => $option) { ?>
                    <tr data-id="<?php echo $option['option_id']; ?>">
                        <td>
                            <?php echo $option['name']; ?>
                            <?php foreach ($languages as $language) { ?>
                            <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][option_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo $option['option_description'][$language['language_id']]['name']; ?>" />
                            <?php } ?>
                        </td>
                        <td><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_' . mb_strtoupper($option['type'])); ?></td>
                        <td>
                            <?php if (isset($option['product_option_value'])) { ?>
                                <?php foreach ($option['product_option_value'] as $j => $value) { ?>
                                <?php echo ($j > 0 ? ', ' : '') . $value['option_value_description'][$this->config->get('language_id')]['name']; ?>
                                <?php } ?>
                            <?php } ?>
                        </td>
                        <td class="right">
                            <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][option_id]" value="<?php echo $option['option_id']; ?>" />
                            <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][type]" value="<?php echo $option['type']; ?>" />
                            <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][active]" value="1" />
                            <?php if (isset($option['product_option_value'])) { ?>
                                <?php foreach ($option['product_option_value'] as $j => $value) { ?>
                                    <?php if (isset($value['option_value_description'])) { ?>
                                    <?php foreach ($value['option_value_description'] as $language_id => $value_name) { ?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][option_value_description][<?php echo $language_id; ?>][name]" value="<?php echo $value_name['name']; ?>" />
                                    <?php } ?>
                                    <?php } ?>

                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][value_id]" value="<?php echo $value['value_id']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][active]" value="<?php echo $value['active']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][quantity]" value="<?php echo $value['quantity']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][subtract]" value="<?php echo $value['subtract']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][price]" value="<?php echo $value['price']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][price_prefix]" value="<?php echo $value['price_prefix']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][weight]" value="<?php echo $value['weight']; ?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_option[<?php echo $k; ?>][product_option_value][<?php echo $j; ?>][weight_prefix]" value="<?php echo $value['weight_prefix']; ?>" />
                                <?php } ?>
                            <?php } ?>

                            <a href="#edit-option" class="btn-xs btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                            <a href="#delete-option" class="btn-xs btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <hr>

            <div class="row">
                <div class="col-md-offset-3 col-md-3">
                    <div class="form-group">
                        <label for="existing" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEW_OPTION'); ?></label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <div class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </div>
                            <input type="text" data-parsley-ui-enabled="false" class="form-control" id="option_name_<?php echo $list['language_id']; ?>" name="option_name_<?php echo $list['language_id']; ?>" data-lang-id="<?php echo $list['language_id']; ?>" placeholder="Label van optie">
                        </div>
                        <?php endforeach; ?>
                        <a href="#optionModal" data-toggle="modal">Kopieer van ander product</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="option-type" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE'); ?>:</label>
                        <select name="option_type" id="option-type" class="form-control">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_CHOOSE'); ?></option>
                            <optgroup label="<?php echo Sumo\Language::getVar('SUMO_NOUN_CHOICE'); ?>">
                                <option value="select"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_SELECT'); ?></option>
                                <option value="radio"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_RADIO'); ?></option>
                                <option value="checkbox"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_CHECKBOX'); ?></option>
                            </optgroup>
                            <optgroup label="<?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_INPUT'); ?>">
                                <option value="text"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_TEXT'); ?></option>
                                <option value="textarea"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_TEXTAREA'); ?></option>
                            </optgroup>
                            <optgroup label="<?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_FILE'); ?>">
                                <option value="file"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_FILE'); ?></option>
                            </optgroup>
                            <optgroup label="<?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_DATE'); ?>">
                                <option value="date"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_DATE'); ?></option>
                                <option value="time"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_TIME'); ?></option>
                                <option value="datetime"><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE_DATETIME'); ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <p class="align-right">
                <a href="javascript:;" id="extra-option-choice" style="display: none;" class="btn btn-xs btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXTRA_CHOICE'); ?></a>
                <a href="javascript:;" id="add-option" class="btn btn-xs btn-secondary">Productoptie opslaan</a>
            </p>

            <table class="table no-border table-option collapse collapsed">
                <thead class="no-border">
                    <tr>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ACTIVE'); ?></strong></th>
                        <th style="width: 200px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_OPTION'); ?></strong></th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STOCK_SHORT'); ?></strong></th>
                        <th style="width: 110px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_FROM_STOCK'); ?></strong></th>
                        <th colspan="2"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></strong></th>
                        <th colspan="3"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_WEIGHT'); ?></strong></th>
                    </tr>
                </thead>
                <tbody class="no-border-y">

                </tbody>
            </table>
        </div>

        <div class="tab-pane cont" id="attributes">
            <p class="align-right"><a data-toggle="modal" href="#attributeModal" class="btn btn-primary btn-sm"><?php echo Sumo\Language::getVar('SUMO_BUTTON_NEW_ATTRIBUTE_SET'); ?></a></p>
            <table class="table no-border"<?php if (empty($attribute_sets)) { echo ' style="display: none;"'; } ?>>
                <thead class="no-border">
                    <tr>
                        <th style="width: 100px;"></th>
                        <th><strong>Attributen</strong></th>
                    </tr>
                </thead>
                <tbody class="no-border-y">
                    <?php foreach ($attribute_sets as $set) { ?>
                    <tr>
                        <td><strong><?php echo $set['name']; ?>:</strong></td>
                        <td>
                            <?php foreach ($set['attributes'] as $attribute) { ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" style="margin-top: 3px;" name="attribute[]" value="<?php echo $attribute['attribute_id']; ?>"<?php if ($attribute['checked']) { ?> checked="checked"<?php } ?> />
                                <?php echo $attribute['name']; ?>
                            </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php if (empty($attribute_sets)) { ?>
            <p class="well" id="no-attributes"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_ATTRIBUTES_INLINE'); ?></p>
            <?php } ?>
        </div>

        <div class="tab-pane cont" id="discounts">
            <div class="row" style="margin-top: 0;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT_TYPE'); ?>:</label>
                        <div class="control-group">
                            <label class="radio-inline">
                                <input type="radio" name="discount-type" checked value="special"> <?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT_SIMPLE'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="discount-type" value="discount"> <?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT_STAFFLE'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer-group" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMERGROUP'); ?>:</label>
                        <select class="form-control" name="customer-group" id="customer-group">
                            <?php foreach ($customer_groups as $customer_group_id => $customer_group) { ?>
                            <option value="<?php echo $customer_group_id; ?>"><?php echo $customer_group['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price-in" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?>:</label>
                        <div class="control-group row" style="margin-top: 0;">
                            <div class="col-md-6" style="padding-right: 5px;">
                                <input type="text" name="price-in" id="sp_price_in" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_NET'); ?>" class="form-control" />
                            </div>

                            <div class="col-md-6" style="padding-left: 5px;">
                                <input type="text" name="price-ex" id="sp_price_ex" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_GROSS'); ?>" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer-group" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE_START_FINISH'); ?>:</label>
                        <div class="control-group row" style="margin-top: 0;">
                            <div class="col-md-6" style="padding-right: 5px;">
                                <input type="text" name="start" id="date-start" class="form-control date-picker" />
                            </div>

                            <div class="col-md-6" style="padding-left: 5px;">
                                <input type="text" name="date-end" id="date-end"class="form-control date-picker" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group collapse collapsed" id="staffle">
                        <label for="min" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIN_AMOUNT'); ?>:</label>
                        <input type="text" name="min" id="min-amount" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="prio" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRIO'); ?>:</label>
                        <input type="text" name="prio" id="prio" class="form-control">
                    </div>

                    <hr>

                    <button type="button" class="btn btn-secondary add-discount"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DISCOUNT_ADD'); ?></button>
                </div>

                <div class="col-md-9">
                    <p class="well" id="discount-placeholder"<?php if (sizeof($product_specials) > 0 || sizeof($product_discounts) > 0) { ?> style="display: none;"<?php } ?>><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_DISCOUNTS'); ?></p>
                    <table class="table no-border" id="discount-table"<?php if (sizeof($product_specials) > 0 || sizeof($product_discounts) > 0) { ?> style="display: table;"<?php } else { ?> style="display: none;"<?php } ?>>
                        <thead class="no-border">
                            <tr>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMERGROUP'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TYPE'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DATE'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_MIN'); ?></strong></th>
                                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRIO_SHORT'); ?></strong></th>
                                <th style="width: 20px;"></th>
                            </tr>
                        </thead>
                        <tbody class="no-border-y">
                            <?php $i = 0; foreach ($product_specials as $group => $list) { $i++; ?>
                            <tr>
                                <td>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_special[<?php echo $i; ?>][customer_group_id]" value="<?php echo $list['customer_group_id']; ?>" />
                                    <?php echo isset($customer_groups[$group]['name']) ? $customer_groups[$group]['name'] : Sumo\Language::getVar('SUMO_NOUN_DEFAULT'); ?>
                                </td>
                                <td>Eenvoudig</td>
                                <td>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_special[<?php echo $i; ?>][price]" value="<?php echo $list['price']?>" />
                                    <?php echo $list['price_in']?>
                                </td>
                                <td>
                                    <?php echo $list['date_start']?> - <?php echo $list['date_end']?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_special[<?php echo $i; ?>][date_start]" value="<?php echo $list['date_start']?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_special[<?php echo $i; ?>][date_end]" value="<?php echo $list['date_end']?>" />
                                </td>
                                <td>
                                    <?php echo isset($list['quantity']) ? $list['quantity'] : ''; ?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_special[<?php echo $i; ?>][quantity]" value="<?php echo isset($list['quantity']) ? $list['quantity'] : ''?>" />
                                </td>
                                <td>
                                    <?php echo $list['priority']; ?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_special[<?php echo $i; ?>][priority]" value="<?php echo $list['priority']?>" />
                                </td>
                                <td><a href="#" class="remove-discount btn btn-xs btn-primary"><i class="fa fa-minus-circle"></i></a></td>
                            </tr>
                            <?php } ?>
                            <?php foreach ($product_discounts as $group => $list) { $i++; ?>
                            <tr>
                                <td>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_discount[<?php echo $i; ?>][customer_group_id]" value="<?php echo $list['customer_group_id']; ?>" />
                                    <?php echo isset($customer_groups[$group]['name']) ? $customer_groups[$group]['name'] : Sumo\Language::getVar('SUMO_NOUN_DEFAULT'); ?>
                                </td>
                                <td>Staffel</td>
                                <td>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_discount[<?php echo $i; ?>][price]" value="<?php echo $list['price']?>" />
                                    <?php echo $list['price_in']?>
                                </td>
                                <td>
                                    <?php echo $list['date_start']?> - <?php echo $list['date_end']?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_discount[<?php echo $i; ?>][date_start]" value="<?php echo $list['date_start']?>" />
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_discount[<?php echo $i; ?>][date_end]" value="<?php echo $list['date_end']?>" />
                                </td>
                                <td>
                                    <?php echo isset($list['quantity']) ? $list['quantity'] : ''; ?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_discount[<?php echo $i; ?>][quantity]" value="<?php echo isset($list['quantity']) ? $list['quantity'] : ''?>" />
                                </td>
                                <td>
                                    <?php echo $list['priority']; ?>
                                    <input data-parsley-ui-enabled="false" type="hidden" name="product_discount[<?php echo $i; ?>][priority]" value="<?php echo $list['priority']?>" />
                                </td>
                                <td><a href="#" class="remove-discount btn btn-xs btn-primary"><i class="fa fa-minus-circle"></i></a></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <script type="text/javascript">
                        var specCount = <?php echo $i + 1; ?>;
                    </script>
                </div>
            </div>
        </div>

        <div class="tab-pane cont" id="other">
            <div class="form-group">
                <label for="location" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_LOCATION_WAREHOUSE'); ?>:</label>
                <input type="text" name="location" value="<?php echo $location?>" id="location" class="form-control">
            </div>

            <div class="form-group">
                <label for="min" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MIN_ORDER'); ?>:</label>
                <input type="text" name="minimum" value="<?php echo $minimum?>" id="min" class="form-control">
            </div>

            <div class="form-group">
                <label for="from-stock-y" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_MANAGE_STOCK'); ?>:</label>
                <div class="control-group">
                    <label class="radio-inline">
                        <input type="radio" name="subtract" value="1" id="from-stock-y"<?php if ($subtract) { echo ' checked="checked"'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_MANAGE_STOCK_YES'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="subtract" value="0" id="from-stock-n"<?php if (!$subtract) { echo ' checked="checked"'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="ship-y" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHIP'); ?>:</label>
                <div class="control-group">
                    <label class="radio-inline">
                        <input type="radio" name="shipping" id="ship-yp" value="1"<?php if ($shipping == 1) { echo ' checked="checked"'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SHIP_YES_PACKAGE'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="shipping" id="ship-yl" value="2"<?php if ($shipping == 2) { echo ' checked="checked"'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SHIP_YES_LETTER'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="shipping" id="ship-n" value="0"<?php if (!$shipping) { echo ' checked="checked"'; }?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SHIP_NO'); ?>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="location" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_AVAILABLE_FROM'); ?>:</label>
                <input type="text" name="date_available" id="available" class="form-control date-picker" value="<?php echo $date_available?>">
            </div>
        </div>
    </div>

    <p class="align-right">
        <a href="<?php echo $cancel; ?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_PRODUCT_SAVE'); ?>" />
    </p>

    <input type="hidden" name="product_id" id="product_id" value="<?php if (isset($product_id)) { echo $product_id; } else { echo '0'; } ?>" />
</form>

<?php echo $footer?>
