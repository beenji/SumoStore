<?php
namespace Mollie;
use Sumo;
use App;

class ControllerMollieCheckout extends App\Controller
{
    private $settings;

    public function payment()
    {
        $this->load->appModel('Settings');
        $this->load->appModel('Api');
        $settings = $this->mollie_model_settings->getSettings($this->config->get('store_id'));
        try {
            $this->mollie_model_api->setKey($settings['general']['api_key']);
        }
        catch (\Exception $e) {
            // Empty/invalid key, return nothing
            return;
        }


        if ($this->config->get('is_admin')) {

        }
        else {
            $methods = $this->mollie_model_api->callMethods();
            $total = $this->cart->getTotal();
            $check = $return = array();
            foreach ($methods['data'] as $method) {
                if (isset($settings[$method['id']]) && isset($settings[$method['id']]['enabled'])) {
                    if ($total >= $method['amount']['minimum'] && $total <= $method['amount']['maximum']) {
                        $check[$method['id']] = array_merge($settings[$method['id']], $method);
                    }
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
                                $rate  = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $settings['tax']));
                            }
                        }
                        else {
                            if (!empty($data['rate'])) {
                                $rate = $data['rate'] . '%';
                            }
                        }
                        $return[$id] = array(
                            'rate'          => $rate,
                            'rate_type'     => $data['rate_type'],
                            'price'         => $price,
                            'tax'           => $settings['tax'],
                            'name'          => '<img src="' . $data['image']['normal'] . '">',
                            'description'   => Sumo\Language::getVar('APP_MOLLIE_CATALOG_' . strtoupper($id) . '_DESCRIPTION')
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

    public function checkout()
    {
        // [empty_output]
    }

    private function setupAPI()
    {
        $this->load->model('checkout/order');
        $this->load->appModel('Settings');
        $this->load->appModel('Api');
        $this->load->appModel('Payments');
        $this->settings = $this->mollie_model_settings->getSettings($this->config->get('store_id'));
        $this->mollie_model_api->setKey($this->settings['general']['api_key']);
    }

    public function ideal()
    {
        $this->setupAPI();

        $settings = $this->settings['ideal'];
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('ideal', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function banktransfer()
    {
        $this->setupAPI();

        $settings = $this->settings['banktransfer'];
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('banktransfer', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function paypal()
    {
        $this->setupAPI();

        $settings = $this->settings['paypal'];
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('paypal', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
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
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('mistercash', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function creditcard()
    {
        $this->setupAPI();

        $settings = $this->settings['creditcard'];
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('creditcard', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function sofort()
    {
        $this->setupAPI();

        $settings = $this->settings['sofort'];
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('sofort', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function bitcoin()
    {
        $this->setupAPI();

        $settings = $this->settings['bitcoin'];
        $description = $this->settings['instructions'];
        if (isset($description[$this->config->get('language_id')])) {
            $description = $description[$this->config->get('language_id')];
        }
        else {
            $description = reset($description);
        }

        $description = str_replace('%', str_pad($this->session->data['order_id'], 6, '0', STR_PAD_LEFT), $description);

        $json = array();

        $response = $this->mollie_model_api->createPayment('bitcoin', $this->session->data['total_amount'], $this->session->data['order_id'], $description);
        if (is_array($response) && !empty($response['links']['paymentUrl'])) {
            $json['success'] = true;
            $json['location'] = $response['links']['paymentUrl'];
            $this->mollie_model_payments->create($this->session->data['order_id'], $response['id'], $response);
        }
        else {
            $json['message'] = $response;
        }
        $this->response->setOutput(json_encode($json));
    }

    public function webhook()
    {
        $data = $this->updateStatus($this->request->post['id']);
        $this->response->setOutput(json_encode(array('welcome')));

    }

    public function webreturn()
    {
        $this->setupAPI();
        $transaction_id = $this->mollie_model_payments->getLastTransactionIdByOrder($this->session->data['order_id']);
        $data = $this->mollie_model_api->getStatus($transaction_id);
        if ($data['status'] == 'paid' || $data['status'] == 'open') {
            $this->session->data['success'] = Sumo\Language::getVar('SUMO_CHECKOUT_SUCCESS_MESSAGE') . '<br />' . Sumo\Language::getVar('APP_MOLLIE_CATALOG_SUCCESS_' . strtoupper($data['status']));
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
            $this->model_checkout_order->addTransaction($order_id, $data['amount'], 'via Mollie, ' . $data['description']);
        }
        else {
            $this->session->data['error'] = Sumo\Language::getVar('APP_MOLLIE_CATALOG_ERROR_' . strtoupper($data['status']));
            $this->redirect($this->url->link('checkout/checkout'));
        }
        exit(print_r($this->request->server,true));
    }

    private function updateStatus($transaction_id)
    {
        $this->setupAPI();
        $data = $this->mollie_model_api->getStatus($transaction_id);

        $payment_id = $this->mollie_model_payments->getPaymentId($transaction_id);
        $this->mollie_model_payments->update($transaction_id, $data);

        $order_id = $this->mollie_model_payments->getOrderId($transaction_id);
        if ($data['status'] == 'paid' || $data['status'] == 'open') {
            $message = Sumo\Language::getVar('APP_MOLLIE_CATALOG_SUCCESS_' . strtoupper($data['status']));
            $this->model_checkout_order->addTransaction($order_id, $data['amount'], 'via Mollie, ' . $data['description']);
        }
        else {
            $message = Sumo\Language::getVar('APP_MOLLIE_CATALOG_ERROR_' . strtoupper($data['status']));
        }
        $this->model_checkout_order->updateStatus($order_id, $this->settings['status'][strtolower($data['status'])], $message);

        return $data;
    }
}
