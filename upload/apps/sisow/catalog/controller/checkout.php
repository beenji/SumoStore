<?php
namespace Sisow;
use Sumo;
use App;

class ControllerSisowCheckout extends App\Controller
{
    public function payment()
    {
        $this->load->appModel('Settings');
        $this->load->appModel('Api');
        $settings = $this->sisow_model_settings->getSettings($this->config->get('store_id'));
        try {
            $this->sisow_model_api->setMerchant($settings['merchant']['id'], $settings['merchant']['key']);
        }
        catch (\Exception $e) {
            // Empty/invalid key, return nothing
            return;
        }


        if ($this->config->get('is_admin')) {

        }
        else {
            $methods = $this->sisow_model_api->callMethods();
            if (!isset($methods['merchant']['active']) || !$methods['merchant']['active']) {
                return;
            }

            if (isset($methods['merchant']['payments']['payment'])) {
                $methods = $methods['merchant']['payments']['payment'];
            }
            else {
                return;
            }

            $total = $this->cart->getTotal();
            $check = $return = array();
            foreach ($methods as $method) {
                if (isset($settings[$method]) && isset($settings[$method]['enabled'])) {
                    $check[$method] = $settings[$method];
                }
            }

            foreach ($check as $id => $data) {
                foreach ($this->session->data['payment']['geo_zone'] as $zone) {
                    if (!empty($data['zone']) && $data['zone'] == $zone['geo_zone_id'] || empty($data['zone'])) {
                        $rate  = '';
                        $price = $data['rate'];
                        if ($data['rate_type'] == 'f') {
                            if (!empty($data['rate'])) {
                                $price = $price * (1 + ($settings['tax'] / 100));
                                $rate = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $settings['tax']));
                            }
                        }
                        else {
                            if (!empty($data['rate'])) {
                                $rate = $data['rate'] . '%';
                            }
                        }
                        
                        $return[$id] = array(
                            'price' => $price,
                            'rate'  => $rate,
                            'tax'   => $settings['tax'],
                            'name'  => '<img src="app/sisow/paylogos/' . $id . '.png" alt="' . $data['description'] . '">',
                            'description' => Sumo\Language::getVar('APP_SISOW_CATALOG_' . strtoupper($id) . '_DESCRIPTION'),
                        );
                    }
                }
            }

            if (count($return)) {
                return $return;
            }
            return;
        }
    }

    private function setupAPI()
    {
        $this->load->model('checkout/order');
        $this->load->appModel('Settings');
        $this->load->appModel('Api');
        $this->load->appModel('Payments');
        $this->settings = $this->sisow_model_settings->getSettings($this->config->get('store_id'));
        $this->sisow_model_api->setMerchant($this->settings['merchant']['id'], $this->settings['merchant']['key']);
    }

    public function ideal()
    {
        $this->setupAPI();

        $settings = $this->settings['ideal'];
        $description = $this->settings['purchaseid'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->sisow_model_api->createPayment('ideal', $this->session->data['total_amount'], $this->session->data['order_id'], $description);

        if (is_array($response) && !empty($response['transaction']['issuerurl'])) {
            $json['success'] = true;
            $json['location'] = rawurldecode($response['transaction']['issuerurl']);
            $this->sisow_model_payments->create($this->session->data['order_id'], md5($this->session->data['order_id']), $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function mistercash()
    {
        $this->setupAPI();

        $settings = $this->settings['mistercash'];
        $description = $this->settings['purchaseid'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->sisow_model_api->createPayment('mistercash', $this->session->data['total_amount'], $this->session->data['order_id'], $description);

        if (is_array($response) && !empty($response['transaction']['issuerurl'])) {
            $json['success'] = true;
            $json['location'] = rawurldecode($response['transaction']['issuerurl']);
            $this->sisow_model_payments->create($this->session->data['order_id'], md5($this->session->data['order_id']), $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function overboeking()
    {
        $this->setupAPI();

        $settings = $this->settings['overboeking'];
        $description = $this->settings['purchaseid'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->sisow_model_api->createPayment('overboeking', $this->session->data['total_amount'], $this->session->data['order_id'], $description);

        if (is_array($response) && !empty($response['transaction']['issuerurl'])) {
            $json['success'] = true;
            $json['location'] = rawurldecode($response['transaction']['issuerurl']);
            $this->sisow_model_payments->create($this->session->data['order_id'], md5($this->session->data['order_id']), $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function paypalec()
    {
        $this->setupAPI();

        $settings = $this->settings['paypalec'];
        $description = $this->settings['purchaseid'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->sisow_model_api->createPayment('paypalec', $this->session->data['total_amount'], $this->session->data['order_id'], $description);

        if (is_array($response) && !empty($response['transaction']['issuerurl'])) {
            $json['success'] = true;
            $json['location'] = rawurldecode($response['transaction']['issuerurl']);
            $this->sisow_model_payments->create($this->session->data['order_id'], md5($this->session->data['order_id']), $response);
        }
        else {
            $json['message'] = $response['error']['errormessage'];
        }
        $this->response->setOutput(json_encode($json));
    }

    public function sofort()
    {
        $this->setupAPI();

        $settings = $this->settings['sofort'];
        $description = $this->settings['purchaseid'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->sisow_model_api->createPayment('sofort', $this->session->data['total_amount'], $this->session->data['order_id'], $description);

        if (is_array($response) && !empty($response['transaction']['issuerurl'])) {
            $json['success'] = true;
            $json['location'] = rawurldecode($response['transaction']['issuerurl']);
            $this->sisow_model_payments->create($this->session->data['order_id'], md5($this->session->data['order_id']), $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function webhook()
    {
        $data = $this->updateStatus($this->request->get['trxid']);
        $this->response->setOutput(json_encode(array('welcome')));

    }

    public function webreturn()
    {
        $this->setupAPI();
        $data = $this->sisow_model_api->getStatus($this->request->get['trxid' ]);
        $data = $data['transaction'];
        $this->updateStatus(md5($this->request->get['ec']), $data);
        if ($data['status'] == 'Success' || $data['status'] == 'Pending') {
            $this->session->data['success'] = Sumo\Language::getVar('SUMO_CHECKOUT_SUCCESS_MESSAGE') . '<br />' . Sumo\Language::getVar('APP_SISOW_CATALOG_SUCCESS_' . strtoupper($data['status']));
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
        else {
            $this->session->data['error'] = Sumo\Language::getVar('APP_SISOW_CATALOG_ERROR_' . strtoupper($data['status']));
            $this->redirect($this->url->link('checkout/checkout'));
        }
        exit(print_r($this->request->server,true));
    }

    private function updateStatus($transaction_id, $data)
    {
        $this->setupAPI();

        $payment_id = $this->sisow_model_payments->getPaymentId($transaction_id);
        $this->sisow_model_payments->update($payment_id, $data);

        $order_id = $this->sisow_model_payments->getOrderId($transaction_id);
        if ($data['status'] == 'Success' || $data['status'] == 'Pending') {
            $message = Sumo\Language::getVar('APP_SISOW_CATALOG_SUCCESS_' . strtoupper($data['status']));
            $amount = $data['amount'] / 100;
            $this->model_checkout_order->addTransaction($order_id, $amount, 'via Sisow, ' . $data['description']);
        }
        else {
            $message = Sumo\Language::getVar('APP_SISOW_CATALOG_ERROR_' . strtoupper($data['status']));
        }
        $this->model_checkout_order->updateStatus($order_id, $this->settings['status'][strtolower($data['status'])], $message);

        return $data;
    }
}
