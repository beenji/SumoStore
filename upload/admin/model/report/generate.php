<?php
namespace Sumo;
class ModelReportGenerate extends Model
{
    public function getTotal($type, $filters)
    {
        switch ($type) {
            case 'sales':
                $sql = $this->getDataSales($filters);
                break;

            case 'customer':
                $sql = $this->getDataCustomer($filters);
                break;

            case 'returns':
                $sql = $this->getDataReturns($filters);
                break;

            case 'coupons':
                $sql = $this->getDataCoupons($filters);
                break;

            case 'products_viewed':
                $sql = $this->getDataViews($filters);
                break;

            case 'products_sales':
                return $this->getDataProductSales($filters);
                break;
        }

        if (!empty($sql)) {
            $sql = str_replace('SELECT', 'SELECT COUNT(*) AS get_total_' . $type . '/* ', $sql);
            $sql = str_replacE('FROM', '*/ FROM', $sql);
            $result = $this->query($sql);
            if ($result) {
                $result = $result->fetch();
                if (isset($result['get_total_' . $type])) {
                    return $result['get_total_' . $type];
                }
            }

        }

        return 0;
    }

    public function getData($type, $filters)
    {
        switch ($type) {
            case 'sales':
                return $this->getDataSales($filters);
                break;

            case 'customer':
                return $this->getDataCustomer($filters);
                break;

            case 'returns':
                return $this->getDataReturns($filters);
                break;

            case 'coupons':
                return $this->getDataCoupons($filters);
                break;

            case 'products_viewed':
                return $this->getDataViews($filters);
                break;

            case 'products_sales':
                return $this->getDataProductSales($filters);
                break;
        }
        return array();
    }

    private function getDataSales($filters)
    {
        $cache = 'report.sales.' . json_encode($filters);
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $sql = "SELECT order_id, order_date, order_status
        FROM PREFIX_orders WHERE 1=1 ";

        if (!empty($filters['status_id'])) {
            $sql .= " AND order_status = " . (int)$filters['status_id'];
        }

        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(order_date) >= '" . Formatter::dateReverse($filters['date_start']) . "'";
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(order_date) <= '" . Formatter::dateReverse($filters['date_end']) . "'";
        }

        if (isset($filters['start']) && !empty($filters['limit'])) {
            $sql .= " LIMIT " . (int) $filters['start'] . "," . (int)$filter['limit'];
        }
        else {
            return $sql;
        }

        if (empty($filters['group'])) {
            $filters['group'] = 'week';
        }

        $orders = $this->fetchAll($sql);
        $return = array();
        foreach ($orders as $order) {
            $total = $tax = $products = 0;
            $tmp = $this->fetchAll("SELECT line_type, data FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order['order_id']));
            foreach ($tmp as $list) {
                $tmpData = json_decode($list['data'], true);
                if ($list['line_type'] == 'product') {
                    $total += $tmpData['total'];
                    $tax += ($tmpData['total'] / 100 * $tmpData['tax_percentage']);
                    $products += $tmpData['quantity'];
                }
                else if ($list['line_type'] == 'shipping' || $list['line_type'] == 'payment') {
                    if ($tmpData['total']) {
                        $total += $tmpData['total'];
                        $tax += ($tmpData['total'] / 100 * $tmpData['tax_percentage']);
                    }
                }
            }
            $put = '';
            $order_time = strtotime($order['order_date']);
            switch ($filters['group']) {
                case 'day':
                    $put .= date('Y-m-d', $order_time);
                    $start_time = $end_time = $order_time;
                    break;

                case 'month':
                    $put .= date('Y-m', $order_time);
                    $start_time = strtotime('first day of this month', $order_time);
                    $end_time = strtotime('last day of this month', $order_time);
                    break;

                case 'year':
                    $put .= date('Y', $order_time);
                    $start_time = strtotime(date('Y-01-01', $order_time));
                    $end_time = strtotime(date('Y-12-31', $order_time));
                    break;

                default:
                    $put .= date('Y', $order_time);
                    $put .= 'W';
                    $put .= str_pad(date('W', $order_time), 2, '0', STR_PAD_LEFT);
                    $tmp = strtotime($put);
                    $start_time = strtotime('monday this week', $order_time);
                    $end_time = strtotime('sunday this week', $order_time);
                    break;
            }
            if (!isset($return[$put])) {
                $return[$put] = array(
                    1 => Formatter::date($start_time),
                    2 => Formatter::date($end_time),
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0
                );
            }

            $return[$put][3] += 1;
            $return[$put][4] += $products;
            $return[$put][5] += $tax;
            $return[$put][6] += $total;
        }

        foreach ($return as $date => $order) {
            $return[$date][5] = Formatter::currency($order[5]);
            $return[$date][6] = Formatter::currency($order[6]);
        }

        Cache::set($cache, $return);
        return $return;
    }

