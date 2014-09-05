<?php
namespace Multisafepay;
use App;
use Sumo;

class ModelApi extends App\Model
{
    const API_LIVE = 'https://api.multisafepay.com/ewx/';
    const API_TEST = 'https://testapi.multisafepay.com/ewx/';
    const CLIENT_VERSION = '1.0.0';

    private $settings;

    public function setSettings($settings)
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }

    protected function set($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function get($key)
    {
        if (!isset($this->settings[$key])) {
            return null;
        }
        return $this->settings[$key];
    }

    public function getGateways($admin = true)
    {
        $root = new \SimpleXMLElement('<gateways ua="SumoStore" />');

        $merchant = $root->addChild('merchant');
        $merchant->addChild('account', $this->get('account'));
        $merchant->addChild('site_id', $this->get('site_id'));
        $merchant->addChild('site_secure_code', $this->get('site_secure_code'));

        $customer = $root->addChild('customer');
        $customer->addChild('locale', $this->config->get('locale'));
        if (!$admin) {
            $this->load->model('localisation/country');
            $country = $this->model_localisation_country->getCountry($this->session->data['customer']['payment_address']['country_id']);
            $customer->addChild('country', $country['name']);
        }

        return $this->request($root->asXML());
    }

    public function getIdealIssuers($admin = true)
    {
        $root = new \SimpleXMLElement('<idealissuers ua="SumoStore" />');

        $merchant = $root->addChild('merchant');
        $merchant->addChild('account', $this->get('account'));
        $merchant->addChild('site_id', $this->get('site_id'));
        $merchant->addChild('site_secure_code', $this->get('site_secure_code'));

        return $this->request($root->asXML());
    }

    private function request($xml)
    {
        if ($this->get('live') !== null) {
            $url = self::API_LIVE;
        }
        else {
            $url = self::API_TEST;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: text/xml',
            'Content-type: text/xml',
            'User-Agent: SumoStore/' . VERSION . ' SumoStoreMultisafepayApi/' . self::CLIENT_VERSION,
            'X-Multisafepay-Referrer: SumoStore'
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $return = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Error communicating with Multisafepay. Error number: ' . curl_errno($ch) . ', ' . curl_error($ch));
        }

        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if (preg_match('/(application|text)\/xml/i', $content_type)) {
            $return = simplexml_load_string($return);
            return json_decode(json_encode($return), true);
        }
        return $return;
    }
}
