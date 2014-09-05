<?php
namespace Sumo;
class ControllerCommonPc extends Controller
{
    public function index()
    {
        // Do we have an API-key?
        $apiKey   = $this->config->get('pc_api_key');
        $postCode = $this->request->get['q'];

        if (!empty($apiKey)) {
            $c = curl_init('http://api.postcodeapi.nu/' . $postCode);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_HTTPHEADER, array('Api-Key: ' . $apiKey));

            $response = curl_exec($c);
            curl_close($c);
        }
        else {
            $response = array();
        }

        $this->response->setOutput($response);
    }
}
