<?php
namespace Sumo;
class ControllerCheckoutCart extends Controller
{
    private $error = array();

    public function index()
    {
        if (!isset($this->session->data['vouchers'])) {
            $this->session->data['vouchers'] = array();
        }

        // Update
        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
            $this->redirect($this->url->link('checkout/cart'));
        }

        // Remove
        if (isset($this->request->get['remove'])) {
            $this->cart->remove($this->request->get['remove']);
            unset($this->session->data['vouchers'][$this->request->get['remove']]);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
            $this->session->data['success'] = Language::getVar('SUMO_CART_PRODUCT_REMOVED');
            $this->redirect($this->url->link('checkout/cart'));
        }

        // Coupon
        if (isset($this->request->post['coupon']) && $this->validateCoupon()) {
            $this->session->data['coupon'] = $this->request->post['coupon'];
            $this->session->data['success'] = Language::getVar('SUMO_CART_COUPON_ADDED');
            $this->redirect($this->url->link('checkout/cart'));
        }

        // Voucher
        if (isset($this->request->post['voucher']) && $this->validateVoucher()) {
            $this->session->data['voucher'] = $this->request->post['voucher'];
            $this->session->data['success'] = Language::getVar('SUMO_CART_VOUCHER_ADDED');
            $this->redirect($this->url->link('checkout/cart'));
        }

        // Reward
        if (isset($this->request->post['reward']) && $this->validateReward()) {
            $this->session->data['reward'] = abs($this->request->post['reward']);
            $this->session->data['success'] = Language::getVar('SUMO_CART_REWARD_ADDED');
            $this->redirect($this->url->link('checkout/cart'));
        }

        // Shipping
        if (isset($this->request->post['shipping_method']) && $this->validateShipping()) {
            $shipping = explode('.', $this->request->post['shipping_method']);
            $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
            $this->session->data['success'] = Language::getVar('SUMO_CART_SHIPPING_ADDED');
            $this->redirect($this->url->link('checkout/cart'));
        }

