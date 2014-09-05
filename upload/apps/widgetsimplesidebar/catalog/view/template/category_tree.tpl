<div class="sidebar sidebar-category-tree">
    <div class="block">
        <div class="header">
            <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_CATEGORY_PLURAL')?></h3>
        </div>
        <div class="content">
            <ul class="menu-accordion">
                <?php
                if (is_array($categories) && count($categories)) {
                    foreach ($categories as $list) {
                        // First level
                    echo '<li><a href="' . $list['href'] . '" ' . ($list['category_id'] == $input['data']['filter_category_id'] ? 'class="active"' : '') . '>' . $list['name'] . '</a>';
                        if (isset($list['children'])) {
                            // Second level
                            echo '<ul>';
                            foreach ($list['children'] as $child) {
                                echo '<li><a href="' . $child['href'] . '" ' . ($child['category_id'] == $input['data']['filter_category_id'] ? 'class="active"' : '') . '>' . $child['name'] . '</a>';
                                if (isset($child['children'])) {
                                    // Third level
                                    echo '<ul>';
                                    foreach ($child['children'] as $kiddo) {
                                        echo '<li><a href="' . $kiddo['href'] . '" ' . ($kiddo['category_id'] == $input['data']['filter_category_id'] ? 'class="active"' : '') . '>' . $kiddo['name'] . '</a>';
                                        if (isset($kiddo['children'])) {
                                            // Fourth level
                                            echo '<ul>';
                                            foreach ($kiddo['children'] as $kid) {
                                                echo '<li><a href="' . $kid['href'] . '" ' . ($kid['category_id'] == $input['data']['filter_category_id'] ? 'class="active"' : '') . '>' . $kid['name'] . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                }
                                echo '</li>';
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
