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
            <table class="table table-striped dataTable">
            <thead>
                <tr>
                    <td><?php echo $column_ip; ?></td>
                    <td><?php echo $column_customer; ?></td>
                    <td><?php echo $column_url; ?></td>
                    <td><?php echo $column_referer; ?></td>
                    <td><?php echo $column_date_added; ?></td>
                    <td><?php echo $column_action; ?></td>
                </tr>
            </thead>
            <tbody>
            <?php if ($customers) { ?>
            <?php foreach ($customers as $customer) { ?>
            <tr>
                <td><a href="http://whatismyipaddress.com/ip/<?php echo $customer['ip']; ?>" target="_blank"><?php echo $customer['ip']; ?></a></td>
                <td><?php echo $customer['customer']; ?></td>
                <td><a href="<?php echo $customer['url']; ?>" target="_blank"><?php echo implode('<br/>', str_split($customer['url'], 30)); ?></a></td>
                <td><?php if ($customer['referer']) { ?>
                <a href="<?php echo $customer['referer']; ?>" target="_blank"><?php echo implode('<br/>', str_split($customer['referer'], 30)); ?></a>
                <?php } ?></td>
                <td><?php echo $customer['date_added']; ?></td>
                <td><?php foreach ($customer['action'] as $action) { ?>
                <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a>
                <?php } ?></td>            
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

    location = url;
}

$(document).ready(function() {
    $('.datepicker').datepicker({dateFormat: 'yyyy-mm-dd'});
    $('.dataTable').dataTable({
        "sPaginationType": "full_numbers",
        "oLanguage": DATATABLES_LANG,
        "iDisplayLength": 25,
        "aaSorting": [
            [4, 'desc']
        ],
    });
});
</script> 
<?php echo $footer; ?>