<div class="row">
    <?php
    if (!empty($settings['blocks']['amount'])) {
        switch ($settings['blocks']['amount']) {
            case 0: case 1:
                $class = 'col-md-12 col-xs-12';
                break;

            case 2:
                $class = 'col-md-6 col-xs-6';
                break;

            case 3:
                $class = 'col-md-4 col-xs-6';
                break;

            case 4:
                $class = 'col-md-3 col-xs-6';
                break;
        }

        for ($i = 1; $i <= $settings['blocks']['amount']; $i++) {
            $list = $settings['blocks']['blocks'][$i];
            echo '<div class="' . $class . '">';
                echo '<h3>' . $list['title'][$this->config->get('language_id')] . '</h3>';
                if ($list['type'] == 'content') {
                    echo '<div class="footer-content">' . html_entity_decode($list['content'][$this->config->get('language_id')]) . '</div>';
                }
                else {
                    echo '<ul class="footer-links">';
                    foreach ($list['links'] as $nr => $data) {
                        $tmp = explode('/', ltrim($data['url'], '/'));
                        if ($tmp[0] == 'information' && $tmp[1] == 'information') {
                            $tmp2 = explode('=', $data['url']);
                            $data['url'] = $this->url->link('information/information', 'information_id=' . $tmp2[1]);
                        }
                        echo '<li><a href="' . $data['url'] . '">' . $data['name'][$this->config->get('language_id')] . '</a></li>';
                    }
                    echo '</ul>';
                }
            echo '</div>';
        }
    }
    ?>
</div>
<div class="row" id="bottom-footer">
    <div class="col-md-12">
        <p class="pull-left">
            <?php
            if (!empty($settings['copyright']['notice'])) {
                echo str_replace(array('[websitename]', '[currentyear]', '<p>', '</p>'), array($this->config->get('title'), date('Y'), '', ''), html_entity_decode($settings['copyright']['notice'][$this->config->get('language_id')]));
            }
            else {
                echo '&copy; ' . date('Y') . '  ' . $this->config->get('title');
            }
            ?>
        </p>
        <?php
        if (isset($settings['copyright']['powered_by']) && $settings['copyright']['powered_by']) {
            echo '<p class="pull-right text-right"><a href="http://www.sumostore.net/" target="_blank" rel="software">Powered by SumoStore</a></p>';
        }
        ?>
    </div>
</div>