    private function getDataCustomer($filters)
    {
        $cache = 'report.customer.' . json_encode($filters);
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $sql = "SELECT order_id, order_date, order_status
        FROM PREFIX_orders WHERE 1=1 ";

        if (!empty($filters['status_id'])) {
            $sql .= " AND order_status = " . (int)$filters['status_id'];
        }

        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(order_date) >= '" . Formatter::dateReverse($filters['date_start']) . "'";
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(order_date) <= '" . Formatter::dateReverse($filters['date_end']) . "'";
        }

        if (isset($filters['start']) && !empty($filters['limit'])) {
            $sql .= " LIMIT " . (int) $filters['start'] . "," . (int)$filter['limit'];
        }
        else {
            return $sql;
        }

        if (empty($filters['group'])) {
            $filters['group'] = 'week';
        }

        $orders = $this->fetchAll($sql);
        $return = $customerDates = array();
        foreach ($orders as $order) {
            $total = $tax = $products = 0;
            $tmp = $this->fetchAll("SELECT line_type, data FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order['order_id']));
            foreach ($tmp as $list) {
                $tmpData = json_decode($list['data'], true);
                if ($list['line_type'] == 'product') {
                    $total += $tmpData['total'];
                    $tax += ($tmpData['total'] / 100 * $tmpData['tax_percentage']);
                    $products += $tmpData['quantity'];
                }
                else if ($list['line_type'] == 'shipping' || $list['line_type'] == 'payment') {
                    if ($tmpData['total']) {
                        $total += $tmpData['total'];
                        $tax += ($tmpData['total'] / 100 * $tmpData['tax_percentage']);
                    }
                }
            }
            $customer = $this->query("SELECT customer FROM PREFIX_orders_data WHERE order_id = :id", array('id' => $order['order_id']))->fetch();
            $customer = json_decode($customer['customer'], true);

            if (!isset($customer['customer_id'])) {
                $customer['customer_id'] = 'G-' . $customer['email'];
            }

            $customerDates[$customer['customer_id']][] = $order['order_date'];

            $put = $customer['customer_id'];
            if (!isset($return[$put])) {
                $name = '<a href="' . $this->url->link('sale/orders/info', 'orders_id=' . $order['order_id']) . '">';
                foreach (array('firstname', 'middlename', 'lastname') as $key) {
                    if (isset($customer[$key])) {
                        $name .= $customer[$key] . ' ';
                    }
                }
                $name .= '</a>';

                $return[$put] = array(
                    1 => Formatter::date($order['order_date']),
                    2 => $name,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0
                );
            }

            $return[$put][3] += 1;
            $return[$put][4] += $products;
            $return[$put][5] += $tax;
            $return[$put][6] += $total;
        }

        foreach ($return as $date => $order) {
            $return[$date][5] = Formatter::currency($order[5]);
            $return[$date][6] = Formatter::currency($order[6]);
        }

        foreach ($customerDates as $cid => $line) {
            $return[$cid][1] = min($customerDates[$cid]);
            if (max($customerDates[$cid]) != min($customerDates[$cid])) {
                $return[$cid][1] .= ' / ' . max($customerDates[$cid]);
            }
        }

        //Cache::set($cache, $return);
        return $return;
    }

    private function getDataReturns($filters)
    {
        $cache = 'report.returns.' . json_encode($filters);
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $sql = "SELECT return_status_id, date_added FROM PREFIX_return WHERE 1 = 1";
        if (!empty($filters['status_id'])) {
            $sql .= " AND return_status_id = " . (int)$filters['status_id'];
        }

        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(date_added) >= '" . Formatter::dateReverse($filters['date_start']) . "'";
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(date_added) <= '" . Formatter::dateReverse($filters['date_end']) . "'";
        }

        if (isset($filters['start']) && !empty($filters['limit'])) {
            $sql .= " LIMIT " . (int) $filters['start'] . "," . (int)$filter['limit'];
        }
        else {
            return $sql;
        }

        if (empty($filters['group'])) {
            $filters['group'] = 'week';
        }

        $returns = $this->fetchAll($sql);
        $return = array();

