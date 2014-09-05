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
            <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE')?></h1>

            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                <?php endforeach?>
            </ol>
            <div class="col-md-12">
                <?php if (!empty($success)): ?>
                <div class="alert alert-success"><p><?php echo $success?></p></div>
                <?php endif; if (!empty($warning)): ?>
                <div class="alert alert-warning"><p><?php echo $warning?></p></div>
                <?php endif?>
            </div>
        </div>
        <div class="row">
            <form method="post" class="form">
                <h2><?php echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_BOOK')?><a href="<?php echo $this->url->link('account/address/insert', '', 'SSL')?>" title="<?php echo Sumo\Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE_ADD')?>?"><span class="picons-plus"></span></a></h2>
                <table class="table table-list table-striped table-hover">
                    <tbody>
                        <?php if (count($addresses)): foreach ($addresses as $list): ?>
                        <tr>
                            <td>
                                <?php echo $list['address']?>
                            </td>
                            <td class="text-right">
                                <?php if ($list['default']) { echo Sumo\Language::getVar('SUMO_NOUN_ADDRESS_DEFAULT'); } ?>
                            </td>
                            <td class="text-right">
                                <a href="<?php echo $list['update']?>"><span class="picons-write"></span></a>
                                <?php if (!$list['default']): ?>
                                <a href="<?php echo $list['delete']?>"><span class="picons-unable"></span></a>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php endforeach; endif?>
                    </tbody>
                </table>
            </form>
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
