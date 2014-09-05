<?php
namespace Sumo;
class ControllerAccountOrder extends Controller
{
    private $error = array();

    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->load->model('account/order');

        /*if (isset($this->request->get['order_id'])) {
            $orderInfo = $this->model_account_order->getOrder($this->request->get['order_id']);

            if ($orderInfo) {
                $order_products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

                foreach ($order_products as $order_product) {
                    $option_data = array();

                    $order_options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);

                    foreach ($order_options as $order_option) {
                        if ($order_option['type'] == 'select' || $order_option['type'] == 'radio') {
                            $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                        } elseif ($order_option['type'] == 'checkbox') {
                            $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
                        } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
                            $option_data[$order_option['product_option_id']] = $order_option['value'];
                        } elseif ($order_option['type'] == 'file') {
                            $option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
                        }
                    }

                    $this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->get['order_id']);

                    $this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
                }

                $this->redirect($this->url->link('checkout/cart'));
            }
        }*/

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_ORDER_TITLE'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),

        );

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_ORDER_TITLE'),
            'href'      => $this->url->link('account/order', $url, 'SSL'),

        );

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['orders'] = array();

        $totalOrders = $this->model_account_order->getTotalOrders();
        $results     = $this->model_account_order->getOrders(($page - 1) * 10, 10);

        foreach ($results as $result) {
            $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);

            $this->data['orders'][] = array(
                'order_id'   => str_pad($result['order_id'], 6, 0, STR_PAD_LEFT),
                'name'       => $result['customer']['firstname'] . ' ' . $result['customer']['lastname'],
                'status'     => !empty($result['status']) ? $result['status'] : Language::getVar('SUMO_UNKNOWN'),
                'order_date' => Formatter::date($result['order_date']),
                'products'   => $product_total,
                'total'      => $result['total'],
                'view'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL'),
                'reorder'    => $this->url->link('account/order', 'order_id=' . $result['order_id'], 'SSL')
            );
        }

        $pagination = new Pagination();
        $pagination->total = $totalOrders;
        $pagination->page  = $page;
        $pagination->limit = 10;
        $pagination->url   = $this->url->link('account/order', 'page={page}', 'SSL');

        $this->data = array_merge($this->data, array(
            'pagination'   => $pagination->render(),
            'continue'     => $this->url->link('account/account', '', 'SSL')
        ));

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/order/list.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function info()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $orderID, 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $orderID = 0;
        if (isset($this->request->get['order_id'])) {
            $orderID = $this->request->get['order_id'];
        }
        $this->load->model('account/order');

        $orderInfo = $this->model_account_order->getOrder($orderID);

        if ($orderInfo) {
            $this->document->setTitle(Language::getVar('SUMO_NOUN_ORDER'));

            $this->data['breadcrumbs'] = array();
            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_NOUN_HOME'),
                'href'      => $this->url->link('common/home'),
                'separator' => false
            );
            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
                'href'      => $this->url->link('account/account', '', 'SSL'),
            );

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_ACCOUNT_ORDER_TITLE'),
                'href'      => $this->url->link('account/order', $url, 'SSL'),

            );
            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_NOUN_ORDER'),
                'href'      => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, 'SSL'),

            );

            // ** NOTICE **
            // This data is also fetched in model/checkout/order.php :: updateStatus
            // to create the order 'table'. If anything is changed here,
            // check there as well to be consistent.

            // Grab order totals
            foreach ($this->model_account_order->getOrderTotals($orderID) as $total) {
                if (!empty($total['label_inject'])) {
                    $label = sprintf(Language::getVar($total['label'] . '_INJ'), $total['label_inject']);
                }
                else {
                    $label = Language::getVar($total['label']);
                }

                $this->data['totals'][] = array_merge($total, array(
                    // Add percentage or something to the total-label
                    'label'         => $label
                ));
            }

            // Grab order products
            foreach ($this->model_account_order->getOrderProducts($orderID) as $product) {
                $price = $product['price'] * (1 + $product['tax_percentage'] / 100);

                $this->data['products'][] = array_merge($product, array(
                    'price'         => Formatter::currency($price),
                    'total'         => Formatter::currency($price * $product['quantity']),
                    'return'        => $this->url->link('account/return/insert', 'order_id=' . $orderInfo['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
                ));
            }

            // Grab history
            foreach ($this->model_account_order->getOrderHistories($orderID) as $history) {
                $this->data['histories'][] = array_merge($history, array(
                    'date_added'    => Formatter::date($history['history_date'])
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

            // Remove remaining vars and excessive line breaks
            $shippingAddress = preg_replace("/\{([a-z0-9_\-]+)\}/", '', $shippingAddress);
            $shippingAddress = preg_replace("/[\r\n]+/", "\n", $shippingAddress);

            // 2. Payment
            $paymentAddress = str_replace('{address_1}', '{address_1} {number}{addon}', $orderInfo['customer']['payment_address']['address_format']);

            foreach ($orderInfo['customer']['payment_address'] as $key => $value) {
                $paymentAddress = str_replace('{' . $key . '}', $value, $paymentAddress);
            }

            // Remove remaining vars and excessive line breaks
            $paymentAddress = preg_replace("/\{([a-z0-9_\-]+)\}/", '', $paymentAddress);
            $paymentAddress = preg_replace("/[\r\n]+/", "\n", $paymentAddress);

            $this->data = array_merge($this->data, array(
                'order_date'        => Formatter::date($orderInfo['order_date']),
                'invoice_no'        => isset($orderInfo['invoice_no']) ? $orderInfo['invoice_no'] : '&mdash;',
                'order_id'          => str_pad($orderInfo['order_id'], 6, 0, STR_PAD_LEFT),
                'payment_method'    => $orderInfo['payment']['name'],
                'payment_address'   => $paymentAddress,
                'shipping_method'   => $orderInfo['shipping']['name'],
                'shipping_address'  => $shippingAddress,
                'continue'          => $this->url->link('account/order', '', 'SSL')
            ));

            $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
            if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
            }
            $this->template = 'account/order/view.tpl';
            $this->children = array(
                'common/footer',
                'common/header'
            );
            $this->response->setOutput($this->render());
        } else {
            $this->document->setTitle(Language::getVar('SUMO_NOUN_ORDER'));

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_NOUN_HOME'),
                'href'      => $this->url->link('common/home'),
                'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
                'href'      => $this->url->link('account/account', '', 'SSL'),

            );

            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_ACCOUNT_ORDER_TITLE'),
                'href'      => $this->url->link('account/order', '', 'SSL'),

            );

            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_NOUN_ORDER'),
                'href'      => $this->url->link('account/order/info', 'order_id=' . $orderID, 'SSL'),

            );

            $this->data['continue'] = $this->url->link('account/order', '', 'SSL');

            $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
            if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
            }
            $this->template = 'account/order/view.tpl';
            $this->children = array(
                'common/footer',
                'common/header'
            );
            $this->response->setOutput($this->render());

        }
    }
}