        foreach ($returns as $list) {
            $put = '';
            $return_time = strtotime($list['date_added']);
            switch ($filters['group']) {
                case 'day':
                    $put .= date('Y-m-d', $return_time);
                    $start_time = $end_time = $return_time;
                    break;

                case 'month':
                    $put .= date('Y-m', $return_time);
                    $start_time = strtotime('first day of this month', $return_time);
                    $end_time = strtotime('last day of this month', $return_time);
                    break;

                case 'year':
                    $put .= date('Y', $return_time);
                    $start_time = strtotime(date('Y-01-01', $return_time));
                    $end_time = strtotime(date('Y-12-31', $return_time));
                    break;

                default:
                    $put .= date('Y', $return_time);
                    $put .= 'W';
                    $put .= str_pad(date('W', $return_time), 2, '0', STR_PAD_LEFT);
                    $tmp = strtotime($put);
                    $start_time = strtotime('monday this week', $tmp);
                    $end_time = strtotime('sunday this week', $tmp);
                    break;
            }
            $put .= $list['return_status_id'];

            if (!isset($return[$put])) {
                $return[$put] = array(
                    1 => Formatter::date($start_time),
                    2 => Formatter::date($end_time),
                    3 => 0,
                    4 => $list['return_status_id']
                );
            }

            $return[$put][3] += 1;
        }

        $this->load->model('localisation/return_status');
        foreach ($return as $date => $list) {
            $tmp = $this->model_localisation_return_status->getReturnStatus($list[4]);
            $return[$date][4] = $tmp['name'];
        }

