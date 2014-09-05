<?php echo $header; ?>
<div class="col-md-4 col-md-offset-8 page-head-actions align-right">
    <div class="btn-group align-left">
        <a href="<?php echo $new?>" class="btn btn-primary dropdown-toggle"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADD')?></a>
    </div>
</div>
<?php
// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('cms/cms', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
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
        <form method="post" action="<?php echo $this->url->link('cms/cms/remove', 'token=' . $this->session->data['token'], 'SSL'); ?>" class="form">
            <?php if (isset($items)): ?>
            <table class="table list no-border">
                <thead class="no-border">
                    <tr>
                        <th style="width: 45px;">&nbsp;</th>
                        <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                        <th style="width: 140px;"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_VISIBLE'); ?></strong></th>
                        <th style="width: 100px;" class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION'); ?></strong></th>
                        <th style="width: 100px;" class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_META_DESCRIPTION_ABBR'); ?></strong></th>
                        <th style="width: 100px;" class="align-center"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_KEYWORDS_SHORT'); ?></strong></th>
                        <?php if ($type == 'information'): ?><th style="width: 75px;"></td><?php endif?>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody class="no-border-y items">
                    <?php foreach ($items as $list): //echo '<tr><td colspan="8">' . print_r($list,true) .'</td></tr>'; ?>
                    <tr>
                        <td><input type="checkbox" name="selected[]" value="<?php echo $list[$type . '_id']; ?>" class="icheck"></td>
                        <td<?php if (isset($list['parent_id']) && $list['parent_id'] > 0) { echo ' class="indent" style="padding-left: 30px;"'; } ?>>
                            <?php echo $list['title']?>
                            <?php if (isset($list['author'])): ?>
                            <small class="blog-author"><?php echo $list['author']?></small>
                            <?php endif ?>
                        </td>
                        <td>
                            <div class="switch switch-small switch-status" data-on-label="<?php echo Sumo\Language::getVar('SUMO_NOUN_ON'); ?>" data-off-label="<?php echo Sumo\Language::getVar('SUMO_NOUN_OFF'); ?>" data-item-id="<?php echo $list[$type . '_id']; ?>">
                                <input type="checkbox" name="status[]" value="1"<?php if ($list['status']) { ?> checked<?php } ?>>
                            </div>
                        </td>
                        <td class="align-center">
                            <?php
                            if (isset($list['description']) && empty($list['description']) || isset($list['text']) && empty($list['text'])) {
                                $class = '#f41958';
                            }
                            elseif (isset ($list['description']) && strlen($list['description']) <= 25 || isset($list['text']) && strlen($list['text']) <= 25) {
                                $class = '#FED16C';
                            }
                            else {
                                $class = '#629e14';
                            }
                            ?>
                            <i class="fa fa-circle" style="color: <?php echo $class; ?>"></i>
                        </td>
                        <td class="align-center">
                            <?php
                            if (empty($list['meta_description'])) {
                                $class = '#f41958';
                            }
                            else {
                                $class = '#629e14';
                            }
                            ?>
                            <i class="fa fa-circle" style="color: <?php echo $class; ?>"></i>
                        </td>
                        <td class="align-center">
                            <?php
                            if (isset($list['meta_keywords']) && empty($list['meta_keywords']) || isset($list['meta_keyword']) && empty($list['meta_keyword'])) {
                                $class = '#f41958';
                            }
                            else {
                                $class = '#629e14';
                            }
                            ?>
                            <i class="fa fa-circle" style="color: <?php echo $class; ?>"></i>
                        </td>
                        <?php if ($type == 'information'): ?>
                        <td>
                            <a href="<?php echo $this->url->link('cms/cms/sort', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&id=' . $list['information_id'] . '&move=up&sort_order=' . $list['sort_order'], 'SSL'); ?>" class="fa-enlarged"><i class="fa fa-arrow-circle-o-up"></i></a>
                            <a href="<?php echo $this->url->link('cms/cms/sort', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&id=' . $list['information_id'] . '&move=down&sort_order=' . $list['sort_order'], 'SSL'); ?>" class="fa-enlarged"><i class="fa fa-arrow-circle-o-down"></i></a>
                        </td>
                        <?php endif ?>
                        <td class="right">
                            <div class="btn-group">
                                <a href="<?php echo $this->url->link('cms/cms/editor', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&id=' . $list[$type . '_id'] . '&type=' . $type, 'SSL'); ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                <a href="<?php echo $this->url->link('cms/cms/remove', 'token=' . $this->session->data['token'] . '&type=' . $type . '&id=' . $list[$type . '_id'], 'SSL'); ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <div class="row">
                <div class="col-md-4">
                    <div class="table-padding">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo $this->url->link('cms/cms/remove', 'token=' . $this->session->data['token'], 'SSL'); ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-offset-4 col-md-4 align-right">
                    <div class="btn-group align-left">
                        <a href="<?php echo $new?>" class="btn btn-primary dropdown-toggle"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADD')?></a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_CMS_ITEMS')?></div>
            <?php endif ?>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('.switch-status').on('switch-change', function(e, data) {
        var status = 0;
        if (data.value) {
            status = 1;
        }

        $.post('cms/cms/status?token=<?php echo $this->session->data['token']?>', {type: '<?php echo $type?>', id: $(this).data('item-id')});
    })
})
</script>
<?php echo $footer; ?>
