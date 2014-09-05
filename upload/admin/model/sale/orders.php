<?php
namespace Sumo;
class ModelSaleOrders extends Model
{
    public function addOrder($data)
    {
        $this->query("INSERT INTO PREFIX_orders SET order_status = :status, order_date = :date", array('status' => $data['order_status_id'], 'date' => date('Y-m-d H:i:s')));
        return $this->saveOrder($this->lastInsertId(), $data);
    }

    public function saveOrder($order_id, $data)
    {
        $data['store'] = array();
        $storeData = $this->model_settings_stores->getStore($data['store_id']);
        $data['store']['id']    = $data['store_id'];
        $data['store']['url']   = $storeData['base_' . $storeData['base_default']];
        $data['store']['name']  = $storeData['name'];

        // Existing customer?
        if (!isset($data['customer']['customer_id']) || empty($data['customer']['customer_id'])) {
            $customerData = $data['customer'];
            $customerData['address'][] = $data['customer']['payment_address'];

            unset($customerData['customer_id']);
            unset($customerData['payment_address']);
            unset($customerData['shipping_address']);

            $this->load->model('sale/customer');
            $data['customer']['customer_id'] = $this->model_sale_customer->addCustomer($customerData);
        }

        $this->query("UPDATE PREFIX_orders SET order_status = :status WHERE order_id = :id", array(
            'status'    => $data['order_status_id'],
            'id'        => $order_id 
        ));

        $this->query("DELETE FROM PREFIX_orders_download WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders_data WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders_totals WHERE order_id = :id", array('id' => $order_id));

        $this->query(
            "INSERT INTO PREFIX_orders_data
            SET order_id        = :id,
                store           = :store,
                admin_comment   = :comment,
                discount        = :discount,
                reward          = :reward,
                points          = :points,
                customer        = :customer,
                shipping        = :shipping,
                payment         = :payment",
            array(
                'id'            => $order_id,
                'store'         => json_encode($data['store']),
                'comment'       => $data['comment'],
                'discount'      => json_encode($data['discount']),
                'customer'      => json_encode($data['customer']),
                'shipping'      => json_encode($data['method']['shipping']),
                'payment'       => json_encode($data['method']['payment']),
                'reward'        => '',
                'points'        => $data['points']
            )
        );

        // Add product lines
        foreach ($data['lines'] as $productLine) {
            $this->query("INSERT INTO PREFIX_orders_lines 
                SET order_id        = :id,
                    product_id      = :productID,
                    name            = :name,
                    `option`        = '',
                    download        = '',
                    model           = :model,
                    quantity        = :quantity,
                    price           = :price,
                    tax_percentage  = :taxPercentage", 
                array(
                    'id'            => $order_id,
                    'productID'     => $productLine['product_id'],
                    'name'          => $productLine['name'],
                    'model'         => $productLine['model'],
                    'quantity'      => $productLine['quantity'],
                    'price'         => $productLine['price'],
                    'taxPercentage' => $productLine['tax']
                )
            );

            // Product has download?
            $productDownloads = $this->query("SELECT * 
                FROM PREFIX_download d, PREFIX_download_description dd 
                WHERE d.download_id = dd.download_id 
                    AND dd.language_id = :languageID
                    AND d.download_id IN (
                            SELECT download_id 
                            FROM PREFIX_product_to_download 
                            WHERE product_id = :productID)", array(
                        'languageID'    => $this->config->get('language_id'),
                        'productID'     => $productLine['product_id']
                    ))->fetchAll();
            foreach ($productDownloads as $productDownload) {
                // Add order download
                $this->query("INSERT INTO PREFIX_orders_download SET 
                    order_id = :orderID,
                    name = :name,
                    filename = :filename,
                    remaining = :remaining", array(
                        'orderID'   => $productDownload['order_id'],
                        'name'      => $productDownload['mask'],
                        'filename'  => $productDownload['filename'],
                        'remaining' => $productDownload['remaining']
                    ));
            }
        }

        // Add product totals
        foreach ($data['totals'] as $sortOrder => $productTotal) {
            $labelInject = isset($productTotal['label_inject']) ? $productTotal['label_inject'] : '';

            $this->query("INSERT INTO PREFIX_orders_totals 
                SET order_id        = :id,
                    sort_order      = :sortOrder,
                    label           = :label,
                    label_inject    = :labelInject,
                    value           = :value,
                    value_hr        = :valueHR", 
                array(
                    'id'            => $order_id,
                    'sortOrder'     => $sortOrder,
                    'label'         => $productTotal['label'],
                    'labelInject'   => $labelInject,
                    'value'         => $productTotal['value'],
                    'valueHR'       => Formatter::currency($productTotal['value'])
                )
            );
        }
    }

    public function getOrdersTotal()
    {
        return $this->query("SELECT COUNT(*) AS total FROM PREFIX_orders")->fetchColumn();
    }

    public function getOrders($data)
    {
        $orders = $this->fetchAll(
            "SELECT  *
            FROM PREFIX_orders
            ORDER BY order_date DESC
            LIMIT " . (int)$data['start'] . "," . (int)$data['limit']
        );

        $return = array();
        foreach ($orders as $order) {
            $order = array_merge($order, $this->getOrder($order['order_id']));
            $order['total'] = $this->getOrderTotal($order['order_id']);

            $return[] = $order;
        }

        return $return;
    }

    public function getOrder($order_id)
    {
        $list = $this->query("SELECT * FROM PREFIX_orders WHERE order_id = :id", array('id' => $order_id))->fetch();
        if (!count($list) || !$list) {
            return false;
        }

        $data               = $this->query("SELECT * FROM PREFIX_orders_data WHERE order_id = :id", array('id' => $order_id))->fetch();
        $list['store']      = json_decode($data['store'], true);
        $list['customer']   = json_decode($data['customer'], true);
        $list['shipping']   = json_decode($data['shipping'], true);
        $list['payment']    = json_decode($data['payment'], true);
        $list['discount']   = json_decode($data['discount'], true);
        $list['reward']     = $data['reward'];
        $list['points']     = $data['points'];
        $list['status']     = $this->statusToText($list['order_status']);

        $history = $this->fetchAll("SELECT history_id, status_id, notify, comment, history_date FROM PREFIX_orders_history WHERE order_id = :id ORDER BY order_id DESC", array('id' => $order_id));
        foreach ($history as $data) {
            $list['updated']    = $data['history_date'];
            $list['history'][]  = $data;
        }

        $lines = $this->fetchAll("SELECT * FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order_id));
        foreach ($lines as $data) {
            $data['option']     = empty($data['option']) ? array() : json_decode($data['option']);
            $data['download']   = empty($data['download']) ? array() : json_decode($data['download']);

            $list['lines'][]    = $data;
        }

        $totals = $this->fetchAll("SELECT * FROM PREFIX_orders_totals WHERE order_id = :id ORDER BY sort_order ASC", array('id' => $order_id));
        foreach ($totals as $total) {
            // Confusing..
            $total['label_orig'] = $total['label'];

            if (!empty($total['label_inject'])) {
                $total['label'] = sprintf(Language::getVar($total['label'] . '_INJ'), $total['label_inject']);
            } else {
                $total['label'] = Language::getVar($total['label']);
            }

            $list['totals'][] = $total;
        }

        // Get address format for payment
        
        // Default address format
        $defaultAddressFormat = "{firstname} {lastname}\n{address_1}\n{postcode} {city}\n{country}";

        $addressFormat = $this->query("SELECT * FROM PREFIX_country WHERE country_id = :id", array('id' => $list['customer']['payment_address']['country_id']))->fetch();
        if (!empty($addressFormat)) {
            $list['customer']['payment_address']['address_format'] = !empty($addressFormat['address_format']) ? $addressFormat['address_format'] : $defaultAddressFormat;
            $list['customer']['payment_address']['country'] = $addressFormat['name'];
        } else {
            $list['customer']['payment_address']['address_format'] = $defaultAddressFormat;
            $list['customer']['payment_address']['country'] = '';
        }

        // Get address format for shipping
        $addressFormat = $this->query("SELECT * FROM PREFIX_country WHERE country_id = :id", array('id' => $list['customer']['payment_address']['country_id']))->fetch();
        if (!empty($addressFormat)) {
            $list['customer']['shipping_address']['address_format'] = !empty($addressFormat['address_format']) ? $addressFormat['address_format'] : $defaultAddressFormat;
            $list['customer']['shipping_address']['country'] = $addressFormat['name'];
        } else {
            $list['customer']['shipping_address']['address_format'] = $defaultAddressFormat;
            $list['customer']['shipping_address']['country'] = '';
        }

        // Get invoice information (if applicable)
        $invoiceData = $this->query("SELECT invoice_no, invoice_id 
            FROM PREFIX_invoice 
            WHERE invoice_id IN (
                SELECT invoice_id 
                FROM PREFIX_orders_to_invoice 
                WHERE order_id = :orderID)
            LIMIT 1", array(
                'orderID'   => $order_id))->fetch();
        if (!empty($invoiceData)) {
            $list['invoice_no'] = $invoiceData['invoice_no'];
            $list['invoice_id'] = $invoiceData['invoice_id'];
        }

        // Get apps
        $shippingApp = $this->query("SELECT * FROM PREFIX_apps WHERE name = :shippingApp LIMIT 1", array('shippingApp' => $list['shipping']['name']))->fetch();
        if (!empty($shippingApp)) {
            $list['shipping']['list_name'] = $shippingApp['list_name'];
        } else {
            $list['shipping']['list_name'] = '';
        }

        $paymentApp = $this->query("SELECT * FROM PREFIX_apps WHERE name = :paymentApp LIMIT 1", array('paymentApp' => $list['payment']['name']))->fetch();
        if (!empty($paymentApp)) {
            $list['payment']['list_name'] = $paymentApp['list_name'];
        } else {
            $list['payment']['list_name'] = '';
        }

        return $list;
    }

    public function statusToText($status_id)
    {
        $data = $this->query("SELECT name FROM PREFIX_order_status WHERE order_status_id = :id AND language_id = :language", array('id' => $status_id, 'language' => $this->config->get('language_id')))->fetch();
        return $data['name'];
    }

    public function addHistory($order_id, $data)
    {
        $this->query(
            "INSERT INTO PREFIX_orders_history SET order_id = :id, status_id = :status_id, notify = :notify, comment = :comment, history_date = :date",
            array(
                'id'        => $order_id,
                'status_id' => $data['status'],
                'notify'    => isset($data['notify']) ? $data['notify'] : false,
                'comment'   => !empty($data['comment']) ? strip_tags($data['comment']) : '',
                'date'      => date('Y-m-d H:i:s')
            )
        );
        $this->updateStatus($order_id, $data['status'], isset($data['notify']) ? true : false, !empty($data['comment']) ? strip_tags($data['comment']) : null);
    }

    public function updateStatus($order_id, $status_id, $sendMail = null, $comment = null)
    {
        $this->query("UPDATE PREFIX_orders SET order_status = :status WHERE order_id = :id", array('id' => $order_id, 'status' => $status_id));
        if ($sendMail == null) {
            $sendMail = $this->config->get('customer_notify_email');
        }

        if ($sendMail) {
            $order_info = $this->getOrder($order_id);
            Mailer::setOrder($order_info);
            Mailer::setCustomer($order_info['customer']);
            //$template = Mailer::getTemplate('update_order_status_' . $status_id, !empty($order_info['customer']['language_id']) ? $order_info['customer']['language_id'] : null);
            $template = Mailer::getTemplate('update_order_status_' . $status_id, null);

            Mail::setTo($order_info['customer']['email']);
            Mail::setSubject($template['title']);

            if ($comment != null) {
                $template['content'] = str_replace('{hasComments}', $comment, $template['content']);
            }
            else {
                $template['content'] = str_replace('{hasComments}', '', $template['content']);
            }

            Mail::setHTML($template['content']);
            Mail::send();
        }
    }

    public function generateInvoice($orderID)
    {
        $orderInfo = $this->getOrder($orderID);
        $invoiceID = 0;

        if ($orderInfo && empty($orderInfo['invoice_no'])) {
            $this->load->model('sale/invoice');

            // Go ahead, add the invoice
            $invoiceData['description'] = $invoiceData['amount'] = $invoiceData['tax_percentage'] = $invoiceData['quantity'] = $invoiceData['product'] = $invoiceData['product_id'] = array();

            foreach ($orderInfo['lines'] as $i => $line) {
                $invoiceData['description'][$i]    = $line['name'];
                $invoiceData['amount'][$i]         = $line['price'];
                $invoiceData['tax_percentage'][$i] = $line['tax_percentage'];
                $invoiceData['quantity'][$i]       = $line['quantity'];
                $invoiceData['product'][$i]        = $line['model'];
                $invoiceData['product_id'][$i]     = $line['product_id'];
            }

            foreach ($orderInfo['totals'] as $i => $total) {
                $orderInfo['totals'][$i]['label'] = $total['label_orig'];
            }

            if (!empty($orderInfo['customer']['payment_address']['middlename'])) {
                $name = $orderInfo['customer']['payment_address']['firstname'] . ' ' . $orderInfo['customer']['payment_address']['middlename'] . ' ' . $orderInfo['customer']['payment_address']['lastname'];
            } 
            else {
                $name = $orderInfo['customer']['payment_address']['firstname'] . ' ' . $orderInfo['customer']['payment_address']['lastname'];
            }

            $orderInfo['discount']['points'] = $orderInfo['points'];

            $invoiceData = array_merge($invoiceData, array(
                'customer_id'       => $orderInfo['customer']['customer_id'],
                'customer_name'     => $name,
                'customer_address'  => $orderInfo['customer']['payment_address']['address_1'] . ' ' . $orderInfo['customer']['payment_address']['number'] . $orderInfo['customer']['payment_address']['addon'],
                'customer_postcode' => $orderInfo['customer']['payment_address']['postcode'],
                'customer_city'     => $orderInfo['customer']['payment_address']['city'],
                'customer_country'  => $orderInfo['customer']['payment_address']['country_id'],
                'customer_email'    => $orderInfo['customer']['email'],
                'payment_amount'    => $orderInfo['payment']['total'],
                'payment_tax'       => $orderInfo['payment']['tax_percentage'],
                'shipping_amount'   => $orderInfo['shipping']['total'],
                'shipping_tax'      => $orderInfo['shipping']['tax_percentage'],
                'discount'          => $orderInfo['discount'],
                'sent_date'         => time(),
                'notes'             => '',
                'template'          => 'invoice',
                'term'              => 14,
                'auto'              => 0,
                'reference'         => '',
                'totals'            => $orderInfo['totals']
            ));

            $invoiceID = $this->model_sale_invoice->addInvoice($invoiceData);

            $this->query("INSERT INTO PREFIX_orders_to_invoice SET 
                order_id = :orderID,
                invoice_id = :invoiceID", array(
                    'orderID'   => $orderID,
                    'invoiceID' => $invoiceID
            ));
        }

        return $invoiceID;
    }

    public function getOrderProducts($order_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order_id));
    }

    public function getOrderTotal($order_id)
    {
        $data = $this->query("SELECT value FROM PREFIX_orders_totals WHERE order_id = :id AND label = 'SUMO_NOUN_OT_TOTAL'", array('id' => $order_id))->fetch();
        return $data['value'];
    }

    public function hasInvoice($order_id)
    {
        $check = $this->getInvoice($order_id);
        return !empty($check);
    }

    public function getInvoice($order_id)
    {
        return $this->query("SELECT invoice_id FROM PREFIX_orders_to_invoice WHERE order_id = :id", array('id' => $order_id))->fetch();
    }

    public function getTotalSales()
    {
        $totalSales = $this->query("SELECT SUM(value) AS total_sales FROM PREFIX_orders_totals WHERE label = 'SUMO_NOUN_OT_TOTAL'")->fetch();

        return $totalSales['total_sales'];
    }

    public function getTotalSalesByYear($year)
    {
        $totalSales = $this->query("SELECT SUM(ot.value) AS total_sales 
            FROM PREFIX_orders_totals ot, PREFIX_orders o 
            WHERE ot.order_id = o.order_id 
                AND ot.label = 'SUMO_NOUN_OT_TOTAL'
                AND YEAR(o.order_date) = :year", array('year' => $year))->fetch();

        return $totalSales['total_sales'];
    }

    public function getOrderStats($interval)
    {
        switch ($interval) {
            case 'day':
                $sqlColumn = 'HOUR(order_date) AS label';
                $sqlGroup  = 'HOUR(order_date)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 23 HOUR) <= order_date'; 
            break;

            case 'week':
                $sqlColumn = 'WEEKDAY(order_date) AS label';
                $sqlGroup  = 'WEEKDAY(order_date)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 6 DAY) <= order_date';
            break;

            case 'month':
                $sqlColumn = 'WEEKOFYEAR(order_date) AS label';
                $sqlGroup  = 'WEEKOFYEAR(order_date)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= order_date';
            break;

            case 'year':
                $sqlColumn = 'MONTH(order_date) AS label';
                $sqlGroup  = 'MONTH(order_date)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 1 YEAR) <= order_date';
            break;
        }

        return $this->query("SELECT $sqlColumn, COUNT(*) AS value FROM PREFIX_orders WHERE $sqlWhere GROUP BY $sqlGroup ORDER BY order_date")->fetchAll();
    }

    public function remove($order_id)
    {
        $this->query("DELETE FROM PREFIX_orders_data WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders_totals WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders_history WHERE order_id = :id", array('id' => $order_id));
        $this->query("DELETE FROM PREFIX_orders WHERE order_id = :id", array('id' => $order_id));
    }
}
