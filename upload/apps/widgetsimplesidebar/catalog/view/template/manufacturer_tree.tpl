<?php if (is_array($items) && count($items)): ?>
<div class="sidebar sidebar-category-tree">
    <div class="block">
        <div class="header">
            <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL')?></h3>
        </div>
        <div class="content">
            <ul class="menu-accordion">
                <li><a href="<?php echo $this->url->link('product/manufacturer')?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_MANUFACTURER_OVERVIEW')?></a></li>
                <?php
                if (is_array($items) && count($items)) {
                    foreach ($items as $list) {
                        // First level
                    echo '<li><a href="' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $list['manufacturer_id']) . '" ' . (isset($this->request->get['manufacturer_id']) && $list['manufacturer_id'] == $this->request->get['manufacturer_id'] ? 'class="active"' : '') . '>' . $list['name'] . '</a></li>';
                    }
                }
                else {
                    echo 'geen merken?';
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<?php endif?>
