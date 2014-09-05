<?php echo $header; ?>

<?php if ($invoices) { ?>
<div class="fd-tile detail tile-purple align-right">
    <div class="content">
	   <big><?php echo Sumo\Language::getVar('SUMO_NOUN_PAGE_TOTAL'); ?>: <?php echo $page_total_ex; ?> <?php echo Sumo\Language::getVar('SUMO_NOUN_EX_VAT'); ?> / <?php echo $page_total_in; ?> <?php echo Sumo\Language::getVar('SUMO_NOUN_IN_VAT'); ?></big>
    </div>
    <div class="icon">
       <i class="fa fa-euro"></i>
   </div>
</div>
<?php } ?>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($invoices) { ?>
        <div class="align-right">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOW'); ?>: <?php echo $status; ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu align-left pull-right" role="menu">
                    <li><a href="<?php echo $overview; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DEFAULT'); ?></a></li>
                    <li><a href="<?php echo $filter; ?>concept"><?php echo Sumo\Language::getVar('SUMO_NOUN_CONCEPT'); ?></a></li>
                    <li><a href="<?php echo $filter; ?>sent"><?php echo Sumo\Language::getVar('SUMO_NOUN_SENT'); ?></a></li>
                    <li><a href="<?php echo $filter; ?>partially_paid"><?php echo Sumo\Language::getVar('SUMO_NOUN_PARTIALLY_PAID'); ?></a></li>
                    <li><a href="<?php echo $filter; ?>paid"><?php echo Sumo\Language::getVar('SUMO_NOUN_PAID'); ?></a></li>
                    <li><a href="<?php echo $filter; ?>credit"><?php echo Sumo\Language::getVar('SUMO_NOUN_CREDIT'); ?></a></li>
                    <li><a href="<?php echo $filter; ?>expired"><?php echo Sumo\Language::getVar('SUMO_NOUN_EXPIRED'); ?></a></li>
                </ul>
            </div>
        </div>

        <table class="table no-border list">
            <thead class="no-border items">
                <tr>
                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll"></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_NO'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_AMOUNT_IN'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_INVOICE_DATE'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></strong></th>
                    <th style="wdith: 100px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($invoices as $invoice) { ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?php echo $invoice['invoice_id']; ?>" class="icheck"></td>
                    <td><?php echo $invoice['invoice_no']?></td>
                    <td><?php echo $invoice['customer']?></td>
                    <td><?php echo $invoice['amount']?></td>
                    <td><?php echo $invoice['date']?></td>
                    <td><?php echo Sumo\Language::getVar('SUMO_NOUN_' . $invoice['status']); ?></td>
                    <td class="right">
                        <div class="btn-group">
                            <a href="<?php echo $invoice['view']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_VIEW'); ?></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_MORE'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="<?php echo $invoice['update']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a></li>
                                    <li><a href="<?php echo $invoice['download']; ?>" target="_blank"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PRINT_INVOICE'); ?></a></li>
                                    <li><a href="<?php echo $invoice['send']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SEND'); ?></a></li>
                                    <li><a href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_INVOICE'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
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
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_INVOICES'); ?></p>
        <?php } ?>

        <div class="row">
            <?php if ($invoices) { ?>
            <div class="col-md-4">
                <div class="table-padding">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $delete; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 align-center">
                <?php echo $pagination; ?>
            </div>
            <div class="col-md-4 align-right">
            <?php } else { ?>
            <div class="col-md-12 align-right">
            <?php } ?>
                <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
            </div>
        </div>
    </div>
</form>

<?php echo $footer; ?>