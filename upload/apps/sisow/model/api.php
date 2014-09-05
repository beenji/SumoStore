<?php
namespace Sisow;
use Sumo;
use App;

class ModelApi extends App\Model
{
    const API_LOCATION      = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/';
    const CLIENT_VERSION    = '1.0.0';

    protected $api_methods  = array('CheckMerchantRequest', 'issuers', 'methods', 'account-claim');
    protected $settings;

    public function setMerchant($id, $key)
    {
        $this->settings['merchant']['id']   = $id;
        $this->settings['merchant']['key']  = $key;
    }

    public function callMethods()
    {
        return $this->call('POST', 'CheckMerchantRequest');
    }

    public function createPayment($type, $amount, $order_id, $description)
    {
        return $this->call('GET', 'TransactionRequest',
            array(
                'purchaseid'    => $order_id,
                'payment'       => $type,
                'amount'        => round($amount, 2) * 100,
                'entrancecode'  => preg_replace('/[^0-9]/', '', $order_id),
                'description'   => $description,
                'returnurl'     => $this->url->link('app/sisow/checkout/webreturn', '', 'SSL'),
                'callbackurl'   => $this->url->link('app/sisow/checkout/webhook', 'secret_key=' . md5(microtime(true) . $description) . '&order_id=' . $order_id, 'SSL'),
                'notifyurl'     => $this->url->link('app/sisow/checkout/webhook', 'secret_key=' . md5(microtime(true) . $description) . '&order_id=' . $order_id, 'SSL'),
            )
        );
    }

    public function getStatus($transaction_id)
    {
        return $this->call('POST', 'StatusRequest',
            array(
                'trxid' => $transaction_id,
            )
        );
    }

    protected function call($request_method = 'POST', $api_method = 'CheckMerchantRequest', $request_body = null)
    {
        if (empty($this->settings['merchant']) || empty($this->settings['merchant']['id']) || empty($this->settings['merchant']['key'])) {
            throw new \Exception('[SISOW] Merchant ID and Key must be provided');
        }

        $request_body['merchantid'] = $this->settings['merchant']['id'];
        if ($api_method == 'TransactionRequest') {
            $request_body['sha1'] = sha1($request_body['purchaseid'] . $request_body['entrancecode'] . $request_body['amount'] . $this->settings['merchant']['id'] . $this->settings['merchant']['key']);
        }
        else if ($api_method == 'StatusRequest') {
            $request_body['sha1'] = sha1($request_body['trxid'] . $this->settings['merchant']['id'] . $this->settings['merchant']['key']);
        }
        else {
            $request_body['sha1'] = sha1($this->settings['merchant']['id'] . $this->settings['merchant']['key']);
        }


        $ch = curl_init(self::API_LOCATION . $api_method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $request_headers = array(
            'Accept: application/xml',
            'User-Agent: SumoStore/' . VERSION . ' SumoStoreSisowApi/' . self::CLIENT_VERSION,
            'X-Sisow-Referrer: SumoStore'
        );

        if ($request_method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
            else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_method);
        }
        if (is_array($request_body)) {
            $request_body = http_build_query($request_body);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $return = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Error communicating with Sisow. Error number: ' . curl_errno($ch) . ', ' . curl_error($ch));
        }

        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if (preg_match('/(application|text)\/xml/i', $content_type)) {
            $xml = simplexml_load_string($return);
            return json_decode(json_encode($xml), true);
        }
        else {
            $test = @json_decode($return, true);
            if ($test && is_array($test)) {
                return $test;
            }
        }
        return $return;
    }
}
