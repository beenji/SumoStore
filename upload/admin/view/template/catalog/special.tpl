<?php echo $header; ?>

<script type="text/javascript">
    formError = '<?php echo $error; ?>';
    sessionToken = '<?php echo $token; ?>';
</script>

<div class="row">
    <div class="col-md-8">
        <form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
            <div class="block-flat">
                <?php if ($specials) { ?>
                <table class="table no-border list">
                    <thead class="no-border items">
                        <tr>
                            <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                            <th><strong>#</strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_PRICE'); ?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TIMESPAN'); ?></strong></th>
                            <th style="wdith: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y items">
                        <?php foreach ($specials as $special) { ?>
                        <tr>
                            <td><input type="checkbox" name="selected[]" value="<?php echo $special['product_special_id']; ?>" class="icheck"></td>
                            <td valign="top"><?php echo $special['product_special_no']; ?></td>
                            <td><?php echo $special['name']; ?><br /><small><?php echo !empty($special['model']) ? $special['model'] : 'P' . $special['product_id']?></small></td>
                            <td><s><?php echo $special['product_price']; ?></s><br /><?php echo $special['price']; ?></td>
                            <td valign="top"><?php echo $special['date_start']; ?> - <?php echo $special['date_end']; ?></td>
                            <td class="right">
                                <div class="btn-group">
                                    <a href="<?php echo $special['edit']; ?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a>
                                    <a href="<?php echo $delete; ?>" rel="singleItemTrigger" class="btn btn-sm btn-primary" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_ORDER'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <hr>

                <div class="row">
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
                    <div class="col-md-4 align-right">
                        <?php echo $pagination; ?>
                    </div>
                </div>
                <?php } else { ?>
                <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_SPECIALS'); ?></p>
                <?php } ?>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <form action="<?php echo $action; ?>" method="post">
            <div class="block-flat">
                <div class="form-group">
                    <label for="product" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_PRODUCT'); ?>:</label>
                    <input type="text" name="product" id="product" value="<?php echo $product; ?>" class="form-control">
                    <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>" />
                </div>

                <div class="form-group">
                    <label for="discount" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT'); ?>:</label>
                    <input type="text" name="discount" id="discount" class="form-control" value="<?php echo $discount . $discount_suffix; ?>">
                    <small><?php echo Sumo\Language::getVar('SUMO_NOUN_DISCOUNT_EXPLANATION'); ?></small>
                </div>

                <div class="form-group">
                    <label for="date_start" class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_VALIDITY'); ?>:</label>
                    <div class="control-group row" style="margin-top: 0;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_FROM'); ?></small></span>
                                <input type="text" name="date_start" id="date_start" value="<?php echo $date_start; ?>" class="form-control date-picker" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><small><?php echo Sumo\Language::getVar('SUMO_NOUN_TO'); ?></small></span>
                                <input type="text" name="date_end" class="form-control date-picker" value="<?php echo $date_end; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_BUTTON_SAVE_DISCOUNT'); ?>" />
            <a class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
        </form>
    </div>
</div>

<?php echo $footer; ?>
