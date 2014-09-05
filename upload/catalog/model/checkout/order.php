<?php
namespace Sumo;
class ModelCheckoutOrder extends Model
{
    public function add($data)
    {
        // Update quantities
        if (!empty($data['products'])) {
            $this->load->model('catalog/product');
            foreach ($data['products'] as $list) {
                $product = $this->model_catalog_product->getProduct($list['product_id']);
                if (count($list['options_data'])) {
                    foreach ($list['options_data'] as $option_id => $option_list) {
                        if (is_array($option_list['options'])) {
                            foreach ($option_list['options'] as $value_id => $value_list) {
                                if ($value_list['subtract']) {
                                    $this->query("UPDATE PREFIX_product_option_value SET quantity = quantity - " . (int)$list['quantity'] . " WHERE value_id = :id", array('id' => $value_id));
                                }
                            }
                        }
                    }
                }
                else {
                    if ($list['stock_id'] == $list['product_id']) {
                        $this->query("UPDATE PREFIX_product SET quantity = quantity - " . (int) $list['quantity'] . " WHERE product_id = :id", array('id' => $list['product_id']));
                    }
                    else {
                        $this->query("UPDATE PREFIX_product SET quantity = quantity - " . (int) $list['quantity'] . " WHERE product_id = :id", array('id' => $list['stock_id']));
                    }
                }
            }
        }

        // Create order placeholder
        $order_id = $this->insert('PREFIX_orders', array(
            'order_date'    => date('Y-m-d H:i:s'),
            'order_status'  => 1,
        ));

        // Update order with data
        return $this->update($order_id, $data);
    }

