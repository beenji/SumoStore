<?php echo $header; ?>

<div class="page-head-actions align-right" style="max-width: 300px;">
    <form action="">
        <div class="input-group" style="margin-bottom: 5px;">
            <span class="input-group-addon">
                <i class="fa fa-search"></i>
            </span>
            <input type="search" name="search" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_QUICK_SEARCH_PLURAL'); ?>" class="form-control" value="<?php if (isset($search)) { echo $search; }?>">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit"><?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCH_PLURAL'); ?></button>
            </span>
        </div>
        <p style="padding-top: 4px;"><a href="#search-advanced" data-toggle="collapse"><?php echo Sumo\Language::getVar('SUMO_NOUN_ADVANCED_SEARCH_PLURAL'); ?></a></p>
    </form>
</div>

<div class="clearfix"></div>

<div id="search-advanced"<?php if (!$advanced_search) { ?> class="collapse collapsed"<?php } else { ?> class="in"<?php } ?>>
    <form role="form" action="<?php echo $current_url; ?>" class="form-horizontal" method="get">
        <input type="hidden" name="token" value="<?php echo $token; ?>" />

        <div class="block-flat" style="padding-bottom: 5px; margin: 20px 0 10px;">
            <div class="row" style="margin-top: 0;">
                <div class="col-sm-6 col-lg-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label class="control-label col-sm-5" for="name"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_name" id="name" value="<?php echo $filter_name; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-5" for="email"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_email" id="email" value="<?php echo $filter_email; ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label class="control-label col-sm-5" for="ip"><?php echo Sumo\Language::getVar('SUMO_NOUN_IP'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_ip" id="ip" value="<?php echo $filter_ip; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stock-control" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP'); ?>:</label>
                        <div class="col-sm-7">
                            <select name="filter_customer_group" id="stock-control" class="form-control">
                                <option value="">&mdash;</option>
                                <?php foreach ($customer_groups as $cg) { ?>
                                <option value="<?php echo $cg['customer_group_id']; ?>"><?php echo $cg['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label for="filter_date_added" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_REGISTER_DATE'); ?>:</label>
                        <div class="col-sm-7">
                            <input type="text" name="filter_date_added" id="filter_date_added" class="form-control date-picker" value="<?php echo $filter_date_added; ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="filter_approved_1" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_APPROVED'); ?>:</label>
                        <div class="col-sm-7">
                            <label class="radio-inline">
                                <input type="radio" name="filter_approved" id="filter_approved" value="" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_ALL'); ?>
                            </label>
                            <label class="radio-inline">
                                <input<?php if ($filter_approved == '1') { echo ' checked="checked"'; } ?> type="radio" name="filter_approved" id="filter_approved_1" value="1" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                            </label>
                            <label class="radio-inline">
                                <input<?php if ($filter_approved == '0') { echo ' checked="checked"'; } ?> type="radio" name="filter_approved" id="filter_approved_0" value="0" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group" style="margin-top: 0;">
                        <label for="filter_newsletter_1" class="control-label col-sm-5"><?php echo Sumo\Language::getVar('SUMO_NOUN_NEWSLETTER'); ?>:</label>
                        <div class="col-sm-7">
                            <label class="radio-inline">
                                <input type="radio" name="filter_newsletter" id="filter_newsletter" value="" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_ALL'); ?>
                            </label>
                            <label class="radio-inline">
                                <input<?php if ($filter_newsletter == '1') { echo ' checked="checked"'; } ?> type="radio" name="filter_newsletter" id="filter_newsletter_1" value="1" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_YES'); ?>
                            </label>
                            <label<?php if ($filter_newsletter == '0') { echo ' checked="checked"'; } ?> class="radio-inline">
                                <input type="radio" name="filter_newsletter" id="filter_newsletter_0" value="0" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_NO'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="form-group" style="margin-top: 0;">
                        <label for="filter_status_1" class="control-label col-sm-4"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?>:</label>
                        <div class="col-sm-7">
                            <label class="radio-inline">
                                <input type="radio" name="filter_status" id="filter_status_1" value="" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_ALL'); ?>
                            </label>
                            <label class="radio-inline">
                                <input<?php if ($filter_status == '1') { echo ' checked="checked"'; } ?> type="radio" name="filter_status" id="filter_status_1" value="1" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_ACTIVE'); ?>
                            </label>
                            <label class="radio-inline">
                                <input<?php if ($filter_status == '0') { echo ' checked="checked"'; } ?> type="radio" name="filter_status" id="filter_status_0" value="0" />
                                <?php echo Sumo\Language::getVar('SUMO_NOUN_INACTIVE'); ?>
                            </label>
                        </div>
                    </div>
                </div>                
            </div>
        </div>

        <p class="align-right">
            <a class="btn btn-secondary" href="<?php echo $cancel; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
            <button class="btn btn-primary" type="submit"><?php echo Sumo\Language::getVar('SUMO_BUTTON_SHOW_RESULTS'); ?></button>
        </p>
    </form>
</div>

<form action="<?php echo $delete; ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($customers) { ?>
        <table class="table no-border list">
            <thead class="no-border items">
                <tr>
                    <th style="width: 45px;"><input type="checkbox" class="icheck toggleAll" /></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER_GROUP'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_STATUS'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_APPROVED'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_IP'); ?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_REGISTER_DATE'); ?></strong></th>
                    <th style="wdith: 180px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y items">
                <?php foreach ($customers as $list) { ?>
                <tr>
                    <td>
                        <input type="checkbox" name="selected[]" class="icheck" value="<?php echo $list['customer_id']; ?>">
                    </td>
                    <td><a href="<?php echo $list['edit']?>"><?php echo $list['name']?></a></td>
                    <td><a href="<?php echo $list['edit']?>"><?php echo $list['email']?></a></td>
                    <td><?php echo $list['customer_group']?></td>
                    <td>
                        <?php if ($list['status']) { echo Sumo\Language::getVar('SUMO_NOUN_ACTIVE'); } else { echo Sumo\Language::getVar('SUMO_NOUN_INACTIVE'); } ?></td>
                    <td>
                        <?php if ($list['approved']) { echo Sumo\Language::getVar('SUMO_NOUN_YES'); } else { echo Sumo\Language::getVar('SUMO_NOUN_NO'); } ?>
                    </td>
                    <td><?php echo $list['ip']?></td>
                    <td><?php echo $list['date_added']?></td>
                    <td class="right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">Actie <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a href="<?php echo $approve; ?>" rel="singleItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_APPROVE'); ?></a></li>
                                <li><a href="<?php echo $list['edit']; ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_EDIT'); ?></a></li>
                                <li><a href="<?php echo $delete; ?>" rel="singleItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONFIRM_DELETE_CUSTOMER'); ?>"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary mono4 dropdown-toggle" data-toggle="dropdown">Inloggen via... <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <?php foreach ($stores as $store) { ?>
                                <li><a target="_blank" href="<?php echo $list['login']; ?><?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
        <p><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_CUSTOMERS'); ?></p>
        <?php } ?>

        <hr>

        <div class="row">
            <?php if ($customers) { ?>
            <div class="col-md-4">
                <div class="table-padding">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $approve; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_APPROVE'); ?></a></li>
                            <li><a href="<?php echo $delete; ?>" rel="selectedItemTrigger"><?php echo Sumo\Language::getVar('SUMO_BUTTON_DELETE'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 align-center">
                <?php echo $pagination; ?>
            </div>
            <?php } ?>
            <div class="<?php if ($customers) { ?>col-md-4 <?php } else { ?>col-md-12<?php } ?> align-right">
                <div class="table-padding">
                    <a href="<?php echo $insert; ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_BUTTON_INSERT'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo $footer?>