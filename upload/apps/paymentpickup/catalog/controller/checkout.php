<?php
namespace Paymentpickup;
use App;
use Sumo;

class ControllerPaymentpickupCheckout extends App\Controller
{
    // Displayed in checkout/payment-methods
    public function payment()
    {
        $this->load->appModel('Settings');
        $settings = $this->paymentpickup_model_settings->getSettings($this->config->get('store_id'));
        if (($settings['enabled'] == 'on' || $settings['enabled']) && ($this->session->data['shipping']['method'] == 'shippingpickup.shippingpickup')) {
            $data = array('tax' => '0');
            return array('paymentpickup' => $data);
        }
    }

    // If required, display extra instructions on checkout/confirm
    // Extra note: this function shows information on the bottom right of that page, above the [confirm] button
    public function checkout()
    {
        /*
        $this->load->appModel('Settings');
        $settings = $this->paymentpickup_model_settings->getSettings($this->config->get('store_id'));
        if (isset($settings['instructions'][$this->config->get('language_id')])) {
            $this->data['instructions'] = $settings['instructions'][$this->config->get('language_id')];
        }
        else {
            $this->data['instructions'] = reset($settings['instructions']);
        }
        if ($this->request->server['REQUEST_METHOD'] == 'POST' || $this->session->data['validated_payment_app']) {
            $this->data['continue'] = $this->url->link('checkout/success', '', 'SSL');
            if (isset($this->request->post['confirm'])) {
                $this->load->model('checkout/order');
                $this->model_checkout_order->updateStatus($this->session->data['order_id'], $settings['payment']['status']);
                $this->response->setOutput('OK');
                $this->session->data['validated_payment_app'] = false;
            }
            else {
                $this->template = 'checkout.tpl';
                $this->response->setOutput($this->render());
            }
        }
        
        $this->template = 'checkout.tpl';
        $this->response->setOutput($this->render());
        */
    }

    // This function is called after the order was confirmed
    public function paymentpickup()
    {
        $this->load->model('checkout/order');
        $this->load->appModel('Settings');
        $settings = $this->paymentpickup_model_settings->getSettings($this->config->get('store_id'));
        
        if (!empty($settings['payment']['status'])) {
            $this->model_checkout_order->updateStatus($this->session->data['order_id'], $settings['payment']['status']);
        }

        $this->session->data['success'] = Sumo\Language::getVar('SUMO_CHECKOUT_SUCCESS_MESSAGE');
        $data = json_encode(array('success' => true, 'location' => $this->url->link('checkout/success')));
        $this->response->setOutput($data);
    }
}
