<?php echo $header ?>

<div class="row">
    <div class="col-sm-7 col-md-7">
        <div class="block-flat">
            <div class="header"><?php echo Sumo\Language::getVar('SUMO_ADMIN_SETTINGS_MENU_OVERVIEW')?> <a href="#" class="btn btn-xs btn-success pull-right" id="item_add"><i class="fa fa-plus"></i></a></div>
            <div class="content">
                <div class="dd" id="menu_list">
                    <ol class="dd-list">
                        <?php
                        foreach ($items as $list) {
                            echo '<li data-id="' . $list['menu_id'] . '" class="dd-item dd3-item">';
                            echo '<div class="dd-handle dd3-handle"></div>';
                            echo '<div class="dd3-content">' . Sumo\Language::getVar($list['name']) . '</div>';

                            if (isset($list['children'])) {
                                echo '<ol class="dd-list">';
                                foreach ($list['children'] as $child) {
                                    echo '<li data-id="' . $child['menu_id'] . '" class="dd-item dd3-item">';
                                    echo '<div class="dd-handle dd3-handle"></div>';
                                    echo '<div class="dd3-content">' . Sumo\Language::getVar($child['name']) . '</div>';
                                    echo '</li>';
                                }
                                echo '</ol>';
                            }

                            echo '</li>';
                        }
                        ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5 col-md-5">
        <div class="block-flat" id="edit-block">
            <div class="header" id="header-edit"><?php echo Sumo\Language::getVar('SUMO_ADMIN_SETTINGS_MENU_EDIT')?></div>
            <div class="header" id="header-add"><?php echo Sumo\Language::getVar('SUMO_ADMIN_SETTINGS_MENU_ADD')?></div>
            <div class="content">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?>:</label>
                    <input type="text" name="name" id="item_name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESCRIPTION')?>:</label>
                    <input type="text" name="description" id="item_description" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ICON')?>:</label>
                    <input type="text" name="icon" id="item_icon" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_PARENT')?>:</label>
                    <select name="parent_id" id="item_parent_id" class="form-control">
                        <option value="0"><?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_PARENT_NONE')?></option>
                        <?php foreach ($parents as $list): ?>
                        <option value="<?php echo $list['menu_id']?>"><?php echo Sumo\Language::getVar($list['name'])?></option>
                        <?php endforeach?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">URL:</label>
                    <input type="text" name="url" id="item_url" class="form-control">
                </div>
                <a href="#" class="btn btn-secondary" id="item_cancel"><?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?></a>
                <a href="#" class="btn btn-primary" id="item_save"><?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?></a>
                <a href="#" class="btn btn-primary" id="item_save_and_close"><?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE_AND_CLOSE')?></a>

                <br /><br />
                <a href="#" class="btn btn-danger md-trigger" data-modal="confirm-delete" id="item-delete"><i class="fa fa-trash-o"></i><?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_DELETE')?></a>
            </div>
        </div>
    </div>
</div>

<div id="confirm-delete" class="md-modal full-color danger md-effect-8">
    <div class="md-content">
        <div class="modal-header">
            <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_DELETE')?></h3>
            <button class="close md-close" aria-hidden="true" data-dismiss="modal" type="button">&times;</button>
        </div>
        <div class="modal-body">
            <div class="text-center">
                <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE')?></h4>
                <h5><?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_DESCRIPTION')?></h5>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary btn-mono3 md-close" data-dismiss="modal" type="button"><?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?></button>
            <button class="btn btn-primary btn-mono3" id="item_delete_confirm" type="button"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM')?></button>
        </div>
    </div>
</div>

<div class="md-overlay"></div>
<!-- @todo: Move to own JS file -->
<script type="text/javascript">
var ajaxURL = 'settings/menu/ajax?token=<?php echo $this->session->data['token']?>';
$(function(){
    $('.md-trigger').modalEffects();

    $('.md-trigger').on('click', function(e) {
        e.preventDefault();
    })
    $('.dd').nestable({
        maxDepth: 2,
        callback: function() {
            var data = $('.dd').nestable('serialize');
            $.post(
                ajaxURL,
                {
                    order: true,
                    data: data
                },
                function (json) {
                    var text = json.order;
                    $.gritter.add({
                        title: "<?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_SORT_SAVED_TITLE')?>",
                        text: text,
                        class_name: 'clean',
                        time: ''
                    })
                },
                'JSON'
            )
        }
    });

    $('#edit-block .header,#item-delete').hide();
    $('#header-add').show();

    $('.dd3-content').each(function(){
        $(this).append('<a href="#" class="edit"><i class="fa fa-pencil pull-right"></i></a>');
    })

    $('#item_add').on('click', function(e){
        e.preventDefault();
        $('#edit-block').find(':input').val('');
        $('#edit-block .header').hide();
        $('#edit-block').attr('menu-id', '0');
        $('#header-add').show();
        $('#item-delete').hide();
        if ($('#edit-block').is(':visible') == false) {
            $('#edit-block').slideDown();
        }
    })
    $('.edit').on('click', function(e){
        e.preventDefault();
        $('#item-delete').show();
        if ($('#header-edit').is(':visible') == false) {
            $('#edit-block .header').hide();
            $('#header-edit').show();
        }
        if ($('#edit-block').is(':visible') == false) {
            $('#edit-block').slideDown();
        }
        $('#edit-block').attr('menu-id', $(this).parent().parent().attr('data-id'));
        $('#edit-block :input').prop('disabled', 1);
        $.post(
            ajaxURL,
            {
                get: true,
                id: $('#edit-block').attr('menu-id')
            },
            function (json) {
                $('#item_name').val(json.name);
                $('#item_description').val(json.description);
                $('#item_icon').val(json.icon);
                $('#item_parent_id').val(json.parent_id);
                $('#item_url').val(json.url);
                $('#edit-block :input').prop('disabled', 0);
            },
            'JSON'
        );
    })
    $('#item_save').on('click', function(){
        saveItem();
        return false;
    })
    $('#item_save_and_close').on('click', function(){
        $('#edit-block').slideUp();
        saveItem();
        return false;
    })
    $('#item_delete_confirm').on('click', function(e){
        $.post(
            ajaxURL,
            {
                delete: true,
                id: $('#edit-block').attr('menu-id')
            },
            function (json) {
                window.location = window.location;
            },
            'json'
        );
    })
});

function saveItem() {
    var type = $('#edit-block .header:visible').attr('id');
    if (type == 'header-add') {
        type = 'add';
    }
    else {
        type = 'edit';
    }
    var id = $('#edit-block').attr('menu-id');
    $.post(
        ajaxURL,
        {
            save: true,
            type: type,
            data: $('#edit-block :input').serializeArray(),
            id  : id
        },
        function (json) {
            $.gritter.add({
                title: "<?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_SAVED_TITLE')?>",
                text: "<?php echo Sumo\Language::getVar('SUMO_NOUN_ITEM_SAVED_CONTENT')?>",
                class_name: 'clean',
                time: ''
            })
            if (type == 'add') {
                $('#edit-block').slideUp();
                window.location = window.location;
            }
        }
    )

}
</script>

<?php echo $footer ?>
