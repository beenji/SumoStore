<?php if (count($sliders)): ?>
<ul class="home-slider">
    <?php foreach ($sliders as $slide): ?>
        <li data-thumb="<?php echo $this->model_tool_image->resize($slide['image'], $this->config->get('image_thumb_width'), $this->config->get('image_thumb_height'))?>">
            <a href="<?php echo $slide['href']?>"><img src="<?php echo $this->model_tool_image->resize($slide['image'], 800, 315, 'w')?>"></a>
        </li>
    <?php endforeach ?>
</ul>
<?php endif?>
