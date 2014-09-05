<?php
namespace Sumo;
class ControllerCommonHome extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_NOUN_DASHBOARD'));
        $this->document->addStyle('view/css/pages/dashboard.css');
        $this->document->addScript('view/js/pages/dashboard.js');
        $this->document->addScript('view/js/jquery/jquery.flot.js');
        $this->document->addScript('view/js/jquery/jquery.flot.pie.js');
        $this->document->addScript('view/js/jquery/jquery.flot.resize.js');

        $this->data['token'] = $this->session->data['token'];

        $this->load->model('sale/orders');
        $this->data['total_sale']      = Formatter::currency($this->model_sale_orders->getTotalSales());
        $this->data['total_sale_year'] = Formatter::currency($this->model_sale_orders->getTotalSalesByYear(date('Y')));
        $this->data['total_order']     = $this->model_sale_orders->getOrdersTotal();

        $this->load->model('sale/customer');
        $this->data['total_customer'] = $this->model_sale_customer->getTotalCustomers();
        $this->data['total_customer_approval'] = $this->model_sale_customer->getTotalCustomersAwaitingApproval();

        $this->load->model('catalog/review');
        $this->data['total_review'] = $this->model_catalog_review->getTotalReviews();
        $this->data['total_review_approval'] = $this->model_catalog_review->getTotalReviewsAwaitingApproval();

        $this->data['orders'] = array();

        $data = array(
            'sort'  => 'o.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 10
        );

        $results = $this->model_sale_orders->getOrders($data);

        foreach ($results as $result) {

            if (!empty($result['middlename'])) {
                $name = $result['customer']['firstname'] . ' ' . $result['customer']['middlename'] . ' ' . $result['customer']['lastname'];
            }
            else {
                $name = $result['customer']['firstname'] . ' ' . $result['customer']['lastname'];
            }

            $this->data['orders'][] = array(
                'order_id'   => $result['order_id'],
                'customer'   => $name,
                'status'     => $result['status'],
                'date_added' => Formatter::date($result['order_date']),
                'total'      => Formatter::currency($result['total']),
                'info'       => $this->url->link('sale/orders/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], 'SSL')
            );
        }

        // Get last ten customers
        $data = array(
            'sort'  => 'date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 10
        );

        $results = $this->model_sale_customer->getCustomers($data);

        foreach ($results as $result) {

            if (!empty($result['middlename'])) {
                $name = $result['firstname'] . ' ' . $result['middlename'] . ' ' . $result['lastname'];
            }
            else {
                $name = $result['firstname'] . ' ' . $result['lastname'];
            }

            // Get address
            if ($result['address_id'] > 0) {
                $addressInfo = $this->model_sale_customer->getAddress($result['address_id']);
                $result['city'] = $addressInfo['city'];
            } else {
                $result['city'] = '&mdash;';
            }

            $this->data['visitors'][] = array(
                'customer_id' => $result['customer_id'],
                'customer'    => $name,
                'city'        => $result['city'],
                'info'        => $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL')
            );
        }

        // Get countries for customers
        /*
        $this->load->model('sale/customer');
        $countries = $this->model_sale_customer->getCustomersPerCountry();
        $colors = array("#93e529", "#ffffff", "#f97f32", "#40a5c3");

        foreach ($countries as $i => $country) {
            $this->data['countries'][] = array(
                'label'     => $country['country_name'],
                'data'      => $country['customers'],
                'color'     => $colors[$i]
            );
        }
        */

        $this->load->model('user/user');

        $this->data = array_merge($this->data, array(
            'todo'          => $this->model_user_user->getTodos(),
            'uri_orders'    => $this->url->link('sale/orders', 'token=' . $this->session->data['token']),
            'uri_customers' => $this->url->link('sale/customers', 'token=' . $this->session->data['token'])
        ));


        /*
        $this->data['visitors'] = array();
        $this->load->model('report/online');

        $results = Database::fetchAll("SELECT customer_id FROM PREFIX_customer ORDER BY date_added DESC LIMIT 0,5");
        foreach ($results as $list) {
            if ($list['customer_id'] >= 1) {
                $tmp = Database::query("SELECT city, firstname, lastname FROM PREFIX_address WHERE customer_id = :id LIMIT 1", array('id' => $list['customer_id']))->fetch();
                $list['city'] = $tmp['city'];
                $list['customer'] = $tmp['firstname'] . ' ' . $tmp['lastname'];
            }
            else {
                $list['city'] = '';
                $list['customer'] = Language::getVar('SUMO_NOUN_GUEST');
            }
            $this->data['visitors'][] = $list;
        }

        if ($this->config->get('currency_auto')) {
            $this->load->model('localisation/currency');

            $this->model_localisation_currency->updateCurrencies();
        }
        */

        $this->assembleCharts();

        $this->template = 'common/home.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function add_todo()
    {
        $this->load->model('user/user');

        // AJAX
        $todo = $this->request->post['todo'];

        if (!empty($todo)) {
            $todoID = $this->model_user_user->addTodo($todo);

            $return = array('todo' => $todo, 'id' => $todoID);
        }
        else {
            $return = false;
        }

        $this->response->setOutput(json_encode($return));
    }

    public function complete_todo()
    {
        $this->load->model('user/user');

        // AJAX
        $todoID = $this->request->get['todo_id'];

        if (!empty($todoID)) {
            $this->model_user_user->completeTodo($todoID);

            $return = true;
        }
        else {
            $return = false;
        }

        $this->response->setOutput(json_encode($return));
    }

    public function assembleCharts()
    {
        $this->load->model('sale/orders');
        $this->load->model('sale/return');
        $this->load->model('sale/customer');

        $orderRange    = isset($this->request->get['order_range']) ? $this->request->get['order_range'] : 'week';
        $customerRange = isset($this->request->get['customer_range']) ? $this->request->get['customer_range'] : 'week';

        /**
        * First do orders
        */
        $orderStats = $returnStats = array();

        foreach ($this->model_sale_orders->getOrderStats($orderRange) as $stat) {
            $orderStats[$stat['label']] = $stat['value'];
        }

        foreach ($this->model_sale_return->getReturnStats($orderRange) as $stat) {
            $returnStats[$stat['label']] = $stat['value'];
        }

        $uri_addon_order = $uri_addon_customer = '';

        switch ($orderRange) {
            case 'day':
                // Show key as hour
                $currentHour = date("H");
                $i = 0;

                for ($hour = $currentHour - 23; $hour <= $currentHour; $hour++) {
                    $statHour = $hour;

                    if ($hour < 0) {
                        $statHour += 24;
                    }

                    $totalOrders = isset($orderStats[$statHour]) ? $orderStats[$statHour] : 0;
                    $totalReturns = isset($returnStats[$statHour]) ? $returnStats[$statHour] : 0;

                    $this->data['order_stats'][] = array($i, $totalOrders);
                    $this->data['return_stats'][] = array($i, $totalReturns);
                    $this->data['order_stats_labels'][] = $statHour . ':00';
                    $i++;
                }

                $this->data['orders_stats'] = Language::getVar('SUMO_NOUN_STATS_DAY');
                $uri_addon_order = 'order_range=day';
            break;

            case 'week':
                // Show key as day of week, 0 = monday
                $currentDay = date("N");
                $i = 0;

                for ($day = $currentDay - 6; $day <= $currentDay; $day++) {
                    $statDay = $day;

                    if ($day < 1) {
                        $statDay += 7;
                    }

                    // MySQL starts at 0 for weekdays.
                    $totalOrders = isset($orderStats[$statDay - 1]) ? $orderStats[$statDay - 1] : 0;
                    $totalReturns = isset($returnStats[$statDay - 1]) ? $returnStats[$statDay - 1] : 0;

                    $this->data['order_stats'][] = array($i, $totalOrders);
                    $this->data['return_stats'][] = array($i, $totalReturns);
                    $this->data['order_stats_labels'][] = Language::getVar('SUMO_NOUN_DAY_ABBR_' . $statDay);
                    $i++;
                }

                $this->data['orders_stats'] = Language::getVar('SUMO_NOUN_STATS_WEEK');
            break;

            case 'month':
                // Show key as week of year
                $currentWeek = date("W");
                $i = 0;

                // Determine the number of weeks in the previous year
                $weeksInLastYear = 52; // Defaulting to 52.

                if ($currentWeek < 4) {
                    $date = new DateTime;
                    $date->setISODate((date("Y") - 1), 53);

                    if ($data->format("W") === "53") {
                        $weeksInLastYear = 53;
                    }
                }

                for ($week = $currentWeek - 5; $week <= $currentWeek; $week++) {
                    $statWeek = $week;

                    if ($week < 1) {
                        $statWeek += $weeksInLastYear;
                    }

                    // Does this week actually belong here? Not all months are 6 weeks.
                    if ($i == 0) {
                        if (date("W", strtotime("-1 month")) != $statWeek) {
                            continue;
                        }
                    }

                    $totalOrders = isset($orderStats[$statWeek]) ? $orderStats[$statWeek] : 0;
                    $totalReturns = isset($returnStats[$statWeek]) ? $returnStats[$statWeek] : 0;

                    $this->data['order_stats'][] = array($i, $totalOrders);
                    $this->data['return_stats'][] = array($i, $totalReturns);
                    $this->data['order_stats_labels'][] = 'WK' . $statWeek;
                    $i++;
                }

                $this->data['orders_stats'] = Language::getVar('SUMO_NOUN_STATS_MONTH');
                $uri_addon_order = 'order_range=month';
            break;

            case 'year':
                // Show key as hour
                $currentMonth = date("n");
                $i = 0;

                for ($month = $currentMonth - 11; $month <= $currentMonth; $month++) {
                    $statMonth = $month;

                    if ($month < 1) {
                        $statMonth += 12;
                    }

                    $totalOrders = isset($orderStats[$statMonth]) ? $orderStats[$statMonth] : 0;
                    $totalReturns = isset($returnStats[$statMonth]) ? $returnStats[$statMonth] : 0;

                    $this->data['order_stats'][] = array($i, $totalOrders);
                    $this->data['return_stats'][] = array($i, $totalReturns);
                    $this->data['order_stats_labels'][] = Language::getVar('SUMO_NOUN_MONTH_ABBR_' . $statMonth);
                    $i++;
                }

                $this->data['orders_stats'] = Language::getVar('SUMO_NOUN_STATS_YEAR');
                $uri_addon_order = 'order_range=year';
            break;
        }

        /**
        * Do customers
        */
        $customerStats = array();

        foreach ($this->model_sale_customer->getCustomerStats($customerRange) as $stat) {
            $customerStats[$stat['label']] = $stat['value'];
        }

        switch ($customerRange) {
            case 'day':
                // Show key as hour
                $currentHour = date("H");
                $i = 0;

                for ($hour = $currentHour - 23; $hour <= $currentHour; $hour++) {
                    $statHour = $hour;

                    if ($hour < 0) {
                        $statHour += 24;
                    }

                    $totalCustomers = isset($customerStats[$statHour]) ? $customerStats[$statHour] : 0;

                    $this->data['customer_stats'][] = array($i, $totalCustomers);
                    $this->data['customer_stats_labels'][] = $statHour . ':00';
                    $i++;
                }

                $this->data['customers_stats'] = Language::getVar('SUMO_NOUN_STATS_DAY');
                $uri_addon_customer = 'customer_range=day';
            break;

            case 'week':
                // Show key as day of week, 0 = monday
                $currentDay = date("N");
                $i = 0;

                for ($day = $currentDay - 6; $day <= $currentDay; $day++) {
                    $statDay = $day;

                    if ($day < 1) {
                        $statDay += 7;
                    }

                    // MySQL starts at 0 for weekdays.
                    $totalCustomers = isset($customerStats[$statDay - 1]) ? $customerStats[$statDay - 1] : 0;

                    $this->data['customer_stats'][] = array($i, $totalCustomers);
                    $this->data['customer_stats_labels'][] = Language::getVar('SUMO_NOUN_DAY_ABBR_' . $statDay);
                    $i++;
                }

                $this->data['customers_stats'] = Language::getVar('SUMO_NOUN_STATS_WEEK');
            break;

            case 'month':
                // Show key as week of year
                $currentWeek = date("W");
                $i = 0;

                // Determine the number of weeks in the previous year
                $weeksInLastYear = 52; // Defaulting to 52.

                if ($currentWeek < 4) {
                    $date = new DateTime;
                    $date->setISODate((date("Y") - 1), 53);

                    if ($data->format("W") === "53") {
                        $weeksInLastYear = 53;
                    }
                }

                for ($week = $currentWeek - 5; $week <= $currentWeek; $week++) {
                    $statWeek = $week;

                    if ($week < 1) {
                        $statWeek += $weeksInLastYear;
                    }

                    // Does this week actually belong here? Not all months are 6 weeks.
                    if ($i == 0) {
                        if (date("W", strtotime("-1 month")) != $statWeek) {
                            continue;
                        }
                    }

                    $totalCustomers = isset($customerStats[$statWeek]) ? $customerStats[$statWeek] : 0;

                    $this->data['customer_stats'][] = array($i, $totalCustomers);
                    $this->data['customer_stats_labels'][] = 'WK' . $statWeek;
                    $i++;
                }

                $this->data['customers_stats'] = Language::getVar('SUMO_NOUN_STATS_MONTH');
                $uri_addon_customer = 'customer_range=month';
            break;

            case 'year':
                // Show key as hour
                $currentMonth = date("n");
                $i = 0;

                for ($month = $currentMonth - 11; $month <= $currentMonth; $month++) {
                    $statMonth = $month;

                    if ($month < 1) {
                        $statMonth += 12;
                    }

                    $totalCustomers = isset($customerStats[$statMonth]) ? $customerStats[$statMonth] : 0;

                    $this->data['customer_stats'][] = array($i, $totalOrders);
                    $this->data['customer_stats_labels'][] = Language::getVar('SUMO_NOUN_MONTH_ABBR_' . $statMonth);
                    $i++;
                }

                $this->data['customers_stats'] = Language::getVar('SUMO_NOUN_STATS_YEAR');
                $uri_addon_customer = 'customer_range=year';
            break;
        }

        $uri_range = $this->url->link('common/home', 'token=' . $this->session->data['token']);

        $this->data = array_merge($this->data, array(
            'orders_day'        => $uri_range . '&order_range=day' . (!empty($uri_addon_customer) ? '&' . $uri_addon_customer : ''),
            'orders_week'       => $uri_range . (!empty($uri_addon_customer) ? '&' . $uri_addon_customer : ''),
            'orders_month'      => $uri_range . '&order_range=month' . (!empty($uri_addon_customer) ? '&' . $uri_addon_customer : ''),
            'orders_year'       => $uri_range . '&order_range=year' . (!empty($uri_addon_customer) ? '&' . $uri_addon_customer : ''),
            'customers_day'     => $uri_range . '&customer_range=day' . (!empty($uri_addon_order) ? '&' . $uri_addon_order : ''),
            'customers_week'    => $uri_range . (!empty($uri_addon_order) ? '&' . $uri_addon_order : ''),
            'customers_month'   => $uri_range . '&customer_range=month' . (!empty($uri_addon_order) ? '&' . $uri_addon_order : ''),
            'customers_year'    => $uri_range . '&customer_range=year' . (!empty($uri_addon_order) ? '&' . $uri_addon_order : '')
        ));
    }

    public function login()
    {
        $route = '';
        if (isset($this->request->get['route'])) {
            $part = explode('/', $this->request->get['route']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }
        }

        $ignore = array(
            'common/login',
            'common/forgotten',
            'common/reset'
        );

        if (!$this->user->isLogged() && !in_array($route, $ignore)) {
            Logger::warning('User is not logged in');
            return $this->forward('common/login');
        }

        if (!isset($this->request->get['token']) && !empty($this->session->data['token'])) {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                if (empty($_GET['route']) || $_GET['route'] == $this->config->get('admin_directory')) {
                    $_GET['route'] = 'common/home';
                }
                unset($this->request->get['route']);
                unset($this->request->get['_route_']);
                header('Location: ' . str_replace('&amp;', '&', $this->url->link($_GET['route'], 'token=' . $this->session->data['token'] . '&' . ltrim(http_build_query($this->request->get), '&'), 'SSL')));
                exit;
            }
        }

        if (isset($this->request->get['route'])) {
            $ignore = array(
                'common/login',
                'common/logout',
                'common/forgotten',
                'common/reset',
                'error/not_found',
                'error/permission'
            );

            $config_ignore = array();

            if ($this->config->get('token_ignore')) {
                $config_ignore = unserialize($this->config->get('token_ignore'));
            }

            $ignore = array_merge($ignore, $config_ignore);
            if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token']))) {
                Logger::warning('No token, no session token or token vs session token do not match (does have a route)');
                return $this->forward('common/login');
            }
        }
        else {
            if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
                Logger::warning('No token, no session token or token vs session token do not match (no route)');
                return $this->forward('common/login');
            }
        }
    }

    public function permission()
    {
        if (isset($this->request->get['route'])) {
            $route = '';

            $part = explode('/', $this->request->get['route']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }

            $ignore = array(
                '',
                'common/home',
                'common/login',
                'common/logout',
                'common/forgotten',
                'common/reset',
                'error/not_found',
                'error/permission'
            );

            if (!in_array($route, $ignore) && !$this->user->hasPermission('access', $route)) {
                return $this->forward('error/permission');
            }
        }
    }

    public function notifications()
    {
        // Check if the notifications-app is available, if so, redirect there
        if (file_exists(DIR_HOME . 'apps/notifications/information.php')) {
            return $this->forward('app/notifications');
        }

        $json = array();

        // Version check
        $latestVersion = Communicator::getVersion();
        if ($latestVersion != VERSION) {
            $json['version'] = array(
                'url'   => 'http://www.sumostore.net/download',
                'text'  => '<i class="fa fa-exclamation-triangle"></i><strong>Download V' . $latestVersion . '</strong><span class="date">' . Formatter::dateTime(time()) . '</span>'
            );
        }

        // Latest customer
        $json['customer']['data'] = Database::query("SELECT firstname, middlename, lastname, gender, customer_id, date_added FROM PREFIX_customer ORDER BY date_added DESC LIMIT 1")->fetch();
        if (empty($json['customer']['data'])) {
            unset($json['customer']);
        }
        else {
            $json['customer'] = array(
                'url'   => $this->url->link('sale/customer/update', 'customer_id=' . $json['customer']['data']['customer_id'], 'SSL'),
                'text'  => Language::getVar('SUMO_ADMIN_LATEST_CUSTOMER_REGISTERED', array($json['customer']['data']['gender'] == 'm' ? 'male' : 'female', $json['customer']['data']['firstname'] . (!empty($json['customer']['data']['middlename']) ? ' ' . $json['customer']['data']['middlename'] . ' ' : ' ') . $json['customer']['data']['lastname'], Formatter::dateTime($json['customer']['data']['date_added'], false)))
            );
        }

        // Latest order
        $json['order']['data'] = Database::query("SELECT order_id, order_date, order_status FROM PREFIX_orders ORDER BY order_date DESC LIMIT 1")->fetch();
        if (empty($json['order']['data'])) {
            unset($json['order']);
        }
        else {
            $json['order'] = array(
                'url'   => $this->url->link('sale/order/info', 'order_id=' . $json['order']['data']['order_id'], 'SSL'),
                'text'  => Language::getVar('SUMO_ADMIN_LATEST_ORDER', array(Formatter::dateTime($json['order']['data']['order_date'], false)))
            );
        }

        // Latest return
        $json['return']['data'] = Database::query("SELECT return_id, date_added FROM PREFIX_return ORDER BY date_added DESC LIMIT 1")->fetch();
        if (empty($json['return']['data'])) {
            unset($json['return']);
        }
        else {
            $json['return'] = array(
                'url'   => $this->url->link('sale/return/info', 'return_id=' . $json['return']['data']['return_id'], 'SSL'),
                'text'  => Language::getVar('SUMO_ADMIN_LATEST_RETURN', array(Formatter::dateTime($json['return']['data']['date_added'], false)))
            );
        }

        $this->response->setOutput(json_encode($json));
    }
}