        Cache::set($cache, $return);
        return $return;
    }

    private function getDataCoupons($filters)
    {
        $cache = 'report.coupons.' . json_encode($filters);
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $sql = "SELECT ch.coupon_id, ch.amount, ch.date_added, c.name, c.code, c.type, c.discount FROM PREFIX_coupon_history AS ch LEFT JOIN PREFIX_coupon AS c ON c.coupon_id = ch.coupon_id WHERE 1 = 1";
        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(ch.date_added) >= '" . Formatter::dateReverse($filters['date_start']) . "'";
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(ch.date_added) <= '" . Formatter::dateReverse($filters['date_end']) . "'";
        }

        if (isset($filters['start']) && !empty($filters['limit'])) {
            $sql .= " LIMIT " . (int) $filters['start'] . "," . (int)$filter['limit'];
        }
        else {
            return $sql;
        }

        if (empty($filters['group'])) {
            $filters['group'] = 'week';
        }

        $coupons = $this->fetchAll($sql);
        $return = array();

        foreach ($coupons as $list) {
            $put = '';
            $return_time = strtotime($list['date_added']);
            switch ($filters['group']) {
                case 'day':
                    $put .= date('Y-m-d', $return_time);
                    $start_time = $end_time = $return_time;
                    break;

                case 'month':
                    $put .= date('Y-m', $return_time);
                    $start_time = strtotime('first day of this month', $return_time);
                    $end_time = strtotime('last day of this month', $return_time);
                    break;

                case 'year':
                    $put .= date('Y', $return_time);
                    $start_time = strtotime(date('Y-01-01', $return_time));
                    $end_time = strtotime(date('Y-12-31', $return_time));
                    break;

                default:
                    $put .= date('Y', $return_time);
                    $put .= 'W';
                    $put .= str_pad(date('W', $return_time), 2, '0', STR_PAD_LEFT);
                    $tmp = strtotime($put);

                    $start_time = strtotime('monday this week', $tmp);
                    $end_time = strtotime('sunday this week', $tmp);
                    break;
            }

            if (!isset($return[$put])) {
                $return[$put] = array(
                    1 => Formatter::date($start_time),
                    2 => Formatter::date($end_time),
                    3 => $list['name'],
                    4 => $list['code'],
                    5 => 0,
                    6 => 0,
                    7 => 0
                );
            }

            $return[$put][5] += 1;
            $return[$put][6] += $list['amount'];
            if (strtolower($list['type']) == 'p') {
                $return[$put][7] += ($list['amount'] / 100) * $list['discount'];
            }
            else {
                $return[$put][7] += $list['discount'];
            }
        }

        foreach ($return as $key => $list) {
            $return[$key][6] = Formatter::currency($return[$key][6]) . '<br /><span class="alert-danger">- ' . Formatter::currency($return[$key][7]) . '</span>';
            unset($return[$key][7]);
        }

        return $return;
    }

    private function getDataViews($filters)
    {
        $cache = 'report.views.' . json_encode($filters);
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $sql = "SELECT p.product_id, p.viewed, p.model_2 AS model, pd.name, p.status FROM PREFIX_product AS p LEFT JOIN PREFIX_product_description AS pd ON p.product_id = pd.product_id WHERE pd.language_id = " . $this->config->get('language_id');
        if (!isset($filters['start']) && empty($filters['limit'])) {
            $sql = "SELECT nothing FROM PREFIX_product WHERE 1 = 1";
        }

        if ($filters['status_id'] >= 1) {
            if ($filters['status_id'] == 1) {
                $sql .= " AND status = 1";
            }
            else {
                $sql .= " AND status = 0";
            }
        }
        else {
            $sql .= " AND 1 = 1";
        }

        $sql .= " ORDER BY viewed DESC";

        if (isset($filters['start']) && !empty($filters['limit'])) {
            if ($filters['start'] < 1) {
                $filters['start'] = 0;
            }
            $sql .= " LIMIT " . (int) $filters['start'] . "," . (int)$filters['limit'];
        }
        else {
            return $sql;
        }

        $return = array();
        $total = $this->getTotalViews($filters['status_id']);
        $products = $this->fetchAll($sql);
        $i = 0;

        foreach ($products as $list) {
            $return[$i++] = array(
                0 => '<i class="fa ' . ($list['status'] ? 'fa-eye' : 'fa-eye-slash') . '"></i> '  . $list['name'],
                1 => !empty($list['model']) ? $list['model'] : 'P' . $list['product_id'],
                2 => $list['viewed'],
                3 => round($list['viewed'] / $total * 100, 2) . '%'
            );
        }

        Cache::set($cache, $return);
        return $return;
    }

    private function getTotalViews($active = 0)
    {
        $cache = 'report.total.views.' . date('Y-m-d-H');
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data['total'];
        }

        $sql = "SELECT SUM(viewed) AS total FROM PREFIX_product";
        if ($active > 0) {
            if ($active == 1) {
                $sql .= " WHERE active = 1";
            }
            else {
                $sql .= " WHERE active = 0";
            }
        }
        $data = $this->query($sql)->fetch();

        Cache::set($cache, $data);
        return $data['total'];
    }

    private function getDataProductSales($filters)
    {
        $cache = 'report.products.sales.' . json_encode($filters);
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            //return $data;
        }

        $sql = "SELECT * FROM PREFIX_orders_lines AS ol LEFT JOIN PREFIX_orders AS o ON o.order_id = ol.order_id";

        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(order_date) >= '" . Formatter::dateReverse($filters['date_start']) . "'";
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(order_date) <= '" . Formatter::dateReverse($filters['date_end']) . "'";
        }


        if (isset($filters['start']) && !empty($filters['limit'])) {
            if ($filters['start'] < 1) {
                $filters['start'] = 0;
            }
        }

        $return = $temp = array();

        $products = $this->fetchAll($sql);
        $i = 0;

        foreach ($products as $list) {
            //$list = json_decode($list['data'], true);

            $check = $this->query("SELECT viewed FROM PREFIX_product WHERE product_id = :id", array('id' => $list['product_id']))->fetch();
            if (empty($check['viewed'])) {
                $check['viewed'] = 0;
            }
            $poc = $list['name'] . $list['product_id'];
            if (!isset($temp[$poc])) {
                $temp[$poc] = array(
                    0 => $list['name'],
                    1 => !empty($list['model']) ? $list['model'] : 'P' . $list['product_id'],
                    2 => $check['viewed'],
                    3 => 0, //round($list['viewed'] / $total * 100, 2) . '%',
                    4 => 0
                );
            }
            $temp[$poc][3] += $list['quantity'];
            $temp[$poc][4] += ($list['price'] * $list['quantity']);
        }

        $quantity = $total = array();
        foreach ($temp as $key => $row) {
            $quantity[$key] = $row[3];
            $total[$key] = $row[4];
        }
        unset($products);

        array_multisort($quantity, SORT_DESC, $total, SORT_DESC, $temp);

        unset($quantity);
        unset($total);

        if (isset($filters['start']) && !empty($filters['limit'])) {
            $return = array_slice($temp, $filters['start'], $filters['limit']);
        }
        else {
            return count($temp);
        }

        foreach ($return as $key => $list) {
            $return[$key][4] = Formatter::currency($list[4]);
            $return[$key][5] = round($list[3] / $list[2] * 100, 2) . '%';
        }

        Cache::set($cache, $return);
        return $return;
    }
}
