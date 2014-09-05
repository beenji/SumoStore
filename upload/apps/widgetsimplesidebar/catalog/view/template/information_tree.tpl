<div class="sidebar sidebar-category-tree">
    <div class="block">
        <div class="header">
            <h3><?php echo $title?></h3>
        </div>
        <div class="content">
            <ul class="menu-accordion">
                <?php
                if (is_array($items) && count($items)) {
                    foreach ($items as $list) {
                        // First level
                    echo '<li><a href="' . $this->url->link($url, $type . '=' . $list[$type]) . '" ' . ($list[$type] == $item_id ? 'class="active"' : '') . '>' . $list['title'] . '</a>';
                        if (isset($list['children'])) {
                            // Second level
                            echo '<ul>';
                            foreach ($list['children'] as $child) {
                                echo '<li><a href="' . $this->url->link($url, $type . '=' . $child[$type]) . '" ' . ($child[$type] == $item_id ? 'class="active"' : '') . '>' . $child['title'] . '</a></li>';
                            }
                            echo '</ul>';
                        }
                    echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>
