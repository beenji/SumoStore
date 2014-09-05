<?php echo $header; ?>

<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?>
    <a href="<?php echo $breadcrumb['href']; ?>">
        <?php echo $breadcrumb['text']; ?>
    </a>
    <?php } ?>
</div>

<div id="pad-wrapper">

    <?php if ($error_warning) { ?>
        <div class="alert alert-warning">
            <i class="icon-warning-sign"></i>
            <?php echo $error_warning; ?>
        </div>
    <?php } ?>
  
    <div class="row">
        <div class="col-md-4">
            <h4><img src="img/icons/cats-24.png" alt="" /> <?php echo $heading_title; ?></h4>
        </div>
        <div class="col-md-8">
            <div class="buttons pull-right">
                <a onclick="$('#form').submit();" class="btn btn-sm btn-primary"><?php echo $button_save; ?></a>
                <a href="<?php echo $cancel?>" class="btn btn-sm btn-primary"><?php echo $button_cancel; ?></a>
            </div>
        </div>
    </div>
    <div class="clearfix"><br /></div>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" autocomplete="off" class="form-horizontal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        <?php echo $entry_name?> *
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="name" value="<?php echo $name?>" class="form-control">
                        <?php if ($error_name): ?>
                        <span class="has-error"><span class="help-block"><?php echo $error_name?></span></span>
                        <?php endif?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4><?php echo $entry_access?></h4>
                <p>
                    <a onclick="$(this).parent().parent().find(':checkbox').attr('checked', true);">
                        <?php echo $text_select_all; ?>
                    </a> / 
                    <a onclick="$(this).parent().parent().find(':checkbox').attr('checked', false);">
                        <?php echo $text_unselect_all; ?>
                    </a>
                </p>
                <table class="table table-striped">
                    <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="permission[access][]"  value="<?php echo $permission?>" <?php if (in_array($permission, $access)) { echo 'checked="checked"'; } ?>>
                        </td>
                        <td>
                            <?php echo $permission?>
                        </td>
                    </tr>
                    <?php endforeach?>
                </table>
            </div>
            <div class="col-md-6">
                <h4><?php echo $entry_modify?></h4>
                <p>
                    <a onclick="$(this).parent().parent().find(':checkbox').attr('checked', true);">
                        <?php echo $text_select_all; ?>
                    </a> / 
                    <a onclick="$(this).parent().parent().find(':checkbox').attr('checked', false);">
                        <?php echo $text_unselect_all; ?>
                    </a>
                </p>
                <table class="table table-striped">
                    <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="permission[modify][]"  value="<?php echo $permission?>" <?php if (in_array($permission, $modify)) { echo 'checked="checked"'; } ?>>
                        </td>
                        <td>
                            <?php echo $permission?>
                        </td>
                    </tr>
                    <?php endforeach?>
                </table>
            </div>
        </div>
    </form>
</div>
<?php echo $footer; ?> 