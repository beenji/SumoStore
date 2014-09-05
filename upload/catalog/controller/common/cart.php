<?php
namespace Sumo;
class ControllerCommonCart extends Controller
{
    public function index()
    {
        if (isset($this->request->get['remove'])) {
            $this->cart->remove($this->request->get['remove']);

            unset($this->session->data['vouchers'][$this->request->get['remove']]);
        }

        // Totals
        $this->load->model('setting/extension');

        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        // Display prices
        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }

                $sort_order = array();

                foreach ($total_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $total_data);
            }
        }

        $this->data['totals'] = $total_data;

        $this->data['heading_title'] = Language::getVar('SUMO_NOUN_SHOPPING_CART');

        $items = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
        if ($items) {
            if ($items > 1) {
                $text_items = Language::getVar('SUMO_CHECKOUT_CART_ITEMS_PLURAL', $items);
            }
            else {
                $text_items = Language::getVar('SUMO_CHECKOUT_CART_ITEMS_SINGULAR');
            }
        }
        else {
            $text_items = Language::getVar('SUMO_CHECKOUT_CART_ITEMS_NONE');
        }
        $text_items .= ' - ' . $this->currency->format($total);

        $this->data['text_items'] = $text_items;
        $this->data['text_empty'] = Language::getVar('SUMO_CHECKOUT_CART_EMPTY', strtolower(Language::getVar('SUMO_NOUN_SHOPPING_CART')));
        $this->data['text_cart'] = Language::getVar('SUMO_CHECKOUT_CART_VIEW');
        $this->data['text_checkout'] = Language::getVar('SUMO_NOUN_CHECKOUT');

        $this->data['button_remove'] = Language::getVar('BUTTON_REMOVE');

        $this->load->model('tool/image');

        $this->data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }

            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $filename = $this->encryption->decrypt($option['option_value']);

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type'  => $option['type']
                );
            }

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $price = false;
            }

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
            } else {
                $total = false;
            }

            $this->data['products'][] = array(
                'key'      => $product['key'],
                'thumb'    => $image,
                'name'     => $product['name'],
                'model'    => $product['model'],
                'option'   => $option_data,
                'quantity' => $product['quantity'],
                'price'    => $price,
                'total'    => $total,
                'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        // Gift Voucher
        $this->data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $this->data['vouchers'][] = array(
                    'key'         => $key,
                    'description' => $voucher['description'],
                    'amount'      => $this->currency->format($voucher['amount'])
                );
            }
        }

        $this->data['cart'] = $this->url->link('checkout/cart');

        $this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

        $this->template = 'common/cart.tpl';

        $this->response->setOutput($this->render());
    }
}
