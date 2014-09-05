<?php
namespace Sumo;
class ControllerCommonPc extends Controller
{
    public function index()
    {
        // Since this is public, we might want to prevent 'others' from using it using a simple check
        if (!empty($this->session->data['pc']) && !empty($this->request->get['token_pc']) && ($this->request->get['token_pc'] == $this->session->data['pc'])) {
            // Do we have an API-key?
            $apiKey   = $this->config->get('pc_api_key');
            $postCode = preg_replace('/[^a-zA-Z0-9]/', '', $this->request->get['q']);

            if (!empty($apiKey)) {
                $c = curl_init('http://api.postcodeapi.nu/' . $postCode);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($c, CURLOPT_HTTPHEADER, array('Api-Key: ' . $apiKey));

                $response = curl_exec($c);
                curl_close($c);
                $response = json_decode($response, true);
            }
            else {
                $response = array('success' => false);
            }
        }
        else {
            $response = array('success' => false, 'disabled' => true);
        }
        $this->response->setOutput(json_encode($response));
    }
}
