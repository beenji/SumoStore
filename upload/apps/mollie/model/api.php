<?php
namespace Mollie;
use Sumo;
use App;

class ModelApi extends App\Model
{
    const API_VERSION       = 'v1';
    const API_LOCATION      = 'https://api.mollie.nl/';
    const API_RESELLER      = 'https://www.mollie.nl/api/reseller/';
    const CLIENT_VERSION    = '1.0.0';

    protected $api_methods  = array('payments', 'issuers', 'methods', 'account-claim', 'ideal', 'creditcard', 'mistercash', 'banktransfer', 'bitcoin', 'paypal', 'paysafecard', 'sofort');
    protected $key;

    public function setKey($key)
    {
        if (!preg_match("!^(?:live|test)_\\w+\$!", $key)) {
            throw new \Exception('Invalid license key, this should have been tested before you added the key.');
        }
        $this->key = $key;
    }

    public function callMethods()
    {
        try {
            $data = $this->call('POST', 'payments', 'methods');
        }
        catch (\Exception $e) {
            return array();
        }
        return $data;
    }

    public function createPayment($type, $amount, $order_id, $description)
    {
        try {
            $data = $this->call(
                'POST', 'payments', 'payments',
                array(
                    'amount'        => $amount,
                    'description'   => $description,
                    'method'        => $type,
                    'redirectUrl'   => $this->url->link('app/mollie/checkout/webreturn', '', 'SSL'),
                    'webhookUrl'    => $this->url->link('app/mollie/checkout/webhook', 'secret_key=' . md5(microtime(true) . $description) . '&order_id=' . $order_id, 'SSL'),
                    'base'          => $base,
                )
            );
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    public function getStatus($transaction_id)
    {
        try {
            $data = $this->call(
                'POST', 'payments', 'payments/' . $transaction_id
            );
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    public function addDiscount($username, $password)
    {
        return $this->call('POST', 'reseller', 'account-claim', array('username' => $username, 'password' => $password));
    }

    protected function call($request_method = 'POST', $api_type = 'payments', $api_method = '', $request_body = null)
    {
        if (empty($this->key) && $api_type != 'reseller') {
            throw new \Exception('No license key found, use function setKey to set the API key.');
        }

        if (!empty($api_method) && !in_array($api_method, $this->api_methods)) {
            //throw new \Exception('Invalid $api_method given');
        }

        if ($api_type != 'reseller') {
            $url = self::API_LOCATION  . self::API_VERSION . '/' . $api_method;
        }
        else {
            $request_body['partner_id'] = 1532321;
            $request_body['profile_key'] = '9E5BFE96';
            $request_body['timestamp'] = time();
            ksort($request_body);
            $request_body['signature'] = hash_hmac('sha1', '/api/reseller/' . self::API_VERSION . '/' . $api_method . '?' . http_build_query($request_body, '', '&'), '44B14E9F93FB1C6DF56896B45ECF2EEFBA5F14DF');
            ksort($request_body);
            $request_body = http_build_query($request_body);
            $url = self::API_RESELLER . self::API_VERSION . '/' . $api_method . '?' . $request_body;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $request_headers = array(
            'Accept: application/json',
            'Authorization: Bearer ' . $this->key,
            'User-Agent: SumoStore/' . VERSION . ' SumoStoreMollieApi/' . self::CLIENT_VERSION,
            'X-Mollie-Referrer: SumoStore'
        );

        if ($request_body != null && $api_type != 'reseller') {
            $request_headers[] = 'Content-Type: application/json';
            if ($request_method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_method);
            }
            if (is_array($request_body)) {
                $request_body = json_encode($request_body);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        }
        else if ($api_type == 'reseller') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $return = curl_exec($ch);
        $array = array();
        if (curl_errno($ch)) {
            throw new \Exception('Error communicating with Mollie. Error number: ' . curl_errno($ch) . ', ' . curl_error($ch));
        }

        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if (preg_match('/(application|text)\/xml/i', $content_type)) {
            $xml = simplexml_load_string($return);
            $array = json_decode(json_encode($xml), true);
        }
        else {
            $test = @json_decode($return, true);
            if ($test && is_array($test)) {
                $array = $test;
            }
        }

        if (count($array)) {
            $array['debug'] = json_decode($request_body, true);
            return $array;
        }

        return $return;
    }
}
