<?php echo $header?>
<div class="container">
    <h1><?php echo $title?></h1>
    <?php if (!empty($content)): ?>
    <div class="alert alert-warning"><?php echo $content?> <?php if (isset($continue)): ?><a href="<?php echo $continue?>" class="alert-link"><?php echo Sumo\Language::getVar('BUTTON_CONTINUE')?></a><?php endif?></div>
    <?php endif?>

    <?php if (defined('DEVELOPMENT') && !isset($debug) || $debug): ?>
    <h3>Debug:</h3>
    <pre><?php echo print_r(Sumo\Logger::get('total'), true)?></pre>
    <?php endif?>
</div>
<?php echo $footer?>
