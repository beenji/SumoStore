<?php
namespace Cashondelivery;
use App;
use Sumo;

class ControllerCashondeliveryCheckout extends App\Controller
{
    public function payment()
    {

        $this->load->appModel('Settings');
        $settings = $this->cashondelivery_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {
            if ($this->config->get('is_admin')) {
                $this->load->model('localisation/zone');
                $zones = $this->model_localisation_zone->getCountryToGeoZone($this->request->get['country_id']);
                $total = $this->request->get['totalamount'];
                $data = array();
                if (!empty($settings['general'])) {
                    if (strlen($settings['general']['rate']) > 0) {
                        $minimum = 0;
                        $maximum = 100000;
                        if (!empty($settings['general']['minimum'])) {
                            $minimum = $settings['general']['minimum'] + ($settings['general']['minimum'] / 100 * $settings['general']['tax']);
                        }
                        if (!empty($settings['general']['maximum'])) {
                            $maximum = $settings['general']['maximum'] + ($settings['general']['maximum'] / 100 * $settings['general']['tax']);
                        }

                        if ($total >= $minimum && $total <= $maximum) {
                            $data = array(
                                'rate'      => $settings['general']['rate'],
                                'tax'       => $settings['general']['tax']
                            );
                        }
                    }
                }

                foreach ($zones as $zone) {
                    if (isset($settings['zone'][$zone['geo_zone_id']]) && $settings['zone'][$zone['geo_zone_id']] == 'on' || $settings['zone'][$zone['geo_zone_id']] && strlen($settings['zone'][$zone['geo_zone_id']]['rate']) >0) {
                        if (isset($data['rate']) && $data['rate'] > $settings['zone'][$zone['geo_zone_id']]['rate'] || !isset($data['rate'])) {
                            $data = array(
                                'rate'      => $settings['zone'][$zone['geo_zone_id']]['rate'],
                                'tax'       => $settings['zone'][$zone['geo_zone_id']]['tax'],
                            );
                        }
                    }
                }

                if (count($data)) {
                    if (!empty($data['rate']) && $data['rate'] > 0.00) {
                        $data['rate'] = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $data['tax']));
                    }
                    else {
                        unset($data['rate']);
                    }
                    return array('cashondelivery' => $data);
                }

                /*
                if (!empty($this->request->get['country_id']) && isset($settings['zone'][$this->request->get['country_id']])) {
                    $data = $settings['zone'][$this->request->get['country_id']];
                }
                else if (strlen($settings['general']['extra']) > 0) {
                    $data = $settings['general'];
                }

                if (!$data) {
                    return;
                }

                if (!empty($this->request->get['totalamount'])) {
                    if (!empty($data['rate'])) {
                        $total = $data['rate'];
                        if (!empty($data['tax'])) {
                            //$total += ($data['rate'] / 100 * $data['tax']);
                        }
                        if ($total >= $this->request->get['totalamount']) {
                            return array('cashondelivery' => array('rate' => $data['extra'], 'tax' => $data['tax']));
                        }
                    }
                    else {
                        return array('cashondelivery' => array('rate' => $data['extra'], 'tax' => $data['tax']));
                    }
                }
                else {
                    return array('cashondelivery' => array('rate' => $data['extra'], 'tax' => $data['tax']));
                }
                */
            }
            else
            if (isset($settings['zone'][$this->session->data['payment']['geo_zone']]) && ($settings['zone'][$this->session->data['payment']['geo_zone']] == 'on' || $settings['zone'][$this->session->data['payment']['geo_zone']]) && strlen($settings['zone'][$this->session->data['payment']['geo_zone']]['rate']) > 0 && $this->cart->getTotal() >= $settings['zone'][$this->session->data['payment']['geo_zone']]['minimum'] && $this->cart->hasShipping()) {

                $data['tax']    = $settings['zone'][$this->session->data['payment']['geo_zone']]['tax'];
                $data['price']  = $settings['zone'][$this->session->data['payment']['geo_zone']]['extra'] + ($settings['zone'][$this->session->data['payment']['geo_zone']]['extra'] / 100 * $data['tax']);
                $data['rate']   = Sumo\Formatter::currency($data['price']);
                return array('cashondelivery' => $data);
            }
            else if (isset($settings['general'])) {
                if (strlen($settings['general']['rate']) > 0 && ($this->config->get('is_admin') || $this->cart->getTotal() >= $settings['general']['minimum'] && $this->cart->hasShipping())) {
                    $data['tax']    = $settings['general']['tax'];
                    $data['price']  = $settings['general']['rate'] + ($settings['general']['rate'] / 100 * $data['tax']);
                    $data['rate']   = Sumo\Formatter::currency($data['price']);
                    return array('cashondelivery' => $data);
                }
            }
        }
    }

    public function checkout()
    {
        $this->load->appModel('Settings');
        $settings = $this->cashondelivery_model_settings->getSettings($this->config->get('store_id'));
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
    }
}
