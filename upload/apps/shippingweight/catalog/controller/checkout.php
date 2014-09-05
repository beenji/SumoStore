<?php
namespace Shippingweight;
use App;
use Sumo;

class ControllerShippingweightCheckout extends App\Controller
{
    public function shipping()
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingweight_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {
            if ($this->config->get('is_admin')) {
                if (!empty($this->request->get['country_id'])) {
                    $this->load->model('localisation/geo_zone');
                    $zones = $this->model_localisation_geo_zone->getZoneToGeoZone($this->request->get['country_id']);
                    $data = $this->getBestOffer($zones);
                }
                if (empty($data) || !count($data)) {
                    return;
                }
                return array('shippingweight' => $data);
            }
            else {
                if (!$this->cart->hasShipping()) {
                    return;
                }
                $zones = $this->session->data['shipping']['geo_zone'];
                $data = $this->getBestOffer($zones, $this->cart->getWeight());

                if (empty($data)) {
                    return;
                }
                if (!empty($data['rate']) && $data['rate'] > 0.00) {
                    $data['price'] = $data['rate'];
                    $data['rate'] = Sumo\Formatter::currency($data['rate'] + ($data['rate'] / 100 * $data['tax']));
                }
                return array('shippingweight' => $data);
            }
        }
    }

    private function getBestOffer($zones, $weight = 0.0000)
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingweight_model_settings->getSettings($this->config->get('store_id'));

        $possible = $prices = $data = array();
        $lowestRate = 999999999;
        foreach ($zones as $zoneList) {
            if (isset($settings['zone'][$zoneList['geo_zone_id']]) && count($settings['zone'][$zoneList['geo_zone_id']])) {
                $data = $settings['zone'][$zoneList['geo_zone_id']];
                $rates = array();
                foreach ($data as $key => $list) {
                    $rates[$list['weight']] = $list;
                }
                ksort($rates);
                foreach ($rates as $list) {
                    if ($weight <= $list['weight']) {
                        if ($lowestRate >= $list['rate']) {
                            $possible = $list;
                            $lowestRate = $list['rate'];
                        }
                    }
                }
            }
            else if (isset($settings['general']) && count($settings['general'])) {
                $rates = array();
                foreach ($settings['general'] as $list) {
                    $rates[$list['weight']] = $list;
                }
                krsort($rates);
                foreach ($rates as $list) {
                    if ($weight <= $list['weight']) {
                        if ($lowestRate >= $list['rate']) {
                            $possible = $list;
                            $lowestRate = $list['rate'];
                        }
                    }
                }
            }
        }

        return $possible;

    }
}
