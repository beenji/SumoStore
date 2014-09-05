<?php
namespace Shippingpickup;
use App;
use Sumo;

class ControllerShippingpickupCheckout extends App\Controller
{
    public function shipping()
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingpickup_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {

            $data = array('rate' => '0.00', 'tax' => '0');
            if ($this->config->get('is_admin')) {
                return array('shippingpickup' => $data);
            }
            else {
                foreach ($this->session->data['shipping']['geo_zone'] as $geoList) {
                    if (isset($settings['zone'][$geoList['geo_zone_id']]) && ($settings['zone'][$geoList['geo_zone_id']]['enabled'] == 'on')) {
                        if (empty($data['price'])) {
                            $data['price'] = $data['rate'];
                        }
                        if (!empty($data['rate']) && $data['rate'] > 0.00) {
                            $data['rate'] = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $data['tax']));
                        }
                        else {
                            unset($data['rate']);
                        }
                        return array('shippingpickup' => $data);
                    }
                }
            }
        }
    }
}
