<?php
namespace Shippingproduct;
use App;
use Sumo;

class ControllerShippingproductCheckout extends App\Controller
{
    public function shipping()
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingproduct_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {
            if ($this->config->get('is_admin')) {

                if (!empty($this->request->get['country_id'])) {
                    $this->load->model('localisation/geo_zone');
                    $zones = $this->model_localisation_geo_zone->getZoneToGeoZone($this->request->get['country_id']);
                    $data = $this->getBestOffer($zones);
                }
                else {

                }
                if (empty($data) || !count($data)) {
                    return;
                }

                if (!empty($this->request->get['totalquantity'])) {
                    if (!empty($data['rate'])) {
                        $data['rate'] *= $this->request->get['totalquantity'];
                    }
                }
                return array('shippingproduct' => $data);
            }
            else {
                if (!$this->cart->hasShipping()) {
                    return;
                }
                $zones = $this->session->data['shipping']['geo_zone'];
                $data = $this->getBestOffer($zones);

                if (empty($data)) {
                    return;
                }

                if ($this->cart->countProducts()) {
                    if (!empty($data['rate'])) {
                        $data['rate'] *= $this->cart->countProducts();
                        $data['price'] = $data['rate'];
                        $data['rate'] = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $data['tax']));
                    }
                    else {
                        unset($data['rate']);
                    }
                }

                return array('shippingproduct' => $data);
            }
        }
    }

    private function getBestOffer($zones)
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingproduct_model_settings->getSettings($this->config->get('store_id'));

        $possible = $prices = $return = array();
        $count = 1;
        $lowestRate = 9999999999;
        if (!empty($settings['general']['rate'])) {
            if ($lowestRate >= $settings['general']['rate']) {
                $possible = $settings['general'];
                $lowestRate = $settings['general']['rate'];
            }
        }
        foreach ($zones as $zoneList) {
            if (isset($settings['zone'][$zoneList['geo_zone_id']]) && !empty($settings['zone'][$zoneList['geo_zone_id']]['rate'])) {
                if ($lowestRate >= $settings['zone'][$zoneList['geo_zone_id']]['rate']) {
                    $possible = $settings['zone'][$zoneList['geo_zone_id']];
                    $lowestRate = $settings['zone'][$zoneList['geo_zone_id']]['rate'];
                }
            }
        }

        return $possible;

    }
}
