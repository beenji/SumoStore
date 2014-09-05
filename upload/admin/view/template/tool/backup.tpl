<?php echo $header; ?>

<?php if ($error_warning) { ?>
<script type="text/javascript">
    var formError = '<?php echo $error_warning; ?>';
</script>
<?php } ?>

<ul class="nav nav-tabs">
    <li class="active"><a href="#backup" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_BACKUP'); ?></a></li>
    <li><a href="#restore" data-toggle="tab"><?php echo Sumo\Language::getVar('SUMO_NOUN_RESTORE'); ?></a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="backup">
        <form action="<?php echo $backup; ?>" method="post" class="form-horizontal">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn btn-default" href="javascript:;" rel="selectAllTables"><?php echo Sumo\Language::getVar('SUMO_NOUN_SELECT_ALL'); ?></a>  
                        <a class="btn btn-default" href="javascript:;" rel="deselectAllTables"><?php echo Sumo\Language::getVar('SUMO_NOUN_DESELECT_ALL'); ?></a>
                    </div>
                </div>

                <div class="col-md-6 align-right">
                    <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_BACKUP'); ?>" class="btn btn-primary">
                </div>
            </div>

            <div class="form-group">
                <label for="tables" class="control-label col-md-2"><?php echo Sumo\Language::getVar('SUMO_NOUN_TABLES'); ?>:<br /><span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_TABLES_HELP'); ?></span></label>
                <div class="control-group col-md-10">
                    <select name="backup[]" id="tables" class="form-control" multiple="multiple" size="15">
                        <?php foreach ($tables as $table) { ?>
                        <option value="<?php echo $table; ?>" selected="selected"><?php echo $table; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div> 
        </form>
    </div>

    <div class="tab-pane" id="restore">
        <form action="<?php echo $restore; ?>" method="post" class="form-horizontal">
            <div class="row">
                <div class="col-md-12 align-right">
                    <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_RESTORE'); ?>" class="btn btn-primary">
                </div>    
            </div>
            
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_RESTORE_FILE'); ?>:</label>
                <div class="col-md-3">
                    <input type="file" name="import" class="form-control">
                </div>
            </div>
        </form>
    </div>
</div>

<?php echo $footer; ?>