                        <?php if (!isset($this->request->get['type'])): ?>
                        <div class="col-md-4">
                            <div id="cart">
                                <?php endif ?>
                                <div class="header">
                                    <h5 class="text-right"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPPING_CART')?></h5>
                                    <a href="<?php echo $this->url->link('checkout/cart') ?>"><i class="picons-cart pull-right"></i></a>
                                    <div id="cart-total" class="text-right">
                                        <?php
                                        $items = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
                                        if ($items) {
                                            if ($items > 1) {
                                                echo Sumo\Language::getVar('SUMO_CHECKOUT_CART_ITEMS_PLURAL', $items);
                                            }
                                            else {
                                                echo Sumo\Language::getVar('SUMO_CHECKOUT_CART_ITEMS_SINGULAR');
                                            }
                                        }
                                        else {
                                            echo Sumo\Language::getVar('SUMO_CHECKOUT_CART_ITEMS_NONE');
                                        }
                                        echo ' - ' . Sumo\Formatter::currency($this->cart->getTotal());
                                        ?>
                                    </div>
                                </div>
                                <div class="content">
                                    <?php if ($items && isset($products)): ?>
                                    <table class="table table-striped table-cart">
                                        <thead class="no-border">
                                            <tr>
                                                <td class=""></td>
                                                <td class=""><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT_SINGULAR') ?></td>
                                                <td class=""><?php echo Sumo\Language::getVar('SUMO_NOUN_QUANTITY_SINGULAR') ?></td>
                                                <td class=""><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE_SINGULAR') ?></td>
                                                <td class=""><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_SINGULAR') ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $list): ?>
                                            <tr product="<?php echo $list['product_id']?>">
                                                <td><?php if (isset($list['thumb'])) { echo '<img src="' . $list['thumb'] . '">'; } ?></td>
                                                <td>
                                                    <?php if (!empty($list['href'])): ?><a href="<?php echo $list['href']?>"><?php endif?>
                                                    <?php echo $list['name']?>
                                                    <?php if (!empty($list['href'])): ?></a><?php endif?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($this->config->get('tax_enabled')) {
                                                        $price = round($list['price'] + $list['price'] / 100 * $list['tax'], 2);
                                                    }
                                                    else {
                                                        $price = $list['price'];
                                                    }
                                                    echo Sumo\Formatter::currency($price)?>
                                                </td>
                                                <td><?php echo $list['quantity']?></td>
                                                <td>
                                                    <?php
                                                    echo Sumo\Formatter::currency($price * $list['quantity']);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                    <a href="<?php echo $this->url->link('checkout/cart')?>" class="btn btn-primary"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_CART_VIEW')?></a>
                                    <a href="<?php echo $this->url->link('checkout/checkout', '', 'SSL')?>" class="pull-right btn btn-order"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_TITLE')?></a>
                                    <?php else:
                                        echo '<span class="text-center">' . Sumo\Language::getVar('SUMO_CHECKOUT_CART_EMPTY', strtolower(Sumo\Language::getVar('SUMO_NOUN_SHOPPING_CART'))) . '</span>';
                                    endif;?>
                                </div>
                                <?php if (!isset($this->request->get['type'])): ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <?php endif ?>
