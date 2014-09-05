<?php
namespace Multisafepay;
use Sumo;
use App;

class ControllerMultisafepayCheckout extends App\Controller
{
    public function payment()
    {
        $this->load->appModel('Settings');
        $this->load->appModel('Api');
        $settings = $this->multisafepay_model_settings->getSettings($this->config->get('store_id'));
        try {
            $this->multisafepay_model_api->setSettings($settings);
        }
        catch (\Exception $e) {
            // Empty/invalid key, return nothing
            return;
        }


        if ($this->config->get('is_admin')) {

        }
        else {
            $methods = $this->multisafepay_model_api->getGateways(false);
            if (isset($methods['gateways']['gateway'])) {
                $methods = $methods['gateways']['gateway'];
            }
            else {
                return;
            }
            $total = $this->cart->getTotal();
            $check = $return = array();
            foreach ($methods as $method) {
                $method['id'] = strtolower($method['id']);
                if (isset($settings[$method['id']])) {
                    $check[$method['id']] = array_merge($settings[$method['id']], $method);
                }
            }

            foreach ($check as $id => $data) {
                foreach ($this->session->data['payment']['geo_zone'] as $zone) {
                    if (!empty($data['zone']) && $data['zone'] == $zone['geo_zone_id'] || empty($data['zone'])) {
                        $rate = '';
                        if ($data['rate_type'] == 'f') {
                            if (!empty($data['rate'])) {
                                $rate = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $settings['tax']));
                            }
                        }
                        else {
                            if (!empty($data['rate'])) {
                                $rate = $data['rate'] . '%';
                            }
                        }
                        $return[$id] = array(
                            'rate'  => $rate,
                            'tax'   => $settings['tax'],
                            'name'  => '<img src="app/multisafepay/catalog/view/img/' . $id . '.png">',
                            'description' => $data['description']
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
}
