<?php echo $header; ?>

<script type="text/javascript">
    /**
    * Dnyamically generated JavaScript variables,
    * do all fancy jQuery stuff in page footer
    */
    var customersPerCountry = <?php echo json_encode($countries); ?>,
        orders = <?php echo json_encode($order_stats); ?>,
        ordersTickers = <?php echo json_encode($order_stats_labels); ?>,
        ordersLabel = '<?php echo Sumo\Language::getVar('SUMO_NOUN_ORDERS'); ?>',
        returns = <?php echo json_encode($return_stats); ?>,
        returnsLabel = '<?php echo Sumo\Language::getVar('SUMO_NOUN_RETOURS'); ?>',
        customers = <?php echo json_encode($customer_stats); ?>,
        customersLabel = '<?php echo Sumo\Language::getVar('SUMO_NOUN_NEW_CUSTOMERS'); ?>',
        customersTickers = <?php echo json_encode($customer_stats_labels); ?>;
</script>

<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="fd-tile detail tile-green">
            <div class="content">
                <h1 class="text-left"><?php echo $total_sale; ?></h1>
                <p><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_SALE'); ?></p>
            </div>
            <div class="icon"><i class="fa fa-eur"></i></div>
            <a class="details" href="<?php echo $uri_orders; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?> <span><i class="fa fa-arrow-circle-right pull-right"></i></span></a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="fd-tile detail tile-orange">
            <div class="content">
                <h1 class="text-left"><?php echo $total_sale_year; ?></h1>
                <p><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_SALE_YEAR'); ?></p>
            </div>
            <div class="icon"><i class="fa fa-eur"></i></div>
            <a class="details" href="<?php echo $uri_orders; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?> <span><i class="fa fa-arrow-circle-right pull-right"></i></span></a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="fd-tile detail tile-prusia">
            <div class="content">
                <h1 class="text-left"><?php echo $total_order; ?></h1>
                <p><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_ORDERS'); ?></p>
            </div>
            <div class="icon"><i class="fa fa-shopping-cart"></i></div>
            <a class="details" href="<?php echo $uri_orders; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?> <span><i class="fa fa-arrow-circle-right pull-right"></i></span></a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="fd-tile detail tile-blue">
            <div class="content">
                <h1 class="text-left"><?php echo $total_customer; ?></h1>
                <p><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL_CUSTOMERS'); ?></p>
            </div>
            <div class="icon"><i class="fa fa-users"></i></div>
            <a class="details" href="<?php echo $uri_customers; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?> <span><i class="fa fa-arrow-circle-right pull-right"></i></span></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="block-flat">
            <?php if (isset($orders) && !empty($orders)) { ?>
            <div class="header">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOW'); ?>: <?php echo $orders_stats; ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $orders_day; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_DAY'); ?></a></li>
                        <li><a href="<?php echo $orders_week; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_WEEK'); ?></a></li>
                        <li><a href="<?php echo $orders_month; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_MONTH'); ?></a></li>
                        <li><a href="<?php echo $orders_year; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_YEAR'); ?></a></li>
                    </ul>
                </div>
                <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDERS'); ?></h3>
            </div>
            <div class="content">
                <div id="sales_chart_legend" class="legend-container"></div>
                <div id="sales_chart" style="height: 180px;"></div>
            </div>
            <div class="content">
                <table class="no-border hover">
                    <thead class="no-border">
                        <tr>
                            <th style="width: 60px;"><strong>#</strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER'); ?></strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_TOTAL'); ?></strong></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y">
                        <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td><?php echo str_pad($order['order_id'], 4, 0, STR_PAD_LEFT); ?></td>
                            <td><?php echo $order['customer']; ?></td>
                            <td><?php echo $order['total']; ?></td>
                            <td class="right"><i class="fa fa-angle-right"></i><a href="<?php echo $order['info']; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
            <div class="header">
                <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_ORDERS'); ?></h3>
            </div>
            <div class="content">
                <p><?php echo Sumo\Language::getVar('SUMO_NO_RESULTS'); ?></p>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="block-flat">
            <?php if (isset($visitors) && !empty($visitors)) { ?>
            <div class="header">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_SHOW'); ?>: <?php echo $customers_stats; ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $customers_day; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_DAY'); ?></a></li>
                        <li><a href="<?php echo $customers_week; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_WEEK'); ?></a></li>
                        <li><a href="<?php echo $customers_month; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_MONTH'); ?></a></li>
                        <li><a href="<?php echo $customers_year; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_STATS_YEAR'); ?></a></li>
                    </ul>
                </div>
                <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_VISITORS'); ?></h3>
            </div>
            <div class="content">
                <div id="visitors_chart_legend" class="legend-container"></div>
                <div id="visitors_chart" style="height: 180px;"></div>
            </div>
            <div class="content">
                <table class="no-border hover">
                    <thead class="no-border">
                        <tr>
                            <th style="width: 60px;"><strong>#</strong></th>
                            <th><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CUSTOMER'); ?></strong></th>
                            <th class="visible-lg"><strong><?php echo Sumo\Language::getVar('SUMO_NOUN_CITY'); ?></strong></th>
                            <th style="width: 85px;"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border-y">

                        <?php foreach ($visitors as $visitor) { ?>
                        <tr>
                            <td style="width: 60px;"><?php echo str_pad($visitor['customer_id'], 4, 0, STR_PAD_LEFT); ?></td>
                            <td><?php echo $visitor['customer']; ?></td>
                            <td class="visible-lg"><?php echo $visitor['city']; ?></td>
                            <td style="width: 85px;"><i class="fa fa-angle-right"></i><a href="<?php echo $visitor['info']; ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DETAILS'); ?></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
            <div class="header">
                <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_VISITORS'); ?></h3>
            </div>
            <div class="content">
                <p><?php echo Sumo\Language::getVar('SUMO_NO_RESULTS'); ?></p>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="row">
    <?php /*
    <div class="col-md-5">
        <div class="block-flat dark-box visitors">
            <h4 class="no-margin">Herkomst debiteuren</h4>
            <?php if (isset($countries) && !empty($countries)) { ?>
            <div class="row">
                <div class="counters col-md-4">
                <?php foreach ($countries as $country) { ?>
                <h1><?php echo $country['data']; ?></h1>
                <?php } ?>
                </div>
                <div class="col-md-8">
                    <div id="country_chart" style="height: 140px;"></div>
                </div>
            </div>
            <div class="row footer">
                <?php foreach ($countries as $country) { ?>
                <div class="col-md-6"><p><i style="color: <?php echo $country['color']; ?>" class=" fa fa-square"></i> <?php echo $country['label']; ?></p></div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <p><?php echo Sumo\Language::getVar('SUMO_NO_RESULTS'); ?></p>
            <?php } ?>
        </div>
    </div>
    */?>
    <div class="col-md-12">
        <div class="block widget-notes">
            <div class="header dark"><h4><?php echo Sumo\Language::getVar('SUMO_TODO_LIST'); ?></h4></div>
            <div class="content">
                <ul class="paper todo" id="list_todo">
                    <?php foreach ($todo as $item) { ?>
                    <li>
                        <dl>
                            <dt><input type="checkbox" value="<?php echo $item['todo_id']; ?>" /></dt>
                            <dd><?php echo $item['content']; ?></dd>
                        </dl>
                    </li>
                    <?php } ?>
                    <li>
                        <dl>
                            <dt><a href="javascript:;" id="add_todo"><i class="fa fa-plus"></i></a></dt>
                            <dd><input type="text" id="new_todo" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_ADD_TODO'); ?>" /></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
