<?php echo $header; ?>

<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?>
    <a href="<?php echo $breadcrumb['href']; ?>">
        <?php echo $breadcrumb['text']; ?>
    </a>
    <?php } ?>
</div>
<div id="pad-wrapper">
    <div class="row">
        <div class="col-md-4">
            <h4><?php echo $heading_title; ?></h4>
        </div> 
    </div>
    <div class="row">
        <div class="alert alert-danger">
            <i class="icon-warning-sign"></i> <?php echo $text_permission?>
        </div>
    </div>
</div>
<?php echo $footer; ?>