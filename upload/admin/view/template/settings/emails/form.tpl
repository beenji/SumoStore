<?php echo $header ?>

<div class="page-head-actions align-right">
    <a href="#preview-mail" id="preview-mail" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PREVIEW_MAIL'); ?></a>
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

<form method="post">
    <?php if (!empty($error)): ?>
    <div class="col-md-12">
        <div class="alert alert-danger">
            <p><?php echo $error ?></p>
        </div>
    </div>
    <?php endif ?>
    <div class="col-md-12">
        <div class="col-md-8">
            <?php foreach ($languages as $language_id => $list) { ?>
            <div class="block-flat lang-<?php echo $list['language_id']; ?> lang-block<?php if ($list['is_default']) { ?> lang-active<?php } ?>">

                <div class="form-group">
                    <label for="url" class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_TITLE')?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <input type="text" class="form-control preview-title" id="url-<?php echo $language_id; ?>" name="content[<?php echo $list['language_id']?>][title]" value="<?php if (isset($mail['content'][$list['language_id']]['title'])) { echo $mail['content'][$list['language_id']]['title']; } ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="desc-<?php echo $language_id; ?>" class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_CONTENT'); ?>:</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <textarea name="content[<?php echo $list['language_id']?>][content]" class="form-control allow-tab preview-content" rows="6"><?php if(isset($mail['content'][$list['language_id']]['content'])) { echo $mail['content'][$list['language_id']]['content']; } ?></textarea>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="block-flat">
                <div class="header">
                    <h3><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_SHORTCODE_PLURAL')?></h3>
                </div>
                <div class="content">
                    <table class="table no-border">
                        <thead class="no-border-x">
                            <tr>
                                <th><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_SHORTCODE_SINGULAR')?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_SHORTCODE_RESULT')?></th>
                            </tr>
                        </thead>
                        <tbody class="no-border-x">
                            <tr>
                                <td>{name}</td>
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_NAME')?></td>
                            </tr>
                            <tr>
                                <td>{firstname}</td>
                                <td>John</td>
                            </tr>
                            <tr>
                                <td>{lastname}</td>
                                <td>Doe</td>
                            </tr>
                            <tr>
                                <td>{status}</td>
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_NAME')?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="block-flat">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_NAME')?>:</label>
                    <input type="text" name="name" value="<?php if(isset($mail['name'])) { echo $mail['name']; }?>" class="form-control">
                    <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_NAME_HELPER')?></span>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_EVENT')?>:</label>
                    <input type="text" name="event_key" value="<?php if(isset($mail['event_key'])) { echo $mail['event_key']; }?>" class="form-control">
                    <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_EVENT_KEY')?></span>
                </div>
                <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
$(function(){
    $('#preview-mail').on('click', function(e) {
        e.preventDefault();
        var content = $('.preview-content:visible').val();
        if (content.length) {
            bootbox.dialog({
                title:      $('.preview-title:visible').val(),
                message:    $('.preview-content:visible').val()
            });
        }
        else {
            $.gritter.add({
                text: '<?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_ERROR_CONTENT')?>',
                class_name: 'clean',
                time: ''
            })
        }
    })
})
</script>
<?php echo $footer ?>
