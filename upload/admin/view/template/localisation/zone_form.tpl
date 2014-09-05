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
                        <?php echo $entry_status?>
                    </label>
                    <div class="col-md-5">
                        <div class="radio-inline">
                            <input type="radio" name="status" value="1" <?php if ($status) { echo 'checked="checked"'; } ?>>
                            <?php echo $text_enabled?>
                        </div>
                        <div class="radio-inline">
                            <input type="radio" name="status" value="0" <?php if (!$status) { echo 'checked="checked"'; } ?>>
                            <?php echo $text_disabled?>
                        </div>
                    </div>
                </div>
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
                <div class="form-group">
                    <label class= "col-md-2 control-label">
                        <?php echo $entry_code?>
                    </label>
                    <div class="col-md-1">
                        <input type="text" name="code" value="<?php echo $code?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        <?php echo $entry_country?>
                    </label>
                    <div class="col-md-3">
                        <select name="country_id" class="form-control">
                            <?php foreach ($countries as $country) { ?>
                            <?php if ($country['country_id'] == $country_id) { ?>
                            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php echo $footer; ?>