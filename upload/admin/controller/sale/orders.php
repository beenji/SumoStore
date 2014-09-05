<?php
namespace Sumo;
class ControllerSaleOrders extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALE_ORDER'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALE_ORDER'),
        ));

        $this->load->model('sale/orders');

        $filters = array();

        $page = 1;
        if (!empty($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        }

        $total = $this->model_sale_orders->getOrdersTotal();

        $filters['start'] = ($page - 1) * 25;
        $filters['limit'] = 25;
        $this->data['orders'] = $this->model_sale_orders->getOrders($filters);

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('sale/orders', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->template = 'sale/orders/list.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function generate_invoice()
    {
        $this->load->model('sale/orders');

        $orderID   = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        $invoiceID = $this->model_sale_orders->generateInvoice($orderID);

        if ($invoiceID) {
            // Redirect to invoice
            $this->redirect($this->url->link('sale/invoice/view', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoiceID));
        } else {
            // Redirect to overview
            $this->redirect($this->url->link('sale/orders'));
        }
    }

    public function info()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALE_ORDER_INFO'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALE_ORDER'),
            'href'      => $this->url->link('sale/orders', 'token=' . $this->session->data['token']),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALE_ORDER_INFO')
        ));

        // Load required models
        $this->load->model('sale/orders');
        $this->load->model('sale/customer');
        $this->load->model('sale/customer_group');
        $this->load->model('localisation/order_status');

        $orderID   = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        $orderData = $this->model_sale_orders->getOrder($orderID);

        // Is this even a valid order?
        if (!$orderData) {
            $this->redirect($this->url->link('sale/orders', '', 'SSL'));
        }

        // Assemble status list
        $statuses = $this->model_localisation_order_status->getOrderStatuses();
        foreach ($statuses as $status) {
            $this->data['order_statuses'][$status['order_status_id']] = $status;
        }

        // Customer group present?
        if (isset($orderData['customer']['customer_group_id'])) {
            $customerGroup = $this->model_sale_customer_group->getCustomerGroup($orderData['customer']['customer_group_id']);

            // Add the name of the customer group for in the template
            $orderData['customer']['customer_group_name'] = $customerGroup['name'];
        }

        /**
        * Parse address info
        */

        // 1. Shipping
        $shippingAddress = str_replace('{address_1}', '{address_1} {number}{addon}', $orderData['customer']['shipping_address']['address_format']);

        foreach ($orderData['customer']['shipping_address'] as $key => $value) {
            $shippingAddress = str_replace('{' . $key . '}', $value, $shippingAddress);
        }

        // Remove remaining vars and excessive line breaks
        $shippingAddress = preg_replace("/\{([a-z0-9_\-]+)\}/", '', $shippingAddress);
        $shippingAddress = preg_replace("/[\r\n]+/", "\n", $shippingAddress);

        // 2. Payment
        $paymentAddress = str_replace('{address_1}', '{address_1} {number}{addon}', $orderData['customer']['payment_address']['address_format']);

        foreach ($orderData['customer']['payment_address'] as $key => $value) {
            $paymentAddress = str_replace('{' . $key . '}', $value, $paymentAddress);
        }

        // Remove remaining vars and excessive line breaks
        $paymentAddress = preg_replace("/\{([a-z0-9_\-]+)\}/", '', $paymentAddress);
        $paymentAddress = preg_replace("/[\r\n]+/", "\n", $paymentAddress);

        $this->data = array_merge($this->data, array(
            'order'             => $orderData,
            'shipping_address'  => $shippingAddress,
            'payment_address'   => $paymentAddress,
            'url_invoice'       => $this->url->link('sale/orders/generate_invoice') . '?token=' . $this->session->data['token'] . '&order_id=' . $orderID
        ));

        // Has invoice?
        if (isset($orderData['invoice_no'])) {
            $this->data['invoice'] = $this->url->link('sale/invoice/download') . '?token=' . $this->session->data['token'] . '&invoice_id=' . $orderData['invoice_id'];
        }

        // Add extra CSS and JS
        $this->document->addScript('//maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false');
        $this->document->addScript('view/js/pages/order_info.js');
        $this->document->addStyle('view/css/pages/order.css');

        $this->template = 'sale/orders/info.tpl';
        $this->children = array(
            'common/header', 
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    public function edit()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALE_ORDER_ADD'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALE_ORDER'),
            'href'      => $this->url->link('sale/orders', 'token=' . $this->session->data['token']),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALE_ORDER_INFO')
        ));
        $this->document->addScript('view/js/pages/order_form.js');
        $this->document->addStyle('view/css/pages/order.css');

        $this->load->model('sale/orders');
        $this->load->model('sale/customer');
        $this->load->model('sale/customer_group');
        $this->load->model('localisation/country');
        $this->load->model('localisation/currency');
        $this->load->model('localisation/order_status');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!empty($this->request->get['order_id'])) {
                $this->model_sale_orders->saveOrder($this->request->get['order_id'], $this->request->post);
            }
            else {
                $this->model_sale_orders->addOrder($this->request->post);
            }
            $this->redirect($this->url->link('sale/orders'));
        }

        $this->data['order'] = array();
        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
        $this->data['countries'] = $this->model_localisation_country->getCountries();
        $this->data['addresses'] = array();
        $this->data['currency'] = $this->model_localisation_currency->getCurrency($this->config->get('currency_id'));

        if (!empty($this->request->get['order_id'])) {
            $order = $this->model_sale_orders->getOrder($this->request->get['order_id']);
            if (!empty($order)) {
                $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALE_ORDER_EDIT'));
                $this->data['order'] = $order;
                $this->data['addresses'] = $this->model_sale_customer->getAddresses($order['customer']['customer_id']);
                
                if (!empty($order['store']['id'])) {
                    $orderCurrencyInfo = $this->model_settings_stores->getSetting($order['store']['id'], 'currency_id');
                    $this->data['currency'] = $this->model_localisation_currency->getCurrency($orderCurrencyInfo);
                }
            }
        }

        $this->template = 'sale/orders/edit.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function remove()
    {
        $this->load->model('sale/orders');
        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $orderID) {
                if (!$this->model_sale_orders->hasInvoice($orderID)) {
                    $this->model_sale_orders->remove($orderID);
                }
            }
        }
        $url = 'token=' . $this->session->data['token'];
        if (!empty($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $this->redirect($this->url->link('sale/orders', $url, 'SSL'));
    }

    public function history()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('sale/orders');
            $this->model_sale_orders->addHistory($this->request->get['order_id'], $this->request->post);
            $this->response->setOutput(json_encode(array('success' => true)));
        }
    }

    public function getAddresses()
    {
        if (!empty($this->request->get['customer_id'])) {
            $this->response->setOutput(json_encode($this->model_sale_customer->getAddresses($this->request->get['customer_id'])));
        }
    }

    public function getMethods()
    {
        $json = array('' => array('name' => Language::getVar('SUMO_NOUN_SELECT')));
        $app_id = 0;
        if (isset($this->request->get['store_id']) && !empty($this->request->get['method'])) {
            if (isset($this->request->get['order_id'])) {
                $this->load->model('sale/orders');
                $check = $this->model_sale_orders->getOrder($this->request->get['order_id']);

            }
            $apps = Apps::getAvailable($this->request->get['store_id'], $this->request->get['method']);
            $this->config->set('store_id', $this->request->get['store_id']);
            $this->config->set('is_admin', true);
            if ($this->request->get['method'] == 1) {
                $type = 'shipping';
            }
            else {
                $type = 'payment';
            }
            $app_id = $check[$type]['app']['id'];
            foreach ($apps as $list) {
                $file = DIR_HOME . 'apps/' . $list['list_name'] . '/catalog/controller/checkout.php';
                if (file_exists($file)) {
                    include($file);
                    $class = ucfirst($list['list_name']) . '\Controller' . ucfirst($list['list_name']) . 'Checkout';
                    $class = new $class($this->registry);
                    $list['options'] = $class->$type();
                }
                if (empty($list['options'])) {
                    continue;
                }
                if ($list['id'] == $app_id) {
                    $list['selected'] = true;
                }
                $json[$list['list_name']] = $list;
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function get_coupon_info()
    {
        $this->load->model('sale/coupon');

        // Get coupon info
        $couponCode = isset($this->request->get['coupon_code']) ? $this->request->get['coupon_code'] : 0;
        $amount     = isset($this->request->get['totalamount']) ? $this->request->get['totalamount'] : 0.0;
        $products   = isset($this->request->get['product']) ? $this->request->get['product'] : array();
        $orderID    = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        $customerID = isset($this->request->get['customer_id']) ? $this->request->get['customer_id'] : 0;
        $response   = array();
        $pass       = true;

        $couponInfo = $this->model_sale_coupon->getCouponByCode($couponCode);
        $couponID   = $couponInfo['coupon_id'];

        if (!empty($couponInfo)) {
            // 
            if ($couponInfo['total'] > 0 && $couponInfo['total'] > (float)$amount) {
                $pass = false;
            }
            
            if ($orderID > 0) {
                $couponHistoryForOrder = $this->model_sale_coupon->getCouponHistories($couponInfo['coupon_id'], 0, 10, $orderID);
            } else {
                $couponHistoryForOrder = array();
            }

            // Only perform these checks if the coupon hasn't been used for this order yet
            if ($pass && empty($couponHistoryForOrder)) {
                // Date limit
                if (($couponInfo['date_start'] != '0000-00-00' && strtotime($couponInfo['date_start']) > time()) || 
                    ($couponInfo['date_end'] != '0000-00-00' && strtotime($couponInfo['date_end']) < time())) {
                    $pass = false;
                }

                // Usage limit
                if ($pass && $couponInfo['uses_total'] > 0 && $couponInfo['uses_total'] <= $this->model_sale_coupon->getTotalCouponHistories($couponID)) {
                    $pass = false;
                }

                // Login required?
                if ($pass && $couponInfo['logged'] == 1 && $customerID <= 0) {
                    $pass = false;
                } 
                
                // Usage limit for customer
                if ($pass && $couponInfo['uses_customer'] > 0 && $couponInfo['uses_customer'] <= $this->model_sale_coupon->getTotalCouponHistoriesByCustomer($couponID, $customerID)) {
                    $pass = false;
                }

                // Get coupon products
                if ($pass) {
                    $couponProducts = $this->model_sale_coupon->getCouponProducts($couponID);
                    
                    if (!empty($couponProducts) && !array_intersect($couponProducts, $products)) {
                        $pass = false;
                    }
                }

                // Get coupon categories
                if ($pass) {
                    $couponCategories = $this->model_sale_coupon->getCouponCategories($couponID);

                    // Do we even need to check categories?
                    if (!empty($couponCategories)) {
                        $productCategories = array();

                        $this->load->model('catalog/product');

                        // Get all categories for the supplied products
                        foreach ($products as $productID) {
                            $productCategories = array_merge($productCategories, $this->model_catalog_product->getProductCategories($productID));
                        }

                        // Matching categories?
                        if (!array_intersect($couponCategories, $productCategories)) {
                            $pass = false;
                        }
                    }
                }
            }

            // Valid?
            if ($pass) {
                $response = $couponInfo; 
            }
        }

        $this->response->setOutput(json_encode($response));
    }
}
