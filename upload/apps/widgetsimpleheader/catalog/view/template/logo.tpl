<?php
echo '
<div class="col-md-4">
    <a href="' . $this->url->link('', '', 'SSL') . '">';
    $logo = $this->config->get('logo');
    if (!empty($logo)) {
        echo '<img src="image/' . $logo . '" alt="' . $this->config->get('title') . '">';
    }
    else {
        echo '<h1>' . $this->config->get('title') . '</h1>';
    }
        echo '
    </a>
</div>';


