<?php echo $header?>
<div class="container">
    <h1><?php echo $heading_title; ?></h1>
    <ol class="breadcrumb">
        <?php foreach ($breadcrumbs as $crumb): ?>
        <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
        <?php endforeach ?>
    </ol>
    <div class="col-md-12">
        <p><?php echo $text_message; ?></p>
    </div>
    <div class="col-md-12">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('BUTTON_CONTINUE'); ?></a></div>
    </div>
</div>
<?php echo $footer?>
