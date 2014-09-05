<?php echo $header?>
<div class="col-md-4 col-md-offset-8 page-head-actions align-right">
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
</div>
<form action="" data-parsley-validate novalidate method="post">
    <div class="tab-content">
        <div class="tab-pane active cont">
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="description" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION_INTRO'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <textarea name="description[<?php echo $list['language_id']?>][intro_text]" class="form-control redactor-intro" data-parsley-ui-enabled="false"><?php if (isset($data['description'][$list['language_id']]['intro_text'])) { echo $data['description'][$list['language_id']]['intro_text']; } ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <textarea name="description[<?php echo $list['language_id']?>][text]" class="form-control redactor-blog" data-parsley-ui-enabled="false"><?php if (isset($data['description'][$list['language_id']]['text'])) { echo $data['description'][$list['language_id']]['text']; } ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_TITLE'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" required data-parsley-length="[1,255]" data-parsley-error-message="Voer voor iedere taal een titel in." name="description[<?php echo $list['language_id']?>][title]" data-lang-id="<?php echo $list['language_id']; ?>" value="<?php echo isset($data['description'][$list['language_id']]['title']) ? $data['description'][$list['language_id']]['title'] : ''?>" class="form-control" />

                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BLOG_AUTHOR')?>:</label>
                        <input type="text" name="author" value="<?php if (isset($data['author'])) { echo $data['author']; } ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_META_DESCRIPTION'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <textarea rows="1" type="text" name="description[<?php echo $list['language_id']?>][meta_description]" data-lang-id="<?php echo $list['language_id']; ?>" class="form-control"><?php echo isset($data['description'][$list['language_id']]['meta_description']) ? $data['description'][$list['language_id']]['meta_description'] : ''?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_KEYWORDS'); ?>:</label>
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group lang-block lang-<?php echo $list['language_id']; ?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="description[<?php echo $list['language_id']?>][meta_keyword]" data-lang-id="<?php echo $list['language_id']; ?>" value="<?php echo isset($data['description'][$list['language_id']]['meta_keyword']) ? $data['description'][$list['language_id']]['meta_keyword'] : ''?>" class="form-control" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_BLOG_PUBLISH_DATE')?>:</label>
                        <input type="datetime" name="publish_date" value="<?php if (isset($data['publish_date'])) { echo $data['publish_date']; } else { echo date('Y-m-d H:i:s'); }?>" class="form-control datetime">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="align-right">
        <a href="<?php echo $this->url->link('cms/cms', 'store_id=' . $this->request->get['store_id'], 'SSL')?>" class="btn btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?>" />
        <input type="submit" name="save_and_quit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE_AND_CLOSE')?>" />
    </p>
</form>
<script type="text/javascript">
var languages = <?php echo json_encode($languages); ?>;
$(function(){
    var redactorSettings = {
        focus: true,
        tabFocus: false,
        minHeight: 100,
        imageUploadParam: 'uploads',
        imageUpload: base + 'common/images/upload?mode=redactor&token=' + sessionToken
    };
    $('.redactor-intro').redactor(redactorSettings);
    redactorSettings.minHeight = 270;
    $('.redactor-blog').redactor(redactorSettings);
})
</script>
<?php echo $footer?>