    public function update($order_id, $data)
    {
        $oldData = $this->get($order_id);

        if (isset($data['order_status'])) {
            $this->query("UPDATE PREFIX_orders SET order_status = :status WHERE order_id = :id", array('status' => $data['order_status'], 'id' => $order_id));
            unset($data['order_status']);
        }

        if (isset($data['payment_address'])) {
            $data['customer']['payment_address'] = $data['payment_address'];
            unset($data['payment_address']);
        }

        if (isset($data['shipping_address'])) {
            $data['customer']['shipping_address'] = $data['shipping_address'];
            unset($data['shipping_address']);
        }

        if (empty($data['discount'])) {
            $data['discount'] = array();
        }

        // Check if there already is data
        $old = $this->query("SELECT customer, shipping, payment FROM PREFIX_orders_data WHERE order_id = :id", array('id' => $order_id))->fetch();
        if (is_array($old) && count($old)) {
            $this->query(
                "UPDATE PREFIX_orders_data
                SET customer    = :c,
                    shipping    = :shipping,
                    payment     = :p,
                    store       = :store,
                    discount    = :discount
                WHERE order_id  = :id",
                array(
                    'c'         => json_encode($data['customer']),
                    'shipping'  => json_encode($data['shipping_method']),
                    'p'         => json_encode($data['payment_method']),
                    'id'        => $order_id,
                    'discount'  => json_encode($data['discount']),
                    'store'     => json_encode(array(
                        'id'    => $this->config->get('store_id'),
                        'name'  => $this->config->get('name'),
                        'url'   => $this->config->get('base_http')
                    ))
                )
            );
        }
        else {
            $this->query(
                "INSERT INTO PREFIX_orders_data
                SET customer    = :c,
                    shipping    = :shipping,
                    payment     = :p,
                    store       = :store,
                    discount    = :discount,
                    order_id    = :id",
                array(
                    'c'         => json_encode($data['customer']),
                    'shipping'  => json_encode($data['shipping_method']),
                    'p'         => json_encode($data['payment_method']),
                    'id'        => $order_id,
                    'discount'  => json_encode($data['discount']),
                    'store'     => json_encode(array(
                        'id'    => $this->config->get('store_id'),
                        'name'  => $this->config->get('name'),
                        'url'   => $this->config->get('base_http')
                    ))
                )
            );
        }
        if (!empty($data['discount']['reward'])) {
            $this->query("UPDATE PREFIX_orders_data SET points = :points WHERE order_id = :id", array('id' => $order_id, 'points' => $data['discount']['reward']));
        }

        $this->query("DELETE FROM PREFIX_orders_lines WHERE order_id = :id", array('id' => $order_id));

        if (!empty($data['vouchers'])) {
            // Update 1.1
        }

        // Add product lines
        if (!empty($data['products'])) {
            foreach ($data['products'] as $list) {
                $this->query("INSERT INTO PREFIX_orders_lines
                    SET order_id        = :id,
                        product_id      = :productID,
                        name            = :name,
                        `option`        = :options,
                        download        = '',
                        model           = :model,
                        quantity        = :quantity,
                        price           = :price,
                        tax_percentage  = :taxPercentage",
                    array(
                        'id'            => $order_id,
                        'productID'     => $list['product_id'],
                        'name'          => $list['name'],
                        'model'         => $list['model'],
                        'quantity'      => $list['quantity'],
                        'price'         => $list['price'],
                        'taxPercentage' => $list['tax_percentage'],
                        'options'       => is_array($list['options']) ? json_encode($list['options']) : json_encode(array())
                    )
                );
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

            // Add points based on total amount
            if ($productTotal['label'] == 'SUMO_NOUN_OT_TOTAL') {
                if ($this->config->get('points_value')) {
                    $this->query("DELETE FROM PREFIX_customer_reward WHERE order_id = :id", array('id' => $order_id));
                    $this->query("INSERT INTO PREFIX_customer_reward SET order_id = :id, customer_id = :cid, points = :points, date_added = NOW()", array('id' => $order_id, 'cid' => $data['customer']['customer_id'], 'points' => $productTotal['value'] * $this->config->get('points_value')));
                }
            }
        }

        Cache::removeAll();

        sleep(1);

        if (!isset($old['status_id']) || (isset($old['status_id']) && isset($data['status_id']) && $old['status_id'] != $data['status_id'])) {
            $this->updateStatus($order_id, !empty($data['status_id']) ? $data['status_id'] : 1, !empty($data['status']['comment']) ? $data['status']['comment'] : !empty($data['comment']) ? $data['comment'] : '', true);
        }
        else {
            $this->updateStatus($order_id, 1, !empty($data['comment']) ? $data['comment'] : '');
        }

        Cache::removeAll();
        return $order_id;

    }

    public function get($order_id)
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
        $defaultAddressFormat = "{firstname} {lastname}\n{address_1}\n{postcode} {city}";

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
        $invoiceData = $this->query("SELECT invoice_no
            FROM PREFIX_invoice
            WHERE invoice_id IN (
                SELECT invoice_id
                FROM PREFIX_orders_to_invoice
                WHERE order_id = :orderID)
            LIMIT 1", array(
                'orderID'   => $order_id))->fetch();
        if (!empty($invoiceData)) {
            $list['invoice_no'] = $invoiceData['invoice_no'];
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

    public function addTransaction($order_id, $amount, $description = '')
    {
        $orderData = $this->get($order_id);
        $this->query("INSERT INTO PREFIX_customer_transaction SET customer_id = :cid, order_id = :id, description = :desc, amount = :amnt, date_added = NOW()",
            array(
                'cid'   => $orderData['customer']['customer_id'],
                'id'    => $order_id,
                'desc'  => !empty($description) ? $description : '',
                'amnt'  => $amount
            )
        );
    }

    public function statusToText($status_id)
    {
        $data = $this->query("SELECT name FROM PREFIX_order_status WHERE order_status_id = :id AND language_id = :language", array('id' => $status_id, 'language' => $this->config->get('language_id')))->fetch();
        return $data['name'];
    }

    public function updateStatus($order_id, $status_id, $extra = '', $notify = null)
    {
        if ($status_id == 1) {
            $notify = true;
        }
        else {
            $old = $this->get($order_id);
            if (!isset($old['status_id']) || (isset($old['status_id']) && isset($data['status_id']) && $old['status_id'] != $data['status_id'])) {
                //$this->updateStatus($order_id, !empty($data['status_id']) ? $data['status_id'] : 1, !empty($data['status']['comment']) ? $data['status']['comment'] : !empty($data['comment']) ? $data['comment'] : '');
                if ($notify == null) {
                    $notify = $this->config->get('customer_notify_email');
                }
            }
            else {
                //$this->updateStatus($order_id, 1, !empty($data['comment']) ? $data['comment'] : '');
                if ($notify == null) {
                    $notify = false;
                }
            }
        }

        $this->query(
            "UPDATE PREFIX_orders
            SET order_status    = :status
            WHERE order_id      = :id",
            array(
                'status'        => $status_id,
                'id'            => $order_id
            )
        );

        if ($notify || $this->config->get('admin_notify_email')) {
            $template   = Mailer::getTemplate('update_order_status_' . $status_id);
            $content    = $template['content'];

            if ($status_id == 1) {
                $this->load->model('account/order');
                $orderInfo = $this->model_account_order->getOrder($order_id);
                Mailer::setOrder($orderInfo);
                // Grab order totals
                foreach ($this->model_account_order->getOrderTotals($order_id) as $total) {
                    if (!empty($total['label_inject'])) {
                        $label = sprintf(Language::getVar($total['label'] . '_INJ'), $total['label_inject']);
                    }
                    else {
                        $label = Language::getVar($total['label']);
                    }

                    $totals[] = array_merge($total, array(
                        // Add percentage or something to the total-label
                        'label'         => $label
                    ));
                }

                // Grab order products
                foreach ($this->model_account_order->getOrderProducts($order_id) as $product) {
                    $price = $product['price'] * (1 + $product['tax_percentage'] / 100);

                    $products[] = array_merge($product, array(
                        'price'         => Formatter::currency($price),
                        'total'         => Formatter::currency($price * $product['quantity']),
                        'return'        => $this->url->link('account/return/insert', 'order_id=' . $orderInfo['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
                    ));
                }

                /**
                * Parse address info
                */

                // 1. Shipping
                $shippingAddress = str_replace('{address_1}', '{address_1} {number}{addon}', $orderInfo['customer']['shipping_address']['address_format']);
                foreach ($orderInfo['customer']['shipping_address'] as $key => $value) {
                    $shippingAddress = str_replace('{' . $key . '}', $value, $shippingAddress);
                }

                // 2. Payment
                $paymentAddress = str_replace('{address_1}', '{address_1} {number}{addon}', $orderInfo['customer']['payment_address']['address_format']);
                foreach ($orderInfo['customer']['payment_address'] as $key => $value) {
                    $paymentAddress = str_replace('{' . $key . '}', $value, $paymentAddress);
                }

                // Remove remaining vars and excessive line breaks
                $shippingAddress        = preg_replace("/\{([a-z0-9_\-]+)\}/", '', $shippingAddress);
                $shippingAddress        = preg_replace("/[\r\n]+/", "\n", $shippingAddress);

                // Remove remaining vars and excessive line breaks
                $paymentAddress         = preg_replace("/\{([a-z0-9_\-]+)\}/", '', $paymentAddress);
                $paymentAddress         = preg_replace("/[\r\n]+/", "\n", $paymentAddress);

                // Other data
                $order_date             = Formatter::date(time());
                $order_id               = str_pad($order_id, 6, 0, STR_PAD_LEFT);
                $payment_method         = $orderInfo['payment']['name'];
                $shipping_method        = $orderInfo['shipping']['name'];

                $order_view = '<hr />
<div class="row">
    <div class="col-sm-6">
        <h4>' . Language::getVar('SUMO_NOUN_INVOICE_ADDRESS') . '</h4>
        <p>' . nl2br($paymentAddress) . '</p>
    </div>

    <div class="col-sm-6">
        <h4>' . Language::getVar('SUMO_NOUN_SHIPPING_ADDRESS') . '</h4>
        <p>' . nl2br($shippingAddress) . '</p>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-6">
        <dl class="info">
            <dt>' . Language::getVar('SUMO_NOUN_ORDER_NO') . ':</dt>
            <dd>' . $order_id . '</dd>
        </dl>
    </div>
    <div class="col-sm-6">
        <dl class="info">
            <dt>' . Language::getVar('SUMO_NOUN_ORDER_DATE') . ':</dt>
            <dd>' . $order_date . '</dd>
        </dl>
    </div>
</div>
<table class="table" style="margin-top: 30px; font-size: 100%;">
    <thead>
        <tr>
            <th style="width: 65px; font-size: 14px;">' . Language::getVar('SUMO_NOUN_QUANTITY') . '</th>
            <th>' . Language::getVar('SUMO_NOUN_PRODUCT') . '</th>
            <th style="width: 75px;">' . Language::getVar('SUMO_NOUN_MODEL') . '</th>
            <th class="text-right" style="width: 75px;">' . Language::getVar('SUMO_NOUN_PRICE') . '</th>
            <th class="text-right" style="width: 75px;">' . Language::getVar('SUMO_NOUN_TOTAL') . '</th>
            <th style="width: 30px;"></th>
        </tr>
    </thead>
    <tbody>';
        foreach ($products as $product) {
                $order_view .= '
        <tr>
            <td>' . $product['quantity'] . '</td>
            <td>' . $product['name'] . '</td>
            <td>' . $product['model'] . '</td>
            <td class="text-right">' . $product['price'] . '</td>
            <td class="text-right">' . $product['total'] . '</td>
        </tr>';
        }
                $order_view .= '
    </tbody>
</table>
<hr>
<div class="row">
    <div class="col-sm-6">
        <div class="content">
            <dl class="info">
                <dt>' . Language::getVar('SUMO_NOUN_PAYMENT_BY') . ':</dt>
                <dd>' . $payment_method . '</dd>
            </dl>

            <dl class="info">
                <dt>' . Language::getVar('SUMO_NOUN_SHIPPING_METHOD') . ':</dt>
                <dd>' . $shipping_method . '</dd>
            </dl>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="content pull-right">';
            foreach ($totals as $total) {
                $order_view .= '
            <dl class="info">
                <dt>' . $total['label'] . ':</dt>
                <dd class="text-right" style="min-width: 75px; padding-right: 38px;">' . $total['value_hr'] . '</dd>
            </dl>';
            }
                $order_view .= '
        </div>
    </div>
</div>';

            }
            if (!empty($content)) {
                if ($notify) {
                    $data = $this->get($order_id);
                    Mailer::setCustomer($data['customer']);
                    Mailer::setOrder(array('order_id' => $order_id));
                    $template = Mailer::getTemplate('update_order_status_' . $status_id);

                    $content = $template['content'] = str_replace('{hasComments}', '<br />' . $extra, $template['content']);
                    if ($status_id == 1) {
                        $template['content'] = str_replace('{orderView}', $order_view, $template['content']);
                    }
                    Mail::setTo($data['customer']['email']);
                    Mail::setSubject($template['title']);
                    Mail::setHtml($template['content']);
                    Mail::send();
                }

                if ($this->config->get('admin_notify_email')) {
                    $sendTo = array($this->config->get('email'));
                    $extraMails = $this->config->get('extra_notify_email');
                    if (!empty($extraMails)) {
                        $extraMails = explode(',', $extraMails);
                        foreach ($extraMails as $mail) {
                            if (!empty($mail) && filter_var($mail, \FILTER_VALIDATE_EMAIL)) {
                                $sendTo[] = $mail;
                            }
                        }
                    }
                    $data = $this->get($order_id);
                    Mailer::setCustomer($data['customer']);
                    Mailer::setOrder(array('order_id' => $order_id));
                    $template = Mailer::getTemplate('update_order_status_' . $status_id);
                    $template['content'] = str_replace('{hasComments}', '<br />' . $extra, $template['content']);
                    if ($status_id == 1) {
                        $template['content'] = str_replace('{orderView}', $order_view, $template['content']);
                    }
                    foreach ($sendTo as $to) {
                        Mail::setTo($to);
                        Mail::setSubject($template['title']);
                        Mail::setHtml($template['content']);
                        Mail::send();
                    }
                }
            }
        }
        // Fallback
        if ($status_id != 1) {
            $template   = Mailer::getTemplate('update_order_status_' . $status_id);
            $content    = $template['content'];
            $content = str_replace('{hasComments}', '<br />' . $extra, $content);
            $content = str_replace('{orderView}', '', $content);
        }
        else {
            $content = $extra;
        }

        $this->query(
            "INSERT INTO PREFIX_orders_history
            SET order_id        = :id,
                status_id       = :status,
                notify          = :notify,
                comment         = :comment,
                history_date    = :date",
            array(
                'id'            => $order_id,
                'status'        => $status_id,
                'notify'        => $notify,
                'comment'       => !empty($content) ? $content : '',
                'date'          => date('Y-m-d H:i:s')
            )
        );
        Cache::removeAll();
        return true;

    }

    /*
    public function addOrder($data)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");

        $order_id = $this->db->getLastId();

        foreach ($data['products'] as $product) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

            $order_product_id = $this->db->getLastId();

            foreach ($product['option'] as $option) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
            }

            foreach ($product['download'] as $download) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int)($download['remaining'] * $product['quantity']) . "'");
            }
        }

        foreach ($data['vouchers'] as $voucher) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");
        }

        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
        }

        return $order_id;
    }

    public function getOrder($order_id)
    {
        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }

            $this->load->model('localisation/language');

            $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

            if ($language_info) {
                $language_code = $language_info['code'];
                $language_filename = $language_info['filename'];
                $language_directory = $language_info['directory'];
            } else {
                $language_code = '';
                $language_filename = '';
                $language_directory = '';
            }

            return array(
                'order_id'                => $order_query->row['order_id'],
                'invoice_no'              => $order_query->row['invoice_no'],
                'invoice_prefix'          => $order_query->row['invoice_prefix'],
                'store_id'                => $order_query->row['store_id'],
                'store_name'              => $order_query->row['store_name'],
                'store_url'               => $order_query->row['store_url'],
                'customer_id'             => $order_query->row['customer_id'],
                'firstname'               => $order_query->row['firstname'],
                'lastname'                => $order_query->row['lastname'],
                'telephone'               => $order_query->row['telephone'],
                'fax'                     => $order_query->row['fax'],
                'email'                   => $order_query->row['email'],
                'payment_firstname'       => $order_query->row['payment_firstname'],
                'payment_lastname'        => $order_query->row['payment_lastname'],
                'payment_company'         => $order_query->row['payment_company'],
                'payment_company_id'      => $order_query->row['payment_company_id'],
                'payment_tax_id'          => $order_query->row['payment_tax_id'],
                'payment_address_1'       => $order_query->row['payment_address_1'],
                'payment_address_2'       => $order_query->row['payment_address_2'],
                'payment_postcode'        => $order_query->row['payment_postcode'],
                'payment_city'            => $order_query->row['payment_city'],
                'payment_zone_id'         => $order_query->row['payment_zone_id'],
                'payment_zone'            => $order_query->row['payment_zone'],
                'payment_zone_code'       => $payment_zone_code,
                'payment_country_id'      => $order_query->row['payment_country_id'],
                'payment_country'         => $order_query->row['payment_country'],
                'payment_iso_code_2'      => $payment_iso_code_2,
                'payment_iso_code_3'      => $payment_iso_code_3,
                'payment_address_format'  => $order_query->row['payment_address_format'],
                'payment_method'          => $order_query->row['payment_method'],
                'payment_code'            => $order_query->row['payment_code'],
                'shipping_firstname'      => $order_query->row['shipping_firstname'],
                'shipping_lastname'       => $order_query->row['shipping_lastname'],
                'shipping_company'        => $order_query->row['shipping_company'],
                'shipping_address_1'      => $order_query->row['shipping_address_1'],
                'shipping_address_2'      => $order_query->row['shipping_address_2'],
                'shipping_postcode'       => $order_query->row['shipping_postcode'],
                'shipping_city'           => $order_query->row['shipping_city'],
                'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
                'shipping_zone'           => $order_query->row['shipping_zone'],
                'shipping_zone_code'      => $shipping_zone_code,
                'shipping_country_id'     => $order_query->row['shipping_country_id'],
                'shipping_country'        => $order_query->row['shipping_country'],
                'shipping_iso_code_2'     => $shipping_iso_code_2,
                'shipping_iso_code_3'     => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_method'         => $order_query->row['shipping_method'],
                'shipping_code'           => $order_query->row['shipping_code'],
                'comment'                 => $order_query->row['comment'],
                'total'                   => $order_query->row['total'],
                'order_status_id'         => $order_query->row['order_status_id'],
                'order_status'            => $order_query->row['order_status'],
                'language_id'             => $order_query->row['language_id'],
                'language_code'           => $language_code,
                'language_filename'       => $language_filename,
                'language_directory'      => $language_directory,
                'currency_id'             => $order_query->row['currency_id'],
                'currency_code'           => $order_query->row['currency_code'],
                'currency_value'          => $order_query->row['currency_value'],
                'ip'                      => $order_query->row['ip'],
                'forwarded_ip'            => $order_query->row['forwarded_ip'],
                'user_agent'              => $order_query->row['user_agent'],
                'accept_language'         => $order_query->row['accept_language'],
                'date_modified'           => $order_query->row['date_modified'],
                'date_added'              => $order_query->row['date_added']
            );
        } else {
            return false;
        }
    }

    public function confirm($order_id, $order_status_id, $comment = '', $notify = false)
    {
        $order_info = $this->getOrder($order_id);

        if ($order_info && !$order_info['order_status_id']) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Ban IP
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {
                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBanIp($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBanIp($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '1', comment = '" . $this->db->escape(($comment && $notify) ? $comment : '') . "', date_added = NOW()");

            $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

            foreach ($order_product_query->rows as $order_product) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
                }
            }

            $this->cache->delete('product');

            // Downloads
            $order_download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");

            // Gift Voucher
            $this->load->model('checkout/voucher');

            $order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

            foreach ($order_voucher_query->rows as $order_voucher) {
                $voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $order_voucher);

                $this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher['order_voucher_id'] . "'");
            }

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->model_checkout_voucher->confirm($order_id);
            }

            // Order Totals
            $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

            foreach ($order_total_query->rows as $order_total) {
                $this->load->model('total/' . $order_total['code']);

                if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
                    $this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
                }
            }

            // Send out order confirmation mail
            $language = new LanguageOld($order_info['language_directory']);
            $language->load($order_info['language_filename']);
            $language->load('mail/order');

            $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

            if ($order_status_query->num_rows) {
                $order_status = $order_status_query->row['name'];
            } else {
                $order_status = '';
            }

            $subject = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);

            // HTML Mail
            $template = new Template();

            $template->data['title'] = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

            $template->data['text_greeting'] = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
            $template->data['text_link'] = $language->get('text_new_link');
            $template->data['text_download'] = $language->get('text_new_download');
            $template->data['text_order_detail'] = $language->get('text_new_order_detail');
            $template->data['text_instruction'] = $language->get('text_new_instruction');
            $template->data['text_order_id'] = $language->get('text_new_order_id');
            $template->data['text_date_added'] = $language->get('text_new_date_added');
            $template->data['text_payment_method'] = $language->get('text_new_payment_method');
            $template->data['text_shipping_method'] = $language->get('text_new_shipping_method');
            $template->data['text_email'] = $language->get('text_new_email');
            $template->data['text_telephone'] = $language->get('text_new_telephone');
            $template->data['text_ip'] = $language->get('text_new_ip');
            $template->data['text_payment_address'] = $language->get('text_new_payment_address');
            $template->data['text_shipping_address'] = $language->get('text_new_shipping_address');
            $template->data['text_product'] = $language->get('text_new_product');
            $template->data['text_model'] = $language->get('text_new_model');
            $template->data['text_quantity'] = $language->get('text_new_quantity');
            $template->data['text_price'] = $language->get('text_new_price');
            $template->data['text_total'] = $language->get('text_new_total');
            $template->data['text_footer'] = $language->get('text_new_footer');

            $template->data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
            $template->data['store_name'] = $order_info['store_name'];
            $template->data['store_url'] = $order_info['store_url'];
            $template->data['customer_id'] = $order_info['customer_id'];
            $template->data['link'] = $order_info['store_url'] . 'account/order/info&order_id=' . $order_id;

            if ($order_download_query->num_rows) {
                $template->data['download'] = $order_info['store_url'] . 'account/download';
            } else {
                $template->data['download'] = '';
            }

            $template->data['order_id'] = $order_id;
            $template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
            $template->data['payment_method'] = $order_info['payment_method'];
            $template->data['shipping_method'] = $order_info['shipping_method'];
            $template->data['email'] = $order_info['email'];
            $template->data['telephone'] = $order_info['telephone'];
            $template->data['ip'] = $order_info['ip'];

            if ($comment && $notify) {
                $template->data['comment'] = nl2br($comment);
            } else {
                $template->data['comment'] = '';
            }

            if ($order_info['payment_address_format']) {
                $format = $order_info['payment_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['payment_firstname'],
                'lastname'  => $order_info['payment_lastname'],
                'company'   => $order_info['payment_company'],
                'address_1' => $order_info['payment_address_1'],
                'address_2' => $order_info['payment_address_2'],
                'city'      => $order_info['payment_city'],
                'postcode'  => $order_info['payment_postcode'],
                'zone'      => $order_info['payment_zone'],
                'zone_code' => $order_info['payment_zone_code'],
                'country'   => $order_info['payment_country']
            );

            $template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            if ($order_info['shipping_address_format']) {
                $format = $order_info['shipping_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['shipping_firstname'],
                'lastname'  => $order_info['shipping_lastname'],
                'company'   => $order_info['shipping_company'],
                'address_1' => $order_info['shipping_address_1'],
                'address_2' => $order_info['shipping_address_2'],
                'city'      => $order_info['shipping_city'],
                'postcode'  => $order_info['shipping_postcode'],
                'zone'      => $order_info['shipping_zone'],
                'zone_code' => $order_info['shipping_zone_code'],
                'country'   => $order_info['shipping_country']
            );

            $template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            // Products
            $template->data['products'] = array();

            foreach ($order_product_query->rows as $product) {
                $option_data = array();

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    } else {
                        $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                    }

                    $option_data[] = array(
                        'name'  => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
                }

                $template->data['products'][] = array(
                    'name'     => $product['name'],
                    'model'    => $product['model'],
                    'option'   => $option_data,
                    'quantity' => $product['quantity'],
                    'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                );
            }

            // Vouchers
            $template->data['vouchers'] = array();

            foreach ($order_voucher_query->rows as $voucher) {
                $template->data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
                );
            }

            $template->data['totals'] = $order_total_query->rows;

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order.tpl')) {
                $html = $template->fetch($this->config->get('config_template') . '/template/mail/order.tpl');
            } else {
                $html = $template->fetch('default/template/mail/order.tpl');
            }

            // Text Mail
            $text  = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
            $text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
            $text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
            $text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";

            if ($comment && $notify) {
                $text .= $language->get('text_new_instruction') . "\n\n";
                $text .= $comment . "\n\n";
            }

            // Products
            $text .= $language->get('text_new_products') . "\n";

            foreach ($order_product_query->rows as $product) {
                $text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
                }
            }

            foreach ($order_voucher_query->rows as $voucher) {
                $text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
            }

            $text .= "\n";

            $text .= $language->get('text_new_order_total') . "\n";

            foreach ($order_total_query->rows as $total) {
                $text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
            }

            $text .= "\n";

            if ($order_info['customer_id']) {
                $text .= $language->get('text_new_link') . "\n";
                $text .= $order_info['store_url'] . 'account/order/info&order_id=' . $order_id . "\n\n";
            }

            if ($order_download_query->num_rows) {
                $text .= $language->get('text_new_download') . "\n";
                $text .= $order_info['store_url'] . 'account/download' . "\n\n";
            }

            // Comment
            if ($order_info['comment']) {
                $text .= $language->get('text_new_comment') . "\n\n";
                $text .= $order_info['comment'] . "\n\n";
            }

            $text .= "Powered by SumoStore\n\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($order_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($order_info['store_name']);
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($html);
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            // Admin Alert Mail
            if ($this->config->get('config_alert_mail')) {
                $subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);

                // Text
                $text  = $language->get('text_new_received') . "\n\n";
                $text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
                $text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
                $text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
                $text .= $language->get('text_new_products') . "\n";

                foreach ($order_product_query->rows as $product) {
                    $text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

                    $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");

                    foreach ($order_option_query->rows as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                        }

                        $text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . "\n";
                    }
                }

                foreach ($order_voucher_query->rows as $voucher) {
                    $text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
                }

                $text .= "\n";

                $text .= $language->get('text_new_order_total') . "\n";

                foreach ($order_total_query->rows as $total) {
                    $text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
                }

                $text .= "\n";

                if ($order_info['comment']) {
                    $text .= $language->get('text_new_comment') . "\n\n";
                    $text .= $order_info['comment'] . "\n\n";
                }

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($order_info['store_name']);
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
                $mail->send();

                // Send to additional alert emails
                $emails = explode(',', $this->config->get('config_alert_emails'));

                foreach ($emails as $email) {
                    if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
                        $mail->setTo($email);
                        $mail->send();
                    }
                }
            }
        }
    }

    public function update($order_id, $order_status_id, $comment = '', $notify = false)
    {
        $order_info = $this->getOrder($order_id);

        if ($order_info && $order_info['order_status_id']) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Ban IP
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {

                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBanIp($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBanIp($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->load->model('checkout/voucher');

                $this->model_checkout_voucher->confirm($order_id);
            }

            if ($notify) {
                $language = new LanguageOld($order_info['language_directory']);
                $language->load($order_info['language_filename']);
                $language->load('mail/order');

                $subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

                $message  = $language->get('text_update_order') . ' ' . $order_id . "\n";
                $message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

                if ($order_status_query->num_rows) {
                    $message .= $language->get('text_update_order_status') . "\n\n";
                    $message .= $order_status_query->row['name'] . "\n\n";
                }

                if ($order_info['customer_id']) {
                    $message .= $language->get('text_update_link') . "\n";
                    $message .= $order_info['store_url'] . 'account/order/info&order_id=' . $order_id . "\n\n";
                }

                if ($comment) {
                    $message .= $language->get('text_update_comment') . "\n\n";
                    $message .= $comment . "\n\n";
                }

                $message .= $language->get('text_update_footer');

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                $mail->setTo($order_info['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($order_info['store_name']);
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            }
        }
    }
    */
}
