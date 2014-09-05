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
    <?php if ($success) { ?>
        <div class="alert alert-success">
            <i class="icon-ok-sign"></i>
            <?php echo $success; ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-4">
            <h4><img src="img/icons/cats-24.png" alt="" /> <?php echo $heading_title; ?></h4>
        </div>
        <div class="col-md-8">
            <div class="buttons pull-right">
                <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo $button_insert; ?></a>
                <a onclick="$('#form').submit();" class="btn btn-sm btn-primary"><?php echo $button_delete; ?></a>
            </div>
        </div>
    </div>
    
    <div class="clearfix"><br /></div>
    <div class="row">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
            <div class="col-md-9">
                <table class="table table-striped dataTable">
                    <thead>
                        <tr>
                            <td width="1" style="text-align: center;">
                                <input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                            </td>
                            <td class="left">
                                <?php echo $column_group; ?>
                            </td>
                            <td style="width: 60px;">
                                <?php echo $column_action; ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($filters) { ?>
                        <?php foreach ($filters as $filter) { ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="selected[]" value="<?php echo $filter['filter_group_id']; ?>" <?php if ($filter['selected']) { echo 'checked="checked"'; }?> />
                            </td>
                            <td class="left">
                                <?php echo $filter['name']; ?>
                            </td>
                            <td class="right">
                                <?php foreach ($filter['action'] as $action) { ?>
                                <a href="<?php echo $action['href']; ?>">
                                    <i class="table-edit"></i>
                                </a>
                                <?php } ?>
                                &nbsp;
                                <a href="#delete" class="single-delete">
                                    <i class="table-delete"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } 
                        } else { ?>
                        <tr>
                            <td class="center" colspan="4">
                                <?php echo $text_no_results; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <h5><?php echo $info_title?></h5>
                <?php echo $info_content?>
            </div>
        </form>
    </div>
    <div class="clearfix"><br /></div>
</div>
<script type="text/javascript">
$(function(){
    $('.dataTable').dataTable({
        "sPaginationType": "full_numbers",
        "oLanguage": DATATABLES_LANG,
        "bSort": false,
        "iDisplayLength": 25,
        "bAutoWidth": false
    });
    $('.single-delete').each(function(){
        $(this).on('click', function(){
            $(this).parent().parent().find(':input').prop('checked', 1);
            $('#form').submit();
            return false;
        })
    })
});
</script>
<?php echo $footer; ?>