<?php if (isset($filters) && count($filters)): ?>
<div class="sidebar sidebar-filters">
    <div class="block">
        <div class="header">
            <h3><?php echo Sumo\Language::getVar('WSP_NOUN_FILTER_PLURAL')?></h3>
        </div>
        <div class="content">
            <?php foreach ($filters as $filter): ?>
            <h4><?php echo $filter['name']?></h4>
            <ul>
                <?php foreach ($filter['filters'] as $list): ?>
                <li><input type="checkbox" name="filter[<?php echo $list['attribute_id']?>]" value="<?php echo $list['name']?>" class="filter-check" data-url="<?php echo $list['url']?>" <?php if (isset($list['active'])) { echo 'checked'; }?>> <?php echo $list['name']?></li>
                <?php endforeach ?>
            </ul>
            <?php endforeach?>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('.filter-check').on('click', function() {
        window.location = $(this).attr('data-url');
    })
})
</script>
<?php else: ?>
    <!-- No filters available -->
<?php endif?>
