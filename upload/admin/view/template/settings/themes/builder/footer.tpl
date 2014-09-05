<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" action="" class="form-horizontal">

                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_BLOCKS_AMOUNT')?>:
                    </label>
                    <div class="col-md-6">
                        <select name="footer[blocks][amount]" class="form-control">
                            <?php
                            for($i = 0; $i <= 4; $i++) {
                                $selected = '';
                                if ((isset($settings['blocks']['amount']) && $settings['blocks']['amount'] == $i) || !isset($settings['blocks']['amount']) && $i == 3) {
                                    $selected = ' selected';
                                }
                                echo '<option' . $selected . '>' . $i . '</option>';
                            } ?>
                        </select>
                        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_BLOCKS')?></span>
                    </div>
                    <div class="col-md-3 align-right">
                        <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>" class="btn btn-primary">
                    </div>
                </div>

            <?php for ($i = 0; $i <= 4; $i++): ?>
            <div class="footer-block" <?php if (!$i) { echo 'style="display:none;"'; }?>>
                <div class="form-group">
                    <?php foreach ($languages as $list): ?>
                    <label class="col-md-3 control-label">
                        <?php echo Sumo\Language::getVar('SUMO_NOUN_TITLE') . ' ' . $i ?>:
                    </label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="footer[blocks][blocks][<?php echo $i?>][title][<?php echo $list['language_id']?>]" value="<?php if (isset($settings['blocks']['blocks'][$i]['title'][$list['language_id']])) { echo $settings['blocks']['blocks'][$i]['title'][$list['language_id']]; } ?>" class="form-control product-name" Sumo\language="<?php echo $list['language_id']?>" />
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Type content:</label>
                    <div class="col-md-9">
                        <select name="footer[blocks][blocks][<?php echo $i?>][type]" class="form-control choose-type">
                            <option value=""><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT')?></option>
                            <option value="content" <?php if (isset($settings['blocks']['blocks'][$i]['type']) && $settings['blocks']['blocks'][$i]['type'] == 'content') { echo 'selected'; }?>>Content</option>
                            <option value="links" <?php if (isset($settings['blocks']['blocks'][$i]['type']) && $settings['blocks']['blocks'][$i]['type'] == 'links') { echo 'selected'; }?>>Links</option>
                        </select>
                    </div>
                </div>

                <div class="footer-block-links">
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="padding-top: 11px;">Links:</label>
                        <div class="col-md-9">
                            <table class="table table-urls no-border" row="<?php echo $i?>">
                                <thead class="no-border">
                                    <tr>
                                        <th style="width: 60%" colspan="2"><strong>URL</strong></th>
                                        <th style="width: 30%"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DISPLAY_TITLE')?></strong></th>
                                        <th class="right" style="padding-right: 14px;"><a href="javascript:;" class="btn-add-url"><i style="font-size: 14px;" class="fa fa-plus"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody class="no-border-y">
                                    <?php
                                    if (isset($settings['blocks']['blocks'][$i]['links'])) {
                                        foreach ($settings['blocks']['blocks'][$i]['links'] as $nr => $list):
                                            if (empty($list['url'])) {
                                                continue;
                                            }?>
                                    <tr number="<?php echo $nr?>">
                                        <td>
                                            <input type="text" name="footer[blocks][blocks][<?php echo $i?>][links][<?php echo $nr?>][url]" class="form-control input-url" value="<?php echo $list['url']?>">
                                        </td>
                                        <td><select class="form-control link-holder"></select></td>
                                        <td>
                                            <?php foreach ($languages as $lang): ?>
                                            <div class="input-group">
                                                <span class="input-group-addon"><img src="view/img/flags/<?php echo $lang['image']?>" title="<?php echo $lang['name']?>" /></span>
                                                <input type="text" name="footer[blocks][blocks][<?php echo $i?>][links][<?php echo $nr?>][name][<?php echo $lang['language_id']?>]" value="<?php if (isset($list['name'][$lang['language_id']])) { echo $list['name'][$lang['language_id']]; } ?>" class="form-control product-name" Sumo\language="<?php echo $lang['language_id']?>" />
                                            </div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td><a href="javascript:;" class="btn-remove-url"><i class="fa fa-trash-o"></i></a></td>
                                    </tr>
                                        <?php endforeach;
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <p><span class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_LINKS')?></span></p>
                        </div>
                    </div>
                </div>

                <div class="footer-block-content">
                    <div class="form-group">
                        <?php foreach ($languages as $list): ?>
                        <label class="col-md-3 control-label">Content:</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                                </span>
                                <textarea class="redactor form-control" name="footer[blocks][blocks][<?php echo $i?>][content][<?php echo $list['language_id']?>]"><?php if (isset($settings['blocks']['blocks'][$i]['content'][$list['language_id']])) { echo $settings['blocks']['blocks'][$i]['content'][$list['language_id']]; } ?></textarea>
                            </div>
                        </div>
                        <?php endforeach?>
                    </div>
                </div>
            </div>
            <?php endfor?>

            <div class="clearfix"></div>

            <hr />

            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT')?>:
                </label>
                <div class="col-md-9">
                    <?php foreach ($languages as $list): ?>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <textarea name="footer[copyright][notice][<?php echo $list['language_id']?>]" class="redactor form-control"><?php if (isset($settings['copyright']['notice'][$list['language_id']])) { echo $settings['copyright']['notice'][$list['language_id']]; } else { echo '<p>&copy; Copyright [websitename], 2013-[currentyear]</p>'; } ?></textarea>
                    </div>
                    <?php endforeach?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FOOTER_COPYRIGHT_POWERED_BY')?>
                </label>
                <div class="col-md-9">
                    <div class="radio-inline">
                        <input type="radio" name="footer[copyright][powered_by]" value="1" <?php if ((isset($settings['copyright']['powered_by']) && $settings['copyright']['powered_by']) || !isset($settings['copyright']['powered_by'])) { echo 'checked'; } ?>>
                        <?php echo Sumo\Language::getVar('YES') ?>
                    </div>
                    <div class="radio-inline">
                        <input type="radio" name="footer[copyright][powered_by]" value="0" <?php if (isset($settings['copyright']['powered_by']) && !$settings['copyright']['powered_by']) { echo 'checked'; } ?>>
                        <?php echo Sumo\Language::getVar('NO') ?>
                    </div>

                    <p class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HELP_FOOTER_COPYRIGHT_POWERED_BY')?></p>
                </div>
            </div>

            <p class="align-right">
                <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>" class="btn btn-primary">
            </p>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function() {
    var firstrun = 0;
    $('.choose-type').each(function() {
        $(this).on('change click', function() {
            var value = $(this, 'option:selected').val();
            var parent = $(this).parent().parent().parent();
            parent.find('.footer-block-links').hide();
            parent.find('.footer-block-content').hide();
            parent.find('.footer-block-' + value).show();
        });
    });

    $('.footer-block-content').hide();
    $('.footer-block-links').hide();
    var firstrun = true;
    $('select[name="footer[blocks][amount]"]').on('change', function() {
        var until   = parseInt($('option:selected', this).val());
        var current = 0;
        $('.footer-block').each(function() {
            if (!current) {
                $(this).hide();
                current++;
            }
            else if (current <= until) {
                if ($(this).is(':visible') == false) {
                    $(this).slideDown();
                }
                $(this).find('.choose-type').trigger('click');
                current++;
                if (current > until) {
                    $(this).find('hr').hide();
                }
                else {
                    $(this).find('hr').show();
                }
            }
            else {
                if ($(this).is(':visible')) {
                    $(this).slideUp();
                }
                if (firstrun) {
                    firstrun = false;
                    $(this).hide();
                }
            }
        })
    }).trigger('change');

    var links = '<option value=""><?php echo Sumo\Language::getVar('SUMO_FORM_SELECT_CHOOSE')?></option>';
    links += '<option value="/account/account"><?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT_TITLE')?></option>';
    links += '<option value="/account/order"><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER')?></option>';
    links += '<option value="/account/newsletter"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEWSLETTER'); ?></option>';
    links += '<option value="/account/return/insert"><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN')?></option>';
    links += '<option value="/account/voucher"><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_VOUCHER_TITLE')?></option>';
    links += '<option value="/affiliate/account"><?php echo Sumo\Language::getVar('SUMO_NOUN_AFFILIATE_TITLE')?></option>';
    links += '<option value="/information/contact"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONTACT_US')?></option>';
    <?php foreach ($pages as $page): ?>
    links += '<option value="/information/information?information_id=<?php echo $page['information_id']?>"><?php echo htmlentities($page['title'])?></option>';
    <?php endforeach; ?>

    function addLinks() {
        $('.link-holder').each(function(){
            $(this).html(links);
            $(this).on('change', function() {
                var value = $('option:selected', this).val();
                if (value != '') {
                    $(this).parent().parent().find('.input-url').val(value);
                }
            });
        });
    }
    addLinks();
    function removeLink() {
        $('.btn-remove-url').on('click', function() {
            $(this).parent().parent().remove();
        })
    }
    removeLink();

    $('.btn-add-url').each(function(){
        $(this).on('click', function(){
            var body = $(this).parent().parent().parent().parent().find('tbody');
            var newI = 1;
            $(body).find('tr').each(function(){
                if ($(this).attr('number')) {
                    if (newI <= $(this).attr('number')) {
                        newI = $(this).attr('number');
                    }
                    newI++;
                }
            })
            if (body.parent().attr('row')) {
                var rowI = body.parent().attr('row');
            }
            else {
                rowI = 1;
            }
            //$settings['link_blocks']['blocks'][$i]['links']
            body.append('<tr><td><input type="text" name="footer[blocks][blocks][' + rowI + '][links][' + newI + '][url]" class="form-control input-url"></td><td><select class="form-control link-holder"></select></td><td><?php foreach ($languages as $list): ?><div class="input-group"><span class="input-group-addon"><img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" /></span><input type="text" name="footer[blocks][blocks][' + rowI + '][links][' + newI + '][name][<?php echo $list['language_id']?>]" value="" class="form-control product-name" language="<?php echo $list['language_id']?>" /></div><?php endforeach; ?></td><td class="right" style="padding-top: 16px;"><a href="javascript:;" class="btn-remove-url"><i class="fa fa-trash-o" style="font-size: 14px;"></i></a></td></tr>');
            addLinks();
            removeLink();
        }).trigger('click');
    });

    $('.icons_enabled').hide();
    $('input[name="footer[copyright][icons_enabled]"]').on('change', function() {
        if ($(this).is(':checked')) {
            if ($(this).val() != undefined && $(this).val() == 1) {
                if ($('.icons_enabled').is(':visible') == false) {
                    $('.icons_enabled').slideDown();
                }
            }
            else {
                if ($('.icons_enabled').is(':visible')) {
                    $('.icons_enabled').slideUp();
                }
            }
        }
    }).trigger('change');
})
</script>
