<?php
namespace sumo;
class ControllerCheckoutCheckout extends Controller
{
    public function index()
    {
        if (!$this->config->get('guest_checkout') && !$this->customer->isLogged()) {
            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('checkout_stock_empty'))) {
            $this->redirect($this->url->link('checkout/cart'));
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->redirect($this->url->link('checkout/cart'));
            }
        }

        $this->document->setTitle(Language::getVar('SUMO_CHECKOUT_TITLE'));

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_CART_TITLE'),
            'href'      => $this->url->link('checkout/cart'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_CHECKOUT_TITLE'),
            'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
        );

        $this->data['logged'] = $this->customer->isLogged();
        $this->data['shipping_required'] = $this->cart->hasShipping();

        $this->template = 'checkout/checkout.tpl';

        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function progress()
    {
        parse_str(str_replace('&amp;', '&', $this->request->post['data']), $data);

        $this->session->data['customer']['customer_id'] = $this->customer->getId();
        foreach (array('shipping', 'payment') as $type) {
            if (isset($data['customer'][$type . '_address'])) {
                $this->load->model('account/address');
                $this->load->model('localisation/zone');
                if (empty($data['customer'][$type . '_address']['firstname']) && !empty($data['customer'][$type . '_address']['address_id'])) {
                    $address = $this->model_account_address->getAddress($data['customer'][$type . '_address']['address_id'], $this->customer->getId());
                    foreach ($address as $key => $value) {
                        //if (isset($data['customer'][$type . '_address'][$key])) {
                            $data['customer'][$type . '_address'][$key] = $value;
                        //}
                    }
                    $zone = $this->model_localisation_zone->getCountryToGeoZone($address['country_id']);
                    $data[$type . '_geo_zone_id'] = $zone;
                    $this->session->data[$type]['geo_zone'] = $zone;
                }
                else {

                }
                $this->session->data[$type . '_address'] = $data['customer'][$type . '_address'];
            }

            if (!empty($data[$type . '_method'])) {
                $method = explode('.', $data[$type . '_method']);

                $this->session->data[$type . '_method'] = array(
                    'app'   => $this->session->data[$type . '_methods'][$method[0]],
                    'option'=> $method[1]
                );
                $this->session->data[$type]['method'] = $data[$type . '_method'];
            }
        }
        if (!empty($data['comment'])) {
            $this->session->data['comment'] = strip_tags($data['comment']);
        }
        if (!empty($data['discount'])) {
            if (!empty($data['discount']['coupon'])) {
                $this->load->model('checkout/coupon');
                $coupon = $this->model_checkout_coupon->check($data['discount']['coupon']);
                if (isset($coupon['discount'])) {
                    $this->session->data['discount']['coupon'] = $coupon;
                }
            }

            if (!empty($data['discount']['voucher'])) {
                $this->load->model('checkout/voucher');
                $voucher = $this->model_checkout_voucher->check($data['discount']['voucher']);
                if (isset($voucher['amount'])) {
                    $this->session->data['discount']['voucher'] = $voucher;
                }
            }

            if (!empty($data['discount']['reward']) && $data['discount']['reward'] <= $this->customer->getRewardPoints()) {
                $this->session->data['discount']['reward'] = $data['discount']['reward'];
            }
        }
        if (defined('DEVELOPMENT')) {
            $data['session_data'] = $this->session->data;
        }
        $this->response->setOutput(json_encode($data));
    }

    public function address()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('account/address');
            $this->load->model('account/customer_group');
            $this->load->model('localisation/country');

            if (empty($this->session->data['pc'])) {
                $this->session->data['pc'] = md5(microtime(true) . $this->request->server['REMOTE_ADDR'] . rand());
            }

            $type = $this->request->post['type'];
            if (!in_array($type, array('shipping', 'payment'))) {
                $type = 'payment';
            }

            $this->data['type'] = $type;
            if (isset($this->session->data['payment_address_id'])) {
                $this->data['address_id'] = $this->session->data['payment_address_id'];
            }
            else {
                $this->data['address_id'] = $this->customer->getAddressId();
            }

            $this->data['addresses']    = $this->model_account_address->getAddresses();
            $this->data['group_info']   = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());
            $this->data['countries']    = $this->model_localisation_country->getCountries();

            $this->template = 'checkout/address.tpl';
            $this->response->setOutput($this->render());
        }
        else {
            $this->response->setOutput('invalid_request_method');
        }
    }

    public function discount()
    {

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $this->session->data['force_step'] = 4;
            $this->template = 'checkout/discount.tpl';
            $this->response->setOutput($this->render());
        }
        else {
            $this->response->setOutput('invalid_request_method');
        }
    }

    public function method()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $type = $this->request->post['type'];
            if (!in_array($type, array('payment', 'shipping'))) {
                $type = 'shipping';
            }
            $this->data['info'] = Language::getVar('SUMO_CATALOG_' . strtoupper($type) . '_METHODS');
            $this->data['type'] = $type;

            $apps = Apps::getAvailable($this->config->get('store_id'), $type == 'shipping' ? 1  : 2);

            // Unset old methods
            unset($this->session->data[$type . '_methods']);

            foreach ($apps as $list) {
                $file = DIR_HOME . 'apps/' . $list['list_name'] . '/catalog/controller/checkout.php';
                if (file_exists($file)) {
                    include($file);
                    $class = ucfirst($list['list_name']) . '\Controller' . ucfirst($list['list_name']) . 'Checkout';
                    $class = new $class($this->registry);
                    $list['options'] = $class->$type();
                }
                $this->session->data[$type . '_methods'][$list['list_name']] = $list;
            }

            $this->data['warning'] = '';
            if (empty($this->session->data[$type . '_methods'])) {
                $this->data['warning'] = Language::getVar('SUMO_NOUN_' . strtoupper($type) . '_ERROR_NONE_AVAILABLE', $this->url->link('information/contact', '', 'SSL'));
            }

            if (isset($this->session->data[$type . '_methods'])) {
                $this->data['methods'] = $this->session->data[$type . '_methods'];
            }

            if ($type == 'shipping') {
                $this->session->data['force_step'] = 5;
            }
            else {
                $this->session->data['force_step'] = 6;
            }

            $this->template = 'checkout/method.tpl';
            $this->response->setOutput($this->render());
        }
        else {
            $this->response->setOutput(json_encode(array()));
        }
    }

    public function addresscheck()
    {
        $json = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!empty($this->request->post['address_id'])) {
                $this->load->model('account/address');
                $check = $this->model_account_address->getAddress($this->request->post['address_id'], $this->customer->getId());
                if ($check && count($check)) {
                    $json['ok'] = true;
                }
                else {
                    $json['message'] = Language::getVar('SUMO_NOUN_ERROR_ADDRESS');
                }
            }
            else {
                $this->load->model('account/customer_group');
                parse_str(str_replace('&amp;', '&', $this->request->post['address']), $address);
                foreach ($address['customer'] as $type => $data) {

                    $customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());

                    if ($customer_group_info) {
                        if ($customer_group_info['company_id_display'] && $customer_group_info['company_id_required'] && empty($data['company_id'])) {
                            $json['message'] = Language::getVar('SUMO_NOUN_ERROR_COMPANY');
                        }
                        if ($customer_group_info['tax_id_display'] && $customer_group_info['tax_id_required'] && empty($data['tax_id'])) {
                            $json['message'] = Language::getVar('SUMO_NOUN_ERROR_TAX');
                        }
                    }
                    foreach ($data as $key => $value) {
                        if (!empty($json['message'])) {
                            continue;
                        }
                        switch ($key) {
                            case 'firstname':
                            case 'lastname':
                                if (strlen($value) <= 1 || strlen($value) >= 32) {
                                    $json['message'] = Language::getVar('SUMO_ERROR_' . strtoupper($key));
                                }
                                break;

                            case 'postcode':
                                if (strlen($value) <= 2 || strlen($value) >= 10) {
                                    $json['message'] = Language::getVar('SUMO_NOUN_ERROR_POSTAL_CODE');
                                }
                                break;

                            case 'address_1':
                                $error = false;
                                $number = preg_replace('/[^0-9]/', '', $value);
                                if (strlen($value) <= 3) {
                                    $error = true;
                                }
                                else if (strlen($number) == 0) {
                                    //$error = true;
                                }
                                else if (strlen($value) >= 128) {
                                    $error = true;
                                }

                                if ($error) {
                                    $json['message'] = Language::getVar('SUMO_NOUN_ERROR_ADDRESS');
                                }
                                break;

                            case 'city':
                                if (strlen($value) <= 2 || strlen($value) >= 40) {
                                    $json['message'] = Language::getVar('SUMO_NOUN_ERROR_CITY');
                                }
                                break;

                            case 'country_id':
                                $this->load->model('localisation/country');
                                $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
                                if (!count($country_info)) {
                                    $json['message'] = Language::getVar('SUMO_NOUN_ERROR_COUNTRY');
                                }
                                break;

                            case 'zone_id':
                                break;
                        }
                    }
                }
                if (!isset($json['message'])) {
                    $json['ok'] = true;
                }
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function discountcheck()
    {
        $json = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!empty($this->request->post['type'])) {
                switch ($this->request->post['type']) {
                    case 'coupon':
                        $this->load->model('checkout/coupon');
                        $json['coupon'] = $this->model_checkout_coupon->check($this->request->post['code']);
                        if (isset($json['coupon']['discount'])) {
                            if ($json['coupon']['type'] == 'P') {
                                $json['coupon']['display'] = round($json['coupon']['discount']) . '%';
                            }
                            else {
                                if ($this->config->get('tax_enabled')) {
                                    $json['coupon']['display'] = Formatter::currency($json['coupon']['discount'] + ($json['coupon']['discount'] / 100 * $json['coupon']['tax_percentage']));
                                }
                                else {
                                    $json['coupon']['display'] = Formatter::currency($json['coupon']['discount']);
                                }
                            }
                            $json['coupon']['display'] = Language::getVar('SUMO_CATALOG_COUPON_DISCOUNT', $json['coupon']['display']);
                        }
                        break;

                    case 'voucher':
                        $this->load->model('checkout/voucher');
                        $json['voucher'] = $this->model_checkout_voucher->check($this->request->post['code']);
                        if (isset($json['voucher']['amount'])) {
                            $json['voucher']['display'] = Language::getVar('SUMO_CATALOG_VOUCHER_DISCOUNT', array($json['voucher']['to_name'], $json['voucher']['from_name'], $json['voucher']['theme'], Formatter::currency($json['voucher']['amount']), htmlentities($json['voucher']['message'])));
                        }
                        break;

                    case 'reward':
                        if (!empty($this->request->post['amount'])) {
                            $json['reward']['display'] = Language::getVar('SUMO_CHECKOUT_REWARD_CALCULATION', array(intval($this->request->post['amount']), Formatter::currency($this->config->get('points_value')), Formatter::currency($this->config->get('points_value') * intval($this->request->post['amount']))));
                        }
                        break;
                }
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function confirmcheck()
    {
        if (empty($this->session->data['payment_address'])) {
            $step = 2;
        }
        else if (empty($this->session->data['shipping_address'])) {
            $step = 3;
        }
        else if (empty($this->session->data['shipping_method']['app']) || empty($this->session->data['shipping_method']['option'])) {
            $step = 5;
        }
        else if (empty($this->session->data['payment_method']['app']) || empty($this->session->data['payment_method']['option'])) {
            $step = 6;
        }
        else {
            $step = 7;
        }

        $this->session->data['confirm_check'] = $step;
        $this->response->setOutput(json_encode(array('step' => $step)));

    }

    public function confirm()
    {
        if (!isset($this->session->data['confirm_check'])) {
            $this->response->setOutput(json_encode(array()));
        }
        else if ($this->session->data['confirm_check'] != 7) {
            $this->response->setOutput(json_encode(array('step' => $this->session->data['confirm_check'])));
        }
        else {
            if ($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post['agree'])) {
                $this->load->model('account/customer');
                $this->load->model('account/address');
                $this->load->model('checkout/order');
                $data       = array();
                $check      = true;
                $message    = '';

                $data['order_status']               = 1;
                $data['customer']                   = $this->model_account_customer->getCustomer($this->customer->getId());
                $data['customer']['payment_address']= $this->session->data['payment_address'];
                $data['customer']['shipping_address']= $this->session->data['shipping_address'];
                $data['shipping_method']            = $this->session->data['shipping_method']['app'];
                $data['shipping_method']['option']  = $this->session->data['shipping_method']['option'];
                $data['payment_method']             = $this->session->data['payment_method']['app'];
                $data['payment_method']['option']   = $this->session->data['payment_method']['option'];
                $data['products']                   = $this->cart->getProducts();
                $data['comment']                    = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
                $data['totals']                     = $this->session->data['totals'];
                $data['discount']                   = $this->session->data['discount'];

                if ($check) {
                    //exit('[' . 'app/' . $data['payment_method']['list_name'] . '/checkout/' . $data['payment_method']['option'] . '] >> ' . print_r($data,true));
                    if (empty($this->session->data['order_id'])) {
                        $order_id = $this->model_checkout_order->add($data);
                        $this->session->data['order_id'] = $order_id;
                    }
                    else {
                        $this->model_checkout_order->update($this->session->data['order_id'], $data);
                    }

                    // Fetch JSON result from payment app to determine next step(s)
                    return $this->getChild('app/' . $data['payment_method']['list_name'] . '/checkout/' . $data['payment_method']['option']);
                }
                else {
                    $this->response->setOutput(json_encode(array('message' => $message)));
                }
            }
            else {
                $this->template = 'checkout/confirm.tpl';
                $this->response->setOutput($this->render());
            }
        }
    }

    public function country()
    {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id'        => $country_info['country_id'],
                'name'              => $country_info['name'],
                'iso_code_2'        => $country_info['iso_code_2'],
                'iso_code_3'        => $country_info['iso_code_3'],
                'address_format'    => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status'            => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    public function fetchzones()
    {
        $json = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!empty($this->request->post['country_id'])) {
                $this->load->model('localisation/zone');
                $json['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->request->post['country_id']);
            }
        }
        $this->response->setOutput(json_encode($json));
    }
}
