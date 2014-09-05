<?php
namespace Shippingflat;
use App;
use Sumo;

class ControllerShippingflatCheckout extends App\Controller
{
    public function shipping()
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingflat_model_settings->getSettings($this->config->get('store_id'));
        if ($settings['enabled'] == 'on' || $settings['enabled']) {
            if ($this->config->get('is_admin')) {
                $this->load->model('localisation/geo_zone');
                return array('shippingflat' => $this->getBestOffer($this->model_localisation_geo_zone->getZoneToGeoZone($this->request->get['country_id'])));
            }
            else {
                if (!$this->cart->hasShipping()) {
                    return;
                }
                return array('shippingflat' => $this->getBestOffer($this->session->data['shipping']['geo_zone']));
            }
        }
    }

    private function getBestOffer($zones)
    {
        $this->load->appModel('Settings');
        $settings = $this->shippingflat_model_settings->getSettings($this->config->get('store_id'));

        $possible = $prices = $return = array();
        $count = 1;
        $lowestRate = 999999999;
        foreach ($zones as $zoneList) {
            if (isset($settings['zone'][$zoneList['geo_zone_id']]) && !empty($settings['zone'][$zoneList['geo_zone_id']]['rate'])) {
                $rate = $settings['zone'][$zoneList['geo_zone_id']];
                if (empty($rate['price'])) {
                    $rate['price'] = $rate['rate'];
                }
                $rate['rate'] = Sumo\Formatter::currency($rate['rate'] + ($rate['rate'] / 100 * $rate['tax']));
                $possible[$count] = $rate;
                $prices[$rate['rate']] = $count;
                if ($lowestRate >= $rate['rate']) {
                    $possible = $rate;
                    $lowestRate = $rate['rate'];
                }
            }
        }

        return $possible;
    }
}
