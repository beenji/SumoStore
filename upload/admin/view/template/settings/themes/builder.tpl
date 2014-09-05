<?php echo $header ?>
<div class="col-md-4 col-md-offset-8 page-head-actions align-right settingstable">
    <div class="btn-group align-left">
        <?php
        foreach ($stores as $list):
            if ($list['store_id'] == $current_store):
        ?>
        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" type="button"><span><?php echo $list['name']; ?></span>&nbsp; <span class="caret"></span></button>
        <?php
                break;
            endif;
        endforeach; ?>
        <ul class="dropdown-menu pull-right">
            <?php foreach ($stores as $list): ?>
            <li><a href="<?php echo $this->url->link('settings/themes/builder', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'] . '&theme=' . $theme . '&action=' . $action, 'SSL')?>"><?php echo $list['name']?></a></li>
            <?php endforeach?>
        </ul>
    </div>
</div>
<ul class="nav nav-tabs">
    <li class="<?php if ($action == 'colors'){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('settings/themes/builder', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&theme=' . $theme . '&action=colors', 'SSL')?>">
            <?php echo Sumo\Language::getVar('SUMO_ADMIN_THEMES_TAB_COLORS')?>
        </a>
    </li>
    <li class="<?php if ($action == 'css'){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('settings/themes/builder', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&theme=' . $theme . '&action=css', 'SSL')?>">
            <?php echo Sumo\Language::getVar('SUMO_ADMIN_THEMES_TAB_CSS')?>
        </a>
    </li>
    <li class="<?php if ($action == 'header'){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('settings/themes/builder', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&theme=' . $theme . '&action=header', 'SSL')?>">
            <?php echo Sumo\Language::getVar('SUMO_ADMIN_THEMES_TAB_HEADER')?>
        </a>
    </li>
    <li class="<?php if ($action == 'footer'){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('settings/themes/builder', 'token=' . $this->session->data['token'] . '&store_id=' . $current_store . '&theme=' . $theme . '&action=footer', 'SSL')?>">
            <?php echo Sumo\Language::getVar('SUMO_ADMIN_THEMES_TAB_FOOTER')?>
        </a>
    </li>
</ul>

<?php echo $content?>

<?php echo $footer?>
