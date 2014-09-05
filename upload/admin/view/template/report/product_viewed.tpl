<?php echo $header?>
<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?>
    <a href="<?php echo $breadcrumb['href']; ?>">
        <?php echo $breadcrumb['text']; ?>
    </a>
    <?php } ?>
</div>

<div id="pad-wrapper">
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
                <a href="<?php echo $reset; ?>" class="btn btn-sm btn-primary">
                    <?php echo $button_reset; ?>
                </a>
            </div>
        </div>
    </div>
    <div class="clearfix"><br /></div>
    
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped dataTable">
                <thead>
                    <tr>
                        <td><?php echo $column_name; ?></td>
                        <td><?php echo $column_model; ?></td>
                        <td><?php echo $column_viewed; ?></td>
                        <td><?php echo $column_percent; ?></td>
                    </tr>
                </thead>
                <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                    <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['model']; ?></td>
                    <td><?php echo $product['viewed']; ?></td>
                    <td><?php echo $product['percent']; ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr>
                    <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"><br /></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.dataTable').dataTable({
        "sPaginationType": "full_numbers",
        "oLanguage": DATATABLES_LANG,
        "iDisplayLength": 25,
        "aaSorting": [
            [3, 'desc']
        ],
    });
});
</script> 
<?php echo $footer; ?>