<?php echo $header; ?>

<form action="<?php echo $this->url->link('sale/orders/remove', 'token=' . $this->session->data['token'], 'SSL')?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if (!empty($orders)) { ?>
        <table class="table no-border list">
            <thead class="no-border items">
                <tr>
                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                    <th><strong>#</strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_NAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDER_DATE'); ?></strong></th>
                    <th style="wdith: 100px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($orders as $list) { ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" class="icheck" value="<?php echo $list['order_id']; ?>"></td>
                    <td><?php echo str_pad($list['order_id'], 6, 0, STR_PAD_LEFT);?></td>
                    <td><?php echo $list['customer']['lastname'] . ', ' . $list['customer']['firstname']?></td>
                    <td><?php echo $list['status']?></td>
                    <td><?php echo Sumo\Formatter::currency($list['total'])?></td>
                    <td><?php echo Sumo\Formatter::dateTime(strtotime($list['order_date']), false)?></td>
                    <td class="right">
                        <div class="btn-group">
                            <a href="<?php echo $this->url->link('sale/orders/info', 'token=' . $this->session->data['token'] . '&order_id=' . $list['order_id'], 'SSL') ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INFO'); ?></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_MORE'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="<?php echo $this->url->link('sale/orders/edit', 'token=' . $this->session->data['token'] . '&order_id=' . $list['order_id'], 'SSL') ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a></li>
                                    <li><a href="<?php echo $this->url->link('sale/orders/remove', 'token=' . $this->session->data['token'] . '&order_id=' . $list['order_id'], 'SSL') ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_ORDER'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <hr>
        <?php } else { ?>
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NO_RESULTS'); ?></p>
        <?php } ?>

        <div class="row">
            <div class="col-md-4">
                <?php if (!empty($orders)) { ?>
                <div class="table-padding">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $this->url->link('sale/orders/invoice', 'token=' . $this->session->data['token'], 'SSL')?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PRINT_INVOICE'); ?></a></li>
                            <li><a href="<?php echo $this->url->link('sale/orders/remove', 'token=' . $this->session->data['token'], 'SSL')?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                        </ul>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="col-md-4 align-center">
                <?php echo isset($pagination) ? $pagination : '' ?>
            </div>
            <div class="col-md-4 align-right">
                <a href="<?php echo $this->url->link('sale/orders/edit') ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
            </div>
        </div>
    </div>
</form>

<?php echo $footer?>
