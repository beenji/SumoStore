<?php echo $header; ?>

<script type="text/javascript">
    var categories = new Object,
        sessionToken = '<?php echo $token; ?>';

    <?php $prev_store_id = ''; foreach ($categories_to_choose as $category) { ?>
        <?php if ($prev_store_id !== $category['store_id']) { ?>
            categories[<?php echo $category['store_id']; ?>] = new Array;
        <?php } ?>

        categories[<?php echo $category['store_id']; ?>].push({'id': <?php echo $category['category_id']; ?>, 'label': '<?php echo $category['name']; ?>'});

    <?php $prev_store_id = $category['store_id']; } ?>
</script>

<form action="" data-parsley-validate novalidate method="post">
    <?php if ($status == 0) { ?>
    <input type="hidden" name="status" value="0" />
    <?php } ?>

    <div class="align-right page-head-actions">
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

        <a style="margin-left: 30px;" class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_CATEGORY'); ?>" />
    </div>

    <div class="row">
        <div class="col-md-8">
            <?php foreach ($languages as $language_id => $list) { ?>
            <div class="block-flat lang-<?php echo $list['language_id']; ?> lang-block<?php if ($list['is_default']) { ?> lang-active<?php } ?>">
                <div class="row" style="margin-top: 0;">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name-<?php echo $language_id; ?>" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORY_NAME'); ?>:</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                </span>
                                <input type="text" required data-parsley-length="[2,255]" data-parsley-error-message="Voer voor iedere taal een categorienaam in a.u.b." id="name-<?php echo $language_id; ?>" name="category_description[<?php echo $language_id; ?>][name]" class="form-control ge-name-trigger" value="<?php echo isset($category_description[$list['language_id']]) ? $category_description[$list['language_id']]['name'] : ''; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="title-<?php echo $language_id; ?>" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAGE_TITLE'); ?>:</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                </span>
                                <input type="text" id="title-<?php echo $language_id; ?>" name="category_description[<?php echo $language_id; ?>][title]" class="form-control ge-title-trigger" value="<?php echo isset($category_description[$list['language_id']]) ? $category_description[$list['language_id']]['title'] : ''; ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="url" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEO_URL'); ?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <input type="text" class="form-control ge-url-trigger" id="url-<?php echo $language_id; ?>" readonly="readonly" name="category_description[<?php echo $language_id; ?>][keyword]" value="<?php echo isset($category_description[$list['language_id']]) ? $category_description[$list['language_id']]['keyword'] : ''; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="desc-<?php echo $language_id; ?>" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <textarea name="category_description[<?php echo $language_id; ?>][description]" id="desc-<?php echo $language_id; ?>" class="form-control redactor" rows="6"><?php echo isset($category_description[$list['language_id']]) ? $category_description[$list['language_id']]['description'] : ''; ?></textarea>
                    </div>
                </div>

                <hr>

                <div class="form-group has-warning">
                    <label for="meta-desc-<?php echo $language_id; ?>" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_META_DESCRIPTION'); ?> (<span class="counter">156</span> <?php echo Sumo\Language::getVar('SUMO_NOUN_CHARACTERS'); ?>):</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <textarea name="category_description[<?php echo $language_id; ?>][meta_description]" id="meta-desc-<?php echo $language_id; ?>" maxlength="156" class="form-control ge-description-trigger" rows="2"><?php echo isset($category_description[$list['language_id']]) ? $category_description[$list['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="meta-kw-<?php echo $language_id; ?>" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_META_KEYWORDS'); ?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <input type="text" name="category_description[<?php echo $language_id; ?>][meta_keyword]" id="meta-kw-<?php echo $language_id; ?>" value="<?php echo isset($category_description[$list['language_id']]) ? $category_description[$list['language_id']]['meta_keyword'] : ''; ?>" class="form-control">
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_GOOGLE_EXAMPLE'); ?>:</label>
                </div>
                <div class="google-example">
                    <span class="ge-title" id="ge-title-<?php echo $language_id; ?>"><?php if (!empty($category_description[$list['language_id']]['title'])) { echo $category_description[$list['language_id']]['title']; } else { ?>SumoStore BV<?php } ?></span>
                    <span class="ge-url" id="ge-url-<?php echo $language_id; ?>"><?php if (!empty($category_description[$list['language_id']]['keyword'])) { echo $category_description[$list['language_id']]['keyword']; } else { ?>www.sumostore.net<?php } ?></span>
                    <span class="ge-description" id="ge-description-<?php echo $language_id; ?>"><?php if (!empty($category_description[$list['language_id']]['meta_description'])) { echo $category_description[$list['language_id']]['meta_description']; } else { ?>Think forward with SumoStore<?php } ?></span>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="block-flat">
                <div class="form-group">
                    <label class="control-label" for="shop"><?php echo Sumo\Language::getVar('SUMO_NOUN_STORE'); ?>:</label>
                    <select required name="category_store" id="shop" class="form-control">
                        <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT'); ?></option>
                        <?php foreach ($stores as $store) { ?>
                        <option value="<?php echo $store['store_id']?>"<?php if ($store['store_id'] == $category_store) { echo ' selected="selected"'; } ?>>
                            <?php echo $store['name'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label" for="category"><?php echo Sumo\Language::getVar('SUMO_NOUN_PARENT_CATEGORY'); ?>:</label>
                    <select name="parent_id" id="category" class="form-control">
                        <option value="0"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_NONE')); ?></option>
                        <?php foreach ($categories_to_choose as $list): ?>
                        <option value="<?php echo $list['category_id']?>"<?php if ($list['selected']){ echo ' selected="selected"'; } ?> class="store-<?php echo $list['store_id']?>">
                            <?php echo $list['name'];?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label" for="image"><?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGE'); ?>:</label>
                    <div class="control-group">
                        <div class="fancy-upload">
                            <?php if ($image): ?>
                            <img src="../image/<?php echo $image; ?>" />
                            <a class="fu-edit" href="#edit" id="upload-image"><i class="fa fa-wrench"></i></a>
                            <a class="fu-delete" href="#delete"><i class="fa fa-times"></i></a>
                            <?php else: ?>
                            <a class="fu-new" href="#upload" id="upload-image"><i class="fa fa-plus-circle"></i></a>
                            <?php endif; ?>

                            <input type="hidden" name="image" id="image" value="<?php echo $image ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="align-right">
        <a class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_CATEGORY'); ?>" />
    </p>

    <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($category_id)) { echo $category_id; } else { echo '0'; } ?>" />
</form>

<?php echo $footer; ?>