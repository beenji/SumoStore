<?php echo $header?>

<div class="block-flat">
    <table class="table no-border list">
        <thead class="no-border">
            <tr>
                <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPNAME'); ?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_PLURAL')?></strong></th>
                <th><strong><?php echo Sumo\Language::getVar('APP_WIDGET_SS_USP_PLURAL')?></strong></th>
                <th style="width: 250px;"></th>
            </tr>
        </thead>
        <tbody class="no-border-y">
            <?php foreach ($stores as $list): ?>
            <tr>
                <td><?php echo $list['name']?><br /><small><a class="store-url"><?php echo $list['base_default'] . '://' . $list['base_' . $list['base_default']]?></a></small></td>
                <td><span class="get-total" data-type="banner" data-store="<?php echo $list['store_id']?>">0</span></td>
                <td><span class="get-total" data-type="usp" data-store="<?php echo $list['store_id']?>">0</span></td>
                <td class="right">
                    <div class="btn-group">
                        <a href="<?php echo $this->url->link('app/widgetsimplesidebar/banner', 'store_id=' . $list['store_id'] . '&token=' . $this->session->data['token'], 'SSL')?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_BANNER_PLURAL_EDIT')?></a>
                        <a href="<?php echo $this->url->link('app/widgetsimplesidebar/usp', 'store_id=' . $list['store_id'] . '&token=' . $this->session->data['token'], 'SSL')?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('APP_WIDGET_SS_USP_PLURAL_EDIT')?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
$(function() {
    $('.store-url').each(function() {
        $(this).attr('href', $(this).html()).attr('target', '_blank');
    })
    $('.get-total').each(function() {
        var elem = $(this);
        $.post('app/widgetsimplesidebar/ajax?store_id=' + elem.data('store') + '&token=' + sessionToken, {request: 'total', type: elem.data('type')}, function(response) {
            elem.html(response.total);
        }, 'json');
    })
})
</script>

<?php echo $footer?>
