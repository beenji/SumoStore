<?php
namespace Shippingfree;
use App;
use Sumo;

class ControllerShippingfreeCheckout extends App\Controller
{
    public function shipping()
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingfree_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {
            if ($this->config->get('is_admin')) {
                return array('shippingfree' => array(
                    'rate'      => '',
                    'tax'       => '0'
                ));
            }
            else {
                if (!$this->cart->hasShipping()) {
                    return;
                }
                $zones = $this->session->data['shipping']['geo_zone'];
                $rates = array();
                foreach ($zones as $zoneList) {
                    if (isset($settings['zone'][$zoneList['geo_zone_id']]) && !empty($settings['zone'][$zoneList['geo_zone_id']]['rate'])) {
                        $setting = $settings['zone'][$zoneList['geo_zone_id']];
                        $setting['rate'] += ($setting['rate'] / 100 * $setting['tax']);
                        if ($this->cart->getTotal() >= $setting['rate']) {
                            return array('shippingfree' => array(
                                'rate'      => '',
                                'tax'       => '0',
                            ));
                        }
                    }
                }
                if (!empty($settings['general']['rate']) && $this->cart->getTotal() >= $settings['general']['rate'] + ($settings['general']['rate'] / 100 * $settings['general']['tax'])) {
                    return array('shippingfree' => array(
                        'rate'      => '0.00',
                        'tax'       => '0',
                    ));
                }
            }
        }
    }
}
