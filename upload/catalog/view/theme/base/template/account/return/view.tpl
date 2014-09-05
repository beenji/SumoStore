<?php echo $header; ?>


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
        <h1><?php echo Sumo\Language::getVar('SUMO_ACCOUNT_RETURN_DETAILS')?></h1>

        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
            <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
            <?php endforeach?>
        </ol>
        
        <?php if ($return_id) { ?>
        <div class="row">
            <div class="col-md-5">
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_ID_ABBR'); ?>:</dt>
                    <dd><?php echo $return_id; ?></dd>
                </dl>

                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_NO'); ?>:</dt>
                    <dd><a href="<?php echo $order; ?>"><?php echo $order_id; ?></a></dd>
                </dl>

                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</dt>
                    <dd><?php echo $status; ?></dd>
                </dl>
            </div>
            <div class="col-md-7">
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_DATE'); ?>:</dt>
                    <dd><?php echo $date_ordered; ?></dd>
                </dl>

                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_RETURN_DATE'); ?>:</dt>
                    <dd><?php echo $date_added; ?></dd>
                </dl>
            </div>
        </div>

        <table class="table" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th style="width: 65px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY'); ?></th>
                    <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></th>
                    <th style="width: 75px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL'); ?></th>
                    <th class="text-center" style="width: 100px;"><?php echo ucfirst(Sumo\Language::getVar('SUMO_NOUN_OPENED')); ?></th>
                    <th style="width: 250px;"><?php echo Sumo\Language::getVar('SUMO_NOUN_REASON'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $quantity; ?></td>
                    <td><?php echo $product; ?></td>
                    <td><?php echo $model; ?></td>
                    <td class="text-center"><?php echo $opened; ?></td>
                    <td><?php echo $reason; ?></td>
                </tr>
            </tbody>
        </table>

        <hr>
        
        <div class="row">
            <div class="col-md-5">
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</dt>
                    <dd><?php echo $firstname . ' ' . $lastname; ?></dd>
                </dl>
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>:</dt>
                    <dd><?php if ($email) { echo $email; } else { echo '&mdash;'; } ?></dd>
                </dl>
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_PHONE'); ?>:</dt>
                    <dd><?php if ($telephone) { echo $telephone; } else { echo '&mdash;'; } ?></dd>
                </dl>        
            </div>
            <div class="col-md-7">
                <?php if ($comment) { ?>
                <dl class="info">
                    <dt><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENT'); ?>:</dt>
                    <dd><?php echo $comment; ?></dd>
                </dl>
                <?php } ?>
            </div>
        </div>
        
        
        <?php if ($histories) { ?>        
        <hr>

        <h4><?php echo Sumo\Language::getVar('SUMO_NOUN_HISTORY'); ?></h4>

        <ul class="timeline">
            <?php foreach ($histories as $history) { ?>
            <li>
                <dl>
                    <dt><strong><?php echo $history['date_added']; ?></strong> <?php if ($history['status']) { echo ' &mdash; ' . $history['status']; } ?></dt>
                    <dd><?php echo $history['comment']; ?></dd>
                </dl>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>

        <?php } else { ?>
        <div class="alert alert-danger">
            <p><?php echo Sumo\Language::getVar('SUMO_ERROR_NO_RETURN'); ?></p>
        </div>
        <?php } ?>
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

<?php echo $footer; ?>