        $this->document->setTitle(Language::getVar('SUMO_CART_TITLE'));

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('common/home'),
            'text'      => Language::getVar('SUMO_NOUN_HOME')
        );

        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('checkout/cart'),
            'text'      => Language::getVar('SUMO_CART_TITLE')
        );

        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
            $points = $this->customer->getRewardPoints();

            $points_total = 0;
            $stocks = array();
            $requested = array();
            foreach ($this->cart->getProducts() as $product) {
                if (empty($product['options_data'])) {
                    $stocks[$product['stock_id']] = $product['in_stock'];
                    $requested[$product['stock_id']] += $product['quantity'];
                }
            }

            $this->data['error_warning'] = '';
            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            }

            $this->data['attention'] = '';
            if ($this->config->get('customer_display_price') && !$this->customer->isLogged()) {
                $this->data['attention'] = Language::getVar('SUMO_CART_LOGIN_REQUIRED', array($this->url->link('account/login'), $this->url->link('account/login')));
            }

            $this->data['success'] = '';
            if (isset($this->session->data['success'])) {
                $this->data['success'] = $this->session->data['success'];
                unset($this->session->data['success']);
            }

            $this->data['action'] = $this->url->link('checkout/cart');
            $this->data['weight'] = '';
            if ($this->config->get('display_cart_weight')) {
                $this->data['weight'] = $this->weight->format($this->cart->getWeight());
            }

            $this->load->model('tool/image');

            $this->data['products'] = array();

            $products = $this->cart->getProducts();
            foreach ($products as $product) {
                $product_total = 0;

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total) {
                    $this->data['error_warning'] = Language::getVar('SUMO_CART_MINIMUM_REQUIRED', array($product['name'], $product['minimum']));
                }

                if ($product['image']) {
                    $image = $this->model_tool_image->resize($product['image'], $this->config->get('image_cart_width'), $this->config->get('image_cart_height'));
                }

                $stock = true;
                if (!count($product['options_data'])) {
                    if (!$product['stock']) {
                        $stock = false;
                        if (!$this->config->get('checkout_stock_empty')) {
                            $this->data['not_in_stock_stop'] = true;
                        }
                    }
                    else {
                        if ($requested[$product['stock_id']] > $stocks[$product['stock_id']]) {
                            $stock = false;
                            if (!$this->config->get('checkout_stock_empty')) {
                                $this->data['not_in_stock_stop'] = true;
                            }
                        }
                    }
                }

                $option_data = array();

                foreach ($product['options_data'] as $data) {
                    foreach ($data['options'] as $option) {
                        //echo print_r($option,true);
                        //if ($data['type'] != 'file') {
                            $value = $option['name'];
                        //}
                        //else {
                            //$filename = $this->encryption->decrypt($option['option_value']);
                            //$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                        //}
                        if (!$option['quantity']) {
                            $stock = false;
                            if (!$this->config->get('checkout_stock_empty')) {
                                $this->data['not_in_stock_stop'] = true;
                            }
                        }
                        else {
                            if ($product['quantity'] > $option['quantity']) {
                                $stock = false;
                                if (!$this->config->get('checkout_stock_empty')) {
                                    $this->data['not_in_stock_stop'] = true;
                                }
                            }
                        }
                        $option_data[] = array(
                            'name'  => $data['name'],
                            'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                        );
                    }
                }

                // Display prices
                $price = false;
                if (($this->config->get('customer_display_price') && $this->customer->isLogged()) || !$this->config->get('customer_display_price')) {
                    $price = round($product['price'] + $product['price'] / 100 * $product['tax_percentage'], 2);
                }

                // Display prices
                $total = false;
                if (($this->config->get('customer_display_price') && $this->customer->isLogged()) || !$this->config->get('customer_display_price')) {
                    $total = $price * $product['quantity'];
                }

                $this->data['products'][] = array(
                    'key'      => $product['key'],
                    'thumb'    => $image,
                    'name'     => $product['name'],
                    'model'    => empty($product['model_2']) ? $product['model'] : $product['model_2'],
                    'option'   => $option_data,
                    'quantity' => $product['quantity'],
                    'stock'    => $stock,
                    'stock_id' => $product['stock_id'],
                    'price'    => $price,
                    'total'    => $total,
                    'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'remove'   => $this->url->link('checkout/cart', 'remove=' . $product['key'])
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $key => $voucher) {
                    $this->data['products'][] = array(
                        'key'       => $key,
                        'name'      => $voucher['description'],
                        'model'     => Language::getVar('SUMO_NOUN_VOUCHER'),
                        'price'     => $voucher['amount'],
                        'total'     => $voucher['amount'],
                        'remove'    => $this->url->link('checkout/cart', 'remove=' . $key)
                    );
                }
            }

            if (isset($this->request->post['next'])) {
                $this->data['next'] = $this->request->post['next'];
            }
            else {
                $this->data['next'] = '';
            }

            $this->data['coupon_status'] = $this->config->get('coupon_status');

            if (isset($this->request->post['coupon'])) {
                $this->data['coupon'] = $this->request->post['coupon'];
            }
            elseif (isset($this->session->data['coupon'])) {
                $this->data['coupon'] = $this->session->data['coupon'];
            }
            else {
                $this->data['coupon'] = '';
            }

            $this->data['voucher_status'] = $this->config->get('voucher_status');

            if (isset($this->request->post['voucher'])) {
                $this->data['voucher'] = $this->request->post['voucher'];
            }
            elseif (isset($this->session->data['voucher'])) {
                $this->data['voucher'] = $this->session->data['voucher'];
            }
            else {
                $this->data['voucher'] = '';
            }

            $this->data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

            if (isset($this->request->post['reward'])) {
                $this->data['reward'] = $this->request->post['reward'];
            }
            elseif (isset($this->session->data['reward'])) {
                $this->data['reward'] = $this->session->data['reward'];
            }
            else {
                $this->data['reward'] = '';
            }

            $this->data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();

            if (isset($this->request->post['country_id'])) {
                $this->data['country_id'] = $this->request->post['country_id'];
            }
            elseif (isset($this->session->data['shipping_country_id'])) {
                $this->data['country_id'] = $this->session->data['shipping_country_id'];
            }
            else {
                $this->data['country_id'] = $this->config->get('config_country_id');
            }

            $this->load->model('localisation/country');

            $this->data['countries'] = $this->model_localisation_country->getCountries();

            if (isset($this->request->post['zone_id'])) {
                $this->data['zone_id'] = $this->request->post['zone_id'];
            }
            elseif (isset($this->session->data['shipping_zone_id'])) {
                $this->data['zone_id'] = $this->session->data['shipping_zone_id'];
            }
            else {
                $this->data['zone_id'] = '';
            }

            if (isset($this->request->post['postcode'])) {
                $this->data['postcode'] = $this->request->post['postcode'];
            }
            elseif (isset($this->session->data['shipping_postcode'])) {
                $this->data['postcode'] = $this->session->data['shipping_postcode'];
            }
            else {
                $this->data['postcode'] = '';
            }

            if (isset($this->request->post['shipping_method'])) {
                $this->data['shipping_method'] = $this->request->post['shipping_method'];
            }
            elseif (isset($this->session->data['shipping_method'])) {
                $this->data['shipping_method'] = $this->session->data['shipping_method']['code'];
            }
            else {
                $this->data['shipping_method'] = '';
            }
            $this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        $this->data['continue'] = $this->url->link('common/home');


        $this->template = 'checkout/cart.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateCoupon()
    {
        $this->load->model('checkout/coupon');

        $coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

        if (!$coupon_info) {
            $this->error['warning'] = Language::getVar('SUMO_CART_ERROR_COUPON_NOT_FOUND');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateVoucher()
    {
        $this->load->model('checkout/voucher');

        $voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

        if (!$voucher_info) {
            $this->error['warning'] = Language::getVar('SUMO_CART_ERROR_VOUCHER_NOT_FOUND');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateReward()
    {
        $points = $this->customer->getRewardPoints();

        $points_total = 0;

        foreach ($this->cart->getProducts() as $product) {
            if ($product['points']) {
                $points_total += $product['points'];
            }
        }

        if (empty($this->request->post['reward'])) {
            //$this->error['warning'] = $this->language->get('error_reward');
        }

        if ($this->request->post['reward'] > $points) {
            $this->error['warning'] = Language::getVar('SUMO_CART_ERROR_REWARD_NOT_ENOUGH');
        }

        if ($this->request->post['reward'] > $points_total) {
            $this->error['warning'] = Language::getVar('SUMO_CART_ERROR_REWARD_TOO_MUCHT');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateShipping()
    {
        if (!empty($this->request->post['shipping_method'])) {
            $shipping = explode('.', $this->request->post['shipping_method']);

            if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                $this->error['warning'] = Language::getVar('SUMO_CART_ERROR_SHIPPING');
            }
        }
        else {
            $this->error['warning'] = Language::getVar('SUMO_CART_ERROR_SHIPPING');
        }

        if (!$this->error) {
            return true;
        }
        return false;

    }

    public function add()
    {
        $json = array();

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        }
        else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (isset($this->request->post['quantity'])) {
                $quantity = $this->request->post['quantity'];
            }
            else {
                $quantity = 1;
            }

            if (isset($this->request->post['option'])) {
                $option = array_filter($this->request->post['option']);
            }
            else {
                $option = array();
            }

            $product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

            foreach ($product_options as $product_option) {
                //if ($product_option['type'] == 'radio' && empty($option[$product_option['product_option_id']]) || $product_option['type'] == 'select' && empty($option[$product_option['product_option_id']])) {
                    //$json['error'][] = Language::getVar('SUMO_CART_ERROR_OPTION_REQUIRED', $product_option['name']);
                //}
                if ($product_option['type'] == 'radio' || $product_option['type'] == 'select') {
                    if (empty($option[$product_option['option_id']])) {
                        $json['error'][] = Language::getVar('SUMO_CART_ERROR_OPTION_REQUIRED', $product_option['name']);
                    }
                }
            }

            if (!$json) {
                $this->cart->add($this->request->post['product_id'], $quantity, $option);

                //$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));
                $json['success'] = Language::getVar('SUMO_CHECKOUT_CART_PRODUCT_ADD', array($this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'), strtolower(Language::getVar('SUMO_NOUN_SHOPPING_CART'))));

                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
            }
            else {
                $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'path=unknown&product_id=' . $this->request->post['product_id']));
            }
        }

        if (!empty($json['error'])) {
            $json['error'] = implode('<br />', $json['error']);
        }

        $this->response->setOutput(json_encode($json));
    }

    public function quote()
    {
        $json = array();

        if (!$this->cart->hasProducts()) {
            $json['error']['warning'] = Language::getVar('SUMO_CART_ERROR_EMPTY_CART');
        }

        if (!$this->cart->hasShipping()) {
            $json['error']['warning'] = Language::getVar('SUMO_CART_ERROR_SHIPPING');
        }

        if ($this->request->post['country_id'] == '') {
            $json['error']['country'] = Language::getVar('SUMO_ERROR_COUNTRY');
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
            $json['error']['zone'] = Language::getVar('SUMO_ERROR_ZONE');
        }

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
            $json['error']['postcode'] = Language::getVar('SUMO_ERROR_POSTAL_CODE');
        }

        if (!$json) {
            $this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

            // Default Shipping Address
            $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
            $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
            $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

            if ($country_info) {
                $country = $country_info['name'];
                $iso_code_2 = $country_info['iso_code_2'];
                $iso_code_3 = $country_info['iso_code_3'];
                $address_format = $country_info['address_format'];
            } else {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                $zone = $zone_info['name'];
                $zone_code = $zone_info['code'];
            } else {
                $zone = '';
                $zone_code = '';
            }

            $address_data = array(
                'firstname'      => '',
                'lastname'       => '',
                'company'        => '',
                'address_1'      => '',
                'address_2'      => '',
                'postcode'       => $this->request->post['postcode'],
                'city'           => '',
                'zone_id'        => $this->request->post['zone_id'],
                'zone'           => $zone,
                'zone_code'      => $zone_code,
                'country_id'     => $this->request->post['country_id'],
                'country'        => $country,
                'iso_code_2'     => $iso_code_2,
                'iso_code_3'     => $iso_code_3,
                'address_format' => $address_format
            );

            $quote_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);

                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data);

                    if ($quote) {
                        $quote_data[$result['code']] = array(
                            'title'      => $quote['title'],
                            'quote'      => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error'      => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);

            $this->session->data['shipping_methods'] = $quote_data;

            if ($this->session->data['shipping_methods']) {
                $json['shipping_method'] = $this->session->data['shipping_methods'];
            } else {
                $json['error']['warning'] = Language::getVar('SUMO_CART_ERROR_SHIPPING');
            }
        }

        $this->response->setOutput(json_encode($json));
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
}
