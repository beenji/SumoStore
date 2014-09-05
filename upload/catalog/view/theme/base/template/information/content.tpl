<?php echo $header?>
<div class="container">

    <?php if (!empty($settings['left']) && count($settings['left'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['left'] as $key => $item) {
            if (!$item || $item == null) {
                unset($settings['left'][$key]);
                continue;
            }
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    $mainClass = 'col-md-12';
    if (!empty($settings['left']) && !empty($settings['right'])) {
        $mainClass = 'col-md-6';
    }
    else if (!empty($settings['left']) || !empty($settings['right'])) {
        $mainClass = 'col-md-9';
    }
    ?>

    <div class="<?php echo $mainClass?>">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo $heading_title?></h1>

                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $crumb): ?>
                    <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                    <?php endforeach;
                    if (isset($blog_info)):
                        if (!empty($blog_info['author'])): ?><li><?php echo $blog_info['author']?></li><?php endif;
                        if (!empty($blog_info['publish_date'])): ?><li><?php echo Sumo\Formatter::dateTime($blog_info['publish_date'], false)?></li><?php endif;
                    endif?>
                </ol>
                <div class="information-content content-<?php echo $type?>">
                    <?php if (!empty($description)): ?>
                    <p><?php echo $description?></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($settings['right'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['right'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    if (isset($settings['bottom'])): ?>
    <div class="col-md-12">
        <?php
        foreach ($settings['bottom'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <div class="clearfix"></div>
    <?php endif ?>
</div>
<?php echo $footer ?>
