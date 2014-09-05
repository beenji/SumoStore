<?php if (isset($banners) && count($banners)):
foreach ($banners as $banner): ?>
<div class="sidebar sidebar-banner">
    <?php if (!empty($banner['href'])): ?>
    <a href="<?php echo $banner['href']?>" <?php if (!empty($banner['target'])): ?>target="<?php echo $banner['target']?>"<?php endif?>>
    <?php endif ?>
        <?php if (!empty($banner['image'])): ?>
        <img src="image/<?php echo $banner['image']?>" alt="<?php if (!empty($banner['title'][$this->config->get('language_id')])) { echo $banner['title'][$this->config->get('language_id')]; }?>" title="<?php if (!empty($banner['title'][$this->config->get('language_id')])) { echo $banner['title'][$this->config->get('language_id')]; }?>">
        <?php elseif (!empty($banner['title'])):
            echo $banner['title'];
        endif;?>
    <?php if (!empty($banner['href'])): ?>
    </a>
    <?php endif ?>
</div>
<?php
endforeach; endif ?>
