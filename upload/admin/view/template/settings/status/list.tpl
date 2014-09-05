<?php echo $header; ?>

<script type="text/javascript">
    var token = '<?php echo $this->session->data['token'] ?>';
</script>

<div class="row">
    <div class="col-md-8">
        <?php $i = 0; foreach ($types as $part): $i++; ?>
        <div class="col-md-6">
            <div class="block-flat">
                <div class="header">
                    <h3><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_' . strtoupper(str_replace('_status', '', $part)))?></h3>
                </div>
                <div class="content">
                    <table class="table watch-table">
                        <tbody id="<?php echo $part?>_table"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if ($i % 2 == false) { echo '<div class="clearfix"></div>'; } endforeach ?>
    </div>
    <div class="col-md-4">
        <form action="<?php echo $this->url->link('settings/status/update', 'token='. $this->session->data['token'] . '&new=true', 'SSL')?>" method="post">
            <div class="block-flat">
                <div class="header">
                    <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_NEW_STATUS'); ?></h3>
                </div>

                <div class="content">
                    <div id="edit-form">
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_TYPE')?></label>
                            <select name="type" class="form-control edit-type">
                                <?php foreach ($types as $type): ?>
                                <option value="<?php echo $type?>"><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_' . strtoupper(str_replace('_status', '', $type)))?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS_NAME')?></label>
                            <?php foreach ($languages as $list): ?>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $list['image']?>" alt="<?php echo $list['name']?>">
                                </span>
                                <input type="text" name="name[<?php echo $list['language_id']?>]" class="form-control name-<?php echo $list['language_id']?>">
                            </div>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <hr>

                    <input type="submit" name="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?>">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="hidden">
    <div id="edit-title"><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT_STATUS')?></div>
    <div id="edit-form-remove-confirm"><?php echo Sumo\Language::getVar('SUMO_NOUN_REMOVE_STATUS_CONFIRM')?></div>
    <span id="edit-form-cancel"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></span>
    <span id="edit-form-save"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE'); ?></span>
    <span id="edit-form-remove"><?php echo Sumo\Language::getVar('SUMO_NOUN_REMOVE'); ?></span>
</div>

<?php echo $footer; ?>
