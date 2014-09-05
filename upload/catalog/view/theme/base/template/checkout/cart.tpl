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
                <h1><?php echo Sumo\Language::getVar('SUMO_CART_TITLE')?></h1>
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $crumb): ?>
                    <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                    <?php endforeach ?>
                </ol>
                <?php if ($attention) { ?>
                <div class="alert alert-info"><?php echo $attention; ?></div>
                <?php }
                if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php }
                if ($error_warning) { ?>
                <div class="alert alert-warning"><?php echo $error_warning; ?></div>
                <?php }
                if (isset($not_in_stock_stop)): ?>
                <div class="alert alert-danger"><?php echo Sumo\Language::getVar('SUMO_CART_NOT_IN_STOCK_STOP')?></div>
                <?php endif;
                $items = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
                if ($items && isset($products)): $total = 0; ?>
                <form method="post" id="cart-form">
                    <table class="table table-striped">
                        <thead class="no-border">
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_SINGULAR') ?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_MODEL') ?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_SINGULAR') ?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY_SINGULAR') ?></th>
                                <th><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_SINGULAR') ?></th>
                            </tr>
                        </thead>
                        <tbody class="no-border">
                            <?php foreach ($products as $item): ?>
                            <tr>
                                <td><?php if (!empty($item['thumb'])): ?><img src="<?php echo $item['thumb']?>"><?php endif; ?></td>
                                <td>
                                    <?php if (!empty($item['href'])): ?><a href="<?php echo $item['href']?>"><?php endif?>
                                    <?php echo $item['name']; if (!$item['stock']): echo '***'; endif;
                                    echo '<br />';
                                    if (!empty($item['href'])): ?></a><?php endif;
                                    if (!empty($item['option'])): foreach ($item['option'] as $option): ?>
                                    <small>- <?php echo $option['name'] . ': ' . $option['value']?></small><br />
                                    <?php endforeach; endif; ?>
                                </td>
                                <td><?php echo isset($item['model']) ? $item['model'] : ''?></td>
                                <td><?php echo isset($item['price']) ? Sumo\Formatter::currency($item['price']) : ''?></td>
                                <td>
                                    <input type="text" name="quantity[<?php echo $item['key']; ?>]" value="<?php echo $item['quantity']; ?>" size="1" />
                                    <a href="#update" onclick="$('#cart-form').submit(); return false;"><img src="catalog/view/theme/<?php echo $this->config->get('template')?>/image/update.png" alt="update" title="update" /></a>
                                    <a href="<?php echo $item['remove']; ?>">
                                        <img src="catalog/view/theme/<?php echo $this->config->get('template')?>/image/remove.png" alt="<?php echo Sumo\Language::getVar('BUTTON_REMOVE') ?>" title="<?php echo Sumo\Language::getVar('BUTTON_REMOVE') ?>" />
                                    </a>
                                </td>
                                <td><?php $tmpTotal = isset($item['total']) ? Sumo\Formatter::currency($item['total']) : ''; $total += $item['total']; echo $tmpTotal;?></td>
                            </tr>
                            <?php endforeach ?>
                            <tr>
                                <td colspan="4">&nbsp;</td>
                                <td><?php echo Sumo\Language::getVar('SUMO_NOUN_OT_SUBTOTAL')?>:</td>
                                <td><?php echo Sumo\Formatter::currency($total)?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <div class="pull-right"><a href="<?php echo $this->url->link('checkout/checkout', '', 'SSL') ?>" class="btn btn-order btn-ignore"><?php echo Sumo\Language::getVar('SUMO_NOUN_CHECKOUT') ?></a></div>
                        <div class="pull-left"><a href="<?php echo $this->url->link('') ?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONTINUE_SHOPPING') ?></a></div>
                    </div>
                <div class="clearfix"></div>
                </form>
                <?php
                else:
                    echo Sumo\Language::getVar('SUMO_CHECKOUT_CART_ITEMS_NONE');
                endif;
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
