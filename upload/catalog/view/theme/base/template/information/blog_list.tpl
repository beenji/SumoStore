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
                    <?php endforeach ?>
                </ol>

                <?php if (empty($blogs)) {
                    echo '<div class="alert alert-info">' . $no_results . '</div>';
                }
                else {
                    foreach ($blogs as $item): ?>
                    <div class="blog-item block">
                        <div class="header">
                            <h3><?php echo $item['title']?> <small><?php echo $item['author'] . ', ' . Sumo\Formatter::dateTime($item['publish_date'], false)?></small></h3>
                        </div>
                        <div class="content">
                            <?php echo strip_tags($item['intro_text'])?>
                            <br />
                            <?php if (!empty($item['text']) && strip_tags($item['intro_text']) != strip_tags($item['text'])): ?>
                            <a href="<?php echo $this->url->link('information/blog', 'blog_id=' . $item['blog_id'])?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_READ_MORE')?></a>
                            <?php endif ?>
                        </div>
                    </div>
                    <?php endforeach;
                }
                ?>
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
