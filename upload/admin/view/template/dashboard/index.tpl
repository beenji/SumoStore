<?php echo $header;
$this->load->model('settings/menu');
$items = $this->model_settings_menu->getChildItems($this->model_settings_menu->getParentId());
?>

<div class="row">
<?php
$count = 0;
foreach ($items as $list) {
?>
    <div class="col-md-6">
        <div class="fd-tile detail detail-primary">
            <div class="content">
                <h1 class="text-left"><a href="<?php echo $list['url']?>"><?php echo Sumo\Language::getVar($list['name']); ?></a></h1>
                <?php if (!empty($list['description'])) { ?>
                <p><?php echo Sumo\Language::getVar($list['description']); ?></p>
                <?php } ?>
            </div>
            <div class="icon">
                <?php if (!empty($list['icon'])) { ?>
                <i class="<?php echo $list['icon']; ?>"></i>
                <?php } ?>
            </div>
            <a class="details details-interactive" href="<?php echo $list['url']; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?><span><i class="fa fa-arrow-circle-right pull-right"></i></span></a>
        </div>
    </div>
        <?php
        // This makes sure the rows are EVEN and the blocks are on the same row, no matter the size of the description
        if ($count % 2) {
            echo '</div><div class="row">';
        }
        $count++;
    }
    ?>
</div>

<?php echo $footer ?>
