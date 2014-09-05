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
        <h1><?php echo $heading_title?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach ?>
        </ol>
        <div class="category-info">
            <?php if (!empty($thumb)): ?>
            <div class="image pull-left"><img src="<?php echo $thumb?>" alt="<?php echo $heading_title?>"></div>
            <?php endif ?>

            <?php if (!empty($description)): ?>
            <p><?php echo $description?></p>
            <?php endif ?>
        </div>
        <div class="clearfix"></div>


        <?php if (isset($categories) && $categories && $this->config->get('catalog_display_subcategories')): ?>
        <h4><?php echo Sumo\Language::getVar('SUMO_CATEGORY_REFINE') ?></h4>
        <div class="category-subcategories">
            <div class="col-sm-12">
                <?php foreach ($categories as $category): ?>
                <div class="col-sm-3 text-center">
                    <?php if (!empty($category['thumb'])): ?>
                    <img src="<?php echo $category['thumb']?>" alt="<?php echo $category['name']?>"><br />
                    <?php endif?>
                    <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
                </div>
                <?php endforeach ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php endif ?>

        <div class="category-container">
            <?php
            $data = array();
            $data['type'] = 'category';
            $data['filter_type'] = isset($type) ? $type : 'category';
            if (isset($this->request->get['path'])) {
                $data['path'] = $this->request->get['path'];
            }
            $data['data'] = $products_data;
            echo $this->getChild('app/widgetsimpleproduct', $data);
            ?>
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
