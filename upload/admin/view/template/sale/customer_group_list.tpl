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
                <a href="<?php echo $return?>" class="btn btn-sm btn-primary">
                    <?php echo $text_customer?>
                </a>
                <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary">
                    <?php echo $button_insert; ?>
                </a>
                <a onclick="$('#form').submit();" class="btn btn-sm btn-primary">
                    <?php echo $button_delete; ?>
                </a>
            </div>
        </div>
    </div>
    
    <div class="clearfix"><br /></div>
    <div class="row">
        <div class="col-md-12">
            <?php if ($customer_groups): ?>
            <table class="table table-striped dataTable">
                <thead>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </td>
                        <td>
                            <?php echo $column_name?>
                        </td>
                        <td>
                            <?php echo $column_sort_order?>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customer_groups as $list): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected[]" value="<?php echo $list['order_id']; ?>" <?php if ($list['selected']) { echo 'checked="checked"'; } ?>>
                        </td>
                        <td>
                            <?php echo $list['name']?>
                        </td>
                        <td>
                            <?php echo $list['sort_order']?>
                        </td>
                        <td>
                            <ul class="actions">
                            <?php foreach ($list['action'] as $action): ?>
                            <li>
                                <a href="<?php echo $action['href']?>"><?php echo $action['text']?></a>
                            </li>
                            <?php endforeach; ?>
                            <li>
                                <a href="#delete" class="single-delete"><i class="table-delete"></i></a>
                            </li>
                            </ul>
                        </td>
                    </tr>
                    <?php endforeach?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-info">
                <?php echo $text_no_results?>
            </div>
            <?php endif?>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('.single-delete').each(function(){
        $(this).on('click', function(){
            $(this).parent().parent().parent().find(':input').prop('checked', 1);
            $('#form').submit();
            return false;
        })
    })
})
</script>
<?php echo $footer?>