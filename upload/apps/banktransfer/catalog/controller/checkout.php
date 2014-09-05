<?php
namespace Banktransfer;
use App;
use Sumo;

class ControllerBanktransferCheckout extends App\Controller
{
    public function payment()
    {
        $this->load->appModel('Settings');
        $settings = $this->banktransfer_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {
            if ($this->config->get('is_admin')) {
                $this->load->model('localisation/zone');
                $zones = $this->model_localisation_zone->getCountryToGeoZone($this->request->get['country_id']);

                $data = array();
                if (!empty($settings['general'])) {
                    if (strlen($settings['general']['extra']) > 0) {
                        $data = array(
                            'rate'      => $settings['general']['extra'],
                            'tax'       => $settings['general']['tax']
                        );
                    }
                }

                foreach ($zones as $zone) {
                    if (isset($settings['zone'][$zone['geo_zone_id']]) && $settings['zone'][$zone['geo_zone_id']] == 'on' || $settings['zone'][$zone['geo_zone_id']] && strlen($settings['zone'][$zone['geo_zone_id']]['extra']) >0) {
                        if (isset($data['rate']) && $data['rate'] > $settings['zone'][$zone['geo_zone_id']]['extra'] || !isset($data['rate'])) {
                            $data = array(
                                'rate'      => $settings['zone'][$zone['geo_zone_id']]['extra'],
                                'tax'       => $settings['zone'][$zone['geo_zone_id']]['tax'],
                            );
                        }
                    }
                }

                if (count($data)) {
                    return array('banktransfer' => $data);
                }
            }
            else {
                $data = array();
                if (isset($settings['general'])) {
                    if (strlen($settings['general']['extra']) > 0 && ($this->config->get('is_admin') || $this->cart->getTotal() >= $settings['general']['rate'])) {
                        $data = array(
                            'rate'      => $settings['general']['extra'],
                            'tax'       => $settings['general']['tax']
                        );
                    }
                }
                foreach ($this->session->data['payment']['geo_zone'] as $zone) {
                    $settingsZone = $settings['zone'][$zone['geo_zone_id']];
                    if (!empty($settingsZone)) {
                        if (strlen($settingsZone['extra']) && strlen($settingsZone['rate'])) {
                            if (empty($settingsZone['rate']) || $this->cart->getTotal() >= $settingsZone['rate']) {
                                if (!isset($data['extra']) || $data['rate'] > $settingsZone['extra']) {
                                    $data = array(
                                        'rate'      => $settingsZone['extra'],
                                        'tax'       => $settingsZone['tax'],
                                    );
                                }
                            }
                        }
                    }
                }

                if (count($data)) {
                    if (!empty($data['rate']) && $data['rate'] > 0.00) {
                        $data['price'] = $data['rate'] + ($data['rate'] / 100 * $data['tax']);
                        $data['rate'] = Sumo\Formatter::currency($data['price']);
                    }
                    else {
                        unset($data['rate']);
                    }
                    return array('banktransfer' => $data);
                }
            }
        }
    }

    public function checkout()
    {
        $this->load->appModel('Settings');
        $settings = $this->banktransfer_model_settings->getSettings($this->config->get('store_id'));
        if (isset($settings['instructions'][$this->config->get('language_id')])) {
            $this->data['instructions'] = $settings['instructions'][$this->config->get('language_id')];
        }
        else {
            $this->data['instructions'] = reset($settings['instructions']);
        }
        //$this->session->
        /*
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
        */
        $this->template = 'checkout.tpl';
        $this->response->setOutput($this->render());
    }

    public function banktransfer()
    {
        $this->load->model('checkout/order');
        $this->load->appModel('Settings');
        $settings = $this->banktransfer_model_settings->getSettings($this->config->get('store_id'));
        $instructions = $settings['instructions'];
        if (isset($instructions[$this->config->get('language_id')])) {
            $instructions = $instructions[$this->config->get('language_id')];
        }
        else {
            $instructions = reset($instructions);
        }
        $instructions = nl2br(html_entity_decode($instructions));

        if (!empty($settings['payment']['status'])) {
            $this->model_checkout_order->updateStatus($this->session->data['order_id'], $settings['payment']['status'], nl2br($instructions));
        }

        $this->session->data['success'] = Sumo\Language::getVar('SUMO_CHECKOUT_SUCCESS_MESSAGE') . '<br />' . $instructions;
        $data = json_encode(array('success' => true, 'location' => $this->url->link('checkout/success')));
        $this->response->setOutput($data);
    }
}
