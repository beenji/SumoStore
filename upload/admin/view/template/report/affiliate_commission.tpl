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
    
    <div class="row">
        <div class="col-md-4">
            <h4><img src="img/icons/cats-24.png" alt="" /> <?php echo $heading_title; ?></h4>
        </div>
    </div>
    <div class="clearfix"><br /></div>
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <tr>
                    <td width="20%">
                        <?php echo $entry_date_start; ?>
                        <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" class="datepicker form-control" size="12" />
                    </td>
                    <td width="20%">
                        <?php echo $entry_date_end; ?>
                        <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" class="datepicker form-control" size="12" />
                    </td>
                    <td width="50%">
                        &nbsp;
                    </td>
                    <td>
                        <br />
                        <a onclick="filter();" class="btn btn-sm btn-primary">
                            <?php echo $button_filter; ?>
                        </a>
                    </td>
                </tr>
            </table>
            <table class="table table-striped dataTable">
                <thead>
                    <tr>
                        <td><?php echo $column_affiliate; ?></td>
                        <td><?php echo $column_email; ?></td>
                        <td><?php echo $column_status; ?></td>
                        <td><?php echo $column_commission; ?></td>
                        <td><?php echo $column_orders; ?></td>
                        <td><?php echo $column_total; ?></td>
                        <td><?php echo $column_action; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($affiliates) { ?>
                    <?php foreach ($affiliates as $affiliate) { ?>
                    <tr>
                        <td><?php echo $affiliate['affiliate']; ?></td>
                        <td><?php echo $affiliate['email']; ?></td>
                        <td><?php echo $affiliate['status']; ?></td>
                        <td><?php echo $affiliate['commission']; ?></td>
                        <td><?php echo $affiliate['orders']; ?></td>
                        <td><?php echo $affiliate['total']; ?></td>
                        <td><?php foreach ($affiliate['action'] as $action) { ?>
                        <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a>
                        <?php } ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr>
                        <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"><br /></div>
</div>
<script type="text/javascript">
function filter() {
    url = 'index.php?route=report/sale_order&token=<?php echo $token; ?>';
    
    var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
    
    if (filter_date_start) {
        url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
    
    if (filter_date_end) {
        url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }

    location = url;
}

$(document).ready(function() {
    $('.datepicker').datepicker({dateFormat: 'yyyy-mm-dd'});
    $('.dataTable').dataTable({
        "sPaginationType": "full_numbers",
        "oLanguage": DATATABLES_LANG,
        "iDisplayLength": 25,
    });
});
</script> 
<?php echo $footer; ?>