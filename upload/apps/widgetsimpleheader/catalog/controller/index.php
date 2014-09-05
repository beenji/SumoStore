<?php
namespace Widgetsimpleheader;
use App;
use Sumo;
class ControllerWidgetsimpleheader extends App\Controller
{
    public function index($var)
    {
        $settings = $this->config->get('header_' . $this->config->get('template'));
        $this->data['settings'] = $settings;
        $this->template = 'header.tpl';
        $this->output = $this->render();
    }

    public function get($type)
    {
        $this->template = $type . '.tpl';
        return $this->render();
    }

    public function cart()
    {
        $this->load->model('tool/image');

        $this->data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('image_cart_width'), $this->config->get('image_cart_height'));
            }
            else {
                $image = '';
            }

            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                }
                else {
                    $filename = $this->encryption->decrypt($option['option_value']);

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type'  => $option['type']
                );
            }

            $this->data['products'][] = array(
                'product_id'=> $product['product_id'],
                'key'       => $product['key'],
                'thumb'     => $image,
                'name'      => $product['name'],
                'model'     => $product['model'],
                'option'    => $option_data,
                'quantity'  => $product['quantity'],
                'price'     => $product['price'],
                'total'     => $product['total'],
                'tax'       => $product['tax_percentage'],
                'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        // Gift Voucher
        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $this->data['products'][] = array(
                    'key'       => $key,
                    'name'      => $voucher['description'],
                    'price'     => $voucher['amount'],
                    'amount'    => 1,
                    'total'     => $voucher['amount']
                );
            }
        }
        $this->template = 'cart.tpl';
        $this->response->setOutput($this->render());
    }
}
