<?php echo $header; ?>
<div class="align-right page-head-actions">
    <div class="btn-group align-left pull-right">
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
<?php
// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('app/widgetsimplesidebar/banner', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
            <?php echo $list['name']?>
        </a>
    </li>
    <?php endforeach?>
</ul>
<?php
endif;
?>

<div class="tab-content">
    <div class="tab-pane active cont">
        <div class="row">
            <div class="col-md-8">
                <form method="post" id="listform">
                    <div class="block-flat">
                        <?php if ($banners) { ?>
                        <table class="table no-border list">
                            <thead class="no-border items">
                                <tr>
                                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                                    <th><strong><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION'); ?></strong></th>
                                    <th style="width: 150px;"></th>
                                    <th style="width: 150px;"></th>
                                </tr>
                            </thead>
                            <tbody class="no-border-y items">
                                <?php foreach ($banners as $id => $list) { ?>
                                <tr>
                                    <td><input type="checkbox" name="selected[]" value="<?php echo $id; ?>" class="icheck"></td>
                                    <td><?php echo $list['title'][$this->config->get('language_id')]?><br /><small><?php echo $list['href'] ?></small></td>
                                    <td><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_' . strtoupper($list['location']))?></td>
                                    <td><?php if (!empty($list['image'])) { echo '<img src="' . $list['image'] . '" alt="Banner preview" title="Banner preview">'; }?></td>
                                    <td class="right">
                                        <div class="btn-group">
                                            <a href="#edit-banner" class="btn btn-sm btn-secondary edit-banner" data-banner="<?php echo $id?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                            <a href="#delete-banner" class="btn btn-sm btn-primary delete-banner" data-banner="<?php echo $id?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-padding">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#deleteAll" id="deleteAll"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-right">
                                <?php if (isset($pagination)) {
                                    echo $pagination;
                                } ?>
                            </div>
                        </div>
                        <?php } else { ?>
                        <p class="well" style="margin-bottom: 0;"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_NO_BANNERS')?></p>
                        <?php } ?>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <form id="bannerform" method="post" data-parsley-validate novalidate>
                    <input type="hidden" name="banner[id]" value="0">
                    <div class="block-flat">
                        <div class="header">
                            <h3 data-edit="<?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_EDIT')?>" data-add="<?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_ADD')?>"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_ADD')?></h3>
                        </div>

                        <div class="content">
                            <div class="form-group">
                                <label for="name" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                                <?php foreach ($languages as $list): ?>
                                <div class="input-group lang-block<?php if ($list['is_default']) { ?> lang-active<?php } ?> lang-<?php echo $list['language_id'];?>">
                                    <span class="input-group-addon">
                                        <img src="view/img/flags/<?php echo $list['image']?>" alt="<?php echo $list['name']; ?>" />
                                    </span>
                                    <input type="text" required data-parsley-length="[1,64]" name="banner[title][<?php echo $list['language_id']?>]" value="" class="form-control" />
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-group">
                                <label for="file" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_IMAGE'); ?>:</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" id="upload-btn" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_CHOOSE_FILE'); ?></button>
                                    </span>
                                    <input class="form-control" required data-parsley-length="[3,128]" readonly="readonly" name="banner[image]" id="upload" value="" />
                                </div>
                                <br />
                                <img id="preview" />
                            </div>

                            <div class="form-group">
                                <label for="url" class="control-label">URL:</label>
                                <input type="text" required data-parsley-length="[3,128]" id="url" name="banner[href]" value="" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="remaining" class="control-label"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION')?>:</label>
                                <select name="banner[location]" id="location" class="form-control">
                                    <option value="home"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_HOME')?></option>
                                    <option value="slider"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_SLIDER')?></option>
                                    <option value="category"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_CATEGORY')?></option>
                                    <option value="product"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_PRODUCT')?></option>
                                    <option value="information"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_INFORMATION')?></option>
                                    <option value="blog"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_BLOG')?></option>
                                    <option value="account"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_LOCATION_ACCOUNT')?></option>
                                </select>
                            </div>

                            <hr>

                            <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_SAVE'); ?>">
                            <a href="#cancel-edit" class="btn btn-secondary" id="btn-cancel"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('#btn-cancel').on('click', function(e) {
        e.preventDefault();
        $('#bannerform').find('input').each(function() {
            if ($(this).attr('type') != 'submit') {
                $(this).val('');
            }
            $('#btn-cancel, #preview').hide();
            $('#bannerform h3').html($('#bannerform h3').data('add'));
        })
    }).hide();
    $('#preview').hide();
    $('#bannerform').on('submit', function(e) {
        e.preventDefault();
        if ($('#bannerform').parsley().isValid()) {
            $.post('app/widgetsimplesidebar/ajax?token=' + sessionToken + '&store_id=<?php echo $current_store?>', {request: 'banner', data: $('#bannerform').serialize()}, function(response) {
                window.location = window.location;
            }, 'json')
        }
    });
    $('.edit-banner').on('click', function(e) {
        e.preventDefault();
        $('#btn-cancel').trigger('click');
        $('#bannerform h3').html($('#bannerform h3').data('edit'));
        $('#btn-cancel').show();
        var elem = $(this);
        $.post('app/widgetsimplesidebar/ajax?token=' + sessionToken + '&store_id=<?php echo $current_store?>', {request: 'banner-edit', id: $(elem).data('banner')}, function(response) {
            if (response.error) {
                window.location = window.location;
            }
            else {
                var data = response.data;
                $.each(data.title, function(id, value) {
                    $('input[name="banner[title][' + id + ']"]').val(value);
                });
                $('input[name="banner[href]"]').val(data.href);
                $('input[name="banner[image]"]').val(data.image);
                $('#preview').attr('src', '../image/' + data.image).show();
                $('select[name="banner[location]"]').val(data.location);
                $('input[name="banner[id]"]').val($(elem).data('banner'));
            }
        }, 'JSON')
    })
    $('.delete-banner').on('click', function(e) {
        e.preventDefault();
        $.post('app/widgetsimplesidebar/ajax?token=' + sessionToken + '&store_id=<?php echo $current_store?>', {request: 'banner-delete', id: $(this).data('banner')}, function(response) {
            window.location = window.location;
        })
    })

    new AjaxUpload('#upload-btn', {
        action: 'common/images/upload?token=' + sessionToken + '&store_id=<?php echo $current_store?>',
        name: 'uploads',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function (file, extension) {
            // Uploading...
        },
        onComplete: function (file, result) {
            if (result['success']) {
                result = result['success'][0];
                $('#upload').val(result['location']);

                // Add a link and image
                $('#upload-btn').prev('img').remove();
                $('#upload-btn').next('a').remove();

                $('#preview').attr('src', '../image/' + result['location']).show();
                $('#upload-btn').removeClass('fu-new').addClass('fu-edit');
                $('#upload-btn i').removeClass('fa-plus-circle').addClass('fa-wrench');
            }
            else {
                var message = 'Er is iets misgegaan met het uploaden.';
                if (result['error']) {
                    message = result['error'];
                }

                // Show error
                alert(message);
            }
        }
    })

})
</script>

<?php echo $footer; ?>
