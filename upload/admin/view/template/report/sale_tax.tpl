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
                    <td>
                        <?php echo $entry_date_start; ?>
                        <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" class="datepicker form-control" size="12" />
                    </td>
                    <td>
                        <?php echo $entry_date_end; ?>
                        <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" class="datepicker form-control" size="12" />
                    </td>
                    <td>
                        <?php echo $entry_group; ?>
                        <select name="filter_group" class="form-control">
                            <?php foreach ($groups as $groups) { ?>
                            <?php if ($groups['value'] == $filter_group) { ?>
                            <option value="<?php echo $groups['value']; ?>" selected="selected"><?php echo $groups['text']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $groups['value']; ?>"><?php echo $groups['text']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <?php echo $entry_status; ?>
                        <select name="filter_order_status_id" class="form-control">
                            <option value="0"><?php echo $text_all_status; ?></option>
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
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
                        <td><?php echo $column_date_start; ?></td>
                        <td><?php echo $column_date_end; ?></td>
                        <td><?php echo $column_title; ?></td>
                        <td><?php echo $column_orders; ?></td>
                        <td><?php echo $column_total; ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($orders) { ?>
                    <?php foreach ($orders as $order) { ?>
                    <tr>
                        <td><?php echo $order['date_start']; ?></td>
                        <td><?php echo $order['date_end']; ?></td>
                        <td><?php echo $order['title']; ?></td>
                        <td><?php echo $order['orders']; ?></td>
                        <td><?php echo $order['total']; ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr>
                        <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
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
        
    var filter_group = $('select[name=\'filter_group\']').attr('value');
    
    if (filter_group) {
        url += '&filter_group=' + encodeURIComponent(filter_group);
    }
    
    var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
    
    if (filter_order_status_id != 0) {
        url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
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