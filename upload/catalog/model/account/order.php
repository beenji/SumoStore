<?php
namespace Sumo;
class ModelAccountOrder extends Model
{
    public function getOrder($orderID, $customerId = null)
    {
        if ($customerId == null) {
            $customerId = (int)$this->customer->getId();
        }
        $orderInfo = $this->query("SELECT *
            FROM PREFIX_orders o, PREFIX_orders_data od
            WHERE o.order_id = od.order_id
                AND o.order_id = :orderID
                AND od.customer LIKE :customerID",
            array(
                'orderID'    => $orderID,
                'customerID' => '%"customer_id":"' . $customerId . '"%'
            ))->fetch();

        if (!empty($orderInfo)) {
            // Decode some json-data
            $orderInfo['customer'] = json_decode($orderInfo['customer'], true);
            $orderInfo['discount'] = json_decode($orderInfo['discount'], true);
            $orderInfo['shipping'] = json_decode($orderInfo['shipping'], true);
            $orderInfo['payment']  = json_decode($orderInfo['payment'], true);

            // Get invoice
            $invoiceInfo = $this->query("SELECT i.*
                FROM PREFIX_invoice i, PREFIX_orders_to_invoice oti
                WHERE i.invoice_id = oti.invoice_id
                    AND oti.order_id = :orderID", array(
                    'orderID'   => $orderInfo['order_id']
                ))->fetch();

            if (!empty($invoiceInfo)) {
                $orderInfo['invoice_no'] = $invoiceInfo['invoice_no'];
            }

            // Get address format
            $orderInfo['customer']['shipping_address']['country'] = $orderInfo['customer']['payment_address']['country'] = '';
            $orderInfo['customer']['shipping_address']['address_format'] = $orderInfo['customer']['payment_address']['address_format'] = "{firstname} {lastname}\n{address_1}\n{postcode} {city}\n{country}";

            // Shipping
            if ($orderInfo['customer']['shipping_address']['country_id'] > 0) {
                // Get country information, it holds
                // the default address format
                $countryInfo = $this->query("SELECT *
                    FROM PREFIX_country
                    WHERE country_id = :countryID", array(
                        'countryID' => $orderInfo['customer']['shipping_address']['country_id']
                    ))->fetch();

                if (!empty($countryInfo)) {
                    $orderInfo['customer']['shipping_address']['country'] = $countryInfo['name'];

                    if (!empty($countryInfo['address_format'])) {
                        $orderInfo['customer']['shipping_address']['address_format'] = $countryInfo['address_format'];
                    }
                }
            }

            // Payment
            if ($orderInfo['customer']['payment_address']['country_id'] > 0) {
                // Get country information, it holds
                // the default address format
                $countryInfo = $this->query("SELECT *
                    FROM PREFIX_country
                    WHERE country_id = :countryID", array(
                        'countryID' => $orderInfo['customer']['payment_address']['country_id']
                    ))->fetch();

                if (!empty($countryInfo)) {
                    $orderInfo['customer']['payment_address']['country'] = $countryInfo['name'];

                    if (!empty($countryInfo['address_format'])) {
                        $orderInfo['customer']['payment_address']['address_format'] = $countryInfo['address_format'];
                    }
                }
            }

            return $orderInfo;
        }
        else {
            return false;
        }
    }

    public function getOrders($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $orders = $this->query("SELECT o.*, od.*, ot.value_hr AS total, os.name AS status
            FROM PREFIX_orders o
                LEFT JOIN PREFIX_order_status os ON o.order_status = os.order_status_id AND os.language_id = :languageID,
            PREFIX_orders_data od
                LEFT JOIN PREFIX_orders_totals ot ON od.order_id = ot.order_id AND label = 'SUMO_NOUN_OT_TOTAL'
            WHERE o.order_id = od.order_id
                AND customer LIKE :customerID
            ORDER BY o.order_id DESC LIMIT :start, :limit", array(
                'customerID' => '%"customer_id":"' . (int)$this->customer->getId() . '"%',
                'languageID' => (int)$this->config->get('language_id'),
                'start'      => $start,
                'limit'      => $limit))->fetchAll();

        foreach ($orders as $i => $order) {
            $orders[$i]['customer'] = json_decode($order['customer'], true);
        }

        return $orders;
    }

    public function getOrderProducts($order_id)
    {
        return $this->query("SELECT *
            FROM PREFIX_orders_lines
            WHERE order_id = '" . (int)$order_id . "'")->fetchAll();
    }

    /* ?? public function getOrderOptions($order_id, $order_product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->rows;
    }*/

    public function getOrderTotals($order_id)
    {
        return $this->query("SELECT *
            FROM PREFIX_orders_totals
            WHERE order_id = '" . (int)$order_id . "'
            ORDER BY sort_order ASC")->fetchAll();
    }

    public function getOrderHistories($order_id)
    {
        return $this->query("SELECT oh.*, os.name AS status
            FROM PREFIX_orders_history oh
            LEFT JOIN PREFIX_order_status os ON oh.status_id = os.order_status_id AND os.language_id = :languageID
            WHERE oh.order_id = '" . (int)$order_id . "'
                AND oh.notify = '1'
            ORDER BY oh.history_date", array(
                'languageID'    => (int)$this->config->get('language_id')
            ))->fetchAll();
    }

    /* ?? public function getOrderDownloads($order_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' ORDER BY name");

        return $query->rows;
    }*/

    public function getTotalOrders()
    {
        $orderData = $this->query("SELECT COUNT(*) AS total
            FROM PREFIX_orders o, PREFIX_orders_data od
            WHERE o.order_id = od.order_id
                AND od.customer LIKE :customerID", array(
                'customerID' => '%"customer_id":"' . (int)$this->customer->getId() . '"%',
            ))->fetch();

        return $orderData['total'];
    }

    public function getTotalOrderProductsByOrderId($order_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total
            FROM PREFIX_orders_lines
            WHERE product_id > 0
                AND order_id = " . (int)$order_id)->fetch();

        return $query['total'];
    }

}
